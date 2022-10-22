<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmailTemplateResource;
use App\Models\EmailTemplate;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EmailController extends Controller
{
    protected $template_type = [
        "contact_mail"      => "Contact Email",
        "signup_mail"       => "Signuo Email",
    ];
    /**
     * Email Template List
     */
    public function index(){
        try{
            $templates = EmailTemplate::get();
            $this->data["template"] = EmailTemplateResource::collection($templates);
            $this->apiSuccess("Email Template loaded Successfully");
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }

    /**
     * Email Template Create
     * @return Available Template
     */
    public function create(){
        try{
            $template_type = EmailTemplate::select("email_type")->pluck("email_type")->toArray();
            $template_type = array_diff($this->template_type, $template_type);
            $this->data["types"] = $template_type;
            $this->apiSuccess("Template Type loaded Successfully");
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }

    /**
     * Store Template Design
     */
    public function store(Request $request){
        try{
            $template_type = $this->template_type;
            $validator = Validator::make($request->all(), [
                "email_type"    => ["required", "string", Rule::in(array_keys($template_type))],
                "mail_send"     => ["required", "boolean"],
                "cc"            => ["nullable", "string"],
                "template"      => ["nullable", "string"]
            ]);
            if($validator->fails()){
                return $this->apiOutput($this->getValidationError($validator));
            }
            
            $template = new EmailTemplate();
            $template->email_type   = $request->email_type;
            $template->mail_send    = $request->mail_send;
            $template->cc           = $request->cc;
            $template->template     = $request->template;
            $template->created_by   = $request->user()->id;
            $template->save();
           
            $this->data["email_templates"] = new EmailTemplateResource($template);
            $this->apiSuccess("Template Configuration Added Successfully");
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }

    /**
     * Update Template
     */
    public function update(Request $request){
        try{
            $template_type = $this->template_type;
            $validator = Validator::make($request->all(), [
                "id"            => ["required", "exists:email_templates,id"],
                "email_type"    => ["required", "string", Rule::in(array_keys($template_type))],
                "mail_send"     => ["required", "boolean"],
                "cc"            => ["nullable", "string"],
                "template"      => ["nullable", "string"]
            ]);
            if($validator->fails()){
                return $this->apiOutput($this->getValidationError($validator));
            }
            
            $template = EmailTemplate::find($request->id);
            $template->email_type   = $request->email_type;
            $template->mail_send    = $request->mail_send;
            $template->cc           = $request->cc;
            $template->template     = $request->template;
            $template->updated_by   = $request->user()->id;
            $template->save();
           
            $this->data["email_templates"] = new EmailTemplateResource($template);
            $this->apiSuccess("Template Configuration Updated Successfully");
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }

    /**
     * View Configuration
     */
    public function view(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                "id"            => ["required", "exists:email_templates,id"],
            ]);
            if($validator->fails()){
                return $this->apiOutput($this->getValidationError($validator));
            }
            
            $template = EmailTemplate::withTrashed()->find($request->id);
            $template_type_use = EmailTemplate::select("email_type")
                ->where("email_type", "!=", $template->email_type)->pluck("email_type")->toArray();
            $template_type = array_diff($this->template_type, $template_type_use);

            $this->data["types"] = $template_type;
            $this->data["email_templates"] = new EmailTemplateResource($template);
            $this->apiSuccess("Template Configuration loaded Successfully");
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }

    /**
     * Delete  Configuration
     */
    public function delete(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                "id"            => ["required", "exists:email_templates,id"],
            ]);
            if($validator->fails()){
                return $this->apiOutput($this->getValidationError($validator));
            }
            EmailTemplate::where("id", $request->id)->delete();            
            $this->apiSuccess("Template Configuration Deleted Successfully");
            return $this->apiOutput();
        }catch(Exception $e){
            return $this->apiOutput($this->getError($e), 500);
        }
    }
}
