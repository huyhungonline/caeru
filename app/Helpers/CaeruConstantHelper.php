<?php

namespace App\Helpers;

use Config;

class CaeruConstantHelper
{
    /**
     * List of all genders
     */
    public static function genders()
    {
        return [
            Config::get('constants.male')           =>      '男',
            Config::get('constants.female')         =>      '女',
        ];
    }

    /**
     * List of all schedule types
     */
    public static function scheduleTypes()
    {
        return [
            Config::get('constants.normal_schedule')               =>      '通常',
            Config::get('constants.monthly_based_schedule')        =>      '1ヶ月単位変形労働',
            Config::get('constants.yearly_based_schedule')         =>      '１年単位変形労働',
            Config::get('constants.flexible_schedule')             =>       'フレックス',
        ];
    }

    /**
     * List of all employment types
     */
    public static function employmentTypes()
    {
        return [
            Config::get('constants.official_employee')          =>      '正社員',
            Config::get('constants.contracted_employee')        =>      '契約社員',
            Config::get('constants.part_time_1_employee')       =>      'パート',
            Config::get('constants.part_time_2_employee')       =>      'アルバイト',
            Config::get('constants.dispatched_employee')        =>      '派遣',
        ];
    }

    /**
     * List of all salary types
     */
    public static function salaryTypes()
    {
        return [
            Config::get('constants.monthly_salary')         =>      '月給',
            Config::get('constants.hourly_salary')          =>      '時給',
            Config::get('constants.daily_salary')           =>      '日給',
        ];
    }

    /**
     * List of all work statuses
     */
    public static function workStatuses()
    {
        return [
            Config::get('constants.working')                =>      '勤務中',
            Config::get('constants.on_vacation')            =>      '休職中',
            Config::get('constants.retired')                =>      '退職済',
        ];
    }

    /**
     * List of all work statuses + 1 more
     * This list is used in employee working information page, and for some reason, it has that last one.
     */
    public static function holidayBonusTypes()
    {
        return [
            Config::get('constants.normal_bonus')                   =>      '一般',
            Config::get('constants.four_days_per_week_bonus')       =>      '週4日勤務',
            Config::get('constants.three_days_per_week_bonus')      =>      '週3日勤務',
            Config::get('constants.two_days_per_week_bonus')        =>      '週2日勤務',
            Config::get('constants.one_day_per_week_bonus')         =>      '週1日勤務',
            6                                                       =>      '手動入力',
        ];
    }


    /**
     * The list default day of week
     */
    public static function dayOfTheWeek()
    {
        return [
            Config::get('constants.monday')                     =>      '月曜日',
            Config::get('constants.tuesday')                    =>      '火曜日',
            Config::get('constants.wednesday')                  =>      '水曜日',
            Config::get('constants.thursday')                   =>      '木曜日',
            Config::get('constants.friday')                     =>      '金曜日',
            Config::get('constants.saturday')                   =>      '土曜日',
            Config::get('constants.sunday')                     =>      '日曜日',
        ];
    }
}
