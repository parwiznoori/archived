<?php

namespace App\DataTables;

use App\Models\Monograph;
use Yajra\DataTables\Services\DataTable;

class MonographDataTable extends DataTable
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

            ->setRowClass(function ($monograph) {
                $className='';
                if(isset($monograph->deleted_at))
                {
                    $className.='row_deleted';
                }
                return $className;
            })
            ->editColumn('form_no', function ($monograph) {
                return $monograph->student->form_no ?? '' ;
            })
            ->editColumn('name', function ($monograph) {
                return $monograph->student->name ?? '' ;
            })
            ->editColumn('father_name', function ($monograph) {
                return $monograph->student->father_name ?? '' ;
            })
            ->editColumn('university_name', function ($monograph) {
                return $monograph->university_id ? ($monograph->university->name ?? '' ) : '';
            })
            ->editColumn('department_name', function ($monograph) {

                return $monograph->department_id ? ($monograph->department->name ?? '' ) : '';
            })
            ->editColumn('faculty_name', function ($monograph) {

                return $monograph->department_id ? ($monograph->department->facultyName->name ?? '') : '';
            })
            ->editColumn('teacher_name', function ($monograph) {

                return $monograph->teacher_id ? ($monograph->teacher->full_name .' (نام پدر : '.$monograph->teacher->father_name.')'  ?? '' ) : '';
            })
            ->addColumn('action', function ($monograph) {

                $html = '';
                $html = '<div class="btn-group">
                        <a class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false">';     
                        $html .= trans('general.action').'
                            <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu pull-right">';
                        if(auth()->user()->can('edit-monograph') ){
                            $html .= '<li><a href="'. route('monographs.edit', $monograph) .'"  target="new"> <i class="fa fa-pencil"></i> '. trans("general.edit") .' </a></li>';

                        } 
                        if(auth()->user()->hasRole('super-admin') ){
                            if(isset($monograph->deleted_at))
                            {
                                // $html .= '<li><a href="'. route('monographs.recover', $monograph) .'"  target="new" onClick="doConfirm()" > <i class="fa fa-pencil"></i> '. trans("general.restore").' </a></li>';

                            }
                           
                        }   
                        
                        if (auth()->user()->can('delete-monograph')) {
                            $html .= '<li><form action="'. route('monographs.destroy', $monograph) .'" method="post" style="display:inline">
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
    public function query(Monograph $monograph)
    {
        $query = Monograph::select(
            'monographs.id', 
            'monographs.university_id',
            'monographs.department_id',
            'monographs.student_id',
            'monographs.teacher_id',
            'monographs.title',
            'monographs.defense_date',
            'monographs.score',
            'monographs.deleted_at'
        )
        ->with(['student:id,name,form_no,father_name'])
        ->with(['university:id,name'])
        ->with(['department:id,name,faculty_id', 'department.facultyName:id,name'])
        ->with(['teacher:id,name,last_name,father_name'])
        ;

       
        // $query = $monograph->select(
        //     'monographs.id', 
        //     'monographs.score',
        //     'form_no',
        //     'monographs.title',
        //     'defense_date',
        //     'students.name as name',
        //     'students.father_name as father_name',
        //     'universities.name as university', 
        //     'departments.name as department',
        //     \DB::raw('CONCAT(teachers.name," ",teachers.last_name,"-",teachers.father_name) as teacher'),
           
        // )
        // ->leftJoin('students', 'students.id', '=', 'monographs.student_id')
        // ->leftJoin('universities', 'universities.id', '=', 'monographs.university_id')
        // ->leftJoin('departments', 'departments.id', '=', 'monographs.department_id')
        // ->leftJoin('teachers', 'teachers.id', '=', 'monographs.teacher_id')
        // ->orderBy('students.name','desc');

       
        

        
        
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
                                                                                                                    
                                if(this.index() >= 0 && this.index() <= 8) { 
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
            'title'     => [ 'name' => 'title', 'title' => trans('general.monograph_title')],
            'teacher_name'     => [ 'name' => 'teacher.name', 'title' => trans('general.teacher')],
            'university_name' => ['name' => 'university.name', 'title' => trans('general.university')],
            'faculty_name'    => ['name' => 'department.facultyName.name', 'title' => trans('general.faculty')],
            'department_name'    => ['name' => 'department.name', 'title' => trans('general.department')],
            'defense_date' => ['name' => 'defense_date','title' => trans('general.defense_date')],
            'score' => ['name' => 'score','title' => trans('general.score')]
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Monograph_' . date('YmdHis');
    }
}