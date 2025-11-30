<?php

namespace App\DataTables;

use App\Models\Archive;
use App\Models\Archivedata;
use App\Models\Archiveimage;
use App\Models\Role;
use Yajra\DataTables\Services\DataTable;

class SelectForNameUpdatDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $datatables = datatables()
            ->eloquent($query)

        ->setRowClass(function ($archivedata) {
                $className = '';

                if (isset($archivedata->deleted_at)) {
                    $className .= 'row_deleted ';
                }

                if ($archivedata->qc_status_id == 3) {
                    $className .= 'qc_status ';
                }

                if ($archivedata->qc_status_id == 4) {
                    $className .= 'qc_status2 ';
                }

                return $className;
            })
            ->addColumn('action', function ($archivedata) {
                $html = '<div class="btn-group">
                        <a class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false">';
                $html .= trans('general.action') . '
                        <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu pull-right">';

              



                if (auth()->user()->can('update-name')) {
                    $html .= '<li><a href="' . route('archivedata.edit-name', $archivedata) . '" target="_blank">
                     <i class="fa fa-pencil"></i> ' . trans("general.edit") . '
                     </a></li>';
                }

                $html .= '</ul>
                    </div>';

                return $html;
            })
            ->rawColumns(['action']); // Ensure columns with HTML are registered as raw

        return $datatables;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Archivedata $model)
    {
        $wheres = [];
        $orWheres = [];
        $roles = [];

// Check the role of the authenticated user
        if (auth()->user()->hasRole('system-developer') || auth()->user()->hasRole('super-admin')) {
            // System developer: Show all roles and all data
            $roles = Role::all();
        } elseif (
            Archive::where('de_user_id', auth()->user()->id)->exists() ||
            Archive::where('qc_user_id', auth()->user()->id)->exists()
        ) {
            // Specific user: Show only their data
            $wheres[] = ['archives.de_user_id', '=', auth()->user()->id];
            $orWheres[] = ['archives.qc_user_id', '=', auth()->user()->id];
            $roles = Role::all(); // Roles can be fetched if needed
        } else {
              $wheres[] = ['archives.de_user_id', '=', -1];
            // All other users: Show all data
            $roles = Role::all();
        }

// Apply $wheres and $orWheres to the query as needed
        $query = Archive::query();
        if (!empty($wheres)) {
            $query->where($wheres);
        }
        if (!empty($orWheres)) {
            $query->orWhere($orWheres);
        }

        $data = $query->get();





//        if(auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('visitor')){
//        }else{
//            array_push($wheres, ['archives.de_user_id', '=',auth()->user()->id]);
//            array_push($orWheres, ['archives.qc_user_id', '=',auth()->user()->id]);
//        }
        $query = $model->select(
            'archivedatas.id',
            'archives.status_id',
            'archives.qc_status_id',
            'archives.book_name as archive',
            'archiveimages.id as archiveimage',
            'archivedatas.name',
            'archivedatas.updateName_img',
            'archivedatas.updateName_desc',
            'archivedatas.previous_name',
            'last_name',
            'father_name',
            'previous_father_name',
            'grandfather_name',
            'previous_grandfather_name',
            'school',
            'school_graduation_year',
            'tazkira_number',
            'birth_date',
            'previous_birth_date',
            'birth_place',
            'time',
            'kankor_id',
            'semester_type.name as semester_type',
            'year_of_inclusion',
            'graduated_year',
            'transfer_year',
            'leave_year',
            'failled_year',
            'monograph_date',
            'monograph_number',
            'monograph_title',
            'averageOfScores',
            'grades.name as grade',
            'description',
            'archiveqcstatus.qc_status as archiveqcstatus',
            'universities.name as university',
            'faculties.name as faculty',
            'departments.name as department',
           

           
        )
        ->leftJoin('archives', 'archives.id', '=', 'archivedatas.archive_id')
        ->leftJoin('archiveimages', 'archiveimages.id', '=', 'archivedatas.archiveimage_id')
        ->leftJoin('universities', 'universities.id', '=', 'archivedatas.university_id')
        ->leftJoin('faculties', 'faculties.id', '=', 'archivedatas.faculty_id')
        ->leftJoin('departments', 'departments.id', '=', 'archivedatas.department_id')
        ->leftJoin('semester_type', 'semester_type.id', '=', 'archivedatas.semester_type_id')
        ->leftJoin('grades', 'grades.id', '=', 'archivedatas.grade_id')
        ->leftJoin('archiveqcstatus', 'archiveqcstatus.id', '=', 'archivedatas.qc_status_id')
        ->where($wheres)
        ->orWhere($orWheres)
        ->orderBy('archives.book_name');

       
        

        
        
        return $query;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->addAction(['title' => trans('general.action'), 'width' => '100px'])
                    ->parameters(array_merge($this->getBuilderParameters([]), [
                        'dom'          => 'Brtip',
                        'initComplete' => "function (settings, data) {   
                            emptyValue = '';                                     
                            table = this      
                            state = table.api().state.loaded()                        
                            
                            $('.dt-button.buttons-reset').click(function () {
                                $('.nav-tabs li').removeClass('active')
                                $('a[data-status-id=\"all\"]').parent().addClass('active')
                            })

                            if(!state || state.columns[0].search.search == '')        
                                $('a[data-status-id=\"all\"]').parent().addClass('active')
                            else
                                $('a[data-status-id=\"'+state.columns[0].search.search+'\"]').parent().addClass('active')

                            table.api().columns().every(function () {
                                var column = this;
                                var onEvent = 'change';
                                                                                                                    
                                if(this.index() >= 0 && this.index() <= 27) { 
                                    if (this.index() == 0 || this.index() == 27) {
                                        $('<input class=\"datatable-footer-input ltr \" placeholder=\"'+$(column.header()).text()+'\" name=\"'+ column.index() + '\" value=\"'+ (state ? state.columns[this.index()].search.search : emptyValue) +'\" />').attr('size',10).appendTo($(column.footer()).empty())                                        
                                        .on(onEvent, function () {
                                            column.search($(this).val(), false, false, true).draw();
                                        });
                                    } else {
                                        $('<input class=\"datatable-footer-input \"  placeholder=\"'+$(column.header()).text()+'\" name=\"'+ column.index() + '\" value=\"'+ (state ? state.columns[this.index()].search.search : emptyValue) +'\" />') .attr('size',10).appendTo($(column.footer()).empty())                                        
                                        .on(onEvent, function () {
                                            column.search($(this).val(), false, false, true).draw();
                                        });
                                    }
                                }
                            });

                            $('#dataTableBuilder').wrap('<div class=\"table-responsive\"></div>');
                        }"

                    ]));
    }
    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {

        return [
            'id'    => ['name' => 'id', 'title' => trans('general.id')],
            'name'    => ['name' => 'name', 'title' => trans('general.name')],
            'previous_name'    => ['name' => 'previous_name', 'title' => trans('general.previous_name')],
            'last_name'    => ['name' => 'last_name', 'title' => trans('general.last_name')],
            'father_name'    => ['name' => 'father_name', 'title' => trans('general.father_name')],
            'previous_father_name'    => ['name' => 'previous_father_name', 'title' => trans('general.previous_father_name')],
            'previous_grandfather_name'    => ['name' => 'previous_grandfather_name', 'title' => trans('general.previous_grandfather_name')],
            'grandfather_name'    => ['name' => 'grandfather_name', 'title' => trans('general.grandfather_name')],
            'birth_date'    => ['name' => 'birth_date', 'title' => trans('general.birth_date')],
            'previous_birth_date'    => ['name' => 'previous_birth_date', 'title' => trans('general.previous_birth_date')],
            'updateName_desc'    => ['name' => 'updateName_desc', 'title' => trans('general.updateName_desc')],

        ];
    
   
        }
    
    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Archivedata_' . date('YmdHis');
    }
}