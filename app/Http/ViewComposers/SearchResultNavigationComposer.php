<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Route;
use Caeru;


class SearchResultNavigationComposer
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

        // Well, I dont know about the future but, at the moment, it's either employee or work address at this page.
        if (isset($data['employee']) && $data['employee']) {
            $model = $data['employee'];
            $search_history = session('employee_search_history');
        } elseif (isset($data['work_address']) && $data['work_address']) {
            $model = $data['work_address'];
            $search_history = session('work_address_search_history');
        }

        if($search_history) {

            $result_array = $search_history['result_order'];

            $current_search_result_index = false;

            $current_search_result_index = array_search($model->id, array_column($result_array, 'id'));

            if ($current_search_result_index !== false) {

                $previous_index = $current_search_result_index - 1;
                $next_index = $current_search_result_index + 1;

                if ($previous_index >= 0 )
                    $view->with([ 'search_navi_previous' => Caeru::route(Route::currentRouteName(), [ $result_array[$previous_index]['id'], ceil(($previous_index + 1)/20) ]) ]);

                if (($next_index) < count($result_array))
                    $view->with([ 'search_navi_next' => Caeru::route(Route::currentRouteName(), [ $result_array[$next_index]['id'], ceil(($next_index + 1)/20) ]) ]);

                $view->with([
                    'search_navi_presentation_id'   => $result_array[$current_search_result_index]['presentation_id'],
                    'search_navi_name'              => $result_array[$current_search_result_index]['name'],
                ]);

            } else {

                $view->with([
                    'search_navi_presentation_id'   => $model->presentation_id,
                    'search_navi_name'              => isset($data['employee']) ? $model->last_name . $model->first_name : $model->name,
                ]);

            }

        }
    }
}