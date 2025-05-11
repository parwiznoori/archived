<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use App\Models\Announcement;

class NoticeboardController extends Controller
{
    public function index()
    {   
        return view('announcements.noticeboard_list', [
            'title' => trans('general.noticeboard'),
            'announcements' => Announcement::latest('created_at')->paginate(5)
        ]);
    }

    public function show(Announcement $announcement)
    {         
        if (! $announcement->visited()) {
            $announcement->visit();
        }
        
        return view('announcements.show', [
            'title' => trans('general.noticeboard_description'),
            'announcement' => $announcement
        ]);
    }
}
