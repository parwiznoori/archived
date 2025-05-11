<?php

namespace App\DataTables;

use App\Models\Group;
use Yajra\DataTables\Services\DataTable;

class GroupsDataTable extends DataTable
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
            ->setRowClass(function ($group) {
                return isset($group->deleted_at)  ? 'row_deleted' : '';
            })
            ->editColumn('university_name', function ($group) {
                return $group->university->name;
            })
            ->editColumn('department_name', function ($group) {

                return $group->department_id ? $group->department->name : '';
            })
            ->editColumn('gender', function ($group) {
                $gender_name = '';
                switch($group->gender)
                {
                    case 'b':
                        $gender_name = trans('general.Male_and_Female');
                        break;

                    case 'm':
                        $gender_name = trans('general.Male');
                        break;

                    case 'f':
                        $gender_name = trans('general.Female');
                        break;    

                }
                return $gender_name;
            })
            ->addColumn('action', function ($group) {
                $html = '';
                $html = '<div class="btn-group">
                <a class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false">';     
                $html .= trans('general.action').'
                    <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">';

                if (request()->is('students*')) {

                    if (auth()->user()->can('group-view-list') and ! isset($group->deleted_at)) {
                        $html .= '<li><a href="'. route('groups.list', $group) .'" target="new"  title = "'. trans('general.students_list').' " > <i class="fa fa-users"></i> '. trans("general.students_list") .' </a></li>';
                    }

                    if (auth()->user()->can('group-view-list') and ! isset($group->deleted_at)) {
                        $html .= '<li><a href="'. route('groups.courses_list', $group) .'" target="new"  title = "'. trans('general.courses_list').' " > <i class="fa fa-table"></i> '. trans("general.courses_list") .' </a></li>';
                    }

                    if (auth()->user()->can('edit-group') and ! isset($group->deleted_at) ) {
                        $html .= '<li><a href="'. route('groups.edit', $group) .'"  title = "'. trans('general.edit').' " > <i class="fa fa-pencil"></i> '. trans("general.edit") .' </a></li>';
                    }
                    
                    if(auth()->user()->hasRole('super-admin') ){
                        if(isset($group->deleted_at))
                        {
                            $html .= '<li><a href="'. route('groups.recover', $group) .'" target="new" onClick="doConfirm()"  title = "'. trans('general.restore').' " > <i class="fa fa-undo"></i> '. trans("general.restore") .' </a></li>';
                        }
                        
                    }

                    if (auth()->user()->can('delete-group') and ! $group->students_count and ! $group->courses_count ) {
                        $html .= '<li><form action="'. route('groups.destroy', $group) .'" method="post" style="display:inline">
                            <input type="hidden" name="_method" value="DELETE" />
                            <input type="hidden" name="_token" value="'.csrf_token().'" />
                            <button type="submit" style ="color:red" class="btn btn-link" onClick="doConfirm()"><i class="fa fa-trash" style="color:red"></i> '.trans("general.delete").' </button>
                        </form></li>';
                    } 

                }
                elseif (request()->is('groups*')) {
                    $html .= '<a href="'.route('groups.courses.index', $group).'" class="btn btn-default btn-xs">'.trans('general.courses').'</a>';
                    if ($group->courses_count) {
                        $html .= '<span class="badge badge-success">'.$group->courses_count.'</span>';
                    }
                }
                
                $html .= '</ul>
                </div>';
                return $html;
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Group $model)
    {
        $query = Group::select('groups.*')
        ->with(['university'=>function($q){
            $q->select('id','name');
        }])
        ->with(['department'=>function($q){
            $q->select('id','name');
        }]);
        $query->withCount('students');
        $query->withCount('courses');

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
                    ->addAction(['title' => trans('general.action'), 'width' => '120px'])
                    // ->parameters($this->getBuilderParameters());
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
                                                                                                                    
                                if(this.index() >= 0 && this.index() <= 6) { 
                                    if (this.index() == 0) {
                                        $('<input class=\"datatable-footer-input ltr \" placeholder=\"'+$(column.header()).text()+'\" name=\"'+ column.index() + '\" value=\"'+ (state ? state.columns[this.index()].search.search : emptyValue) +'\" />').attr('size',10).appendTo($(column.footer()).empty())                                        
                                        .on(onEvent, function () {
                                            column.search($(this).val(), false, false, true).draw();
                                        });
                                    }
                                    else if(this.index() == 5)
                                    {
                                        let select = document.createElement('select');
                                        select.add(new Option(''));
                                        select.add(new Option('مرد','m'));
                                        select.add(new Option('زن','f'));
                                        select.add(new Option('مرد و زن','b'))
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
        $columns = [  
            'id'      => ['name' => 'groups.id', 'title' => trans('general.id')],          
            'name'     => ['name' => 'groups.name', 'title' => trans('general.name')],  
            'kankor_year'     => ['title' => trans('general.kankor_year')],          
            'description' => ['title' => trans('general.description')],
            'department_name' => ['name' => 'department.name', 'title' => trans('general.department')],
            'gender'     => ['title' => trans('general.gender')],   
        ];

        if (auth()->user()->allUniversities()) {
            $columns['university_name'] = ['name' => 'university.name', 'title' => trans('general.university')];
        }

        $columns['students_count'] = ['name' => 'students_count', 'title' => trans('general.students_count'), 'searchable' => false, 'orderable' => false];

        $columns['courses_count'] = ['name' => 'courses_count', 'title' => trans('general.courses_count'), 'searchable' => false, 'orderable' => false];

        return $columns;
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Group_' . date('YmdHis');
    }
}
