<?php
namespace App\DataTables;

use App\Models\Day;
use Yajra\DataTables\Services\DataTable;

class DayDataTable extends DataTable
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
            ->addColumn('action', function ($Day) {
                $html = '';

                // if (auth()->user()->can('edit-day')) {
                //     $html .= '<a href="'.route('day.edit', [$Day->id]).'" class="btn btn-success btn-xs"><i class="icon-pencil"></i></a>';
                // }

                // if (auth()->user()->can('delete-day')) {
                //     $html .= '<form action="'. route('day.destroy', [$Day->id]) .'" method="post" style="display:inline">
                //             <input type="hidden" name="_method" value="DELETE" />
                //             <input type="hidden" name="_token" value="'.csrf_token().'" />
                //             <button type="submit" class="btn btn-xs btn-danger" onClick="doConfirm()"><i class="icon-trash"></i></button>
                //         </form>';
                // }
               
                
                return $html;
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Day $model)
    {
        $query = $model->select(
            'id',
            'day',
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
            'day'     => ['title' => trans('models/day.fields.day')],
        ];
    }

   
}
