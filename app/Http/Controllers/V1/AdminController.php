<?php


namespace App\Http\Controllers\V1;

use App\Events\AccountRegistration;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\RequestGuard;
use Illuminate\Support\Facades\Session;
use App\Http\Resources\AdminResource;
use App\Models\PasswordReset;

class AdminController extends Controller
{
    /**
     * Show Login
     */
    public function showLogin(Request $request){
        $this->data = [
            "email"     => "required",
            "password"  => "required",
        ];
        $this->apiSuccess("This credentials are required for Login ");
        return $this->apiOutput(200);
    }

    /**
     * Login 
     */
    public function login(Request $request){
        $admin=Admin::all();
        // return $group;
        try{
            $validator = Validator::make($request->all(), [
                "email"     => ["required", "email", "exists:admins,email"],
                "password"  => ["required", "string", "min:4", "max:40"]
            ]); 
            if($validator->fails()){
                return $this->apiOutput($this->getValidationError($validator), 400);
            }
            $admin = Admin::where("email", $request->email)->first();
            if( !Hash::check($request->password, $admin->password) ){
                return $this->apiOutput("Sorry! Password Dosen't Match", 401);
            }
            if( !$admin->status ){
                return $this->apiOutput("Sorry! your account is temporaly blocked", 401);
            }
            // Issueing Access Token
            $this->access_token = $admin->createToken($request->ip() ?? "admin_access_token")->plainTextToken;
            Session::put('access_token',$this->access_token);
            // echo Session::get('access_token');
            $this->apiSuccess("Login Successfully");
            $this->data = (new AdminResource($admin));
            return $this->apiOutput();

        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }
    public function logout(Request $request){
        $user = auth('sanctum')->user();
        // 
        foreach ($user->tokens as $token) {
            $token->delete();
       }
       $this->apiSuccess("Logout Successfull");
       return $this->apiOutput();
   
    }
    
     public function index()
    {
       
        try{
            $this->data = AdminResource::collection(Admin::all());
            $this->apiSuccess("Admin Load has been Successfully done");
            // return $this->apiOutput("Therapist Loaded Successfully",200);
            return $this->apiOutput();

        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }

    public function show(Request $request)
    {
        try{
            $admin = Admin::find($request->id);
            if( empty($admin) ){
                return $this->apiOutput("Admin Data Not Found", 400);
            }
            $this->data = (new AdminResource ($admin));
            $this->apiSuccess("Admin Detail Show Successfully");
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }
    
    public function store(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:4',
            ]);
            
            if ($validator->fails()) {
                $this->apiOutput($this->getValidationError($validator), 400);
            }
   
            $admin = new Admin();
            $admin->name = $request->name;
            $admin->bio = $request->bio;
            $admin->email = $request->email;
            $admin->group_id = $request->group_id;
            $admin->password = !empty($request->password) ? bcrypt($request->password) : $admin->password ;
            $admin->save();
            try{
                event(new AccountRegistration($admin));
            }catch(Exception $e){

            }
            $this->apiSuccess("Admin Added Successfully");
            $this->data = (new AdminResource($admin));
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }
    
    public function update(Request $request, $id)
    {
        try{
        $validator = Validator::make($request->all(),[
                'name' => 'required|min:4',
            ]);
            
           if ($validator->fails()) {    
            $this->apiOutput($this->getValidationError($validator), 400);
           }
   
            $admin = Admin::find($id);
            $admin->name = $request->name;
            $admin->bio = $request->bio;
            $admin->email = $request->email;
            $admin->group_id = $request->group_id;
            $admin->password = !empty($request->password) ? bcrypt($request->password) : $admin->password ;
            $admin->save();
            $this->apiSuccess("Admin Updated Successfully");
            $this->data = (new AdminResource($admin));
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError( $e), 500);
        }
    }
    
    public function destroy($id)
    {
        $admin = Admin::find($id);
        $admin->delete();
        $this->apiSuccess();
        return $this->apiOutput("Admin Deleted Successfully", 200);
    }

    /**
     * Forget Password
     */
    public function forgetPassword(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                "email"     => ["required", "exists:admins,email"],
            ],[
                "email.exists"  => "No Record found under this email",
            ]);

            if($validator->fails()){
                return $this->getValidationError($validator);
            }
            $admin = Admin::where("email", $request->email)->first();
            $token = rand(111111, 999999);

            $password_reset = new PasswordReset();
            $password_reset->tableable      = $admin->getMorphClass();
            $password_reset->tableable_id   = $admin->id;
            $password_reset->email          = $admin->email;
            $password_reset->token          = $token;
            $password_reset->expire_at      = now()->addHour();
            $password_reset->save();

            // Send Password Reset Email
            // event()
            
            $this->apiSuccess("Password Reset Code sent to your registared Email.");
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError($e));
        }
    }    
   
}
