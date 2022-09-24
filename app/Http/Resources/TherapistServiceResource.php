<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TherapistServiceResource extends JsonResource
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
        return tap(new TherapistServiceCollection($resource), function ($collection) {
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
            "therapist"          => (new TherapistResource($this->therapist))->hide(["created_by", "updated_by"]),
            "service_category"          => (new ServiceCategoryResource($this->category))->hide(["created_by", "updated_by"]),
            "service_subcategory"          => (new ServiceSubCategoryResource($this->subCategory))->hide(["created_by", "updated_by"]),
            "name"    => $this->name,
            "status"  => $this->status,
            "created_by"                => $this->created_by ? (new AdminResource($this->createdBy)) : null,
            "updated_by"                => $this->updated_by ? (new AdminResource($this->updatedBy)) : null,
        ]);
    }
}
