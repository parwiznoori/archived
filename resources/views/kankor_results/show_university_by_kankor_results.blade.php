@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
       
        <div class="portlet"> 
                   
            <div class="portlet-body">
                @if (session('message'))
                    <script src="{{ asset('sweetalert2/sweetalert2.all.min.js') }}"></script>
                    <script>
                        Swal.fire(' {{ session("message") }} ')
                    </script>
                @endif
                <!-- BEGIN FORM-->            
                {!! Form::open(['route' => 'kankor_results.store_university_by_kankor_results', 'method' => 'post', 'class' => 'form-horizontal']) !!}
                <div class="form-body" id="app">
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group {{ $errors->has('kankor_year') ? ' has-error' : '' }}">
                                {!! Form::label('kankor_year', trans('general.year'), ['class' => 'control-label col-md-3']) !!}
                                <div class="col-md-8">
                                    {!! Form::select('kankor_year', $kankor_years, $kankorYear, ['class' => 'form-control select2 ', 'placeholder' => trans('general.select')]) !!}
                                    @if ($errors->has('kankor_year'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('kankor_year') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">   
                        <div class="col-md-12">
                            <div class="form-group {{ $errors->has('kankorResults') ? ' has-error' : '' }}">
                                {!! Form::label('kankorResults', trans('general.kankorResults'), ['class' => 'control-label col-md-3']) !!}
                                <div class="col-md-8">
                                    {!! Form::select('kankorResults[]', $kankorResults, null, ['class' => 'form-control select2 select-size','multiple' => 'multiple' ,'size' => '30' , 'id' => 'kankorResults' ]) !!}
                                    <br>
                                    {{-- <input type="checkbox" id="checkbox" >Select All --}}
                                    @if ($errors->has('kankorResults'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('kankorResults') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group {{ $errors->has('university_id') ? ' has-error' : '' }}">
                                {!! Form::label('university_id', trans('general.university'), ['class' => 'control-label col-md-3']) !!}
                                <div class="col-md-8">
                                    {!! Form::select('university_id', $universities, old('university_id'), ['class' => 'form-control select2']) !!}
                                    @if ($errors->has('university_id'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('university_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                   
                    <hr>
                    <div class="form-actions fluid">
                        <div class="row">
                            <div class="col-md-offset-1 col-md-12">
                               
                                
                                <button type="submit"  class="btn green" >{{ trans('general.update_university_id') }}</button>
                                <a href="{{ route('noticeboard') }}" class="btn default">{{ trans('general.cancel') }}</a>
                            </div>
                        </div>
                       
                    </div>
                    <br>
                {!! Form::close() !!}
                <!-- END FORM-->
            </div>
        </div>
    </div>
</div>


@endsection('content')

@push('style')
    <style>
        .select-size{
            height: 100%;
        }

    </style>
    
@endpush

{{-- @push('scripts')
    <script>
        $("#checkbox").click(function(){
            if($("#checkbox").is(':checked') ){
                $("#kankorResults > option").prop("selected","selected");// Select All Options
                $("#kankorResults").trigger("change");// Trigger change to select 2
            }else{
                $("#kankorResults > option").removeAttr("selected");
                $("#kankorResults").trigger("change");// Trigger change to select 2
            }
        });
    </script>
@endpush --}}