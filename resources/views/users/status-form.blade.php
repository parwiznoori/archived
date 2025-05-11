@extends('layouts.app')

@section('content')
    <div class="portlet box">        
        <div class="portlet-body">
            <!-- BEGIN FORM-->            
            {!! Form::model($user, ['route' => ['users.updateStatus', $user], 'method' => 'post', 'class' => 'form-horizontal']) !!}            
                <div class="form-body">
                    <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                        {!! Form::label('name', trans('general.name'), ['class' => 'control-label col-sm-3']) !!}                                
                        <div class="col-sm-4">
                           {{ $user->name }}                                                                                             
                        </div>
                    </div>
                    <div class="form-group {{ $errors->has('position') ? ' has-error' : '' }}">
                        {!! Form::label('position', trans('general.position'), ['class' => 'control-label col-sm-3']) !!}                                
                        <div class="col-sm-4">  
                            {{ $user->position }}                                                                                                     
                        </div>
                    </div>
                    <div class="form-group {{ $errors->has('phone') ? ' has-error' : '' }}">
                        {!! Form::label('phone', trans('general.phone'), ['class' => 'control-label col-sm-3']) !!}                                
                        <div class="col-sm-4">
                            {{ $user->phone }}                                                                                                  
                        </div>
                    </div>
                    <hr>
                    <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                        {!! Form::label('email', trans('general.email'), ['class' => 'control-label col-sm-3']) !!}                                
                        <div class="col-sm-4">
                            {{ $user->email }}                                                                                                 
                        </div>
                    </div>
                    


                    <div class="form-group {{ $errors->has('active') ? ' has-error' : '' }}">
                        {!! Form::label('active', trans('general.status'), ['class' => 'control-label col-sm-3']) !!}                                
                        <div class="col-sm-4">                 
                            <div >
                                <label class="checkbox-inline">
                                <input type="checkbox" name="active" value="1" {{ $user->active ? "checked" : "" }}> {{ trans('general.active') }}                               
                                </label>                                                       
                            </div>                                               
                        </div>
                    </div>

                    
                    
                    <hr>
                </div>
                <div class="form-actions fluid">
                    <div class="row">
                        <div class="col-md-offset-3 col-md-9">
                            <button type="submit" class="btn green">{{ trans('general.save') }}</button>
                            <a href="{{ route('users.index') }}" class="btn default">{{ trans('general.cancel') }}</a>
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