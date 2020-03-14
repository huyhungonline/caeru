<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Validation\Rule;
use App\Http\Controllers\SearchController;
use App\Http\Requests\ChangeViewOrderRequest;

class ChangeViewOrderController extends Controller
{
    use ValidatesRequests;

    /**
     * array type of models, you can add new type of model here
     */
    private $object_types = [];

    private $controller;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(SearchController $controller)
    {
        $this->object_types = [
            1   =>  "WorkLocation",
            2   =>  "Employee",
        ];

        $this->controller = $controller;
    }

    /**
     * Handle the order changing
     *
     * @param ChangeViewOrderRequest $request
     * @return void
     */
    public function changeOrder(ChangeViewOrderRequest $request)
    {

        $company = $request->user()->company;

        extract($request->only(['from', 'to', 'type', 'page']));

        $class_name = 'App\\' . $this->object_types[$type];

        // If the object is employee, then the view order number has been masked. We need to unmasked it.
        if ($type != 2) {
            $to = $this->errorProof($to, $class_name);
            $from = $this->errorProof($from, $class_name);
        } else {
            $to = $this->unMaskViewOrder($to, $class_name);
            $from = $this->unMaskViewOrder($from, $class_name);
            $current_work_location = $request->input('current_work_location');
        }

        // Get the directly moved object. How to get is depend on the type
        $object = ($type != 2) ?  $class_name::where('view_order', $from)->first() :
            $class_name::where('work_location_id', $current_work_location)->where('view_order', $from)->first();


        // Then depend on the relationship between from and to, perform moving the affected_objects
        if ($from > $to) {

            $affected_objects = ($type != 2) ? $class_name::where('view_order', '>=', $to)->where('view_order', '<', $from)->get() :
                $class_name::where('work_location_id', $current_work_location)->where('view_order', '>=', $to)->where('view_order', '<', $from)->get();

            foreach ($affected_objects as $ob) {
                $ob->view_order += 1;
                $ob->save();
            }

        } elseif ($from < $to) {

            $affected_objects = ($type != 2) ? $class_name::where('view_order', '<=', $to)->where('view_order', '>', $from)->get() :
                $class_name::where('work_location_id', $current_work_location)->where('view_order', '<=', $to)->where('view_order', '>', $from)->get();;

            foreach ($affected_objects as $ob) {
                $ob->view_order -= 1;
                $ob->save();
            }
        }

        // Next, we will save the destination position to the directly moved object's view order. In other words, move that shit.
        $object->view_order = $to;

        $object->save();


        // Finally, return the coresponding results.
        if ($type != 2) {

            return view('work_location.list_table')->with([
                'work_locations'    =>  $company->workLocations()->orderBy('view_order')->orderBy('id')->paginate(20, ['*'], 'page', $page),
                'can_change_view_order' => true,
            ]);

        } else {

            $conditions = session('employee_search_history')['conditions'];

            $result = $this->controller->getEmployeesApplyConditions($conditions);

            return view('employee.list_table')->with([
                'employees'     =>      $result->paginate(20, ['*'], 'page', $page),
            ]);

        }

    }


    /**
     * In the case where there has already been a search history stored in the session, then the view order from the client has been masked.
     * So we need to unmask it. We can do that by using the result_order array from the search history.
     *
     * @param   int         $view_order     the masked view order number
     * @param   string      $object_type    type of the object
     * @return  int
     */
    private function unMaskViewOrder($view_order, $object_type)
    {
        $result_order = session('employee_search_history')['result_order'];

        // We have to decrease by one, because the $result_order array indexed from zero.
        $view_order--;

        // Proof the edge cases
        if ($view_order < 0)
            return 1;
        if ($view_order >= count($result_order))
            return last($result_order)['true_view_order'];

        $true_view_order = $result_order[$view_order]['true_view_order'];

        return $true_view_order;
    }


    /**
     * Proof the edge cases, for the work location object's view order.
     *
     * @param   int         $position     the position
     * @param   string      $class_name   class name of the object (Well, we all know it's gonna be work location anyway >.<)
     * @return  int
     */
    private function errorProof($position, $class_name)
    {
        $position = ($position < 1) ? 1:$position;
        $position = ($position > $class_name::count()) ? $class_name::count() : $position;
        $position = round($position);

        return $position;
    }
}