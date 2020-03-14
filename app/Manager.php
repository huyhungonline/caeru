<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use App\Events\ManagerSaved;
use App\Reusables\TodofukenTrait;
use App\Reusables\PasswordTrait;

class Manager extends Model implements
    AuthenticatableContract,
    AuthorizableContract
{
    use Authenticatable, Authorizable, SoftDeletes, PasswordTrait;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $events = [
        'saved'  => ManagerSaved::class,
    ];

    /**
     * Get the authority
     */
    public function managerAuthority()
    {
        return $this->hasOne(ManagerAuthority::class);
    }

    /**
     * Get company of this manager
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get all the work locations that this manager is managing
     */
    public function workLocations()
    {
        return $this->belongsToMany(WorkLocation::class, 'manager_manage_work_location')->withTimestamps();
    }

    /**
     * Get all the ip addresses of this manager
     */
    public function ipAddresses()
    {
        return $this->belongsToMany(IpAddress::class, 'manager_ip_address')->withTimestamps();
    }

    /**
     * Get full name of this manager
     */
    public function fullName()
    {
        return $this->last_name . $this->first_name;
    }
}
