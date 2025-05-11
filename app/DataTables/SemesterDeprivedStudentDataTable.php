<?php

namespace App\DataTables;

use App\Models\SemesterDeprivedStudent;
use Yajra\DataTables\Services\DataTable;

class SemesterDeprivedStudentDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        
        $datatables = datatables($query)
            ->setRowClass(function ($SemesterDeprivedStudent) {
                $className='';
                if(isset($SemesterDeprivedStudent->deleted_at))
                {
                    $className.='row_deleted';
                }
                return $className;
            })
            ->editColumn('form_no', function ($SemesterDeprivedStudent) {
                return $SemesterDeprivedStudent->student->form_no ?? '' ;
            })
            ->editColumn('name', function ($SemesterDeprivedStudent) {
                return $SemesterDeprivedStudent->student->name ?? '' ;
            })
            ->editColumn('father_name', function ($SemesterDeprivedStudent) {
                return $SemesterDeprivedStudent->student->father_name ?? '' ;
            })
            ->editColumn('university_name', function ($SemesterDeprivedStudent) {
                return $SemesterDeprivedStudent->university_id ? ($SemesterDeprivedStudent->university->name ?? '' ) : '';
            })
            ->editColumn('department_name', function ($SemesterDeprivedStudent) {

                return $SemesterDeprivedStudent->department_id ? ($SemesterDeprivedStudent->department->name ?? '' ) : '';
            })
            ->editColumn('faculty_name', function ($SemesterDeprivedStudent) {

                return $SemesterDeprivedStudent->department_id ? ($SemesterDeprivedStudent->department->facultyName->name ?? '') : '';
            })
            ->editColumn('half_year', function ($SemesterDeprivedStudent) {
                return trans('general.'.$SemesterDeprivedStudent->half_year.'') ;
            })
            ->addColumn('action', function ($SemesterDeprivedStudent) {

                $html = '';
                $html = '<div class="btn-group">
                        <a class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false">';     
                        $html .= trans('general.action').'
                            <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu pull-right">';
                        if(auth()->user()->can('edit-semester-deprived') ){
                            $html .= '<li><a href="'. route('semester-deprived-student.edit', $SemesterDeprivedStudent) .'"  target="new"> <i class="fa fa-pencil"></i> '. trans("general.edit") .' </a></li>';

                        } 
                        if(auth()->user()->hasRole('super-admin') ){
                            if(isset($SemesterDeprivedStudent->deleted_at))
                            {
                                $html .= '<li><a href="'. route('semester-deprived-student.recover', $SemesterDeprivedStudent) .'"  target="new" onClick="doConfirm()" > <i class="fa fa-pencil"></i> '. trans("general.restore").' </a></li>';
                            } 
                        }   
                        
                        if (auth()->user()->can('delete-semester-deprived')) {
                            $html .= '<li><form action="'. route('semester-deprived-student.destroy', $SemesterDeprivedStudent) .'" method="post" style="display:inline">
                                <input type="hidden" name="_method" value="DELETE" />
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <button type="submit" style ="color:red" class="btn btn-link" onClick="doConfirm()"><i class="fa fa-trash" style="color:red"></i> '.trans("general.delete").' </button>
                            </form></li>';
                        }

                        $html .= '</ul>
                        </div>';
                return $html;
            })
            ->rawColumns( ['action']);
            
            return $datatables;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(SemesterDeprivedStudent $SemesterDeprivedStudent)
    {
        $query = SemesterDeprivedStudent::select(
            'semester_deprived_students.id', 
            'semester_deprived_students.university_id',
            'semester_deprived_students.department_id',
            'semester_deprived_students.student_id',
            'semester_deprived_students.educational_year',
            'semester_deprived_students.semester',
            'semester_deprived_students.half_year',
            'semester_deprived_students.deleted_at'
        )
        ->with(['student:id,name,form_no,father_name'])
        ->with(['university:id,name'])
        ->with(['department:id,name,faculty_id', 'department.facultyName:id,name'])
        ;
        if(auth()->user()->hasRole('super-admin'))  
        {
            $query =$query->withTrashed();
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
                        'dom'          => '<"top"Bl>rt<"bottom"ip>',
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
                                                                                                                    
                                if(this.index() >= 0 && this.index() <= 9) { 
                                    if (this.index() == 1) {
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
            'form_no'    => ['name' => 'student.form_no', 'title' => trans('general.form_no')],
            'name'     => ['name' => 'student.name','title' => trans('general.name')],
            'father_name'     => ['name' => 'student.father_name','title' => trans('general.father_name')],
            'university_name' => ['name' => 'university.name', 'title' => trans('general.university')],
            'faculty_name'    => ['name' => 'department.facultyName.name', 'title' => trans('general.faculty')],
            'department_name'    => ['name' => 'department.name', 'title' => trans('general.department')],
            'educational_year' => ['name' => 'educational_year','title' => trans('general.education_year')],
            'semester' => ['name' => 'semester','title' => trans('general.semester')],
            'half_year' => ['name' => 'half_year','title' => trans('general.half_year')]
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'SemesterDeprivedStudents_' . date('YmdHis');
    }
}