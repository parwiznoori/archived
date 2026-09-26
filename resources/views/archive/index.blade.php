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
            @can ('create-archive')
            <a href="{{ route('archive.create') }}" class="btn btn-info"><i class="icon-plus"></i> {{ trans('general.create_archive') }} </a>
            @endcan
            <span class="legend-inline">
                <span class="legend-item"><span class="legend-color" style="background-color:#ffffff;"></span> حالت معمولی</span>
                <span class="legend-item"><span class="legend-color" style="background-color:#d6eaf8;"></span> درج‌کننده مشخص</span>
                <span class="legend-item"><span class="legend-color" style="background-color:#d5f5e3;"></span> کنترول‌کننده مشخص</span>
                <span class="legend-item"><span class="legend-color" style="background-color:lightgreen;"></span> موافقه</span>
                <span class="legend-item"><span class="legend-color" style="background-color:indianred;"></span> عدم موافقه</span>
                <span class="legend-item"><span class="legend-color" style="background-color:yellow;"></span> ناتکمیل</span>
                <span class="legend-item"><span class="legend-color" style="background-color:rgb(231, 228, 228);"></span> کتاب تکمیل بدون درج‌کننده</span>
            </span>
            <div class="tools"> </div>
        </div>
        <div class="portlet-body">
            {!! $dataTable->table([], true) !!}
        </div>
    </div>
@endsection

@push('styles')
    <style>

        .qc_status {
           background-color: lightgreen !important;
        }
        .qc_status2{
            background-color: indianred !important;
        }
        .qc_status3{
            background-color: yellow !important;
        }
        .qc_status4{
            background-color: rgb(231, 228, 228) !important;
        }

        .row_de_assigned {
            background-color: #d6eaf8 !important;
        }

        .row_qc_assigned {
            background-color: #d5f5e3 !important;
        }

        tr.row_deleted {
            color: red;
        }

        tr.final_approved {
            color: rgb(11, 132, 11);
        }

        .legend-inline {
            display: inline-flex;
            align-items: center;
            gap: 15px;
            margin-left: 15px;
            vertical-align: middle;
            flex-wrap: wrap;
        }

        .legend-item {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            color: #333;
        }

        .legend-color {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 1px solid #ccc;
            border-radius: 2px;
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