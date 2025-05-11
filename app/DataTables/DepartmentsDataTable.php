<?php

namespace App\DataTables;

use App\Models\Department;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Str;

class DepartmentsDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables($query)

            ->setRowClass(function ($department) {
                $department_name=$department->name;
                $department_type_id=$department->department_type_id;
                $contain_shabana=Str::contains($department_name, 'شبانه');
                $contain_roozana=Str::contains($department_name, 'روزانه');
                $class_name='';
                if(($department_type_id==1 && $contain_shabana) || ($department_type_id==2 && $contain_roozana))
                {
                    $class_name=' warning';
                }
                return isset($department->deleted_at)  ? 'row_deleted '. $class_name : ' '.$class_name;
            })
            ->addColumn('action', function ($department) {
                $html = '<div class="btn-group">
                <a class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false">';     
                $html .= trans('general.action').'
                    <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">';
                if (request()->is('universities*')) {

                    if (auth()->user()->can('view-department')) {
                        $html .= '<li><a href="'. route('departments.show', [request()->segment(2), $department]) .'" target="new"  title = "'. trans('general.view').' " > <i class="fa fa-eye"></i> '. trans("general.view") .' </a></li>';
                    }

                    if (auth()->user()->can('edit-department')) {
                        $html .= '<li><a href="'. route('departments.edit', [request()->segment(2), $department]) .'" target="new"  title = "'. trans('general.edit').' " > <i class="fa fa-pencil"></i> '. trans("general.edit") .' </a></li>';
                    }

                    if (auth()->user()->can('delete-department') && !$department->deleted_at) {
                        $html .= '<li><form action="'.route('departments.destroy', [request()->segment(2), $department])  .'" method="post" style="display:inline">
                            <input type="hidden" name="_method" value="DELETE" />
                            <input type="hidden" name="_token" value="'.csrf_token().'" />
                            <button type="submit" style ="color:red" class="btn btn-link" onClick="doConfirm()"><i class="fa fa-trash" style="color:red"></i> '.trans("general.delete").' </button>
                        </form></li>';
                    } 

                    if(auth()->user()->hasRole('super-admin') ){
                        if(isset($department->deleted_at))
                        {
                            $html .= '<li><a href="'. route('departments.recover', [request()->segment(2), $department]) .'" target="new"  title = "'. trans('general.restore').' " > <i class="fa fa-exchange"></i> '. trans("general.restore") .' </a></li>';
                        }
                    }
                       
                } elseif (request()->is('curriculum*')) {
                    $html .= '<a href="'.route('subjects.index', [request()->segment(2), $department]).'" class="btn btn-default btn-xs">'.trans('general.subjects').'</a>';

                    if ($department->subjects_count) {
                        $html .= '<span class="badge badge-success">'.$department->subjects_count.'</span>';
                    }
                }
                $html .= '</ul>
                </div>';
                return $html;
            })
            ->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\University $department
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Department $department)
    {
        // $model = $model->where('departments.university_id', request()->segment(2))->select('name', 'faculty', 'id','chairman','department_student_affairs');


        $department = $department->where('departments.university_id', request()->segment(2))
            ->leftJoin('grades', 'grades.id', '=', 'grade_id')
            ->leftJoin('department_types', 'department_types.id', '=', 'departments.department_type_id')
            ->leftJoin('faculties', 'faculties.id', '=', 'departments.faculty_id')
            ->select('departments.name as name','departments.deleted_at as deleted_at','departments.department_eng as name_eng','departments.abbreviation_eng as abbreviation_eng','departments.number_of_semesters as number_of_semesters',
            'departments.id', 'grades.name as grade_name', 'departments.chairman','departments.department_type_id','department_types.name as department_type_name', 'departments.department_student_affairs','faculties.id as faculty_id','faculties.name as faculty_name','min_credits_for_graduated')
            ->orderBy('departments.updated_at', 'DESC');

        if(auth()->user()->hasRole('super-admin'))  
        {
            $department =$department->withTrashed();
        } 
        else
        {
            $department =$department->whereNull('departments.deleted_at');

        }

        if (request()->is('curriculum*')) {
            $department->withCount('subjects');
        }

        return $department;
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
                    ->addAction(['title' => trans('general.action'), 'width' => '60px'])
                    // ->parameters($this->getBuilderParameters());
                    ->parameters(array_merge($this->getBuilderParameters([]), [
                        'dom'          => '<"top"Bl>rt<"bottom"ip>',
                        'initComplete' => "function (settings, data) {   
                            emptyValue = ''; 
                            reset_button=false;                                    
                            table = this      
                            state = table.api().state.loaded()                        
                            
                            $('.dt-button.buttons-reset').click(function () {
                                reset_button=true;  
                                console.log('reset buton clicked '+reset_button);
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
                                                                                                                    
                                if(this.index() >= 0 && this.index() <= 9) { 
                                    if (this.index() == 0 || this.index() == 2) {
                                        $('<input class=\"datatable-footer-input ltr \" placeholder=\"'+$(column.header()).text()+'\" name=\"'+ column.index() + '\" value=\"'+ (state ? state.columns[this.index()].search.search : emptyValue) +'\" />').appendTo($(column.footer()).empty())                                        
                                        .on(onEvent, function () {
                                            column.search($(this).val(), false, false, true).draw();
                                        });
                                    } else {
                                        $('<input class=\"datatable-footer-input \" placeholder=\"'+$(column.header()).text()+'\" name=\"'+ column.index() + '\" value=\"'+ (state ? state.columns[this.index()].search.search : emptyValue) +'\" />').appendTo($(column.footer()).empty())                                        
                                        .on(onEvent, function () {
                                            column.search($(this).val(), false, false, true).draw();
                                        });
                                    }
                                }
                            });

                            $('a.student-status').click(function () {
                                if ($(this).attr('data-status-id') == 'all')
                                    table.api().columns(0).search('', false, false, true).draw();
                                else
                                    table.api().columns(0).search($(this).attr('data-status-id'), false, false, true).draw();
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
            'id'     => ['name' => 'departments.id','title' => trans('general.id')],
            'name'    => ['name' => 'departments.name','title' => trans('models/department.fields.name')],            
            'name_eng'    => ['name' => 'departments.department_eng','title' => trans('models/department.fields.department_eng')],  
            'abbreviation_eng'    => ['name' => 'departments.abbreviation_eng','title' => trans('models/department.fields.abbreviation_eng')], 
            'faculty_name'    => ['name' => 'faculties.name','title' => trans('models/department.fields.faculty_id')],                        
            'department_student_affairs'     => ['title' => trans('models/department.fields.department_student_affairs')],                                       
            'department_type_name'     => ['name'=> 'department_types.name', 'title' => trans('models/department.fields.department_type_id')],                                                      
            'grade_name'     => [ 'name'=> 'grades.name', 'title' => trans('models/department.fields.grade_id')]   ,
            'number_of_semesters'    => ['name' => 'departments.number_of_semesters','title' => trans('models/department.fields.number_of_semesters')],  
            'min_credits_for_graduated'    => ['name' => 'departments.min_credits_for_graduated','title' => trans('models/department.fields.min_credits_for_graduated')],                  
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Departments_' . date('YmdHis');
    }
}
