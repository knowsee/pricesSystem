<?php
namespace app\model;

use support\Model;

class Prices extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'prices';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';
	
	public function getDates()
	{
		return array();
	}

    protected static function booted()
    {
        static::saved(function ($info) {
            $log = new PricesLog;
            $log->shop_id = $info->shop_id;
            $log->goods_id = $info->goods_id;
            $log->prices = $info->prices;
            $log->save();
        });
    }
}