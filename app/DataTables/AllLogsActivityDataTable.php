<?php
namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use Spatie\Activitylog\Models\Activity;

class AllLogsActivityDataTable extends DataTable
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
            ->addColumn('action', function ($activity) {
                if( (auth()->user()->hasRole('system-developer')) )
                {
                    $html = '<div class="btn-group">
                    <a class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false">';     
                    $html .= trans('general.action').'
                        <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu pull-right">';
                    $html .= '<li><a href="'. route('allLogs.showLog', [$activity->id]) .'"  target="_blank" title = "'. trans('general.show').' " > <i class="icon-eye"></i> '. trans("general.show") .' </a></li>';
                    $html .= '</ul>
                    </div>';    
                    return $html;
                }  
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\University $department
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Activity $activity)
    {
        $query = Activity::select('id','log_name','description','subject_id','subject_type','causer_id','causer_type','created_at');
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
                                                                                                                    
                                if(this.index() >= 0 && this.index() <= 6) { 
                                    if (this.index() == 0) {
                                        $('<input class=\"datatable-footer-input ltr \" placeholder=\"'+$(column.header()).text()+'\" name=\"'+ column.index() + '\" value=\"'+ (state ? state.columns[this.index()].search.search : emptyValue) +'\" />').attr('size',10).appendTo($(column.footer()).empty())                                        
                                        .on(onEvent, function () {
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
            'log_name'    => ['name' => 'activity_log.log_name','title' => trans('general.log_name')],                   
            'description'    => ['title' => trans('general.description')],  
            'subject_id'     => ['name' => 'subject_id','title' => trans('general.subject_id')],                   
            'subject_type'     => ['title' => trans('general.subject_type')],                   
            'causer_id'     => ['name' => 'causer_id','title' => trans('general.causer_id')],                   
            'causer_type'     => ['title' => trans('general.causer_type')],                     
            'created_at'     => ['title' => trans('general.created_at')],                      
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'LogsActivity_' . date('YmdHis');
    }
}
