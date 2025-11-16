<?php

namespace App\DataTables;

use App\Models\Archive;
use App\Models\UniversityUser;
use App\Models\ArchiveRole;
use Yajra\DataTables\Services\DataTable;

class ArchiveDataTable extends DataTable
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


            ->setRowClass(function ($archive) {
                $className = '';
                // Check if the archive is soft deleted
                if (isset($archive->deleted_at)) {

                }



                // Check if qc_status_id is set and equals 1
                if( $archive->qc_status_id ==3 && $archive->status_id ==4 ) {
                    $className .= ' qc_status';
                }

                if( $archive->qc_status_id ==4 && $archive->status_id ==4 ) {
                    $className .= ' qc_status2';
                }

                if(  $archive->status_id ==3 ) {
                    $className .= ' qc_status3';
                }

                return $className;
            })


            ->addColumn('action', function ($archive) {

                $html = '';
                $html = '<div class="btn-group"> 
                        <a class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false">';     
                        $html .= trans('general.action').'
                            <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu pull-right">';
                        if(auth()->user()->can('edit-archive') ){
                            $html .= '<li><a href="'. route('archive.edit', $archive) .'"  target="new"> <i class="fa fa-pencil"></i> '. trans("general.edit") .' </a></li>';
                            
                        }

                        if(auth()->user()->can('csv-archive') ){
                            $html .= '<li><a href="'. route('archive.view', $archive) .'"  target="new"> <i class="fa fa-pencil"></i> '. trans("general.upload_csv") .' </a></li>';

                        }

                        if(auth()->user()->can('view-archiveimage') ){
                                    $html .= '<li><a href="'. route('archive.show', $archive) .'"  target="new"> <i class="fa fa-pencil"></i> '. trans("general.photo") .' </a></li>';

                                }

                        if(auth()->user()->can('view-archivedata') ){
                            $html .= '<li><a href="' . route("archiveBookDataEntry", ['id' => $archive->id]) . '" target="new"> <i class="fa fa-pencil"></i> '. trans("general.archivedata") .' </a></li>';

                        }
//


                        if(auth()->user()->can('reset-qc-user') && $archive->qc_user_id!=null) {
                    $html .= '<li>
                        <form action="' . route('archive.reset-qc-user', $archive) . '" method="POST" style="display: inline;">
                            ' . csrf_field() . '
                            <button  onclick="return confirm(\'Are you sure you want to reset the QC user?\');" type="submit" class="btn btn-link" target="new">
                                <i class="fa fa-pencil"></i> ' . trans("general.reset-qc-user") . '
                            </button>
                        </form>
                      </li>';
                }


                if(auth()->user()->can('reset-de-user')  &&($archive->de_user_id!=null && $archive->qc_user_id==null) ) {
                    $html .= '<li> 
                        <form action="' . route('archive.reset-de-user', $archive) . '" method="POST" style="display: inline;">
                            ' . csrf_field() . '
                            <button type="submit" class="btn btn-link" target="new">
                                <i class="fa fa-pencil"></i> '. trans("general.reset-de-user") . '
                            </button>
                        </form>
                      </li>';
                }

//                            if(auth()->user()->can('reset-qc-status') ){
//                                $html .= '<li><a href="'. route('archive.reset-qc-status', $archive) .'"  target="new"> <i class="fa fa-pencil"></i> '. trans("general.reset-qc-status") .' </a></li>';
//
//                            }

//                if(auth()->user()->can('reset_de_assign_book') ){
//                    $html .= '<li><a href="'. route('archive.reset-de-status', $archive) .'"  target="new"> <i class="fa fa-pencil"></i> '. trans("general.reset_de_assign_book") .' </a></li>';
//
//                }



                if(auth()->user()->hasRole('super-admin') ){
                            if(isset($archive->deleted_at))
                            {
                                // $html .= '<li><a href="'. route('archive.recover', $archive) .'"  target="new" onClick="doConfirm()" > <i class="fa fa-pencil"></i> '. trans("general.restore").' </a></li>';

                            }
                           
                        }   
                        
                        if (auth()->user()->can('delete-archive')) {
                            $html .= '<li><form action="'. route('archive.destroy', $archive) .'" method="post" style="display:inline">
                                <input type="hidden" name="_method" value="DELETE" />
                                <input type="hidden" name="_token" value="'.csrf_token(). '" />
                                <button type="submit" style ="color:red" class="btn btn-link" onClick="doConfirm()"><i class="fa fa-trash" style="color:#ff0000"></i> ' .trans("general.delete").' </button>
                            </form></li>';
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
    public function query(Archive $archive)
    {




        $query = $archive->select(
            'archives.id',
            'archives.qc_user_id',
            'archives.de_user_id',
            'archives.status_id',
            'archives.qc_status_id',
            'universities.name as university',
            'archiveyears.year as archiveyears',
            'archives.book_pagenumber',
            'book_description',
            'book_name',
            'archivedatastatus.status as archivedatastatus',
            'archiveqcstatus.qc_status as archiveqcstatus'
        )
            ->leftJoin('universities', 'universities.id', '=', 'archives.university_id')
            ->leftJoin('archivedatastatus', 'archivedatastatus.id', '=', 'archives.status_id')
            ->leftJoin('archiveqcstatus', 'archiveqcstatus.id', '=', 'archives.qc_status_id')
            ->leftJoin('archiveyears', 'archiveyears.id', '=', 'archives.archive_year_id');

        // if( auth()->user()->type==2){
        //     $userList = ArchiveRole::where('user_id', auth()->user()->id)->pluck('archive_id')->toArray();
        //     $query->whereIn('archives.id', $userList);
        // }

        //user parts
          $universityList = UniversityUser::where('user_id', auth()->user()->id)
                    ->pluck ('university_id')
                    ->toArray();
                if($universityList!=null){
                     $query->whereIn('archives.university_id', $universityList);
                }

            if (auth()->user()->type == 2) {
                $userList = ArchiveRole::where('user_id', auth()->user()->id)
                    ->pluck('archive_id')
                    ->toArray();
                
                $query->whereIn('archives.id', $userList)
                    ->where('de_user_id', auth()->id()); // Only assigned to this user
            }

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
                        'dom'          => 'Brtip',
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
                                                                                                                    
                                if(this.index() >= 0 && this.index() <= 8) { 
                                    if (this.index() == 0 || this.index() == 8) {
                                        $('<input class=\"datatable-footer-input ltr \" placeholder=\"'+$(column.header()).text()+'\" name=\"'+ column.index() + '\" value=\"'+ (state ? state.columns[this.index()].search.search : emptyValue) +'\" />').attr('size',10).appendTo($(column.footer()).empty())                                        
                                        .on(onEvent, function () {
                                            column.search($(this).val(), false, false, true).draw();
                                        });
                                    } else {
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
            'id'    => ['name' => 'archives.id', 'id' => trans('general.book_name')],
            'book_name'    => ['name' => 'book_name', 'title' => trans('general.book_name')],
            'university' => ['name' => 'universities.name', 'title' => trans('general.university')],
            'archiveyears' => ['name' => 'archiveyears.year','title' => trans('general.book_year')],
            'archivedatastatus'    => ['name' => 'archivedatastatus.status', 'title' => trans('general.status')],
            'archiveqcstatus'    => ['name' => 'archiveqcstatus.qc_status', 'title' => trans('general.accept_or_refuse')],
            'book_description'    => ['name' => 'book_description', 'title' => trans('general.book_description')],
            
            
        ];
       
    }
    
    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Archive_' . date('YmdHis');
    }
}