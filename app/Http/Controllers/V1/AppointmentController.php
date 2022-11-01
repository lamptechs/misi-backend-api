<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Models\AppointmentUpload;
use App\Models\Appointmnet;
use App\Models\TherapistSchedule;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
     /**
     * Get Current Table Model
     */
    private function getModel(){
        return new Appointmnet();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                "date"                  => ["nullable", "date", "date_format:Y-m-d"],
                "therapist_id"          => ["nullable", "exists:therapists,id"],
                "patient_id"            => ["nullable", "exists:users,id"],
            ]);
            if($validator->fails()){
                return $this->apiOutput($this->getValidationError($validator), 400);
            }

            $appoinement = Appointmnet::orderBy('date', "ASC")->orderBy("start_time", "ASC");
            if( !empty($request->date) ){
                $appoinement->where("date", $request->date);
            }
            // else{
            //     $appoinement->where("date", ">=", now()->format('Y-m-d'));
            // }
            if( !empty($request->patient_id) ){
                $appoinement->where("patient_id", $request->patient_id);
            }
            if( !empty($request->therapist_id) ){
                $appoinement->where("therapist_id", $request->therapist_id);
            }
            $appoinement = $appoinement->get();
            $this->data = AppointmentResource::collection($appoinement);
            $this->apiSuccess("Appointment Load has been Successfully done");
            return $this->apiOutput();

        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                "therapist_id"  => ["required", "exists:therapists,id"],
                "patient_id"    => ["required", "exists:users,id"],
                "therapist_schedule_id" => ["required", "exists:therapist_schedules,id"],
                "status"        => ["required", "boolean"]
            ]);
            if($validator->fails()){
                return $this->apiOutput($this->getValidationError($validator), 400);
            }

            DB::beginTransaction();

            $schedule = TherapistSchedule::where("id", $request->therapist_schedule_id)->first();
            if($schedule->status != "open"){
                return $this->apiOutput("Sorry! This time slot has been booked. You can't book this schedule. please try again.", 400);
            }
            $schedule->status = "booked";
            $schedule->patient_id = $request->patient_id;
            $schedule->save();

            $data = $this->getModel();
            $data->created_by   = $request->user()->id;
            $data->therapist_id = $request->therapist_id;
            $data->patient_id   = $request->patient_id;
            $data->therapist_schedule_id = $request->therapist_schedule_id;
            $data->number       = $request->number;
            $data->history      = $request->history ?? null;
            $data->date         = $schedule->date;
            $data->start_time   = $schedule->start_time;
            $data->end_time     = $schedule->end_time;
            $data->fee          = $request->fee;
            $data->language     = $request->language;
            $data->type         = $request->type;
            $data->therapist_comment = $request->comment ?? null;
            $data->remarks      = $request->remarks ?? null;
            $data->status       = $request->status;
            if($request->hasFile('picture')){
                $data->image_url = $this->uploadFile($request, 'picture', $this->appointment_uploads, null,null,$data->image_url);
            }
            $data->save();
            $this->saveFileInfo($request, $data);
            
            DB::commit();
            $this->apiSuccess("Appointment Created Successfully");
            $this->data = (new AppointmentResource($data));
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
            DB::rollBack();
        }
    }

     // Save File Info
     public function saveFileInfo($request, $appointment){
        $file_path = $this->uploadFile($request, 'file', $this->appointment_uploads, 720);
  
        if( !is_array($file_path) ){
            $file_path = (array) $file_path;
        }
        foreach($file_path as $path){
            $data = new AppointmentUpload();
            $data->appointment_id = $appointment->id;
            $data->file_name    = $request->file_name ?? "Appointment Upload";
            $data->file_url     = $path;
            $data->save();
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
            $appointment = Appointmnet::find($request->id);
            if( empty($appointment) ){
                return $this->apiOutput("Appointment Data Not Found", 400);
            }
            $this->data = (new AppointmentResource ($appointment));
            $this->apiSuccess("Appointment Detail Show Successfully");
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }

   

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                "id"                    => ["required", "exists:appointmnets,id"],
                "therapist_id"  => ["required", "exists:therapists,id"],
                "patient_id"    => ["required", "exists:users,id"],
                "therapist_schedule_id" => ["required", "exists:therapist_schedules,id"],
                "status"        => ["required", "boolean"]
            ]);
            if($validator->fails()){
                return $this->apiOutput($this->getValidationError($validator), 400);
            }

            DB::beginTransaction();
            $appoinement = Appointmnet::find($request->id);
            if($appoinement->therapist_schedule_id != $request->therapist_schedule_id){
                $schedule = TherapistSchedule::where("id", $appoinement->therapist_schedule_id)
                    ->update(["status" => "open", "patient_id" => null]);
    
                $schedule->status = "open";
                $schedule->patient_id = null;
                $schedule->save();

                $schedule = TherapistSchedule::where("id", $request->therapist_schedule_id)->first();
                if($schedule->status != "open"){
                    return $this->apiOutput("Sorry! This time slot has been booked. You can't book this schedule. please try again.", 400);
                }
                $schedule->status = "booked";
                $schedule->patient_id = $request->patient_id;
                $schedule->save();
            }
           
            $appoinement->updated_by   = $request->user()->id;
            $appoinement->therapist_id = $request->therapist_id;
            $appoinement->patient_id   = $request->patient_id;
            $appoinement->therapist_schedule_id = $request->therapist_schedule_id;
            $appoinement->number       = $request->number;
            $appoinement->history      = $request->history ?? null;
            //$appoinement->date         = $request->date;
            //$appoinement->start_time   = $request->start_time;
            //$appoinement->end_time     = $request->end_time;
            $appoinement->fee          = $request->fee;
            $appoinement->language     = $request->language;
            $appoinement->type         = $request->type;
            $appoinement->therapist_comment = $request->comment ?? null;
            $appoinement->remarks      = $request->remarks ?? null;
            $appoinement->status       = $request->status;
            if($request->hasFile('picture')){
                $appoinement->image_url = $this->uploadFile($request, 'picture', $this->appointment_uploads, null,null,$appoinement->image_url);
            }
            $appoinement->save();
            $this->saveFileInfo($request, $appoinement);
        
            DB::commit();
            $this->apiSuccess("Appointment Updated Successfully");
            $this->data = (new AppointmentResource($appoinement));
            return $this->apiOutput();

        }catch(Exception $e){
            DB::rollBack();
            return $this->apiOutput($this->getError( $e), 500);
        }

    }

    public function assignedappointmentticketstatus(Request $request)
    {
    
        try{
        $validator = Validator::make(
            $request->all(),[
                "id"            => ["required", "exists:appointmnets,id"]
            ]);
            
           if ($validator->fails()) {    
                $this->apiOutput($this->getValidationError($validator), 200);
           }
            $ticket = Appointmnet::find($request->id);
            $ticket->appointment_ticket_status ="Cancelled";
            $ticket->cancel_appointment_type=$request->cancel_appointment_type;
            $ticket->cancel_reason=$request->cancel_reason;
            $ticket->save();
            $this->apiSuccess("Assigned Ticket Cancelled successfully");
            $this->data = (new AppointmentResource($ticket));
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $data = $this->getModel()->find($id);
            $data->delete();
            $this->apiSuccess();
            return $this->apiOutput("Appointment Deleted Successfully", 200);
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }
}
