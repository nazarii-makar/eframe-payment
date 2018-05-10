<?php

namespace EFrame\Payment\Http\Resources;

use EFrame\Http\Resources\Json\ResourceCollection;

/**
 * Class OrderCollection
 * @package EFrame\Payment\Http\Resources
 */
class OrderCollection extends ResourceCollection
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
            'data' => OrderResource::collection($this->collection),
        ];
    }
}
