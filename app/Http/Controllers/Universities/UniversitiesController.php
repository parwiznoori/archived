<?php

namespace App\Http\Controllers\Universities;

use App\DataTables\UniversitiesDataTable;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Department;
use App\Models\Group;
use App\Models\Province;
use App\Models\Student;
use App\Models\University;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager as Image;
use Maklad\Permission\Models\Permission;
use Maklad\Permission\Models\Role;

class UniversitiesController extends Controller
{

    public function __construct()
    {        
         $this->middleware('permission:view-university', ['only' => ['index', 'show']]);        
         $this->middleware('permission:create-university', ['only' => ['create','store']]);
         $this->middleware('permission:edit-university', ['only' => ['edit','update']]);
         $this->middleware('permission:delete-university', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UniversitiesDataTable $dataTable)
    {        
        return $dataTable->render('universities.index', [
            'title' => trans('general.universities'),
            'description' => trans('general.universities_list'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('universities.create', [
            'title' => trans('general.universities'),
            'description' => trans('general.create_university'),
            'provinces' => Province::pluck('name', 'id'),
            
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
        $validatedData = $request->validate([
            'name' => 'required|max:255|unique:universities,name',
            'name_eng' => 'required|max:100|unique:universities,name_eng',
            'domain' => 'max:10',           
            'chairman' => '',           
            'student_affairs' => '',
            'province_id' => 'required',
            'address' => '',
            'website_url' => '',
            'phone' => '',
            'email' => ''
        ]);
        
        $university = University::create($validatedData);

        return redirect(route('universities.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($university)
    {
        exit();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($university)
    {
        return view('universities.edit', [
            'title' => trans('general.universities'),
            'description' => trans('general.edit_university'),
            'university' => $university,
            'provinces' => Province::pluck('name', 'id'),
            
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $university)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255|unique:universities,name,'.$university->id,
            'name_eng' => 'required|max:100|unique:universities,name_eng,'.$university->id,
            'domain' => 'max:10',        
            'chairman' => '',           
            'student_affairs' => '',
            'province_id' => 'required',
            'address' => '',
            'website_url' => '',
            'phone_number' => '',
            'email' => ''
        ]);
        $university->update($validatedData);
        
        if($request->file('logo'))
        {
            $logo = $request->file('logo');
                
            $path = 'universities/'.$university->id.'.jpg';
            $picpath ='/public/storage/';

                
            if ($university->logo_url) {
                Storage::delete(str_replace("/storage/","public/", $university->photo_url));

            }
            $store = Storage::createLocalDriver(["root" => base_path()]);
            $store->put(
                $picpath.$path, (new Image)->make($logo)->encode()
            );

            $university->update([
                'logo_url' => 'storage/'.$path
            ]);
        
        }

        
          

        return redirect(route('universities.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($university)
    {
        $university->delete();

        return redirect(route('universities.index'));
    }

     /**
     * Show the list of users with in university
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function users_list($university)
    {
    
        $usersListByUniversity=array();
        $i=0;
        $usersList=User::select('users.*')
        ->with('grades')
        ->with('university')
        ->where('users.university_id',$university->id)
        ->orderBy('users.name')
        ->get();

       
        foreach($usersList as $user)
        {
            $user_grades = $user->grades;
            $userGradeList = array();
            $j=0;
            if($user_grades)
            {
                foreach($user_grades as $user_grade)
                {
                    $userGradeList[$j] = $user_grade->name;
                    $j++;
                }
            }
            $last_login = $user->authentications()->orderBy('login_at', 'desc')->first();
            $login_at='';
            if($last_login)
            {
                $login_at = $last_login->login_at;
            }

            $usersListByUniversity[$i]['id']=$user->id;
            $usersListByUniversity[$i]['name']=$user->name;
            $usersListByUniversity[$i]['position']=$user->position;
            $usersListByUniversity[$i]['phone']=$user->phone;
            $usersListByUniversity[$i]['email']=$user->email;
            $usersListByUniversity[$i]['active']=$user->active;
            $usersListByUniversity[$i]['created_at']=$user->created_at;
            $usersListByUniversity[$i]['updated_at']=$user->updated_at;
            $usersListByUniversity[$i]['user_grade']=implode(',',$userGradeList);
            $usersListByUniversity[$i]['login_at']=$login_at;
            $i++;
        }
       
        return view('universities.users-list', [
            'title' => trans('general.universities'),
            'description' => trans('general.users_list'),
            'university' => $university,
            'usersListByUniversity' => $usersListByUniversity,
        ]);
    }

     /**
     * Show the list of activities with in university
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function activities($university,$year = null)
    {
        $kankorYear = $year ?? \App\Models\Student::max('kankor_year');

        $departmentListByUniversity=array();
        $i=0;
        $departmentsList=Department::select(
            'departments.name as department_name',
            'departments.id as department_id',
            'faculties.name as faculty_name',
            'faculties.id as faculty_id'
        )
        ->leftJoin('faculties', 'faculties.id', '=', 'faculty_id')
        ->where('departments.university_id',$university->id)
        ->orderBy('faculties.name')
        ->orderBy('departments.name')
        ->get();

       
        foreach($departmentsList as $department)
        {
            $groups_count = Group::where('department_id',$department->department_id)
            ->where('kankor_year',$kankorYear)
            ->count();
            $courses_count = Course::where('department_id',$department->department_id)
            ->where('year',$kankorYear)
            ->count();
            $students_count = Student::where('department_id',$department->department_id)
            ->where('kankor_year',$kankorYear)
            ->count();
            
            $departmentListByUniversity[$i]['faculty_name']=$department->faculty_name;
            $departmentListByUniversity[$i]['department_name']=$department->department_name;
            $departmentListByUniversity[$i]['department_id']=$department->department_id;
            $departmentListByUniversity[$i]['groups_count']=$groups_count;
            $departmentListByUniversity[$i]['courses_count']=$courses_count;
            $departmentListByUniversity[$i]['students_count']=$students_count;
            
            $i++;
        }
       
        return view('universities.activities', [
            'title' => trans('general.universities'),
            'description' => trans('general.activities'),
            'university' => $university,
            'kankor_years' =>  Student::select('kankor_year')->distinct()->orderBy('kankor_year', 'desc')->pluck('kankor_year','kankor_year'),
            'kankorYear' => $kankorYear,
            'departmentListByUniversity' => $departmentListByUniversity,
        ]);
    }
}
