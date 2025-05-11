@extends('layouts.app')

@section('content')
    <div class="portlet light bordered">
        @if (session('message'))
            <div class="alert alert-success">
                {{ session('message') }}
                
            </div>
        @endif
        <div class="portlet-title">
            @if (auth()->user()->hasRole('system-developer') || auth()->user()->hasRole('super-admin'))
            <a href="{{ route('roles.create') }}" class="btn btn-primary"><i class="icon-plus"></i> {{ trans('general.create_role') }} </a>
            @endif
            <div class="tools"> </div>
        </div>
        <div class="portlet-body">
            {!! $dataTable->table([], true) !!}
        </div>
    </div>
@endsection

@push('styles')
<style>
    tr.row_deleted
    {
        color: red;
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
        $.fn.dataTable.ext.errMode = 'none';
    </script>
@endpush