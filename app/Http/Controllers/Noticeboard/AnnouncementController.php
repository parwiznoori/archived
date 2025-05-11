<?php

namespace App\Http\Controllers\Noticeboard;

use App\DataTables\NoticeBoardDataTable;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maklad\Permission\Models\Permission;
use Maklad\Permission\Models\Role;


class  AnnouncementController extends Controller
{
    public function __construct()
    {        
         $this->middleware('permission:create-announcement', ['only' => ['create','store']]);
         $this->middleware('permission:edit-announcement', ['only' => ['edit','update']]);
         $this->middleware('permission:delete-announcement', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(NoticeBoardDataTable $dataTable)
    {        
        return $dataTable->render('announcements.index', [
            'title' => trans('general.noticeboard'),
            'description' => trans('general.announcements_list')
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('announcements.create', [
            'title' => trans('general.noticeboard'),
            'description' => trans('general.create_noticeboard'),
        ]);
    }

    public function show($announcement)
    {
        if (! $announcement->visited()) {
            $announcement->visit();
        }

        return view('announcements.show', [
            'title' => trans('general.noticeboard_description'),
            'announcement' => $announcement
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|unique:announcements|max:255',
            'body' => 'required|min:10',
        ]);

        $files = $request->file('file');

        $announcement = Announcement::create([
            'title' => $request->title,
            'body' => $request->body,
        ]);

        if ($request->hasFile('file')) {

            foreach($files as $file) {
                $announcement->uploadFile($file);
                }
            }

        return redirect(route('announcements.index'));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($announcement)
    {
        return view('announcements.edit', [
            'title' => trans('general.noticeboard'),
            'description' => trans('general.edit_noticeboard'),
            'announcement' => $announcement,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $announcement)
    {
        Storage::makeDirectory('system_files');

           $files = $request->file('file');

           $announcement->update([
                'title' => $request->title,
                'body' => $request->body,
            ]);

            if ($request->hasFile('file')) {

                foreach($files as $file) {
                    $announcement->uploadFile($file);
                }
            }

        return redirect(route('announcements.index'))->with('message', 'اطلاعات '.$announcement->name.' موفقانه آبدیت شد.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($announcement)
    {
        \DB::transaction(function () use ($announcement){


            $files = $announcement->attachments();

            $announcement->delete();

            foreach ($files as $file) {
                $announcement->deleteFile($file);
            }
        });

        return redirect(route('announcements.index'));
    }
}
