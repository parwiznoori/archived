<?php

namespace App\DataTables;

use App\Models\Archive;
use App\Models\UniversityUser;
use App\Models\Archivedata;
use App\Models\Archiveimage;
use App\Models\Role;
use Yajra\DataTables\Services\DataTable;

class ArchivedataDataTable extends DataTable
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

                if (auth()->user()->hasRole('super-admin')) {
                    if (isset($archivedata->deleted_at)) {
                        // Uncomment this line if recovery action is required
                        // $html .= '<li><a href="'. route('archive.recover', $archive) .'"  target="new" onClick="doConfirm()" > <i class="fa fa-pencil"></i> '. trans("general.restore").' </a></li>';
                    }
                }

//             

                if (auth()->user()->can('archivedata_detials')) {
                    $html .= '<li><a href="' . route('archivedata_detials', $archivedata) . '" target="_blank">
                     <i class="fa fa-pencil"></i> ' . trans("general.archivedata_detials") . '
                     </a></li>';
                }

               if (auth()->user()->can('archive_doc_type')) {
                   $html .= '<li><a href="' . route('archive_doc_type', $archivedata) . '" target="_blank">
                    <i class="fa fa-pencil"></i> ' . trans("general.archive_doc_type") . '
                    </a></li>';
               }

            //    if (auth()->user()->can('archive_doc_form')) {
            //        $html .= '<li><a href="' . route('archive_doc_form', $archivedata) . '" target="_blank">
            //         <i class="fa fa-pencil"></i> ' . trans("general.archive_doc_form") . '
            //         </a></li>';
            //    }

            

               if (auth()->user()->can('archive_form_print')) {
                   $html .= '<li><a href="' . route('archive_form_print', $archivedata) . '" target="_blank">
                    <i class="fa fa-pencil"></i> ' . trans("general.archive_form_print") . '
                    </a></li>';
               }

                    // if (auth()->user()->can('archive_monograph')) {
                    //     $html .= '<li><a href="' . route('archive_monograph', $archivedata) . '" target="_blank">
                    //         <i class="fa fa-pencil"></i> ' . trans("general.archive_monograph") . '
                    //         </a></li>';
                    // }

                      if (auth()->user()->can('archive_baqidari')) {
                        $html .= '<li><a href="' . route('archive_baqidari', $archivedata) . '" target="_blank">
                            <i class="fa fa-pencil"></i> ' . trans("general.archive_baqidari") . '
                            </a></li>';
                    }

                      if (auth()->user()->can('archive_zamayem')) {
                        $html .= '<li><a href="' . route('archive_zamayem', $archivedata) . '" target="_blank">
                            <i class="fa fa-pencil"></i> ' . trans("general.archive_zamayem") . '
                            </a></li>';
                    }

/*
                if (auth()->user()->can('print-archivedoc')) {
                    $html .= '<li><a href="' . route('print-archivedoc', $archivedata) . '" target="_blank">
                <i class="fa fa-pencil"></i> ' . trans("general.archivedoc-pa") . '
              </a></li>';

                    $html .= '<li><a href="' . route('print-archivedocf', $archivedata) . '" target="_blank">
                <i class="fa fa-pencil"></i> ' . trans("general.archivedoc-f") . '
              </a></li>';
                    $html .= '<li><a href="' . route('print-archivedestalam', $archivedata) . '" target="_blank">
                <i class="fa fa-pencil"></i> ' . trans("general.archivedestalam") . '
              </a></li>';
                    $html .= '<li><a href="' . route('print-archivedestalam2', $archivedata) . '" target="_blank">
                <i class="fa fa-pencil"></i> ' . trans("general.archivedestalam2") . '
              </a></li>';
                }
*/



                // Uncomment these blocks if you need edit or delete permissions
                /*
                if (auth()->user()->can('edit-archivedata')) {
                    $html .= '<li><a href="'. route('archivedata.edit', $archivedata) .'"  target="new"> <i class="fa fa-pencil"></i> '. trans("general.edit") .' </a></li>';
                }

                if (auth()->user()->can('delete-archivedata')) {
                    $html .= '<li><form action="'. route('archivedata.destroy', $archivedata) .'" method="post" style="display:inline">
                        <input type="hidden" name="_method" value="DELETE" />
                        <input type="hidden" name="_token" value="'.csrf_token().'" />
                        <button type="submit" style ="color:red" class="btn btn-link" onClick="doConfirm()"><i class="fa fa-trash" style="color:red"></i> '.trans("general.delete").' </button>
                    </form></li>';
                }
                */

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
        // System developer/Super Admin: Show all data
        $roles = Role::all();
    } elseif (
        Archive::where('de_user_id', auth()->user()->id)->exists() ||
        Archive::where('qc_user_id', auth()->user()->id)->exists()
    ) {
        // DE or QC user: Show only their data
        $wheres[] = ['archives.de_user_id', '=', auth()->user()->id];
        $orWheres[] = ['archives.qc_user_id', '=', auth()->user()->id];
        $roles = Role::all();
    } elseif (auth()->user()->hasRole('visitor')) {
        // Visitor: Only show data from their university
        // $universityId[] = auth()->user()->university[0]->id;
        // $wheres[] = ['archives.university_id', '=', $universityId];
        $roles = Role::whereIn('name', ['visitor'])->get(); // Only show visitor role
    } else {
        // All other users: Show nothing (or adjust as needed)
        $wheres[] = ['archives.de_user_id', '=', -1];
        $roles = Role::all();
    }

    
    // Build the query
    $query = Archive::query();
    
    // Apply where conditions
    if (!empty($wheres)) {
        $query->where(function($q) use ($wheres, $orWheres) {
            foreach ($wheres as $where) {
                $q->where($where[0], $where[1], $where[2]);
            }
            
            if (!empty($orWheres)) {
                $q->orWhere(function($orQuery) use ($orWheres) {
                    foreach ($orWheres as $where) {
                        $orQuery->where($where[0], $where[1], $where[2]);
                    }
                });
            }
        });
    }

     

// // Apply $wheres and $orWheres to the query as needed
//         $query = Archive::query();
//         if (!empty($wheres)) {
//             $query->where($wheres);
//         }
//         if (!empty($orWheres)) {
//             $query->orWhere($orWheres);
//         }

//         $data = $query->get();





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
            'last_name',
            'father_name',
            'grandfather_name',
            'school',
            'school_graduation_year',
            'tazkira_number',
            'birth_date',
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

       
         $universityList = UniversityUser::where('user_id', auth()->user()->id)
                    ->pluck ('university_id')
                    ->toArray();
                if($universityList!=null){
                     $query->whereIn('archives.university_id', $universityList);
                }

        
        
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
                                                                                                                    
                                if(this.index() >= 0 && this.index() <= 14) { 
                                    if (this.index() == 0 || this.index() == 14) {
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
            'kankor_id'    => ['name' => 'kankor_id', 'title' => trans('general.kankor_id')],
//            'archive' =>        ['name' => 'archives.book_name', 'title' => trans('general.book_name')],
//            'archiveimage' =>        ['name' => 'archiveimages.', 'title' => trans('general.photo')],
            'university' => ['name' => 'universities.name', 'title' => trans('general.university')],
            'faculty'    => ['name' => 'faculties.name', 'title' => trans('general.faculty')],
            'department'    => ['name' => 'departments.name', 'title' => trans('general.department')],
            'name'    => ['name' => 'name', 'title' => trans('general.name')],
//            'last_name'    => ['name' => 'last_name', 'title' => trans('general.last_name')],
            'father_name'    => ['name' => 'father_name', 'title' => trans('general.father_name')],
            'grandfather_name'    => ['name' => 'grandfather_name', 'title' => trans('general.grandfather_name')],
//            'school' => ['name' => 'school','title' => trans('general.school_name')],
//            'school_graduation_year'    => ['name' => 'school_graduation_year', 'title' => trans('general.school_graduation_year')],
//            'tazkira_number'    => ['name' => 'tazkira_number', 'title' => trans('general.tazkira_number')],
            'birth_date'    => ['name' => 'birth_date', 'title' => trans('general.birth_date')],
//            'birth_place'    => ['name' => 'birth_place', 'title' => trans('general.birth_place')],
            'time'    => ['name' => 'time', 'title' => trans('general.time')],
//            'semester_type' =>        ['name' => 'semester_type.name', 'title' => trans('general.half_year')],
            'year_of_inclusion'    => ['name' => 'year_of_inclusion', 'title' => trans('general.year_of_inclusion')],
            'graduated_year'    => ['name' => 'graduated_year', 'title' => trans('general.graduated_year')],
//            'transfer_year'    => ['name' => 'transfer_year', 'title' => trans('general.transfer_year')],
//            'leave_year'    => ['name' => 'leave_year', 'title' => trans('general.leave_year')],
//            'failled_year'    => ['name' => 'failled_year', 'title' => trans('general.failled_year')],
            'monograph_date'    => ['name' => 'monograph_date', 'title' => trans('general.monograph_date')],
//            'monograph_number'    => ['name' => 'monograph_number', 'title' => trans('general.monograph_number')],
//            'monograph_title'    => ['name' => 'monograph_title', 'title' => trans('general.monograph_title')],
//            'averageOfScores'    => ['name' => 'averageOfScores', 'title' => trans('general.averageOfScores')],
            'grade' =>        ['name' => 'grades.name', 'title' => trans('general.grade')],
//            'description'    => ['name' => 'description', 'title' => trans('general.description')],
            'archiveqcstatus'    => ['name' => 'archiveqcstatus.qc_status', 'title' => trans('general.accept_or_refuse')],

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