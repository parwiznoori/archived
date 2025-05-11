<?php

namespace App\Http\Controllers\Archive;

use App\DataTables\ArchiveimageDataTable;
use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\Archivedatastatus;
use App\Models\Archiveimage;
use DB;
use Illuminate\Http\Request;

class ArchiveimageController extends Controller
{
    public function __construct()
    {        
         $this->middleware('permission:view-archiveimage', ['only' => ['index', 'show']]);        
         $this->middleware('permission:create-archiveimage', ['only' => ['create','store']]);
         $this->middleware('permission:edit-archiveimage', ['only' => ['edit','update', 'updateStatus','update_groups']]);
         $this->middleware('permission:delete-archiveimage', ['only' => ['destroy']]);
         
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ArchiveimageDataTable $dataTable)
    {   
        return $dataTable->render('archiveimage.index', [
            'title' => trans('general.archiveimage'),
            'description' => trans('general.archive_list')            
        ]);

    
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function create()
    {    
    
       //changed teacher code 
        return view('archiveimage.create', [
           
            'title' => trans('general.archiveimage'),
            'description' => trans('general.create_archiveimage'),
            'archives' => Archive::pluck('book_name', 'id'),
            'archive' => old('archive') != '' ? Archive::where('id', old('archive'))->kpluck('book_name', 'id') : [],
            'archivedatastatuss' => Archivedatastatus::pluck('status', 'id'),
            'archivedatastatus' => old('archivedatastatus') != '' ? Archivedatastatus::where('id', old('archivedatastatus'))->pluck('status', 'id') : [],
        ]);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


 

     public function store(Request $request)
     {
         $request->validate([
             'archive_id' => 'required|exists:archives,id',
             'path.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:20408',
         ]);
     
         $archive = Archive::findOrFail($request->archive_id);
         $directory = public_path('archivefiles/' . $archive->book_name);
     
         if ($request->hasFile('path')) {
             $archiveImages = [];
             $pageNo=1;
             foreach ($request->file('path') as $file) {
                 $archiveimage = $file->getClientOriginalName(); // Adjust the filename as needed
                 $file->move($directory, $archiveimage);

                 $archiveImages[] = [
                     'book_pagenumber'=>$pageNo,
                     'archive_id' => $request->archive_id,
                     'path' => '/archivefiles/' . $archive->book_name . '/' . $archiveimage,
                 ];
                 $pageNo=$pageNo+1;
             }

             // Insert new images associated with the archive
             Archiveimage::insert($archiveImages);
     
             return redirect('/archiveimage')->with(['message' => 'Images stored successfully!', 'status' => 'success']);
         } else {
             return redirect()->back()->with(['message' => 'No images were provided.', 'status' => 'error']);
         }
     }
     

     public function setPageTotal($id,$total_student){
        $archive = Archiveimage::findOrFail($id);
        if($total_student==0){
            $status=2;
        }else{
            $status=3;
        }
        $archive->total_students = $total_student;
        $archive->status_id = $status;
        $archive->save();
        return $archive;
     }





    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
   

     public function edit($archiveimage_id)
     {
         $archiveimage = Archiveimage::findOrFail($archiveimage_id);
     
         return view('archiveimage.edit', [
             'title' => trans('general.archiveimage'),
             'description' => trans('general.edit_archiveimage'),
             'archives' => Archive::pluck('book_name', 'id'),
             'archive' => old('archive') != '' ? Archive::where('id', old('archive'))->kpluck('book_name', 'id') : [],
             'archiveimage' => $archiveimage,
         ]);
     }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
 
     public function update(Request $request, $id)
     {
         $request->validate([
             'archive_id' => 'required|exists:archives,id',
             'path.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20408',
         ]);
     
         $archive = Archive::findOrFail($request->archive_id);
         $directory = public_path('archivefiles/' . $archive->book_name);
     
         if ($request->hasFile('path')) {
             $archiveImages = [];
     
             foreach ($request->file('path') as $file) {
                 $archiveimage = $file->getClientOriginalName(); // Adjust the filename as needed
                 $file->move($directory, $archiveimage);
     
                 $archiveImages[] = [
                     'archive_id' => $request->archive_id,
                     'path' => '/archivefiles/' . $archive->book_name . '/' . $archiveimage,
                 ];
             }
     
             // Insert new images associated with the archive
             Archiveimage::insert($archiveImages);
     
             return redirect('/archiveimage')->with(['message' => 'Images updated successfully!', 'status' => 'success']);
         } else {
             return redirect()->back()->with(['message' => 'No new images were provided.', 'status' => 'error']);
         }
     }
     
     
     
     
    


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($archive_id)
    {
        $archiveimage = Archiveimage::findOrFail($archive_id);
    
        \DB::transaction(function () use ($archiveimage) {
            // Delete image file from archivefiles folder
            $path = public_path($archiveimage->path);
            if (file_exists($path)) {
                unlink($path);
            }
    
            // Delete the archive image record from the database
            $archiveimage->delete();
        });
    
        return redirect(route('archiveimage.index'));
    }
    

    
}

