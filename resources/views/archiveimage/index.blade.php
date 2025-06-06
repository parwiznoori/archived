@extends('layouts.app')

@section('content')
    <div class="portlet light bordered card">
        @if (session('message'))
            <div class="alert alert-success">
                {{ session('message') }}
                
            </div>
        @endif
        {{-- archive index --}}


        <div class="portlet-title">
{{--            @can ('create-archiveimage')--}}
{{--            <a href="{{ route('archiveimage.create') }}" class="btn btn-info"><i class="icon-plus"></i> {{ trans('general.create_archiveimage') }} </a>--}}
{{--            @endcan--}}
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
    tr.final_approved
    {
        color:rgb(11, 132, 11);
    }
</style>
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