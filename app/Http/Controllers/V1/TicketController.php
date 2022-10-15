<?php

namespace App\Http\Controllers\V1;

use Exception;
use App\Models\Ticket;
use Illuminate\Support\Str;
Use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\TicketHistoryActivity;
use App\Http\Resources\TicketResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\TicketHistoryActivityResource;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $this->data = TicketResource::collection(Ticket::all());
            $this->apiSuccess("Ticket Loaded Successfully");
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
           $validator = Validator::make( $request->all(),[
                'patient_id' => 'required',
                'therapist_id' => 'required'
            ]);

            if ($validator->fails()) {

                $this->apiOutput($this->getValidationError($validator), 200);
            }

            $ticket = new Ticket();
            //$ticket = Str::of(patient_id)->padRight(5);
            //Str::substr($testString, 6);
            //$ticket->patient_id = $request->Str::substr(patient_id,5);
            //$ticket->patient_id = Str::of($request->patient_id)->limit(5);
            //print_r(explode('-',$str,0));


            //$ticket->patient_id = $request->patient_id;
            //$patientidno = $ticket->patient_id;
            //$patientidsub = explode('-',$patientidno,0);
            //$ticket->patient_id = $patientidsub[0];
            $ticket->patient_id = $request->patient_id;
            $patientidno = $ticket->patient_id;
            $patientidsub = explode('-',$patientidno)[0];
            $ticket->patient_id = $patientidsub;
            //$ticket->patient_id = $request->patient_id;
            $ticket->therapist_id = $request->therapist_id ?? null;
            $ticket->ticket_department_id = $request->ticket_department_id;
            $ticket->location = $request->location ?? null;
            $ticket->language = $request->language ?? null;
            $ticket->date = /*$request->date*/ Carbon::now();
            $ticket->strike = $request->strike ?? null;
            $ticket->strike_history = $request->strike_history ?? null;
            $ticket->ticket_history = $request->ticket_history ?? null;
            $ticket->remarks = $request->remarks ?? null;
            $ticket->status = $request->status;
            $ticket->created_by = $request->user()->id ?? null;
            // $ticket->created_at = Carbon::Now();
            $ticket->save();

            $data= new Ticket();

           // $ticket_id=$data->ticket_id;
            $patient_id=$data->patient_id;
            $user_id=$data->$data->user()->id ?? null;
            $activity_message="new ticket test";

            //$data = $this->getModel();
            $data->ticket_id  = $request->ticket_id->orderBy("id", "DSC")->get() ;

            $lastTicketID = $data->ticket_id;

            $this->saveTicketHistory($lastTicketID, $patient_id,$user_id,$activity_message);


            $this->apiSuccess();
            $this->data = (new TicketResource($ticket));
            return $this->apiOutput();
        }catch(Exception $e){
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
            $ticket = Ticket::find($request->id);
            if( empty($ticket) ){
                return $this->apiOutput("Ticket Data Not Found", 400);
            }
            $this->data = (new TicketResource($ticket));
            $this->apiSuccess("Ticket Detail Show Successfully");
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
        $validator = Validator::make(
            $request->all(),[
               // "id"            => ["required", "exists:tickets,id"],
                //'patient_id'    => ['required'],
                //'therapist_id'  => ['required'],
                //"ticket_department_id" => ['required'],
                //"language"      => ['required', "string"],
                //"strike"        => ['required', "string"],
                //"status"        => ['required']

            ]);

           if ($validator->fails()) {
                $this->apiOutput($this->getValidationError($validator), 200);
           }

            $ticket = Ticket::find($request->id);

            $ticket_id=$ticket->ticket_id;
            $patient_id = $ticket->patient_id;
            $user_id = $ticket->user()->id ?? null;;
            //$activity_message = $ticket->activity_message;


            $ticket->patient_id = $request->patient_id;
            $ticket->therapist_id = $request->therapist_id ?? null;

            $ticket->ticket_department_id = $request->ticket_department_id;

            $ticket->location = $request->location ?? null;
            $ticket->language = $request->language ?? null;
            $ticket->date = now();
            $ticket->strike = $request->strike ?? null;
            $ticket->strike_history = $request->strike_history ?? null;
            $ticket->ticket_history = $request->ticket_history ?? null;
            $ticket->remarks = $request->remarks ?? null;
            $ticket->status = $request->status;
            $ticket->updated_by = $request->user()->id ?? null;
            // $ticket->updated_at = Carbon::Now();
            $ticket->save();

            if($ticket->location!=$request->location)
            {
                $activity_message= " Ticket Location " . $request->location. " has updated";
                $this->saveTicketHistory($ticket_id,$patient_id,$user_id,$activity_message);
            }


            if($activity_message != $ticket->activity_message)
            {
                $msg= " First Name Update";
                $this->saveActivity($request , $msg);
            }

            $this->apiSuccess("Ticket Info Updated successfully");
            $this->data = (new TicketResource($ticket));
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }

    public function assignedupdate(Request $request)
    {
        try{
        $validator = Validator::make(
            $request->all(),[
                "id"            => ["required", "exists:tickets,id"]
            ]);

           if ($validator->fails()) {
                $this->apiOutput($this->getValidationError($validator), 200);
           }
            $ticket = Ticket::find($request->id);
            $ticket->date = now();
            //$ticket->updated_by = $request->user()->id ?? null;
            $ticket->updated_by = $request->updated_by;
            $ticket->assign_to_user = $request->assign_to_user;
            $ticket->group_id=$request->group_id;
            $ticket->assign_to_user_status ="Hold";
            // $ticket->updated_at = Carbon::Now();
            $ticket->save();
            $this->apiSuccess("Assigned Ticket Info Updated successfully");
            $this->data = (new TicketResource($ticket));
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
        Ticket::destroy($id);
        $this->apiSuccess();
        return $this->apiOutput("Ticket Deleted Successfully", 200);
    }


    public function saveTicketHistory($ticket_id,$patient_id,$user_id,$activity_message){


            try{

                DB::beginTransaction();

                $data =new TicketHistoryActivity();
                $data->ticket_id  = $ticket_id ;
                $data->patient_id  = $patient_id;
                $data->user_id = $user_id;
                $data->date_time  = $activity_message;
                $data->save();

                // $this->saveFileInfo($request, $data);

                DB::commit();
                $this->apiSuccess("Ticket History Activity Info Added Successfully");
                $this->data = (new TicketHistoryActivityResource($data));
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
}
