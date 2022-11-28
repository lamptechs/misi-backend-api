<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class IntakeResource extends JsonResource
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
        return tap(new IntakeCollection($resource), function ($collection) {
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
            "id"                => $this->id,
            "appointment_id"    => $this->appointment_id,
            "intake_date"       => $this->intake_date,
            "intake_number"     => $this->intake_number,
            // "scale_value"   => $this->scale_value ?? 0,
            // "status"        => $this->status,
            // "remarks"       => $this->remarks,
            // "created_at"    => $this->created_at,
            // "updated_at"    => $this->updated_at,
            
             "appointment"       =>  isset($this->appointment) ? (new AppointmentResource($this->appointment))->hide([ "updated_by","created_by"]) :null,
            // "pit_formula"   => isset($this->pitformula) ? (new PitFormulaResource($this->pitformula))->hide(["patient", "created_by", "updated_by", "ticket"]) : null,
            // "question"      => isset($this->question) ? (new QuestionResource($this->question))->hide(["created_by", "updated_by"]) : null,
            // "created_by"    => isset($this->createdBy) ? (new AdminResource($this->createdBy))->hide(["department", "created_by", "updated_by"]) : null,
            // "updated_by"    => isset($this->updatedBy) ? (new AdminResource($this->updatedBy))->hide(["department", "created_by", "updated_by"]) : null,
        ]);
    }
}