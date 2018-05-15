<?php

namespace EFrame\Payment\Http\Resources;

use EFrame\Http\Resources\Json\ResourceCollection;

/**
 * Class TransactionCollection
 * @package EFrame\Payment\Http\Resources
 */
class TransactionCollection extends ResourceCollection
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
            'data' => TransactionResource::collection($this->collection),
        ];
    }
}
