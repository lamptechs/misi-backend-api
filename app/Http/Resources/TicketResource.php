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
        return $this->filterFields([
            "id"                    => $this->id,
            "location"              => $this->location ?? "",
            "language"              => $this->language ?? "",
            "date"                  => $this->date ?? "",
            "strike"                => $this->strike ?? "",
            "strike_history"        => $this->strike_history ?? "",
            "ticket_history"        => $this->ticket_history ?? "",
            "remarks"               => $this->remarks ?? "",
            "status"                => $this->status ?? false,
            "total_replies"         => count($this->replies),
            
            "created_by"    => isset($this->created_by) ? (new AdminResource($this->createdBy))->hide(["groupId","department", "created_by","updated_by"]) : null,
            "updated_by"    => isset($this->updated_by) ? (new AdminResource($this->updatedBy))->hide(["groupId","department", "created_by","updated_by"]) : null,
            "therapist"             => (new TherapistResource($this->therapist))->hide(["created_by", "updated_by", "upload_files", "image", "therapist_type", "blood_group", "country", "state"]),
            "patient"               => (new UserResource($this->patient))->hide(["created_by", "updated_by", "blood_group", "group", "upload_files", "updated_by", "created_by", "state", "country"]),
            "ticket_department"     => (new TicketDepartmentResource($this->ticketDepartment))->hide(["created_by", "updated_by"]),
            "replies"               => TicketReplyResource::collection($this->replies)->hide(["updated_by", "created_by"])
        ]);
    }
}
