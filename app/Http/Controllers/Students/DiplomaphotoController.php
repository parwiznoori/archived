<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager as Image;

class DiplomaphotoController extends Controller
{
    public function __construct()
    {        
         $this->middleware('permission:update-student-cardandDiplomaphoto', ['only' => ['index', 'store']]);                 
    }

    public function index($student)
    {
        
        return view('students.diplomaphoto', [
            'student' => $student,
            'title' => trans('general.create_diplomaphoto')
        ]);

    
    }

    public function store(Request $request, $student)
    {

        $this->validate($request, [
            'diplomaphoto' => 'required|image|max:512',
        ]);

        $file = $request->file('diplomaphoto');

      $path = 'diplomaphotos/'.$student->form_no.'.jpg';
      $picpath ='/public/storage/';
//        $disk = Storage::disk('local');
          
      if ($student->diploma_photo) {
          Storage::delete(str_replace("/storage/","public/", $student->diploma_photo));
//  		Storage::delete(base_path().'/public/storage/'.$path);
    }
$store = Storage::createLocalDriver(["root" => base_path()]);

//dd($disk);

        //default size was 300
        $store->put(
           $picpath.$path, (new Image)->make($file)->fit(354, 472)->encode()
        );
	//	$img = $request->file('photo');

		
		
		


        $student->update([
            'diploma_photo' => 'storage/'.$path
        ]);

        return redirect()->back()->with('success', trans('general.x_has_been_updated', ['x' => trans('general.create_diplomaphoto')]));        
    }
}
