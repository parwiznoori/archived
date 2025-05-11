<?php

namespace App\DataTables;

use App\Models\Subject;
use Yajra\DataTables\Services\DataTable;

class SubjectsDataTable extends DataTable
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
            ->editColumn('active', function ($subject) {
                return $subject->active ? trans('general.active') : trans('general.inactive');
            })
            ->editColumn('type', function ($subject) {
                return trans('general.'.$subject->type);
            })
            ->editColumn('courses_count', function ($subject) {
                return $subject->courses_count;
            })
            ->addColumn('action', function ($subject) {
                $html = '';
                $html = '<div class="btn-group">
                <a class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false">';     
                $html .= trans('general.action').'
                    <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">';

                if (auth()->user()->can('edit-curriculum')) {
                    $html .= '<li><a href="'. route('subjects.edit', [$subject->university_id, $subject->department_id, $subject->id]) .'"  title = "'. trans('general.pdf').' " > <i class="fa fa-pencil"></i> '. trans("general.edit") .' </a></li>';
                }

                if(auth()->user()->hasRole('super-admin') ){
                    $html .= '<li><a href="'. route('curriculum.editCredit', [$subject->university_id, $subject->department_id, $subject->id]).'"  target="new" title = "'. trans('general.edit_credit').' " > <i class="fa fa-pencil"></i> '. trans("general.edit_credit") .' </a></li>';
                }

                if (auth()->user()->can('view-curriculum')) {
                    $html .= '<li><a href="'. route('subjects.courses_list', [$subject->university_id, $subject->department_id, $subject->id]) .'" target="new"  title = "'. trans('general.courses_list').' " > <i class="fa fa-table"></i> '. trans("general.courses_list") .' </a></li>';
                }

                if (auth()->user()->can('delete-curriculum') and ! $subject->courses_count ) {
                    $html .= '<li><form action="'. route('subjects.destroy',  [$subject->university_id, $subject->department_id, $subject->id]) .'" method="post" style="display:inline">
                        <input type="hidden" name="_method" value="DELETE" />
                        <input type="hidden" name="_token" value="'.csrf_token().'" />
                        <button type="submit" style ="color:red" class="btn btn-link" onClick="doConfirm()"><i class="fa fa-trash" style="color:red"></i> '.trans("general.delete").' </button>
                    </form></li>';
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
    public function query(Subject $model)
    {
        return $model->where(['university_id' => request()->segment(2), 'department_id' => request()->segment(3)])
        ->withCount('courses')
        ;
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
                                                                                                                    
                                if(this.index() >= 0 && this.index() <= 5) { 
                                    if (this.index() == 1 || this.index() == 3 ) {
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
            'code'     => ['title' => trans('general.code')],            
            'title' => ['title' => trans('general.title')],
            'title_eng' => ['title' => trans('general.title_eng')],
            'credits'    => ['title' => trans('general.credits')], 
            'semester'    => ['title' => trans('general.semester')], 
            'type'    => ['title' => trans('general.type')],
            'active'    => ['title' => trans('general.status')],
           'courses_count' => ['name' => 'courses_count', 'title' => trans('general.courses_count'), 'searchable' => false, 'orderable' => false]
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Subjects_' . date('YmdHis');
    }
}
