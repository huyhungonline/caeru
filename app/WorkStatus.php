<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkStatus extends Model
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
     * DEFAULT WORK STATUS CONSTANTS
     */
    const KEKKIN    = 1;
    const FURIKYUU  = 2;
    const FURIDE    = 3;
    const HOUDE     = 4;
    const KYUUDE    = 5;

    /**
     * get the default work statuses
     */
    public static function defaults()
    {
        return [
            self::KEKKIN        =>      '欠勤',
            self::FURIKYUU      =>      '振休',
            self::FURIDE        =>      '振出',
            self::HOUDE         =>      '法出',
            self::KYUUDE        =>      '休出',
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
