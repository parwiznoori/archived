<?php

namespace App\Http\Controllers\Archive;

use App\DataTables\ArchiveqcDataTable;
use App\DataTables\ArchiveQCImageDataTable;
use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\Archivedata;
use App\Models\Archiveimage;
use App\Models\Grade;
use App\Models\SemesterType;
use DB;
use Illuminate\Http\Request;


class ArchiveqcController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function archvieqc(ArchiveqcDataTable $dataTable)
    {


        return $dataTable->render('archiveqcontrol.archiveqc', [
            'title' => trans('general.archive'),
            'description' => trans('general.archive_list'),

        ]);

    }


    public function archiveqcheck(ArchiveQCImageDataTable $dataTable)
    {

        return $dataTable->render('archiveqcontrol.archiveqcheck', [
            'title' => trans('general.archive'),
            'description' => trans('general.archive_list'),

        ]);
    }

//qc
    public function archiveqcheckdata($archiveId, $imageArchiveId)
    {
        // Fetch the archive
        $archive = Archive::findOrFail($archiveId);

        //  if you need user authentication
         if ($archive->qc_user_id != auth()->user()->id ) {
             return back();  // Redirects the user back if conditions fail
         }

        // Fetch the specific archive image associated with the archive
        $archiveqcheckimage = Archiveimage::where('archive_id', $archive->id)
            ->where('id', $imageArchiveId)
            ->first(); // Get a single image or fail if not found

        if($archiveqcheckimage==null){
            return back();
        }
        // Join semester types
        $archivesymestetype = Archivedata::join('semester_type', 'semester_type.id', '=', 'archivedatas.semester_type_id')
            ->where('archiveimage_id', $imageArchiveId)
            ->select('archivedatas.*', 'semester_type.name')
            ->get();

        // Join grades
        $archivegrad = Archivedata::join('grades', 'grades.id', '=', 'archivedatas.grade_id')
            ->where('archiveimage_id', $imageArchiveId)
            ->select('archivedatas.*', 'grades.name')
            ->get();

        // Join QC status
        $archiveqcheckdata = Archivedata::join('archiveqcstatus', 'archiveqcstatus.id', '=', 'archivedatas.qc_status_id')
            ->where('archiveimage_id', $imageArchiveId)
            ->select('archivedatas.*', 'archiveqcstatus.qc_status')
            ->get();

        // Get QC status list
        $archiveStatusList = $this->qcStatusList();
//        dd($archiveStatusList);

        $totalPages = Archiveimage::where('archive_id', $archiveId)->count();

        // Return the view without opening a new page
        return view('archiveqcontrol.archiveqcheckdata',
            compact('archive',
                'archiveqcheckimage',
                'archivesymestetype',
                'archivegrad',
                'archiveqcheckdata',
                'archiveStatusList',
                'totalPages'));


    }


    public function archiveqcheckImageSetStatus(Request $request)
    {

        $archiveImages = ArchiveImage::where('id', $request->recordId)->first();
        // Update QC status for archive images
        {
            $archiveImages->update([
                'qc_status_id' => $request->approvalStatus
            ]);
        }

        // Update QC status for archives
        {
            $archiveImageRejectCount = Archiveimage::where('archive_id', $archiveImages->archive_id)
                ->where('qc_status_id', 4)
                ->count();
            $archiveImageCountApprove = Archiveimage::where('archive_id', $archiveImages->archive_id)
                ->where('qc_status_id', 3)
                ->count();
            $archiveImageCount = Archiveimage::where('archive_id', $archiveImages->archive_id)
                ->count();
            $archive = Archive::where('id', $archiveImages->archive_id)->first();

            if ($archiveImageRejectCount > 0) {
                $archive->update([
                    'qc_status_id' => 4
                ]);
            } else {
                if ($archiveImageCountApprove == $archiveImageCount) {
                    $archive->update([
                        'qc_status_id' => 3
                    ]);
                } else {
                    $archive->update([
                        'qc_status_id' => 2
                    ]);
                }
            }
        }
        // Redirect back after processing
        return redirect()->back();
    }

    public function archiveQCheckDataSetStatus(Request $request)
    {
        // Update QC status for the archive ddata
        $archiveData = Archivedata::find($request->recordId);
        $archiveData->update([
            'qc_status_id' => $request->approvalStatus
        ]);

        // Update QC status for archive images
        {
            $archiveDataCount = Archivedata::where('archiveimage_id', $archiveData->archiveimage_id)
                ->where('qc_status_id', 4)
                ->count();
            $archiveDataCountApprove = Archivedata::where('archiveimage_id', $archiveData->archiveimage_id)
                ->where('qc_status_id', 3)
                ->count();
            $archiveImages = ArchiveImage::where('id', $archiveData->archiveimage_id)->first();

            if ($archiveDataCount > 0) {
                $archiveImages->update([
                    'qc_status_id' => 4
                ]);
            } else {
                if ($archiveDataCountApprove == $archiveImages->total_students) {
                    $archiveImages->update([
                        'qc_status_id' => 3
                    ]);
                } else {
                    $archiveImages->update([
                        'qc_status_id' => 1
                    ]);
                }
            }
        }

        // Update QC status for archives
        {
            $archiveImageRejectCount = Archiveimage::where('archive_id', $archiveData->archive_id)
                ->where('qc_status_id', 4)
                ->count();
            $archiveImageCountApprove = Archiveimage::where('archive_id', $archiveData->archive_id)
                ->where('qc_status_id', 3)
                ->count();
            $archiveImageCount = Archiveimage::where('archive_id', $archiveData->archive_id)
                ->count();
            $archive = Archive::where('id', $archiveData->archive_id)->first();

            if ($archiveImageRejectCount > 0) {
                $archive->update([
                    'qc_status_id' => 4
                ]);
            } else {
                if ($archiveImageCountApprove == $archiveImageCount) {
                    $archive->update([
                        'qc_status_id' => 3
                    ]);
                } else {
                    if($archiveImageCountApprove>0 || $archiveImageRejectCount>0){

                        $archive->update([
                            'qc_status_id' => 2
                        ]);
                    }else {
                        $archive->update([
                            'qc_status_id' => 1
                        ]);
                    }
                }
            }
        }

        // Redirect back after processing
        return redirect()->back();
    }

    private function qcStatusList()
    {
        $qcStatusList = new \stdClass();
        $qcStatusList->{'1'} = 'حالت معمولی';
//      $qcStatusList->{'2'} = 'پروسس';
        $qcStatusList->{'3'} = 'تایید';
        $qcStatusList->{'4'} = 'رد';
        return $qcStatusList;
    }



}

