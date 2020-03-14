<?php

namespace App;

class WorkAddressWorkingDay extends Model
{

    /**
     * Get the work address of this working day instance
     */
    public function workAddress()
    {
        return $this->belongsTo(WorkAddress::class);
    }


    /**
     * Get all the working information instances of this working day instance
     */
    public function workAddressWorkingInformations()
    {
        return $this->hasMany(WorkAddressWorkingInformation::class);
    }

    /**
     * Get all the candidate information instances of this working day instance
     */
    public function workAddressCandidateInformations()
    {
        return $this->hasMany(WorkAddressCandidateInformation::class);
    }
}
