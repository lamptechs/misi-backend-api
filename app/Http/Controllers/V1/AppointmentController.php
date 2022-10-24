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
    public function index()
    {
        try{
            $this->data = AppointmentResource::collection(Appointmnet::all());
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
            ]);
            if($validator->fails()){
                return $this->apiOutput($this->getValidationError($validator), 400);
            }

            DB::beginTransaction();

            $schedule = TherapistSchedule::where("id", $request->therapist_schedule_id)->first();
            if($schedule->status != "open"){
                return $this->apiOutput("Sorry! You can't book this schedule. please try again.", 400);
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
    public function update(Request $request, $id)
    {
        // $temp= Appointmnet::all();
        // return $temp;

        // $temp= Appointmnet::find($id);
        // return $temp;
        // return 10;

        try{
        $validator = Validator::make($request->all(),[
            "id"            => ["required", "exists:appointmnets,id"],
            'therapist_id'  => ['required', "exists:therapists,id"],
            "patient_id"    => ['required', "exists:users,id"],
        ]);

        if ($validator->fails()) {
            return $this->apiOutput($this->getValidationError($validator), 400);
        }

            DB::beginTransaction();

            $data = Appointmnet::find($request->id);
            //$data = $this->getModel()->find($id);
            $data->updated_by = $request->user()->id;

            $data->therapist_id = $request->therapist_id;
            $data->patient_id   = $request->patient_id;
            $data->therapist_schedule_id = $request->therapist_schedule_id;
            $data->number = $request->number;
            $data->history = $request->history ?? null;
            // $data->date = Carbon::createFromFormat($dformat, $date);
            // $data->time = Carbon::createFromFormat($tformat, $time);
            //$data->date = Carbon::now();
            // $data->time = Carbon::now();
            $data->date = $request->date;
            $data->time = $request->time;
            $data->fee = $request->fee;
            $data->language = $request->language;
            $data->type = $request->type;
            $data->therapist_comment = $request->comment ?? null;
            $data->remarks = $request->remarks ?? null;
            $data->status = $request->status;
            $data->created_by = $request->created_by;
            $data->deleted_at = $request->deleted_at;
            $data->save();

            DB::commit();
            $this->apiSuccess("Appointment Updated Successfully");
            $this->data = (new AppointmentResource($data));
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
