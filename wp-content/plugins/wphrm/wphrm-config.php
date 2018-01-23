<?php
if ( ! defined( 'ABSPATH' ) ) exit;
define('WPHRM_TABLE_PREFIX', $wpdb->prefix);
class WPHRMConfig {
    public $WphrmSalaryTable = 'wphrm_salary';
    public $WphrmWeeklySalaryTable = 'wphrm_weekly_salary';
    public $WphrmEmployeeLevelTable = 'wphrm_employee_level';
    public $WphrmMessagesTable = 'wphrm_messages';
    public $WphrmSettingsTable = 'wphrm_settings';
    public $WphrmHolidaysTable = 'wphrm_holidays';
    public $WphrmFinancialsTable = 'wphrm_financials';
    public $WphrmDesignationTable = 'wphrm_designation';
    public $WphrmDepartmentTable = 'wphrm_department';
    public $WphrmAttendanceTable = 'wphrm_attendance';
    public $WphrmLeaveRulesTable = 'wphrm_leave_rules';
    public $WphrmLeaveApplicationTable = 'wphrm_leave_application';
    public $WphrmLeaveTypeTable = 'wphrm_leavetypes';
    public $WphrmNotificationsTable = 'wphrm_notifications';
    public $WphrmNoticeTable = 'wphrm_notice';
    public $WphrmCurrencyTable = 'wphrm_currency';
    public $WphrmInvitationAttendeeTable = 'wphrm_invitation_attendee';
    public $wphrmGetAdminId;
    protected $WPHRMREPORTS, $WPHRMAJAXDATAS;

    /*J@F*/
    public $WphrmAttendanceFileTable = 'sphr_wphrm_attendance_files';
    /*J@F END*/

    public $wphrmDefineCapability = array(
        'manageOptionsDashboard' => 'Dashboard',
        'manageOptionsDepartment' => 'Departments',
        'manageOptionsEmployee' => 'Employees',
        'manageOptionsHolidays' => 'Holidays',
        'manageOptionsAttendances' => 'Attendances',
        'manageOptionsLeaveApplications' => 'Leave Applications',
        'manageOptionsSalary' => 'Salary',
        'manageOptionsNotice' => 'Notices',
        'manageOptionsFinancials' => 'Financials',
        'manageOptionsNotifications' => 'Notifications',
        'manageOptionsSettings' => 'Settings',
        'manageOptionsFbGroup' => 'Support Desk'
                                        );

    public $wphrmUserDefineCapability = array(
        'manageOptionsDashboard' => 'Dashboard',
        'manageOptionsDepartment' => 'Departments',
        'manageOptionsEmployee' => 'Employees',
        'manageOptionsHolidays' => 'Holidays',
        'manageOptionsAttendances' => 'Attendances',
        'manageOptionsLeaveApplications' => 'Leave Applications',
        'manageOptionsSalary' => 'Salary',
        'manageOptionsNotice' => 'Notices',
        /*'manageOptionsFinancials' => 'Financials',*/
        'manageOptionsNotifications' => 'Notifications',
        'manageOptionsSettings' => 'Settings',
        /*'manageOptionsFbGroup' => 'Support Desk',*/
        'manageOptionsEmployeeView' => 'Employees',
        'manageOptionsHolidayView' => 'Holidays',
        'manageOptionsAttendancesView' => 'Attendances',
        'manageOptionsSalaryView' => 'Salary',
        'manageOptionsDashboardView' => 'Dashboard',
        'manageOptionsNoticeView' => 'Notices',
        'manageOptionsFileDocuments' => 'Files & Documents'
     );

    public $wphrmUserCapabilities = array(
        'manageOptionsEmployeeView',
        'manageOptionsHolidayView',
        'manageOptionsAttendancesView',
        'manageOptionsSalaryView',
        'manageOptionsDashboardView',
        'manageOptionsLeaveApplicationsView',
        'manageOptionsNoticeView',
        'manageOptionsFileDocumentsView',
    );

    public $wphrmCheckPages = array(
        'manageOptionsDepartment',
        /*'manageOptionsFinancials',*/
        'manageOptionsSettings',
        'manageOptionsLeaveApplications',
        'manageOptionsNotifications',
        /*'manageOptionsFbGroup',*/
        'manageOptionsFileDocuments'
    );

    public $wphrm_month = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");


}
