@extends('layouts.app')

@section('content')
    <div class="portlet light bordered card">
        @if (session('message'))
            <div class="alert alert-success">
                {{ session('message') }}

            </div>
        @endif
        {{-- archive index --}}

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
{{--    <div class="portlet box">--}}
{{--        <div class="portlet-body">--}}
{{--            <!-- BEGIN FORM-->--}}
{{--            <div class="panel-body">--}}



{{--                <div class="table-responsive" >--}}
{{--                    <table  class="table table-info table-striped table-hover ">--}}
{{--                        <thead>--}}
{{--                        <tr class="info">--}}
{{--                            <th>{{ trans('general.name') }}</th>--}}
{{--                            <th>{{ trans('general.university') }}</th>--}}
{{--                            <th>{{ trans('general.book_year') }}</th>--}}
{{--                            <th>{{ trans('general.date') }}</th>--}}
{{--                            <th>{{ trans('general.check') }}</th>--}}

{{--                        </tr>--}}
{{--                        </thead>--}}
{{--                        <tbody>--}}
{{--                        @foreach($archvieqc as $archvieqc)--}}
{{--                            <tr>--}}
{{--                                <td>{{ $archvieqc->book_name }}</td>--}}
{{--                                <td>{{ $archvieqc->university_id }}</td>--}}
{{--                                <td>{{ $archvieqc->book_year }}</td>--}}
{{--                                <td>{{ $archvieqc->created_at }}</td>--}}
{{--                                <td><a href="{{ route('archiveqcheck', ['id' => $archvieqc->id]) }}">{{ trans('general.check') }}</a></td>--}}

{{--                            </tr>--}}
{{--                        @endforeach--}}
{{--                        </tbody>--}}
{{--                    </table>--}}
{{--                </div>--}}


{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}




