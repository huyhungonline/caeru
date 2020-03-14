<?php

namespace App\Http\Requests\Reusables;

use App\Reusables\JapaneseYearHandler as Year;

class PostValidationProcesses
{
    /**
     * Merge the 3 parts of the telephone number into one
     *
     * @param mix   $request
     * @return void
     */
    public static function telephone($request)
    {

        $data = $request->only(
            'telephone_1',
            'telephone_2',
            'telephone_3'
        );

        if ($data['telephone_1'] || $data['telephone_2'] || $data['telephone_3']) {
            $data['telephone'] = $data['telephone_1'] . '-' . $data['telephone_2'] . '-' . $data['telephone_3'];
        } else {
            $data['telephone'] = null;
        }

        $request->merge($data);

    }

    /**
     * Merge the 3 parts of the fax number into one
     *
     * @param mix   $request
     * @return void
     */
    public static function fax($request)
    {

        $data = $request->only(
            'fax_1',
            'fax_2',
            'fax_3'
        );

        if ($data['fax_1'] || $data['fax_2'] || $data['fax_3']) {
            $data['fax'] = $data['fax_1'] . '-' . $data['fax_2'] . '-' . $data['fax_3'];
        } else {
            $data['fax'] = null;
        }
        
        $request->merge($data);

    }

    /**
     * Merge the 2 parts of the post number into one
     *
     * @param mix   $request
     * @return void
     */
    public static function postalCode($request)
    {

        $data = $request->only(
            'postal_code_1',
            'postal_code_2'
        );
        $data['postal_code'] = $data['postal_code_1'] . $data['postal_code_2'];

        $request->merge($data);

    }

    /**
     * Merge the 3 parts of the date field into one
     *
     * @param mix       $request
     * @param string    $field_name
     * @return void
     */
    public static function threePartsDate($request, $field_name)
    {

        $data = $request->only(
            $field_name . '_1',
            $field_name . '_2',
            $field_name . '_3'
        );

        if ($data[$field_name . '_1'] || $data[$field_name . '_2'] || $data[$field_name . '_3']) {
            $data[$field_name] = Year::toNormalYear($data[$field_name . '_1']) . '-' . intval($data[$field_name . '_2']) . '-' . intval($data[$field_name . '_3']);
        } else {
            $data[$field_name] = null;
        }

        $request->merge($data);

    }

    /**
     * If the boolean is true then the holiday_bonus_type field will have the first item of the config array.
     *
     * @param mix       $request
     * @return void
     */
    public static function paidHolidayTypes($request)
    {
        $the_boolean = $request->input('holiday_bonus_type_extra');

        if ($the_boolean) $request->merge(['holiday_bonus_type' => config('constants.normal_bonus')]);
    }

    /**
     * If the inputed time's value is of 'hhmm' format, then split it up and reformat into a correct format that can be persisted into the database.
     * This value is after validation so you dont have to consider too much.
     *
     * @param mix       $request
     * @return void
     */
    public static function time($request, $field_name)
    {
        $time = explode(':', $request->input($field_name));

        if ((count($time) == 1) && (strlen($time[0]) == 4)) {
            $request->merge([
                $field_name => substr($time[0], 0, 2) . ':' . substr($time[0], -2)
            ]);
        }

    }
}