<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PitScaleResource extends JsonResource
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
        return tap(new PitScaleCollection($resource), function ($collection) {
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
            "patient_id" => $this->patient_id,
            "pit_formula_id" => $this->pit_formula_id,
            "question_id"=> $this->question_id,
            "scale_value"=> $this->scale_value,
            "deleted_by"=> $this->deleted_by,
            "deleted_date"=> $this->deleted_date,
            "status"=> $this->status,
            "remarks"=> $this->remarks,
            "modified_by"=> $this->modified_by,
            "modified_date"=> $this->modified_date,
            "created_by"   => $this->created_by,
            "created_date"   => $this->created_date,


        ]);
    }
}
