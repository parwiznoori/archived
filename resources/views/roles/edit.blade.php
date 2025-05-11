@extends('layouts.app')

@section('content')
<div class="portlet box">
    <div class="portlet-body">
        <!-- BEGIN FORM-->
        {!! Form::model($role, ['route' => ['roles.update', $role], 'method' => 'put', 'class' => 'form-horizontal'])
        !!}
        <div class="form-body">
            <div class="form-group {{ $errors->has('roleType') ? ' has-error' : '' }}">
                {!! Form::label('roleType', trans('general.roleType'), ['class' => 'control-label col-sm-3']) !!}
                <div class="col-sm-4">
                    {!! Form::select('roleType',$roleTypes, $role->type_id, ['class' => 'form-control select2']) !!}
                    @if ($errors->has('roleType'))
                    <span class="help-block">
                        <strong>{{ $errors->first('roleType') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
            <div class="form-group {{ $errors->has('title') ? ' has-error' : '' }}">
                {!! Form::label('title', trans('general.title'), ['class' => 'control-label col-sm-3']) !!}
                <div class="col-sm-4">
                    {!! Form::text('title', null, ['class' => 'form-control']) !!}
                    @if ($errors->has('title'))
                    <span class="help-block">
                        <strong>{{ $errors->first('title') }}</strong>
                    </span>
                    @endif
                </div>
            </div>
            <div class="form-body">
                <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                    {!! Form::label('name', 'Slug', ['class' => 'control-label col-sm-3']) !!}
                    <div class="col-sm-4">
                        {!! Form::text('name', null, ['class' => 'form-control ltr']) !!}
                        @if ($errors->has('name'))
                        <span class="help-block">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
            </div>

           
                <div class="form-body">
                    <div class="form-group {{ $errors->has('priority') ? ' has-error' : '' }}">
                        {!! Form::label('priority', trans('general.priority'), ['class' => 'control-label col-sm-3']) !!}
                        <div class="col-sm-4">
                            @if(auth()->user()->hasRole('system-developer'))
                                {!! Form::selectRange('priority', 1 , 1000 ,null, ['class' => 'form-control select2']) !!}
                                <p>
                                    {{ trans('general.note'). " : ". trans('general.prority_must_be_between_1_99_and_role_with_higher_priority_must_have_graeter_priority') }}
                                </p>
                            @else
                                {!! Form::selectRange('priority', 1 , 99 ,null, ['class' => 'form-control select2']) !!}
                                <p>
                                    {{ trans('general.note'). " : ". trans('general.prority_must_be_between_1_99_and_role_with_higher_priority_must_have_graeter_priority') }}
                                </p>
                            @endif
                            @if ($errors->has('priority'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('priority') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
           
           
        </div>

        <div class="form-group {{ $errors->has('admin') ? ' has-error' : '' }}">
            {!! Form::label('admin', trans('general.admin'), ['class' => 'control-label col-sm-3']) !!}
            <div class="col-sm-4">
                <div>
                    <label class="checkbox-inline">
                        <input type="checkbox" name="admin" value="1" {{ $role->admin ? "checked" : "" }}>
                        {{ trans('general.active') }}
                    </label>
                </div>
            </div>
        </div>
        <hr>
        <div class="form-group">
            {!! Form::label('abilities[]', trans('general.permissions'), ['class' => 'control-label col-sm-4']) !!}
            <div class="col-md-8">
                @foreach($abilities as $ability)
                <div class="checkbox-list">
                    <label>
                        {!! Form::checkbox('abilities[]', $ability->id, $role->permissions->contains($ability->id)) !!}
                        {{ $ability->title }}
                    </label>
                </div>
                @endforeach
            </div>
        </div>
        <div class="form-actions fluid">
            <div class="row">
                <div class="col-md-offset-3 col-md-9">
                    <button type="submit" class="btn green">{{ trans('general.save') }}</button>
                    <a href="{{ route('roles.index') }}" class="btn default">{{ trans('general.cancel') }}</a>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
        <!-- END FORM-->
    </div>
</div>
@endsection('content')