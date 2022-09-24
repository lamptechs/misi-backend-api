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
            "name" => $this->name,
            "patient_info" => (new UserResource($this->patient))->hide(["created_by", "updated_by"]),
            "type" => $this->type,
            "number" => $this->number,
            "expiration_date" => $this->expiration_date,
            "question"      =>  ScaleResource::collection($this->scale),
            // "question"          => (new QuestionResource($this->question))->hide(["created_by", "updated_by"]),
            "created_by"  => $this->created_by ? (new AdminResource($this->createdBy)) : null,
            "updated_by"  => $this->updated_by ? (new AdminResource($this->updatedBy)) : null
        ]);
    }
}
