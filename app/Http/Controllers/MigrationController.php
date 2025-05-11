<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessMigrationData;
use Illuminate\Http\Request;

class MigrationController extends Controller
{
    public function index(){

        return view('migration');
    }
    public function process(Request $request){

        if($request->has('file')){

            //getting the file
            $data = file($request->file);

            // dd($data);
            //chunnk the file in to 1000 records
            $chuncks = array_chunk($data , 1000);

            //getting the header
            $header = [];

            //looping through each of the chenck
            foreach($chuncks as $key => $chunck){
                $data = array_map ('str_getcsv' , $chunck);


            //getting the header
                if($key == 0 ){

                    $header = $data[0];
                    unset($data[0]);
                }
            
                ProcessMigrationData::dispatch($data , $header);
            }
        }
    }
}
