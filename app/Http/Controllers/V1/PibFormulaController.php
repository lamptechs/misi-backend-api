<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;

use App\Http\Resources\PibFormulaResource;
use App\Models\PibFormula;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;



class PibFormulaController extends Controller
{

     /**
     * Get Current Table Model
     */
    private function getModel(){
        return new PibFormula();
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
            $pibformula = $this->getModel()->where("email", $request->email)->first();
            if( !Hash::check($request->password, $pibformula->password) ){
                return $this->apiOutput("Sorry! Password Dosen't Match", 401);
            }
            if( !$pibformula->status ){
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
            $this->data = PibFormulaResource::collection(PibFormula::all());
            $this->apiSuccess("PIB Loaded Successfully");
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

        // $validator = Validator::make($request->all(),[
        //     'patient_id' => 'required',
        //     'pib_name' => 'required',
        //     "document_number"     => ["required"],
        //     "create_by"     => ["required"]
        // ]);

        // if ($validator->fails()) {
        //     return $this->apiOutput($this->getValidationError($validator), 400);
        // }

        try{

            DB::beginTransaction();

            $data = $this->getModel();

            $data->patient_id = $request->patient_id;
            $data->pib_name = $request->pib_name;
            $data->type_of_legitimation = $request->type_of_legitimation;
            $data->document_number = $request->document_number;
            $data->identify_expire_date = $request->identify_expire_date ?? null;
            $data->patient_code = $request->patient_code;
            $data->create_by = $request->create_by;
            $data->ticket_id = $request->ticket_id;
            $data->deleted_by = $request->deleted_by ?? null;
            $data->deleted_date = $request->deleted_date ?? null ;
            $data->status = $request->status;
            $data->remarks = $request->remarks;
            $data->modified_by = $request->modified_by;
            $data->modified_date = $request->modified_date ?? null;
            $data->created_by = $request->created_by;
            $data->created_date = $request->created_date ?? null;

            $data->save();
            // $this->saveFileInfo($request, $data);

            DB::commit();
            $this->apiSuccess("PIB Info Added Successfully");
            $this->data = (new PibFormulaResource($data));
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

    // Save File Info
    // public function saveFileInfo($request, $data){
    //     $file_path = $this->uploadFile($request, 'file', $this->therapist_uploads, 720);

    //     if( !is_array($file_path) ){
    //         $file_path = (array) $file_path;
    //     }
    //     foreach($file_path as $path){
    //         $data = new TherapistUpload();
    //         $data->therapist_id = $therapist->id;
    //         $data->file_name    = $request->file_name ?? "Therapist Upload";
    //         $data->file_url     = $path;
    //         $data->save();
    //     }

    // }
            /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        try{
            $data = PibFormula::find($request->id);
            if( empty($data) ){
                return $this->apiOutput("Pib Data Not Found", 400);
            }
            $this->data = (new PibFormulaResource ($data));
            $this->apiSuccess("PIB Detail Show Successfully");
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

        // try{
        // $validator = Validator::make($request->all(),[
        //     //"id"  => ['required', "exists:therapists,id"],


        // ]);

        if ($validator->fails()) {
            return $this->apiOutput($this->getValidationError($validator), 400);
        }
            DB::beginTransaction();
            //$data = $this->getModel()->find($request->id);
            $data = PibFormula::find($request->id);


            $data->patient_id = $request->patient_id;
            $data->pib_name = $request->pib_name;
            $data->type_of_legitimation = $request->type_of_legitimation;
            $data->document_number = $request->document_number;
            $data->identify_expire_date = $request->identify_expire_date ?? null;
            $data->patient_code = $request->patient_code;
            $data->create_by = $request->create_by;
            $data->ticket_id = $request->ticket_id;
            $data->deleted_by = $request->deleted_by ?? null;
            $data->deleted_date = $request->deleted_date ?? null ;
            $data->status = $request->status;
            $data->remarks = $request->remarks;
            $data->modified_by = $request->modified_by;
            $data->modified_date = $request->modified_date ?? null;
            $data->created_by = $request->created_by;
            $data->created_date = $request->created_date ?? null;
            $data->save();
            //$this->updateFileInfo($request, $data);
            DB::commit();

            //try{
                // event(new Registered($data));
            //}catch(Exception $e){
                //
            //}

            $this->apiSuccess("PIB Info Updated Successfully");
            $this->data = (new PibFormulaResource($data));
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
            PibFormula::where('id',$data->id)->delete();
            $data->delete();
            $this->apiSuccess();
            return $this->apiOutput("PIB Deleted Successfully", 200);
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }

}
