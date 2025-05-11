@extends('layouts.app')

@section('content')

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="portlet box">
        <div class="portlet-body">
            <div class="panel-body">

                {!! Form::model($archivedata, ['route' => ['archive_monograph_update', $archivedata->id], 'method' => 'POST', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal']) !!}
                <div class="form-body">
                    <div class="row">
                        <div class="col-md-5">
                            @foreach (['monograph_date', 'monograph_number', 'monograph_title', 'monograph_doc_date', 'monograph_doc_number'] as $field)
                                <div class="form-group {{ $errors->has($field) ? ' has-error' : '' }}">
                                    {!! Form::label($field, trans("general.$field"), ['class' => 'control-label col-sm-3']) !!}
                                    <div class="col-sm-8">
                                        {!! Form::text($field, null, ['class' => 'form-control']) !!}
                                        @if ($errors->has($field))
                                            <span class="help-block">
                                                <strong>{{ $errors->first($field) }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="form-actions fluid">
                    <div class="row">
                        <div class="col-md-offset-2 col-md-8">
                            <button type="submit" class="btn green">{{ trans('general.save') }}</button>
                            <a href="{{ route('archivedata.index') }}" class="btn default">{{ trans('general.cancel') }}</a>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>

            <!-- Display Table -->
            <h3>{{ trans('general.archivedata_monograph') }}</h3>
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>{{ trans('general.monograph_date') }}</th>
                    <th>{{ trans('general.monograph_number') }}</th>
                    <th>{{ trans('general.monograph_title') }}</th>
                    <th>{{ trans('general.monograph_doc_date') }}</th>
                    <th>{{ trans('general.monograph_doc_number') }}</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{{ $archivedata->monograph_date }}</td>
                    <td>{{ $archivedata->monograph_number }}</td>
                    <td>{{ $archivedata->monograph_title }}</td>
                    <td>{{ $archivedata->monograph_doc_date }}</td>
                    <td>{{ $archivedata->monograph_doc_number }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ url('assets/plugins/persiandatepicker/css/persianDatepicker-default.css') }}" />
@endpush

@push('scripts')
    <script src="{{ url('assets/plugins/persiandatepicker/js/persianDatepicker.min.js') }}"></script>
    <script>
        $(function() {
            $("#").persianDatepicker({
                months: ["حمل", "ثور", "جوزا", "سرطان", "اسد", "سنبله", "میزان", "عقرب", "قوس", "جدی", "دلو", "حوت"],
                dowTitle: ["شنبه", "یکشنبه", "دوشنبه", "سه شنبه", "چهارشنبه", "پنج شنبه", "جمعه"],
                showGregorianDate: false,
                persianNumbers: true,
                formatDate: "YYYY/MM/DD",
                isRTL: true,
            });
        });
    </script>
@endpush
