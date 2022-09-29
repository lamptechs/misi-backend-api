<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
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
    protected function filter($data){
        return collect($data)->forget($this->withoutFields)->toArray();
    }

    /**
     * Collection
     */
    public static function collection($resource){
        return tap(new AdminResourceCollection($resource), function ($collection) {
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
            "id"            => $this->id ?? "",
            "name"          => $this->name ?? "",
            "email"         => $this->email ?? "",
            "groupid"       => $this->group_id ?? "",
            "department"    => (new GroupResource($this->groupid))->hide(["created_by", "updated_by"]),
            "created_by"    => $this->created_by ? (new AdminResource($this->createdBy)) : null,
            "updated_by"    => $this->updated_by ? (new AdminResource($this->updatedBy)) : null
        ]);
    }
}
