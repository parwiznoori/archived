<?php

namespace App\Http\Controllers\Archive;

use App\Http\Controllers\Controller;
use App\Models\Archivedata;
use DB;
use Illuminate\Http\Request;

class ArchivesearchController extends Controller
{
    // public function __construct()
    // {        
    //      $this->middleware('permission:view-archive', ['only' => ['index', 'show']]);        
    //      $this->middleware('permission:create-archive', ['only' => ['create','store']]);
    //      $this->middleware('permission:edit-archive', ['only' => ['edit','update', 'updateStatus','update_groups']]);
    //      $this->middleware('permission:delete-archive', ['only' => ['destroy']]);
         
    // }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

//index
     public function index()
     {   
        
 
         $archivesearchbar = Archivedata::all();
 
         return view('archivedata.archivesearch', ['archivesearchbar' => $archivesearchbar]);
     }


    public function search(Request $request){
        $search = $request->search;
        $archivesearchbar=null;
        if($search!=null || $search!=""){
        $archivesearchbar = Archivedata::select('archivedatas.*', 'archiveimages.path',
        'universities.name as un_name' ,'faculties.name as fa_name' ,'departments.name as de_name' )
        ->join('archiveimages', 'archiveimages.id', '=', 'archivedatas.archiveimage_id')
        ->join('archives', 'archives.id', '=', 'archivedatas.archive_id')

        ->join('universities', 'universities.id', '=', 'archives.university_id')
        ->join('faculties', 'faculties.id', '=', 'archives.faculty_id')
        ->join('departments', 'departments.id', '=', 'archives.department_id')

            ->where(function ($query) use ($search) {
                $query->where('archivedatas.name', 'like', '%' . $search . '%')
                ->orWhere('archivedatas.kankor_id', '=', $search);
            })
            ->get();
        }
        

         
// return $archivesearchbar;
        return view('archivedata.archivesearch', ['archivesearchbar' => $archivesearchbar]);
    //    return view('archivedata.archivesearch', compact('archivesearchbar'));

    }
}
  

  

    

