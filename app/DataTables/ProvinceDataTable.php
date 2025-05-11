<?php
namespace App\DataTables;

use App\Models\Province;
use Yajra\DataTables\Services\DataTable;

class ProvinceDataTable extends DataTable
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
            ->addColumn('action', function ($Province) {
                $html = '';

                if (auth()->user()->can('edit-province')) {
                    $html .= '<a href="'.route('province.edit', [$Province->id]).'" class="btn btn-success btn-xs"><i class="icon-pencil"></i></a>';
                }

                // if (auth()->user()->can('delete-province')) {
                //     $html .= '<form action="'. route('province.destroy', [$Province->id]) .'" method="post" style="display:inline">
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
    public function query(Province $model)
    {
        $query = $model->select(
            'id',
            'name',
            'name_en'
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
            'id'     => ['title' => trans('models/province.fields.id')],          
            'name'     => ['title' => trans('models/province.fields.name')],
            'name_en'     => ['title' => trans('models/province.fields.name_en')],
        ];
    }

   
}
