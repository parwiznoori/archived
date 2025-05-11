<?php

namespace App\DataTables;

use App\User;
use Yajra\DataTables\Services\DataTable;

class UserAuthLogsDataTable extends DataTable
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
            ->addColumn('action', function ($department) {
                $html = '';
                // $html .= '<a href="'.route('departments.edit', [request()->segment(2), $department]).'" class="btn btn-success btn-xs"><i class="icon-eye"></i></a>';   
                return $html;
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\University $department
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $user)
    {
        // $model = $model->where('departments.university_id', request()->segment(2))->select('name', 'faculty', 'id','chairman','department_student_affairs');

        $user = User::find(request()->segment(2));
        return $user->authentications()->orderBy('login_at', 'desc')->get();
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
            'ip_address'    => ['title' => trans('general.ip_address')],                   
            'user_agent'    => ['title' => trans('general.browser')],                   
            'login_at'     => ['title' => trans('general.log_in')],                   
            'logout_at'     => ['title' => trans('general.log_out')],                     
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'UserLogs_' . date('YmdHis');
    }
}
