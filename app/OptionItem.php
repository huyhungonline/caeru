<?php

namespace App;
use Illuminate\Database\Eloquent\SoftDeletes;

class OptionItem extends Model
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
     * Constants for option_item types
     */
    const WORK_STATUS       = 1;
    const REST_STATUS       = 2;
    const DEPARTMENTS       = 3;

    /**
     * Scope a query to get work status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWorkStatuses($query)
    {
        return $query->where('type', OptionItem::WORK_STATUS);
    }

    /**
     * Scope a query to get rest status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRestStatuses($query)
    {
        return $query->where('type', OptionItem::REST_STATUS);
    }

    /**
     * Scope a query to get department types.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDepartments($query)
    {
        return $query->where('type', OptionItem::DEPARTMENTS);
    }

    /**
     * Return list optin item usage with work location id
     */
    public function optionItemUsages()
    {
        return $this->hasMany(OptionItemUsage::class);
    }

}
