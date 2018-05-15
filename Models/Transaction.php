<?php

namespace EFrame\Payment\Models;

use Carbon\Carbon;
use EFrame\Uuid\HasUuid;
use EFrame\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use EFrame\Payment\Models\Constraints\OrderConstraint;
use EFrame\Payment\Models\Constraints\TransactionConstraint;

/**
 * Class Transaction
 * @package EFrame\Payment\Models
 */
class Transaction extends Model
{
    use HasUuid,
        TransactionConstraint;

    /**
     * Status states
     */
    const STATUS_SUCCESS = 1;
    const STATUS_PENDING = 2;
    const STATUS_FAILURE = 3;
    const STATUS_ERROR   = 0;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transactions';

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
    protected $dates = ['created_at', 'updated_at', 'processing_at'];

    /**
     * @var array
     */
    protected $fillable = [
        'order_id',
        'gateway',
        'amount',
        'currency',
        'details',
        'rec_token',
        'status',
        'created_at',
        'updated_at',
        'processing_at',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'id'        => 'string',
        'order_id'  => 'string',
        'amount'    => 'float',
        'currency'  => 'string',
        'details'   => 'array',
        'rec_token' => 'string',
        'status'    => 'int',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'rec_token',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
