<?php

namespace App\DataTables;

use App\Models\Course;
use Yajra\DataTables\Services\DataTable;

class CourseDataTable extends DataTable
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
            ->editColumn('active', function ($course) {
                return $course->active ? "<i class='fa fa-check font-green'></i>" : "<i class='fa fa-remove font-red'></i>";
            })
            ->editColumn('approve_by_teacher', function ($course) {
                return $course->approve_by_teacher ? "<i class='fa fa-check font-green'></i>" : "<i class='fa fa-remove font-red'></i>";
            })
            ->editColumn('course_status_id', function ($course) {
                return $course->course_status_id >= 1  ? ($course->course_status->name ?? "" ) : "<i class='fa fa-remove font-red'></i>" ;
            })
            ->editColumn('university_name', function ($course) {
                return $course->university_id ? ($course->university->name ?? '' ) : '';
            })
            ->editColumn('department_name', function ($course) {

                return $course->department_id ? ($course->department->name ?? '' ) : '';
            })
            ->editColumn('subject_name', function ($course) {

                return $course->subject_id ? ($course->subject->title ?? '' ) : '';
            })
            ->editColumn('teacher_name', function ($course) {

                return $course->teacher_id ? ($course->teacher->teacher_name ?? '' ) : '';
            })
           
            ->editColumn('half_year', function ($course) {

                return trans('general.'.$course->half_year.'') ;
            })
            ->editColumn('scores_count', function ($course) {

                return $course->scores_count ;
            })
            ->addColumn('group_name', function ($course) {
                $groups=$course->groups;
                $group_array=array();
                $j=0;
               foreach($groups as $group)
               {
                $group_array[$j++]=$group->name.'[ id : '.$group->id.']';
               }
               return implode(',',$group_array);
            })
            ->setRowClass(function ($course) {
                $className='';
                if(isset($course->deleted_at))
                {
                    $className.='row_deleted';
                }
                if(isset($course->course_status_id) &&  $course->course_status_id >=1 )
                {
                    $className.=' final_approved';
                }
                return $className;
            })
            ->addColumn('action', function ($course) {

                $html = '';
                $html = '<div class="btn-group">
                        <a class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false">';     
                        $html .= trans('general.action').'
                            <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu pull-right">';
                        if(auth()->user()->can('edit-course') and $course->course_status_id < 1){
                            $html .= '<li><a href="'. route('courses.edit', $course) .'"  target="new"> <i class="fa fa-pencil"></i> '. trans("general.edit") .' </a></li>';

                        } 
                        if(auth()->user()->hasRole('super-admin') ){
                            if(isset($course->deleted_at))
                            {
                                $html .= '<li><a href="'. route('courses.recover', $course) .'"  target="new" onClick="doConfirm()" > <i class="fa fa-pencil"></i> '. trans("general.restore").' </a></li>';

                            }
                           
                        }   
                        if(auth()->user()->hasRole('super-admin') ){
                            if(isset($course->course_status_id) && $course->course_status_id >= 1)
                            {
                                $html .= '<li><a href="'. route('courses.backToNormal', $course) .'"  target="new" onClick="doConfirm()" > <i class="fa fa-pencil"></i> '. trans("general.back_to_normal").' </a></li>';

                            }
                            if(isset($course->approve_by_teacher) && $course->approve_by_teacher >= 1)
                            {
                                $html .= '<li><a href="'. route('courses.removeTeacherApproved', $course) .'"  target="new" onClick="doConfirm()" > <i class="fa fa-pencil"></i> '. trans("general.removeTeacherApproved").' </a></li>';

                            }
                           
                        }     

                        if (auth()->user()->can('view-course')) {
                            $html .= '<li><a href="'. route('attendance.create', $course) .'"  target="new"> <i class="fa fa-list"></i> '. trans("general.list") .' </a></li>';    
                        }                       

                        if (auth()->user()->can('delete-course') and $course->course_status_id < 1 and $course->scores_count <= 0 ) {
                            $html .= '<li><form action="'. route('courses.destroy', $course) .'" method="post" style="display:inline">
                                <input type="hidden" name="_method" value="DELETE" />
                                <input type="hidden" name="_token" value="'.csrf_token().'" />
                                <button type="submit" style ="color:red" class="btn btn-link" onClick="doConfirm()"><i class="fa fa-trash" style="color:red"></i> '.trans("general.delete").' </button>
                            </form></li>';
                        }
                        // if ($course->scores_count) {
                        //     $html .= '<li><span class="badge badge-success">'.$course->courses_count.'</span></li>';
                        // }

                        $html .= '</ul>
                        </div>';

                return $html;
            })
            ->rawColumns( ['action','active','course_status_name','group_name','approve_by_teacher','course_status_id']);
            
            return $datatables;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Course $model)
    {
        $input = request()->all();
        $query = Course::select(
            'courses.id',
            'courses.code',
            'courses.year',
            'courses.half_year',
            'courses.semester',
            'courses.subject_id',
            'courses.teacher_id',
            'courses.university_id',
            'courses.department_id',
            'courses.active',
            'courses.approve_by_teacher',
            'courses.course_status_id',
            'courses.deleted_at',
        )
        ->with(['university'=>function($q){
            $q->select('id','name');
        }])
        ->with(['department'=>function($q){
            $q->select('id','name');
        }])
        ->with(['subject'=>function($q){
            $q->select('id','title');
        }])
        ->with(['teacher'=>function($q){
            $q->select('id','name',\DB::raw('CONCAT(name," ", COALESCE(last_name," "),"- (نام پدر)",father_name) as teacher_name'));
        }])
        ->with(['course_status'=>function($q){
            $q->select('id','name');
        }])
        ->with(['groups'=>function($q){
            $q->select('groups.id','groups.name');
        }])
        ;
        $query->withCount('scores');

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
                                                                                                                    
                                if(this.index() >= 0 && this.index() <= 12) { 
                                    if (this.index() == 0) {
                                        $('<input class=\"datatable-footer-input ltr \" placeholder=\"'+$(column.header()).text()+'\" name=\"'+ column.index() + '\" value=\"'+ (state ? state.columns[this.index()].search.search : emptyValue) +'\" />').attr('size',10).appendTo($(column.footer()).empty())                                        
                                        .on(onEvent, function () {
                                            column.search($(this).val(), false, false, true).draw();
                                        });
                                    } 
                                    else if(this.index() == 3)
                                    {
                                        let select = document.createElement('select');
                                        select.add(new Option(''));
                                        select.add(new Option('بهاری','spring'));
                                        select.add(new Option('خزانی','fall'));
                                        column.footer().replaceChildren(select);
                                        select.addEventListener('change', function () {
                                            column.search($(this).val(), false, false, true).draw();
                                        });
                                    }
                                    else if(this.index() == 9)
                                    {
                                        let select = document.createElement('select');
                                        select.add(new Option(''));
                                        select.add(new Option('فعال','1'));
                                        select.add(new Option('غیرفعال','0'));
                                        column.footer().replaceChildren(select);
                                        select.addEventListener('change', function () {
                                            column.search($(this).val(), false, false, true).draw();
                                        });
                                    }
                                    else if(this.index() == 10)
                                    {
                                        let select = document.createElement('select');
                                        select.add(new Option(''));
                                        select.add(new Option('بله','1'));
                                        select.add(new Option('خیر','0'));
                                        column.footer().replaceChildren(select);
                                        select.addEventListener('change', function () {
                                            column.search($(this).val(), false, false, true).draw();
                                        });
                                    }
                                    else if(this.index() == 11)
                                    {
                                        let select = document.createElement('select');
                                        select.add(new Option(''));
                                        select.add(new Option('تایید نهایی چانس اول','1'));
                                        select.add(new Option('تایید نهایی چانس دوم','2'));
                                        select.add(new Option('تایید نهایی چانس سوم','3'));
                                        select.add(new Option('تایید نهایی چانس چهارم','4'));
                                        column.footer().replaceChildren(select);
                                        select.addEventListener('change', function () {
                                            column.search($(this).val(), false, false, true).draw();
                                        });
                                    }
                                    else {
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
            'id'      => ['name' => 'courses.id', 'title' => trans('general.id')],
            'code'     => ['title' => trans('general.code')],
            'year'     => ['title' => trans('general.year')],
            'half_year'   => [ 'value' => trans('general.' . 'half_year'),'title' => trans('general.half_year')],
            'semester'     => ['title' => trans('general.semester')],
            'subject_name'     => [ 'name' => 'subject.title', 'title' => trans('general.subject')],
            'teacher_name'     => [ 'name' => 'teacher.name', 'title' => trans('general.teacher')],
            'department_name' => ['name' => 'department.name', 'title' => trans('general.department')],
            'university_name' => ['name' => 'university.name', 'title' => trans('general.university')],
            'active'     => ['title' => trans('general.active')],
            'approve_by_teacher' => ['name' => 'courses.approve_by_teacher','title' => trans('general.approved_by_teacher')],
            'course_status_id' => ['name' => 'courses.course_status_id','title' => trans('general.final_approved')],
            'group_name'     => ['name' =>'groups.name' ,'title' => trans('general.group')],
            'scores_count' => ['name' => 'scores_count', 'title' => trans('general.scores_count'), 'searchable' => false, 'orderable' => false],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Courses_' . date('YmdHis');
    }
}