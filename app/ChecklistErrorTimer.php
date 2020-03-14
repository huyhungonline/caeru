<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChecklistErrorTimer extends Model
{
    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'main';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
