<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketReplyResource extends JsonResource
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
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->filter([
            "id"                => $this->id,
            "ticket_id"         => $this->ticket_id,
            "comment"           => $this->comment ?? "",
            "file"              => !empty($this->file) ? asset($this->file) : null,
            "created_by"        => (new AdminResource($this->createdBy))->hide(["groupid","department", "created_by","updated_by"]),
            "updated_by"        => (new AdminResource($this->updatedBy))->hide(["groupid","department", "created_by","updated_by"]),            
        ]);
    }
}
