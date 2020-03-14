<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use  Carbon\Carbon;


class SeparatedFieldsComposer
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

        // For each object, find and transform all postal_code, telephone and fax fields.
        foreach ($data as $object) {
            if (is_object($object)) {
                foreach ($object->toArray() as $key => $value) {
                    if($key == "postal_code" && $value) {
                        $object->setAttribute('postal_code_1', substr($value, 0, 3));
                        $object->setAttribute('postal_code_2', substr($value, 3, 4));
                    } elseif (($key == "telephone" || $key == "fax" || $key == 'birthday' || $key == 'joined_date' || $key == 'resigned_date') && $value ) {
                        $parts = explode('-', $value);
                        $object->setAttribute($key . '_1',$parts[0]);
                        $object->setAttribute($key . '_2',$parts[1]);
                        $object->setAttribute($key . '_3',$parts[2]);
                    }
                }
            }
        }
    }
}