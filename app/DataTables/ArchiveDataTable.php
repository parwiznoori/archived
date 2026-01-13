<?php

namespace App\DataTables;

use App\Models\Archive;
use Yajra\DataTables\Services\DataTable;

class ArchiveDataTable extends DataTable
{
    public function dataTable($query)
    {
        $datatables = datatables($query)
            ->setRowClass(function ($archive) {

                $className = '';

                if ($archive->qc_status_id == 3 && $archive->status_id == 4) {
                    $className .= ' qc_status';
                }

                if ($archive->qc_status_id == 4 && $archive->status_id == 4) {
                    $className .= ' qc_status2';
                }

                if ($archive->status_id == 3) {
                    $className .= ' qc_status3';
                }

                return $className;
            })

            ->addColumn('created_at_jalali', function ($archive) {

                if (isset($archive->created_at)) {

                    if (class_exists('Morilog\Jalali\Jalalian')) {

                        return \Morilog\Jalali\Jalalian::fromDateTime($archive->created_at)
                            ->format('Y/m/d');
                    }

                    return $archive->created_at->format('Y-m-d');
                }

                return '-';
            })

            ->addColumn('action', function ($archive) {

                $html = '<div class="btn-group">
                        <a class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false">'
                        . trans('general.action') .
                        '<i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu pull-right">';

                if (auth()->user()->can('edit-archive')) {
                    $html .= '<li><a href="' . route('archive.edit', $archive) . '" target="new">
                        <i class="fa fa-pencil"></i> ' . trans("general.edit") . '</a></li>';
                }

                if (auth()->user()->can('csv-archive')) {
                    $html .= '<li><a href="' . route('archive.view', $archive) . '" target="_blank">
                        <i class="fa fa-pencil"></i> ' . trans("general.upload_csv") . '</a></li>';
                }

                if (auth()->user()->can('view-archiveimage')) {
                    $html .= '<li><a href="' . route('archive.show', $archive) . '" target="new">
                        <i class="fa fa-pencil"></i> ' . trans("general.photo") . '</a></li>';
                }

                if (auth()->user()->can('view-archivedata')) {
                    $html .= '<li><a href="' . route("archiveBookDataEntry", ['id' => $archive->id]) . '" target="new">
                        <i class="fa fa-pencil"></i> ' . trans("general.archivedata") . '</a></li>';
                }

                if (auth()->user()->can('reset-qc-user') && !empty($archive->qc_user_id)) {

                    $html .= '<li>
                        <form action="' . route('archive.reset-qc-user', $archive) . '" method="POST" style="display:inline;">
                            ' . csrf_field() . '
                            <button onclick="return confirm(\'Are you sure you want to reset the QC user?\');"
                                type="submit" class="btn btn-link">
                                <i class="fa fa-pencil"></i> ' . trans("general.reset-qc-user") . '
                            </button>
                        </form>
                    </li>';
                }

                if (auth()->user()->can('reset-de-user') && (!empty($archive->de_user_id) && empty($archive->qc_user_id))) {

                    $html .= '<li>
                        <form action="' . route('archive.reset-de-user', $archive) . '" method="POST" style="display:inline;">
                            ' . csrf_field() . '
                            <button type="submit" class="btn btn-link">
                                <i class="fa fa-pencil"></i> ' . trans("general.reset-de-user") . '
                            </button>
                        </form>
                    </li>';
                }

                if (auth()->user()->can('delete-archive')) {

                    $html .= '<li>
                        <form action="' . route('archive.destroy', $archive) . '" method="post" style="display:inline">
                            <input type="hidden" name="_method" value="DELETE" />
                            <input type="hidden" name="_token" value="' . csrf_token() . '" />
                            <button type="submit" class="btn btn-link" style="color:red" onClick="doConfirm()">
                                <i class="fa fa-trash" style="color:#ff0000"></i> ' . trans("general.delete") . '
                            </button>
                        </form>
                    </li>';
                }

                $html .= '</ul></div>';

                return $html;
            })

            ->rawColumns(['action']);

        return $datatables;
    }

    public function query(Archive $archive)
    {
        $query = $archive->allUniversities()->select(
            'archives.id',
            'archives.qc_user_id',
            'archives.de_user_id',
            'archives.status_id',
            'archives.qc_status_id',
            'archives.created_at',

            'universities.name as university',

            \DB::raw('GROUP_CONCAT(DISTINCT faculties.name SEPARATOR ", ") as faculty'),
            \DB::raw('GROUP_CONCAT(DISTINCT departments.name SEPARATOR ", ") as department'),

            'cur_de_user.name as current_de_user',
            'cur_qc_user.name as current_qc_user',

            'archiveyears.year as archiveyears',

            'archives.book_pagenumber',
            'archives.book_description',
            'archives.book_name',

            'archivedatastatus.status as archivedatastatus',
            'archiveqcstatus.qc_status as archiveqcstatus'
        )

            ->leftJoin('universities', 'universities.id', '=', 'archives.university_id')

            ->leftJoin('archive_departments', 'archive_departments.archive_id', '=', 'archives.id')
            ->leftJoin('faculties', 'faculties.id', '=', 'archive_departments.faculty_id')
            ->leftJoin('departments', 'departments.id', '=', 'archive_departments.department_id')

            ->leftJoin('users as cur_de_user', 'cur_de_user.id', '=', 'archives.de_user_id')
            ->leftJoin('users as cur_qc_user', 'cur_qc_user.id', '=', 'archives.qc_user_id')

            ->leftJoin('archivedatastatus', 'archivedatastatus.id', '=', 'archives.status_id')
            ->leftJoin('archiveqcstatus', 'archiveqcstatus.id', '=', 'archives.qc_status_id')
            ->leftJoin('archiveyears', 'archiveyears.id', '=', 'archives.archive_year_id')

            ->groupBy('archives.id')
            ->orderBy('archives.id', 'desc');

        // filter universities of user
        $universityList = \DB::table('university_users')
            ->where('user_id', auth()->user()->id)
            ->whereNull('deleted_at')
            ->pluck('university_id')
            ->toArray();

        if (!empty($universityList)) {
            $query->whereIn('archives.university_id', $universityList);
        }

        // type == 2 data entry
        if (auth()->user()->type == 2) {

            $userList = \DB::table('archive_entry_users')
                ->where('user_id', auth()->user()->id)
                ->pluck('archive_id')
                ->toArray();

            if (!empty($userList)) {
                $query->whereIn('archives.id', $userList);
            }

            if (auth()->user()->user_type == 2 || auth()->user()->user_type == 3) {

                $query->whereNotNull('archives.de_user_id')
                    ->where('archives.de_user_id', auth()->user()->id);
            }
        }

        return $query;
    }

    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['title' => trans('general.action'), 'width' => '100px']);
    }

    protected function getColumns()
    {
        return [
            'id' => ['name' => 'archives.id', 'title' => trans('general.id')],

            'book_name' => ['name' => 'book_name', 'title' => trans('general.book_name')],

            'university' => ['name' => 'universities.name', 'title' => trans('general.university')],

            'faculty' => ['name' => 'faculty', 'title' => trans('general.faculty')],

            'department' => ['name' => 'department', 'title' => trans('general.department')],

            'archiveyears' => ['name' => 'archiveyears.year', 'title' => trans('general.book_year')],

            'archivedatastatus' => ['name' => 'archivedatastatus.status', 'title' => trans('general.status')],

            'archiveqcstatus' => ['name' => 'archiveqcstatus.qc_status', 'title' => trans('general.accept_or_refuse')],

            'created_at_jalali' => ['name' => 'archives.created_at', 'title' => trans('general.created_at')],

            'current_de_user' => ['name' => 'cur_de_user.name', 'title' => 'درج‌کننده'],

            'current_qc_user' => ['name' => 'cur_qc_user.name', 'title' => 'کنترول‌کننده'],

            'book_description' => ['name' => 'book_description', 'title' => trans('general.book_description')],
        ];
    }

    protected function filename()
    {
        return 'Archive_' . date('YmdHis');
    }
}
