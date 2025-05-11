<?php

namespace App\DataTables;

use App\Models\GraduatedStudent;
use Yajra\DataTables\Services\DataTable;

class GraduatedStudentsDataTable extends DataTable
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
                ->editColumn('manual_graduated', function ($graduatedStudent) {
                    $status_name='';
                    if($graduatedStudent->manual_graduated==1)
                    {
                        $status_name= 'انفرادی';
                    }
                    else
                    {
                        $status_name= 'سیستم';
                    }
                    return '<span class="badge ">'.$status_name.'</span>';
                })
                ->editColumn('form_no', function ($graduatedStudent) {
                    return $graduatedStudent->student->form_no ?? '' ;
                })
                ->editColumn('name', function ($graduatedStudent) {
                    return $graduatedStudent->student->name ?? '' ;
                })
                ->editColumn('father_name', function ($graduatedStudent) {
                    return $graduatedStudent->student->father_name ?? '' ;
                })
                ->editColumn('kankor_year', function ($graduatedStudent) {
                    return $graduatedStudent->student->kankor_year ?? '' ;
                })
                ->editColumn('gender', function ($graduatedStudent) {
                    return $graduatedStudent->student->gender == "Male" ? 'ذکور' : 'اناث' ;
                })
                ->editColumn('province_name', function ($graduatedStudent) {
                    return $graduatedStudent->student->originalProvince->name ?? ''  ;
                })
                ->editColumn('university_name', function ($graduatedStudent) {
                    return $graduatedStudent->university_id ? ($graduatedStudent->university->name ?? '' ) : '';
                })
                ->editColumn('department_name', function ($graduatedStudent) {
    
                    return $graduatedStudent->department_id ? ($graduatedStudent->department->name ?? '' ) : '';
                })
                ->editColumn('faculty_name', function ($graduatedStudent) {
    
                    return $graduatedStudent->department_id ? ($graduatedStudent->department->facultyName->name ?? '') : '';
                })
                ->editColumn('grade_name', function ($graduatedStudent) {
    
                    return $graduatedStudent->grade_id ? ($graduatedStudent->grade->name ?? '' ) : '';
                })
                ->addColumn('action', function ($graduatedStudent) {
                    
                    $html = '';
    
                    $html = '<div class="btn-group">
                    <a class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false">';     
                    $html .= trans('general.action').'
                        <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu pull-right">';
                    if(auth()->user()->can('edit-graduated-student')){
                        $html .= '<li><a href="'. route('graduated-students.edit', $graduatedStudent) .'"  target="new" title = "'. trans('general.edit').' " > <i class="fa fa-pencil"></i> '. trans("general.edit") .' </a></li>';
                    }                         
                    if (auth()->user()->allUniversities()) {
                        $html .= '<li><form action="'. route('graduated-students.destroy', $graduatedStudent) .'" method="post" style="display:inline">
                            <input type="hidden" name="_method" value="DELETE" />
                            <input type="hidden" name="_token" value="'.csrf_token().'" />
                            <button type="submit" style ="color:red" class="btn btn-link" onClick="doConfirm()"><i class="fa fa-trash" style="color:red"></i> '.trans("general.delete").' </button>
                        </form></li>';
                    }

                    if (auth()->user()->can('print-transcript')) {
                        $html .= '<li><a href="'. route('student.transcript', $graduatedStudent) .'"  target="new" title = "' .trans('general.print_transcript').'"><i class="fa fa-credit-card"></i> '. trans("general.print_transcript").' </a></li>';  
                        
                        $html .= '<li><a href="'. route('student.transcript-dr', $graduatedStudent) .'"  target="new" title = "' .trans('general.print_transcript_dari').'"><i class="fa fa-credit-card"></i> '. trans("general.print_transcript_dari").' </a></li>'; 

                        $html .= '<li><a href="'. route('student.transcript-ps', $graduatedStudent) .'"  target="new" title = "' .trans('general.print_transcript_pashto').'"><i class="fa fa-credit-card"></i> '. trans("general.print_transcript_pashto").' </a></li>'; 

                        $html .= '<li><a href="'. route('student.graduate-results', $graduatedStudent) .'"  target="new" title = "' .trans('general.graduate-results').'"><i class="fa fa-credit-card"></i> '. trans("general.graduate-results").' </a></li>'; 
                    } 
                  
                    if (auth()->user()->can('print-diploma')) {
                        $html .= '<li><a href="'. route('student.diploma', $graduatedStudent) .'"  target="new" title = "' .trans('general.print_diploma').'"><i class="fa fa-credit-card"></i> '. trans("general.print_diploma").' </a></li>';    
                    } 
                   
                    $html .= '</ul>
                    </div>';  
                    return $html;
                })
            ->rawColumns(['action','manual_graduated']);
            
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(GraduatedStudent $model)
    {
        $input = request()->all();

        $query = GraduatedStudent::select(
            'graduated_students.id', 
            'graduated_students.student_id',
            'graduated_students.description',
            'graduated_students.university_id',
            'graduated_students.department_id',
            'graduated_students.grade_id',
            'graduated_students.graduated_year',
            'graduated_students.manual_graduated',
        )
        // ->with(['student'=>function($q){
        //     $q->select('id','name','form_no','father_name','gender','province','kankor_year');
        // }])
        ->with(['student:id,name,form_no,father_name,gender,province,kankor_year', 'student.originalProvince:id,name'])
        // ->with(['studentProvince'=>function($q){
        //     $q->select('id','name');
        // }])
        ->with(['university'=>function($q){
            $q->select('id','name');
        }])
        ->with(['department:id,name,faculty_id', 'department.facultyName:id,name'])
        // ->with(['department'=>function($q){
        //     $q->select('id','name','faculty_id')
        //         ->with(['facultyName'=>function($q){
        //             $q->select('id','name');
        //         }])
        //     ;
        // }])
        ->with(['grade'=>function($q){
            $q->select('id','name');
        }])
        ;

        // $query = $model->select(
        //     'graduated_students.id', 
        //     'students.form_no',
        //     'students.name as name',
        //     'students.father_name',
        //     'graduated_year',
        //     'students.grandfather_name',
        //     'provinces.name as province_name',
        //     'universities.name as university', 
        //     'departments.name as department',
        //     'faculties.name as faculty',
        //     'students.gender',
        //     'graduated_students.university_id',
        //     'manual_graduated',
        //     'graduated_students.description',
        //     'students.kankor_year',
        //     'grades.name as grade',
        //     'student_sheft'
        // )
        // ->leftJoin('students', 'students.id', '=', 'graduated_students.student_id')
        // ->leftJoin('provinces', 'provinces.id', '=', 'students.province')
        // ->leftJoin('universities', 'universities.id', '=', 'graduated_students.university_id')
        // ->leftJoin('departments', 'departments.id', '=', 'graduated_students.department_id')
        // ->leftJoin('faculties', 'faculties.id', '=', 'departments.faculty_id')
        // ->leftJoin('grades', 'grades.id', '=', 'graduated_students.grade_id')
        // ->orderBy('students.kankor_year','desc')
        // ->orderBy('graduated_year','desc');
            
            // if (isset($input['columns'][0]['search']['value']) and $input['columns'][0]['search']['value'] != '')
            //     $query->where('graduated_students.form_no', 'like', "%".$input['columns'][0]['search']['value']."%");

            // if (isset($input['columns'][1]['search']['value']) and $input['columns'][1]['search']['value'] != '')
            //     $query->where('graduated_students.name', 'like', "%".$input['columns'][1]['search']['value']."%");

            // if (isset($input['columns'][2]['search']['value']) and $input['columns'][2]['search']['value'] != '')
            //     $query->where('graduated_students.father_name', 'like', "%".$input['columns'][2]['search']['value']."%");

            // if (isset($input['columns'][3]['search']['value']) and $input['columns'][3]['search']['value'] != '')
            //     $query->where('provinces.name', 'like', "%".$input['columns'][3]['search']['value']."%");

            // if (isset($input['columns'][4]['search']['value']) and $input['columns'][4]['search']['value'] != '')
            //     $query->where('departments.name', 'like', "%".$input['columns'][4]['search']['value']."%");

            // if (isset($input['columns'][5]['search']['value']) and $input['columns'][5]['search']['value'] != '')
            //     $query->where('departments.faculty', 'like', "%".$input['columns'][5]['search']['value']."%");

            // if (isset($input['columns'][6]['search']['value']) and $input['columns'][6]['search']['value'] != '')
            //     $query->where('universities.name', 'like', "%".$input['columns'][6]['search']['value']."%");

            // if (isset($input['columns'][7]['search']['value']) and $input['columns'][7]['search']['value'] != '')
            //     $query->where('kankor_year', 'like', "%".$input['columns'][7]['search']['value']."%");

            // if (isset($input['columns'][8]['search']['value']) and $input['columns'][8]['search']['value'] != '')
            //     $query->where('grades.name', 'like', "%".$input['columns'][8]['search']['value']."%");
           
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
                    ->addAction(['title' => trans('general.action'), 'width' => '10px'])
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
                                                                                                                    
                                if(this.index() >= 0 && this.index() <= 8) { 
                                    if (this.index() == 1 || this.index() == 7) {
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
            'form_no'     => ['name' => 'student.form_no', 'title' => trans('general.form_no')],
            'name'     => ['name' => 'student.name','title' => trans('general.name')],
            'father_name'     => ['name' => 'student.father_name','title' => trans('general.father_name')],
            'province_name'     => [ 'name' => 'student.originalProvince.name', 'title' => trans('general.province')],
            'department_name'    => ['name' => 'department.name', 'title' => trans('general.department')],
            'faculty_name'    => ['name' => 'department.facultyName.name', 'title' => trans('general.faculty')],
            'university_name' => ['name' => 'university.name', 'title' => trans('general.university')],
            'kankor_year' => ['name' => 'student.kankor_year','title' => trans('general.kankor_year')],
            'graduated_year' => ['title' => trans('general.graduated_year')],
            'grade_name' => ['name' => 'grade.name', 'title' => trans('general.grade'), 'searchable' => false],
            'gender'     => ['name' => 'student.gender','title' => trans('general.gender')],
            'manual_graduated' => [ 'title' => trans('general.status'), 'searchable' => false],
            'description'     => ['title' => trans('general.description')],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'GraduatedStudent_' . date('YmdHis');
    }
}