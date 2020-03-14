<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;


class IpAddressComposer
{

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $data = $view->getData();

        // For each manager, get all ip addresses and transform into a string.
        foreach ($data as $manager) {
            if (is_object($manager)) {
                $ip_array = $manager->ipAddresses->pluck('value')->toArray();
                $string = implode("\n", $ip_array);
                $manager->setAttribute('ip_address', $string);
            }
        }
    }
}