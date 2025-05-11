<?php

namespace App\DataTables;

use App\Models\University;
use Yajra\DataTables\Services\DataTable;

class UniversitiesDataTable extends DataTable
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
            ->addColumn('action', function ($university) {
                $html = '';
                $html = '<div class="btn-group">
                <a class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false">';     
                $html .= trans('general.action').'
                    <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">';

                if (request()->is('universities*')) {
                    
                    if (auth()->user()->can('view-faculty')) {
                        $html .= '<li><a href="'. route('faculties.index', $university) .'"  title = "'. trans('models/faculty.plural').' " > <i class="fa fa-list"></i> '. trans('models/faculty.plural').'<span class="badge badge-success">'.$university->faculties_count.'</span>' .' </a></li>';
                    }

                    if (auth()->user()->can('view-department')) {
                        $html .= '<li><a href="'. route('departments.index', $university) .'"  title = "'. trans('general.departments').' " > <i class="fa fa-list"></i> '. trans('general.departments').'<span class="badge badge-success">'.$university->departments_count.'</span>' .' </a></li>';
                    }
                    if (auth()->user()->can('edit-university')) {
                        $html .= '<li><a href="'. route('universities.edit', $university) .'"  title = "'. trans('general.edit').' " > <i class="fa fa-pencil"></i> '. trans("general.edit") .' </a></li>';
                    }

                    if (auth()->user()->can('delete-university') and ! $university->departments_count ) {
                        $html .= '<li><form action="'.route('universities.destroy', $university)  .'" method="post" style="display:inline">
                            <input type="hidden" name="_method" value="DELETE" />
                            <input type="hidden" name="_token" value="'.csrf_token().'" />
                            <button type="submit" style ="color:red" class="btn btn-link" onClick="doConfirm()"><i class="fa fa-trash" style="color:red"></i> '.trans("general.delete").' </button>
                        </form></li>';
                    } 

                    if (auth()->user()->can('view-user-log')) {
                        $html .= '<li><a href="'. route('university.users_list', $university) .'" target="new"  title = "'. trans('general.users_list').' " > <i class="fa fa-table"></i> '. trans("general.users_list") .' </a></li>';
                    }

                    if (auth()->user()->can('view-university')) {
                        $html .= '<li><a href="'. route('university.activities', $university) .'" target="new"  title = "'. trans('general.view_activities').' " > <i class="fa fa-table"></i> '. trans("general.view_activities") .' </a></li>';
                    }
                    
                } elseif (request()->is('curriculum*')) {
                    $html .= '<li><a href="'.route('curriculum.departments', $university).'" class="btn btn-default btn-xs">'.trans('general.departments').'</a></li>';
                }
                $html .= '</ul>
                </div>';
                return $html;
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\University $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(University $model)
    {
        $university=$model->newQuery()->select('universities.id', 'universities.name','name_eng','chairman','student_affairs','province_id','provinces.name as province_name','universities.address','universities.phone_number','universities.email')
        ->leftJoin('provinces', 'provinces.id', '=', 'province_id')
        ;
        $university->withCount('faculties');
        $university->withCount('departments');
        return  $university;
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
                    ->addAction(['title' => trans('general.action'), 'width' => '80px'])
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
                                    if (this.index() == 1 || this.index() == 2 ) {
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
            'id'     => ['title' => trans('general.id')],
            'name'     => ['title' => trans('general.name')],                              
            'name_eng'     => ['title' => trans('general.name_eng')],                              
            // 'chairman'     => ['title' => trans('general.university_chairman')],                              
            // 'student_affairs'     => ['title' => trans('general.university_student_affairs')],  
            'province_name' => ['name' => 'provinces.name' ,'title' => trans('general.province_name')],                            
            'phone_number' => ['name' => 'universities.phone_number' ,'title' => trans('general.phone')],                            
            'email' => ['name' => 'universities.email' ,'title' => trans('general.email')],                            
            'address' => ['name' => 'universities.address' ,'title' => trans('general.address')],                            
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Universities_' . date('YmdHis');
    }
}
