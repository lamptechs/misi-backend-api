<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PibFormulaResource;
use App\Http\Resources\PibResource;
use App\Models\PibFormula;
use App\Models\Scale;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PibFormulaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $this->data = PibFormulaResource::collection(PibFormula::all());
            $this->apiSuccess("PiB Formula Loaded Successfully");
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
                    'name' => 'required',
        
                ]
               );
                
               if ($validator->fails()) {
        
                $this->apiOutput($this->getValidationError($validator), 200);
               }
           
                    $formula = new PibFormula();
                    $formula->name = $request->name;
                    $formula->patient_id = $request->patient_id;
                    $formula->type = $request->type;
                    $formula->number = $request->number;
                    $formula->expiration_date = /*$request->expiration_date*/ Carbon::now();
                    $formula->created_by = $request->user()->id ?? null;
                    $formula->save();
               
                    $this->saveScale($request,$formula);

                $this->apiSuccess();
                $this->data = (new PibFormulaResource($formula));
                return $this->apiOutput();
            }catch(Exception $e){
                return $this->apiOutput($this->getError( $e), 500);
            }
    }

    /**
     * Save question and scale Info
     */
    public function saveScale($request, $formula){

        $questions = $request->question;

        foreach($questions as $key=>$value){
            $data = new Scale();
            $data->pib_id = $formula->id;
            $data->patient_id = $request->patient_id;
            $data->question_id = $request->question[$key] ?? null;
            $data->scale = $request->scale[$key] ?? null;
            $data->save();


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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
