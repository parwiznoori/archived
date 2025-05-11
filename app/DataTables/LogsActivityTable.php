<?php

namespace App\DataTables;

use Yajra\DataTables\Services\DataTable;
use Spatie\Activitylog\Models\Activity;

class LogsActivityTable extends DataTable
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
            ->addColumn('action', function ($log) {
                $html = '';
                $html .= '<a href="'.route('activities.show', [request()->segment(2), $log]).'" class="btn btn-success btn-xs"><i class="icon-eye"></i></a>';   
                return $html;
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

        $activities = Activity::select('activity_log.id','log_name','description','users.name as name','causer_type','activity_log.created_at')
                   ->leftJoin('users','users.id', '=' , 'activity_log.causer_id')
                    ->where('activity_log.subject_id', request()->segment(2))
                    ->where('activity_log.subject_type', 'App\Models\Student');
        return $activities;
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
                    ->parameters($this->getBuilderParameters());
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
            'description'    => ['title' => trans('general.browser')],                   
            'name'     => ['name' => 'users.name','title' => trans('general.user_name')],                   
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
        return 'Activity_' . date('YmdHis');
    }
}
