<?php
namespace App\DataTables;

use App\Models\Grade;
use Yajra\DataTables\Services\DataTable;

//Grade data table

class GradeDataTable extends DataTable
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
            ->addColumn('action', function ($grades) {
                $html = '';
                // not neccessary 

                // if (auth()->user()->can('edit-grades')) {
                //     $html .= '<a href="'.route('grades.edit', [$grades->id]).'" class="btn btn-success btn-xs"><i class="icon-pencil"></i></a>';
                // }

                // if (auth()->user()->can('delete-grades')) {
                //     $html .= '<form action="'. route('grades.destroy', [$grades->id]) .'" method="post" style="display:inline">
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
    public function query(Grade $model)
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
            'name'     => ['title' => trans('models/grades.fields.name')],
        ];
    }

   
}
