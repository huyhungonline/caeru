<?php

namespace App;

use App\Reusables\BelongsToWorkLocationTrait;

class ChecklistItem extends Model
{
    use BelongsToWorkLocationTrait;
    /**
     * Constants for types of checklist item
     * 100 打刻エラー
     * 200 要チェックリスト
     */
    const TIMESTAMP_ERROR = 100;
    const CONFIRM_NEEDED = 200;

    /**
     * Constants for types of timestamp errors
     * 101 出勤
     * 102 退勤
     * 103 外出・戻り
     */
    const START_WORK_ERROR      = 101;
    const END_WORK_ERROR        = 102;
    const GO_OUT_RETURN_ERROR   = 103;
    const FORGOT_END_WORK_ERROR = 104;
    const FORGOT_RETURN_ERROR   = 105;

    /**
     * Constants for types of situation where confirmation is needed
     * 201 遅刻.早退
     * 202 時間外
     * 203 形態
     * 204 休憩・外出
     * 205 休出
     * 206 欠勤
     */
    const LATE_OR_LEAVE_EARLY_TYPE  = 201;
    const OFF_SCHEDULE_TIME         = 202;
    const STATUS_MISTAKEN           = 203;
    const OVERLIMIT_BREAK_TIME      = 204;
    const WORK_WITHOUT_SCHEDULE     = 205;
    const HAVE_SCHEDULE_BUT_OFFLINE = 206;

    /**
     * Get the employee working information instance of this checklist item
     */
    public function employeeWorkingInformation()
    {
        return $this->belongsTo(EmployeeWorkingInformation::class);
    }

    /**
     * Get the employee of this checklist item
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    /**
     * Get check list base with error type ( 打刻エラーと要チェックリスト)
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeErrList($query, $errtype=[]) {
        if(!count($errtype)) {
            return $query;  
        }
        return $query
        ->whereIn('checklist_items.error_type', $errtype);

    }

}
