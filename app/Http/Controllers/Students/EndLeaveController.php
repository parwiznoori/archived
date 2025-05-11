<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;

class EndLeaveController extends Controller
{
    public function __construct()
    {        
        $this->middleware('permission:end-leave', ['only' => ['index']]);        
    }

    public function index($leave)
    {
        $message = '';
        if( $leave->end_leave == false )
        {
            $leave->update([
                'end_leave' => true
            ]);

            $leave->student->update([
                'status_id' => 2
            ]);
            $message = __('general.end_leave_successfully_is_done',[ 'form_no' => $leave->student->form_no ]);
        }

        if( $leave->end_leave == true &&  $leave->student->status_id == 4 )
        {
            $leave->student->update([
                'status_id' => 2
            ]);
            $message = __('general.end_leave_successfully_is_done',[ 'form_no' => $leave->student->form_no ]);
        }

        return redirect(route('leaves.index'))->with('message', $message);
    }
}
