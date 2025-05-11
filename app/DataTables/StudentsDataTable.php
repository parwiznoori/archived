<?php

namespace App\DataTables;

use App\Models\Student;
use Yajra\DataTables\Services\DataTable;

class StudentsDataTable extends DataTable
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
                ->editColumn('student_id', function ($student) {
                    return $student->id;
                })
                ->editColumn('status_name', function ($student) {
                    return '<span class="badge badge-'.$student->status_color.'">'.$student->status->title.'</span>';
                })
                ->editColumn('university_name', function ($student) {
                    return $student->university_id ? ($student->university->name ?? '' ) : '';
                })
                ->editColumn('department_name', function ($student) {
    
                    return $student->department_id ? ($student->department->name ?? '' ) : '';
                })
                ->editColumn('grade_name', function ($student) {
    
                    return $student->grade_id ? ($student->grade->name ?? '' ) : '';
                })
                ->editColumn('province_name', function ($student) {
    
                    return $student->province ? ($student->originalProvince->name ?? '' ) : '';
                })
                ->editColumn('faculty_name', function ($student) {
    
                    return $student->department_id ? ($student->department->facultyName->name ?? '') : '';
                })
                ->addColumn('action', function ($student) {
                    
                    $html = '';
    
                    $html = '<div class="btn-group">
                    <a class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false">';     
                    $html .= trans('general.action').'
                        <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu pull-right">';
                    if((auth()->user()->can('edit-student') and $student->status->editable) or (auth()->user()->hasRole('super-admin'))  ){ // 
                        $html .= '<li><a href="'. route('students.edit', $student) .'"  target="new" title = "'. trans('general.edit').' " > <i class="fa fa-pencil"></i> '. trans("general.edit") .' </a></li>';
                    }  
                    if(auth()->user()->can('edit-student') ){ // 
                        $html .= '<li><a href="'. route('students.allDetails', $student) .'"  target="new" title = "'. trans('general.all_details_of_student').' " > <i class="fa fa-list"></i> '. trans("general.all_details_of_student") .' </a></li>';
                    }  
                  
                    if(auth()->user()->can('create-graduated-student') && $student->status_id == 2 ){ // 
                        $html .= '<li><a href="'. route('students.manualGraduate', $student) .'"  target="new" title = "'. trans('general.add_individual_student_to_graduation').' " > <i class="fa fa-list"></i> '. trans("general.add_individual_student_to_graduation") .' </a></li>';
                    }  

                    if(auth()->user()->can('view-activity_logs')){
                        $html .= '<li><a href="'. route('activities.list', $student) .'"  target="new" title = "'. trans('general.history').' " > <i class="fa fa-history"></i> '. trans("general.history") .' </a></li>';
                    }  

                    if (auth()->user()->can('view-student')) {
                        $html .= '<li><a href="'. route('students.show', $student) .'"  target="new" title = "'.trans('general.print_student_sawaneh').' " > <i class="icon-printer"> </i> '. trans("general.print_student_sawaneh") .' </a></li>';    
                    }  
                    if (auth()->user()->can('view-student')) {
                        $html .= '<li><a href="'. route('students.showgroup', $student) .'"  target="new" title = "'.trans('general.view_group_of_student').' " > <i class="icon-table"> </i> '. trans("general.view_group_of_student") .' </a></li>';    
                    } 

                    
                    if (auth()->user()->can('create-cardExpiredate')) {
                        $html .= '<li><a href="'. route('students.expiredate', $student) .'"   title = "' .trans('general.create-cardExpiredate').' " > <i class="icon-printer"> </i> '.trans("general.create-cardExpiredate") .' </a></li>';    
                    }
                    if (auth()->user()->can('print-studentCard')) {
                        $html .= '<li><a href="'. route('students.card', $student) .'"   title = "' .trans('general.print_card_info').' " > <i class="icon-printer"> </i> '.trans("general.print_card") .' </a></li>';    
                    }  

                    if(auth()->user()->hasRole('system-developer')  ) {
                        $html .= '<li><a href="'. route('students.show-card', $student) .'"   title = "' .trans('general.print_card_info').' " > <i class="eye"> </i> '.'show card' .' </a></li>';    
                    }

                    // if (auth()->user()->can('print-transcript')) {
                    //     $html .= '<li><a href="'. route('student.transcript', $student) .'"  target="new" title = "' .trans('general.print_transcript').'"><i class="fa fa-credit-card"></i> '. trans("general.print_transcript").' </a></li>';    
                    // } 
                   // This is for deploma
                    // if (auth()->user()->can('print-diploma')) {
                    //     $html .= '<li><a href="'. route('student.diploma', $student) .'"  target="new" title = "' .trans('general.print_diploma').'"><i class="fa fa-credit-card"></i> '. trans("general.print_diploma").' </a></li>';    
                    // } 
                   

                    if (auth()->user()->can('create-cardandDiplomaimage')) {
                        $html .= '<li><a href="'. route('students.diplomaphoto', $student) .'"  target="new" title = "' .trans('general.create_diplomaphoto').' " > <i class="icon-printer"> </i> '.trans("general.create_diplomaphoto") .' </a></li>';    
                    } 

                    if (auth()->user()->allUniversities()) {
                        $html .= '<li><form action="'. route('students.destroy', $student) .'" method="post" style="display:inline">
                            <input type="hidden" name="_method" value="DELETE" />
                            <input type="hidden" name="_token" value="'.csrf_token().'" />
                            <button type="submit" style ="color:red" class="btn btn-link" onClick="doConfirm()"><i class="fa fa-trash" style="color:red"></i> '.trans("general.delete").' </button>
                        </form></li>';
                    }
                   
                    $html .= '</ul>
                    </div>';  
                    if ($student->status_id != 2 && $student->status_id < 5 ) {
                        $html .= '<form action="'. route('students.updateStatus', $student) .'" method="post" style="display:inline">
                                <input type="hidden" name="_method" value="patch" />
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <button type="submit" class="btn btn-xs btn-default" onClick="doConfirm()" style="margin-top: 5px">شامل پوهنتون</button>
                            </form>';
                    }
                    return $html;
                })
            ->rawColumns(['status_name', 'action']);
            //  return $datatables;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Student $model)
    {
        $input = request()->all();
        
        // dd($input);

        // $query = $model->select(
        //         'students.id', 
        //         'form_no',
        //         'students.name as name',
        //         'father_name',
        //         // 'grandfather_name',
        //         'provinces.name as province_name',
        //         'universities.name as university', 
        //         'departments.name as department',
        //         'faculties.name as faculty',
        //         'student_statuses.title as status',
        //         'student_statuses.tag_color as status_color',
        //         'status_id',
        //         'students.gender',
        //         // 'group_id',
        //         // 'groups.name as group_name',
        //         'students.university_id',
        //         'students.kankor_year',
        //         'student_statuses.editable',
        //         'grades.name as grade',
        //         'students.semester',
        //         'students.description',
        //         'student_sheft'
        //     )
        //     ->leftJoin('provinces', 'provinces.id', '=', 'students.province')
        //     ->leftJoin('universities', 'universities.id', '=', 'university_id')
        //     ->leftJoin('departments', 'departments.id', '=', 'department_id')
        //     ->leftJoin('faculties', 'faculties.id', '=', 'departments.faculty_id')
        //     // ->leftJoin('groups', 'groups.id', '=', 'students.group_id')
        //     ->leftJoin('grades', 'grades.id', '=', 'students.grade_id')
        //     ->leftJoin('student_statuses', 'student_statuses.id', '=', 'status_id')
        //     ->orderBy('students.kankor_year','desc');

            
            // if (isset($input['columns'][0]['search']['value']) and $input['columns'][0]['search']['value'] != '')
            //     $query->where('students.status_id', 'like', "%".$input['columns'][0]['search']['value']."%");

            // if (isset($input['columns'][1]['search']['value']) and $input['columns'][1]['search']['value'] != '')
            //     $query->where('students.form_no', 'like', "%".$input['columns'][1]['search']['value']."%");

            // if (isset($input['columns'][2]['search']['value']) and $input['columns'][2]['search']['value'] != '')
            //     $query->where('students.name', 'like', "%".$input['columns'][2]['search']['value']."%");

            // if (isset($input['columns'][3]['search']['value']) and $input['columns'][3]['search']['value'] != '')
            //     $query->where('students.father_name', 'like', "%".$input['columns'][3]['search']['value']."%");

            // if (isset($input['columns'][4]['search']['value']) and $input['columns'][4]['search']['value'] != '')
            //     $query->where('provinces.name', 'like', "%".$input['columns'][4]['search']['value']."%");

            // if (isset($input['columns'][5]['search']['value']) and $input['columns'][5]['search']['value'] != '')
            //     $query->where('departments.name', 'like', "%".$input['columns'][5]['search']['value']."%");

            // if (isset($input['columns'][6]['search']['value']) and $input['columns'][6]['search']['value'] != '')
            //     $query->where('faculties.name', 'like', "%".$input['columns'][6]['search']['value']."%");

            // if (isset($input['columns'][7]['search']['value']) and $input['columns'][7]['search']['value'] != '')
            //     $query->where('universities.name', 'like', "%".$input['columns'][7]['search']['value']."%");

            // if (isset($input['columns'][8]['search']['value']) and $input['columns'][8]['search']['value'] != '')
            //     $query->where('kankor_year', 'like', "%".$input['columns'][8]['search']['value']."%");

            // if (isset($input['columns'][9]['search']['value']) and $input['columns'][9]['search']['value'] != '')
            //     $query->where('grades.name', 'like', "%".$input['columns'][9]['search']['value']."%");

            // if (isset($input['columns'][10]['search']['value']) and $input['columns'][10]['search']['value'] != '')
            // $query->where('students.semester', 'like', "%".$input['columns'][10]['search']['value']."%");

            // if (isset($input['columns'][11]['search']['value']) and $input['columns'][11]['search']['value'] != '')
            //     $query->where('students.student_sheft', 'like', "%".$input['columns'][11]['search']['value']."%");
            // if (isset($input['columns'][12]['search']['value']) and $input['columns'][12]['search']['value'] != '')
            //     $query->where('groups.name', 'like', "%".$input['columns'][12]['search']['value']."%");
           
        // return $query;

        $query = Student::select(
                    'students.id', 
                    'students.form_no',
                    'students.name as name',
                    'students.father_name',
                    'students.status_id',
                    'students.gender',
                    'students.province',
                    'students.university_id',
                    'students.department_id',
                    'students.grade_id',
                    'students.kankor_year',
                    'students.kankor_result',
                    'students.semester',
                    'students.description',
                    'students.student_sheft',
                    'students.email'
                )
        ->with(['university'=>function($q){
            $q->select('id','name');
        }])
        ->with(['department'=>function($q){
            $q->select('id','name','faculty_id')
                ->with(['facultyName'=>function($q){
                    $q->select('id','name');
                }])
            ;
        }])
        ->with(['status'=>function($q){
            $q->select('id','title','editable','tag_color');
        }])
        ->with(['originalProvince'=>function($q){
            $q->select('id','name');
        }])
        ->with(['grade'=>function($q){
            $q->select('id','name');
        }])
        ;

        if (isset($input['columns'][0]['search']['value']) and $input['columns'][0]['search']['value'] != '')
            $query->where('students.status_id', 'like', "%".$input['columns'][0]['search']['value']."%");
        
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
                        'order'        =>  [[1, 'desc']],
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
                                                                                                                    
                                if(this.index() >= 1 && this.index() <= 16) { 
                                    if (this.index() == 2 || this.index() == 9) {
                                        $('<input class=\"datatable-footer-input ltr \" placeholder=\"'+$(column.header()).text()+'\" name=\"'+ column.index() + '\" value=\"'+ (state ? state.columns[this.index()].search.search : emptyValue) +'\" />').appendTo($(column.footer()).empty())                                        
                                        .on(onEvent, function () {
                                            column.search($(this).val(), false, false, true).draw();
                                        });
                                    }else if(this.index() == 7)
                                    {

                                    }
                                    else {
                                        $('<input class=\"datatable-footer-input \" placeholder=\"'+$(column.header()).text()+'\" name=\"'+ column.index() + '\" value=\"'+ (state ? state.columns[this.index()].search.search : emptyValue) +'\" />').appendTo($(column.footer()).empty())                                        
                                        .on(onEvent, function () {
                                            column.search($(this).val(), false, false, true).draw();
                                        });
                                    }
                                }
                            });

                            $('a.student-status').click(function () {
                                if ($(this).attr('data-status-id') == 'all')
                                {
                                    console.log('a link clicked with status all');
                                    table.api().columns(0).search('', false, false, true).draw();
                                }
                                else
                                {
                                    console.log('a link clicked with status ' + $(this).attr('data-status-id'));
                                    table.api().columns(0).search($(this).attr('data-status-id'), false, false, true).draw();
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
            'status_id' => ['name' => 'students.status_id', 'visible' => false, 'class' => 'hide', 'searchable' => false, 'orderable' => false],
            'student_id' => ['name' => 'students.id', 'title' => trans('general.id'),'visible' => true ],
            'form_no'   => ['name' => 'form_no', 'title' => trans('general.form_no')],
            'name'      => ['name' => 'name','title' => trans('general.name')],
            'father_name'     => ['title' => trans('general.father_name')],
            'province_name'     => [ 'name' => 'originalProvince.name', 'title' => trans('general.province')],
            'university_name' => ['name' => 'university.name', 'title' => trans('general.university')],
            'faculty_name'    => ['name' => 'department.facultyName.name', 'title' => trans('general.faculty'), 'searchable' => false, 'orderable' => false],
            'department_name' => ['name' => 'department.name', 'title' => trans('general.department')],
            'kankor_year' => ['title' => trans('general.kankor_year')],
            'grade_name' => ['name' => 'grade.name', 'title' => trans('general.grade'), 'searchable' => false],
            'semester'     => ['title' => trans('general.semester')],
            'student_sheft'     => ['value' => trans('general.' . 'student_sheft'),'title' => trans('general.student_sheft')],
            'gender'     => ['title' => trans('general.gender')],
            'description'     => ['title' => trans('general.descriptionstu')],
            'kankor_result'     => ['title' => trans('general.kankor_result')],
            'email' => ['name' => 'students.email', 'title' => trans('general.email')],
            'status_name' => ['name' => 'status.title', 'title' => trans('general.status')],
        ];
    }
    
    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Students_' . date('YmdHis');
    }
}