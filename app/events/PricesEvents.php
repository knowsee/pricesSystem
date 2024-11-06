<?php
namespace app\events;

use app\model\Prices;
use app\model\PricesLog;

class PricesEvents {

    public function saved(Prices $info)
    {
    	$log = new PricesLog;
        $log->shop_id = $info->shop_id;
        $log->goods_id = $info->goods_id;
        $log->prices = $info->prices;
        $log->save();
    }

}