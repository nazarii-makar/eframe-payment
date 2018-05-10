<?php

namespace EFrame\Payment\Http\Resources;

use EFrame\Http\Resources\Json\ResourceCollection;

/**
 * Class OrderResource
 * @package EFrame\Payment\Http\Resources
 */
class OrderResource extends ResourceCollection
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
            'amount'        => $this->amount,
            'currency'      => $this->currency,
            'client_type'   => $this->client_type,
            'client_id'     => $this->client_id,
            'delivery_type' => $this->delivery_type,
            'delivery_id'   => $this->delivery_id,
            'is_regular'    => $this->is_regular,
            'status'        => $this->status,
            'created_at'    => $this->created_at->toIso8601String(),
            'updated_at'    => optional($this->updated_at)->toIso8601String(),
            'deleted_at'    => optional($this->deleted_at)->toIso8601String(),
        ];
    }
}