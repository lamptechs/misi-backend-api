<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    protected $withoutFields = [];

    /**
     * Set Hidden Item 
     */
    public function hide(array $hide = []){
        $this->withoutFields = $hide;
        return $this;
    }

    /**
     * Filter Hide Items
     */
    protected function filterFields($data){
        return collect($data)->forget($this->withoutFields)->toArray();
    }

    /**
     * Collection
     */
    public static function collection($resource){
        return tap(new TicketCollection($resource), function ($collection) {
            $collection->collects = __CLASS__;
        });
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->filter([
            "id"                    => $this->id,
            "location"              => $this->location,
            "language"              => $this->language,
            "date"                  => $this->date,
            "strike"                => $this->strike,
            "strike_history"        => $this->strike_history,
            "ticket_history"        => $this->ticket_history,
            "remarks"               => $this->remarks,
            "status"                => $this->status,
            "comment"               =>$this->comment,
            "assign_to_user"        => $this->assign_to_user,
            "assign_to_user_status" => $this->assign_to_user_status,
            "ticket_status" => $this->ticket_status,
            "created_by"            => (new AdminResource($this->createdBy))->hide(["groupid","department", "created_by","updated_by"]),
            "updated_by"            => (new AdminResource($this->updatedBy))->hide(["groupid","department", "created_by","updated_by"]),
            "therapist_info"        => (new TherapistResource($this->therapist))->hide(["created_by", "updated_by"]),
            "patient_info"          => (new UserResource($this->patient))->hide(["created_by", "updated_by"]),
            "department"            => (new GroupResource($this->group))->hide(["created_by", "updated_by"]),
            // "department"            => (new AdminResource($this->groupid))->hide(["created_by", "updated_by"]),
            // "admin_name"            => (new AdminResource($this->department))->hide(["created_by", "updated_by"]),
            "ticket_department_info"=> (new TicketDepartmentResource($this->ticketDepartment))->hide(["created_by", "updated_by"]),
            
            
        ]);
    }
}
