<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as Javascript;


class MaskedViewOrderComposer
{

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $re_order = is_numeric(session('current_work_location'));

        // Prepare the presentation data
        $view->with([
            're_order' => $re_order,
        ]);

        // Mask the view order
        if ($re_order) {

            $data = $view->getData();

            $employees = $data["employees"];

            $start_number = (($employees->currentPage() - 1) * $employees->perPage()) + 1;

            foreach ($employees as $employee) {
                $employee->setAttribute('masked_view_order', $start_number);
                $start_number++;
            }
            
        }

    }
}