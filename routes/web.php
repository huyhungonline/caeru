<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/{company_code}/', function () {
//     return view('welcome');
// });
Route::get('/{company_code}/testdb', 'CompanyController@testDB');
Route::get('/{company_code}/test', 'ManagerController@test');
// Authenticate routes
Route::get('/{company_code}/login', 'Auth\LoginController@showLoginForm');
Route::post('/{company_code}/login', 'Auth\LoginController@login')->name('login');
Route::get('/{company_code}/logout', 'Auth\LoginController@logout')->name('logout');

// Company routes
Route::get('/{company_code}/', 'CompanyController@dashboard')->name('dashboard');
Route::get('/{company_code}/company', 'CompanyController@edit')->name('edit_company');
Route::post('/{company_code}/company', 'CompanyController@update')->name('update_company');

// Manager routes
Route::get('/{company_code}/managers', 'ManagerController@index')->name('managers_list');
Route::get('/{company_code}/manager', 'ManagerController@create')->name('create_manager');
Route::post('/{company_code}/manager', 'ManagerController@store')->name('store_manager');
Route::get('/{company_code}/manager/{manager}/{page?}', 'ManagerController@edit')->name('edit_manager');
Route::patch('/{company_code}/manager/{manager}/{page?}', 'ManagerController@update')->name('update_manager');

// Work Location routes
Route::get('/{company_code}/work_locations', 'WorkLocationController@index')->name('work_locations_list');
Route::get('/{company_code}/work_location', 'WorkLocationController@create')->name('create_work_location');
Route::post('/{company_code}/work_location', 'WorkLocationController@store')->name('store_work_location');
Route::get('/{company_code}/work_location/{work_location}/{page?}', 'WorkLocationController@edit')->name('edit_work_location');
Route::patch('/{company_code}/work_locat6ion/{work_location}/{page?}', 'WorkLocationController@update')->name('update_work_location');

// Employee routes
Route::get('/{company_code}/employees', 'EmployeeController@index')->name('employees_list');
Route::get('/{company_code}/employee', 'EmployeeController@create')->name('create_employee');
Route::post('/{company_code}/employee', 'EmployeeController@store')->name('store_employee');
Route::get('/{company_code}/employee/{employee}/{page?}', 'EmployeeController@edit')->name('edit_employee');
Route::patch('/{company_code}/employee/{employee}/{page?}', 'EmployeeController@update')->name('update_employee');
Route::get('/{company_code}/employee/work/{employee}/{page?}', 'EmployeeController@editWork')->name('edit_employee_work');
Route::patch('/{company_code}/employee/work/{employee}/{page?}', 'EmployeeController@updateWork')->name('update_employee_work');
Route::post('/{company_code}/employee/search', 'SearchController@searchEmployee')->name('search_employee');
Route::get('/{company_code}/employee_approval/{employee}/{page?}/{return?}', 'ApprovalController@list')->name('employee_approval_list');
Route::get('/{company_code}/employee_approval_reload/{employee}', 'ApprovalController@load')->name('employee_approval_load');
Route::post('/{company_code}/employee_approval_search', 'ApprovalController@search')->name('employee_approval_search');
Route::post('/{company_code}/employee_approval_update', 'ApprovalController@update')->name('employee_approval_update');
Route::post('/{company_code}/employee_approval_move', 'ApprovalController@move')->name('employee_approval_move');
// checklist route
Route::get('/{company_code}/checklists','CheckListController@index')->name('checklists_list');
Route::post('/{company_code}/checklist/search','CheckListSearchController@searchCheckList')->name('search_check_list');
//totalization
Route::get('/{company_code}/totalization','TotalizationController@index')->name('totalization_list');
// Paid holiday routes
Route::get('/{company_code}/paid_holiday', 'PaidHolidayInformationController@index')->name('paid_holiday_list');
Route::get('/{company_code}/paid_holidays/{employee}/{page?}','PaidHolidayInformationController@edit')->name('edit_paid_holiday');
Route::post('/{company_code}/paid_holiday/{employee}/{page?}','PaidHolidayInformationController@update')->name('update_paid_holiday');
// Top page routes
Route::get('/{company_code}/index','HomeController@index')->name('home_page');
// Planned Schedule routes
Route::post('/{company_code}/schedule', 'PlannedScheduleController@store')->name('store_schedule');
Route::post('/{company_code}/schedule/{schedule}', 'PlannedScheduleController@update')->name('update_schedule');
Route::delete('/{company_code}/schedule/{schedule}', 'PlannedScheduleController@destroy')->name('delete_schedule');

// Work address routes
Route::get('/{company_code}/work_addresses', 'WorkAddressController@index')->name('work_address_list');
Route::get('/{company_code}/work_address', 'WorkAddressController@create')->name('create_work_address');
Route::post('/{company_code}/work_address', 'WorkAddressController@store')->name('store_work_address');
Route::get('/{company_code}/work_address/{work_address}/{page?}', 'WorkAddressController@edit')->name('edit_work_address');
Route::patch('/{company_code}/work_address/{work_address}/{page?}', 'WorkAddressController@update')->name('update_work_address');
Route::get('/{company_code}/work_address/detail/{work_address}/{page?}', 'WorkAddressController@editDetail')->name('edit_work_address_detail');
Route::post('/{company_code}/work_address/search', 'SearchController@searchWorkAddress')->name('search_work_address');

// Calendar routes
Route::get('/{company_code}/calendar', 'CalendarController@edit')->name('edit_calendar');
Route::post('/{company_code}/calendar', 'CalendarController@update')->name('update_calendar');
Route::get('/{company_code}/calendar/{year}', 'CalendarController@load')->name('load_calendar');


// Setting routes
Route::get('/{company_code}/setting', 'SettingController@edit')->name('edit_setting');
Route::post('/{company_code}/setting', 'SettingController@update')->name('update_setting');

// Option routes
Route::get('/{company_code}/option_work_rest', 'OptionController@redirectWorkAndRest')->name('edit_option_work_rest');
Route::get('/{company_code}/option_department', 'OptionController@redirectDepartment')->name('edit_option_department');
Route::post('/{company_code}/updateWorkAndRest', 'OptionController@updateWorkAndRest')->name('update_work_rest');
Route::post('/{company_code}/updateDepartments', 'OptionController@updateDepartments')->name('update_department');

// Upload file routes
Route::post('/{company_code}/upload/employee', 'UploadController@employee')->name('upload_employee');

// Change View order route
Route::post('/{company_code}/change_view_order', 'ChangeViewOrderController@changeOrder');

// Chose Work Location route
Route::get('/{company_code}/choose_work_location', 'ChoseWorkLocationController@list')->name('choosing');
Route::get('/{company_code}/choose_work_location/{chosen}/{target?}', 'ChoseWorkLocationController@choose')->name('choose');



/////////////////////////////////// Attendance (WorkingInformation) Tab Routes ///////////////////////////////////////////////////
Route::get('/{company_code}/employee_working_day/{employee_id}/{date}/{list?}/{page?}', 'Attendance\EmployeeWorkingDayController@detail')->name('employee_working_day_detail');
Route::get('/{company_code}/employee_working_day_by_id/{employee_working_day}', 'Attendance\EmployeeWorkingDayController@retrieve')->name('refresh_working_info_list');
Route::post('/{company_code}/employee_working_information', 'Attendance\EmployeeWorkingInformationController@store')->name('create_employee_working_information');
Route::post('/{company_code}/employee_working_information/{employee_working_info}', 'Attendance\EmployeeWorkingInformationController@update')->name('update_employee_working_information');
Route::delete('/{company_code}/employee_working_information/{employee_working_info}', 'Attendance\EmployeeWorkingInformationController@destroy')->name('delete_employee_working_information');
Route::post('/{company_code}/schedule_transfer', 'Attendance\EmployeeWorkingDayController@scheduleTransfer')->name('transfer_schedule');

Route::post('/{company_code}/working_timestamp/{working_day}', 'Attendance\WorkingTimestampController@store')->name('new_timestamp');
Route::patch('/{company_code}/working_timestamp/{working_timestamp}', 'Attendance\WorkingTimestampController@toggleStatus')->name('toggle_status_timestamp');




Route::get('/{company_code}/attendance_working_info', 'Attendance\WorkAddressWorkingController@attendanceinfor');

Route::get('/{company_code}/attendance_working_member/{work_address_working_day}', 'Attendance\WorkAddressWorkingController@attendanceWorkingMember')->name('attendance_working_member');
Route::get('/{company_code}/attendanceinfo', 'Attendance\WorkAddressWorkingController@attendanceinfor');
Route::get('/{company_code}/attendance_working_place/{address_working_id}/{month?}/{year?}', 'Attendance\WorkAddressWorkingController@attendanceplace')->name('attendance_working_place');
Route::get('/{company_code}/get_attendance_place_infor', 'Attendance\WorkAddressWorkingController@getAttendancePlaceByTime');
Route::get('/{company_code}/save_attendance_place_infor', 'Attendance\WorkAddressWorkingController@saveAttendancePlaceInfor');
Route::get('/{company_code}/cancel_attendance_place_infor', 'Attendance\WorkAddressWorkingController@cancelWorkingPlaceEvent');
Route::get('/{company_code}/insert_employee_working_address', 'Attendance\WorkAddressWorkingController@insertEmployeeAddressWorking');
Route::get('/{company_code}/save_break_time_infor', 'Attendance\WorkAddressWorkingController@saveBreakTimeInfor');
Route::get('/{company_code}/get_working_address_infor', 'Attendance\WorkAddressWorkingController@getWorkingAddressInfor');
Route::get('/{company_code}/save_working_address_infor', 'Attendance\WorkAddressWorkingController@saveAddressWorkingInfor');
Route::get('/{company_code}/attendance_address_infor', 'Attendance\WorkAddressWorkingController@attendance_work_infor')->name('attendance_address_infor');
Route::get('/{company_code}/attendance_address_infor_day_API', 'Attendance\WorkAddressWorkingController@getDayInfor');
Route::post('/{company_code}/attendance_address_infor_API', 'Attendance\WorkAddressWorkingController@attendance_work_infor_API');
Route::get('/{company_code}/attendance_address_infor_get_calender', 'Attendance\WorkAddressWorkingController@getCalender');


// shounin
Route::get('/{company_code}/request_page', 'Attendance\RequestController@requestPage');
Route::get('/{company_code}/request/{employee_id}', 'Attendance\RequestController@person_detail');
Route::post('/{company_code}/save_request_page', 'Attendance\RequestController@save_request_page');



// Sinsei manager routes
Route::group(['prefix' => '/{company_code}/sinsei'], function () {
	//-- Login
	Route::get('/login', 'Sinsei\Login\LoginSinseiController@showLoginPage')->name('ss_show_login');
	Route::post('/login', 'Sinsei\Login\LoginSinseiController@login')->name('ss_login');
	Route::get('/logout', 'Sinsei\Login\LoginSinseiController@logout')->name('ss_logout');
	
	//-- Manager
	Route::get('/manager/list', 'Sinsei\Manager\ManagerController@lists')->name('manager_list');
	Route::get('/manager/detail', 'Sinsei\Manager\ManagerController@detail')->name('managers_detail');
	Route::get('/manager/detail_flextime', 'Sinsei\Manager\ManagerController@detailFlextime')->name('manager_detail_flextime');
	//-- Personal
	Route::get('/personal/personal_account', 'Sinsei\Personal\PersonalController@account')->name('personal_account');
	Route::get('/personal/detail', 'Sinsei\Personal\PersonalController@detail')->name('personal_detail');
	Route::get('/personal/detail_flextime', 'Sinsei\Personal\PersonalController@detailFlextime')->name('personal_detail_flextime');
	//-- Request
	Route::get('/request/applying', 'Sinsei\Request\RequestController@applying')->name('request_applying');
	Route::get('/request/applying_flextime', 'Sinsei\Request\RequestController@applyingFlextime')->name('applying_flextime');
	Route::get('/request/approval', 'Sinsei\Request\RequestController@approval')->name('approval');
	Route::get('/request/approval_flextime', 'Sinsei\Request\RequestController@approvalFlextime')->name('approval_flextime');
	Route::get('/request/index', 'Sinsei\Request\RequestController@index')->name('request_index');
	Route::get('/request/request_flextime', 'Sinsei\Request\RequestController@requestFlextime')->name('request_flextime');
	Route::get('/request/request_rejection', 'Sinsei\Request\RequestController@requestRejection')->name('request_rejection');
	Route::get('/request/request_rejection_flextime', 'Sinsei\Request\RequestController@requestRejectionFlextime')->name('request_rejection_flextime');
	//-- Confirm
	Route::get('/confirm/confirm_page', 'Sinsei\Confirm\ConfirmController@confirm')->name('confirm_page');
	Route::get('/confirm/confirm_multiple', 'Sinsei\Confirm\ConfirmController@confirmMultiple')->name('confirm_multiple');
	Route::get('/confirm/confirm_overtime_flextime', 'Sinsei\Confirm\ConfirmController@confirmOvertimeFlextime')->name('confirm_overtime_flextime');
	Route::get('/confirm/confirm_flextime', 'Sinsei\Confirm\ConfirmController@confirmFlextime')->name('confirm_flextime');
});