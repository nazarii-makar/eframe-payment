<?php

namespace EFrame\Payment\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use EFrame\Payment\Http\Resources\OrderProductResource;

/**
 * Class TransactionResource
 * @package EFrame\Payment\Http\Resources
 */
class TransactionResource extends Resource
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
            'gateway'       => $this->gateway,
            'amount'        => $this->amount,
            'currency'      => $this->currency,
            'details'       => $this->details,
            'rec_token'     => $this->rec_token,
            'status'        => $this->status,
            'created_at'    => $this->created_at->toIso8601String(),
            'updated_at'    => optional($this->updated_at)->toIso8601String(),
            'processing_at' => optional($this->processing_at)->toIso8601String(),
        ];
    }
}