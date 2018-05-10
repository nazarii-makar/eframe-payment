<?php

namespace EFrame\Payment\Http\Resources;

use EFrame\Http\Resources\Json\ResourceCollection;

/**
 * Class OrderProductResource
 * @package EFrame\Payment\Http\Resources
 */
class OrderProductResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'order_id'      => $this->order_id,
            'name'          => $this->name,
            'price'         => $this->price,
            'count'         => $this->count,
            'resource_type' => $this->resource_type,
            'resource_id'   => $this->resource_id,
            'created_at'    => $this->created_at->toIso8601String(),
            'updated_at'    => optional($this->updated_at)->toIso8601String(),
        ];
    }
}