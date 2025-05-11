<?php

namespace App\Http\Controllers\System;

use App\DataTables\TeacheracademicrankDataTable;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\TeacherAcademicRank;
use Illuminate\Http\Request;


class TeacherAcademicRankController extends Controller
{
    public function __construct()
    {        
         $this->middleware('permission:view-teacheracademicrank', ['only' => ['index', 'show']]);        
         $this->middleware('permission:create-teacheracademicrank', ['only' => ['create','store']]);
         $this->middleware('permission:edit-teacheracademicrank', ['only' => ['edit','update']]);
         $this->middleware('permission:delete-teacheracademicrank', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(TeacheracademicrankDataTable $dataTable)
    {        
      
        return $dataTable->render('teacheracademicrank.index', [
            'title' => trans('models/teacheracademicrank.plural'),
            'description' => trans('general.list'),         
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $teacheracademicrank =TeacherAcademicRank::all()->count();
        $i=0;
       
        if(!$teacheracademicrank)
        {
            $facultiesFromDatabase=Student::where('deleted_at',Null)
            ->select('student_sheft')
            ->distinct()
            ->get();

            foreach($facultiesFromDatabase as $faculty)
            {
                if($faculty->student_sheft)
                {
                   
                    $newFaculty=new TeacherAcademicRank();
                    $newFaculty->name=$faculty->student_sheft;
                    $newFaculty->save();
                   // echo "$i++- faculty ".$faculty->faculty." is inserted to faculty table <br>";

                }
               
            }
        }

    //     return view('teacheracademicrank.create', [
    //         'title' => trans('models/teacheracademicrank.singular'),
    //         'description' => trans('crud.add_new'),    
    //     ]);
     }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required'     
        ]);

        $input = $request->all();

        $teacheracademicrank = TeacherAcademicRank::create($input);

        // Flash::success(__('messages.saved', ['model' => __('models/teacheracademicrank.singular')]));

        return redirect(route('teacheracademicrank.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $teacheracademicrank = TeacherAcademicRank::find($id);

        return view('teacheracademicrank.edit', [
            'title' => trans('models/teacheracademicrank.singular'),
            'description' => trans('crud.edit'), 
            'teacheracademicrank' => $teacheracademicrank

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
        $validatedData = $request->validate([
            'title' => 'required'       
        ]);

        $teacheracademicrank = TeacherAcademicRank::find($id);

        if (empty($teacheracademicrank)) {
            // Flash::error(__('messages.not_found', ['model' => __('models/cars.singular')]));

            return redirect(route('teacheracademicrank.index'));
        }

        $teacheracademicrank->update($request->all());

        // Flash::success(__('messages.updated', ['model' => __('models/cars.singular')]));

        return redirect(route('teacheracademicrank.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $teacheracademicrank = TeacherAcademicRank::find($id);

        if (empty($teacheracademicrank)) {
            // Flash::error(__('messages.not_found', ['model' => __('models/cars.singular')]));

            return redirect(route('teacheracademicrank.index'));
        }

        $teacheracademicrank->delete($id);

        // Flash::success(__('messages.deleted', ['model' => __('models/cars.singular')]));

        return redirect(route('teacheracademicrank.index'));
    }
}
