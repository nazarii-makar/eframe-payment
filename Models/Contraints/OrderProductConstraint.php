<?php

namespace EFrame\Payment\Models\Constraints;

use Illuminate\Support\Facades\DB;
use EFrame\Database\Eloquent\Builder;
use EFrame\Payment\Models\OrderProduct;
use EFrame\Payment\Http\Grids\OrderProductGrid;

/**
 * Trait OrderProductConstraint
 * @package EFrame\Payment\Models\Constraints
 *
 * @method static OrderProduct|Builder grid(OrderProductGrid $grid, array $sortable = ['price', 'count', 'created_at', 'updated_at'])
 */
trait OrderProductConstraint
{
    /**
     * @param Builder      $query
     * @param BusinessGrid $grid
     *
     * @return Builder
     */
    public function scopeGrid($query, OrderProductGrid $grid, array $sortable = [
        'price', 'count', 'created_at', 'updated_at'
    ]) {
        $query->when(!is_null($grid->get('q')), function ($query) use ($grid) {
            /** @var Builder $query */
            return $query->where(
                'name', 'like', '%' . $grid->get('q') . '%'
            );
        });

        foreach (
            [
                'id',
                'order_id',
                'count',
                'resource_type',
                'resource_id',
            ] as $attribute
        ) {
            $query->when(!is_null($grid->get($attribute)), function ($query) use ($attribute, $grid) {
                /** @var Builder $query */
                $attributes = collect(explode(',', $grid->get($attribute)));

                $attributes = $attributes->filter(function ($value) {
                    return !is_null($value) && '' !== $value;
                })->unique();

                $query->when($attributes->isNotEmpty(), function ($query) use ($attribute, $attributes) {
                    /** @var Builder $query */
                    return $query->whereIn($attribute, $attributes);
                });

                return $query;
            });
        }

        $query->when(!is_null($grid->get('sort')), function ($query) use ($grid, $sortable) {
            /** @var Builder $query */
            return $query->sortBy($grid->get('sort'), $sortable);
        });

        return $query;
    }
}
