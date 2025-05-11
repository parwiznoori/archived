<?php

namespace App\Http\Controllers\Noticeboard;

use App\Http\Controllers\Controller;
use App\Models\Announcement;


class NoticeBoardController extends Controller
{
    public function show()
    {   
        return view('announcements.noticeboard_list', [

            'announcements' => Announcement::latest('created_at')->paginate(5)
        ]);
    }
}
