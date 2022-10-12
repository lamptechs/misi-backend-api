<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
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
        return tap(new AppointmentCollection($resource), function ($collection) {
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
            "id"         => $this->id,
            "patient_info"          => (new UserResource($this->patient))->hide(["created_by", "updated_by"]),
            "therapist_info"        => (new TherapistResource($this->therapist))->hide(["created_by", "updated_by"]),
            "therapist_schedule"    => (new TherapistScheduleResource($this->schedule))->hide(["created_by", "updated_by"]),
            "number"     => $this->number,
            "history"    => $this->history,
            //"appointment_date"     => $this->appointment_date,
            //"appointment_time" => $this->appointment_time, 
            "date"       => $this->date,
            "time"       => $this->time,
            "fee"        => $this->fee,
            "language"   => $this->language,
            "type"       => $this->type,
            "therapist_comment" => $this->therapist_comment,
            "remarks"     => $this->remarks,
            "status"   => $this->status,
            //"created_by"  => $this->created_by ? (new AdminResource($this->createdBy)) : null,
            //"updated_by"  => $this->updated_by ? (new AdminResource($this->updatedBy)) : null
        ]);
    }
}
