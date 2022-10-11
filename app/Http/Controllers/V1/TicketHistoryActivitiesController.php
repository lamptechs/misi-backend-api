<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Http\Resources\TicketHistoryActivitiesResource;
use App\Models\TicketHistoryActivity;

class TicketHistoryActivitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $this->data = TicketHistoryActivitiesResource::collection(TicketHistoryActivity::all());
            $this->apiSuccess("Ticket History Loaded Successfully");
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
        $validator = Validator::make(
            $request->all(),
            [
                //'name' => 'required|min:4',
    
            ]
           );
            
           if ($validator->fails()) {
    
            $this->apiOutput($this->getValidationError($validator), 400);
           }
   
            $tickethistory = new TicketHistoryActivity();
            $tickethistory->ticket_id = $request->ticket_id;
            $tickethistory->create_by = $request->create_by;
            $tickethistory->modified_by = $request->modified_by;
            $tickethistory->assign_to_therapist = $request->assign_to_therapist;
            $tickethistory->appointment_group = $request->appointment_group;
            $tickethistory->call_strike = $request->call_strike;
            $tickethistory->strike_history = $request->strike_history;
            $tickethistory->ticket_history = $request->ticket_history;
            $tickethistory->status = $request->status;
            $tickethistory->language = $request->language;
            $tickethistory->assign_to_user	 = $request->assign_to_user;
            $tickethistory->assign_to_user_status = $request->assign_to_user_status;
            $tickethistory->created_at = $request->created_at;
            $tickethistory->updated_at = $request->updated_at;
            //$tickethistory->created_at = Carbon::Now();
            $tickethistory->save();
            $this->apiSuccess();
            $this->data = (new TicketHistoryActivitiesResource($tickethistory));
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
    public function show($id)
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
    // public function update(Request $request, $id)
    // {
    //     try{
    //     $validator = Validator::make(
    //         $request->all(),
    //         [
    //             'name' => 'required|min:4',
    //             'remarks' => 'nullable|min:4'
    
    //         ]
    //        );
            
    //        if ($validator->fails()) {    
    //         $this->apiOutput($this->getValidationError($validator), 400);
    //        }
   
    //         $country = Country::find($id);
    //         $country->name = $request->name;
    //         $country->status = $request->status;
    //         $country->updated_by  =  $request->user()->id ?? null;
    //         // $country->updated_at  = Carbon::Now();
    //         $country->save();
    //         $this->apiSuccess();
    //         $this->data = (new CountryResource($country));
    //         return $this->apiOutput();
    //     }catch(Exception $e){
    //         return $this->apiOutput($this->getError( $e), 500);
    //     }
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy($id)
    // {
    //     Country::destroy($id);
    //     $this->apiSuccess();
    //     return $this->apiOutput("Country Deleted Successfully", 200);
    // }
}
