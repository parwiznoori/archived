<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {
            direction: rtl;
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #FAFAFA;
            font-family: 'nazanin';
        }

        * {
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 5mm;
            margin: 10mm auto;
            border: 0.5px #D3D3D3 solid;
            border-radius: 2px;
            background: white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        @media print {

            html,
            body {
                width: 210mm;
                height: 297mm;
            }

            .page {
                margin: 0;
                border: initial;
                border-radius: initial;
                width: initial;
                min-height: initial;
                box-shadow: initial;
                background: initial;
            }
        }

        p {
            margin: 0;
        }

        .table td,
        .table th {
            padding: 4px 4px;
            border: 0.5px solid #000;
        }

        .inner-table tr:first-child td,
        .inner-table tr:first-child th {
            border-top: 0
        }

        .inner-table tr td:first-child,
        .inner-table tr th:first-child {
            border-right: 0
        }

        .inner-table tr:last-child td,
        .inner-table tr:last-child th {
            border-bottom: 0
        }

        .inner-table tr td:last-child,
        .inner-table tr th:last-child {
            border-left: 0
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .bg-grey {
            background-color: #d8d8d8;
        }

        .center {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="page">
        <table class="header_table" style="width:100%;">
            <tr>
                <td style="text-align:right;width:33%;vertical-align:bottom;">
                    <h4>{{__('general.monograph_determination')}}</h4>
                </td>
                <td style="text-align:center;width:33%;vertical-align:top;">
                    <img src="{{ public_path('img/wezarat-logo.jpg') }}" style="max-width: 80px" />
                    <p style="margin-top:5px;font-size: 14px">{{__('general.ministry_title')}}</p>
                    <p style="margin-top:5px;">پوهنتون {{ $student->university->name }}</p>
                    <p style="margin-top:5px;">دیپارتمنت : {{ $student->department ? $student->department->name : '' }}
                    </p>
                </td>
                <td style="text-align:right;width:33%;padding-right:17%;vertical-align:top;padding-top:1%;">
                    <img src="{{ public_path($student->photo_relative_path()) }}"
                        style="max-width: 100px">
                </td>
            </tr>
        </table>
        <table class="table" style="margin-top: 20px">
            <tr>
                <td>
                    {{__('general.to_facultyـofficial')}} 
                    @if(isset($student->department->faculty_id))
                    {{$student->department->facultyName->name}}
                    @else
                    {{$student->department->faculty}}
                    @endif
                    
                   
                </td>
                <br>
            </tr>
            <tr>
            </tr>
            <tr>
                <td>
                    <table class="table inner-table borderless" style>
                        <tr>
                            <td style=" padding-top: 0px; border:none ; padding-right:10px">
                                <p style="text-align: justify; padding-right:10px">
                                    {{__('general.my_self')}}{{$student->getFullNameAttribute()}}&nbsp;{{__('general.my_father_name')}}&nbsp;{{$student->father_name}}&nbsp;{{__('general.year')}}&nbsp;{{$request->year}}
                                    {{__('general.department')}}&nbsp;{{$student->department->name}}&nbsp;
                                    {{__('general.semester')}} &nbsp;{{$student->semester}}
                                    {{__('general.for_the_completion_of_my_study')}} &nbsp; {{$request->year}}
                                    &nbsp; {{__('general.to_present')}} &nbsp;&nbsp;
                                    {{__('general.thus_please_refer_to_the_esteeme_directorate_of_the_relevant_department_to_cooperate_with_me_in_determining_the_subject_and_professor')}}
                                    &nbsp;&nbsp;&nbsp;{{__('general.with_respect')}}
                                </p>
                                <p style="text-align: justify; padding-right:10px">
                                    {{__('general.student_name')}}: {{$student->name}}
                                    .......................................
                                    امضا:............................................. تاریخ : /&nbsp; &nbsp;&nbsp;
                                    &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;/&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;/ 
                                </p>

                            </td>
                            <br>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="table inner-table borderless" style>
                        <tr>
                            <td style=" padding-top: 0px; border:none ; padding-right:10px">
                                <p>{{__('general.head_of_department')}}:
                                    {{$student->department ? $student->department->name : ''}} !</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="border:none ; padding-right:10px">
                                <p>{{__('general.please_take_principled_steps_in_determining_the_subject_and_the_supervisor_for_the_student')}}{{__('general.with_respect')}}
                                </p>
                            </td><br>
                        </tr>
                        <tr>
                            <td style="border:none ; padding-right:20px">
                                <p> {{__('general.faculty_chairman')}} :&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                    &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                    &nbsp;&nbsp; &nbsp;&nbsp; امضاّ :&nbsp;<span>...................<span> &nbsp;&nbsp;
                                            &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>
                                                {{__('general.date')}} :
                                                /&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/
                                            </span></p>
                            </td><br>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="table inner-table borderless" style>
                        <tr>
                            <td style=" padding-top: 0px; border:none ; padding-right:10px">
                                <p>{{__('general.respected')}}:
                                {{__('general.subject_of')}}&nbsp;&nbsp;( مونوگراف ) &nbsp;&nbsp;  
                                {{__('general.name_of')}}&nbsp;&nbsp;(  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  )
                                &nbsp;&nbsp;{{__('general.under_the_supervison_of_teacher')}}&nbsp;&nbsp;(  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  )
                                <br><span style = "padding-top:10px">
                                &nbsp;&nbsp;{{__('general.set_and_subject_tite')}}&nbsp;&nbsp; (  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  )
                                </span>
                                &nbsp;&nbsp;{{__('general.registred_in_department_monograph_book')}}&nbsp;&nbsp; {{__('general.with_respect')}}
                                </p>
                                <br>
                            </td>
                        </tr>
                        <tr>
                            <td style="border:none ; padding-right:20px">
                                <p> {{__('general.name_of_head_of_department')}} :&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                    &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                    &nbsp;&nbsp; &nbsp;&nbsp; امضاّ :&nbsp;<span>...................<span> &nbsp;&nbsp;
                                            &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>
                                                {{__('general.date')}} :
                                                /&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/
                                            </span></p>
                            </td><br>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="table inner-table borderless">
                        <tr>
                            <td style=" padding-top: 0px; border:none ; padding-right:10px">
                            <p style="text-align: justify; padding-right:10px">
                                    {{__('general.my_self')}}&nbsp;{{$student->getFullNameAttribute()}}&nbsp;
                                    {{__('general.teacher_approval')}}
                                    &nbsp;&nbsp;&nbsp;{{__('general.with_respect')}}
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style="border:none ; padding-right:10px">
                                <p></p>
                            </td>
                        </tr>
                        <tr>
                            <td style="border:none ; padding-right:20px">
                                <p> {{__('general.supervise_teacher_name')}} :&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                    &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                    &nbsp;&nbsp; &nbsp;&nbsp; امضاّ :&nbsp;<span>...................<span> &nbsp;&nbsp;
                                    &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>{{__('general.date')}}:
                                    /&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/</span>
                                </p>
                            </td><br>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="table inner-table borderless">
                        <tr>
                            <td style=" padding-top: 0px; border:none ; padding-right:10px">
                                <p>{{__('general.student_reference_for_guidance')}}&nbsp;&nbsp;
                                <br>
                                {{__('general.draft_the_subject_of_the_seminar_scheduled_on')}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;     /&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;14
                                {{__('general.it_was_observed_and_student_should_be_guide')}}&nbsp;&nbsp;&nbsp;
                                {{__('general.with_respect')}}
                                </p>
                            </td><br>
                        </tr>
                        <tr>
                            <td style="border:none ; padding-right:20px">
                                <p> {{__('general.supervise_teacher_name')}} :&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                    &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                    &nbsp;&nbsp; &nbsp;&nbsp; امضاّ :&nbsp;<span>...................<span> &nbsp;&nbsp;
                                    &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>{{__('general.date')}}:
                                    /&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/</span>
                                </p>
                            </td><br>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="table inner-table borderless">
                        <tr>
                            <td style=" padding-top: 0px; border:none ; padding-right:10px">
                                <p>{{__('general.the_seminar_has_been_studied_after_the_corrections_please_final_it')}}&nbsp;&nbsp;
                                <br>
                                {{__('general.with_respect')}}
                                </p>
                            </td><br>
                        </tr>
                        <tr>
                            <td style="border:none ; padding-right:20px">
                                <p> {{__('general.supervise_teacher_name')}} :&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                    &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                    &nbsp;&nbsp; &nbsp;&nbsp; امضاّ :&nbsp;<span>...................<span> &nbsp;&nbsp;
                                    &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>{{__('general.date')}}:
                                    /&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/</span>
                                </p>
                            </td><br>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="table inner-table borderless">
                        <tr>
                            <td style=" padding-top: 0px; border:none ; padding-right:10px">
                                <p>{{__('general.to_facultyـofficial')}} {{$student->department->faculty}}!&nbsp;&nbsp;</p>
                               <p>
                               <p>{{ __('general.the_text_of_the_seminar_prepared_according_to_the_previously_determined_topic_under_the_guidance_and_it_iscompleted_the_student_is_ready_to_give_a_seminar')}}
                               &nbsp; &nbsp;&nbsp;<span>{{__('general.date')}} &nbsp;&nbsp;
                                    /&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/</span>
                                    &nbsp;&nbsp;&nbsp; {{ __('general.student_is_going_to_present_the_seminar')}}
                                    &nbsp;&nbsp;&nbsp;{{__('general.with_respect')}}
                               </p>
                            </td><br>
                        </tr>
                        <tr>
                            <td style="border:none ; padding-right:20px">
                                <p> {{__('general.name_of_head_of_department')}} :&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                    &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                    &nbsp;&nbsp; &nbsp;&nbsp; امضاّ :&nbsp;<span>...................<span> &nbsp;&nbsp;
                                            &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>
                                                {{__('general.date')}} :
                                                /&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/
                                            </span></p>
                            </td><br>
                        </tr>
                    </table>
                </td>
            </tr>

        </table>
    </div>
</body>

</html>