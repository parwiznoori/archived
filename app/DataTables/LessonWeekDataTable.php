<?php

namespace App\DataTables;

use App\Models\LessonWeek;
use Yajra\DataTables\Services\DataTable;

class LessonWeekDataTable extends DataTable
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
            ->editColumn('half_year', function ($lesson_weeks) {

                return trans('general.'.$lesson_weeks->half_year.'') ;
            })
            ->addColumn('action', function ($lesson_weeks) {

                $html = '';
                $html = '<div class="btn-group">
                        <a class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false">';     
                        $html .= trans('general.action').'
                            <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu pull-right">';
                        if(auth()->user()->can('edit-lesson-weeks') ){
                            $html .= '<li><a href="'. route('lesson_weeks.edit', $lesson_weeks) .'"  target="new"> <i class="fa fa-pencil"></i> '. trans("general.edit") .' </a></li>';

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
    public function query(LessonWeek $lesson_weeks)
    {
       
        $query = $lesson_weeks->select(
            'lesson_weeks.id', 
            'universities.name as university', 
            'departments.name as department',
            'lesson_weeks.education_year',
            'lesson_weeks.half_year',
            'lesson_weeks.number_of_weeks',  
        )
        ->leftJoin('universities', 'universities.id', '=', 'lesson_weeks.university_id')
        ->leftJoin('departments', 'departments.id', '=', 'lesson_weeks.department_id')
        ->orderBy('lesson_weeks.education_year','desc')
        ->orderBy('lesson_weeks.half_year','desc')
        ;
        
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
                                                                                                                    
                                if(this.index() >= 0 && this.index() <= 4) { 
                                    if (this.index() == 0 || this.index() == 4) {
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
            'university' =>    ['name' => 'universities.name', 'title' => trans('general.university')],
            'department' =>    ['name' => 'departments.name', 'title' => trans('general.department')],
            'education_year'       =>    ['title' => trans('general.education_year') ],
            'half_year'  =>    [ 'value' => trans('general.' . 'half_year'),'title' => trans('general.half_year')],
            'number_of_weeks'       =>    ['title' => trans('general.number_of_weeks') ]
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'LessonWeek_' . date('YmdHis');
    }
}