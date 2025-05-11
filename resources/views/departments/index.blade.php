@extends('layouts.app')

@section('content')
    
    <div class="portlet light bordered">
        @if (session('message'))
            <div class="alert alert-danger">
                {{ session('message') }}
                
            </div>
        @endif
        <div class="portlet-title">            
            <a href="{{ route('universities.index') }}" class="btn btn-default"><i class="icon-arrow-right"></i> {{ trans('general.universities_list') }}
            </a>

            <a href="{{ route('faculties.index', $university) }}" class="btn btn-default"><i class="icon-arrow-right"></i> {{ trans('general.list').' '. trans('models/faculty.plural') }}
            </a>

            @can ('create-department')
            <a href="{{ route('departments.create', $university) }}" class="btn btn-primary"><i class="icon-plus"></i> {{ trans('general.create_department') }}</a>            
            @endcan
            <div class="tools"> </div>
        </div>
        <div class="portlet-body">
            {!! $dataTable->table([], true) !!}
        </div>
    </div>
@endsection

@push('styles')
<style>
    tr.warning
    {
        background-color: Orange ;

    }
    tr.row_deleted
    {
        
        color: red;
    }
    .datatable-footer-input {
        padding: 2px 5px;
    }
    .datatable-footer-select, .datatable-footer-input {
        width: 100px;
    }
    table.dataTable tfoot td, table.dataTable tfoot th {
        padding: 5px !important;
    }
</style>
    <!-- <link rel="stylesheet" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css"> -->
    <link href="{{ asset('css/datatables.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('js/datatables.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
    {!! $dataTable->scripts() !!}
    <script>
        // $('#dataTableBuilder').DataTable({
        //     scrollX: true,
        // });
        $.fn.dataTable.ext.errMode = 'none';
    </script>
@endpush