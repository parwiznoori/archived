    {{--insert form page--}}





{!! Form::open(['route' => 'archivedata.store', 'method' => 'post','enctype' => 'multipart/form-data', 'class' => 'form-horizontal']) !!}
{!! Form::text('column_number', $column_number, ['style' => 'display:none']) !!}
{!! Form::text('archive_image_id',$archiveImage->id, ['style' => 'display:none']) !!}
{!! Form::text('archivedata_id', $archiveImage->archive_id, ['style' => 'display:none']) !!}

<br>



<!-- Department Selection -->
<div class="row">
    <div class="col-md-12">
        <div class="form-group {{ $errors->has('department_id') ? ' has-error' : '' }}">
            {!! Form::label('department_id', trans('general.department'), ['class' => 'control-label col-sm-3 required']) !!}
            <div class="col-sm-8">

                {!! Form::select('department_id', $departments ,null, [
                    'class' => 'form-control select2',
                    'placeholder' => trans('general.select'),
                     'remote-url' => route('api.departmentArchive'),
                      'remote-param' => '[name="university_id"]'
                ]) !!}
                @if ($errors->has('department_id'))
                    <span class="help-block">
                            <strong>{{ $errors->first('department_id') }}</strong>
                        </span>
                @endif
            </div>
        </div>
    </div>
</div>




<div class="row">
    <div class="col-md-12">
        <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
            {!! Form::label('name', trans('general.name'), ['class' => 'control-label col-sm-3 required']) !!}
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


<div class="row">
    <div class="col-md-12">
        <div class="form-group {{ $errors->has('last_name') ? ' has-error' : '' }}">
            {!! Form::label('last_name', trans('general.last_name'), ['class' => 'control-label col-sm-3']) !!}
            <div class="col-sm-8">
                {!! Form::text('last_name', null, ['class' => 'form-control']) !!}
                @if ($errors->has('last_name'))
                    <span class="help-block">
                        <strong>{{ $errors->first('last_name') }}</strong></span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group {{ $errors->has('father_name') ? ' has-error' : '' }}">
            {!! Form::label('father_name', trans('general.father_name'), ['class' => 'control-label col-sm-3 required']) !!}
            <div class="col-sm-8">
                {!! Form::text('father_name', null, ['class' => 'form-control']) !!}
                @if ($errors->has('father_name'))
                    <span class="help-block">
                        <strong>{{ $errors->first('father_name') }}</strong></span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group {{ $errors->has('grandfather_name') ? ' has-error' : '' }}">
            {!! Form::label('grandfather_name', trans('general.grandfather_name'), ['class' => 'control-label col-sm-3 required']) !!}
            <div class="col-sm-8">
                {!! Form::text('grandfather_name', null, ['class' => 'form-control']) !!}
                @if ($errors->has('grandfather_name'))
                    <span class="help-block">
                        <strong>{{ $errors->first('grandfather_name') }}</strong></span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group {{ $errors->has('school') ? ' has-error' : '' }}">
            {!! Form::label('school', trans('general.school_name'), ['class' => 'control-label col-sm-3']) !!}
            <div class="col-sm-8">
                {!! Form::text('school', null, ['class' => 'form-control']) !!}
                @if ($errors->has('school'))
                    <span class="help-block">
                        <strong>{{ $errors->first('school') }}</strong></span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group {{ $errors->has('school_graduation_year') ? ' has-error' : '' }}">
            {!! Form::label('school_graduation_year', trans('general.graduated_year_school'), ['class' => 'control-label col-sm-3']) !!}
            <div class="col-sm-8">
                {!! Form::text('school_graduation_year', null, ['class' => 'form-control']) !!}
                @if ($errors->has('school_graduation_year'))
                    <span class="help-block">
                        <strong>{{ $errors->first('school_graduation_year') }}</strong></span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group {{ $errors->has('tazkira_number') ? ' has-error' : '' }}">
            {!! Form::label('tazkira_number', trans('general.tazkira_number'), ['class' => 'control-label col-sm-3']) !!}
            <div class="col-sm-8">
                {!! Form::text('tazkira_number', null, ['class' => 'form-control']) !!}
                @if ($errors->has('tazkira_number'))
                    <span class="help-block">
                        <strong>{{ $errors->first('tazkira_number') }}</strong></span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group {{ $errors->has('birth_date') ? ' has-error' : '' }}">
            {!! Form::label('birth_date', trans('general.birth_date'), ['class' => 'control-label col-sm-3']) !!}
            <div class="col-sm-8">
                {!! Form::text('birth_date', null, ['class' => 'form-control']) !!}
                @if ($errors->has('birth_date'))
                    <span class="help-block">
                        <strong>{{ $errors->first('birth_date') }}</strong></span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group {{ $errors->has('birth_place') ? ' has-error' : '' }}">
            {!! Form::label('birth_place', trans('general.birth_place'), ['class' => 'control-label col-sm-3']) !!}
            <div class="col-sm-8">
                {!! Form::text('birth_place', null, ['class' => 'form-control']) !!}
                @if ($errors->has('birth_place'))
                    <span class="help-block">
                        <strong>{{ $errors->first('birth_place') }}</strong></span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group {{ $errors->has('time') ? ' has-error' : '' }}">
            {!! Form::label('time', trans('general.time'), ['class' => 'control-label col-sm-3']) !!}
            <div class="col-sm-8">
                {!! Form::text('time', null, ['class' => 'form-control']) !!}
                @if ($errors->has('time'))
                    <span class="help-block">
                        <strong>{{ $errors->first('time') }}</strong></span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group {{ $errors->has('kankor_id') ? ' has-error' : '' }}">
            {!! Form::label('kankor_id', trans('general.kankor_id'), ['class' => 'control-label col-sm-3']) !!}
            <div class="col-sm-8">
                {!! Form::text('kankor_id', null, ['class' => 'form-control']) !!}
                @if ($errors->has('kankor_id'))
                    <span class="help-block">
                        <strong>{{ $errors->first('kankor_id') }}</strong></span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group {{ $errors->has('semester_type_id') ? ' has-error' : '' }}">
            {!! Form::label('semester_type_id', trans('general.half_year'), ['class' => 'control-label
            col-sm-3']) !!}
            <div class="col-sm-8">
                {!! Form::select('semester_type_id', $semester_types, null, ['class' => 'form-control select2',
                'placeholder' => trans('general.select')]) !!}
                @if ($errors->has('semester_type_id'))
                    <span class="help-block">
                        <strong>{{ $errors->first('semester_type_id') }}</strong></span>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="form-group {{ $errors->has('year_of_inclusion') ? ' has-error' : '' }}">
            {!! Form::label('year_of_inclusion', trans('general.year_of_inclusion'), ['class' => 'control-label col-sm-3 required']) !!}
            <div class="col-sm-8">
                {!! Form::text('year_of_inclusion', null, ['class' => 'form-control']) !!}
                @if ($errors->has('year_of_inclusion'))
                    <span class="help-block">
                        <strong>{{ $errors->first('year_of_inclusion') }}</strong></span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group {{ $errors->has('graduated_year') ? ' has-error' : '' }}">
            {!! Form::label('graduated_year', trans('general.graduated_year_university'), ['class' => 'control-label col-sm-3 required']) !!}
            <div class="col-sm-8">
                {!! Form::text('graduated_year', null, ['class' => 'form-control']) !!}
                @if ($errors->has('graduated_year'))
                    <span class="help-block">
                        <strong>{{ $errors->first('graduated_year') }}</strong></span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group {{ $errors->has('transfer_year') ? ' has-error' : '' }}">
            {!! Form::label('transfer_year', trans('general.transfer_year'), ['class' => 'control-label col-sm-3']) !!}
            <div class="col-sm-8">
                {!! Form::text('transfer_year', null, ['class' => 'form-control']) !!}
                @if ($errors->has('transfer_year'))
                    <span class="help-block">
                        <strong>{{ $errors->first('transfer_year') }}</strong></span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group {{ $errors->has('leave_year') ? ' has-error' : '' }}">
            {!! Form::label('leave_year', trans('general.leave_year'), ['class' => 'control-label col-sm-3']) !!}
            <div class="col-sm-8">
                {!! Form::text('leave_year', null, ['class' => 'form-control']) !!}
                @if ($errors->has('leave_year'))
                    <span class="help-block">
                        <strong>{{ $errors->first('leave_year') }}</strong></span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group {{ $errors->has('failled_year') ? ' has-error' : '' }}">
            {!! Form::label('failled_year', trans('general.failled_year'), ['class' => 'control-label col-sm-3']) !!}
            <div class="col-sm-8">
                {!! Form::text('failled_year', null, ['class' => 'form-control']) !!}
                @if ($errors->has('failled_year'))
                    <span class="help-block">
                        <strong>{{ $errors->first('failled_year') }}</strong></span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group {{ $errors->has('monograph_date') ? ' has-error' : '' }}">
            {!! Form::label('monograph_date', trans('general.monograph_date'), ['class' => 'control-label col-sm-3']) !!}
            <div class="col-sm-8">
                {!! Form::text('monograph_date', null, ['class' => 'form-control']) !!}
                @if ($errors->has('monograph_date'))
                    <span class="help-block">
                        <strong>{{ $errors->first('monograph_date') }}</strong></span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group {{ $errors->has('monograph_number') ? ' has-error' : '' }}">
            {!! Form::label('monograph_number', trans('general.monograph_number'), ['class' => 'control-label col-sm-3']) !!}
            <div class="col-sm-8">
                {!! Form::text('monograph_number', null, ['class' => 'form-control']) !!}
                @if ($errors->has('monograph_number'))
                    <span class="help-block">
                        <strong>{{ $errors->first('monograph_number') }}</strong></span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group {{ $errors->has('monograph_title') ? ' has-error' : '' }}">
            {!! Form::label('monograph_title', trans('general.monograph_title'), ['class' => 'control-label col-sm-3']) !!}
            <div class="col-sm-8">
                {!! Form::text('monograph_title', null, ['class' => 'form-control']) !!}
                @if ($errors->has('monograph_title'))
                    <span class="help-block">
                        <strong>{{ $errors->first('monograph_title') }}</strong></span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group {{ $errors->has('averageOfScores') ? ' has-error' : '' }}">
            {!! Form::label('averageOfScores', trans('general.averageOfScores'), ['class' => 'control-label col-sm-3']) !!}
            <div class="col-sm-8">
                {!! Form::text('averageOfScores', null, ['class' => 'form-control']) !!}
                @if ($errors->has('averageOfScores'))
                    <span class="help-block">
                        <strong>{{ $errors->first('averageOfScores') }}</strong></span>
                @endif
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-12">
        <div class="form-group {{ $errors->has('grade_id') ? ' has-error' : '' }}">
            {!! Form::label('grade_id', trans('general.grade'), ['class' => 'control-label
            col-sm-3']) !!}
            <div class="col-sm-8">
                {!! Form::select('grade_id', $grades, null, ['class' => 'form-control select2',
                'placeholder' => trans('general.select')]) !!}
                @if ($errors->has('grade_id'))
                    <span class="help-block">
                        <strong>{{ $errors->first('grade_id') }}</strong></span>
                @endif
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-12">
        <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
            {!! Form::label('description', trans('general.description'), ['class' => 'control-label col-sm-3']) !!}
            <div class="col-sm-8">
                {!! Form::textarea('description', null, ['class' => 'form-control']) !!}
                @if ($errors->has('description'))
                    <span class="help-block">
                        <strong>{{ $errors->first('description') }}</strong></span>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="form-actions fluid">
    <div class="row">
        <div class="col-md-offset-2 col-md-8">
            <button type="submit" class="btn green">{{ trans('general.save') }}</button>
            <a href="{{ route('archivedata.create') }}" class="btn default">{{ trans('general.cancel') }}</a>
        </div>
    </div>
</div>


{!! Form::close() !!}
