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
        return $this->filterFields([
            "id"         => $this->id,
            "number"     => $this->number,
            "history"    => $this->history,
            "date"       => $this->date,
            "time"       => $this->time,
            "fee"        => $this->fee,
            "language"   => $this->language,
            "type"       => $this->type,
            "therapist_comment" => $this->therapist_comment,
            "remarks"     => $this->remarks,
            "status"   => $this->status,
            "image"             => $this->image,
            "image_url"         => asset($this->image_url),
            "upload_files"      => AppointmentUploadResource::collection($this->fileInfo),
            "appointment ticket status" => $this->appointment_ticket_status,
<<<<<<< HEAD
            "patient_info"          => (new UserResource($this->patient))->hide(["created_by", "updated_by"]),
            "therapist_info"        => (new TherapistResource($this->therapist))->hide(["created_by", "updated_by"]),
            "therapist_schedule"    => (new TherapistScheduleResource($this->schedule))->hide(["created_by", "updated_by", "patient", "therapist"]),
=======
            "patient_info"          => isset($this->patient) ? (new UserResource($this->patient))->hide(["created_by", "updated_by"]) : null,
            "therapist_info"        => isset($this->therapist)? (new TherapistResource($this->therapist))->hide(["created_by", "updated_by"]) : null,
            "therapist_schedule"    => (new TherapistScheduleResource($this->schedule))->hide(["created_by", "updated_by"]),
>>>>>>> 2ad3168d607cf0f4fa9238b75bebea7171b61fb3
        ]);
    }
}
