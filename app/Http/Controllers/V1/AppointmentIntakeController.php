<?php

namespace App\Http\Controllers\V1;

use Exception;
use App\Models\Intake;
use Illuminate\Http\Request;
use App\Models\AppointmentIntake;
use App\Http\Controllers\Controller;
use App\Http\Resources\IntakeResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\V1\Admin\PermissionController;

class AppointmentIntakeController extends Controller
{
    public function index(){
        try{
            if(!PermissionController::hasAccess("intakeappointment_list")){
                return $this->apiOutput("Permission Missing", 403);
            }

            $this->data=IntakeResource::collection(AppointmentIntake::all());
            $this->apiSuccess("Appointment Intake Loaded Successfully");
            return $this->apiOutput();

        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }


    public function store(Request $request)
    {

        try{
            $validator = Validator::make($request->all(), [
                "appointment_id" => ["required", "exists:appointmnets,id"],
            ]);

           if ($validator->fails()) {

            $this->apiOutput($this->getValidationError($validator), 200);
           }

            $intake = new AppointmentIntake();
            $intake->appointment_id = $request->appointment_id;
            $intake->intake_date = $request->intake_date;
            $intake->intake_number=$request->intake_number;
            $intake->save();
            $this->apiSuccess();
            $this->data = (new IntakeResource($intake));
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }


    public function show(Request $request)
    {
        try{
            $intakeshow = AppointmentIntake::find($request->id);
            if( empty($intakeshow) ){
                return $this->apiOutput("Appointment Data Not Found", 400);
            }
            $this->data = (new IntakeResource ($intakeshow));
            $this->apiSuccess("Intake Detail Show Successfully");
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }


    public function update(Request $request)
    {
        try{

            if(!PermissionController::hasAccess("intakeappointment_update")){
                return $this->apiOutput("Permission Missing", 403);
            }

        $validator = Validator::make(
            $request->all(),
            [
                "appointment_id" => ["required", "exists:appointmnets,id"],

            ]
           );

           if ($validator->fails()) {
            $this->apiOutput($this->getValidationError($validator), 200);
           }

            $intake = AppointmentIntake::find($request->id);
            $intake->appointment_id = $request->appointment_id;
            $intake->intake_date = $request->intake_date;
            $intake->intake_number=$request->intake_number;
            $intake->save();
            $this->apiSuccess();
            $this->data = (new IntakeResource($intake));
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);

        }
    }


    public function destroy(Request $request)
    {
        try{
            if(!PermissionController::hasAccess("intakeappointment_delete")){
                return $this->apiOutput("Permission Missing", 403);
            }
            AppointmentIntake::where("id", $request->id)->delete();
            $this->apiSuccess();
            return $this->apiOutput("Appointment Intake Deleted Successfully", 200);
        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }
}
