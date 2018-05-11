<?php

namespace EFrame\Payment\Models;

use EFrame\Uuid\HasUuid;
use EFrame\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use EFrame\Payment\Models\Constraints\OrderProductConstraint;

/**
 * Class OrderProduct
 * @package EFrame\Payment\Models
 */
class OrderProduct extends Model
{
    use HasUuid,
        OrderProductConstraint;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orders_products';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * @var array
     */
    protected $fillable = [
        'order_id',
        'name',
        'price',
        'count',
        'resource_type',
        'resource_id',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'id'            => 'string',
        'order_id'      => 'string',
        'name'          => 'string',
        'price'         => 'float',
        'count'         => 'int',
        'resource_type' => 'string',
        'resource_id'   => 'int',
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function resource()
    {
        return $this->morphTo('resource');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id')->withTrashed();
    }
}
