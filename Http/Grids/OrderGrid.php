<?php

namespace EFrame\Payment\Http\Grids;

use EFrame\Foundation\Http\FormRequest;

/**
 * Class OrderGrid
 * @package EFrame\Payment\Http\Grids
 */
class OrderGrid extends FormRequest
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
            'id'            => 'regex:/^(([0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12}),?){0,}$/',
            'currency'      => 'regex:/^[A-Z,]+$/',
            'client_type'   => 'regex:/^[\w,]+$/',
            'client_id'     => 'regex:/^[0-9,]+$/',
            'delivery_type' => 'regex:/^[\w,]+$/',
            'delivery_id'   => 'regex:/^[0-9,]+$/',
            'is_regular'    => 'bool',
            'withTrashed'   => 'bool',
            'sort'          => 'regex:/^[\w,_-]+$/',
        ];
    }
}