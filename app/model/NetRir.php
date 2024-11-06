<?php
namespace app\model;

use support\Model;

class NetRir extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'network_rir_statistics';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'hashcode';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * 指明模型的 ID 不是自增。
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * 自增ID的数据类型。
     *
     * @var string
     */
    protected $keyType = 'string';
}