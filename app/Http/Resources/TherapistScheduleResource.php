<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TherapistScheduleResource extends JsonResource
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
        return tap(new TherapistScheduleCollection($resource), function ($collection) {
            $collection->collects = __CLASS__;
        });
    }

    public function toArray($request)
    {
        return $this->filter([
            "id"            => $this->id,
            "date"          => $this->date,
            "start_time"    => $this->start_time,
            "end_time"      => $this->end_time,
            "status"        => $this->status,
            "remarks"       => $this->remarks,
            "patient"       => (new UserResource($this->patient))->hide(["created_by", "updated_by", "upload_files", "group"]),
            "therapist"     => (new TherapistResource($this->therapist))->hide(["created_by", "updated_by"]),
            "created_by"    => (new AdminResource($this->createdBy))->hide(["department", "created_by", "updated_by"]),
            "updated_by"    => (new AdminResource($this->updatedBy))->hide(["department", "created_by", "updated_by"]),
        ]);
    }
}
