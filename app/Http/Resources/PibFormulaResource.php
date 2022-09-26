<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PibFormulaResource extends JsonResource
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
        return tap(new PibFormulaCollection($resource), function ($collection) {
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
            "patient_id" => $this->patient_id,
            "pib_name" => $this->pib_name,
            "type_of_legitimation"=> $this->type_of_legitimation,
            "document_number"=> $this->document_number,
            "identify_expire_date"=> $this->identify_expire_date,
            "patient_code"=> $this->patient_code,
            "create_by"=> $this->create_by,
            "ticket_id"=> $this->ticket_id,
            "deleted_by"=> $this->deleted_by,
            "deleted_date"=> $this->deleted_date,
            "status"=> $this->status,
            "remarks"=> $this->remarks,
            "modified_by"   => $this->modified_by,
            "modified_date"   => $this->modified_date,
            "created_by"   => $this->created_by,
            "created_date"   => $this->created_date,

            // "therapist_type"          => (new TherapistTypeResource($this->therapistType))->hide(["created_by", "updated_by"]),
            // "blood_group"          => (new BloodGroupResource($this->blood))->hide(["created_by", "updated_by"]),
            // "country"          => (new CountryResource($this->country))->hide(["created_by", "updated_by"]),
            // "state"          => (new StateResource($this->state))->hide(["created_by", "updated_by"]),
            // "upload_files"      => TherapistUploadResource::collection($this->fileInfo),
            // "file_details"          => (new TherapistUploadResource($this->fileInfo))->hide(["created_by", "updated_by"]),
            // "created_by"  => $this->created_by ? (new AdminResource($this->createdBy)) : null,
            // "updated_by"  => $this->updated_by ? (new AdminResource($this->updatedBy)) : null
        ]);
    }
}
