<?php
namespace app\model;

use support\Model;

class NetRex extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'network_rir_rex';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    protected $fillable = ['opaque_id', 'allocation_address', 'allocation_type', 'country',
        'date','economy_name','holder_account','holder_name','length','sub_account','subregion','trading_name','allocation_start','allocation_end'];
}