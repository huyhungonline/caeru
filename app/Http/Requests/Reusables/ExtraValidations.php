<?php

namespace App\Http\Requests\Reusables;

use Illuminate\Validation\Rule;
use App\WorkLocation;
use App\CalendarRestDay;
use App\Reusables\JapaneseYearHandler as Year;

class ExtraValidations
{

    /**
     * Validation for todofuken fields
     *
     * @param \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public static function todofuken($validator)
    {

        $todofuken_list = \DB::table('todofukens')->pluck('id')->toArray();

        $validator->addRules(['todofuken'=>  Rule::in($todofuken_list)]);

    }

    /**
     * Extra validation for work location select field in the employ detail page
     *
     * @param \Illuminate\Validation\Validator  $validator
     * @param \App\Manager                      $user
     * @return void
     */
    public static function currentWorkLocation($validator, $user)
    {
        $chosen_work_location = session('current_work_location');
        $list = null;
        if ($chosen_work_location === 'all') {
            $list = $user->company->workLocations->pluck('id')->toArray();
        } elseif (!is_array($chosen_work_location)) {
            $list = [$chosen_work_location];
        } else {
            $list = $chosen_work_location;
        }
        $validator->addRules(['work_location_id' => [
            'required',
            Rule::in($list),
        ]]);
    }

    /**
     * Extra validation for deparment select field in the employ detail page
     *
     * @param \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public static function validDepartment($validator)
    {
        $work_location = WorkLocation::find($validator->getData()['work_location_id']);
        $departments = [];
        if($work_location) $departments = $work_location->activatingDepartments()->pluck('id')->toArray();

        $validator->addRules(['department_id' =>
            'nullable',
            Rule::in($departments)
        ]);
    }

    /**
     *  Extra validation for date field type that has been separated into three parts
     *
     * @param \Illuminate\Validation\Validator  $validator
     * @param string                            $field_name
     * @return void
     */
    public static function threePartsDate($validator, $field_name)
    {

        // validation for 30-days-months
        $validator->sometimes($field_name . '_3', 'max:30', function ($input) use ($field_name) {
            $months_with_30_days = [ 4, 6, 9, 11];
            return in_array(intval($input->get($field_name . '_2')), $months_with_30_days);
        });

        // validation for february
        $validator->sometimes($field_name . '_3', 'max:29', function ($input) use ($field_name) {
            return (intval($input->get($field_name . '_2')) == 2) && self::checkIfLeapYear(Year::toNormalYear($input->get($field_name . '_1')));
        });
        $validator->sometimes($field_name . '_3', 'max:28', function ($input) use ($field_name) {
            return (intval($input->get($field_name . '_2')) == 2) && !self::checkIfLeapYear(Year::toNormalYear($input->get($field_name . '_1')));
        });

    }

    /**
     *  Extra validation for date(month/day) field type including leap day
     *
     * @param mix                               $request
     * @param \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public static function dateMonthType($request, $validator, $field_name)
    {
        // change the input string into 0004/mm/dd
        $input_data = $validator->getData();

        $input_data[$field_name] = $input_data[$field_name] ? '0004/' . $input_data[$field_name] : $input_data[$field_name];

        $validator->setData($input_data);

        // Add validation rule
        $validator->addRules([$field_name => 'date']);
    }

    /**
     * The custom validation rule to check if the year is a valid year or not, japanese year format included.
     *
     * @param string                            $attribute
     * @param mix                               $value
     * @param array                             $paramaters
     * @param \Illuminate\Validation\Validator  $validator
     * @return boolean
     */
    public function year($attribute, $value, $parameters, $validator)
    {
        return $this->checkIfValidYear($value);
    }

    /**
     * The custom validation rule to check if the data is valid for a calendar rest day model.
     *
     * @param string                            $attribute
     * @param mix                               $value
     * @param array                             $paramaters
     * @param \Illuminate\Validation\Validator  $validator
     * @return boolean
     */
    public function restDay($attribute, $value, $parameters, $validator)
    {
        if ($value) {
            $date = explode('-', $value['day']);
        } else {
            return false;
        }

        $available_types = [
            CalendarRestDay::LAW_BASED_REST_DAY,
            CalendarRestDay::NORMAL_REST_DAY,
            CalendarRestDay::NOT_A_REST_DAY,
        ];

        return ($value['day'] !== null) && ($value['type'] !== null) && checkdate($date[1], $date[2], $date[0]) && in_array($value['type'], $available_types);
    }

    /**
     * The custom validation rule to check if the data is valid for a calendar work time model.
     *
     * @param string                            $attribute
     * @param mix                               $value
     * @param array                             $paramaters
     * @param \Illuminate\Validation\Validator  $validator
     * @return boolean
     */
    public function workTime($attribute, $value, $parameters, $validator)
    {
        if ($value) {
            $date = explode('-', $value['month']);
        } else {
            return false;
        }

        return ($value['month'] !== null) && ($value['time'] !== null) && checkdate($date[1], 1, $date[0]) && is_numeric($value['time']);
    }

    /**
     * The custom validation rule to validate time field.
     *
     * @param string                            $attribute
     * @param mix                               $value
     * @param array                             $paramaters
     * @param \Illuminate\Validation\Validator  $validator
     * @return boolean
     */
    public function time($attribute, $value, $parameters, $validator)
    {
        return is_string($value) && ($this->getValidTimeFromString($value) !== false);
    }

    /**
     * The custom validation rule to check the working hour attribute of the PlannedSchedule model.
     *
     * @param string                            $attribute
     * @param mix                               $value
     * @param array                             $paramaters
     * @param \Illuminate\Validation\Validator  $validator
     * @return boolean
     */
    public function workingHour($attribute, $value, $parameters, $validator)
    {
        $start_time = $this->getValidTimeFromString($validator->getData()['start_work_time']);

        $end_time = $this->getValidTimeFromString($validator->getData()['end_work_time']);

        // If the end_time is smaller the the start_time, then it's from the next day;
        $end_time = ($end_time < $start_time) ? strtotime('+1 day', $end_time) : $end_time;

        $break_time = intval($validator->getData()['break_time']);

        if ($start_time !== false && $end_time !== false) {
            $working_minutes = ($end_time - $start_time)/60 - $break_time;

            $working_hour = floor($working_minutes/60) . ':' . str_pad($working_minutes%60, 2, '0', STR_PAD_LEFT);

            return strtotime('today ' . $working_hour) === strtotime('today ' . $value);
        } else {
            return true;
        }
    }

    /**
     * get a valid time from a string.
     *
     * @param   string    $string
     * @return  boolean
     */
    private function getValidTimeFromString($string)
    {
        $data = explode(':', $string);

        if (count($data) >= 2) {

            $hour = intval($data[0]);
            $minute = intval($data[1]);

            if ((ctype_digit($data[0])) && (0 <= $hour) && ($hour < 24) && (ctype_digit($data[1])) && (0 <= $minute) && ($minute < 60)) {
                return strtotime('today ' . $hour . ':' . $minute . ':0');
            }
        } elseif ((count($data) == 1) && (strlen($data[0]) == 4)) {

            $hour = intval(substr($data[0], 0, 2));
            $minute = intval(substr($data[0], -2));

            if ((ctype_digit(substr($data[0], 0, 2))) && (0 <= $hour) && ($hour < 24) && (ctype_digit(substr($data[0], -2))) && (0 <= $minute) && ($minute < 60)) {
                return strtotime('today ' . $hour . ':' . $minute . ':0');
            }
        }
        return false;
    }

    /**
     * Check if the input string is a valid year.
     *
     * @param   string    $string
     * @return  boolean
     */
    private function checkIfValidYear($string)
    {
        // It must be either a normal year or a japanese year
        return (preg_match('/^((18\d{2})|(19\d{2})|(20\d{2})|(21\d{2}))$/', $string) === 1) || (Year::isValidJapaneseYear($string));
    }

    /**
     * Check if the input year is a leap year
     *
     * @param int   $year
     * @return boolean
     */
    private static function checkIfLeapYear($year)
    {
        return (($year % 400) == 0) || (($year % 4) == 0) && (($year % 100) != 0);
    }
}