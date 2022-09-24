<?php

namespace App\Http\Controllers\V1\Therapist;

use App\Http\Controllers\Controller;
use App\Http\Resources\TherapistResource;
use App\Http\Resources\TherapistScheduleResource;
use App\Models\Therapist;
//use App\Http\Resources\TherapistServiceResource;
use App\Models\TherapistSchedule;
//use App\Models\TherapistService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TherapistScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $this->data = TherapistScheduleResource::collection(TherapistSchedule::all());
            $this->apiSuccess("Therapist Schedule Loaded Successfully");
            // return $this->apiOutput("Therapist Loaded Successfully",200);
            return $this->apiOutput();

        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   
    public function store(Request $request)
    {
      //return 10;
      
        $validator = Validator::make($request->all(),[
            'first_name' => 'required',
            'last_name' => 'required',
            "email"     => ["required", "email", "unique:therapists"],
            "phone"     => ["required", "numeric", "unique:therapists"]
        ]);
            
        if ($validator->fails()) {
            return $this->apiOutput($this->getValidationError($validator), 400);
        }

        try{

            DB::beginTransaction();
            
            $data = $this->getModel();
            $data->created_by = $request->user()->id;

            $data->first_name = $request->first_name;                  
            $data->last_name = $request->last_name;         
            $data->email = $request->email;
            $data->phone = $request->phone;
            $data->address = $request->address;
            $data->language = $request->language;
            $data->bsn_number = $request->bsn_number;
            $data->dob_number = $request->dob_number;
            $data->insurance_number = $request->insurance_number;
            $data->emergency_contact = $request->emergency_contact ?? 0;
            $data->gender = $request->gender;
            //$data->date_of_birth = /*$request->date_of_birth*/ Carbon::now();
            $data->date_of_birth = $request->date_of_birth;
            $data->status = $request->status;
            $data->therapist_type_id = $request->therapist_type_id;
            $data->blood_group_id = $request->blood_group_id;
            $data->state_id = $request->state_id;
            $data->country_id = $request->country_id;
            $data->password = bcrypt($request->password);
            
            $data->save();
            $this->saveFileInfo($request, $data);
            
            DB::commit();
            $this->apiSuccess("Therapist Info Added Successfully");
            $this->data = (new TherapistResource($data));
            return $this->apiOutput();        
            try{
                // event(new Registered($data));
            }catch(Exception $e){
                //
            }
        }
        catch(Exception $e){
            DB::rollBack();
            return $this->apiOutput($this->getError( $e), 500);
        }                
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        try{
            $therapist = TherapistSchedule::find($request->id);
            if( empty($therapist) ){
                return $this->apiOutput("Therapist Data Not Found", 400);
            }
            $this->data = (new TherapistScheduleResource ($therapist));
            $this->apiSuccess("Therapist Detail Show Successfully");
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
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
        //
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
    }
}
