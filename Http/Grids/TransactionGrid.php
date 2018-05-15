<?php

namespace EFrame\Payment\Http\Grids;

use EFrame\Foundation\Http\FormRequest;

/**
 * Class TransactionGrid
 * @package EFrame\Payment\Http\Grids
 */
class TransactionGrid extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id'       => 'regex:/^(([0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12}),?){0,}$/',
            'order_id' => 'regex:/^(([0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12}),?){0,}$/',
            'gateway'  => 'regex:/^[a-z,_-]+$/',
            'currency' => 'regex:/^[A-Z,]+$/',
            'status'   => 'regex:/^[0-9,]+$/',
            'sort'     => 'regex:/^[\w,_-]+$/',
        ];
    }
}