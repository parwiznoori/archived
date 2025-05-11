<?php

use App\Models\Semester;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

function userPhoto($user, array $atributes = [])
{
	if($user->photo_url and Storage::exists(str_replace("/storage/","public/", $user->photo_url))) {
		$src = url($user->photo_url);
    } else {
    	$src = url("/images/avatar-placeholder.png");
    }
        
   	$attr = "";
   	if ($atributes) {
   		foreach($atributes as $key => $value) {
   			$attr .= $key."='".$value."' ";
   		}
   	}
   	
   	return  "<img src='$src' ".$attr." />";
}

function teamLogo($team = null, array $atributes = [])
{
    return systemLogo($atributes);
}

function systemLogo(array $atributes = [])
{
    $attr = "";
    if ($atributes) {
      foreach($atributes as $key => $value) {
        $attr .= $key."='".$value."' ";
      }
    }
    
    if(Storage::exists("/public/".config('client')."/logo.jpg")) {
      $src = asset("/storage/".config('client')."/logo.jpg");
    } else {
      $src = asset("/images/logo-placeholder.jpg");
    }
      
    return  "<img src='$src' ".$attr." />"; 
}

function teamName($team = null)
{
    return systemName();
}

function systemName()
{
    $setting = Setting::allTeams()->where(['team_id' => 0, 'key' => 'system_name'])->first();
    
    return $setting ? $setting->value : null;
}

function defaultSemesterId()
{
     $semester = Semester::where('default', 1)->first();

     return $semester ? $semester->id : NULL;
}

function defaultSemesterTitle()
{
     $semester = Semester::where('default', 1)->first();

     return $semester ? $semester->title : NULL;
}

function currentTeamId()
{
     return auth()->user()->current_team_id;
}

function valueOf($key, $teamId = null)
{
    if ($teamId) {
        $setting = Setting::allTeams()->where(['team_id' => $teamId, 'key' => $key])->first();
        return $setting ? $setting->value : null;
    }

    return Setting::value($key);
}

function convertJalaliToMiladi($date ,$miladiFormat = 'Y-m-d H:i:s')
{
    $date = explode("/", $date);
    
    if (isset($date[0]) and isset($date[1]) and isset($date[2])) {
        return date($miladiFormat, jDateTime::mktime(0, 0, 0, $date[1], $date[2], $date[0], true, 'Asia/Kabul'));
    } else {
        return '';
    }
    
}

function jalaliStrtotime($input = null)
{
    if (! $input)
       return;

    $date = explode("/", $input);                     
    return jDateTime::mktime(0, 0, 0, $date[1], $date[2], $date[0]);
}

function jalaliDate($format = 'Y/m/j', $time = null, $persianNumber = false)
{
    $time = $time ? $time : time();
    return jDateTime::date($format, $time, $persianNumber);
}

function weekDays()
{
  return \DB::table('days')->get();
}

function getGrades()
{
   return  [
            'bachelor' => trans('general.bachelor'),
            'masters' => trans('general.masters')
        ];
}

function getLastActivity($user = null)
{
    $user = $user ? $user : auth()->user();

    $lastActivity = $user->activity->last();

    return $lastActivity ? jalaliDate('Y/n/j H:i', strtotime($lastActivity->created_at)) : trans('general.no_activity');
}

function getStringBetween($string, $start, $end) //used to parse pdf files config
{
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function getGrade($score)
{
  if($score >= 90) {
      return 'A';
  }
  elseif($score >= 80 and $score < 90){
    return 'B';
  }
  elseif ($score >= 70 and $score < 80) {
    return 'C';
  }
  elseif($score >= 55 and $score < 70){
      return 'D';
  }
  else{
      return "F";
  }
}

function getGradePoint($grade){
    switch($grade){
        case 'A':
            return 4;
            break;
        case 'B':
            return 3.5;
            break;
        case 'C':
            return 3.0;
            break;
        case 'D':
            return 2.5;
            break;
        case 'F':
            return 0.00;
            
    }

    }



function convertNumberToFarsi($number) 
{
    if (is_float($number)) {
        $no = explode('.', $number);

        if (isset($no[0]) and isset($no[1])) {
            return convertNumberToFarsi($no[0])." اعشاری ".convertNumberToFarsi($no[1]);    
        }        
    }   

    $ones = array("", "یک",'دو&nbsp;', "سه", "چهار", "پنج", "شش", "هفت", "هشت", "نه", "ده", "یازده", "دوازده", "سیزده", "چهارده", "پانزده", "شانزده", "هفده", "هجده", "نونزده");
    $tens = array("", "", "بیست", "سی", "چهل", "پنجاه", "شصت", "هفتاد", "هشتاد", "نود");
    $tows = array("", "صد", "دویست", "سیصد", "چهار صد", "پانصد", "ششصد", "هفتصد", "هشت صد", "نه صد");

    if (($number < 0) || ($number > 999999999)) {
        throw new Exception("Number is out of range");
    }
    $Gn = floor($number / 1000000);
    /* Millions (giga) */
    $number -= $Gn * 1000000;
    $kn = floor($number / 1000);
    /* Thousands (kilo) */
    $number -= $kn * 1000;
    $Hn = floor($number / 100);
    /* Hundreds (hecto) */
    $number -= $Hn * 100;
    $Dn = floor($number / 10);
    /* Tens (deca) */
    $n = $number % 10;
    /* Ones */
    $res = "";
    if ($Gn) {
        $res .= convertNumberToFarsi($Gn) .  " میلیون و ";
    }
    if ($kn) {
        $res .= (empty($res) ? "" : " ") .convertNumberToFarsi($kn) . " هزار و";
    }
    if ($Hn) {
        $res .= (empty($res) ? "" : " ") . $tows[$Hn] . " و ";
    }
    if ($Dn || $n) {
        if (!empty($res)) {
            $res .= "";
        }
        if ($Dn < 2) {
            $res .= $ones[$Dn * 10 + $n];
        } else {
            $res .= $tens[$Dn];
            if ($n) {
                $res .= " و " . $ones[$n];
            }
        }
    }
    if (empty($res)) {
        $res = "صفر";
    }
    $res=rtrim($res," و");
    return $res;
}

function is_this_student_passed_this_course($total,$chance_two=null,$chance_three=null,$chance_four=null,$MIN_SCORE_FOR_PASSED_EXAM=55)
{
    $passed=0;
    if(isset($total) &&  $total >= $MIN_SCORE_FOR_PASSED_EXAM)
    {
        $passed=1;
        return $passed;
    }

    if(isset($chance_two) &&  $chance_two >= $MIN_SCORE_FOR_PASSED_EXAM)
    {
        $passed=1;
        return $passed;
    }
    if(isset($chance_three) &&  $chance_three >= $MIN_SCORE_FOR_PASSED_EXAM)
    {
        $passed=1;
        return $passed;
    }
    if(isset($chance_four) &&  $chance_four >= $MIN_SCORE_FOR_PASSED_EXAM)
    {
        $passed=1;
        return $passed;
    }

    return $passed;
}

function is_this_student_absent_in_final_exam($final_score,$present,$absent,$absent_exam,$excuse_exam,$deprived)
{
    $is_absent=0;
    //first check student is not deprived
    $is_deprived=is_this_student_deprived_from_exam($present,$absent,$deprived);
    if($is_deprived==0 && $present>0)
    {
        if($absent_exam == 1 && $excuse_exam==0)
        {
            $is_absent=1;
        }
        if(!$final_score)
        {
            $is_absent=1;
        }
    }
    return $is_absent;
}

function is_this_student_deprived_from_exam($present,$absent,$deprived)
{
    $is_deprived=0;
    if(($present * 25) / 100 < $absent)
    {
        $is_deprived=1;

    }
    if($deprived == 1)
    {
        $is_deprived=1;
    }

    return $is_deprived;

}

// forissue and expiar date
function getYerarsBySemesters($numberofsemesters)
{
  if($numberofsemesters == 8) {
      return 4;
  }
  elseif($numberofsemesters == 10 ){
    return 5;
  }
  elseif($numberofsemesters == 12 ){
    return 6;
  }
  
  else{
      return 4;
  }
}

function get_dari_name_of_semester($semester)
{
    switch($semester){
        case '1':
            return 'اول';
            break;
        case '2':
            return 'دوم';
            break;
        case '3':
            return 'سوم';
            break;
        case '4':
            return 'چهارم';
            break;
        case '5':
            return 'پنجم';
        case '6':
            return 'ششم';
        case '7':
            return 'هفتم';
            break;
        case '8':
            return'هشتم';
            break;
        case '9':
            return 'نهم';
            break;
        case '10':
            return 'دهم';
            break;
        case '11':
            return 'یازدهم';
        case '12':
            return 'دوازدهم';   
    }
}

function get_pashto_name_of_semester($semester)
{
    switch($semester){
        case '1':
            return 'لومړی';
            break;
        case '2':
            return 'دوهم';
            break;
        case '3':
            return 'دریم';
            break;
        case '4':
            return 'څلوم';
            break;
        case '5':
            return 'پنڅم';
        case '6':
            return 'شپږم';
        case '7':
            return 'اووم';
            break;
        case '8':
            return'اتم';
            break;
        case '9':
            return 'نهم';
            break;
        case '10':
            return 'لسم';
            break;
        case '11':
            return 'یوولسم';
        case '12':
            return 'دولسم';   
    }
}

function get_student_id()
{
    $studentId = auth('student')->user()->id; 
    return  $studentId;
}

function get_half_year_name($half_year)
{
    switch($half_year){
        case 'fall':
            return __('general.fall');
            break;
        case 'spring':
            return __('general.spring');
            break;
        // case 'winter':
        //     return __('general.winter');
        //     break;
        default:
            return $half_year;
            break;
       
    }
}

function get_half_year_options()
{
    return ['spring' => trans('general.spring'),  'fall' => trans('general.fall')];
   //return ['spring' => trans('general.spring'),  'fall' => trans('general.fall'), 'winter' => trans('general.winter')];
}
function get_score_status($passed,$deprived,$absent_exam,$excuse_exam)
{
    $status = '';
    if($excuse_exam == 1)
    {
        $status = __('general.excuse_exam');
        return  $status;
    }
    if($passed == 1)
    {
        $status = __('general.passed');
        return  $status;
    }
    if($deprived==1)
    {
        $status = __('general.deprived');
        return  $status;
    }
    if($absent_exam==1)
    {
        $status = __('general.absent_exam');
        return  $status;
    }
}

function get_max_courses_in_semester($row_start,$row_end,$studentScores)
{
    $maxRows = 0;
    $countCourses = 0;
    for($i = $row_start; $i <= $row_end ; $i++)
    {
        $countCourses = count($studentScores[$i]);
        if($maxRows < $countCourses) {
            $maxRows = $countCourses;
        }

    }
   
    return $maxRows;
}

function get_subject_type($type)
{
    switch($type){
        case 'general':
            return __('general.general');
            break;
        case 'core':
            return __('general.core');
            break;
        case 'specialized':
            return __('general.specialized');
            break;
        case 'profesional':
            return __('general.profesional');
            break;
        case 'elective':
            return __('general.elective');
            break;
        default:
            return '';
            break;
       
    }
}
function is_semester_deprived_this_student($student_id,$educational_year,$semester,$half_year)
{

}

function getDayNameByIndex($index)
{
    switch($index){
        case '0':
            return __('general.none_schedule');
            break;
        case '1':
            return __('general.saturday');
            break;
        case '2':
            return __('general.sunday');
            break;
        case '3':
            return __('general.monday');
            break;
        case '4':
            return __('general.tuesday');
            break;
        case '5':
            return __('general.wednesday');
            break;
        case '6':
            return __('general.thursday');
            break;
        case '7':
            return __('general.friday');
            break;
    }
}