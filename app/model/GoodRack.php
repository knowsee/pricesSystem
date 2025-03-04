<?php
namespace app\model;

use support\Model;

class GoodRack extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'good_rack';

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
	
	public function getDates()
	{
		return array();
	}
}