<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TherapistResource extends JsonResource
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
        return tap(new TherapistCollection($resource), function ($collection) {
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
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "email"=> $this->email,
            "phone"=> $this->phone,
            "address"=> $this->address,
            "language"=> $this->language,
            "bsn_number"=> $this->bsn_number,
            "dob_number"=> $this->dob_number,
            "insurance_number"=> $this->insurance_number,
            "emergency_contact"=> $this->emergency_contact,
            "gender"=> $this->gender,
            "date_of_birth"=> $this->date_of_birth,
            "status"   => $this->status,
            "therapist_type"          => (new TherapistTypeResource($this->therapistType))->hide(["created_by", "updated_by"]),
            "blood_group"          => (new BloodGroupResource($this->blood))->hide(["created_by", "updated_by"]),
            "country"          => (new CountryResource($this->country))->hide(["created_by", "updated_by"]),
            "state"          => (new StateResource($this->state))->hide(["created_by", "updated_by"]),
            "upload_files"      => TherapistUploadResource::collection($this->fileInfo),
            // "file_details"          => (new TherapistUploadResource($this->fileInfo))->hide(["created_by", "updated_by"]),
            "created_by"  => $this->created_by ? (new AdminResource($this->createdBy)) : null,
            "updated_by"  => $this->updated_by ? (new AdminResource($this->updatedBy)) : null
        ]);
    }
}
