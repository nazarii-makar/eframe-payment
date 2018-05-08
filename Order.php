<?php

namespace EFrame\Payment;

use EFrame\Uuid\HasUuid;
use EFrame\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Order
 * @package EFrame\Payment
 */
class Order extends Model
{
    use SoftDeletes,
        HasUuid;

    /**
     * Status states
     */
    const STATUS_ACTIVE     = 1;
    const STATUS_PENDING    = 2;
    const STATUS_EXPIRED    = 3;
    const STATUS_NOT_ACTIVE = 0;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orders';

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
    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'expire_in'];

    /**
     * @var array
     */
    protected $fillable = [
        'amount',
        'currency',
        'client_type',
        'client_id',
        'delivery_type',
        'delivery_id',
        'is_regular',
        'status',
        'expire_in',
        'rec_token',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'id'            => 'string',
        'amount'        => 'float',
        'client_type'   => 'string',
        'client_id'     => 'int',
        'delivery_type' => 'string',
        'delivery_id'   => 'int',
        'is_regular'    => 'bool',
        'status'        => 'int',
        'rec_token'     => 'string',
    ];

    /**
     * Order exposed observable events.
     *
     * These are extra user-defined events observers may subscribe to.
     *
     * @var array
     */
    protected $observables = [
        'activating',
        'activated',
        'excepting',
        'excepted',
        'deactivating',
        'deactivated',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function client()
    {
        return $this->morphTo('client');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function delivery()
    {
        return $this->morphTo('delivery');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(OrderProduct::class, 'order_id');
    }

    /**
     * Activate order
     *
     * @return void
     */
    public function activate()
    {
        if (false === $this->fireModelEvent('activating')) {
            return;
        }

        $this->status = self::STATUS_ACTIVE;

        $this->save();

        $this->fireModelEvent('activated', false);
    }

    /**
     * Except order
     *
     * @return void
     */
    public function except()
    {
        if (false === $this->fireModelEvent('excepting')) {
            return;
        }

        $this->status = self::STATUS_PENDING;

        $this->save();

        $this->fireModelEvent('excepted', false);
    }

    /**
     * Deactivate order
     *
     * @return void
     */
    public function deactivate()
    {
        if (false === $this->fireModelEvent('deactivating')) {
            return;
        }

        $this->status = self::STATUS_NOT_ACTIVE;

        $this->save();

        $this->fireModelEvent('deactivated', false);
    }
}
