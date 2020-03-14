<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestStatus extends Model
{
    use SoftDeletes;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * DEFAULT REST STATUS CONSTANTS
     */
    const YUUKYU_1      = 1;
    const YUUKYU_2      = 2;
    const ZENKYUU_1     = 3;
    const ZENKYUU_2     = 4;
    const GOKYUU_1      = 5;
    const GOKYUU_2      = 6;
    const JIYUU         = 7;
    const HANKYUU_1     = 8;
    const HANKYUU_2     = 9;

    /**
     * get the default work statuses
     */
    public static function defaults()
    {
        return [
            self::YUUKYU_1          =>      '有給',
            self::YUUKYU_2          =>      '有休',
            self::ZENKYUU_1         =>      '前給',
            self::ZENKYUU_2         =>      '前休',
            self::GOKYUU_1          =>      '後給',
            self::GOKYUU_2          =>      '後休',
            self::JIYUU             =>      '時有',
            self::HANKYUU_1         =>      '半給',
            self::HANKYUU_2         =>      '半休',
        ];
    }

    /**
     * Get the company of this work location
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
