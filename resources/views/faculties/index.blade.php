@extends('layouts.app')

@section('content')
    
    <div class="portlet light bordered">
        @if (session('message'))
            <div class="alert alert-danger">
                {{ session('message') }}
                
            </div>
        @endif
        <div class="portlet-title">            
            <a href="{{ route('universities.index') }}" class="btn btn-default"><i class="icon-arrow-right"></i> {{ trans('general.universities_list') }}</a>
            @can ('create-department')
            <a href="{{ route('faculties.create', $university) }}" class="btn btn-primary"><i class="icon-plus"></i> {{ trans('models/faculty.singular').' '. trans('crud.new') }}</a>            
            @endcan
            <div class="tools"> </div>
        </div>
        <div class="portlet-body">
            {!! $dataTable->table([], true) !!}
        </div>
    </div>
@endsection

@push('styles')
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