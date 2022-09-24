<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointmnet;
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
        try{
            DB::beginTransaction();

            $data = $this->getModel();
            $data->created_by = $request->user()->id;

            $data->therapist_id = $request->therapist_id;
            $data->patient_id   = $request->patient_id;
            $data->therapist_schedule_id = $request->therapist_schedule_id;
            $data->number = $request->number;
            $data->history = $request->history ?? null;
            //$data->date = Carbon::now();
            $data->date = $request->date;
            $data->time = $request->time;
            //$data->appointment_date = $request->appointment_date;
            //$data->appointment_time = $request->appointment_time;
            $data->fee = $request->fee;
            $data->language = $request->language;
            $data->type = $request->type;
            $data->therapist_comment = $request->comment ?? null;
            $data->remarks = $request->remarks ?? null;
            $data->status = $request->status;
            $data->save();
            DB::commit();
            $this->apiSuccess("Appointment Created Successfully");
            $this->data = (new AppointmentResource($data));
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
            DB::rollBack();
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

    // public function edit(Request $request){
    //     try{
    //         $appointment =Appointmnet:: find($request->id);
    //         if( empty($appointment) ){
    //             return $this->apiOutput("Appointment Data Not Found", 400);
    //         }
    //         $this->data = (new Appointmnet($appointment));
    //         $this->apiSuccess("Appointment Detail Show Successfully");
    //         return $this->apiOutput();
    //     }catch(Exception $e){
    //         return $this->apiOutput($this->getError($e), 500);
    //     }
    // }

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

        $temp= Appointmnet::find($id);
        return $temp;
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
