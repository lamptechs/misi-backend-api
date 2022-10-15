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

            // $data->ticket_id = $request->ticket_id;
            // $data->assign_to_therapist = $request->assign_to_therapist;
            // $data->appointment_group = $request->appointment_group ?? null;
            // $data->call_strike = $request->call_strike;
            // $data->strike_history = $request->strike_history;
            // $data->ticket_history = $request->ticket_history;
            // $data->status = $request->status;
            // $data->language = $request->language;
            // $data->assign_to_user = $request->assign_to_user;
            // $data->assign_to_user_status = $request->assign_to_user_status;
            // $data->deleted_by = $request->deleted_by ?? null;
            // $data->deleted_date = $request->deleted_date ?? null ;
            // $data->remarks = $request->remarks;
            // $data->modified_by = $request->modified_by;
            // $data->modified_date = $request->modified_date ?? null;
            // $data->created_by = $request->created_by;
            // $data->created_date = $request->created_date ?? null;

            $ticket_id = $data->ticket_id;
            $patient_id = $data->patient_id;
            $user_id = $data->user_id ;
            $activity_message =$data->activity_message ;
            $date_time =$data->date_time ;
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

            $ticket_id = $data->ticket_id;
            $patient_id = $data->patient_id;
            $user_id = $data->user_id ;
            $activity_message =$data->activity_message ;
            $date_time =$data->date_time ;

            $data->ticket_id = $request->ticket_id;
            $data->patient_id = $request->patient_id;
            $data->user_id = $request->user_id;
            $data->activity_message = $request->activity_message;
            $data->date_time = $request->date_time;
            $data->save();

            if($activity_message != $data->activity_message)
            {
                $msg= $data->first_name.' '.$data->last_name. " First Name Update";
                $this->saveActivity($request , $msg);
            }




            // $data->ticket_id = $request->ticket_id;
            // $data->assign_to_therapist = $request->assign_to_therapist;
            // $data->appointment_group = $request->appointment_group ?? null;
            // $data->call_strike = $request->call_strike;
            // $data->strike_history = $request->strike_history;
            // $data->ticket_history = $request->ticket_history;
            // $data->status = $request->status;
            // $data->language = $request->language;
            // $data->assign_to_user = $request->assign_to_user;
            // $data->assign_to_user_status = $request->assign_to_user_status;
            // $data->deleted_by = $request->deleted_by ?? null;
            // $data->deleted_date = $request->deleted_date ?? null ;
            // $data->remarks = $request->remarks;
            // $data->modified_by = $request->modified_by;
            // $data->modified_date = $request->modified_date ?? null;
            // $data->created_by = $request->created_by;
            // $data->created_date = $request->created_date ?? null;
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



    // /**
    //  * Update Advisor Profile
    //  */
    // public function updateProfile(Request $request){

    //     //New Error Validation
    //      $validator_data = [
    //             "profession_id"     => ['required', 'numeric', 'min:1'],
    //             'first_name'        => ['required', 'string', 'max:191'],
    //             'last_name'         => ['nullable', 'string', 'max:191'],
    //             /*"phone"             => ['required', 'string', "min:8", "max:13"],
    //             'telephone'         => ['nullable', 'string', "min:8", "max:13"],*/
    //             "personal_fca_number"=> ['nullable', 'string', "min:2", "max:30"],
    //             /*"fca_status_date"   => ['nullable', 'date'],*/

    //             "advisor_type_id.*" => ["required", "numeric"],
    //             "address_line_one"  => ['required', 'string', 'min:4', 'max:191'],
    //             "address_line_two"  => ['nullable', 'string', 'min:4', 'max:191'],
    //             "town"              => ['required', 'string', 'min:2', 'max:191'],
    //             "post_code"         => ['required', 'string', 'min:4', 'max:8'],
    //             "country"           => ['required', 'string', 'min:2', 'max:100'],

    //             "service_offered_id.*"          => ["required", "numeric", "min:1"],
    //             /*'primary_region_id'             => ["nullable", "numeric", "min:1"],*/
    //             "location_postcode_id.*"        => ["nullable", "numeric"],
    //         ];
    //         Validator::make($request->all(), $validator_data,[
    //             "first_name.min"        => "First name must be at least 2 characters",
    //             "last_name.min"         => "Last name must be at least 2 characters",
    //             "phone.min"             => "Phone number must be at least 8 characters",
    //             //"telephone.min"         => "Telephone number must be at least 8 characters",
    //             "address_line_one.min"  => "Error! Address line 1 must be at least 4 characters",
    //             "address_line_two.min"  => "Error! Address line 2 must be at least 4 characters",
    //             "town.min"              => "Error! Town must be at least 2 characters",
    //             "post_code.min"         => "Error! Postcode must be at least 4 characters",
    //             "country.min"           => "Error! County must be at least 2 characters",
    //             //"firm_name.min"         => "Firm name must be at least 2 characters",
    //             //"firm_fca_number.min"   => "Firm FCA number must be at least 2 characters ",

    //             //"firm_website_address.min"  => "Firm website address must be at least 2 characters",
    //             "personal_fca_number.min"   => "Error! Personal FCA number must be at least 2 characters",
    //             //"linkedin_id.min"           => "Linkedin ID must be at least 2 characters",
    //         ])->validate();


    //     /*try{
    //         $validator = Validator::make($request->all(),[
    //             "profession_id"     => ['required', 'numeric', 'min:1'],
    //             'first_name'        => ['required', 'string', 'max:191'],
    //             'last_name'         => ['nullable', 'string', 'max:191'],
    //             //"phone"             => ['required', 'string', "min:8", "max:13"],
    //             //'telephone'         => ['nullable', 'string', "min:8", "max:13"],
    //             "personal_fca_number"=> ['nullable', 'string', "min:2", "max:30"],
    //             //"fca_status_date"   => ['nullable', 'date'],

    //             "advisor_type_id.*" => ["required", "numeric"],
    //             "address_line_one"  => ['required', 'string', 'min:4', 'max:191'],
    //             "address_line_two"  => ['nullable', 'string', 'min:4', 'max:191'],
    //             "town"              => ['required', 'string', 'min:2', 'max:191'],
    //             "post_code"         => ['required', 'string', 'min:4', 'max:8'],
    //             "country"           => ['required', 'string', 'min:2', 'max:100'],

    //             "service_offered_id.*"          => ["required", "numeric", "min:1"],
    //             //'primary_region_id'             => ["nullable", "numeric", "min:1"],
    //             "location_postcode_id.*"        => ["nullable", "numeric"],
    //         ]);
    //         if($validator->fails()){
    //             return back()->with('error', $validator->errors()->first());
    //         }*/


    //         /*$response =  (new Fetchify())->isValidPhone($request->phone);
    //         if( !$response["status"] ){
    //             return back()->withInput()->withErrors( ["phone" => $response["message"] ]);
    //         }*/
    //         try{
    //         $response =  (new Fetchify())->isValidPostCode($request->post_code);
    //         if( !$response["status"] ){
    //             return back()->withInput()->withErrors( ["post_code" => $response["message"] ]);
    //         }

    //         $data = User::find(Auth::user()->id);
    //         $profile_brief = $data->profile_brief;
    //         $first_name=$data->first_name;
    //         $last_name = $data->last_name;
    //         $postcode = $data->post_code;
    //         $fund     = $data->fund_size_id;
    //         $service  = $data->service_offered_id;
    //         $live     = $data->is_live;
    //         $location_postcode_id = $data->location_postcode_id;
    //         $addressline1 = $data->address_line_one;
    //         $addressline2 = $data->address_line_two;
    //         $personalfcano = $data->personal_fca_number;
    //         $town = $data->town;
    //         $country = $data->country;
    //         $longitude=$data->longitude;
    //         $latitude=$data->latitude;


    //         $data->profession_id = $request->profession_id;
    //         $data->first_name = $request->first_name;
    //         $data->last_name = $request->last_name;
    //         /*$data->phone = $request->phone;
    //         $data->telephone = $request->telephone;*/
    //         $data->personal_fca_number = $request->personal_fca_number;
    //         //$data->fca_status_date = $request->fca_status_date;
    //         $data->address_line_one = $request->address_line_one;
    //         $data->address_line_two = $request->address_line_two;
    //         $data->post_code = $request->post_code;
    //         $data->longitude = $request->longitude ;
    //         $data->latitude  = $request->latitude;
    //         $data->town = $request->town;
    //         $data->country = $request->country;
    //         $data->fund_size_id = $request->fund_size_id;
    //         /*$data->primary_region_id = $request->primary_region_id;*/
    //         $data->profile_brief = $request->profile_brief;
    //         $data->advisor_type_id = $request->advisor_type_id;
    //         $data->service_offered_id = $request->service_offered_id;
    //         $data->location_postcode_id = $request->location_postcode_id;
    //         $data->is_live          = $request->is_live;
    //         $data->image = $this->uploadImage($request, 'image', $this->advisor_image, null, null, $data->image);
    //         $data->save();
    //         //$this->saveActivity($request, "Update Profile Information");

    //         //Specific Input Field Activity

    //         if($first_name != $data->first_name)
    //         {
    //             $msg= $data->first_name.' '.$data->last_name. " First Name Update";
    //             $this->saveActivity($request , $msg);
    //         }
    //         if($last_name != $data->last_name)
    //         {
    //             $msg= $data->first_name.' '.$data->last_name. " Last Name Update";
    //             $this->saveActivity($request , $msg);
    //         }

    //         if($addressline1 != $data->address_line_one)
    //         {
    //             $msg= $data->first_name.' '.$data->last_name. " address line 1 updated";
    //             $this->saveActivity($request , $msg);
    //         }
    //         if($addressline2 != $data->address_line_two)
    //         {
    //             $msg= $data->first_name.' '.$data->last_name. " address line 2 updated";
    //             $this->saveActivity($request , $msg);
    //         }
    //         if($personalfcano != $data->personal_fca_number)
    //         {
    //             $msg = $data->first_name.' '.$data->last_name. " personal fca number updated";
    //             $this->saveActivity($request , $msg);
    //         }

    //         if($postcode != $data->post_code){

    //             $msg= $data->first_name.' '.$data->last_name. " Postcode updated";
    //             $this->saveActivity($request , $msg);
    //         }


    //         if($fund != $data->fund_size_id){
    //             $msg= $data->first_name.' '.$data->last_name. " Fund value updated";
    //             $this->saveActivity($request , $msg);
    //         }

    //         if($service  != $data->service_offered_id){
    //             $msg= $data->first_name.' '.$data->last_name. " Areas of advice updated";
    //             $this->saveActivity($request , $msg);
    //         }

    //         if($live == $data->is_live){

    //         }
    //         else{
    //             /*$msg= "Account status active or paused";
    //             $this->saveActivity($request , $msg);*/
    //              if($data->is_live == 1){
    //                 $msg= $data->first_name.' '.$data->last_name. " Account status active";
    //                 $this->saveActivity($request , $msg);
    //             }
    //             else{
    //                 $msg= $data->first_name.' '.$data->last_name. " Account status pause";
    //                 $this->saveActivity($request , $msg);
    //             }
    //         }
    //         if($location_postcode_id != $data->location_postcode_id){
    //             $msg= $data->first_name.' '.$data->last_name. "Post code area covered updated";
    //             $this->saveActivity($request , $msg);
    //         }

    //         if($profile_brief != $data->profile_brief){
    //             $msg= $data->first_name.' '.$data->last_name. " About me updated";
    //             $this->saveActivity($request , $msg);
    //         }

    //         if($town != $data->town){
    //             $msg= $data->first_name.' '.$data->last_name. " Town updated";
    //             $this->saveActivity($request , $msg);
    //         }

    //         if($country != $data->country){
    //             $msg= $data->first_name.' '.$data->last_name. " County updated";
    //             $this->saveActivity($request , $msg);
    //         }
    //         if($latitude != $data->latitude){
    //             $msg= $data->first_name.' '.$data->last_name. " latitude updated";
    //             $this->saveActivity($request , $msg);
    //         }

    //         if($longitude != $data->longitude){
    //             $msg= $data->first_name.' '.$data->last_name. " Longitude updated";
    //             $this->saveActivity($request , $msg);
    //         }



    //         return back()->with("success","Advisor basic information updated successfully");
    //     }catch(Exception $e){
    //         return back()->with('error', $this->getError($e));
    //     }
    // }

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
