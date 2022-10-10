<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;

use App\Http\Resources\TicketHistoryActivityResource;
use App\Models\TicketHistoryActivity;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;



class TicketHistoryActivityController extends Controller
{

     /**
     * Get Current Table Model
     */
    private function getModel(){
        return new TicketHistoryActivity();
    }

    /**
     * Show Login
     */
    public function showLogin(Request $request){
        $this->data = [
            "email"     => "required",
            "password"  => "required",
        ];
        $this->apiSuccess("This credentials are required for Login ");
        return $this->apiOutput();
    }

    /**
     * Login
     */
    public function login(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                "email"     => ["required"],
                "password"  => ["required"]
            ]);
            if($validator->fails()){
                return $this->apiOutput($this->getValidationError($validator), 400);
            }
            $TicketHistoryActivity = $this->getModel()->where("email", $request->email)->first();
            if( !Hash::check($request->password, $TicketHistoryActivity->password) ){
                return $this->apiOutput("Sorry! Password Dosen't Match", 401);
            }
            if( !$TicketHistoryActivity->status ){
                return $this->apiOutput("Sorry! your account is temporaly blocked", 401);
            }
            // Issueing Access Token
            // $this->access_token = $pibformula->createToken($request->ip() ?? "therapist_access_token")->plainTextToken;
            $this->apiSuccess("Login Successfully");
            return $this->apiOutput();

        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }
    public function logout(Request $request){
        $user = $request->user();
        foreach ($user->tokens as $token) {
            $token->delete();
       }
       $this->apiSuccess("Logout Successfull");
       return $this->apiOutput();

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $this->data = TicketHistoryActivityResource::collection(TicketHistoryActivity::all());
            $this->apiSuccess("Ticket History Activity Loaded Successfully");
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

            DB::beginTransaction();

            $data = $this->getModel();

            $data->ticket_id = $request->ticket_id;
            $data->assign_to_therapist = $request->assign_to_therapist;
            $data->appointment_group = $request->appointment_group ?? null;
            $data->call_strike = $request->call_strike;
            $data->strike_history = $request->strike_history;
            $data->ticket_history = $request->ticket_history;
            $data->status = $request->status;
            $data->language = $request->language;
            $data->assign_to_user = $request->assign_to_user;
            $data->assign_to_user_status = $request->assign_to_user_status;
            $data->deleted_by = $request->deleted_by ?? null;
            $data->deleted_date = $request->deleted_date ?? null ;
            $data->remarks = $request->remarks;
            $data->modified_by = $request->modified_by;
            $data->modified_date = $request->modified_date ?? null;
            $data->created_by = $request->created_by;
            $data->created_date = $request->created_date ?? null;
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



            /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        try{
            $data = TicketHistoryActivity::find($request->id);
            if( empty($data) ){
                return $this->apiOutput("Ticket History Activity Data Not Found", 400);
            }
            $this->data = (new TicketHistoryActivityResource($data));
            $this->apiSuccess("Ticket History Activity Detail Show Successfully");
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
    public function update(Request $request,$id)
    {

        try{
        $validator = Validator::make($request->all(),[
            //"id"  => ['required', "exists:therapists,id"],


        ]);

        if ($validator->fails()) {
            return $this->apiOutput($this->getValidationError($validator), 400);
        }
            DB::beginTransaction();
            //$data = $this->getModel()->find($request->id);
            $data = TicketHistoryActivity::find($request->id);

            $data->ticket_id = $request->ticket_id;
            $data->assign_to_therapist = $request->assign_to_therapist;
            $data->appointment_group = $request->appointment_group ?? null;
            $data->call_strike = $request->call_strike;
            $data->strike_history = $request->strike_history;
            $data->ticket_history = $request->ticket_history;
            $data->status = $request->status;
            $data->language = $request->language;
            $data->assign_to_user = $request->assign_to_user;
            $data->assign_to_user_status = $request->assign_to_user_status;
            $data->deleted_by = $request->deleted_by ?? null;
            $data->deleted_date = $request->deleted_date ?? null ;
            $data->remarks = $request->remarks;
            $data->modified_by = $request->modified_by;
            $data->modified_date = $request->modified_date ?? null;
            $data->created_by = $request->created_by;
            $data->created_date = $request->created_date ?? null;
            $data->save();
            //$this->updateFileInfo($request, $data);
            DB::commit();



            $this->apiSuccess("Ticket History Activity Info Updated Successfully");
            $this->data = (new TicketHistoryActivityResource($data));
            return $this->apiOutput();
        }
        catch(Exception $e){
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
            TicketHistoryActivity::where('id',$data->id)->delete();
            $data->delete();
            $this->apiSuccess();
            return $this->apiOutput("Ticket History Activity Deleted Successfully", 200);
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }

}
