<?php

namespace App\Observers;

use App\WorkLocation;
use App\Employee;

// This class's purpose is to handle the view_order of the model 
class AddViewOrderNumberObserver
{

    /**
     * Listen to the created event of the model then add the appropriate view_order number
     *
     * @param Eloquent $model
     * @return void
     */
    public function created($model)
    {
        $model->view_order = $this->getViewOrder($model);
        $model->save();
    }

    /**
     * Listen to the deleting event of the model and then change the view_order to an unused number
     *
     * @param Eloquent $model
     * @return void
     */
    public function deleting($model)
    {
        // Get the affected models and reduce their view_orders by one
        $affected_models = $model::where('view_order', '>', $model->view_order)->get();
        foreach ($affected_models as $affected_model) {
            $affected_model->view_order -= 1;
            $affected_model->save();
        }

        // Then change the  to-be-deleted model's view_order to an unused number
        $model->view_order = config('caeru.unused_view_order');
        $model->save();
    }

    /**
     * Listen to the restoring event of the model and add the approriate view_order number
     *
     * @param Eloquent $model
     * @return void
     */
    public function restored($model)
    {
        $model->view_order = $this->getViewOrder($model);
        $model->save();
    }

    /**
     * Get the correct view order for this object
     *
     * @param Eloquent $model
     * @return int
     */
    private function getViewOrder($model)
    {
        if (is_a($model, WorkLocation::class)) {
            return $model::count();
        } elseif (is_a($model, Employee::class)) {
            return Employee::where('work_location_id', $model->workLocation->id)->count();
        } else {
            return config('caeru.unused_view_order');
        }
    }

}