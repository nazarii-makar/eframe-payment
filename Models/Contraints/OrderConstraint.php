<?php

namespace EFrame\Payment\Models\Constraints;

use EFrame\Payment\Models\Order;
use Illuminate\Support\Facades\DB;
use EFrame\Database\Eloquent\Builder;
use EFrame\Payment\Http\Grids\OrderGrid;

/**
 * Trait OrderConstraint
 * @package EFrame\Payment\Models\Constraints
 *
 * @method static Order|Builder grid(OrderGrid $grid, array $sortable = ['amount', 'created_at', 'updated_at', 'deleted_at'])
 */
trait OrderConstraint
{
    /**
     * @param Builder      $query
     * @param BusinessGrid $grid
     *
     * @return Builder
     */
    public function scopeGrid($query, OrderGrid $grid, array $sortable = [
        'amount', 'created_at', 'updated_at', 'deleted_at'
    ]) {
        foreach (
            [
                'is_regular',
            ] as $attribute
        ) {
            $query->when(!is_null($grid->get($attribute)), function ($query) use ($attribute, $grid) {
                /** @var Builder $query */
                return $query->where($attribute, $grid->get($attribute));
            });
        }

        foreach (
            [
                'id',
                'currency',
                'client_type',
                'client_id',
                'delivery_type',
                'delivery_id',
                'status',
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

        $query->when(boolval($grid->get('withTrashed')), function ($query) {
            /** @var Builder $query */
            return $query->withTrashed();
        });

        return $query;
    }
}
