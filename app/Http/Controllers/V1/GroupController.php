<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Http\Resources\GroupResource;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       try{
            $this->data = GroupResource::collection(Group::all());
            $this->apiSuccess("Grouop Loaded Successfully");
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
        $validator = Validator::make(
            $request->all(),
            [
                // 'name' => 'required|min:4',
                // 'description' => 'required|min:4',
    
            ]
           );
            
           if ($validator->fails()) {
    
            $this->apiOutput($this->getValidationError($validator), 400);
           }
   
            $group = new Group();
            $group->name = $request->name ;
            $group->description = $request->description;
            // $group->is_admin = $request->is_admin;
            $group->created_by = $request->user()->id ;
            $group->created_at = Carbon::Now();
            $group->save();
            $this->apiSuccess();
            $this->data = (new GroupResource($group));
            return $this->apiOutput("Group Added Successfully");
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
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
         try{
        $validator = Validator::make(
            $request->all(),
            [
                // 'name' => 'required|min:4',
                // 'description' => 'required|min:4',
    
            ]
           );
            
           if ($validator->fails()) {
    
            $this->apiOutput($this->getValidationError($validator), 400);
           }
   
            $group = Group::find($id);
            $group->name = $request->name ;
            $group->description = $request->description;
            // $group->is_admin = $request->is_admin;
            $group->created_by = $request->user()->id ;
            $group->created_at = Carbon::Now();
            $group->save();
            $this->apiSuccess();
            $this->data = (new GroupResource($group));
            return $this->apiOutput("Group Updated Successfully");
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
        $group = Group::find($id);
        $group->delete();
        $this->apiSuccess();
        return $this->apiOutput("Group Deleted Successfully", 200);
    }
}
