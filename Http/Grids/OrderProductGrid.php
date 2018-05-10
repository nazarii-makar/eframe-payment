<?php

namespace EFrame\Payment\Http\Grids;

use EFrame\Foundation\Http\FormRequest;

/**
 * Class OrderProductGrid
 * @package EFrame\Payment\Http\Grids
 */
class OrderProductGrid extends FormRequest
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
            'q'             => 'string',
            'id'            => 'regex:/^(([0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12}),?){0,}$/',
            'order_id'      => 'regex:/^(([0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12}),?){0,}$/',
            'count'         => 'regex:/^[0-9,]+$/',
            'resource_type' => 'regex:/^[\w,]+$/',
            'resource_id'   => 'regex:/^[0-9,]+$/',
            'sort'          => 'regex:/^[\w,_-]+$/',
        ];
    }
}