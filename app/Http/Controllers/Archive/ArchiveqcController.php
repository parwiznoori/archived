<?php

namespace App\Http\Controllers\Archive;

use App\DataTables\ArchiveQCImageDataTable;
use App\DataTables\ArchiveqcDataTable;
use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\Archivedata;
use App\Models\Archiveimage;
use App\Notifications\ArchivePageRejectedNotification;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArchiveqcController extends Controller
{
    public function archvieqc(ArchiveqcDataTable $dataTable)
    {
        return $dataTable->render('archiveqcontrol.archiveqc', [
            'title' => trans('general.archive'),
            'description' => trans('general.archive_list'),
        ]);
    }

    public function archiveqcheck(ArchiveQCImageDataTable $dataTable, $id)
    {
        $archive = Archive::findOrFail($id);
        $this->authorizeReview($archive);

        return $dataTable->render('archiveqcontrol.archiveqcheck', [
            'archiveId' => $archive->id,
            'title' => trans('general.archive'),
            'description' => trans('general.archive_list'),
        ]);
    }

    public function archiveqcheckdata($archiveId, $imageArchiveId)
    {
        $archive = Archive::findOrFail($archiveId);
        $this->authorizeReview($archive);

        $archiveqcheckimage = $archive->images()->findOrFail($imageArchiveId);
        $archiveqcheckdata = collect();

        if ((int) $archiveqcheckimage->status_id !== 2) {
            $archiveqcheckdata = $archiveqcheckimage->archivedatas()
                ->with(['semester_type:id,name', 'grade:id,name'])
                ->leftJoin('archiveqcstatus', 'archiveqcstatus.id', '=', 'archivedatas.qc_status_id')
                ->where('archivedatas.archive_id', $archive->id)
                ->select('archivedatas.*', 'archiveqcstatus.qc_status')
                ->orderBy('archivedatas.column_number')
                ->orderBy('archivedatas.id')
                ->get();
        }

        $totalPages = $archive->images()->count();
        $previousPageId = $archive->images()->where('id', '<', $archiveqcheckimage->id)->max('id');
        $nextPageId = $archive->images()->where('id', '>', $archiveqcheckimage->id)->min('id');
        $archiveStatusList = $this->qcStatusList();

        return view('archiveqcontrol.archiveqcheckdata', compact(
            'archive',
            'archiveqcheckimage',
            'archiveqcheckdata',
            'archiveStatusList',
            'totalPages',
            'previousPageId',
            'nextPageId'
        ));
    }

    public function archiveqcheckImageSetStatus(Request $request)
    {
        $data = $request->validate([
            'recordId' => 'required|integer|min:1',
            'approvalStatus' => 'required|integer|in:1,3,4',
        ]);
        $archiveId = Archiveimage::findOrFail($data['recordId'])->archive_id;

        DB::transaction(function () use ($data, $archiveId) {
            $archive = Archive::lockForUpdate()->findOrFail($archiveId);
            $this->authorizeReview($archive);

            $image = $archive->images()->lockForUpdate()->findOrFail($data['recordId']);
            abort_unless((int) $image->status_id === 2, 409);
            $image->update(['qc_status_id' => (int) $data['approvalStatus']]);
            $this->refreshArchiveStatus($archive);
        }, 3);

        return redirect()->back();
    }

    public function archiveQCheckDataSetStatus(Request $request)
    {
        $data = $request->validate([
            'recordId' => 'required|integer|min:1',
            'approvalStatus' => 'required|integer|in:1,3,4',
            'reject_comments' => 'required_if:approvalStatus,4|nullable|string|max:1000',
        ]);
        $archiveId = Archivedata::findOrFail($data['recordId'])->archive_id;
        $status = (int) $data['approvalStatus'];

        DB::transaction(function () use ($data, $archiveId, $status) {
            $archive = Archive::lockForUpdate()->findOrFail($archiveId);
            $this->authorizeReview($archive);

            $record = $archive->archivedatas()->lockForUpdate()->findOrFail($data['recordId']);
            $image = $archive->images()->lockForUpdate()->findOrFail($record->archiveimage_id);
            abort_if((int) $image->status_id === 2, 409);

            $update = ['qc_status_id' => $status, 'reject_comments' => null];
            if ($status === 4) {
                $update['reject_comments'] = $data['reject_comments'];
            } elseif ($status === 3) {
                $update['reject_comments'] = Archivedata::QC_APPROVAL_COMMENT;
            }
            $record->fill($update);
            if ($status !== 1) {
                $record->re_updated = false;
            }
            $notify = $status === 4 && $record->isDirty(['qc_status_id', 'reject_comments', 're_updated']);
            $record->save();

            $counts = $image->archivedatas()
                ->where('archive_id', $archive->id)
                ->selectRaw('qc_status_id, COUNT(*) as total')
                ->groupBy('qc_status_id')
                ->pluck('total', 'qc_status_id');

            $pageStatus = 1;
            if ($counts->get(4, 0) > 0) {
                $pageStatus = 4;
            } elseif ($counts->sum() > 0
                && (int) $counts->get(3, 0) === $counts->sum()
                && $counts->sum() === (int) $image->total_students) {
                $pageStatus = 3;
            }

            $image->update(['qc_status_id' => $pageStatus]);
            $this->refreshArchiveStatus($archive);

            if ($notify) {
                $this->notifyRejection($archive, $image, $record);
            }
        }, 3);

        return redirect()->back();
    }

    private function authorizeReview(Archive $archive)
    {
        $user = auth()->user();
        abort_unless(
            (int) $archive->qc_user_id === (int) $user->id
                || $user->hasAnyRole(['super-admin', 'system-developer']),
            403
        );
    }

    private function refreshArchiveStatus(Archive $archive)
    {
        $counts = $archive->images()
            ->selectRaw('qc_status_id, COUNT(*) as total')
            ->groupBy('qc_status_id')
            ->pluck('total', 'qc_status_id');
        $approved = (int) $counts->get(3, 0);
        $status = 1;

        if ($counts->get(4, 0) > 0) {
            $status = 4;
        } elseif ($approved > 0) {
            $status = $approved === $counts->sum() ? 3 : 2;
        }

        $archive->update(['qc_status_id' => $status]);
    }

    private function notifyRejection(Archive $archive, Archiveimage $image, Archivedata $record)
    {
        $recipients = User::where(function ($query) use ($archive) {
            $query->whereKey($archive->de_user_id)->orWhereHas('roles', function ($roles) {
                $roles->whereIn('name', ['super-admin', 'system-developer']);
            });
        })->where('id', '!=', auth()->id())->get();

        foreach ($recipients as $recipient) {
            $recipient->notify(new ArchivePageRejectedNotification(
                $archive->id,
                $archive->book_name ?? '-',
                'صفحه ' . $image->book_pagenumber,
                $record->reject_comments,
                auth()->user()->name,
                $image->id,
                $record->column_number
            ));
        }
    }

    private function qcStatusList(): array
    {
        return [1 => 'حالت معمولی', 3 => 'تایید', 4 => 'رد'];
    }
}
