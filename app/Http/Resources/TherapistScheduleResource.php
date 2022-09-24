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
            "id"         => $this->id,
            "schedule_day" => $this->schedule_day,
            "start_date"   => $this->start_date,
            "end_date"     => $this->end_date,
            "start_time"   => $this->start_time,
            "end_time"     => $this->end_time,
            "consulting_time"     => $this->consulting_time,
            "remarks"             => $this->remarks,
            "status"              => $this->status,
            "therapist"          => (new TherapistResource($this->therapist))->hide(["created_by", "updated_by"]),
            "created_by"  => $this->created_by ? (new AdminResource($this->createdBy)) : null,
            "updated_by"  => $this->updated_by ? (new AdminResource($this->updatedBy)) : null
        ]);
    }
}
