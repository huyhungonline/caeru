<?php

namespace App\Reusables;


trait TodofukenTrait 
{
    /**
     * Get todofuken name of this model
     */
    public function todofuken()
    {
        $todofuken = \DB::table('todofukens')->find($this->todofuken);

        return $todofuken ? $todofuken->name : '';
    }
}