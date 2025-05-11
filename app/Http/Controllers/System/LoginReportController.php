<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\AuthenticationLogReadOnly;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LoginReportController extends Controller
{
    public function __construct()
    {
        // Applying permission middleware to the 'index' and 'show' actions
        $this->middleware('permission:view-loginReport', ['only' => ['index', 'showReport']]);
        ini_set('memory_limit', '4000M');
    }

    /**
     * Display the login report index view.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLog()
    {
        return view('login_report.index');
    }

    /**
     * Handle search requests for the login report.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */

     public function showReport(Request $request)
     {
         // Validate request data
         $request->validate([
             'from_login_at' => 'required|date',
             'to_login_at' => 'required|date',
         ]);
     
         // Retrieve the date range from the request
         $fromDate = Carbon::parse($request->input('from_login_at'));
         $toDate = Carbon::parse($request->input('to_login_at'));
     
         // Calculate the date difference (including both dates)
         $dateDifference = $fromDate->diffInDays($toDate) + 1;
     
         // Query for logs within the specified date range
         $authenticationLogs = AuthenticationLogReadOnly::whereBetween('login_at', [$fromDate, $toDate])
             ->get()
             ->groupBy(function ($log) {
                 // Group logs by date (format: 'Y-m-d')
                 return $log->login_at->format('Y-m-d');
             });
     
         // Calculate logs by day and get the day of the week for each date
         $logsByDay = [];
         $logsByDayData = [];
         $j= 0;
         
        //  dd($dateDifference,$authenticationLogs,$authenticationLogs->toArray());
         foreach ($authenticationLogs as $date => $logs) {
             // Create an entry for each date with the date, count, day of the week, and IDs
             $logsByDay['date'][$j] = $date;
             $logsByDay['count'][$j] = $logs->count();
             $logsByDay['day_of_week'][$j] = Carbon::parse($date)->format('l');
             $j++;
         }
        //  dd($logsByDay);
         $authenticationLogsArray =$authenticationLogs->toArray();
         for($i=0;$i<$dateDifference; $i++){
            $currDate=$fromDate;
            $hasData=false;
            for($m=0;$m<count($logsByDay['date']);$m++)
            {
               
                if($logsByDay['date'][$m] ==$currDate->format('Y-m-d'))
                {
                    $logsByDayData['date'][$i] = $logsByDay['date'][$m];
                    $logsByDayData['count'][$i] = $logsByDay['count'][$m];
                    $logsByDayData['day_of_week'][$i] = $logsByDay['day_of_week'][$m];
                    $hasData=true;
                    // break;
                    // echo $m,'---',$currDate, '<br>';
                }
            }
            if($hasData==false)
            {
                $logsByDayData['date'][$i] = $currDate->format('Y-m-d');
                $logsByDayData['count'][$i] = 0;
                $logsByDayData['day_of_week'][$i] = Carbon::parse($currDate)->format('l');
            }
            $currDate->modify('+1 day');

         }
        //  dd($logsByDay['date'],$logsByDayData);
     
         // Pass data to the view
         return view('login_report.index', [
             'dateDifference' => $dateDifference,
             'logsByDay' => $logsByDay,
             'logsByDayData' => $logsByDayData,
             'from_login_at' => $fromDate->format('Y-m-d'),
             'to_login_at' => $toDate->format('Y-m-d'),
         ]);
     }
     
}
