<?php

namespace EFrame\Payment\Models\Constraints;

use Illuminate\Support\Facades\DB;
use EFrame\Database\Eloquent\Builder;
use EFrame\Payment\Models\Transaction;
use EFrame\Payment\Http\Grids\TransactionGrid;

/**
 * Trait TransactionConstraint
 * @package EFrame\Payment\Models\Constraints
 *
 * @method static Transaction|Builder grid(TransactionGrid $grid, array $sortable = ['amount', 'created_at', 'updated_at', 'processing_at'])
 */
trait TransactionConstraint
{
    /**
     * @param Builder      $query
     * @param BusinessGrid $grid
     *
     * @return Builder
     */
    public function scopeGrid($query, TransactionGrid $grid, array $sortable = [
        'amount', 'created_at', 'updated_at', 'processing_at',
    ]) {
        foreach (
            [
                'id',
                'order_id',
                'gateway',
                'currency',
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

        return $query;
    }
}
