<?php
namespace App\DataTables;

use App\Models\DepartmentType;
use Yajra\DataTables\Services\DataTable;

class DepartmentTypeDataTable extends DataTable
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
            ->addColumn('action', function ($DepartmentType) {
                $html = '';

                if (auth()->user()->can('edit-departmentType')) {
                    $html .= '<a href="'.route('departmentType.edit', [$DepartmentType->id]).'" class="btn btn-success btn-xs"><i class="icon-pencil"></i></a>';
                }

                if (auth()->user()->can('delete-departmentType')) {
                    $html .= '<form action="'. route('departmentType.destroy', [$DepartmentType->id]) .'" method="post" style="display:inline">
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
    public function query(DepartmentType $model)
    {
        $query = $model->select(
            'department_types.id',
            'department_types.name',
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
            'name'     => ['title' => trans('models/departmentType.fields.name')],
        ];
    }

   
}
