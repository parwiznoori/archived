@extends('layouts.app')

@section('content')
    <h3>{{ trans('لیست وظایف یوزر آرشیف') }}</h3>

    <div class="portlet light bordered">
        <div class="portlet-title">
            <a href="{{ route('archiverole.create') }}" class="btn btn-primary">{{ trans('وظایف جدید') }}</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="portlet-body">
            @php($search = request()->query('search'))
            {!! $dataTable->table(['data-column-filters' => true, 'data-search' => is_string($search) ? json_encode(['search' => $search]) : null], true) !!}
        </div>
    </div>
@endsection

@push('styles')
    <link href="{{ asset('css/datatables.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('js/datatables.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
    {!! $dataTable->scripts() !!}
@endpush
