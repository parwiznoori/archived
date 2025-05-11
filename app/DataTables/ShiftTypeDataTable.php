<?php
namespace App\DataTables;

use App\Models\ShiftType;
use Yajra\DataTables\Services\DataTable;

class ShiftTypeDataTable extends DataTable
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
            ->addColumn('action', function ($ShiftType) {
                $html = '';

                if (auth()->user()->can('edit-shiftType')) {
                    $html .= '<a href="'.route('shiftType.edit', [$ShiftType->id]).'" class="btn btn-success btn-xs"><i class="icon-pencil"></i></a>';
                }

                if (auth()->user()->can('delete-shiftType')) {
                    $html .= '<form action="'. route('shiftType.destroy', [$ShiftType->id]) .'" method="post" style="display:inline">
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
    public function query(ShiftType $model)
    {
        $query = $model->select(
            'id',
            'name',
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
            'name'     => ['title' => trans('models/shiftType.fields.name')],
        ];
    }

   
}
