<?php

namespace App\Http\Controllers\V1\Patient;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketReplyResource;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Models\TicketReply;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Components\Classes\Facade\ActivityLog;
use App\Http\Controllers\V1\Admin\PermissionController;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            if(!PermissionController::hasAccess("ticket_list")){
                return $this->apiOutput("Permission Missing", 403);
            }
            $validator = Validator::make( $request->all(),[
                "date"          => ["nullable", "date", "date_format:Y-m-d"],
                'therapist_id'  => ['nullable', "exists:therapists,id"],
            ]);
            
            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }
            
            $therapist = $request->user();
            $tickets = Ticket::where("patient_id", $therapist->id)
                ->orderBy("date", "DESC")->orderBy("id", "DESC");
            // Filter By Date
            if( !empty($request->date) ){
                $tickets->where("date", $request->date);
            }
            // Filter By Paitent
            if( !empty($request->therapist_id) ){
                $tickets->where("therapist_id", $request->therapist_id);
            }
            $tickets = $tickets->get();
            $this->data = TicketResource::collection($tickets)->hide(["replies", "created_by", "updated_by"]);
            $this->apiSuccess("Ticket Loaded Successfully");
            return $this->apiOutput();

        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        try{
           if(!PermissionController::hasAccess("ticket_create")){
                return $this->apiOutput("Permission Missing", 403);
            }
           $validator = Validator::make( $request->all(),[
                'therapist_id'    => ['nullable', "exists:therapists,id"],
                "ticket_department_id" => ["required"],
                "location"      => ["required", "string", "min:2"],
                "language"      => ["required", "string", "min:2"],
                "strike"        => ["required", "string"],
                "strike_history"=> ["nullable", "string"],
                "remarks"       => ["nullable", "string"],
                "ticket_history"=> ["nullable", "string"],
                "status"        => ["required", "boolean"],
                "file"          => ["nullable", "file"],
            ]);

            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }

            $patient = $request->user();
            $ticket = new Ticket();            
            $ticket->patient_id = $patient->id;
            $ticket->therapist_id = $request->therapist_id;
            $ticket->ticket_department_id = $request->ticket_department_id;
            $ticket->location = $request->location ?? null;
            $ticket->language = $request->language ?? null;
            $ticket->date = now()->format("Y-m-d");
            $ticket->strike = $request->strike ?? null;
            $ticket->strike_history = $request->strike_history ?? null;
            $ticket->ticket_history = $request->ticket_history ?? null;
            $ticket->remarks = $request->remarks ?? null;
            $ticket->status     = $request->status;
            $ticket->mono_multi_zd = $request->mono_multi_zd ?? null ;
            $ticket->mono_multi_screeing = $request->mono_multi_screeing ?? null;
            $ticket->intakes_therapist = $request->intakes_therapist ?? null;
            $ticket->tresonit_number = $request->tresonit_number ?? null;
            $ticket->datum_intake = $request->datum_intake ?? null;
            $ticket->datum_intake_2 = $request->datum_intake_2 ?? null;
            $ticket->nd_account = $request->nd_account?? null;
            $ticket->avc_alfmvm_sbg = $request->avc_alfmvm_sbg?? null;
            $ticket->honos= $request->honos?? null;
            $ticket->berha_intake=$request->berha_intake?? null;
            $ticket->rom_start=$request->rom_start?? null;
            $ticket->rom_end=$request->rom_end?? null;
            $ticket->berha_eind=$request->berha_eind?? null;
            $ticket->vtcb_date=$request->vtcb_date?? null;
            $ticket->closure=$request->closure?? null;
            $ticket->aanm_intake_1=$request->aanm_intake_1?? null;
            $ticket->assigned_to_user_name=$request->assigned_to_user_name?? null;
            $ticket->assigned_to_user_status=$request->assigned_to_user_status?? null;
            $ticket->file = $this->uploadFile($request, "file", $this->others_dir, null, null, $ticket->file);
            $ticket->save();
            ActivityLog::model($ticket)->user($request->user())->save($request, "Ticket Created Successfully");
            $this->apiSuccess("Ticket Create Successfully");
            $this->data = (new TicketResource($ticket))->hide(["ticket_department", "updated_by", "created_by"]);
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }

    /**
     * Display the specified resource.
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        try{
            if(!PermissionController::hasAccess("ticket_show")){
                return $this->apiOutput("Permission Missing", 403);
            }
            $validator = Validator::make( $request->all(),[
                'id'    => ['required', "exists:tickets,id"],
            ]);
            
            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }

            $patient = $request->user();
            $ticket = Ticket::where("id", $request->id)->where("patient_id", $patient->id)->first();
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
     */
    public function update(Request $request)
    {
        try{
            if(!PermissionController::hasAccess("ticket_update")){
                return $this->apiOutput("Permission Missing", 403);
            }
            $validator = Validator::make( $request->all(),[
                "id"            => ["required", "exists:tickets,id"],
                'therapist_id'    => ['nullable', "exists:therapists,id"],
                "location"      => ["nullable", "string"],
                "language"      => ["required", "string"],
                "strike"        => ["required", "string"],
                "strike_history"=> ["nullable", "string"],
                "remarks"       => ["nullable", "string"],
                "ticket_history"=> ["nullable", "string"],
                "status"        => ["required", "boolean"],
                "ticket_department_id" => ["required"],
                "file"          => ["nullable", "file"],
            ]);

            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }
    
            $patient = $request->user();
            $ticket = Ticket::where("id", $request->id)->where("patient_id", $patient->id)->first();
            $ticket->therapist_id = $request->therapist_id ?? null;
            
            if($ticket->ticket_department_id != $request->ticket_department_id)
            {
                $ticket->assigned_to_user_name = null;
                $ticket->assigned_to_user_status= null;
            }
            
            $ticket->location = $request->location ?? null;
            $ticket->language = $request->language ?? null;
            $ticket->date = now()->format("Y-m-d");
            $ticket->strike = $request->strike ?? null;
            $ticket->strike_history = $request->strike_history ?? null;
            $ticket->ticket_history = $request->ticket_history ?? null;
            $ticket->remarks = $request->remarks ?? null;
            $ticket->status     = $request->status;
            $ticket->updated_by = $request->user()->id ?? null;
            $ticket->mono_multi_zd = $request->mono_multi_zd ?? null ;
            $ticket->mono_multi_screeing = $request->mono_multi_screeing ?? null;
            $ticket->intakes_therapist = $request->intakes_therapist ?? null;
            $ticket->tresonit_number = $request->tresonit_number ?? null;
            $ticket->datum_intake = $request->datum_intake ?? null;
            $ticket->datum_intake_2 = $request->datum_intake_2 ?? null;
            $ticket->nd_account = $request->nd_account?? null;
            $ticket->avc_alfmvm_sbg = $request->avc_alfmvm_sbg?? null;
            $ticket->honos= $request->honos?? null;
            $ticket->berha_intake=$request->berha_intake?? null;
            $ticket->rom_start=$request->rom_start?? null;
            $ticket->rom_end=$request->rom_end?? null;
            $ticket->berha_eind=$request->berha_eind?? null;
            $ticket->vtcb_date=$request->vtcb_date?? null;
            $ticket->closure=$request->closure?? null;
            $ticket->aanm_intake_1=$request->aanm_intake_1?? null;
            $ticket->assigned_to_user_name=$request->assigned_to_user_name?? null;
            $ticket->assigned_to_user_status=$request->assigned_to_user_status?? null;
            $ticket->file = $this->uploadFile($request, "file", $this->others_dir, null, null, $ticket->file);
            $ticket->save();

            ActivityLog::model($ticket)->user($request->user())->save($request, "Ticket Updated Successfully");
            $this->apiSuccess("Ticket Info Updated successfully");
            $this->data = (new TicketResource($ticket))->hide(["replies", "created_by", "updated_by"]);
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }
     

    /**
     * Cancel Ticket
     */
    public function cancelticket(Request $request)
    {
        try{
            if(!PermissionController::hasAccess("cancelticket")){
                return $this->apiOutput("Permission Missing", 403);
            }    
        $validator = Validator::make(
            $request->all(),[
                "id"            => ["required", "exists:tickets,id"]
            ]);

           if ($validator->fails()) {
                $this->apiOutput($this->getValidationError($validator), 200);
           }
            $ticket = Ticket::find($request->id);
            $ticket->status =$request->status;
            $ticket->save();
            $this->apiSuccess("Ticket cancelled successfully");
            $this->data = (new TicketResource($ticket));
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }

    /**
     * Delete Ticket
     */
    public function deleteTicket(Request $request){
        try{
            if(!PermissionController::hasAccess("ticket_delete")){
                return $this->apiOutput("Permission Missing", 403);
            }
            $validator = Validator::make( $request->all(),[
                "id"            => ["required", "exists:tickets,id"],
            ]);

            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }
    
            $ticket = Ticket::where("id", $request->id)->first();
            $this->RemoveFile($this->$ticket);
            $ticket->delete();
            $this->apiSuccess("Ticket Deleted successfully");
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }

    

    /**
     * Ticket Reply List
     */
    public function replyList(Request $request){
        try{
            if(!PermissionController::hasAccess("ticket_replyList")){
                return $this->apiOutput("Permission Missing", 403);
            }
            $validator = Validator::make( $request->all(),[
                "ticket_id"     => ["required", "exists:tickets,id"],
            ]);

            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }
    
            $reply = TicketReply::where("ticket_id", $request->ticket_id)
                ->orderBy("created_at", "desc")->get();
            $this->data = TicketReplyResource::collection($reply)->hide(["created_by", "updated_by"]);
            $this->apiSuccess("Ticket reply loaded successfully");
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
          
    }


    /**
     * Add Ticket Reply
     */
    public function addReply(Request $request){
        try{
            if(!PermissionController::hasAccess("ticket_addReply")){
                return $this->apiOutput("Permission Missing", 403);
            }
            $validator = Validator::make( $request->all(),[
                "ticket_id" => ["required", "exists:tickets,id"],
                "comment"   => ["nullable", "string"],
                "file"      => ["nullable", "file"],
            ]);
    
            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }
            if( empty($request->comment) && !$request->hasFile('file') ){
                return $this->apiOutput("Comment or File Upload is required", 200);
            }
    
            $reply = new TicketReply();
            $reply->ticket_id = $request->ticket_id;
            $reply->comment = $request->comment;
            if($request->hasFile('file')){
                $reply->file = $this->uploadFile($request, "file", $this->others_dir);
            }
            $reply->save();

            $this->apiSuccess("Ticket reply added successfully");
            $this->data = (new TicketReplyResource($reply));
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }
    /**
     * Edit Reply
     */
    public function editReply(Request $request){
        try{
            if(!PermissionController::hasAccess("ticket_editReply")){
                return $this->apiOutput("Permission Missing", 403);
            }
            $validator = Validator::make( $request->all(),[
                "id"            => ["required", "exists:ticket_replies,id"],
                "ticket_id"     => ["required", "exists:tickets,id"],
            ]);

            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }
            $reply = TicketReply::where("id", $request->id)
                ->where("ticket_id", $request->ticket_id)
                ->first();
            $this->data = (new TicketReplyResource($reply));
            $this->apiSuccess("Ticket reply loaded successfully");
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }

    /**
     * Update Reply
     */
    public function updateReply(Request $request){
        try{
            if(!PermissionController::hasAccess("ticket_updateReply")){
                return $this->apiOutput("Permission Missing", 403);
            }
            $validator = Validator::make( $request->all(),[
                "id"            => ["required", "exists:ticket_replies,id"],
                "ticket_id" => ["required", "exists:tickets,id"],
                "comment"   => ["nullable", "string"],
                "file"      => ["nullable", "file"],
            ]);
    
            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }
            if( empty($request->comment) && $request->hasFile('file') ){
                $this->apiOutput("Comment or File Upload is required", 200);
            }
    
            $reply =  TicketReply::where("id", $request->id)->where("ticket_id", $request->ticket_id)->first();
            $reply->ticket_id = $request->ticket_id;
            $reply->comment = $request->comment;
            if($request->hasFile('file')){
                $reply->file = $this->uploadFile($request, "file", $this->others_dir, null, null, $reply->file);
            }
            $reply->save();

            $this->apiSuccess("Ticket reply Updated successfully");
            $this->data = (new TicketReplyResource($reply));
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }

    /**
     * Delete Reply
     */
    public function deleteReply(Request $request){
        try{
            if(!PermissionController::hasAccess("ticket_deleteReply")){
                return $this->apiOutput("Permission Missing", 403);
            }
            $validator = Validator::make( $request->all(),[
                "id"            => ["required", "exists:ticket_replies,id"],
                "ticket_id"     => ["required", "exists:tickets,id"],
            ]);

            if ($validator->fails()) {
                return $this->apiOutput($this->getValidationError($validator), 200);
            }
    
            TicketReply::where("id", $request->id)->where("ticket_id", $request->ticket_id)->delete();
            $this->apiSuccess("Ticket reply Deleted successfully");
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }
}
