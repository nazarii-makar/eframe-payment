<?php

namespace EFrame\Payment\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use EFrame\Payment\Http\Resources\OrderProductResource;

/**
 * Class OrderResource
 * @package EFrame\Payment\Http\Resources
 */
class OrderResource extends Resource
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
            'purchased_at'  => optional($this->purchased_at)->toIso8601String(),
            'created_at'    => $this->created_at->toIso8601String(),
            'updated_at'    => optional($this->updated_at)->toIso8601String(),
            'deleted_at'    => optional($this->deleted_at)->toIso8601String(),
            $this->mergeWhen($this->resource->relationLoaded('products'), [
                'products' => OrderProductResource::collection($this->resource->products),
            ]),
        ];
    }
}