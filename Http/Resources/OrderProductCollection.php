<?php

namespace EFrame\Payment\Http\Resources;

use EFrame\Http\Resources\Json\ResourceCollection;

/**
 * Class OrderProductCollection
 * @package EFrame\Payment\Http\Resources
 */
class OrderProductCollection extends ResourceCollection
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
            'data' => OrderProductResource::collection($this->collection),
        ];
    }
}
