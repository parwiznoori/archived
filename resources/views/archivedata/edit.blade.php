@extends('layouts.app')

@section('content')

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="portlet box">
        <div class="portlet-body">
            <!-- BEGIN FORM-->
            <div class="panel-body">        

       
             
               {!! Form::model($archivedata, ['route' => ['archivedata.update', $archivedata->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data','class' => 'form-horizontal']) !!}
                <div class="form-body" id="app">
                   
                    <div class="row">
                        
                        <div class="col-md-5"> 

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group {{ $errors->has('archive_id') ? ' has-error' : '' }}">
                                        {!! Form::label('archive_id', trans('general.book_name'), ['class' => 'control-label
                                        col-sm-3']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::select('archive_id', $archives, null, ['class' => 'form-control select2',
                                            'placeholder' => trans('general.select'),'onchange'=>"loadPage(this.value);"   ]) !!}
                                            @if ($errors->has('archive_id'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('archive_id') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                           
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group {{ $errors->has('archiveimage_id') ? ' has-error' : '' }}">
                                        {!! Form::label('archiveimage_id', trans('general.photo'), ['class' => 'control-label
                                        col-sm-3']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::text('archive_image_id', "", ['class' => 'hidden ','id'=>'archive_image_id',]) !!}
                                            {!! Form::select('archiveimage_id', [], null, ['id'=>'archiveimage_id','class' => 'form-control select2',
                                            'placeholder' => trans('general.select'),'onchange'=>"setImage(this.value);" ]) !!}
                                            @if ($errors->has('archiveimage_id'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('archiveimage_id') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div> 
                            </div>
                    
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                                        {!! Form::label('name', trans('general.name'), ['class' => 'control-label col-sm-3']) !!}
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
                                                    <strong>{{ $errors->first('last_name') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group {{ $errors->has('father_name') ? ' has-error' : '' }}">
                                        {!! Form::label('father_name', trans('general.father_name'), ['class' => 'control-label col-sm-3']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::text('father_name', null, ['class' => 'form-control']) !!}
                                            @if ($errors->has('father_name'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('father_name') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group {{ $errors->has('grandfather_name') ? ' has-error' : '' }}">
                                        {!! Form::label('grandfather_name', trans('general.grandfather_name'), ['class' => 'control-label col-sm-3']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::text('grandfather_name', null, ['class' => 'form-control']) !!}
                                            @if ($errors->has('grandfather_name'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('grandfather_name') }}</strong>
                                                </span>
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
                                                    <strong>{{ $errors->first('school') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group {{ $errors->has('school_graduation_year') ? ' has-error' : '' }}">
                                        {!! Form::label('school_graduation_year', trans('general.school_graduation_year'), ['class' => 'control-label col-sm-3']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::text('school_graduation_year', null, ['class' => 'form-control']) !!}
                                            @if ($errors->has('school_graduation_year'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('school_graduation_year') }}</strong>
                                                </span>
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
                                                    <strong>{{ $errors->first('tazkira_number') }}</strong>
                                                </span>
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
                                                    <strong>{{ $errors->first('birth_date') }}</strong>
                                                </span>
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
                                                    <strong>{{ $errors->first('birth_place') }}</strong>
                                                </span>
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
                                                    <strong>{{ $errors->first('time') }}</strong>
                                                </span>
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
                                                    <strong>{{ $errors->first('kankor_id') }}</strong>
                                                </span>
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
                                                <strong>{{ $errors->first('semester_type_id') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group {{ $errors->has('year_of_inclusion') ? ' has-error' : '' }}">
                                        {!! Form::label('year_of_inclusion', trans('general.year_of_inclusion'), ['class' => 'control-label col-sm-3']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::text('year_of_inclusion', null, ['class' => 'form-control']) !!}
                                            @if ($errors->has('year_of_inclusion'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('year_of_inclusion') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group {{ $errors->has('graduated_year') ? ' has-error' : '' }}">
                                        {!! Form::label('graduated_year', trans('general.graduated_year'), ['class' => 'control-label col-sm-3']) !!}
                                        <div class="col-sm-8">
                                            {!! Form::text('graduated_year', null, ['class' => 'form-control']) !!}
                                            @if ($errors->has('graduated_year'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('graduated_year') }}</strong>
                                                </span>
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
                                                    <strong>{{ $errors->first('transfer_year') }}</strong>
                                                </span>
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
                                                    <strong>{{ $errors->first('leave_year') }}</strong>
                                                </span>
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
                                                    <strong>{{ $errors->first('failled_year') }}</strong>
                                                </span>
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
                                                    <strong>{{ $errors->first('monograph_date') }}</strong>
                                                </span>
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
                                                    <strong>{{ $errors->first('monograph_number') }}</strong>
                                                </span>
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
                                                    <strong>{{ $errors->first('monograph_title') }}</strong>
                                                </span>
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
                                                    <strong>{{ $errors->first('averageOfScores') }}</strong>
                                                </span>
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
                                                    <strong>{{ $errors->first('monograph_title') }}</strong>
                                                </span>
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
                                                <strong>{{ $errors->first('grade_id') }}</strong>
                                            </span>
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
                                                    <strong>{{ $errors->first('description') }}</strong>
                                                </span>
                                            @endif  
                                        </div>
                                    </div>
                                </div>
                            </div>
                        

                
                        </div>
                        <div class="col-md-6 bg-dark">
                            <div style="position:fixed">
                                <img id='img'  />
                            </div>
                        </div>
                        
                    </div>

                </div>
                <br>
                <div class="form-actions fluid">
                    <div class="row">
                        <div class="col-md-offset-2 col-md-8">
                            <button type="submit" class="btn green">{{ trans('general.save') }}</button>
                            <a href="{{ route('archivedata.create') }}"
                               class="btn default">{{ trans('general.cancel') }}</a>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
            
        </div>
    </div>
@endsection('content')


@push('styles')
<link rel="stylesheet" href="{{ url('assets/plugins/persiandatepicker') }}/css/persianDatepicker-default.css"/>
    
@endpush

@push('scripts')
<script src="{{ url('assets/plugins/persiandatepicker') }}/js/persianDatepicker.min.js"></script>
<script type="text/javascript">
    //  $("#school_graduation_year,#birth_date,#year_of_inclusion,#graduated_year,#transfer_year,#leave_year,#failled_year,#monograph_date").persianDatepicker({
   
        $(function() {
        $("#school_graduation_year,#birth_date,#year_of_inclusion,#graduated_year,#transfer_year,#leave_year,#failled_year,#monograph_date").persianDatepicker({
     	months: ["حمل","ثور","جوزا","سرطان","اسد","سنبله","میزان","عقرب","قوس","جدی","دلو","حوت"],
        dowTitle: ["شنبه", "یکشنبه", "دوشنبه", "سه شنبه", "چهارشنبه", "پنج شنبه", "جمعه"],
        shortDowTitle: ["ش", "ی", "د", "س", "چ", "پ", "ج"],
        showGregorianDate: !1,
        persianNumbers: !0,
        formatDate: "YYYY/MM/DD",
        selectedBefore: !1,
        selectedDate: null,
        startDate: null,
        endDate: null,
        prevArrow: '\u25c4',
        nextArrow: '\u25ba',
        theme: 'default',
        alwaysShow: !1,
        selectableYears: null,
        selectableMonths: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
        cellWidth: 25, // by px
        cellHeight: 25, // by px
        fontSize: 15, // by px                
        isRTL: !1,
        calendarPosition: {
            x: 0,
            y: 0,
        },
        onShow: function () { },
        onHide: function () { },
        onSelect: function () { }
        }); 
    });
         
    </script>

    <script>
    function loadPage(bookId) {
       $.ajax({
          type:'GET',
          url:'/archiveLoadPage/'+bookId,
          data:'_token = <?php echo csrf_token() ?>',
          success:function(data) {
            $("#archiveimage_id").empty();
                $.each(data.pages, function (key, value) {
                    
                    // var v=value.path.split("")[3].replace(".png","").replace(".jpg","");
                    var v = value.path.split("/").pop().replace(".png", "").replace(".jpg", "");
                    console.log(v);
                    $("#archiveimage_id").append('<option value="' +value.path + "::"+ value.id +' " image="' + value.path + '">' + v +
                        '</option>');
                }); 
          }
       });
    }
  

    function setImage(image){
    var v = image.split("::");
    $("#img").attr('src', v[0]);

    // Set the width and height of the image to A4 dimensions
    $("#img").css({
        'width': '240mm',
        'height': '170mm'
    });

    $("#archive_image_id").val(v[1]); 
    }


    </script>
    @endpush