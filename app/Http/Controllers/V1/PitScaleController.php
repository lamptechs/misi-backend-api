<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PitScaleResource;
use App\Models\PitScale;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PitScaleController extends Controller
{


     /**
     * Get Current Table Model
     */
    private function getModel(){
        return new PitScale();
    }

    /**
     * Show Login
     */
    // public function showLogin(Request $request){
    //     $this->data = [
    //         "email"     => "required",
    //         "password"  => "required",
    //     ];
    //     $this->apiSuccess("This credentials are required for Login ");
    //     return $this->apiOutput();
    // }

    /**
     * Login
     */
    // public function login(Request $request){
    //     try{
    //         $validator = Validator::make($request->all(), [
    //             "email"     => ["required"],
    //             "password"  => ["required"]
    //         ]);
    //         if($validator->fails()){
    //             return $this->apiOutput($this->getValidationError($validator), 400);
    //         }
    //         $pibscale = $this->getModel()->where("email", $request->email)->first();
    //         if( !Hash::check($request->password, $pibscale->password) ){
    //             return $this->apiOutput("Sorry! Password Dosen't Match", 401);
    //         }
    //         if( !$pibscale->status ){
    //             return $this->apiOutput("Sorry! your account is temporaly blocked", 401);
    //         }
    //         // Issueing Access Token
    //         // $this->access_token = $pibformula->createToken($request->ip() ?? "therapist_access_token")->plainTextToken;
    //         $this->apiSuccess("Login Successfully");
    //         return $this->apiOutput();

    //     }catch(Exception $e){
    //         return $this->apiOutput($this->getError($e), 500);
    //     }
    // }
    // public function logout(Request $request){
    //     $user = $request->user();
    //     foreach ($user->tokens as $token) {
    //         $token->delete();
    //    }
    //    $this->apiSuccess("Logout Successfull");
    //    return $this->apiOutput();

    // }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $this->data = PitScaleResource::collection(PitScale::all());
            $this->apiSuccess("PIT Scale Loaded Successfully");
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
            $data->pit_formula_id = $request->pit_formula_id;
            $data->question_id = $request->question_id;
            $data->scale_value = $request->scale_value;
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
            $this->apiSuccess("PIT Scale Info Added Successfully");
            $this->data = (new PitScaleResource($data));
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
    //     $file_path = $this->uploadImage($request, 'file', $this->therapist_uploads, 720);

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
            $data = PitScale::find($request->id);
            if( empty($data) ){
                return $this->apiOutput("PIT Scale Data Not Found", 400);
            }
            $this->data = (new PitScaleResource($data));
            $this->apiSuccess("PIT Scale Detail Show Successfully");
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
            $data = PitScale::find($request->id);


            $data->patient_id = $request->patient_id;
            $data->pit_formula_id = $request->pit_formula_id;
            $data->question_id = $request->question_id;
            $data->scale_value = $request->scale_value;
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

            $this->apiSuccess("PIT Info Updated Successfully");
            $this->data = (new PitScaleResource($data));
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
            PitScale::where('id',$data->id)->delete();
            $data->delete();
            $this->apiSuccess();
            return $this->apiOutput("PIT Scale Deleted Successfully", 200);
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }
}
