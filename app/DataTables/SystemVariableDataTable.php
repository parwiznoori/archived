<?php
namespace App\DataTables;

use App\Models\SystemVariable;
use Yajra\DataTables\Services\DataTable;

class SystemVariableDataTable extends DataTable
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
            ->addColumn('action', function ($SystemVariable) {
                $html = '';

                if (auth()->user()->can('edit-systemVariable')) {
                    $html .= '<a href="'.route('systemVariable.edit', [$SystemVariable->id]).'" class="btn btn-success btn-xs"><i class="icon-pencil"></i></a>';
                }

                if (auth()->user()->can('delete-systemVariable')) {
                    $html .= '<form action="'. route('systemVariable.destroy', [$SystemVariable->id]) .'" method="post" style="display:inline">
                            <input type="hidden" name="_method" value="DELETE" />
                            <input type="hidden" name="_token" value="'.csrf_token().'" />
                            <button type="submit" class="btn btn-xs btn-danger" onClick="doConfirm()"><i class="icon-trash"></i></button>
                        </form>';
                }
               
                
                return $html;
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(SystemVariable $model)
    {
        $query = $model->select(
            'system_variables.id',
            'system_variables.name',
            'system_variables.default_value',
            'system_variables.user_value',
            'system_variables.description'
            
        );
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
            'name'     => ['title' => trans('models/systemVariable.fields.name')],            
            'default_value' => ['title' => trans('models/systemVariable.fields.default_value')],
            'user_value' => ['title' => trans('models/systemVariable.fields.user_value')],
            'description'    => ['title' => trans('models/systemVariable.fields.description')],
            // 'action'    => ['title' => trans('general.status')],
        ];
    }

   
}
