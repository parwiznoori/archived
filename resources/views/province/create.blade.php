@extends('layouts.app')

@section('content')
    <div class="portlet box">
        <div class="portlet-body">
            <!-- BEGIN FORM-->
            {!! Form::open(['route' => 'province.store', 'method' => 'post', 'class' => 'form-horizontal']) !!}
            <div class="form-body" id="app">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                            {!! Form::label('name', trans('models/province.fields.name'), ['class' => 'control-label col-sm-3']) !!}
                            <div class="col-sm-8">
                                {!! Form::text('name', null, ['class' => 'form-control']) !!}
                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    

                </div>
              
                <hr>
                <div class="form-actions fluid">
                    <div class="row">
                        <div class="col-md-offset-2 col-md-8">
                            <button type="submit" class="btn green">{{ trans('crud.save') }}</button>
                            <a href="{{ route('province.index') }}" class="btn default">{{ trans('general.cancel') }}</a>
                        </div>
                    </div>
                </div>
            {!! Form::close() !!}
            <!-- END FORM-->
            </div>
        </div>
        @endsection('content')

        @push('scripts')
            <script>
                $(function () {
                    $('.select2').change(function () {
                        $('.select2-ajax').val(null).trigger('change');
                    })
                })
            </script>
    @endpush