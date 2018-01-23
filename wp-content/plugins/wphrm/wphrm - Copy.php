<?php
/**
 * Plugin Name: WP HRM
 * Plugin URI: https://indigothemes.com/products/wp-hrm-wordpress-plugin/
 * Description: WPHRM is a WordPress plugin for Human resource management of small and medium sized companies. Using WPHRM, companies can easily manage Employee data, Employees salary data, Notices, Holidays, Leaves, Expenses etc.
 * Version: 1.4
 * Author: IndigoThemes
 * Author URI: https://indigothemes.com
 * Text Domain: wphrm
 */
if (!defined('ABSPATH'))
    exit;
require_once('wphrm-config.php');
require_once('wphrm-reports.php');
require_once('wphrm-ajax-data.php');
require_once('library/UploadHandler.php');

Class WPHRM extends WPHRMConfig {
    // Define Global Variables and Other Private and Protected Variables.
    // Constructor
    function __construct() {
        define('WPHRMPDF', dirname(__FILE__) . '/library/wphrmpdf/');
        define('WPHRMLIB', dirname(__FILE__) . '/library/wphrm/');
        define('WPHRMXMLLIB', dirname(__FILE__) . '/library/databasefiles/');
        register_activation_hook(__FILE__, array(&$this, 'WPHRMPluginActivation'));
        $this->WphrmSalaryTable = WPHRM_TABLE_PREFIX.$this->WphrmSalaryTable;
        $this->WphrmWeeklySalaryTable = WPHRM_TABLE_PREFIX.$this->WphrmWeeklySalaryTable;
        $this->WphrmEmployeeLevelTable = WPHRM_TABLE_PREFIX.$this->WphrmEmployeeLevelTable;
        $this->WphrmMessagesTable = WPHRM_TABLE_PREFIX.$this->WphrmMessagesTable;
        $this->WphrmSettingsTable = WPHRM_TABLE_PREFIX.$this->WphrmSettingsTable;
        $this->WphrmHolidaysTable = WPHRM_TABLE_PREFIX.$this->WphrmHolidaysTable;
        $this->WphrmFinancialsTable = WPHRM_TABLE_PREFIX.$this->WphrmFinancialsTable;
        $this->WphrmDesignationTable = WPHRM_TABLE_PREFIX.$this->WphrmDesignationTable;
        $this->WphrmDepartmentTable = WPHRM_TABLE_PREFIX.$this->WphrmDepartmentTable;
        $this->WphrmAttendanceTable = WPHRM_TABLE_PREFIX.$this->WphrmAttendanceTable;
        $this->WphrmLeaveApplicationTable = WPHRM_TABLE_PREFIX.$this->WphrmLeaveApplicationTable;
        $this->WphrmLeaveRulesTable = WPHRM_TABLE_PREFIX.$this->WphrmLeaveRulesTable;
        $this->WphrmLeaveTypeTable = WPHRM_TABLE_PREFIX.$this->WphrmLeaveTypeTable;
        $this->WphrmNotificationsTable = WPHRM_TABLE_PREFIX.$this->WphrmNotificationsTable;
        $this->WphrmNoticeTable = WPHRM_TABLE_PREFIX.$this->WphrmNoticeTable;
        $this->WphrmCurrencyTable = WPHRM_TABLE_PREFIX.$this->WphrmCurrencyTable;
        $this->WphrmInvitationAttendeeTable = WPHRM_TABLE_PREFIX.$this->WphrmInvitationAttendeeTable;
        add_action('init', array(&$this, 'WPHRMTextDomain'));
        add_action('admin_init', array(&$this, 'WPHRMAssets'));
        add_action('admin_menu', array(&$this, 'WPHRMMenu'));
        add_action('wp_login', array(&$this, 'WPHRMLoginUser'), 10, 2);
        add_filter('login_message', array(&$this, 'WPHRMUserLoginMessage'));

        /** event shedule * */
        add_filter('cron_schedules', array(&$this, 'WPHRMAddEventInterval'));
        if (!wp_next_scheduled('WPHRMUpdateCheckEventAction')) {
            wp_schedule_event(time(), 'everyMinutes', 'WPHRMUpdateCheckEventAction');
        }
        add_action('WPHRMUpdateCheckEventAction', array(&$this, 'WPHRMGenerateDefaultSalary'));

        register_deactivation_hook(__FILE__, array(&$this, 'WPHRMPluginDeactive'));
        $this->wphrmGetAdminId();
        $wphrmReporting = new WPHRMReporting($this);
        $wphrmAjaxDatas = new WPHRMAjaxDatas($this);
        $this->WPHRMREPORTS = $wphrmReporting;
        $this->WPHRMAJAXDATAS = $wphrmAjaxDatas;

        /*J@F*/
        add_action( 'wpmu_new_blog', array($this, 'new_site_created'), 10, 6);

        if(class_exists('WPHRM_Annual_Leave')) $this->wphrm_annual_leave = new WPHRM_Annual_Leave();
        add_action('wp_ajax_wphrm_employee_work_permit_form', array($this, 'wphrm_employee_work_permit_form') );
        add_action('wp_ajax_calculate_unused_annual_leave', array($this, 'calculate_unused_annual_leave') );
        add_filter('after_calculate_leave_total', array($this, 'get_total_leave_used'), 10, 3 );
        add_filter('employee_attendance_after_calculate_leave_total', array($this, 'get_total_leave_used'), 10, 3 );
        add_action('wp_ajax_WPHRMGetLeaveTypeInfo', array($this, 'WPHRMGetLeaveTypeInfo'));
        add_action('wp_ajax_wphrm_get_leave_limit', array($this, 'wphrm_get_leave_limit'));
        add_action('wp_ajax_wphrm_remove_weekends', array($this, 'wphrm_remove_weekends'));
        add_action('after_leave_type_insert', array($this, 'wphrm_add_new_user_leave_type'));
        add_action('wp_ajax_wphrm_disable_leave_dates', array($this, 'wphrm_disable_leave_dates'));
        add_action('wp_ajax_wphrm_get_email_notification_details', array($this, 'wphrm_get_email_notification_details'));
        add_action('wp_ajax_wphrm_update_email_notication', array($this, 'wphrm_update_email_notication'));
        add_action('admin_head', array($this, 'custom_script_for_admin'));
    }
    function new_site_created($blog_id, $user_id, $domain, $path, $site_id, $meta ) {
        global $wpdb;
        if (is_plugin_active_for_network('wphrm/wphrm.php')) {
            $old_blog = $wpdb->blogid;
            $show_on_front = get_option('show_on_front', 'posts');
            $page_on_front = get_option('page_on_front', 0);
            $all_meta = array();
            $oldpost = null;
            if($page_on_front){
                $post = get_post($page_on_front);
                $oldpost    = array(
                    'post_status' => 'publish',
                    'post_type' => $post->post_type,
                    'post_title' => $post->post_title,
                    'post_content' => $post->post_content,
                    'post_author' => $user_id
                );
                $all_meta = get_post_custom($page_on_front);
            }
            switch_to_blog($blog_id);

            $this->WPHRMPluginActivation(true);

            if($show_on_front == 'page'){
                $new_post_id = wp_insert_post($oldpost);
                update_option('show_on_front', 'page');
                update_option('page_on_front', $new_post_id);
                foreach ( $all_meta as $key => $values) {
                    foreach ($values as $value) {
                        add_post_meta( $new_post_id, $key, $value );
                    }
                }
            }
            switch_to_blog($old_blog);
        }
    }
    public function WPHRMAssets() {
        wp_enqueue_style('notification-css', plugins_url('assets/css/notification.css', __FILE__), '');
        wp_enqueue_style('wphrm-font-css', plugins_url('assets/css/font-wphrm.css', __FILE__), ''); // for the wp-hrm icon
        wp_enqueue_script('wphrm-notification-js', plugins_url('assets/js/wphrm-notification.js', __FILE__), '');
        wp_register_style('wphrm-font-awesome-css', plugins_url('assets/css/font-awesome.min.css', __FILE__), '');
        wp_register_style('wphrm-bootstrap-switch-css', plugins_url('assets/css/bootstrap-switch.css', __FILE__), '');
        wp_register_style('wphrm-bootstrap-min-css', plugins_url('assets/css/bootstrap.min.css', __FILE__), '');
        wp_register_style('wphrm-datepicker3-css', plugins_url('assets/css/datepicker3.css', __FILE__), '');
        wp_register_style('wphrm-fullcalendar-css', plugins_url('assets/css/fullcalendar.min.css', __FILE__), '');
        wp_register_style('wphrm-dataTables-bootstrap-css', plugins_url('assets/css/dataTables.bootstrap.css', __FILE__), '');
        wp_register_style('wphrm-css', plugins_url('assets/css/wphrm.css', __FILE__), array(), '');
        wp_register_script('wphrm-bootstrap-fileinput-js', plugins_url('assets/js/bootstrap-fileinput.js', __FILE__), '');
        wp_register_script('wphrm-bootstrap-datepicker-js', plugins_url('assets/js/bootstrap-datepicker.js', __FILE__), '');
        wp_register_script('wphrm-jscolor-js', plugins_url('assets/js/jscolor.js', __FILE__), '');
        wp_register_script('wphrm-jquery-validate-js', plugins_url('assets/js/jquery.validate.min.js', __FILE__), '');
        wp_register_script('wphrm-additional-methods-js', plugins_url('assets/js/additional-methods.min.js', __FILE__), '');
        wp_register_script('wphrm-bootstrap-min-js', plugins_url('assets/js/bootstrap.min.js', __FILE__), '');
        wp_register_script('wphrm-jquery-dataTables-min-js', plugins_url('assets/js/jquery.dataTables.min.js', __FILE__), '');
        wp_register_script('wphrm-dataTables-bootstrap-js', plugins_url('assets/js/dataTables.bootstrap.js', __FILE__), '');
        wp_register_script('wphrm-bootstrap-switch-js', plugins_url('assets/js/bootstrap-switch.js', __FILE__), '');
        wp_register_script('wphrm-custom-js', plugins_url('assets/js/wphrm.js', __FILE__), '');

        wp_register_script('wphrm-bic-calendar-js', plugins_url('assets/js/bic_calendar.js', __FILE__), '');
        wp_register_script('wphrm-sol-js', plugins_url('assets/js/sol.js', __FILE__), '');
        wp_register_script('wphrm-graph-js', plugins_url('assets/js/wphrm-graph.js', __FILE__), '');
        wp_register_script('wphrm-fullcalender-js', plugins_url('assets/js/fullcalendar.min.js', __FILE__), '');
        wp_register_script('wphrm-attendance-calenader-js', plugins_url('assets/js/attendance_calendar.js', __FILE__), '');
        wp_register_script('wphrm-dashboard-calenader-js', plugins_url('assets/js/dashboard_calendar.js', __FILE__), '');
        wp_register_script('wphrm-holiday-year-js', plugins_url('assets/js/year-holiday.js', __FILE__), '');

        /*J@F*/
        wp_deregister_script('wphrm-jquery-dataTables-min-js');
        wp_register_script('wphrm-jquery-dataTables-min-js', '//cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js', array('jquery'));
        wp_deregister_style('wphrm-dataTables-bootstrap-css');
        wp_register_style('wphrm-dataTables-bootstrap-css', '//cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css', null, null );
        wp_register_style('wphrm-jaf-dt-bootstrap-buttons', '//cdn.datatables.net/buttons/1.2.4/css/buttons.bootstrap.min.css', array('wphrm-dataTables-bootstrap-css'), null );
        wp_register_style('wphrm-jaf-dt-buttons', '//cdn.datatables.net/buttons/1.2.4/css/buttons.jqueryui.min.css', array('wphrm-dataTables-bootstrap-css'), null );
        wp_register_style('wphrm-file-upload', '//cdn.datatables.net/buttons/1.2.4/css/buttons.jqueryui.min.css', array('wphrm-dataTables-bootstrap-css'), null );
        wp_register_script('wphrm-jaf-dt-buttons', '//cdn.datatables.net/buttons/1.2.4/js/dataTables.buttons.min.js', array('wphrm-jquery-dataTables-min-js') );
        wp_register_script('wphrm-jaf-dt-jszip', '//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js', array('wphrm-jquery-dataTables-min-js') );
        wp_register_script('wphrm-jaf-dt-pdfmake', '//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.27/pdfmake.min.js', array('wphrm-jquery-dataTables-min-js') );
        wp_register_script('wphrm-jaf-dt-vfs_fonts', '//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.27/vfs_fonts.js', array('wphrm-jquery-dataTables-min-js') );
        wp_register_script('wphrm-jaf-dt-html5-button', '//cdn.datatables.net/buttons/1.2.4/js/buttons.html5.min.js', array('wphrm-jquery-dataTables-min-js') );
        wp_register_script('wphrm-jaf-dt-colVis', '//cdn.datatables.net/buttons/1.2.4/js/buttons.colVis.min.js', array('wphrm-jquery-dataTables-min-js') );
        wp_register_script('wphrm-jaf-dt-print', '//cdn.datatables.net/buttons/1.0.3/js/buttons.print.min.js', array('wphrm-jquery-dataTables-min-js') );
        wp_register_style('wphrm-fileupload', plugins_url('assets/css/jquery.fileupload.css', __FILE__) );
        wp_register_script('wphrm-jquery-ui-widget', plugins_url('assets/js/jquery.ui.widget.js', __FILE__), array('jquery') );
        wp_register_script('wphrm-fileupload', plugins_url('assets/js/jquery.fileupload.js', __FILE__), array('jquery') );
        wp_register_script('wphrm-jaf-custom-js', plugins_url('assets/js/jaf-wphrm.js', __FILE__), '');
        /*J@F*/
    }

    public function WPHRMEnqueues() {
        wp_enqueue_media();
        wp_enqueue_style('wphrm-font-awesome-css');
        wp_enqueue_style('wphrm-bootstrap-switch-css');
        wp_enqueue_style('wphrm-bootstrap-min-css');
        wp_enqueue_style('wphrm-datepicker3-css');
        wp_enqueue_style('wphrm-css');
        wp_enqueue_script('wphrm-bootstrap-fileinput-js');
        wp_enqueue_script('wphrm-bootstrap-datepicker-js');
        wp_enqueue_script('wphrm-jscolor-js');
        wp_enqueue_script('wphrm-jquery-validate-js');
        wp_enqueue_script('wphrm-additional-methods-js');
        wp_enqueue_script('wphrm-bootstrap-min-js');
        wp_enqueue_script('wphrm-jquery-dataTables-min-js');
        wp_enqueue_script('wphrm-dataTables-bootstrap-js');
        wp_enqueue_script('wphrm-bootstrap-switch-js');
        wp_enqueue_script('wphrm-sol-js');
        wp_enqueue_script('wphrm-custom-js');
        wp_localize_script('wphrm-custom-js', 'WPHRMCustomJS', array('records' => __('Records :', 'wphrm'), 'Deletemsg' => __('Are you sure you went to delete?', 'wphrm'), 'occasion' => __('*Must enter the Occasion', 'wphrm'), 'designationName' => __('Designation Name', 'wphrm'), 'departmentName' => __('Department Name', 'wphrm')
                                                                     , 'bankfieldlabel' => __('Bank Field Label', 'wphrm'), 'fileError' => __('Please select a database xml file.', 'wphrm'), 'otherfieldlabel' => __('Other Field Label', 'wphrm'), 'documentfieldlabel' => __('Document Field Label', 'wphrm'), 'salaryfieldlabel' => __('Salary Field Label', 'wphrm'), 'earninglabel' => __('Earning Label', 'wphrm'), 'deductionlabel' => __('Deduction Label', 'wphrm')));
        wp_localize_script('wphrm-jquery-dataTables-min-js', 'WPHRMJS', array('sSearch' => __('Search :', 'wphrm'), 'sSortAscending' => __(': activate to sort column ascending', 'wphrm'), 'sSortDescending' => __(': activate to sort column descending', 'wphrm'), 'sFirst' => __('First', 'wphrm'), 'sLast' => __('Last', 'wphrm')
                                                                              , 'sNext' => __('Next :', 'wphrm'), 'sPrevious' => __('Previous', 'wphrm'), 'sEmptyTable' => __('No data available in table', 'wphrm'), 'sInfo' => __('Showing ', 'wphrm'), 'of' => __('of', 'wphrm'), 'to' => __('to', 'wphrm'), 'entries' => __('entries', 'wphrm'), 'sInfoEmpty' => __('Showing 0 to 0 of 0 entries', 'wphrm'), 'totalentries' => __('total entries', 'wphrm'), 'filteredfrom' => __('filtered from', 'wphrm'), 'sLoadingRecords' => __('Loading...', 'wphrm')
                                                                              , 'sProcessing' => __('Processing...', 'wphrm'), 'sZeroRecords' => __('No matching records found', 'wphrm')));

        wp_localize_script('wphrm-fullcalender-js', 'WPHRMDashboardJS', array('today' => __('Today', 'wphrm'),
                                                                              'monthtitle' => __('MMMM YYYY', 'wphrm'), 'monthday' => __('ddd', 'wphrm'),));

        wp_localize_script('wphrm-sol-js', 'WPHRMSol', array('noItemsAvailable' => __('No entries found', 'wpttar'), 'selectAll' => __('Select all', 'wpttar'), 'selectNone' => __('Select none', 'wpttar'), 'quickDelete' => __('times', 'wpttar'), 'searchplaceholder' => __('Click here to search', 'wpttar')
                                                             , 'loadingData' => __('Still loading data...', 'wpttar'), 'itemsSelected' => __('items selected', 'wpttar'), 'itemsattribute' => __('name attribute is required', 'wpttar')));


        /*J@F*/
        wp_enqueue_style('jaf-wphrm-css');
        wp_enqueue_style('wphrm-dataTables-bootstrap-css');
        wp_enqueue_style('wphrm-jaf-dt-bootstrap-buttons');
        wp_enqueue_style('wphrm-jaf-dt-buttons');
        wp_enqueue_script('wphrm-jaf-dt-buttons');
        wp_enqueue_script('wphrm-jaf-dt-jszip');
        wp_enqueue_script('wphrm-jaf-dt-pdfmake');
        wp_enqueue_script('wphrm-jaf-dt-vfs_fonts');
        wp_enqueue_script('wphrm-jaf-dt-html5-button');
        wp_enqueue_script('wphrm-jaf-dt-print');
        wp_enqueue_script('wphrm-jaf-dt-colVis');
        wp_enqueue_style('wphrm-jaf-dt-colVis');
        wp_enqueue_script('wphrm-jquery-ui-widget');
        wp_enqueue_script('wphrm-fileupload');
        wp_enqueue_style('wphrm-fileupload');
        wp_enqueue_script('wphrm-jaf-custom-js');
        /*J@F END*/
    }

    public function WPHRMTextDomain() {
        load_plugin_textdomain('wphrm', false, basename(dirname(__FILE__)) . '/languages');
        $this->WPHRMDefaultAttendanceRun();
    }

    public function WPHRMAddEventInterval($schedules) {
        $schedules['everyMinutes'] = array(
            'interval' => 43200, //43200 for 12 hour.
            'display' => esc_html__('Every Five Seconds'),
        );
        return $schedules;
    }

    public function WPHRMMenu() {
        global $current_user, $wpdb;
        $wphrmUserRole = implode(',', $current_user->roles);
        $NotificationCounters = '';
        $NotificationCounter = $this->WPHRMNotificationCounter();
        if ($NotificationCounter != '') {
            $NotificationCounters = $NotificationCounter;
        }
        $wphrmGetPagePermissions = $this->WPHRMGetPagePermissions();
        $wphrmViewOnlyPermissionPages = $this->WPHRMViewOnlyPermissionPages();
        $wphrmSalaryAccording = $this->WPHRMGetSettings('wphrsalaryDayOrHourlyInfo');
        add_menu_page('Simplex Portal', __('Simplex Portal', 'wphrm'), 'wphrm-dashboard', 'wphrm', '', '');
        /** Add sub menus * */
        if (in_array('manageOptionsDashboard', $wphrmGetPagePermissions)) {
            $wphrmPage = add_submenu_page('wphrm', 'Dashboard', __('<i class="fa fa-dashboard"></i> Dashboard', 'wphrm'), 'manageOptionsDashboard', 'wphrm-dashboard', array(&$this, 'WPHRMDashboardCallback'));
            add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
        } else {
            $wphrmPage = add_submenu_page('wphrm', 'Dashboard', __('Dashboard', 'wphrm'), 'manageOptionsDashboardView', 'wphrm-dashboard', array(&$this, 'WPHRMDashboardCallback'));
            add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
        }

        if (empty($wphrmViewOnlyPermissionPages)) {
            if (in_array('manageOptionsDepartment', $wphrmGetPagePermissions)) {
                $wphrmPage = add_submenu_page('wphrm', 'Departments', __('<i class="fa fa-building"></i> Departments', 'wphrm'), 'manageOptionsDepartment', 'wphrm-departments', array(&$this, 'WPHRMDepartments'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            }

            if (in_array('manageOptionsNotice', $wphrmGetPagePermissions)) {
                $wphrmPage = add_submenu_page('wphrm', 'Notices', __('<i class="fa fa-exclamation-circle"></i> Notice Board', 'wphrm'), 'manageOptionsNotice', 'wphrm-notice', array(&$this, 'WPHRMNoticeCallback'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            } else {
                $wphrmPage = add_submenu_page('wphrm', 'Notices', __('Notice Board', 'wphrm'), 'manageOptionsNoticeView', 'wphrm-notice', array(&$this, 'WPHRMNoticeCallback'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            }

            if (in_array('manageOptionsEmployee', $wphrmGetPagePermissions)) {
                $wphrmPage = add_submenu_page('wphrm', 'Employees', __('<i class="fa fa-users"></i> Employees', 'wphrm'), 'manageOptionsEmployee', 'wphrm-employees', array(&$this, 'WPHRMEmployees'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            } else {
                $wphrmPage = add_submenu_page('wphrm-dashboard', 'My Profile', __('View Profile', 'wphrm'), 'manageOptionsEmployeeView', 'wphrm-employee-view-details', array(&$this, 'WPHRMEmployeeViewDetails'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            }

            if (in_array('manageOptionsHolidays', $wphrmGetPagePermissions)) {
                $wphrmPage = add_submenu_page('wphrm', 'Holidays', __('<i class="fa fa-gift"></i> Holidays', 'wphrm'), 'manageOptionsHolidays', 'wphrm-holidays', array(&$this, 'WPHRMHolidaysCallback'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            } else {
                $wphrmPage = add_submenu_page('wphrm', 'Holidays', __('Holidays', 'wphrm'), 'manageOptionsHolidayView', 'wphrm-holidays', array(&$this, 'WPHRMHolidaysCallback'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            }

            /*if (in_array('manageOptionsLeaveRules', $wphrmGetPagePermissions)) {
                $wphrmPage = add_submenu_page('wphrm', 'Leave Rules Management', __('<i class="fa fa-file-text"></i> Leave Rules Management', 'wphrm'), 'manageOptionsLeaveRules', 'wphrm-leave-rules', array(&$this, 'WPHRMLeaveRulesCallback'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            }*/
            if (in_array('manageOptionsLeaveApplications', $wphrmGetPagePermissions)) {
                $wphrmPage = add_submenu_page('wphrm', 'Leave Management', __('<i class="fa fa-file-text"></i> Leave Management', 'wphrm'), 'manageOptionsLeaveApplications', 'wphrm-leaves-application', array(&$this, 'WPHRMLeavesApplicationCallback'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            } else {
                $wphrmPage = add_submenu_page('wphrm', 'Leave Management', __('Leave Management', 'wphrm'), 'manageOptionsLeaveApplicationsView', 'wphrm-leaves-application', array(&$this, 'WPHRMLeavesApplicationCallback'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            }

            if (in_array('manageOptionsAttendances', $wphrmGetPagePermissions)) {
                $wphrmPage = add_submenu_page('wphrm-leaves-application', 'Attendance Management', __('Attendance Management', 'wphrm'), 'manageOptionsAttendances', 'wphrm-attendances', array(&$this, 'WPHRMAttendancesCallback'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            } else {
                $wphrmPage = add_submenu_page('wphrm-leaves-application', 'Attendance Management', __('Attendance Management', 'wphrm'), 'manageOptionsAttendancesView', 'wphrm-view-attendance', array(&$this, 'WPHRMViewAttendances'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            }

            /*if (in_array('manageOptionsSalary', $wphrmGetPagePermissions)) {
                if (isset($wphrmSalaryAccording['wphrm-according']) && $wphrmSalaryAccording['wphrm-according'] == 'Week') {
                    $wphrmPage = add_submenu_page('wphrm', 'Salary Management', __('Salary Management', 'wphrm'), 'manageOptionsSalary', 'wphrm-salary', array(&$this, 'WPHRMSalaryCallback'));
                    add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
                }else{
                    $wphrmPage = add_submenu_page('wphrm', 'Salary Management', __('Salary Management', 'wphrm'), 'manageOptionsSalary', 'wphrm-salary', array(&$this, 'WPHRMSalaryCallback'));
                    add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
                }
            } else {
                if (isset($wphrmSalaryAccording['wphrm-according']) && $wphrmSalaryAccording['wphrm-according'] == 'Week') {
                    $wphrmPage = add_submenu_page('wphrm', 'Salary Management', __('Salary Management', 'wphrm'), 'manageOptionsSalaryView', 'wphrm-select-financials-week', array(&$this, 'WPHRMSelectFinancialsWeek'));
                    add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
                }else{
                    $wphrmPage = add_submenu_page('wphrm', 'Salary Management', __('Salary Management', 'wphrm'), 'manageOptionsSalaryView', 'wphrm-select-financials-month', array(&$this, 'WPHRMSelectFinancialsMonth'));
                    add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
                }
            }*/

            if (in_array('manageOptionsFinancials', $wphrmGetPagePermissions)) {
                $wphrmPage = add_submenu_page('wphrm', 'Finance Management', __('Finance Management', 'wphrm'), 'manageOptionsFinancials', 'wphrm-financials', array(&$this, 'WPHRMFinancialsCallback'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            }

            if (in_array('manageOptionsNotice', $wphrmGetPagePermissions)) {
                $wphrmPage = add_submenu_page('wphrm', 'Notifications', __('<i class="fa fa-info-circle"></i> Notifications ' . $NotificationCounters, 'wphrm'), 'manageOptionsNotifications', 'wphrm-notifications', array(&$this, 'WPHRMNotificationsCallback'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            } else {
                $wphrmPage = add_submenu_page('wphrm', 'Notifications', __('Notifications ' . $NotificationCounters, 'wphrm'), 'manageOptionsNotifications', 'wphrm-notifications', array(&$this, 'WPHRMNotificationsCallback'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            }
            
            if (in_array('manageOptionsNotice', $wphrmGetPagePermissions)) {
                $wphrmPage = add_submenu_page('wphrm-notice', '', '', 'manageOptionsNotice', 'wphrm-add-notice', array(&$this, 'WPHRMAddNotice'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            } else {
                $wphrmPage = add_submenu_page('wphrm-notice', '', '', 'manageOptionsNoticeView', 'wphrm-view-notice', array(&$this, 'WPHRMAddNotice'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            }

            if (in_array('manageOptionsSettings', $wphrmGetPagePermissions)) {
                $wphrmPage = add_submenu_page('wphrm', 'Settings', __('<i class="fa fa-cogs"></i> Settings', 'wphrm'), 'manageOptionsSettings', 'wphrm-settings', array(&$this, 'WPHRMSettingsCallback'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            }

            if (in_array('manageOptionsSettings', $wphrmGetPagePermissions)) {
                $wphrmPage = add_submenu_page('wphrm-leaves-application', 'Leave Rules', __('<i class="fa fa-cogs"></i> Leave Rules', 'wphrm'), 'manageOptionsSettings', 'wphrm-leave-rules', array(&$this, 'WPHRMLeaveRulesCallback'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            }

            if (in_array('manageOptionsFileDocuments', $wphrmGetPagePermissions)) {
                $wphrmPage = add_submenu_page('wphrm', 'Files & Documents', __('<i class="fa fa-files-o"></i> Files & Documents', 'wphrm'), 'manageOptionsFileDocuments', 'wphrm-files', array(&$this, 'WPHRMFileDocuments') );
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            }else{
                $wphrmPage = add_submenu_page('wphrm', 'Files & Documents', __('Files & Documents', 'wphrm'), 'manageOptionsFileDocumentsView', 'wphrm-files', array(&$this, 'WPHRMFileDocuments') );
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            }

            /*if (in_array('manageOptionsFbGroup', $wphrmGetPagePermissions)) {
                $wphrmPage = add_submenu_page('wphrm', 'FB Support Group', __('FB Support Group', 'wphrm'), 'manageOptionsFbGroup', 'wphrm-fb-support', array(&$this, 'WPHRMFBSupportCallback'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
                $wphrmPage = add_submenu_page('wphrm', 'Documentation', __('Documentation', 'wphrm'), 'manageOptionsFbGroup', 'wphrm-documentation', array(&$this, 'WPHRMDocumentsSupportCallback'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
                $wphrmPage = add_submenu_page('wphrm', 'Support Desk', __('Support Desk', 'wphrm'), 'manageOptionsFbGroup', 'wphrm-support', array(&$this, 'WPHRMSupportCallback'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            }*/

        } else {
            $this->wphrmAddMainMenu($wphrmViewOnlyPermissionPages);
        }

        /*J@F*/
        $wphrmPage = add_submenu_page( 'wphrm-invitation', 'Invitation Attendee', __('Invitation Attendee', 'wphrm'), 'manageOptionsNotice', 'wphrm-invitation', array(&$this, 'WPHRMInvitationAttendee') );
        add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
        /*J@F END*/

        $wphrmPage = add_submenu_page('wphrm-departments', '', '', 'manageOptionsDepartment', 'wphrm-add-designation', array(&$this, 'WPHRMAddDesignation'));
        add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
        $wphrmPage = add_submenu_page('wphrm-employees', '', '', 'manageOptionsEmployeeAdd', 'wphrm-add-employee', array(&$this, 'WPHRMEmployeePage'));
        add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
        $wphrmPage = add_submenu_page('wphrm-employees', '', '', 'manageOptionsEmployee', 'wphrm-employee-info', array(&$this, 'WPHRMEmployeeInfo'));
        add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
        $wphrmPage = add_submenu_page('wphrm-employees', '', '', 'manageOptionsEmployee', 'wphrm-employee-view-details', array(&$this, 'WPHRMEmployeeViewDetails'));
        add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
        $wphrmPage = add_submenu_page('wphrm-attendances', '', '', 'manageOptionsAttendances', 'wphrm-view-attendance', array(&$this, 'WPHRMViewAttendances'));
        add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
        $wphrmPage = add_submenu_page('wphrm-attendances', '', '', 'manageOptionsLeaveRules', 'wphrm-leave-rule', array(&$this, 'WPHRMLeaveRulesCallback'));
        add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
        $wphrmPage = add_submenu_page('wphrm-attendances', '', '', 'manageOptionsLeaveApplications', 'wphrm-leave-type', array(&$this, 'WPHRMLeaveType'));
        add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
        $wphrmPage = add_submenu_page('wphrm-attendances', '', '', 'manageOptionsAttendances', 'wphrm-mark-attendance', array(&$this, 'WPHRMMarkAttendances'));
        add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));

        $wphrmPage = add_submenu_page('wphrm-salary', '', '', 'manageOptionsSalary', 'wphrm-select-financials-month', array(&$this, 'WPHRMSelectFinancialsMonth'));
        add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));

        if (isset($wphrmSalaryAccording['wphrm-according']) && $wphrmSalaryAccording['wphrm-according'] == 'Week') {
            if (in_array('manageOptionsSalary', $wphrmGetPagePermissions)) {
                $wphrmPage = add_submenu_page('wphrm-salary', '', '', 'manageOptionsSalary', 'wphrm-select-financials-week', array(&$this, 'WPHRMSelectFinancialsWeek'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            } else {
                $wphrmPage = add_submenu_page('wphrm-salary', '', '', 'manageOptionsSalaryView', 'wphrm-select-financials-week', array(&$this, 'WPHRMSelectFinancialsWeek'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            }
        }

        $wphrmPage = add_submenu_page('wphrm-salary', '', '', 'manageOptionsSalary', 'wphrm-salary-slip-details-week', array(&$this, 'WPHRMSalarySlipDetailsWeek'));
        add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));

        if (isset($wphrmSalaryAccording['wphrm-according']) && $wphrmSalaryAccording['wphrm-according'] == 'Week') {
            if (in_array('manageOptionsSalary', $wphrmGetPagePermissions)) {
                $wphrmPage = add_submenu_page('wphrm-salary', '', '', 'manageOptionsSalary', 'wphrm-salary-slip-week-pdf', array(&$this, 'WPHRMSalarySlipWeekPdf'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            } else {
                $wphrmPage = add_submenu_page('wphrm-salary', '', '', 'manageOptionsSalaryView', 'wphrm-salary-slip-week-pdf', array(&$this, 'WPHRMSalarySlipWeekPdf'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            }
        }

        $wphrmPage = add_submenu_page('wphrm-salary', '', '', 'manageOptionsSalary', 'wphrm-salary-slip-details', array(&$this, 'WPHRMSalarySlipDetails'));
        add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));

        $wphrmPage = add_submenu_page('wphrm-salary', '', '', 'manageOptionsSalary', 'wphrm-salary-slip-pdf', array(&$this, 'WPHRMSalarySlipPdf'));
        add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));

        $wphrmPage = add_submenu_page('wphrm-salary', '', '', 'manageOptionsSalary', 'wphrm-total-salary', array(&$this, 'WPHRMTotalSalary'));
        add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));

        $wphrmPage = add_submenu_page('Financials', '', '', 'manageOptionsFinancials', 'wphrm-profit-loss-reports', array(&$this, 'WPHRMProfitLossReports'));
        add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));

        $wphrmPage = add_submenu_page('Financials', '', '', 'manageOptionsSalary', 'wphrm-salary-reports', array(&$this, 'WPHRMSalaryReports'));
        add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));

        $wphrmPage = add_submenu_page('wphrm-salary', '', '', 'manageOptionsSalary', 'wphrm-total-salary-reports', array(&$this, 'WPHRMTotalSalaryReports'));
        add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));

        $wphrmPage = add_submenu_page('wphrm-salary', '', '', 'manageOptionsSalary', 'wphrm-employee-salary-reports', array(&$this, 'WPHRMEmployeeSalaryReports'));
        add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));

        $wphrmPage = add_submenu_page('wphrm-salary', '', '', 'manageOptionsSalary', 'wphrm-generate-attendance-reports', array(&$this, 'WPHRMGenerateAttendanceReports'));
        add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));

        $wphrmPage = add_submenu_page('wphrm-salary', '', '', 'manageOptionsSalary', 'database-generate-reports', array(&$this, 'WPHRMGenerateXMLFile'));
        add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));

        $wphrmPage = add_submenu_page('wphrm-attendances', '', '', 'manageOptionsAbsent', 'wphrm-employee-absent', array(&$this, 'WPHRMEmployeeAbsent'));
        add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
    }

    /** WP-HRM Activation Plugin * */
    public function WPHRMPluginActivation($networkwide = false) {
        global $wpdb;
        require_once('wphrm-import.php');
        $wphrm_import = new WphrmImport();
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        $main_blog_prefix = $wpdb->get_blog_prefix(BLOG_ID_CURRENT_SITE);

        $table_name = $networkwide ? str_replace($main_blog_prefix, $wpdb->prefix, $this->WphrmSalaryTable) : $this->WphrmSalaryTable;
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE `$table_name` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `employeeID` bigint(50) NOT NULL,
                `employeeKey` varchar(255) NOT NULL,
                `employeeValue` longtext NOT NULL,
                `date` date NOT NULL,
                PRIMARY KEY (`id`)
            )";
            dbDelta($sql);
        }
        $table_name = $networkwide ? str_replace($main_blog_prefix, $wpdb->prefix, $this->WphrmWeeklySalaryTable) : $this->WphrmWeeklySalaryTable;
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE `$table_name` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `employeeID` bigint(50) NOT NULL,
                `weekOn` varchar(255) NOT NULL,
                `employeeKey` varchar(255) NOT NULL,
                `employeeValue` longtext NOT NULL,
                `date` date NOT NULL,
                PRIMARY KEY (`id`)
            )";
            dbDelta($sql);
        }
        $table_name = $networkwide ? str_replace($main_blog_prefix, $wpdb->prefix, $this->WphrmEmployeeLevelTable) : $this->WphrmEmployeeLevelTable;
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE `$table_name` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `year_of_service` bigint(20) NOT NULL,
                `senior_manager` bigint(20) NOT NULL,
                `manager` bigint(20) NOT NULL,
                `supervisor` bigint(20) NOT NULL,
                `staff` bigint(20) NOT NULL,
                PRIMARY KEY (`id`)
            )";
            dbDelta($sql);
        }
        $table_name = $networkwide ? str_replace($main_blog_prefix, $wpdb->prefix, $this->WphrmSettingsTable) : $this->WphrmSettingsTable;
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){
            //$wphrm_import->sqlImport(WPHRMLIB . 'wphrm-settings.sql');
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "SET NAMES utf8;
                    SET time_zone = '+00:00';
                    SET foreign_key_checks = 0;
                    SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

                    DROP TABLE IF EXISTS `$table_name`;
                    CREATE TABLE `$table_name` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `authorID` varchar(200) NOT NULL,
                      `settingKey` varchar(200) NOT NULL,
                      `settingValue` text NOT NULL,
                      PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

                    INSERT INTO `$table_name` (`id`, `authorID`, `settingKey`, `settingValue`) VALUES
                    (1,	'0',	'wphrmMonths',	'YToxMjp7czoyOiIwMSI7czo3OiJKYW51YXJ5IjtzOjI6IjAyIjtzOjg6IkZlYnJ1YXJ5IjtzOjI6IjAzIjtzOjU6Ik1hcmNoIjtzOjI6IjA0IjtzOjU6IkFwcmlsIjtzOjI6IjA1IjtzOjM6Ik1heSI7czoyOiIwNiI7czo0OiJKdW5lIjtzOjI6IjA3IjtzOjQ6Ikp1bHkiO3M6MjoiMDgiO3M6NjoiQXVndXN0IjtzOjI6IjA5IjtzOjk6IlNlcHRlbWJlciI7aToxMDtzOjc6Ik9jdG9iZXIiO2k6MTE7czo4OiJOb3ZlbWJlciI7aToxMjtzOjg6IkRlY2VtYmVyIjt9'),
                    (3,	'0',	'wphrmGeneralSettingsInfo',	'YTo2OntzOjE4OiJ3cGhybV9jb21wYW55X2xvZ28iO3M6MDoiIjtzOjIzOiJ3cGhybV9jb21wYW55X2Z1bGxfbmFtZSI7czowOiIiO3M6MTk6IndwaHJtX2NvbXBhbnlfZW1haWwiO3M6MDoiIjtzOjE5OiJ3cGhybV9jb21wYW55X3Bob25lIjtzOjA6IiI7czoyMToid3Bocm1fY29tcGFueV9hZGRyZXNzIjtzOjE1MjoiICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAiO3M6MTQ6IndwaHJtX2N1cnJlbmN5IjtzOjc6IuKCuS1JTlIiO30='),
                    (7,	'0',	'wphrmNotificationsSettingsInfo',	'YTo0OntzOjI5OiJ3cGhybV9hdHRlbmRhbmNlX25vdGlmaWNhdGlvbiI7TjtzOjI1OiJ3cGhybV9ub3RpY2Vfbm90aWZpY2F0aW9uIjtzOjE6IjEiO3M6MjQ6IndwaHJtX2xlYXZlX25vdGlmaWNhdGlvbiI7czoxOiIxIjtzOjE4OiJ3cGhybV9lbXBsb3llZV9hZGQiO047fQ=='),
                    (8,	'0',	'wphrmSalarySlipInfo',	'YTo2OntzOjE2OiJ3cGhybV9sb2dvX2FsaWduIjtzOjQ6ImxlZnQiO3M6MTg6IndwaHJtX3NsaXBfY29udGVudCI7czo1OiJXUEhSTSI7czoyNjoid3Bocm1fZm9vdGVyX2NvbnRlbnRfYWxpZ24iO3M6NToicmlnaHQiO3M6MTg6IndwaHJtX2JvcmRlcl9jb2xvciI7czoxOiIjIjtzOjIyOiJ3cGhybV9iYWNrZ3JvdW5kX2NvbG9yIjtzOjc6IiNFQ0VGRjEiO3M6MTY6IndwaHJtX2ZvbnRfY29sb3IiO3M6NzoiIzU0NkU3QSI7fQ=='),
                    (9,	'0',	'wphrmUserPermissionInfo',	'YToxOntzOjIxOiJ3cGhybV91c2VyX3Blcm1pc3Npb24iO3M6MTA6InN1YnNjcmliZXIiO30='),
                    (10,	'0',	'wphrmExpenseReportInfo',	'YToxOntzOjIwOiJ3cGhybV9leHBlbnNlX2Ftb3VudCI7czo1OiIyMDAwMCI7fQ=='),
                    (11,	'0',	'Bankfieldskey',	'YToxOntzOjE1OiJCYW5rZmllbGRzbGViYWwiO2E6Mjp7aTowO3M6MTE6IkJyYW5jaCBOYW1lIjtpOjE7czo5OiJJRlNDIENvZGUiO319'),
                    (12,	'0',	'Otherfieldskey',	'YToxOntzOjE2OiJPdGhlcmZpZWxkc2xlYmFsIjthOjI6e2k6MDtzOjEzOiJHbWFpbCBBY2NvdW50IjtpOjE7czoxMzoiU2t5cGUgQWNjb3VudCI7fX0='),
                    (13,	'0',	'salarydetailfieldskey',	'YToxOntzOjIyOiJzYWxhcnlkZXRhaWxmaWVsZGxhYmVsIjthOjI6e2k6MDtzOjE0OiJKb2luaW5nIFNhbGFyeSI7aToxO3M6MTI6IkJhc2ljIFNhbGFyeSI7fX0='),
                    (14,	'0',	'wphrmEarningInfo',	'YToxOntzOjEyOiJlYXJuaW5nTGViYWwiO2E6MTp7aTowO3M6MzoiSFJBIjt9fQ=='),
                    (15,	'0',	'wphrmDeductionInfo',	'YToxOntzOjE0OiJkZWR1Y3Rpb25sZWJhbCI7YToxOntpOjA7czoyOiJQRiI7fX0=');";
            dbDelta($sql);
        }
        $table_name = $networkwide ? str_replace($main_blog_prefix, $wpdb->prefix, $this->WphrmHolidaysTable) : $this->WphrmHolidaysTable;
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE IF NOT EXISTS $table_name(
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `wphrmDate` date NOT NULL,
                `wphrmOccassion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
                `type` enum('weekend','holiday','') NOT NULL,
                `createdAt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
                `updatedAt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
                PRIMARY KEY (`id`),
                UNIQUE KEY `holidays_date_unique` (`wphrmDate`))";
            dbDelta($sql);
        }
        $table_name = $networkwide ? str_replace($main_blog_prefix, $wpdb->prefix, $this->WphrmInvitationAttendeeTable) : $this->WphrmInvitationAttendeeTable;
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `notice_id` int(11) NOT NULL,
              `user_id` int(11) NOT NULL,
              `status` varchar(150) NOT NULL,
              `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
            dbDelta($sql);
        }
        $table_name = $networkwide ? str_replace($main_blog_prefix, $wpdb->prefix, $this->WphrmLeaveRulesTable) : $this->WphrmLeaveRulesTable;
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `leaveRule` varchar(150) NOT NULL,
              `employeeType` varchar(150) NOT NULL,
              `years_in_service` int(11) NOT NULL,
              `gender` int(11) NOT NULL,
              `nationality` int(11) NOT NULL,
              `age` int(11) NOT NULL,
              `marital_status` varchar(50) NOT NULL,
              `max_children_covered` int(11) NOT NULL,
              `child_age_limit` int(11) NOT NULL,
              `medical_claim_limit` bigint(50) NOT NULL,
              `elderly_screening_limit` bigint(50) NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
            dbDelta($sql);
        }
        $table_name = $networkwide ? str_replace($main_blog_prefix, $wpdb->prefix, $this->WphrmLeaveApplicationTable) : $this->WphrmLeaveApplicationTable;
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `employeeID` bigint(20) NOT NULL,
              `date` date NOT NULL,
              `toDate` date DEFAULT '0000-00-00',
              `leaveType` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
              `halfDayType` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
              `reason` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
              `medical_claim_amount` decimal(11,0) NOT NULL,
              `elderly_screening_amount` decimal(11,0) NOT NULL,
              `adminComments` varchar(250) NOT NULL,
              `applicationStatus` enum('approved','rejected','pending') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
              `appliedOn` date DEFAULT NULL,
              `updatedBy` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
              `createdAt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
              `updatedAt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
              PRIMARY KEY (`id`),
              KEY `attendance_employeeid_index` (`employeeID`),
              KEY `attendance_leavetype_index` (`leaveType`),
              KEY `attendance_updated_by_index` (`updatedBy`),
              KEY `attendance_halfdaytype_index` (`halfDayType`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
            dbDelta($sql);
        }
        $table_name = $networkwide ? str_replace($main_blog_prefix, $wpdb->prefix, $this->WphrmFinancialsTable) : $this->WphrmFinancialsTable;
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){
            $charset_collate = $wpdb->get_charset_collate();
            $sql = " CREATE TABLE IF NOT EXISTS $table_name (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `wphrmItem` varchar(100) NOT NULL,
                `wphrmAmounts` varchar(100) NOT NULL,
                `wphrmStatus` varchar(100) NOT NULL,
                `wphrmDate` date NOT NULL,
                PRIMARY KEY (`id`))";
            dbDelta($sql);
        }
        $table_name = $networkwide ? str_replace($main_blog_prefix, $wpdb->prefix, $this->WphrmNotificationsTable) : $this->WphrmNotificationsTable;
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){
            $charset_collate = $wpdb->get_charset_collate();
            $sql = " CREATE TABLE IF NOT EXISTS `$table_name` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `wphrmUserID` int(11) NOT NULL,
                    `wphrmFromId` int(11) NOT NULL,
                    `wphrmDesc` varchar(255) NOT NULL,
                    `notificationType` varchar(200) NOT NULL,
                    `wphrmStatus` enum('unseen','seen') NOT NULL,
                    `wphrmDate` date NOT NULL,
                    PRIMARY KEY (`id`))";
            dbDelta($sql);
        }
        $table_name = $networkwide ? str_replace($main_blog_prefix, $wpdb->prefix, $this->WphrmDesignationTable) : $this->WphrmDesignationTable;
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                `designationID` bigint(20) NOT NULL AUTO_INCREMENT,
                `departmentID` bigint(20) NOT NULL,
                `designationName` varchar(200) DEFAULT NULL,
                PRIMARY KEY (`designationID`))";
            dbDelta($sql);
        }
        $table_name = $networkwide ? str_replace($main_blog_prefix, $wpdb->prefix, $this->WphrmDepartmentTable) : $this->WphrmDepartmentTable;
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE IF NOT EXISTS $table_name(
                `departmentID` bigint(20) NOT NULL AUTO_INCREMENT,
                `departmentName` varchar(200) DEFAULT NULL,
                PRIMARY KEY (`departmentID`))";
            dbDelta($sql);
        }
        $table_name = $networkwide ? str_replace($main_blog_prefix, $wpdb->prefix, $this->WphrmAttendanceTable) : $this->WphrmAttendanceTable;
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `employeeID` bigint(20) NOT NULL,
                `date` date NOT NULL,
                `status` enum('absent','present') COLLATE utf8_unicode_ci NOT NULL,
                `leaveApplicationId` int(10) NULL,
                `leaveType` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
                `halfDayType` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
                `reason` text COLLATE utf8_unicode_ci NOT NULL,
                `applicationStatus` enum('approved','rejected','pending') COLLATE utf8_unicode_ci DEFAULT NULL,
                `appliedOn` date DEFAULT NULL,
                `updatedBy` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
                `createdAt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
                `updatedAt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
                PRIMARY KEY (`id`),
                KEY `attendance_employeeid_index` (`employeeID`),
                KEY `attendance_leavetype_index` (`leaveType`),
                KEY `attendance_updated_by_index` (`updatedBy`),
                KEY `attendance_halfdaytype_index` (`halfDayType`))";
            dbDelta($sql);
            $result = $wpdb->query("SHOW COLUMNS FROM $table_name LIKE 'toDate'");
            $wpdb->query("ALTER TABLE $table_name ADD `toDate` date  DEFAULT '0000-00-00' after `date`");
            $result1 = $wpdb->query("SHOW COLUMNS FROM $table_name LIKE 'adminComments'");
            $wpdb->query("ALTER TABLE $table_name ADD `adminComments` VARCHAR(250) NOT NULL after `reason`");
        } else {
            $result = $wpdb->query("SHOW COLUMNS FROM $table_name LIKE 'toDate'");
            if ($result != 1) {
                $wpdb->query("ALTER TABLE $table_name ADD `toDate` date  DEFAULT '0000-00-00' after `date`");
            }
            $result1 = $wpdb->query("SHOW COLUMNS FROM $table_name LIKE 'adminComments'");
            if ($result1 != 1) {
                $wpdb->query("ALTER TABLE $table_name ADD `adminComments` VARCHAR(250) NOT NULL after `reason`");
            }
        }
        $table_name = $networkwide ? str_replace($main_blog_prefix, $wpdb->prefix, $this->WphrmLeaveTypeTable) : $this->WphrmLeaveTypeTable;
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){
            //$wphrm_import->sqlImport(WPHRMLIB . 'wphrm-leave-types.sql');
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "SET NAMES utf8;
                    SET time_zone = '+00:00';
                    SET foreign_key_checks = 0;
                    SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

                    CREATE TABLE IF NOT EXISTS `$table_name` (
                    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                    `leaveType` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
                    `period` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
                    `numberOfLeave` int(10) unsigned NOT NULL,
                    `require_file_attachment` tinyint(1) NOT NULL,
                    `notice_period` int(11) NOT NULL,
                    `leave_rules` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                    `leave_description` text COLLATE utf8_unicode_ci NOT NULL,
                    PRIMARY KEY (`id`),
                    KEY `leavetypes_leavetype_index` (`leaveType`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
            dbDelta($sql);
        }
        $table_name = $networkwide ? str_replace($main_blog_prefix, $wpdb->prefix, $this->WphrmMessagesTable) : $this->WphrmMessagesTable;
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            //$wphrm_import->sqlImport(WPHRMLIB . 'wphrm-messages.sql');
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "SET NAMES utf8;
                    SET time_zone = '+00:00';
                    SET foreign_key_checks = 0;
                    SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

                    DROP TABLE IF EXISTS `$table_name`;
                    CREATE TABLE `$table_name` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `messagesTitle` varchar(50) NOT NULL,
                      `messagesDesc` varchar(255) NOT NULL,
                      PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

                    INSERT INTO `$table_name` (`id`, `messagesTitle`, `messagesDesc`) VALUES
                    (1,	'Add Employee',	'Employee has been successfully added.'),
                    (2,	'Update Employee',	'Employee has been successfully updated.'),
                    (3,	'Update Personal Details',	'Personal Details have been successfully updated.'),
                    (4,	'Update Bank  Details',	'Bank Details have been successfully updated.'),
                    (5,	'Update Documents',	'Documents have been successfully updated.'),
                    (6,	'Update Other Details',	'Other Details have been successfully updated.'),
                    (7,	'Update Salary Details',	'Salary Details have been successfully updated.'),
                    (8,	'Add Department',	'Department has been successfully added.'),
                    (9,	'Update Department',	'Department has been successfully updated.'),
                    (10,	'Add Designation',	'Designation has been successfully added.'),
                    (11,	'Update Designation',	'Designation has been successfully updated.'),
                    (12,	'Delete Designation',	'Designation has been successfully deleted.'),
                    (13,	'Delete Department',	'Department has been successfully deleted.'),
                    (14,	'Add Holiday',	'Holiday has been successfully added.'),
                    (15,	'Delete Holiday',	'Holiday has been successfully deleted. '),
                    (16,	'Mark Attendance',	'Attendance has been successfully marked.'),
                    (17,	'Delete Leave Application',	'Leave Application has been successfully deleted.'),
                    (18,	'Update Leave Type',	'Leave Type  has been successfully updated.'),
                    (19,	'Add Leave Type',	'Leave Type  has been successfully added.'),
                    (20,	'Delete Leave Type',	'Leave Type has been successfully deleted.'),
                    (21,	'Create Salary slip',	'Salary Slip Details have been successfully created.'),
                    (22,	'Update Salary Slip',	'Salary Slip has been successfully updated.'),
                    (23,	'Delete Salary slip',	'Salary Slip has been successfully deleted.'),
                    (24,	'Sent Salary slip Request',	'Salary Slip Request has been successfully sent.'),
                    (25,	'Sent Salary slip',	'Salary Slip has been successfully sent.'),
                    (26,	'Update Notices',	'Notice has been successfully updated.'),
                    (27,	'Update General Settings',	'General Settings have been successfully updated.'),
                    (28,	'Update Notifications Settings',	'Notifications Settings  have been successfully updated.'),
                    (29,	'Update Change Password',	'Password has been successfully updated.'),
                    (30,	'Update Salary Slip Settings',	'Salary Slip Settings has been successfully updated.'),
                    (31,	'Update Users Permission Settings',	'Users Permission has been successfully updated.'),
                    (32,	'Update Leave Application',	'Leave Application has been successfully updated.'),
                    (33,	'Sent Leave Appliction',	'Leave appliction has been successfully sent.'),
                    (34,	'Update Messges Settings',	'Messge has been successfully updated.'),
                    (35,	'Expense Amount Update',	'Expense amount has been successfully updated.'),
                    (36,	'Add Financials',	'Financial has been successfully added.'),
                    (37,	'Update Financials',	'Financial has been successfully updated.'),
                    (38,	'Duplicate Salary Slip',	'Salary Slip has been successfully duplicated.'),
                    (39,	'Update Settings',	'Settings field has been successfully updated.'),
                    (40,	'Add Deduction label',	'Deduction label has been successfully added.'),
                    (41,	'Update Deduction label',	'Deduction label has been successfully updated.'),
                    (42,	'Delete Settings Field label',	'Settings Field label has been successfully deleted.');";
            dbDelta($sql);
        }
        $table_name = $networkwide ? str_replace($main_blog_prefix, $wpdb->prefix, $this->WphrmCurrencyTable) : $this->WphrmCurrencyTable;
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){
            //            $wphrm_import->sqlImport(WPHRMLIB . 'wphrm-currency.sql');
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "SET NAMES utf8;
                    SET time_zone = '+00:00';
                    SET foreign_key_checks = 0;
                    SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

                    DROP TABLE IF EXISTS `$table_name`;
                    CREATE TABLE `$table_name` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `currencyName` varchar(200) NOT NULL,
                      `currencySign` varchar(200) NOT NULL,
                      `currencyDesc` varchar(250) NOT NULL,
                      PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

                    INSERT INTO `$table_name` (`id`, `currencyName`, `currencySign`, `currencyDesc`) VALUES
                    (1, 'USD', '&#36', 'USD Currency'),
                    (2, 'INR', '&#8377', 'INR Currency'),
                    (3, 'GBP', '&#163', 'GBP Currency'),
                    (4, 'JPY', '&#165', 'JPY Currency'),
                    (5, 'YEN', '&#165', 'YEN Currency'),
                    (6, 'EUR', '&#8364', 'EUR Currency'),
                    (7, 'WON', '&#8361', 'WON Currency'),
                    (8, 'TRY', '&#8356', 'TRY Currency'),
                    (9, 'RUB', '&#1088', 'RUB Currency'),
                    (10, 'RMB', '&#165', 'RMB Currency'),
                    (11, 'KRW', '&#8361', 'KRW Currency'),
                    (12, 'BTC', '&#8361', 'BTC Currency'),
                    (13, 'THB', '&#3647', 'THB Currency'),
                    (14, 'BDT', '&#2547', 'BDT Currency'),
                    (15, 'CRC', '&#8353', 'CRC Currency'),
                    (16, 'GEL', '&#4314', 'GEL Currency');";
            dbDelta($sql);
        }
        $table_name = $networkwide ? str_replace($main_blog_prefix, $wpdb->prefix, $this->WphrmNoticeTable) : $this->WphrmNoticeTable;
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `wphrmtitle` varchar(250) NOT NULL,
                      `wphrmdesc` longtext NOT NULL,
                      `wphrmdate` date DEFAULT NULL,
                      `wphrmcreatedDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                      `wphrmdepartment` int(11) DEFAULT NULL,
                      `wphrminvitationnotice` tinyint(1) NOT NULL,
                      `wphrminvitationsender` int(11) NOT NULL,
                      `wphrminvitationrecipient` varchar(100) NOT NULL,
                      PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            dbDelta($sql);
        }
        $this->WPHRMAddCapabilityWithRole();
        $this->WPHRMAddDefaultSettings();
    }

    /** WP-HRM Deactive Plugin * */
    public function WPHRMPluginDeactive() {
        wp_clear_scheduled_hook('WPHRMUpdateCheckEventAction');
    }

    /** WP-HRM Add Default Settings * */
    public function WPHRMAddDefaultSettings() {
        global $current_user, $wpdb;
        $wphrmSalaryAccording = $this->WPHRMGetSettings('wphrsalaryDayOrHourlyInfo');
        if (empty($wphrmSalaryAccording)) {
            $digiproOtherDatas = 'YToxOntzOjE1OiJ3cGhybS1hY2NvcmRpbmciO3M6MzoiRGF5Ijt9';
            $wpdb->query("INSERT INTO $this->WphrmSettingsTable (`settingKey`, `settingValue`) VALUES('wphrsalaryDayOrHourlyInfo','$digiproOtherDatas')");
        }
    }

    /** Start WP-HRM All Roles and Capability Functions * */
    public function WPHRMAddCapabilityWithRole() {
        $getAllRoles = $this->WPHRMGetUserRoles();
        foreach ($getAllRoles as $key => $role) {
            if ($key == 'administrator') {
                $role_admin = get_role($key);
                if (isset($role_admin->capabilities['manageOptionsEmployee']) && $role_admin->capabilities['manageOptionsEmployee'] == 1) {

                } else {
                    $role_admin->add_cap('manageOptionsEmployee');
                }
                if (isset($role_admin->capabilities['manageOptionsDepartment']) && $role_admin->capabilities['manageOptionsDepartment'] == 1) {

                } else {
                    $role_admin->add_cap('manageOptionsDepartment');
                }
                if (isset($role_admin->capabilities['manageOptionsHolidays']) && $role_admin->capabilities['manageOptionsHolidays'] == 1) {

                } else {
                    $role_admin->add_cap('manageOptionsHolidays');
                }
                if (isset($role_admin->capabilities['manageOptionsAttendances']) && $role_admin->capabilities['manageOptionsAttendances'] == 1) {

                } else {
                    $role_admin->add_cap('manageOptionsAttendances');
                }
                if (isset($role_admin->capabilities['manageOptionsLeaveRules']) && $role_admin->capabilities['manageOptionsLeaveRules'] == 1) {

                } else {
                    $role_admin->add_cap('manageOptionsLeaveRules');
                }
                if (isset($role_admin->capabilities['manageOptionsLeaveApplications']) && $role_admin->capabilities['manageOptionsLeaveApplications'] == 1) {

                } else {
                    $role_admin->add_cap('manageOptionsLeaveApplications');
                }
                if (isset($role_admin->capabilities['manageOptionsSalary']) && $role_admin->capabilities['manageOptionsSalary'] == 1) {

                } else {
                    $role_admin->add_cap('manageOptionsSalary');
                }

                if (isset($role_admin->capabilities['manageOptionsDashboard']) && $role_admin->capabilities['manageOptionsDashboard'] == 1) {

                } else {
                    $role_admin->add_cap('manageOptionsDashboard');
                }
                if (isset($role_admin->capabilities['manageOptionsNotice']) && $role_admin->capabilities['manageOptionsNotice'] == 1) {

                } else {
                    $role_admin->add_cap('manageOptionsNotice');
                }
                if (isset($role_admin->capabilities['manageOptionsSettings']) && $role_admin->capabilities['manageOptionsSettings'] == 1) {

                } else {
                    $role_admin->add_cap('manageOptionsSettings');
                }

                if (isset($role_admin->capabilities['manageOptionsSlipDetails']) && $role_admin->capabilities['manageOptionsSlipDetails'] == 1) {

                } else {
                    $role_admin->add_cap('manageOptionsSlipDetails');
                }

                if (isset($role_admin->capabilities['manageOptionsFinancials']) && $role_admin->capabilities['manageOptionsFinancials'] == 1) {

                } else {
                    $role_admin->add_cap('manageOptionsFinancials');
                }

                if (isset($role_admin->capabilities['manageOptionsAbsent']) && $role_admin->capabilities['manageOptionsAbsent'] == 1) {

                } else {
                    $role_admin->add_cap('manageOptionsAbsent');
                }

                if (isset($role_admin->capabilities['manageOptionsFbGroup']) && $role_admin->capabilities['manageOptionsFbGroup'] == 1) {

                } else {
                    $role_admin->add_cap('manageOptionsFbGroup');
                }

                if (isset($role_admin->capabilities['manageOptionsNotifications']) && $role_admin->capabilities['manageOptionsNotifications'] == 1) {

                } else {
                    $role_admin->add_cap('manageOptionsNotifications');
                }

                if (!(isset($role_admin->capabilities['manageOptionsFileDocuments']) && $role_admin->capabilities['manageOptionsFileDocuments'] == 1)) {
                    $role_admin->add_cap('manageOptionsFileDocuments');
                }

                $role_admin->add_cap('read');
            } else {

                $role_subscriber = get_role($key);
                if (isset($role_subscriber->capabilities['manageOptionsEmployeeView']) && $role_subscriber->capabilities['manageOptionsEmployeeView'] == 1) {
                    $role_subscriber->remove_cap('manageOptionsEmployee');
                    $role_subscriber->add_cap('manageOptionsEmployeeView');
                } else if (isset($role_subscriber->capabilities['manageOptionsEmployee']) && $role_subscriber->capabilities['manageOptionsEmployee'] == 1) {
                    $role_subscriber->remove_cap('manageOptionsEmployeeView');
                    $role_subscriber->add_cap('manageOptionsEmployee');
                } else {
                    $role_subscriber->remove_cap('manageOptionsEmployee');
                    $role_subscriber->add_cap('manageOptionsEmployeeView');
                }


                if (isset($role_subscriber->capabilities['manageOptionsHolidayView']) && $role_subscriber->capabilities['manageOptionsHolidayView'] == 1) {
                    $role_subscriber->remove_cap('manageOptionsHolidays');
                    $role_subscriber->add_cap('manageOptionsHolidayView');
                } else if (isset($role_subscriber->capabilities['manageOptionsHolidays']) && $role_subscriber->capabilities['manageOptionsHolidays'] == 1) {
                    $role_subscriber->remove_cap('manageOptionsHolidayView');
                    $role_subscriber->add_cap('manageOptionsHolidays');
                } else {
                    $role_subscriber->remove_cap('manageOptionsHolidays');
                    $role_subscriber->add_cap('manageOptionsHolidayView');
                }

                if (isset($role_subscriber->capabilities['manageOptionsAttendancesView']) && $role_subscriber->capabilities['manageOptionsAttendancesView'] == 1) {
                    $role_subscriber->remove_cap('manageOptionsAttendances');
                    $role_subscriber->add_cap('manageOptionsAttendancesView');
                } else if (isset($role_subscriber->capabilities['manageOptionsAttendances']) && $role_subscriber->capabilities['manageOptionsAttendances'] == 1) {
                    $role_subscriber->remove_cap('manageOptionsAttendancesView');
                    $role_subscriber->add_cap('manageOptionsAttendances');
                } else {
                    $role_subscriber->remove_cap('manageOptionsAttendances');
                    $role_subscriber->add_cap('manageOptionsAttendancesView');
                }


                if (isset($role_subscriber->capabilities['manageOptionsSalaryView']) && $role_subscriber->capabilities['manageOptionsSalaryView'] == 1) {
                    $role_subscriber->remove_cap('manageOptionsSalary');
                    $role_subscriber->add_cap('manageOptionsSalaryView');
                } else if (isset($role_subscriber->capabilities['manageOptionsSalary']) && $role_subscriber->capabilities['manageOptionsSalary'] == 1) {
                    $role_subscriber->remove_cap('manageOptionsSalaryView');
                    $role_subscriber->add_cap('manageOptionsSalary');
                } else {
                    $role_subscriber->remove_cap('manageOptionsSalary');
                    $role_subscriber->add_cap('manageOptionsSalaryView');
                }

                if (isset($role_subscriber->capabilities['manageOptionsDashboardView']) && $role_subscriber->capabilities['manageOptionsDashboardView'] == 1) {
                    $role_subscriber->remove_cap('manageOptionsDashboard');
                    $role_subscriber->add_cap('manageOptionsDashboardView');
                } else if (isset($role_subscriber->capabilities['manageOptionsDashboard']) && $role_subscriber->capabilities['manageOptionsDashboard'] == 1) {
                    $role_subscriber->remove_cap('manageOptionsDashboardView');
                    $role_subscriber->add_cap('manageOptionsDashboard');
                } else {
                    $role_subscriber->remove_cap('manageOptionsDashboard');
                    $role_subscriber->add_cap('manageOptionsDashboardView');
                }

                if (isset($role_subscriber->capabilities['manageOptionsNotifications']) && $role_subscriber->capabilities['manageOptionsNotifications'] == 1) {

                } else {
                    $role_subscriber->add_cap('manageOptionsNotifications');
                }

                if (isset($role_subscriber->capabilities['manageOptionsLeaveRules']) && $role_subscriber->capabilities['manageOptionsLeaveRules'] == 1) {

                } else {
                    $role_subscriber->add_cap('manageOptionsLeaveRules');
                }

                if (isset($role_subscriber->capabilities['manageOptionsLeaveApplicationsView']) && $role_subscriber->capabilities['manageOptionsLeaveApplicationsView'] == 1) {
                    $role_subscriber->remove_cap('manageOptionsLeaveApplications');
                    $role_subscriber->add_cap('manageOptionsLeaveApplicationsView');
                } else if (isset($role_subscriber->capabilities['manageOptionsLeaveApplications']) && $role_subscriber->capabilities['manageOptionsLeaveApplications'] == 1) {
                    $role_subscriber->remove_cap('manageOptionsLeaveApplicationsView');
                    $role_subscriber->add_cap('manageOptionsLeaveApplications');
                } else {
                    $role_subscriber->remove_cap('manageOptionsLeaveApplications');
                    $role_subscriber->add_cap('manageOptionsLeaveApplicationsView');
                }

                if (isset($role_subscriber->capabilities['manageOptionsNoticeView']) && $role_subscriber->capabilities['manageOptionsNoticeView'] == 1) {
                    $role_subscriber->remove_cap('manageOptionsNotice');
                    $role_subscriber->add_cap('manageOptionsNoticeView');
                } else if (isset($role_subscriber->capabilities['manageOptionsNotice']) && $role_subscriber->capabilities['manageOptionsNotice'] == 1) {
                    $role_subscriber->remove_cap('manageOptionsNoticeView');
                    $role_subscriber->add_cap('manageOptionsNotice');
                } else {
                    $role_subscriber->remove_cap('manageOptionsNotice');
                    $role_subscriber->add_cap('manageOptionsNoticeView');
                }

                $role_subscriber->add_cap('read');
            }
        }
        return;
    }

    public function WPHRMAddRoles() {
        global $current_user, $wpdb;
        $wphrmUserRole = implode(',', $current_user->roles);
        $message = array();
        $wphrmGetDefineCapability = array();
        $wphrmAccess = array();
        $wphrmUserCapability = array();
        $wphrm_rolename = '';
        $wphrm_rolenames = '';
        $wphrm_displayname = '';
        $pagename = $_POST['pageName'];
        $wphrmAll = $_POST['wphrm-all'];

        if (isset($_POST['wphrm_user_permission']) && $_POST['wphrm_user_permission'] != '') {
            $wphrm_rolenames = sanitize_text_field($_POST['wphrm_user_permission']);
        } else if (isset($_POST['wphrm_rolename']) && $_POST['wphrm_rolename'] != '') {
            $wphrm_displaynames = sanitize_text_field($_POST['wphrm_rolename']);
            $rolenames = strtolower($wphrm_displaynames);
            $rolenames = str_replace(' ', '_', $rolenames);
            $wphrm_rolenames = sanitize_text_field($rolenames);
        }
        if ($wphrm_rolenames != '') {
            foreach ($pagename as $pageKey => $pagenames) {
                foreach ($wphrmAll as $wphrmAllKey => $wphrmAllpermission) {
                    if ($pageKey == $wphrmAllKey) {
                        if ($wphrmAllpermission == 'all') {
                            $wphrmAccess[] = $pagenames;
                        }
                    }
                }
            }

            $wphrmGetAccess = $this->WPHRMGetAccess($wphrmAccess);
            $wphrmGetDefines = base64_encode(serialize($wphrmGetAccess));
            $checkPageAccess = $this->WPHRMCheckPageAccess($wphrm_rolenames);
            if (empty($checkPageAccess)) {
                $wpdb->insert($this->WphrmSettingsTable, array('authorID' => 'defineaccess', 'settingKey' => $wphrm_rolenames, 'settingValue' => $wphrmGetDefines));
            } else {
                $whereArray = array('authorID' => 'defineaccess', 'settingKey' => $wphrm_rolenames);
                $datas = array('settingValue' => $wphrmGetDefines);
                $wpdb->update($this->WphrmSettingsTable, $datas, $whereArray);
            }
        }

        if ((isset($_POST['roleAction']) && $_POST['roleAction'] == 'add-role')) {
            if (isset($_POST['wphrm_rolename']) && $_POST['wphrm_rolename'] != '') {
                $wphrm_displayname = sanitize_text_field($_POST['wphrm_rolename']);
            }
            $rolename = strtolower($wphrm_displayname);
            $rolename = str_replace(' ', '_', $rolename);
            $wphrm_rolename = sanitize_text_field($rolename);

            $checkExistRole = get_role($rolenameget);
            if (empty($checkExistRole)) {
                add_role($wphrm_rolename, $wphrm_displayname, array('read' => true));
            }
            $wphrmUserCapabilities = $this->wphrmUserDefineCapability;
            foreach ($wphrmUserCapabilities as $key => $wphrmUserCapabilities) {
                $wphrmUserCapability[] = $key;
            }
            $getRoles = get_role($wphrm_rolename);
            $wphrmCheckPages = $this->wphrmCheckPages;
            foreach ($getRoles->capabilities as $key => $wphrmDefineCapability) {
                if (in_array($key, $wphrmUserCapability)) {
                    $wphrmGetDefineCapability[] = $key;
                }
            }
            $wphrmGetDefineCapabilityData = array_merge_recursive($wphrmGetDefineCapability, $wphrmCheckPages);
            $this->wphrmPageInAddCapability($wphrm_rolename, $pagename, $wphrmAll, $wphrmGetDefineCapabilityData);
            if (empty($checkExistRole)) {
                $message['success'] = __('Role has been successfully added.', 'wphrm');
            } else {
                $message['error'] = __('Something went wrong', 'wphrm');
            }
            echo json_encode($message);
            exit;
        } else if ((isset($_POST['roleAction']) && $_POST['roleAction'] == 'edit-role')) {
            if (isset($_POST['wphrm_user_permission']) && $_POST['wphrm_user_permission'] != '') {
                $wphrm_rolename = sanitize_text_field($_POST['wphrm_user_permission']);
            }
            $wphrmUserCapabilities = $this->wphrmUserDefineCapability;
            foreach ($wphrmUserCapabilities as $key => $wphrmUserCapabilities) {
                $wphrmUserCapability[] = $key;
            }
            $getRoles = get_role($wphrm_rolename);
            $wphrmCheckPages = $this->wphrmCheckPages;
            foreach ($getRoles->capabilities as $key => $wphrmDefineCapability) {
                if (in_array($key, $wphrmUserCapability)) {
                    $wphrmGetDefineCapability[] = $key;
                }
            }
            $wphrmGetDefineCapabilityData = array_merge_recursive($wphrmGetDefineCapability, $wphrmCheckPages);
            $this->wphrmPageInAddCapability($wphrm_rolename, $pagename, $wphrmAll, $wphrmGetDefineCapabilityData);


            if (isset($_POST['roleAction']) && $_POST['roleAction'] == 'edit-role') {
                $message['success'] = __('Role has been successfully updated.', 'wphrm');
            } else {
                $message['error'] = __('Something went wrong', 'wphrm');
            }
        }
        echo json_encode($message);
        exit;
    }

    public function WPHRMGetAccess($wphrmAccess) {
        $getPages = array();
        foreach ($wphrmAccess as $wphrmAccessPage) {
            if ($wphrmAccessPage == 'manageOptionsEmployeeView') {
                $getPages[] = 'manageOptionsEmployee';
            } else if ($wphrmAccessPage == 'manageOptionsHolidayView') {
                $getPages[] = 'manageOptionsHolidays';
            } else if ($wphrmAccessPage == 'manageOptionsAttendancesView') {
                $getPages[] = 'manageOptionsAttendances';
            } else if ($wphrmAccessPage == 'manageOptionsSalaryView') {
                $getPages[] = 'manageOptionsSalary';
            } else if ($wphrmAccessPage == 'manageOptionsDashboardView') {
                $getPages[] = 'manageOptionsDashboard';
            } else if ($wphrmAccessPage == 'manageOptionsLeaveApplicationsView') {
                $getPages[] = 'manageOptionsLeaveApplications';
            } else if ($wphrmAccessPage == 'manageOptionsNoticeView') {
                $getPages[] = 'manageOptionsNotice';
            } else {
                $getPages[] = $wphrmAccessPage;
            }
        }
        return $getPages;
    }

    public function wphrmPageInAddCapability($wphrm_rolename, $pagename, $wphrmAll, $wphrmGetDefineCapabilityData) {
        $getRoles = get_role($wphrm_rolename);
        foreach ($pagename as $pageKey => $pagenames) {
            foreach ($wphrmAll as $wphrmAllKey => $wphrmAllpermission) {
                if ($pageKey == $wphrmAllKey) {
                    if (in_array($pagenames, $wphrmGetDefineCapabilityData)) {
                        if (isset($pagenames) && $pagenames == 'manageOptionsEmployeeView') {
                            if (isset($getRoles->capabilities['manageOptionsEmployeeView']) && $getRoles->capabilities['manageOptionsEmployeeView'] == 1) {
                                if ($wphrmAllpermission == 'all') {
                                    $getRoles->remove_cap('manageOptionsEmployeeView');
                                    $getRoles->add_cap('manageOptionsEmployee');
                                }
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsEmployee') {
                            if (isset($getRoles->capabilities['manageOptionsEmployee']) && $getRoles->capabilities['manageOptionsEmployee'] == 1) {
                                if ($wphrmAllpermission == 'view') {
                                    $getRoles->remove_cap('manageOptionsEmployee');
                                    $getRoles->add_cap('manageOptionsEmployeeView');
                                }
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsHolidayView') {
                            if (isset($getRoles->capabilities['manageOptionsHolidayView']) && $getRoles->capabilities['manageOptionsHolidayView'] == 1) {
                                if ($wphrmAllpermission == 'all') {
                                    $getRoles->remove_cap('manageOptionsHolidayView');
                                    $getRoles->add_cap('manageOptionsHolidays');
                                }
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsHolidays') {
                            if (isset($getRoles->capabilities['manageOptionsHolidays']) && $getRoles->capabilities['manageOptionsHolidays'] == 1) {
                                if ($wphrmAllpermission == 'view') {
                                    $getRoles->remove_cap('manageOptionsHolidays');
                                    $getRoles->add_cap('manageOptionsHolidayView');
                                }
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsNoticeView') {
                            if (isset($getRoles->capabilities['manageOptionsNoticeView']) && $getRoles->capabilities['manageOptionsNoticeView'] == 1) {
                                if ($wphrmAllpermission == 'all') {
                                    $getRoles->remove_cap('manageOptionsNoticeView');
                                    $getRoles->add_cap('manageOptionsNotice');
                                }
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsNotice') {
                            if (isset($getRoles->capabilities['manageOptionsNotice']) && $getRoles->capabilities['manageOptionsNotice'] == 1) {
                                if ($wphrmAllpermission == 'view') {
                                    $getRoles->remove_cap('manageOptionsNotice');
                                    $getRoles->add_cap('manageOptionsNoticeView');
                                }
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsAttendancesView') {
                            if (isset($getRoles->capabilities['manageOptionsAttendancesView']) && $getRoles->capabilities['manageOptionsAttendancesView'] == 1) {
                                if ($wphrmAllpermission == 'all') {
                                    $getRoles->remove_cap('manageOptionsAttendancesView');
                                    $getRoles->add_cap('manageOptionsAttendances');
                                }
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsAttendances') {
                            if (isset($getRoles->capabilities['manageOptionsAttendances']) && $getRoles->capabilities['manageOptionsAttendances'] == 1) {
                                if ($wphrmAllpermission == 'view') {
                                    $getRoles->remove_cap('manageOptionsAttendances');
                                    $getRoles->add_cap('manageOptionsAttendancesView');
                                }
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsLeaveRules') {
                            if (isset($getRoles->capabilities['manageOptionsLeaveRules']) && $getRoles->capabilities['manageOptionsLeaveRules'] == 1) {
                                if ($wphrmAllpermission == 'all') {
                                    //$getRoles->remove_cap('manageOptionsLeaveApplicationsView');
                                    $getRoles->add_cap('manageOptionsLeaveRules');
                                }
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsLeaveApplicationsView') {
                            if (isset($getRoles->capabilities['manageOptionsLeaveApplicationsView']) && $getRoles->capabilities['manageOptionsLeaveApplicationsView'] == 1) {
                                if ($wphrmAllpermission == 'all') {
                                    $getRoles->remove_cap('manageOptionsLeaveApplicationsView');
                                    $getRoles->add_cap('manageOptionsLeaveApplications');
                                }
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsLeaveApplications') {
                            if (isset($getRoles->capabilities['manageOptionsLeaveApplications']) && $getRoles->capabilities['manageOptionsLeaveApplications'] == 1) {
                                if ($wphrmAllpermission == 'view') {
                                    $getRoles->remove_cap('manageOptionsLeaveApplications');
                                    $getRoles->add_cap('manageOptionsLeaveApplicationsView');
                                }
                            }elseif (isset($getRoles->capabilities['manageOptionsLeaveApplicationsView']) && $getRoles->capabilities['manageOptionsLeaveApplicationsView'] == 1) {
                                if($wphrmAllpermission == 'all'){
                                    $getRoles->remove_cap('manageOptionsLeaveApplicationsView');
                                    $getRoles->add_cap('manageOptionsLeaveApplications');
                                }
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsSalaryView') {
                            if (isset($getRoles->capabilities['manageOptionsSalaryView']) && $getRoles->capabilities['manageOptionsSalaryView'] == 1) {
                                if ($wphrmAllpermission == 'all') {
                                    $getRoles->remove_cap('manageOptionsSalaryView');
                                    $getRoles->add_cap('manageOptionsSalary');
                                }
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsSalary') {
                            if (isset($getRoles->capabilities['manageOptionsSalary']) && $getRoles->capabilities['manageOptionsSalary'] == 1) {
                                if ($wphrmAllpermission == 'view') {
                                    $getRoles->remove_cap('manageOptionsSalary');
                                    $getRoles->add_cap('manageOptionsSalaryView');
                                }
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsDashboardView') {
                            if (isset($getRoles->capabilities['manageOptionsDashboardView']) && $getRoles->capabilities['manageOptionsDashboardView'] == 1) {
                                if ($wphrmAllpermission == 'all') {
                                    $getRoles->remove_cap('manageOptionsDashboardView');
                                    $getRoles->add_cap('manageOptionsDashboard');
                                }
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsDashboard') {
                            if (isset($getRoles->capabilities['manageOptionsDashboard']) && $getRoles->capabilities['manageOptionsDashboard'] == 1) {
                                if ($wphrmAllpermission == 'view') {
                                    $getRoles->remove_cap('manageOptionsDashboard');
                                    $getRoles->add_cap('manageOptionsDashboardView');
                                }
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsDashboardView') {
                            if (isset($getRoles->capabilities['manageOptionsDashboardView']) && $getRoles->capabilities['manageOptionsDashboardView'] == 1) {
                                if ($wphrmAllpermission == 'view') {
                                    $getRoles->remove_cap('manageOptionsDashboard');
                                }
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsFinancials') {
                            if (isset($getRoles->capabilities['manageOptionsFinancials']) && $getRoles->capabilities['manageOptionsFinancials'] == 1) {
                                if ($wphrmAllpermission == 'view') {
                                    $getRoles->remove_cap('manageOptionsFinancials');
                                }
                            } else {
                                if ($wphrmAllpermission == 'view') {
                                    $getRoles->remove_cap('manageOptionsFinancials');
                                } else if ($wphrmAllpermission == 'all') {
                                    $getRoles->add_cap('manageOptionsFinancials');
                                }
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsSettings') {
                            if (isset($getRoles->capabilities['manageOptionsSettings']) && $getRoles->capabilities['manageOptionsSettings'] == 1) {
                                if ($wphrmAllpermission == 'view') {
                                    $getRoles->remove_cap('manageOptionsSettings');
                                }
                            } else {
                                if ($wphrmAllpermission == 'view') {
                                    $getRoles->remove_cap('manageOptionsSettings');
                                } else if ($wphrmAllpermission == 'all') {
                                    $getRoles->add_cap('manageOptionsSettings');
                                }
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsDepartment') {
                            if (isset($getRoles->capabilities['manageOptionsDepartment']) && $getRoles->capabilities['manageOptionsDepartment'] == 1) {
                                if ($wphrmAllpermission == 'view') {
                                    $getRoles->remove_cap('manageOptionsDepartment');
                                }
                            } else {
                                if ($wphrmAllpermission == 'view') {
                                    $getRoles->remove_cap('manageOptionsDepartment');
                                } else if ($wphrmAllpermission == 'all') {
                                    $getRoles->add_cap('manageOptionsDepartment');
                                }
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsNotifications') {
                            if (isset($getRoles->capabilities['manageOptionsNotifications']) && $getRoles->capabilities['manageOptionsNotifications'] == 1) {
                                if ($wphrmAllpermission == 'view') {
                                    $getRoles->remove_cap('manageOptionsNotifications');
                                }
                            } else {
                                if ($wphrmAllpermission == 'view') {
                                    $getRoles->remove_cap('manageOptionsNotifications');
                                } else if ($wphrmAllpermission == 'all') {
                                    $getRoles->add_cap('manageOptionsNotifications');
                                }
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsFbGroup') {
                            if (isset($getRoles->capabilities['manageOptionsFbGroup']) && $getRoles->capabilities['manageOptionsFbGroup'] == 1) {
                                if ($wphrmAllpermission == 'view') {
                                    $getRoles->remove_cap('manageOptionsFbGroup');
                                }
                            } else {
                                if ($wphrmAllpermission == 'view') {
                                    $getRoles->remove_cap('manageOptionsFbGroup');
                                } else if ($wphrmAllpermission == 'all') {
                                    $getRoles->add_cap('manageOptionsFbGroup');
                                }
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsFileDocuments') {
                            if (isset($getRoles->capabilities['manageOptionsFileDocuments']) && $getRoles->capabilities['manageOptionsFileDocuments'] == 1) {
                                if ($wphrmAllpermission == 'all') {
                                    $getRoles->remove_cap('manageOptionsFileDocumentsView');
                                    $getRoles->add_cap('manageOptionsFileDocuments');
                                }elseif ($wphrmAllpermission == 'view') {
                                    $getRoles->remove_cap('manageOptionsFileDocuments');
                                    $getRoles->add_cap('manageOptionsFileDocumentsView');
                                }
                            }else{
                                if ($wphrmAllpermission == 'all') {
                                    $getRoles->remove_cap('manageOptionsFileDocumentsView');
                                    $getRoles->add_cap('manageOptionsFileDocuments');
                                }elseif ($wphrmAllpermission == 'view') {
                                    $getRoles->remove_cap('manageOptionsFileDocuments');
                                    $getRoles->add_cap('manageOptionsFileDocumentsView');
                                }
                            }
                        }
                    } else {
                        if (isset($pagenames) && $pagenames == 'manageOptionsEmployee') {
                            if ($wphrmAllpermission == 'view') {
                                $getRoles->remove_cap('manageOptionsEmployee');
                                $getRoles->add_cap('manageOptionsEmployeeView');
                            } else if ($wphrmAllpermission == 'all') {
                                $getRoles->remove_cap('manageOptionsEmployeeView');
                                $getRoles->add_cap('manageOptionsEmployee');
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsEmployeeView') {
                            if ($wphrmAllpermission == 'all') {
                                $getRoles->remove_cap('manageOptionsEmployeeView');
                                $getRoles->add_cap('manageOptionsEmployee');
                            } else if ($wphrmAllpermission == 'view') {
                                $getRoles->remove_cap('manageOptionsEmployee');
                                $getRoles->add_cap('manageOptionsEmployeeView');
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsHolidays') {
                            if ($wphrmAllpermission == 'view') {
                                $getRoles->remove_cap('manageOptionsHolidays');
                                $getRoles->add_cap('manageOptionsHolidayView');
                            } else if ($wphrmAllpermission == 'all') {
                                $getRoles->remove_cap('manageOptionsHolidayView');
                                $getRoles->add_cap('manageOptionsHolidays');
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsHolidayView') {
                            if ($wphrmAllpermission == 'all') {
                                $getRoles->remove_cap('manageOptionsHolidayView');
                                $getRoles->add_cap('manageOptionsHolidays');
                            } else if ($wphrmAllpermission == 'view') {
                                $getRoles->remove_cap('manageOptionsHolidays');
                                $getRoles->add_cap('manageOptionsHolidayView');
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsNotice') {
                            if ($wphrmAllpermission == 'view') {
                                $getRoles->remove_cap('manageOptionsNotice');
                                $getRoles->add_cap('manageOptionsNoticeView');
                            } else if ($wphrmAllpermission == 'all') {
                                $getRoles->remove_cap('manageOptionsNoticeView');
                                $getRoles->add_cap('manageOptionsNotice');
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsNoticeView') {
                            if ($wphrmAllpermission == 'all') {
                                $getRoles->remove_cap('manageOptionsNoticeView');
                                $getRoles->add_cap('manageOptionsNotice');
                            } else if ($wphrmAllpermission == 'view') {
                                $getRoles->remove_cap('manageOptionsNotice');
                                $getRoles->add_cap('manageOptionsNoticeView');
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsLeaveRules') {
                            if ($wphrmAllpermission == 'view') {
                                $getRoles->remove_cap('manageOptionsLeaveRules');
                                //$getRoles->add_cap('manageOptionsLeaveApplicationsView');
                            } else if ($wphrmAllpermission == 'all') {
                                $getRoles->remove_cap('manageOptionsLeaveRules');
                                $getRoles->add_cap('manageOptionsLeaveRules');
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsLeaveApplications') {
                            if ($wphrmAllpermission == 'view') {
                                $getRoles->remove_cap('manageOptionsLeaveApplications');
                                $getRoles->add_cap('manageOptionsLeaveApplicationsView');
                            } else if ($wphrmAllpermission == 'all') {
                                $getRoles->remove_cap('manageOptionsLeaveApplicationsView');
                                $getRoles->add_cap('manageOptionsLeaveApplications');
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsLeaveApplicationsView') {
                            if ($wphrmAllpermission == 'all') {
                                $getRoles->remove_cap('manageOptionsLeaveApplicationsView');
                                $getRoles->add_cap('manageOptionsLeaveApplications');
                            } else if ($wphrmAllpermission == 'view') {
                                $getRoles->remove_cap('manageOptionsLeaveApplications');
                                $getRoles->add_cap('manageOptionsLeaveApplicationsView');
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsSalary') {
                            if ($wphrmAllpermission == 'view') {
                                $getRoles->remove_cap('manageOptionsSalary');
                                $getRoles->add_cap('manageOptionsSalaryView');
                            } else if ($wphrmAllpermission == 'all') {
                                $getRoles->remove_cap('manageOptionsSalaryView');
                                $getRoles->add_cap('manageOptionsSalary');
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsSalaryView') {
                            if ($wphrmAllpermission == 'all') {
                                $getRoles->remove_cap('manageOptionsSalaryView');
                                $getRoles->add_cap('manageOptionsSalary');
                            } else if ($wphrmAllpermission == 'view') {
                                $getRoles->remove_cap('manageOptionsSalary');
                                $getRoles->add_cap('manageOptionsSalaryView');
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsAttendances') {
                            if ($wphrmAllpermission == 'view') {
                                $getRoles->remove_cap('manageOptionsAttendances');
                                $getRoles->add_cap('manageOptionsAttendancesView');
                            } else if ($wphrmAllpermission == 'all') {
                                $getRoles->remove_cap('manageOptionsAttendancesView');
                                $getRoles->add_cap('manageOptionsAttendances');
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsAttendancesView') {
                            if ($wphrmAllpermission == 'all') {
                                $getRoles->remove_cap('manageOptionsAttendancesView');
                                $getRoles->add_cap('manageOptionsAttendances');
                            } else if ($wphrmAllpermission == 'view') {
                                $getRoles->remove_cap('manageOptionsAttendances');
                                $getRoles->add_cap('manageOptionsAttendancesView');
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsDashboard') {
                            if ($wphrmAllpermission == 'view') {
                                $getRoles->remove_cap('manageOptionsDashboard');
                                $getRoles->add_cap('manageOptionsDashboardView');
                            } else if ($wphrmAllpermission == 'all') {
                                $getRoles->remove_cap('manageOptionsDashboardView');
                                $getRoles->add_cap('manageOptionsDashboard');
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsDashboardView') {
                            if ($wphrmAllpermission == 'all') {
                                $getRoles->add_cap('manageOptionsDashboard');
                                $getRoles->remove_cap('manageOptionsDashboardView');
                            } else if ($wphrmAllpermission == 'view') {
                                $getRoles->remove_cap('manageOptionsDashboard');
                                $getRoles->add_cap('manageOptionsDashboardView');
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsFinancials') {
                            if ($wphrmAllpermission == 'all') {
                                $getRoles->add_cap('manageOptionsFinancials');
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsSettings') {
                            if ($wphrmAllpermission == 'all') {
                                $getRoles->add_cap('manageOptionsSettings');
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsFbGroup') {
                            if ($wphrmAllpermission == 'all') {
                                $getRoles->add_cap('manageOptionsFbGroup');
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsDepartment') {
                            if ($wphrmAllpermission == 'all') {
                                $getRoles->add_cap('manageOptionsDepartment');
                            }
                        } else if (isset($pagenames) && $pagenames == 'manageOptionsNotifications') {
                            if ($wphrmAllpermission == 'all') {
                                $getRoles->add_cap('manageOptionsNotifications');
                            }
                        }
                    }
                }
            }
        }
        return;
    }

    public function wphrmAddMainMenu($wphrmManupages) {
        $NotificationCounters = '';
        $NotificationCounter = $this->WPHRMNotificationCounter();
        if ($NotificationCounter != '') {
            $NotificationCounters = $NotificationCounter;
        }
        foreach ($wphrmManupages as $pageKey => $pagenames) {
            if ($pagenames == 'manageOptionsEmployee') {
                $wphrmPage = add_submenu_page('wphrm', 'Employees', __('Employees', 'wphrm'), 'manageOptionsEmployee', 'wphrm-employees', array(&$this, 'WPHRMEmployees'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            } else if ($pagenames == 'manageOptionsDepartment') {
                $wphrmPage = add_submenu_page('wphrm', 'Departments', __('Departments', 'wphrm'), 'manageOptionsDepartment', 'wphrm-departments', array(&$this, 'WPHRMDepartments'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            } else if ($pagenames == 'manageOptionsHolidays') {
                $wphrmPage = add_submenu_page('wphrm', 'Holidays', __('Holidays', 'wphrm'), 'manageOptionsHolidays', 'wphrm-holidays', array(&$this, 'WPHRMHolidaysCallback'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            } else if ($pagenames == 'manageOptionsAttendances') {
                $wphrmPage = add_submenu_page('wphrm', 'Attendance Management', __('Attendance Management', 'wphrm'), 'manageOptionsAttendances', 'wphrm-attendances', array(&$this, 'WPHRMAttendancesCallback'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            } else if ($pagenames == 'manageOptionsLeaveRules') {
                $wphrmPage = add_submenu_page('wphrm', 'Leave Rules Management', __('Leave Rules Management', 'wphrm'), 'manageOptionsLeaveRules', 'wphrm-leave-rules', array(&$this, 'WPHRMLeaveRulesCallback'));
            } else if ($pagenames == 'manageOptionsLeaveApplications') {
                $wphrmPage = add_submenu_page('wphrm', 'Leave Management', __('Leave Management', 'wphrm'), 'manageOptionsLeaveApplications', 'wphrm-leaves-application', array(&$this, 'WPHRMLeavesApplicationCallback'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            } else if ($pagenames == 'manageOptionsLeaveApplicationsView') {
                $wphrmPage = add_submenu_page('wphrm', 'Leave Management', __('Leave Management', 'wphrm'), 'manageOptionsLeaveApplicationsView', 'wphrm-leaves-application', array(&$this, 'WPHRMLeavesApplicationCallback'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            } else if ($pagenames == 'manageOptionsSalary') {
                $wphrmPage = add_submenu_page('wphrm', 'Salary Management', __('Salary Management', 'wphrm'), 'manageOptionsSalary', 'wphrm-salary', array(&$this, 'WPHRMSalaryCallback'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            }  else if ($pagenames == 'manageOptionsFinancials') {
                $wphrmPage = add_submenu_page('wphrm', 'Finance Management', __('Finance Management', 'wphrm'), 'manageOptionsFinancials', 'wphrm-financials', array(&$this, 'WPHRMFinancialsCallback'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            }else if ($pagenames == 'manageOptionsNotice') {
                $wphrmPage = add_submenu_page('wphrm', 'Notices', __('Notices', 'wphrm'), 'manageOptionsNotice', 'wphrm-notice', array(&$this, 'WPHRMNoticeCallback'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
                $wphrmPage = add_submenu_page('wphrm-notice', '', '', 'manageOptionsNotice', 'wphrm-add-notice', array(&$this, 'WPHRMAddNotice'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            }else if ($pagenames == 'manageOptionsNotifications') {
                $wphrmPage = add_submenu_page('wphrm', 'Notifications', __('Notifications ' . $NotificationCounters, 'wphrm'), 'manageOptionsNotifications', 'wphrm-notifications', array(&$this, 'WPHRMNotificationsCallback'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            }else if ($pagenames == 'manageOptionsSettings') {
                $wphrmPage = add_submenu_page('wphrm', 'Settings', __('Settings', 'wphrm'), 'manageOptionsSettings', 'wphrm-settings', array(&$this, 'WPHRMSettingsCallback'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            }else if ($pagenames == 'manageOptionsFbGroup') {
                /*$wphrmPage = add_submenu_page('wphrm', 'FB Support Group', __('FB Support Group', 'wphrm'), 'manageOptionsFbGroup', 'wphrm-fb-support', array(&$this, 'WPHRMFBSupportCallback'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
                $wphrmPage = add_submenu_page('wphrm', 'Documentation', __('Documentation', 'wphrm'), 'manageOptionsFbGroup', 'wphrm-documentation', array(&$this, 'WPHRMDocumentsSupportCallback'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
                $wphrmPage = add_submenu_page('wphrm', 'Support Desk', __('Support Desk', 'wphrm'), 'manageOptionsFbGroup', 'wphrm-support', array(&$this, 'WPHRMSupportCallback'));
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));*/
            }else if ($pagenames == 'manageOptionsFileDocuments') {
                $wphrmPage = add_submenu_page('wphrm', 'Files & Documents', __('Files & Documents', 'wphrm'), 'manageOptionsFileDocuments', 'wphrm-files', array(&$this, 'WPHRMFileDocuments') );
                add_action("admin_print_styles-{$wphrmPage}", array(&$this, 'WPHRMEnqueues'));
            }
        }
        return;
    }

    public function wphrmRoleWiseCapabilityGet() {
        $message = array();
        $rolenameget = '';
        if (isset($_POST['rolenameget']) && $_POST['rolenameget'] != '') {
            $rolenameget = sanitize_text_field($_POST['rolenameget']);
        }
        $getRoles = get_role($rolenameget);
        $wphrmCheckPages = $this->wphrmCheckPages;
        foreach ($getRoles->capabilities as $key => $wphrmDefineCapability) {
            $wphrmGetDefineCapability[] = $key;
        }
        $wphrmGetDefineCapabilityData = array_merge_recursive($wphrmGetDefineCapability, $wphrmCheckPages);
        $wphrmUserCapabilities = $this->wphrmUserCapabilities;
        foreach ($wphrmUserCapabilities as $key => $wphrmUserCapabilities) {
            $wphrmUserCapability[] = $wphrmUserCapabilities;
        }
        $wphrmUserDefineCapabilities = $this->wphrmUserDefineCapability;
        $i = 1;
        $html .='<thead><tr><th>Role Name</th>
                                    <th>Permissions</th>
                                </tr>
                            </thead>';
        foreach ($wphrmUserDefineCapabilities as $key => $getRole) {
            if (in_array($key, $wphrmGetDefineCapabilityData)) {
                if (in_array($key, $wphrmUserCapability)) {
                    $html .= '<tr>td>' . $i . '</td>' .
                        '<td>' . $getRole . '</td>' .
                        '<td><div class="radio-list" data-error-container="#form_2_membership_error">' .
                        '<input type="hidden" name="pageName[' . $i . ']" value=' . $key . '>' .
                        '<input style="margin: 9px 4px 10px;" name="wphrm-all[' . $i . ']" id="wphrm-according-' . $i . '" value="all" type="radio">&nbsp;All &nbsp;&nbsp;&nbsp;&nbsp;' .
                        '<input style="margin: 9px 4px 10px;" name="wphrm-all[' . $i . ']" id="wphrm-according-' . $i . '" value="view" checked type="radio">&nbsp;Only View' .
                        '</div></td></tr>';
                } else {
                    if (in_array($key, $wphrmGetDefineCapability)) {
                        $html .= '<tr>td>' . $i . '</td>' .
                            '<td>' . $getRole . '</td>' .
                            '<td><div class="radio-list" data-error-container="#form_2_membership_error">' .
                            '<input type="hidden" name="pageName[' . $i . ']" value=' . $key . '>' .
                            '<input style="margin: 9px 4px 10px;" name="wphrm-all[' . $i . ']" id="wphrm-according-' . $i . '" value="all" checked type="radio">&nbsp;All &nbsp;&nbsp;&nbsp;&nbsp;' .
                            '<input style="margin: 9px 4px 10px;" name="wphrm-all[' . $i . ']" id="wphrm-according-' . $i . '" value="view" type="radio">&nbsp;Only View' .
                            '</div></td></tr>';
                    } else {
                        $html .= '<tr>td>' . $i . '</td>' .
                            '<td>' . $getRole . '</td>' .
                            '<input type="hidden" name="pageName[' . $i . ']" value=' . $key . '>' .
                            '<td><div class="radio-list" data-error-container="#form_2_membership_error">' .
                            '<input style="margin: 9px 4px 10px;" name="wphrm-all[' . $i . ']" id="wphrm-according-' . $i . '" value="all" type="radio">&nbsp;All &nbsp;&nbsp;&nbsp;&nbsp;' .
                            '<input style="margin: 9px 4px 10px;" name="wphrm-all[' . $i . ']" id="wphrm-according-' . $i . '" value="view" checked type="radio">&nbsp;Only View' .
                            '</div></td></tr>';
                    }
                }
            }
            $i++;
        }
        $html .='<input type="hidden" name="roleAction" value="edit-role">';
        if (isset($html)) {
            $message['success'] = $html;
        } else {
            $message['error'] = __('Something went wrong', 'wphrm');
        }
        echo json_encode($message);
        exit;
    }

    public function WPHRMGetRoles() {
        $message = array();
        $wphrmUserAllRoles = $this->WPHRMGetUserRoles();
        $getOption .='<option value="">--Select--</option>';
        $array = array('administrator', 'editor', 'subscriber');
        foreach ($wphrmUserAllRoles as $key => $wphrmUserRoles) {
            if (!in_array($key, $array)) {
                $getOption .='<option value=' . $key . '>' . $wphrmUserRoles . '</option>';
            }
        }
        $message['success'] = $getOption;
        echo json_encode($message);
        exit;
    }

    public function WPHRMGetDefaultRolePages() {
        $message = array();
        $wphrmUserDefineCapabilities = $this->wphrmDefineCapability;
        $i = 1;
        $html .='<thead><tr><th>Role Name</th>
                                    <th>Permissions</th>
                                </tr>
                            </thead>';

        foreach ($wphrmUserDefineCapabilities as $key => $getRole) {
            $html .= '<tr>td>' . $i . '</td>' .
                '<td>' . $getRole . '</td>' .
                '<td><div class="radio-list" data-error-container="#form_2_membership_error">' .
                '<input type="hidden" name="pageName[' . $i . ']" value=' . $key . '>' .
                '<input style="margin: 9px 4px 10px;" name="wphrm-all[' . $i . ']" id="wphrm-according-' . $i . '" value="all" type="radio">&nbsp;All &nbsp;&nbsp;&nbsp;&nbsp;' .
                '<input style="margin: 9px 4px 10px;" name="wphrm-all[' . $i . ']" id="wphrm-according-' . $i . '" value="view" checked type="radio">&nbsp;Only View' .
                '</div></td></tr>';
            $i++;
        }
        $html .='<input type="hidden" name="roleAction" value="add-role">';
        if (isset($html)) {
            $message['success'] = $html;
        } else {
            $message['error'] = __('Something went wrong', 'wphrm');
        }
        echo json_encode($message);
        exit;
    }

    public function WPHRMGetPagePermissions() {
        global $current_user, $wpdb;
        $wphrmUserRole = implode(',', $current_user->roles);
        $allRoles = array();
        $getRoles = get_role($wphrmUserRole);
        if($getRoles){
            foreach ($getRoles->capabilities as $key => $getRole) {
                $allRoles[] = $key;
            }
        }
        return $allRoles;
    }

    public function WPHRMChangeRolePermission($action) {
        global $current_user, $wpdb;
        $wphrmUserRole = implode(',', $current_user->roles);
        $settingsDatas = array();
        $settingsDatas = $wpdb->get_row("select * from $this->WphrmSettingsTable where `settingKey`='$wphrmUserRole' and `authorID` = $current_user->ID");
        if ($action == 'getData') {
            if (!empty($settingsDatas)) {
                $result = $settingsDatas;
            } else {
                $result = $settingsDatas;
            }
        } else if (!empty($settingsDatas)) {
            $result = 'yes';
        } else {
            $result = 'no';
        }
        return $result;
    }

    public function WPHRMGetUserWiseData($userID, $userRole) {
        global $wpdb;
        $settingsDatas = array();
        $settingsData = $wpdb->get_row("select * from $this->WphrmSettingsTable where `settingKey` = '$userRole' and `authorID` = $userID");
        if (!empty($settingsData)) {
            $settingsDatas = $settingsData;
        }
        return $settingsDatas;
    }

    public function WPHRMCheckPageAccess($wphrmUserRole) {
        global $current_user, $wpdb;
        $settingsDatas = array();
        $settingsData = $wpdb->get_row("select * from $this->WphrmSettingsTable where `authorID` = 'defineaccess' and `settingKey`='$wphrmUserRole'");
        if (!empty($settingsData)) {
            $settingsDatas = $settingsData;
        }
        return $settingsDatas;
    }

    public function WPHRMViewOnlyPermissionPages() {
        global $current_user, $wpdb;
        $pages = array();
        $wphrmUserRole = implode(',', $current_user->roles);
        $checkPage = $this->WPHRMGetUserWiseData($current_user->ID, $wphrmUserRole);
        if (empty($checkPage)) {
            $checkPageAccess = $this->WPHRMCheckPageAccess($wphrmUserRole);
            if (!empty($checkPageAccess)) {
                $pages = unserialize(base64_decode($checkPageAccess->settingValue));
            }
        }
        return $pages;
    }

    public function WPHRMChangeRoleWiseDisplay() {
        global $current_user, $wpdb;
        $wphrmGetDefine = array();
        $message = array();
        $wphrmUserRole = implode(',', $current_user->roles);
        if (isset($_POST['wphrm-actions']) && $_POST['wphrm-actions'] == 'adddatabaserole') {
            $changeRole = $this->WPHRMChangeRolePermission('status');
            if ($changeRole == 'yes') {
                $getData = $this->WPHRMChangeRolePermission('getData');
                $defaultPages = $this->wphrmUserCapabilities;
                $wphrmUserRoles = get_role($wphrmUserRole);
                foreach ($defaultPages as $defaultPage) {
                    $wphrmUserRoles->remove_cap($defaultPage);
                }
                $getRoles = get_role($getData->settingKey);
                $pagesArray = unserialize(base64_decode($getData->settingValue));
                foreach ($pagesArray as $pages) {
                    $getRoles->remove_cap($pages);
                    $getRoles->add_cap($pages);
                }
                $wpdb->query("DELETE FROM `$this->WphrmSettingsTable` WHERE `settingKey`= '$wphrmUserRole' and `authorID` = $current_user->ID");
            }
        } else if (isset($_POST['wphrm-actions']) && $_POST['wphrm-actions'] == 'adddefaultrole') {
            $changeRole = $this->WPHRMChangeRolePermission('status');
            if ($changeRole == 'no') {
                $getRoles = get_role($wphrmUserRole);
                foreach ($getRoles->capabilities as $key => $wphrmDefineCapability) {
                    $wphrmGetDefineCapability[] = $key;
                }
                $wphrmGetDefine = base64_encode(serialize($wphrmGetDefineCapability));
                $wpdb->insert($this->WphrmSettingsTable, array('authorID' => $current_user->ID, 'settingKey' => $wphrmUserRole, 'settingValue' => $wphrmGetDefine));

                $wphrmUserDefineCapability = $this->wphrmUserDefineCapability;
                foreach ($wphrmUserDefineCapability as $key => $wphrmUserDefineCap) {
                    $capability[] = $key;
                }

                $wphrmUserRoles = get_role($wphrmUserRole);
                foreach ($wphrmUserRoles->capabilities as $key => $defaultPage) {
                    if (in_array($key, $capability)) {
                        $wphrmUserRoles->remove_cap($key);
                    }
                }
                $defaultPages = $this->wphrmUserCapabilities;
                $getUserRoles = get_role($wphrmUserRole);
                foreach ($defaultPages as $pages) {
                    $getUserRoles->remove_cap($pages);
                    $getUserRoles->add_cap($pages);
                }
            }
        }
        $message['success'] = true;
        echo json_encode($message);
        exit;
    }

    public function WPHRMDefaultRoleToLogin($addDefaultRole, $wphrmUserRoles, $userID) {
        global $current_user, $wpdb;
        $wphrmGetDefine = array();
        $message = array();
        $wphrmGetDefineCapability = array();
        $wphrmUserRole = $wphrmUserRoles;
        if (isset($addDefaultRole) && $addDefaultRole == 'adddefaultrole') {
            $changeRole = $this->WPHRMChangeRolePermission('status');
            $getRoles = get_role($wphrmUserRole);
            if($getRoles){
                foreach ($getRoles->capabilities as $key => $wphrmDefineCapability) {
                    $wphrmGetDefineCapability[] = $key;
                }
            }
            $wphrmGetDefine = base64_encode(serialize($wphrmGetDefineCapability));
            $id = $wpdb->insert($this->WphrmSettingsTable, array('authorID' => $userID, 'settingKey' => $wphrmUserRole, 'settingValue' => $wphrmGetDefine));
            $wphrmUserDefineCapability = $this->wphrmUserDefineCapability;
            foreach ($wphrmUserDefineCapability as $key => $wphrmUserDefineCap) {
                $capability[] = $key;
            }
            $wphrmUserRoles = get_role($wphrmUserRole);
            if(!empty($wphrmUserRoles->capabilities)){
                foreach ($wphrmUserRoles->capabilities as $key => $defaultPage) {
                    if (in_array($key, $capability)) {
                        $wphrmUserRoles->remove_cap($key);
                    }
                }
            }
            $defaultPages = $this->wphrmUserCapabilities;
            $getUserRoles = get_role($wphrmUserRole);
            if($defaultPages && $getUserRoles){
                foreach ($defaultPages as $pages) {
                    $getUserRoles->remove_cap($pages);
                    $getUserRoles->add_cap($pages);
                }
            }
        }
        return;
    }

    /** End WP-HRM All Roles and Capability Functions * */
    /** BEGIN WP-HRM PAGE ACTIONS * */

    /** WP-HRM Deshboard * */
    public function WPHRMDashboardCallback() {
        include_once('wphrm-dashboard.php');
    }

    /** WP-HRM Searching * */
    public function WPHRMEmployees() {
        include_once('wphrm-employee-list.php');
    }

    /** WP-HRM Department * */
    public function WPHRMDepartments() {
        include_once('wphrm-department.php');
    }

    /** WP-HRM Designation * */
    public function WPHRMAddDesignation() {
        include_once('wphrm-designation.php');
    }

    /** WP-HRM Attendance View * */
    public function WPHRMViewAttendances() {
        include_once('wphrm-view-attendance.php');
    }

    /** WP-HRM Attendance type * */
    public function WPHRMLeaveType() {
        include_once('wphrm-leave-type.php');
    }

    /** WP-HRM Mark Attendance * */
    public function WPHRMMarkAttendances() {
        include_once('wphrm-mark-attendance.php');
    }

    /** WP-HRM Employess * */
    public function WPHRMEmployeePage() {
        include_once('wphrm-employee-page.php');
    }

    /** WP-HRM Employess Information * */
    public function WPHRMEmployeeInfo() {
        include_once('wphrm-employee-info.php');
    }

    /** WP-HRM View Employess Information * */
    public function WPHRMEmployeeViewDetails() {
        include_once('wphrm-employee-view-info.php');
    }

    /** WP-HRM Holidays * */
    public function WPHRMHolidaysCallback() {
        include_once('wphrm-holidays.php');
    }

    /** WP-HRM Attendance * */
    public function WPHRMAttendancesCallback() {
        include_once('wphrm-attendances.php');
    }

    /** WP-HRM Leaves * */
    public function WPHRMLeavesApplicationCallback() {
        include_once('wphrm-leaves-application.php');
    }

    /** WP-HRM Leaves * */
    public function WPHRMLeaveRulesCallback() {
        include_once('wphrm-leave-rules.php');
    }

    /** WP-HRM Salary * */
    public function WPHRMSalaryCallback() {
        include_once('wphrm-salary.php');
    }

    /** WP-HRM Notice List * */
    public function WPHRMNoticeCallback() {
        include_once ('wphrm-notice.php');
    }

    /** WP-HRM Add Add Update Notice * */
    public function WPHRMAddNotice() {
        include_once ('wphrm-add-notice.php');
    }

    /** WP-HRM Financials * */
    public function WPHRMFinancialsCallback() {
        include_once ('wphrm-financials-report.php');
    }

    /** WP-HRM Notifications * */
    public function WPHRMNotificationsCallback() {
        include_once ('wphrm-notifications.php');
    }

    /** WP-HRM Settings * */
    public function WPHRMSettingsCallback() {
        include("wphrm-settings.php");
    }

    /** WP-HRM FB Support * */
    public function WPHRMFBSupportCallback() {
?>
<script>
    window.location = "https://www.facebook.com/groups/wphrm/";
</script>
<?php

    }

    /** WP-HRM Documents Support* */
    public function WPHRMDocumentsSupportCallback() {
?>
<script>
    window.location = "https://indigothemes.com/documentation/wphrm/";
</script>
<?php

    }

    /** WP-HRM Support * */
    public function WPHRMSupportCallback() {
?>
<script>
    window.location = "https://indigothemes.com/support";
</script>
<?php

    }

    /** WP-HRM  select financials month * */
    public function WPHRMSelectFinancialsMonth() {
        include_once('wphrm-select-financials-month.php');
    }

    /** WP-HRM  select financials week * */
    public function WPHRMSelectFinancialsWeek() {
        include_once('wphrm-select-financials-week.php');
    }

    /** WP-HRM Salary Slip * */
    public function WPHRMSalarySlipDetails() {
        include_once('wphrm-salary-slip-details.php');
    }

    /** WP-HRM Salary Slip Details Week Slip * */
    public function WPHRMSalarySlipDetailsWeek() {
        include_once('wphrm-salary-slip-details-week.php');
    }

    /** WP-HRM Employee Absent View * */
    public function WPHRMEmployeeAbsent() {
        include_once('wphrm-employee-absent.php');
    }

    /** WP-HRM Employee Total Salary View * */
    public function WPHRMTotalSalary() {
        include_once('wphrm-total-salary.php');
    }

    /** END WP-HRM PAGE ACTIONS * */
    /** BEGIN WP_HRM FUNCTIONAL ACTIONS * */

    /** WP-HRM Get Admin ID Function * */
    public function wphrmGetAdminId() {
        $this->wphrmGetAdminId = array();
        $wphrmUsersQuery = new WP_User_Query(array(
            'role' => 'administrator',
            'orderby' => 'display_name'
        ));
        $results = $wphrmUsersQuery->get_results();
        foreach($results as $user){
            $this->wphrmGetAdminId[] = $user->ID;
        }
        $wphrmUsersQuery = new WP_User_Query(array(
            'role' => 'hr_manager',
            'orderby' => 'display_name'
        ));
        $results = $wphrmUsersQuery->get_results();
        foreach($results as $user){
            $this->wphrmGetAdminId[] = $user->ID;
        }
        return $this->wphrmGetAdminId;
    }

    /** WP-HRM User Details Function * */
    public function WPHRMUserDetails($wphrmEmployeeID) {
        $userDatas = get_user_meta($wphrmEmployeeID, '', true);
        $wphrmEmployeeInfo = $this->WPHRMGetUserDatas($wphrmEmployeeID, 'wphrmEmployeeInfo');
        $wphrmEmployeeFirstName = get_user_meta($wphrmEmployeeID, 'first_name', true);
        $wphrmEmployeeLastName = get_user_meta($wphrmEmployeeID, 'last_name', true);
        $wphrmEmail = $wphrmEmployeeInfo['wphrm_employee_email'];
        return array('userName' => $wphrmEmployeeFirstName . ' ' . $wphrmEmployeeLastName, 'useremail' => $wphrmEmail);
    }

    /** WP-HRM Send Email * */
    public function WPHRMSendEmail($wphrmEmployeeID, $action, $date) {
        $message = '';
        $mailTitle = '';
        $URL = admin_url();
        $userDetails = $this->WPHRMUserDetails($wphrmEmployeeID);
        if ($action == 'approved' || $action == 'rejected') {
            $mailTitle = 'Leave status';
            $message = 'Your applied leave on ' . $date['from'] . ' to ' . $date['to'] . ' has been ' . $action . '.';
            $userEmail = $userDetails['useremail'];
            $fromName = get_bloginfo('name');
            $fromEamil = get_bloginfo('admin_email');
        }

        if ($action == 'applied') {
            $mailTitle = 'Applied for a leave';
            $message = '' . $userDetails['userName'] . ' has applied for a leave on ' . $date['from'] . ' to ' . $date['to'] . '<br>
             <a href="' . $URL . '">Click here to approve/reject link</a>';
            $userEmail = get_bloginfo('admin_email');
            $fromName = get_bloginfo('name');
            $fromEamil = $userDetails['useremail'];
        }
        $headers = "MIME-Version: 1.0\n" . "From: " . $fromName . " <" . $fromEamil . ">\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        wp_mail($userEmail, $mailTitle, $message, $headers);
        return;
    }

    /*
     * Generate Random Number Char return
     */

    public function WPHRMRandomString($length) {
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));
        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }
        return $key;
    }

    /** WP-HRM Generate the XML file * */
    public function WPHRMGenerateXMLFile() {
        ob_clean();
        global $wpdb, $current_user;
        $usersArrays = array();
        $getHolidays = array();
        $getAttendanceDatas = array();
        $getDepartment = array();
        $getDesignation = array();
        $getFinancial = array();
        $getLeave = array();
        $getNotice = array();
        $getSalary = array();
        $message = array();
        $getMessages = array();
        $getSettings = array();
        header('Content-type: "text/xml"; charset="utf8"');
        header('Content-disposition: attachment; filename="wphrm_database_file.xml"');

        $usersArray = $this->WPHRMGetEmployees();
        $employeeHolidays = $wpdb->get_results("select * from $this->WphrmHolidaysTable");
        $attendanceDatas = $wpdb->get_results("select * from $this->WphrmAttendanceTable");
        $leaveDatas = $wpdb->get_results("select * from $this->WphrmLeaveApplicationTable");
        $departmentDatas = $wpdb->get_results("select * from $this->WphrmDepartmentTable");
        $designationDatas = $wpdb->get_results("select * from $this->WphrmDesignationTable");
        $financialsDatas = $wpdb->get_results("select * from $this->WphrmFinancialsTable");
        $leaveTypeDatas = $wpdb->get_results("select * from $this->WphrmLeaveTypeTable");
        $noticeDatas = $wpdb->get_results("select * from $this->WphrmNoticeTable");
        $salaryDatas = $wpdb->get_results("select * from $this->WphrmSalaryTable");
        $messagesDatas = $wpdb->get_results("select * from $this->WphrmMessagesTable");
        $weeklyDatas = $wpdb->get_results("select * from $this->WphrmWeeklySalaryTable");
        $settingsDatas = $wpdb->get_results("select * from $this->WphrmSettingsTable");

        foreach ($employeeHolidays as $employeeHoliday) {
            $getHolidays['HolidayDatas'][] = array('wphrmDate' => $employeeHoliday->wphrmDate,
                                                   'wphrmOccassion' => $employeeHoliday->wphrmOccassion,
                                                   'createdAt' => $employeeHoliday->createdAt,
                                                   'updatedAt' => $employeeHoliday->updatedAt);
        }


        foreach ($departmentDatas as $departmentData) {
            $getDepartment['DepartmentDatas'][] = array('departmentID' => $departmentData->departmentID, 'departmentName' => $departmentData->departmentName);
        }

        foreach ($financialsDatas as $financialsData) {
            $getFinancial['FinancialDatas'][] = array(
                'wphrmItem' => $financialsData->wphrmItem,
                'wphrmAmounts' => $financialsData->wphrmAmounts,
                'wphrmStatus' => $financialsData->wphrmStatus,
                'wphrmDate' => $financialsData->wphrmDate,
            );
        }

        foreach ($messagesDatas as $messagesData) {
            $getMessages['MessagesDatas'][] = array(
                'id' => $messagesData->id,
                'messagesTitle' => $messagesData->messagesTitle,
                'messagesDesc' => $messagesData->messagesDesc,
            );
        }

        foreach ($settingsDatas as $settingsData) {
            $getSettings['SettingsDatas'][] = array(
                'id' => $settingsData->id,
                'authorID' => $settingsData->authorID,
                'settingKey' => $settingsData->settingKey,
                'settingValue' => $settingsData->settingValue,
            );
        }

        foreach ($leaveTypeDatas as $leaveTypeData) {
            $getLeave['leaveDatas'][] = array(
                'id' => $leaveTypeData->id,
                'leaveType' => $leaveTypeData->leaveType,
                'period' => $leaveTypeData->period,
                'numberOfLeave' => $leaveTypeData->numberOfLeave
            );
        }
        foreach ($designationDatas as $designationData) {
            $getDesignation['DesignationDatas'][] = array('designationID' => $designationData->designationID, 'departmentID' => $designationData->departmentID,
                                                          'designationName' => $designationData->designationName);
        }
        foreach ($noticeDatas as $noticeData) {
            $getNotice['NoticeDatas'][] = array('wphrmtitle' => $noticeData->wphrmtitle,
                                                'wphrmdesc' => $noticeData->wphrmdesc,
                                                'wphrmcreatedDate' => $noticeData->wphrmcreatedDate,
                                               );
        }

        foreach ($salaryDatas as $salaryData) {
            $getSalary['SalaryDatas'][] = array('employeeID' => $salaryData->employeeID,
                                                'employeeKey' => $salaryData->employeeKey,
                                                'employeeValue' => $salaryData->employeeValue,
                                                'date' => $salaryData->date,
                                               );
        }
        $getWeekly = array();
        foreach ($weeklyDatas as $weeklyData) {
            $getWeekly['weeklyDatas'][] = array('employeeID' => $weeklyData->employeeID,
                                                'employeeKey' => $weeklyData->employeeKey,
                                                'weekOn' => $weeklyData->weekOn,
                                                'employeeValue' => $weeklyData->employeeValue,
                                                'date' => $weeklyData->date,
                                               );
        }
        foreach ($attendanceDatas as $attendanceData) {
            $getAttendanceDatas['AttendanceData'][] = array('employeeID' => $attendanceData->employeeID,
                                                            'date' => $attendanceData->date,
                                                            'toDate' => $attendanceData->toDate,
                                                            'status' => $attendanceData->status,
                                                            'leaveType' => $attendanceData->leaveType,
                                                            'halfDayType' => $attendanceData->halfDayType,
                                                            'reason' => $attendanceData->reason,
                                                            'adminComments' => $attendanceData->adminComments,
                                                            'applicationStatus' => $attendanceData->applicationStatus,
                                                            'appliedOn' => $attendanceData->appliedOn,
                                                            'updatedBy' => $attendanceData->updatedBy,
                                                            'createdAt' => $attendanceData->createdAt,
                                                            'updatedAt' => $attendanceData->updatedAt,
                                                           );
        }

        $getLeaveDatas = array();
        foreach ($leaveDatas as $leaveData) {
            $getLeaveDatas['AttendanceData'][] = array('employeeID' => $leaveData->employeeID,
                                                       'date' => $leaveData->date,
                                                       'toDate' => $leaveData->toDate,
                                                       'leaveType' => $leaveData->leaveType,
                                                       'halfDayType' => $leaveData->halfDayType,
                                                       'reason' => $leaveData->reason,
                                                       'adminComments' => $leaveData->adminComments,
                                                       'applicationStatus' => $leaveData->applicationStatus,
                                                       'appliedOn' => $leaveData->appliedOn,
                                                       'updatedBy' => $leaveData->updatedBy,
                                                       'createdAt' => $leaveData->createdAt,
                                                       'updatedAt' => $leaveData->updatedAt,
                                                      );
        }
        foreach ($usersArray as $userDatas) {
            $userMeta = array();
            $userMeta = get_user_meta($userDatas->data->ID, '', true);
            $userRole = $userDatas->roles;
            $usersArrays['userDatas'][] = array(
                'User_id' => $userDatas->data->ID,
                'user_login' => $userDatas->data->user_login,
                'user_pass' => $userDatas->data->user_pass,
                'user_nicename' => $userDatas->data->user_nicename,
                'user_email' => $userDatas->data->user_email,
                'user_url' => $userDatas->data->user_url,
                'user_registered' => $userDatas->data->user_registered,
                'user_activation_key' => $userDatas->data->user_activation_key,
                'user_status' => $userDatas->data->user_status,
                'display_name' => $userDatas->data->display_name,
                'User_role' => $userRole,
                'userMeta' => $userMeta,
            );
        }
        $getAllDatas = array_merge($getDepartment, $getMessages, $getSettings, $getDesignation, $getFinancial, $getLeave, $getNotice, $getSalary, $getWeekly, $getHolidays, $getAttendanceDatas, $getLeaveDatas, $usersArrays);
        $xmlData = new SimpleXMLElement('<?xml version="1.0"?><data></data>');
        $this->WPHRMArrayToXml($getAllDatas, $xmlData);
        echo $xmlData->asXML();
        exit;
    }

    function WPHRMFileUpload($imageUrl = '', $postID = '', $imageType = 'text') {
        $uploadPath = wp_upload_dir();
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $returnDatas = false;
        $imageUrl = stripslashes($imageUrl);
        $uploads = wp_upload_dir();
        $ext = pathinfo(basename($imageUrl), PATHINFO_EXTENSION);
        $newfilename = basename($imageUrl);

        $filename = wp_unique_filename($uploads['path'], $newfilename, $unique_filename_callback = null);
        $wp_filetype = wp_check_filetype($filename, null);
        $fullpathfilename = $uploads['path'] . "/" . $filename;
        if (!substr_count($wp_filetype['type'], "image")) {
            return;
        }
        $existImage = str_replace(' ', '-', strtolower(preg_replace('/\.[^.]+$/', '', $newfilename)));
        $newExistImage = str_replace(' ', '-', strtolower(preg_replace('/\.[^.]+$/', '', $filename)));
        $attechmentType = $wp_filetype['type'];
        $datas['img'] = str_replace(" ", "%20", $imageUrl);
        $image_string = $this->WPHRMRFetchImage($datas);
        if (!file_exists($uploads['path'] . "/" . $filename)) :
        $fileSaved = file_put_contents($uploads['path'] . "/" . $filename, $image_string);
        if (!$fileSaved) {
            return;
        }
        endif;
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
            'post_content' => '',
            'post_status' => 'inherit',
            'guid' => $uploads['url'] . "/" . $filename
        );
        $attach_id = wp_insert_attachment($attachment, $fullpathfilename, $postID);
        $getUrl = get_post_meta($attach_id, $key = '_wp_attached_file', $single = false);
        update_post_meta($attach_id, "WPHRMImagesType", $imageType);
        return $uploadPath['baseurl'] . '/' . $getUrl[0];
    }

    public function WPHRMRFetchImage($arg) {
        if (function_exists("curl_init")) {
            return $this->WPHRMFileGetContentsCurl($arg['img']);
        } elseif (ini_get("allow_url_fopen")) {
            return $this->WPHRMFOpenFetchImage($arg['img']);
        }
    }

    public function WPHRMFOpenFetchImage($url) {
        $image = file_get_contents($url, false, $context);
        return $image;
    }

    public function WPHRMFTruncateTable() {
        global $wpdb;
        $message = array();
        $storeEmail = array();
        $wpdb->query("TRUNCATE TABLE $this->WphrmDepartmentTable");
        $wpdb->query("TRUNCATE TABLE $this->WphrmDesignationTable");
        $wpdb->query("TRUNCATE TABLE $this->WphrmSalaryTable");
        $wpdb->query("TRUNCATE TABLE $this->WphrmAttendanceTable");
        $wpdb->query("TRUNCATE TABLE $this->WphrmFinancialsTable");
        $wpdb->query("TRUNCATE TABLE $this->WphrmLeaveTypeTable");
        $wpdb->query("TRUNCATE TABLE $this->WphrmNoticeTable");
        $wpdb->query("TRUNCATE TABLE $this->WphrmHolidaysTable");

        $wphrmUsers = $this->WPHRMGetEmployees();
        if (!empty($wphrmUsers)) {
            foreach ($wphrmUsers as $key => $userdata) {
                $wphrmEmployeeInfo = $this->WPHRMGetUserDatas($userdata->data->ID, 'wphrmEmployeeInfo');
                if (!empty($wphrmEmployeeInfo) && !in_array('administrator', $userdata->roles)) {
                    wp_delete_user($userdata->data->ID, $reassign);
                }
            }
        }

        return true;
    }

    public function WPHRMFileGetContentsCurl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    public function WPHRMImportXml() {
        global $wpdb;
        $wphrmErrors = '';
        $message = array();
        $digiproUserTable = $wpdb->prefix . 'users';
        $uploaddir = WPHRMXMLLIB;
        if (isset($_FILES['wphrm_import']['type']) && $_FILES['wphrm_import']['type'] == 'text/xml') {
            $file = $uploaddir . basename($_FILES['wphrm_import']['name']);
            $raw_file_name = $_FILES['wphrm_import']['tmp_name'];
            $movefileImport = move_uploaded_file($_FILES['wphrm_import']['tmp_name'], $file);
            $storeUserID = array();
            if (!empty($movefileImport)) {
                $xml = simplexml_load_file($file);
                if (isset($xml->userDatas->item) && !empty($xml->userDatas->item)) {
                    $checkTable = $this->WPHRMFTruncateTable();
                    foreach ($xml->userDatas->item as $userDatas) {
                        $wphrmNewUserId = '';
                        $userID = '';
                        $userInfo = '';
                        $employeeInfoSerilizeData = '';
                        $documentInfoSerilizeData = '';
                        $employeeID = '';
                        $wphrmErrors = 'false';
                        if (isset($userDatas->user_email) && email_exists((string) $userDatas->user_email)) {
                            $wphrmErrors = 'true'; // Email address already registered
                        }
                        if (isset($wphrmErrors) && $wphrmErrors == 'false') {
                            $userPassword = $userDatas->user_pass;
                            $wphrmUserRole = implode(',', $userDatas->User_role);
                            $getRoles= get_role($wphrmUserRole);
                            if(!empty($getRoles)){
                                $wphrmGetRoles = $userDatas->User_role;
                            }else{
                                $wphrmGetRoles = 'subscriber';
                            }
                            if(isset($userDatas->User_role))
                                $wphrmNewUserId = wp_insert_user(array(
                                    'user_email' => (string) $userDatas->user_email,
                                    'user_pass' => '',
                                    'display_name' => (string) $userDatas->user_login,
                                    'user_login' => (string) $userDatas->user_login,
                                    'user_registered' => (string) $userDatas->user_registered,
                                    'role' => (string) $wphrmGetRoles));
                            $wpdb->query("UPDATE $digiproUserTable SET `user_pass`='$userPassword' WHERE `ID`=$wphrmNewUserId");
                            if (isset($userDatas->userMeta->wphrmEmployeeInfo->item)) {
                                $employeeProfileUrl = unserialize(base64_decode((string) $userDatas->userMeta->wphrmEmployeeInfo->item));
                                if (isset($employeeProfileUrl['employee_profile']) && !empty($employeeProfileUrl['employee_profile'])) {
                                    $getUrl = $this->WPHRMFileUpload($employeeProfileUrl['employee_profile']);
                                    $employeeProfileUrl['employee_profile'] = $getUrl;
                                    $employeeInfoSerilizeData = base64_encode(serialize($employeeProfileUrl));
                                } else {
                                    $employeeInfoSerilizeData = (string) $userDatas->userMeta->wphrmEmployeeInfo->item;
                                }
                            }

                            if (isset($userDatas->userMeta->wphrmEmployeeDocumentInfo->item)) {
                                $documentInfo = unserialize(base64_decode((string) $userDatas->userMeta->wphrmEmployeeDocumentInfo->item));
                                if (isset($documentInfo['resume']) && !empty($documentInfo['resume'])) {
                                    $getUrl = $this->WPHRMFileUpload($documentInfo['resume']);
                                    $documentInfo['resume'] = $getUrl;
                                } else {
                                    $documentInfo['resume'] = '';
                                }

                                if (isset($documentInfo['offerLetter']) && !empty($documentInfo['offerLetter'])) {
                                    $getUrl = $this->WPHRMFileUpload($documentInfo['offerLetter']);
                                    $documentInfo['offerLetter'] = $getUrl;
                                } else {
                                    $documentInfo['offerLetter'] = '';
                                }

                                if (isset($documentInfo['joiningLetter']) && !empty($documentInfo['joiningLetter'])) {
                                    $getUrl = $this->WPHRMFileUpload($documentInfo['joiningLetter']);
                                    $documentInfo['joiningLetter'] = $getUrl;
                                } else {
                                    $documentInfo['joiningLetter'] = '';
                                }

                                if (isset($documentInfo['contract']) && !empty($documentInfo['contract'])) {
                                    $getUrl = $this->WPHRMFileUpload($documentInfo['contract']);
                                    $documentInfo['contract'] = $getUrl;
                                } else {
                                    $documentInfo['contract'] = '';
                                }

                                if (isset($documentInfo['IDProof']) && !empty($documentInfo['IDProof'])) {
                                    $getUrl = $this->WPHRMFileUpload($documentInfo['IDProof']);
                                    $documentInfo['IDProof'] = $getUrl;
                                } else {
                                    $documentInfo['IDProof'] = '';
                                }
                                $documentInfoSerilizeData = base64_encode(serialize($documentInfo));
                            }


                            update_user_meta($wphrmNewUserId, "first_name", (string) $userDatas->userMeta->first_name->item);
                            update_user_meta($wphrmNewUserId, "last_name", (string) $userDatas->userMeta->last_name->item);
                            update_user_meta($wphrmNewUserId, "wphrmEmployeeInfo", $employeeInfoSerilizeData);
                            update_user_meta($wphrmNewUserId, "wphrmEmployeeDocumentInfo", $documentInfoSerilizeData);
                            update_user_meta($wphrmNewUserId, "wphrmEmployeeBankInfo", (string) $userDatas->userMeta->wphrmEmployeeBankInfo->item);
                            update_user_meta($wphrmNewUserId, "wphrmEmployeeOtherInfo", (string) $userDatas->userMeta->wphrmEmployeeOtherInfo->item);
                            update_user_meta($wphrmNewUserId, "wphrmEmployeeSalaryInfo", (string) $userDatas->userMeta->wphrmEmployeeSalaryInfo->item);

                            foreach ($xml->AttendanceData->item as $attendanceData) {
                                if ($attendanceData->employeeID == (int) $userDatas->User_id) {
                                    $wpdb->insert($this->WphrmAttendanceTable, array('employeeID' => $wphrmNewUserId, 'date' => $attendanceData->date,
                                                                                     'toDate' => $attendanceData->toDate, 'status' => $attendanceData->status, 'leaveType' => $attendanceData->leaveType, 'halfDayType' => $attendanceData->halfDayType
                                                                                     , 'reason' => $attendanceData->reason, 'adminComments' => $attendanceData->adminComments, 'applicationStatus' => $attendanceData->applicationStatus, 'appliedOn' => $attendanceData->appliedOn
                                                                                     , 'updatedBy' => $attendanceData->updatedBy, 'createdAt' => $attendanceData->createdAt, 'updatedAt' => $attendanceData->updatedAt));
                                }
                            }

                            foreach ($xml->SalaryDatas->item as $salaryDatas) {
                                if ($salaryDatas->employeeID == (int) $userDatas->User_id) {
                                    $wpdb->insert($this->WphrmSalaryTable, array('employeeID' => $wphrmNewUserId,
                                                                                 'employeeKey' => $salaryDatas->employeeKey, 'employeeValue' => $salaryDatas->employeeValue, 'date' => $salaryDatas->date));
                                }
                            }

                            foreach ($xml->weeklyDatas->item as $weeklyDatas) {
                                if ($weeklyDatas->employeeID == (int) $userDatas->User_id) {
                                    $wpdb->insert($this->WphrmWeeklySalaryTable, array('employeeID' => $wphrmNewUserId,
                                                                                       'employeeKey' => $weeklyDatas->employeeKey, 'employeeValue' => $weeklyDatas->employeeValue, 'date' => $weeklyDatas->date));
                                }
                            }
                        }
                    }

                    foreach ($xml->DepartmentDatas->item as $departmentData) {
                        $wpdb->insert($this->WphrmDepartmentTable, array('departmentID' => $departmentData->departmentID, 'departmentName' => $departmentData->departmentName));
                    }

                    foreach ($xml->DesignationDatas->item as $designationData) {
                        $wpdb->insert($this->WphrmDesignationTable, array('designationID' => $designationData->designationID, 'departmentID' => $designationData->departmentID,
                                                                          'designationName' => $designationData->designationName));
                    }


                    foreach ($xml->FinancialDatas->item as $financialData) {
                        $wpdb->insert($this->WphrmFinancialsTable, array('wphrmItem' => $financialData->wphrmItem,
                                                                         'wphrmAmounts' => $financialData->wphrmAmounts, 'wphrmStatus' => $financialData->wphrmStatus, 'wphrmDate' => $financialData->wphrmDate));
                    }


                    foreach ($xml->leaveDatas->item as $leaveDatas) {
                        $wpdb->insert($this->WphrmLeaveTypeTable, array('id' => $leaveDatas->id, 'leaveType' => $leaveDatas->leaveType,
                                                                        'period' => $leaveDatas->period, 'numberOfLeave' => $leaveDatas->numberOfLeave));
                    }

                    foreach ($xml->NoticeDatas->item as $noticeDatas) {
                        $wpdb->insert($this->WphrmNoticeTable, array('wphrmdesc' => $noticeDatas->wphrmdesc,
                                                                     'wphrmcreatedDate' => $noticeDatas->wphrmcreatedDate, 'wphrmtitle' => $noticeDatas->wphrmtitle));
                    }

                    foreach ($xml->HolidayDatas->item as $holidayDatas) {
                        $wpdb->insert($this->WphrmHolidaysTable, array('wphrmDate' => $holidayDatas->wphrmDate, 'wphrmOccassion' => $holidayDatas->wphrmOccassion,
                                                                       'createdAt' => $holidayDatas->createdAt, 'updatedAt' => $holidayDatas->updatedAt));
                    }

                    foreach ($xml->MessagesDatas->item as $messagesDatas) {
                        $messagesCheck = $wpdb->get_row("select * from $this->WphrmMessagesTable WHERE `id`= '$messagesDatas->id'");
                        if (!empty($messagesCheck)) {
                            $whereArray = array('id' => $messagesDatas->id);
                            $datas = array('messagesTitle' => $messagesDatas->messagesTitle, 'messagesDesc' => $messagesDatas->messagesDesc);
                            $wpdb->update($this->WphrmMessagesTable, $datas, $whereArray);
                        }
                    }

                    foreach ($xml->SettingsDatas->item as $settingsDatas) {
                        $serilizeData = array();
                        $settingsCheck = $wpdb->get_row("select * from $this->WphrmSettingsTable WHERE `id`= '$settingsDatas->id'");
                        if (!empty($settingsCheck)) {
                            $serilizeData = $settingsDatas->settingValue;
                            if ($settingsCheck->settingKey == 'wphrmGeneralSettingsInfo') {
                                $file_url = unserialize(base64_decode($settingsDatas->settingValue));
                                $getUrl = $this->WPHRMFileUpload($file_url['wphrm_company_logo']);
                                $file_url['wphrm_company_logo'] = $getUrl;
                                $serilizeData = base64_encode(serialize($file_url));
                            }

                            $whereArray = array('id' => $settingsDatas->id);
                            $datas = array('authorID' => $settingsDatas->authorID, 'settingKey' => $settingsDatas->settingKey, 'settingValue' => $serilizeData);
                            $wpdb->update($this->WphrmSettingsTable, $datas, $whereArray);
                        }
                    }

                    $message['success'] = __('File successfully imported.', 'wphrm');
                } else {
                    $message['error'] = __('File not supported.', 'wphrm');
                }
            } else {
                $message['error'] = __('Something went wrong', 'wphrm');
            }
        } else {
            $message['error'] = __('File not supported.', 'wphrm');
        }
        echo json_encode($message);
        exit;
    }

    public function WPHRMMonthInSalaryGenerated($employee_id, $k, $currentYear) {
        global $wpdb;
        $dateOf = $currentYear . '-' . $k . '-' . '01';
        $wphrmGenerated = array();
        $wphrmCount = array();
        $wphrmGeneratedSalary = $wpdb->get_results("SELECT * FROM $this->WphrmWeeklySalaryTable WHERE `employeeID` = $employee_id AND `date` ='$dateOf' AND `employeeKey`='employeeSalaryGenerated'");
        foreach ($wphrmGeneratedSalary as $monthG => $monthInSalaryGenerate) {
            $wphrmGenerated[] = $monthInSalaryGenerate->weekOn;
        }
        if (!empty($wphrmGenerated)) {
            $wphrmCount = $wphrmGenerated;
        }
        return $wphrmCount;
    }

    public function WPHRMArrayToXml($data, &$xml_data) {
        foreach ($data as $key => $value) {
            if (is_numeric($key)) {
                $key = 'item'; //dealing with <0/>..<n/> issues
            }
            if (is_array($value)) {
                $subnode = $xml_data->addChild($key);
                $this->WPHRMArrayToXml($value, $subnode);
            } else {
                $xml_data->addChild("$key", htmlspecialchars("$value"));
            }
        }
        return;
    }

    /* WP-HRM Get Months * */

    public function WPHRMGetMonths() {
        global $wpdb;
        $wphrmMonths = esc_sql('wphrmMonths'); // esc
        $month = $wpdb->get_row("SELECT * FROM $this->WphrmSettingsTable WHERE `authorID`=0 AND `settingKey`='$wphrmMonths'");
        return unserialize(base64_decode($month->settingValue));
    }

    /* WP-HRM Get Months * */

    public function WPHRMRequestButtonDisable($currentCalenderYear) {
        global $wpdb, $current_user;
        $montharr = array();
        $yeararr = array();
        $i = 1;
        $salarySliprequestinfomation = esc_sql('Salary Slip request'); // esc
        $wphrmNotification = $wpdb->get_results("SELECT * FROM  $this->WphrmNotificationsTable  where `wphrmFromId` = '$current_user->ID' AND `notificationType`= '$salarySliprequestinfomation'");
        if (!empty($wphrmNotification)) {
            foreach ($wphrmNotification as $keys => $wphrmNotifications) {
                $string = $wphrmNotifications->wphrmDesc;
                $split = explode(" ", $string);
                if ($currentCalenderYear == $split[count($split) - 1]) {
                    $yeararr[] = esc_sql($split[count($split) - 1]); // esc
                    $montharr[] = esc_sql($split[count($split) - 2]); // esc
                }
            }
            return array('month' => $montharr, 'year' => $yeararr);
        }
        return false;
    }

    function WPHRMDateForSpecificDayBetweenDates($startDate, $endDate, $weekdayNumber, $dayCounter) {
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);
        $dateArr = array();
        do {
            if (date("w", $startDate) != $weekdayNumber) {
                $startDate += (24 * 3600); // add 1 day
            }
        } while (date("w", $startDate) != $weekdayNumber);
        while ($startDate <= $endDate) {
            $dateArr[] = date('Y-m-d', $startDate);
            $startDate += ($dayCounter * 24 * 3600); // add 7 days
        }
        return($dateArr);
    }

    /** WP-HRM Sanitize array value
     *   @ Array value
     *   @return return Sanitize value.
     * */
    public function WPHRMSanitize($input) {
        $new_input = array();
        foreach ($input as $key => $val) {
            $new_input[$key] = sanitize_text_field($val);
        }
        return $new_input;
    }

    /** WP-HRM get user roles
     *   @access only relative the family
     *   @no-argument
     *   @return all user roles
     * */
    public function WPHRMGetUserRoles() {
        global $wp_roles;
        $wphrmUserRole = $wp_roles->roles;
        $wphrmUserRoles = array();
        foreach ($wphrmUserRole as $k => $role) :
        $wphrmUserRoles[$k] = $role['name'];
        endforeach;
        return $wphrmUserRoles;
    }

    /** WP-HRM get user roles
     *   @access only relative the family
     *   @no-argument
     *   @return all user roles
     * */
    public function WPHRMGetUserRolesKey() {
        global $wp_roles;
        $wphrmUserRole = $wp_roles->roles;
        $wphrmUserRoles = array();
        foreach ($wphrmUserRole as $k => $role) :
        $wphrmUserRoles[] = $k;
        endforeach;
        $wphrmUserRoles[] = 'Inactive';
        return $wphrmUserRoles;
    }

    /** WP-HRM Get All Employee Active User
     *   @no-argument
     *   @return if administrator all Employee Users & if employee only one datas return.
     * */
    public function WPHRMGetEmployees() {
        global $current_user;
        $wphrmEmployeeUsers = array();
        $wphrmUserRole = implode(',', $current_user->roles);
        if ($wphrmUserRole == 'administrator') {
            $getRoles = $this->WPHRMGetUserRolesKey();
            $employeeRole = array('role__in' => $getRoles);
            $wphrmEmployeeUsers = get_users($employeeRole);
        } else {
            $wphrmEmployeeUsers[] = get_userdata($current_user->ID);
        }
        return $wphrmEmployeeUsers;
    }

    /** WP-HRM Get All Employee Active User For The All User Access
     *   @no-argument
     *   @return all Employee Users datas return.
     * */
    public function WPHRMGetAllEmployees() {
        $getRoles = $this->WPHRMGetUserRolesKey();
        $employeeRole = array('role__in' => $getRoles);
        return get_users($employeeRole);
    }

    /** Month array key wise function. * */
    public function wphrmCurrentMonth($databaseMonth, $currentMonth) {
        $wphrmReturns = array();
        foreach ($databaseMonth as $dbm => $dbMonth):
        foreach ($currentMonth as $cm => $cMonth):
        if ($dbMonth == $cMonth):
        $wphrmReturns[$cm] = $cMonth;
        endif;
        endforeach;
        endforeach;
        return $wphrmReturns;
    }

    public function WPHRMCheckUserID($ID) {
        $usersCheck = get_userdata($ID);
        if ($usersCheck == false) {
            wp_redirect(admin_url('admin.php?page=wphrm-employees'), 301);
        }
    }

    /**
     *   WP-HRM get User Information
     *   Perameter ID is user id
     *   Return User Information
     * */
    public function WPHRMGetUserDatas($ID, $key) {
        $wphrmUserInfo = get_user_meta($ID, $key, true);
        $wphrmUserDatas = array();
        if ($wphrmUserInfo != '') :
        $wphrmUserDatas = unserialize(base64_decode($wphrmUserInfo));
        endif;
        return $wphrmUserDatas;
    }

    /**
     *   WP-HRM get Notification Message
     *   Perameter ID is Message ID
     *   Return Meaage Description
     * */
    public function WPHRMGetMessage($ID) {
        global $wpdb;
        $wphrmMessage = '';
        $ID = esc_sql($ID); // esc
        $messages = $wpdb->get_row("SELECT * FROM  $this->WphrmMessagesTable  where `id` = $ID");
        if (isset($messages->messagesDesc)) : $wphrmMessage = $messages->messagesDesc;
        else : $wphrmMessage = '';
        endif;
        return $wphrmMessage;
    }

    /** Date For Specific Two Day Off function. * */
    function WPHRMDateForSpecificTwoDayOff($y, $m, $weekdayNumber, $dayCounter) {
        $startDate = "$y-$m-01";
        $endDate1 = date('t', strtotime($startDate));
        $endDate = "$y-$m-$endDate1";
        $startDateTime = strtotime($startDate);
        $endDate = strtotime($endDate);
        $dateArr = array();
        do {
            if (date("w", $startDateTime) != $weekdayNumber) {
                $startDateTime += (24 * 3600); // add 1 day
            }
        } while (date("w", $startDateTime) != $weekdayNumber);
        while ($startDateTime <= $endDate) {
            $dateArr[] = date('Y-m-d', $startDateTime);
            $startDateTime += ($dayCounter * 24 * 3600); // add 7 days
        }
        return($dateArr);
    }

    /** WP-HRM Financial Reports * */
    public function WPHRMGetFinancialsReport($currentYear = null) {
        global $wpdb;
        if ($currentYear == null) : $currentYear = date('Y');
        endif;
        $wphrmProfit = array();
        $wphrmLoss = array();
        $wphrmExpenceReport = array();
        $wphrmMonths = $this->WPHRMGetMonths();
        $wphrmFinacials = $wpdb->get_results("SELECT * FROM $this->WphrmFinancialsTable");
        foreach ($wphrmFinacials as $F => $wphrmFinacial) {
            $str = $wphrmFinacial->wphrmDate;
            $dates = explode("-", $str);
            if (!empty($dates)) :
            $year = $dates[0];
            $month = $dates[1];
            foreach ($wphrmMonths as $month_key => $wphrm_month) {
                if ($year == $currentYear) {
                    if ($month == $month_key) {
                        if ($wphrmFinacial->wphrmStatus == 'Profit') {
                            $wphrmProfits = intval($wphrmFinacial->wphrmAmounts) + intval(isset($wphrmProfit[$month_key]) ? $wphrmProfit[$month_key] : '');
                            $wphrmProfit[$month_key] = $wphrmProfits;
                        }
                        if ($wphrmFinacial->wphrmStatus == 'Loss') {
                            $wphrmLosses = intval($wphrmFinacial->wphrmAmounts) + intval(isset($wphrmLoss[$month_key]) ? $wphrmLoss[$month_key] : '');
                            $wphrmLoss[$month_key] = $wphrmLosses;
                        }
                    }
                    $wphrmExpenceReport[$month_key] = intval(isset($wphrmProfit[$month_key]) ? $wphrmProfit[$month_key] : '') - intval(isset($wphrmLoss[$month_key]) ? $wphrmLoss[$month_key] : '');
                }
            }
            endif;
        }
        return $wphrmExpenceReport;
    }

    /**
     *   WP-HRM get Settings
     *   @argument key
     *   -featch settings from settingsTable using setting key
     *   @returns settings datas
     * */
    public function WPHRMGetSettings($key) {
        global $wpdb;
        $wphrmSettingsResults = array();
        $key = esc_sql($key); // esc
        $wphrmSettings = $wpdb->get_row("SELECT `settingValue` FROM  $this->WphrmSettingsTable  where `settingKey` = '$key'");
        if (!empty($wphrmSettings)) {
            $wphrmSettingsResults = unserialize(base64_decode($wphrmSettings->settingValue));
        }
        return $wphrmSettingsResults;
    }

    /** END WP_HRM FUNCTIONAL ACTIONS * */
    /** BEGIN WP_HRM AJAX PAGE ACTIONS * */

    /** Load  month wise  holidays View. * */
    public function WPHRMHolidayMonthWise() {
        global $WPHRMAJAXDATAS;
        ob_clean();
        if (isset($_POST['wphrm_year']) && isset($_POST['holiday_month'])) :
        $this->WPHRMAJAXDATAS->WPHRMGetHolidayMonth($_POST['wphrm_year'], $_POST['holiday_month']);
        $datas['wphrmHolidayMonth'] = ob_get_contents();
        ob_clean();
        endif;
        echo json_encode($datas);
        exit();
    }

    /** Load  month wise  holidays View. * */
    public function WPHRMHolidayYearWise() {
        global $WPHRMAJAXDATAS;
        ob_clean();
        if (isset($_POST['wphrm_year'])) :
        $this->WPHRMAJAXDATAS->WPHRMGetHolidayYear($_POST['wphrm_year']);
        $datas['wphrmHolidayYear'] = ob_get_contents();
        ob_clean();
        endif;
        echo json_encode($datas);
        exit();
    }

    /** Load   Financial Graph View. * */
    public function WPHRMAjaxFinancialGraphLoad() {
        global $WPHRMAJAXDATAS;
        ob_clean();
        if (isset($_POST['wphrm_year'])) :
        $this->WPHRMAJAXDATAS->WPHRMGetFinancialGraph($_POST['wphrm_year']);
        $datas['wphrmFinancialGraph'] = ob_get_contents();
        ob_clean();
        endif;
        echo json_encode($datas);
        exit();
    }

    /** ajax calender load function. * */
    public function WPHRMAjaxCalenderLoad() {
        global $WPHRMAJAXDATAS;
        $wphrmSalaryAccording = $this->WPHRMGetSettings('wphrsalaryDayOrHourlyInfo');
        ob_clean();
        $datas = array();
        if (isset($_POST['wphrm_empId']) && isset($_POST['wphrm_year'])) :
        if (isset($wphrmSalaryAccording['wphrm-according']) && $wphrmSalaryAccording['wphrm-according'] != 'Week') {
            $this->WPHRMAJAXDATAS->WPHRMGetSalaryData($_POST['wphrm_empId'], $_POST['wphrm_year']);
        } else {
            $this->WPHRMAJAXDATAS->WPHRMGetSalaryWeekData($_POST['wphrm_empId'], $_POST['wphrm_year']);
        }
        $datas['wphrmSalaryData'] = ob_get_contents();
        ob_clean();
        endif;
        echo json_encode($datas);
        exit();
    }

    /** END WP_HRM AJAX PAGE ACTIONS * */
    /** BEGIN WP_HRM AJAX ACTIONS * */

    /** WP-HRM Salary Slip PDF * */
    public function WPHRMSalarySlipPdf() {
        global $WPHRMREPORTS;
        require_once (WPHRMPDF . 'wphrmpdf.php');
        $wphrmEmployeeId = $_REQUEST['employee_id'];
        $wphrmDate = $_REQUEST['years'] . '-' . $_REQUEST['month'] . '-01';
        $wphrmDate = date('Y-m-d', strtotime($wphrmDate));
        $wphrmInfo = $this->WPHRMGetUserDatas($wphrmEmployeeId, 'wphrmEmployeeInfo');
        ob_clean();
        $pdfData = $this->WPHRMREPORTS->WPHRMGetSalarySlipPDF($wphrmEmployeeId, $wphrmDate);
        $styleSections = $pdfData['style'];
        $htmlSections = $pdfData['html']; //ob_get_contents();
        ob_clean();
        $wphrmpdf = new mPDF();
        $wphrmpdf->WriteHTML($styleSections, 1);
        $wphrmpdf->WriteHTML($htmlSections);
        $filename = $wphrmInfo['wphrm_employee_fname'] . '_' . $_REQUEST['month'] . '_' . $_REQUEST['years'] . '.pdf';
        echo $wphrmpdf->Output($filename, 'D');
    }

    /** WP-HRM Salary Slip PDF * */
    public function WPHRMSalarySlipWeekPdf() {
        global $WPHRMREPORTS;
        require_once (WPHRMPDF . 'wphrmpdf.php');
        $wphrmEmployeeId = $_REQUEST['employee_id'];
        $wphrmWeekNo = $_REQUEST['week-no'];
        $wphrmDate = $_REQUEST['years'] . '-' . $_REQUEST['month'] . '-01';
        $wphrmDate = date('Y-m-d', strtotime($wphrmDate));
        $wphrmInfo = $this->WPHRMGetUserDatas($wphrmEmployeeId, 'wphrmEmployeeInfo');
        ob_clean();
        $pdfData = $this->WPHRMREPORTS->WPHRMGetSalarySlipWeekPDF($wphrmEmployeeId, $wphrmDate, $wphrmWeekNo);
        $styleSections = $pdfData['style'];
        $htmlSections = $pdfData['html']; //ob_get_contents();
        ob_clean();
        $wphrmpdf = new mPDF();
        $wphrmpdf->WriteHTML($styleSections, 1);
        $wphrmpdf->WriteHTML($htmlSections);
        $filename = $wphrmInfo['wphrm_employee_fname'] . '_' . $_REQUEST['month'] . '_' . $_REQUEST['years'] . '.pdf';
        echo $wphrmpdf->Output($filename, 'D');
    }

    /** Genrate All Excel and pdf File function. * */
    public function WPHRMProfitLossReports() {
        global $wpdb, $WPHRMREPORTS;
        if (isset($_POST)) {
            $wphrmCheck = $wpdb->get_results("SELECT * FROM  $this->WphrmFinancialsTable");
            if (!empty($wphrmCheck)) {
                if (isset($_POST['from-date']) && isset($_POST['to-date'])) {
                    if (isset($_POST['wphrm-report-type']) && $_POST['wphrm-report-type'] == 'wphrm-dashboard-excel-reports') {
                        $this->WPHRMREPORTS->WPHRMGetProfitLossDashboardReportExcel($_POST['from-date'], $_POST['to-date'], $_POST['mainsearch']);
                        ob_clean();
                    } elseif (isset($_POST['wphrm-report-type']) && $_POST['wphrm-report-type'] == 'wphrm-excel-reports') {
                        $this->WPHRMREPORTS->WPHRMGetProfitLossReportExcel($_POST['from-date'], $_POST['to-date'], $_POST['mainsearch']);
                    } elseif (isset($_POST['wphrm-report-type']) && $_POST['wphrm-report-type'] == 'wphrm-pdf-reports') {
                        require_once (WPHRMPDF . 'wphrmpdf.php');
                        ob_clean();
                        $this->WPHRMREPORTS->WPHRMGetProfitLossReportPdf($_POST['from-date'], $_POST['to-date'], $_POST['mainsearch']);
                        $htmlSections = ob_get_contents();
                        ob_clean();
                        $wphrmpdf = new mPDF();
                        $wphrmpdf->WriteHTML($htmlSections);
                        $wphrmCurrentDate = date('dmYHis');
                        $filename = 'profitLossReports-' . $wphrmCurrentDate . '.pdf';
                        echo $wphrmpdf->Output($filename, 'D');
                    }
                }
            } else {

                if ($_POST['wphrm-report-action'] == 'finacial-report') {
                    wp_redirect(admin_url('admin.php?page=wphrm-financials'), 301);
                } else {
                    wp_redirect(admin_url('admin.php?page=wphrm-dashboard'), 301);
                }
            }
        }
    }

    /** Genrate All Excel and pdf File function. * */
    public function WPHRMTotalSalaryReports() {
        global $wpdb, $WPHRMREPORTS;
        if (isset($_POST)) {
            $wphrmCheck = $wpdb->get_results("SELECT * FROM  $this->WphrmSalaryTable");
            if (!empty($wphrmCheck)) {
                if (isset($_POST['from-date']) && isset($_POST['to-date']) && isset($_POST['wphrm-employee-id'])) {
                    if (isset($_POST['wphrm-report-type']) && $_POST['wphrm-report-type'] == 'wphrm-excel-reports') {
                        $this->WPHRMREPORTS->WPHRMGetTotalSalaryExcel($_POST['from-date'], $_POST['to-date'], $_POST['wphrm-employee-id']);
                        ob_clean();
                    } elseif (isset($_POST['wphrm-report-type']) && $_POST['wphrm-report-type'] == 'wphrm-pdf-reports') {
                        require_once (WPHRMPDF . 'wphrmpdf.php');
                        ob_clean();
                        $this->WPHRMREPORTS->WPHRMGetTotalSalaryPdf($_POST['from-date'], $_POST['to-date'], $_POST['wphrm-employee-id']);
                        $htmlSections = ob_get_contents();
                        ob_clean();
                        $wphrmpdf = new mPDF();
                        $wphrmpdf->WriteHTML($htmlSections);
                        $wphrmCurrentDate = date('dmYHis');
                        $filename = 'profitTotalSalary-' . $wphrmCurrentDate . '.pdf';
                        echo $wphrmpdf->Output($filename, 'D');
                    }
                }
            } else {
                wp_redirect(admin_url('admin.php?page=wphrm-total-salary&employee_id=' . $_POST['wphrm-employee-id']), 301);
            }
        }
    }

    /** Genrate All Excel  File function. * */
    public function WPHRMEmployeeSalaryReports() {
        global $wpdb, $WPHRMREPORTS;
        if (isset($_POST)) {
            $wphrmCheck = $wpdb->get_results("SELECT * FROM  $this->WphrmSalaryTable");
            if (!empty($wphrmCheck)) {
                if (isset($_POST['from-date']) && isset($_POST['to-date']) && isset($_POST['employee-id'])) {
                    $this->WPHRMREPORTS->WPHRMGetEmloyeeSalaryExcel($_POST['from-date'], $_POST['to-date'], $_POST['employee-id']);
                    ob_clean();
                }
            } else {
                wp_redirect(admin_url('admin.php?page=wphrm-salary'), 301);
            }
        }
    }

    /** Genrate All Excel  File function. * */
    public function WPHRMGenerateAttendanceReports() {
        global $wpdb, $WPHRMREPORTS;
        ;
        if (isset($_POST)) {
            $wphrmCheck = $wpdb->get_results("SELECT * FROM  $this->WphrmSalaryTable");
            if (!empty($wphrmCheck)) {
                if (isset($_POST['from-date']) && isset($_POST['to-date']) && isset($_POST['employee-id'])) {
                    $this->WPHRMREPORTS->WPHRMGetEmloyeeAttendanceExcel($_POST['from-date'], $_POST['to-date'], $_POST['employee-id']);
                    ob_clean();
                }
            } else {
                wp_redirect(admin_url('admin.php?page=wphrm-salary'), 301);
            }
        }
    }

    /** WP-HRM Get Salary Report Excel Formate * */
    public function WPHRMSalaryReports() {
        global $WPHRMREPORTS;
        if (isset($_POST)) {
            $this->WPHRMREPORTS->WPHRMGetSalaryReportExcel($_POST['from-date'], $_POST['to-date'], $_POST['wphrm-employee-id']);
        }
    }

    /** Get Notification Counter. * */
    public function WPHRMNotificationCounter() {
        global $current_user, $wpdb;
        $notificationCounter = '';
        $notificationCounters = $wpdb->get_row("SELECT COUNT(`id`) AS notifications FROM  $this->WphrmNotificationsTable WHERE `wphrmStatus` = 'unseen' AND  `wphrmUserID` = $current_user->ID");
        if ($notificationCounters->notifications != 0) {
            $notificationCounter = '<div class="buttonnotification"><span class="button__badge">' . $notificationCounters->notifications . '</span></div>';
            return $notificationCounter;
        }
    }
    
    /* Add Leave Rules Record */
    public function WPHRMLeaveRule() {
        global $wpdb;
        $result = array();
        $description = '';
        $leaveRule = '';
        $emp_type = 'N/A';
        $emp_status = 0;
        $yis = 0;
        $gender = 0;
        $nationality = 0;
        $age = 0;
        $marital_status = 'N/A';
        $max_children = 0;
        $child_age_limit = 0;
        $child_nationality = 0;
        $med_claim_limit = 0;
        $elderly_screening = 0;
        $maternity = 'N/A';
        $paternity = 'N/A';
        $lieu = 'N/A';
        $outstation = 'N/A';
        $examination = 'N/A';
        
        if( isset( $_POST['wphrm-leaveRule'] ) && $_POST['wphrm-leaveRule'] != '' ) {
            $leaveRule = sanitize_text_field( $_POST['wphrm-leaveRule'] );
        } elseif( isset( $_POST['wphrm-leaveRule-edit'] ) && $_POST['wphrm-leaveRule-edit'] != '' ) {
            $leaveRule = sanitize_text_field( $_POST['wphrm-leaveRule-edit'] );
        }
        
        if( isset( $_POST['wphrm-leaveRule-description'] ) && $_POST['wphrm-leaveRule-description'] != '' ) {
            $description = sanitize_text_field( $_POST['wphrm-leaveRule-description'] );
        } elseif( isset( $_POST['wphrm-leaveRule-description-edit'] ) && $_POST['wphrm-leaveRule-description-edit'] != '' ) {
            $description = sanitize_text_field( $_POST['wphrm-leaveRule-description-edit'] );
        }
        
        if( isset( $_POST['wphrm-employee-type'] ) && $_POST['wphrm-employee-type'] != '' ) {
            $emp_type = implode( ',', $_POST['wphrm-employee-type'] );
        } elseif( isset( $_POST['wphrm-employee-type-edit'] ) && $_POST['wphrm-employee-type-edit'] != '' ) {
            $emp_type = implode( ',', $_POST['wphrm-employee-type-edit'] );
        }
        
        if( isset( $_POST['wphrm-employment-status'] ) && $_POST['wphrm-employment-status'] != '' ) {
            $emp_status = sanitize_text_field( $_POST['wphrm-employment-status'] );
        } elseif( isset( $_POST['wphrm-employment-status-edit'] ) && $_POST['wphrm-employment-status-edit'] != '' ) {
            $emp_status = sanitize_text_field( $_POST['wphrm-employment-status-edit'] );
        }
        
        if( isset( $_POST['wphrm-yis'] ) && $_POST['wphrm-yis'] != '' ) {
            $yis = sanitize_text_field( $_POST['wphrm-yis'] );
        } elseif( isset( $_POST['wphrm-yis-edit'] ) && $_POST['wphrm-yis-edit'] != '' ) {
            $yis = sanitize_text_field( $_POST['wphrm-yis-edit'] );
        }
        
        if( isset( $_POST['wphrm-gender'] ) && $_POST['wphrm-gender'] != '' ) {
            $gender = sanitize_text_field( $_POST['wphrm-gender'] );
        } elseif( isset( $_POST['wphrm-gender-edit'] ) && $_POST['wphrm-gender-edit'] != '' ) {
            $gender = sanitize_text_field( $_POST['wphrm-gender-edit'] );
        }
        
        if( isset( $_POST['wphrm-nationality'] ) && $_POST['wphrm-nationality'] != '' ) {
            $nationality = sanitize_text_field( $_POST['wphrm-nationality'] );
        } elseif( isset( $_POST['wphrm-nationality-edit'] ) && $_POST['wphrm-nationality-edit'] != '' ) {
            $nationality = sanitize_text_field( $_POST['wphrm-nationality-edit'] );
        }
        
        if( isset( $_POST['wphrm-age'] ) && $_POST['wphrm-age'] != '' ) {
            $age = sanitize_text_field( $_POST['wphrm-age'] );
        } elseif( isset( $_POST['wphrm-age-edit'] ) && $_POST['wphrm-age-edit'] != '' ) {
            $age = sanitize_text_field( $_POST['wphrm-age-edit'] );
        }
        
        if( isset( $_POST['wphrm-status'] ) && $_POST['wphrm-status'] != '' ) {
            $marital_status = implode( ',', $_POST['wphrm-status'] );
        } elseif( isset( $_POST['wphrm-status-edit'] ) && $_POST['wphrm-status-edit'] != '' ) {
            $marital_status = implode( ',', $_POST['wphrm-status-edit'] );
        }
        
        if( isset( $_POST['wphrm-max-child'] ) && $_POST['wphrm-max-child'] != '' ) {
            $max_children = sanitize_text_field( $_POST['wphrm-max-child'] );
        } elseif( isset( $_POST['wphrm-max-child-edit'] ) && $_POST['wphrm-max-child-edit'] != '' ) {
            $max_children = sanitize_text_field( $_POST['wphrm-max-child-edit'] );
        }
        
        if( isset( $_POST['wphrm-age-limit'] ) && $_POST['wphrm-age-limit'] != '' ) {
            $child_age_limit = sanitize_text_field( $_POST['wphrm-age-limit'] );
        } elseif( isset( $_POST['wphrm-age-limit-edit'] ) && $_POST['wphrm-age-limit-edit'] != '' ) {
            $child_age_limit = sanitize_text_field( $_POST['wphrm-age-limit-edit'] );
        }
        
        if( isset( $_POST['wphrm-child-nationality'] ) && $_POST['wphrm-child-nationality'] != '' ) {
            $child_nationality = sanitize_text_field( $_POST['wphrm-child-nationality'] );
        } elseif( isset( $_POST['wphrm-child-nationality-edit'] ) && $_POST['wphrm-child-nationality-edit'] != '' ) {
            $child_nationality = sanitize_text_field( $_POST['wphrm-child-nationality-edit'] );
        }
        
        if( isset( $_POST['wphrm-claim-limit'] ) && $_POST['wphrm-claim-limit'] != '' ) {
            $med_claim_limit = sanitize_text_field( $_POST['wphrm-claim-limit'] );
        } elseif( isset( $_POST['wphrm-claim-limit-edit'] ) && $_POST['wphrm-claim-limit-edit'] != '' ) {
            $med_claim_limit = sanitize_text_field( $_POST['wphrm-claim-limit-edit'] );
        }
        
        if( isset( $_POST['wphrm-elderly-limit'] ) && $_POST['wphrm-elderly-limit'] != '' ) {
            $elderly_screening = sanitize_text_field( $_POST['wphrm-elderly-limit'] );
        } elseif( isset( $_POST['wphrm-elderly-limit-edit'] ) && $_POST['wphrm-elderly-limit-edit'] != '' ) {
            $elderly_screening = sanitize_text_field( $_POST['wphrm-elderly-limit-edit'] );
        }
        
        if( isset( $_POST['wphrm-maternity'] ) && $_POST['wphrm-maternity'] != '' ) {
            $maternity = sanitize_text_field( $_POST['wphrm-maternity'] );
        } elseif( isset( $_POST['wphrm-maternity-edit'] ) && $_POST['wphrm-maternity-edit'] != '' ) {
            $maternity = sanitize_text_field( $_POST['wphrm-maternity-edit'] );
        }
        
        if( isset( $_POST['wphrm-paternity'] ) && $_POST['wphrm-paternity'] != '' ) {
            $paternity = sanitize_text_field( $_POST['wphrm-paternity'] );
        } elseif( isset( $_POST['wphrm-paternity-edit'] ) && $_POST['wphrm-paternity-edit'] != '' ) {
            $paternity = sanitize_text_field( $_POST['wphrm-paternity-edit'] );
        }
        
        if( isset( $_POST['wphrm-lieu'] ) && $_POST['wphrm-lieu'] != '' ) {
            $lieu = sanitize_text_field( $_POST['wphrm-lieu'] );
        } elseif( isset( $_POST['wphrm-lieu-edit'] ) && $_POST['wphrm-lieu-edit'] != '' ) {
            $lieu = sanitize_text_field( $_POST['wphrm-lieu-edit'] );
        }
        
        if( isset( $_POST['wphrm-outstation'] ) && $_POST['wphrm-outstation'] != '' ) {
            $outstation = sanitize_text_field( $_POST['wphrm-outstation'] );
        } elseif( isset( $_POST['wphrm-outstation-edit'] ) && $_POST['wphrm-outstation-edit'] != '' ) {
            $outstation = sanitize_text_field( $_POST['wphrm-outstation-edit'] );
        }
        
        if( isset( $_POST['wphrm-examination'] ) && $_POST['wphrm-examination'] != '' ) {
            $examination = sanitize_text_field( $_POST['wphrm-examination'] );
        } elseif( isset( $_POST['wphrm-examination-edit'] ) && $_POST['wphrm-examination-edit'] != '' ) {
            $examination = sanitize_text_field( $_POST['wphrm-examination-edit'] );
        }
        
        $leaveRuleData = array(
            'leaveRule' => $leaveRule,
            'description' => $description,
            'employeeType' => $emp_type,
            'years_in_service' => $yis,
            'employment_status' => $emp_status,
            'gender' => $gender,
            'nationality' => $nationality,
            'age' => $age,
            'marital_status' => $marital_status,
            'max_children_covered' => $max_children,
            'child_age_limit' => $child_age_limit,
            'child_nationality' => $child_nationality,
            'medical_claim_limit' => $med_claim_limit,
            'elderly_screening_limit' => $elderly_screening,
            'maternity' => $maternity,
            'paternity' => $paternity,
            'off_in_lieu' => $lieu,
            'outstation' => $outstation,
            'examination' => $examination
        );
        
        
        
        if( isset( $_POST['wphrm-leaverule-id'] ) && $_POST['wphrm-leaverule-id'] != '' ) {
            $rule_id = sanitize_text_field( $_POST['wphrm-leaverule-id'] );
            $sql = $wpdb->update( $this->WphrmLeaveRulesTable, $leaveRuleData, array( 'id' => $rule_id ) );
        } else {
            $sql = $wpdb->insert( $this->WphrmLeaveRulesTable, $leaveRuleData);
        }
        
        if( $sql ) {
            $result['status'] = true;
        } else {
            $result['status'] = true;
        }
        echo json_encode($result);
        exit;
    }

    /** Add Employee Details function. * */
    public function WPHRMEmployeeBasicInfo() {
        global $current_user;
        $wphrmCurrentUserRole = implode(',', $current_user->roles);
        $wphrmGetPagePermissions = $this->WPHRMGetPagePermissions();
        $result = array();
        $password = '';
        $movefile_employee_profile = '';
        $wphrm_employee_permanant_address = '';
        $wphrm_employee_local_address = '';
        $wphrm_employee_bod = '';
        $wphrm_employee_phone = '';
        $wphrm_employee_status = '';
        $wphrm_employee_gender = '';
        $wphrm_employee_joining_date = '';
        $wphrm_employee_designation = '';
        $wphrm_employee_department = '';
        $wphrm_employee_userid = '';
        $wphrm_employee_uniqueid = '';
        $wphrm_employee_email = '';
        $wphrm_employee_lname = '';
        $wphrm_employee_fname = '';
        $wphrm_employee_mname = '';
        $Employeerole = '';

        if (isset($_POST['wphrm_employee_password']) && $_POST['wphrm_employee_password'] != '') {
            $password = sanitize_text_field($_POST['wphrm_employee_password']);
        }
        if (isset($_POST['wphrm_employee_fname']) && $_POST['wphrm_employee_fname'] != '') {
            $wphrm_employee_fname = sanitize_text_field($_POST['wphrm_employee_fname']);
        }
        if (isset($_POST['wphrm_employee_lname']) && $_POST['wphrm_employee_lname'] != '') {
            $wphrm_employee_lname = sanitize_text_field($_POST['wphrm_employee_lname']);
        }
        if (isset($_POST['wphrm_employee_mname']) && $_POST['wphrm_employee_mname'] != '') {
            $wphrm_employee_mname = sanitize_text_field($_POST['wphrm_employee_mname']);
        }
        if (isset($_POST['wphrm_employee_email']) && $_POST['wphrm_employee_email'] != '') {
            $wphrm_employee_email = sanitize_text_field($_POST['wphrm_employee_email']);
        }
        if (isset($_POST['wphrm_employee_userid']) && $_POST['wphrm_employee_userid'] != '') {
            $wphrm_employee_userid = sanitize_text_field($_POST['wphrm_employee_userid']);
        }
        if (isset($_POST['wphrm_employee_uniqueid']) && $_POST['wphrm_employee_uniqueid'] != '') {
            $wphrm_employee_uniqueid = empty($_POST['wphrm_employee_uniqueid']) ? $wphrm_employee_userid : sanitize_text_field($_POST['wphrm_employee_uniqueid']);
        }
        if (isset($_POST['wphrm_employee_department']) && $_POST['wphrm_employee_department'] != '') {
            $wphrm_employee_department = sanitize_text_field($_POST['wphrm_employee_department']);
        }
        if (isset($_POST['wphrm_employee_designation']) && $_POST['wphrm_employee_designation'] != '') {
            $wphrm_employee_designation = sanitize_text_field($_POST['wphrm_employee_designation']);
        }
        if (isset($_POST['wphrm_employee_joining_date']) && $_POST['wphrm_employee_joining_date'] != '') {
            $wphrm_employee_joining_date = sanitize_text_field($_POST['wphrm_employee_joining_date']);
        }
        if (isset($_POST['wphrm_employee_gender']) && $_POST['wphrm_employee_gender'] != '') {
            $wphrm_employee_gender = sanitize_text_field($_POST['wphrm_employee_gender']);
        }
        if (isset($_POST['wphrm_employee_status']) && $_POST['wphrm_employee_status'] != '') {
            $wphrm_employee_status = sanitize_text_field($_POST['wphrm_employee_status']);
        }
        if (isset($_POST['wphrm_employee_phone']) && $_POST['wphrm_employee_phone'] != '') {
            $wphrm_employee_phone = sanitize_text_field($_POST['wphrm_employee_phone']);
        }
        if (isset($_POST['wphrm_employee_bod']) && $_POST['wphrm_employee_bod'] != '') {
            $wphrm_employee_bod = sanitize_text_field($_POST['wphrm_employee_bod']);
        }
        if (isset($_POST['wphrm_employee_local_address']) && $_POST['wphrm_employee_local_address'] != '') {
            $wphrm_employee_local_address = sanitize_text_field($_POST['wphrm_employee_local_address']);
        }
        if (isset($_POST['wphrm_employee_permanant_address']) && $_POST['wphrm_employee_permanant_address'] != '') {
            $wphrm_employee_permanant_address = sanitize_text_field($_POST['wphrm_employee_permanant_address']);
        }

        $Employeerole = empty($_POST['wphrm_employee_role']) ? sanitize_text_field('Inactive') : sanitize_text_field($_POST['wphrm_employee_role']);

        /*J@F*/
        $info_type = empty($_POST['info_type']) ? 'personal_info' : sanitize_text_field($_POST['info_type']);

        $wphrm_employee_mstatus = '';
        if (isset($_POST['wphrm_employee_mstatus']) && $_POST['wphrm_employee_mstatus'] != '') {
            $wphrm_employee_mstatus = sanitize_text_field($_POST['wphrm_employee_mstatus']);
        }
        $wphrm_employee_approving_officer = '';
        if (isset($_POST['wphrm_employee_approving_officer']) && $_POST['wphrm_employee_approving_officer'] != '') {
            $wphrm_employee_approving_officer = sanitize_text_field($_POST['wphrm_employee_approving_officer']);
        }
        $wphrm_employee_employment_country = '';
        if (isset($_POST['wphrm_employee_employment_country']) && $_POST['wphrm_employee_employment_country'] != '') {
            $wphrm_employee_employment_country = sanitize_text_field($_POST['wphrm_employee_employment_country']);
        }
        $wphrm_employee_race = '';
        if (isset($_POST['wphrm_employee_race']) && $_POST['wphrm_employee_race'] != '') {
            $wphrm_employee_race = sanitize_text_field($_POST['wphrm_employee_race']);
        }
        $wphrm_employee_religion = '';
        if (isset($_POST['wphrm_employee_religion']) && $_POST['wphrm_employee_religion'] != '') {
            $wphrm_employee_religion = sanitize_text_field($_POST['wphrm_employee_religion']);
        }
        $wphrm_employee_home_phone = '';
        if (isset($_POST['wphrm_employee_home_phone']) && $_POST['wphrm_employee_home_phone'] != '') {
            $wphrm_employee_home_phone = sanitize_text_field($_POST['wphrm_employee_home_phone']);
        }
        $wphrm_employee_reporting_manager = array();
        if (!empty($_POST['wphrm_employee_reporting_manager'])) {
            foreach ($_POST['wphrm_employee_reporting_manager'] as $select_option) $wphrm_employee_reporting_manager[] = $select_option;
        }
        $wphrm_employee_entitled_leave = '';
        if (isset($_POST['wphrm_employee_entitled_leave']) && $_POST['wphrm_employee_entitled_leave'] != '') {
            $wphrm_employee_entitled_leave = explode( ',', $_POST['wphrm_employee_entitled_leave'] );
        }

        $result['post_data'] = $_POST;
        $result['$wphrm_employee_reporting_manager'] = $wphrm_employee_reporting_manager;

        $wphrm_employee_auto_approve_leave = '';
        if (isset($_POST['wphrm_employee_auto_approve_leave']) && $_POST['wphrm_employee_auto_approve_leave'] != '') {
            $wphrm_employee_auto_approve_leave = sanitize_text_field($_POST['wphrm_employee_auto_approve_leave']);
        }
        $wphrm_employee_auto_approve_leave_limit = '';
        if (isset($_POST['wphrm_employee_auto_approve_leave_limit']) && $_POST['wphrm_employee_auto_approve_leave_limit'] != '') {
            $wphrm_employee_auto_approve_leave_limit = sanitize_text_field($_POST['wphrm_employee_auto_approve_leave_limit']);
        }
        $wphrm_employee_sick_leave_limit = '';
        if (isset($_POST['wphrm_employee_sick_leave_limit']) && $_POST['wphrm_employee_sick_leave_limit'] != '') {
            $wphrm_employee_sick_leave_limit = sanitize_text_field($_POST['wphrm_employee_sick_leave_limit']);
        }
        $wphrm_employee_leaving_date = '';
        if (isset($_POST['wphrm_employee_leaving_date']) && $_POST['wphrm_employee_leaving_date'] != '') {
            $wphrm_employee_leaving_date = sanitize_text_field($_POST['wphrm_employee_leaving_date']);
        }
        $wphrm_employee_probation_period = '';
        if (isset($_POST['wphrm_employee_probation_period']) && $_POST['wphrm_employee_probation_period'] != '') {
            $wphrm_employee_probation_period = sanitize_text_field($_POST['wphrm_employee_probation_period']);
        }
        $wphrm_employee_level = '';
        if (isset($_POST['wphrm_employee_level']) && $_POST['wphrm_employee_level'] != '') {
            $wphrm_employee_level = sanitize_text_field($_POST['wphrm_employee_level']);
        }
        $wphrm_employee_leave_count = array();
        if( isset($_POST['wphrm_employee_leave_count']) ){
            $wphrm_employee_leave_count = $_POST['wphrm_employee_leave_count'];
        }
        $wphrm_employee_leave_carried = array();
        if( isset($_POST['wphrm_employee_leave_carried']) ){
            $wphrm_employee_leave_carried = $_POST['wphrm_employee_leave_carried'];
        }

        /*J@F End*/

        $wphrmEmployeeId = intval($_POST['wphrm_employee_id']);

        if (!empty($_FILES['employee_profile']['name'])) {
            $employeeProfiles = wp_check_filetype($_FILES['employee_profile']['name']);
            $employeeProfile = $employeeProfiles['ext'];
            if ($employeeProfile == 'png' || $employeeProfile == 'jpg' || $employeeProfile == 'jpeg' ||
                $employeeProfile == 'gif' || $employeeProfile == 'PNG' || $employeeProfile == 'JPG' || $employeeProfile == 'JPEG' || $employeeProfile == 'GIF') {
                $uploadedEmployeeProfileFile = $_FILES['employee_profile'];
                $uploadEmployeeProfileOverrides = array('test_form' => false);
                $movefile_employee_profile = wp_handle_upload($uploadedEmployeeProfileFile, $uploadEmployeeProfileOverrides);
            } else {
                $result['error'] = __('This is invalid file format.', 'wphrm');
                echo json_encode($result);
                exit;
            }
        }

        /*Check if we are updating or not*/
        if (!empty($wphrmEmployeeId)) { //update
            $wphrmFormDetails = array();
            $wphrmFormDetails = $this->WPHRMGetUserDatas($wphrmEmployeeId, 'wphrmEmployeeInfo');
            if (!empty($_FILES['employee_profile']['tmp_name'])) {
                $wphrmFormDetails['employee_profile'] = $movefile_employee_profile['url'];
            } else {
                $wphrmFormDetails['employee_profile'] = $wphrmFormDetails['employee_profile'];
            }

            //we update only for those specific info type
            if($info_type == 'work_info'){
                //update those related to work info
                $wphrmFormDetails['wphrm_employee_employment_country'] = $wphrm_employee_employment_country;
                //$wphrmFormDetails['wphrm_employee_uniqueid'] = $wphrm_employee_uniqueid;
                $wphrmFormDetails['wphrm_employee_status'] = $wphrm_employee_status;
                $wphrmFormDetails['wphrm_employee_department'] = $wphrm_employee_department;
                $wphrmFormDetails['wphrm_employee_designation'] = $wphrm_employee_designation;
                $wphrmFormDetails['wphrm_employee_joining_date'] = $wphrm_employee_joining_date;
                $wphrmFormDetails['wphrm_employee_level'] = $wphrm_employee_level;
                $wphrmFormDetails['wphrm_employee_reporting_manager'] = $wphrm_employee_reporting_manager;
                $wphrmFormDetails['wphrm_employee_entitled_leave'] = $wphrm_employee_entitled_leave;
                $wphrmFormDetails['wphrm_employee_leaving_date'] = $wphrm_employee_leaving_date;
                $wphrmFormDetails['wphrm_employee_probation_period'] = $wphrm_employee_probation_period;
                $wphrmFormDetails['wphrm_employee_leave_count'] = $wphrm_employee_leave_count;

                if(!empty($wphrm_employee_leave_carried)){
                    $wphrmFormDetails['wphrm_employee_leave_carried'] = $wphrm_employee_leave_carried;
                }

                $wphrmFormDetails['wphrm_employee_auto_approve_leave'] = $wphrm_employee_auto_approve_leave;
                $wphrmFormDetails['wphrm_employee_auto_approve_leave_limit'] = $wphrm_employee_auto_approve_leave_limit;
                $wphrmFormDetails['wphrm_employee_sick_leave_limit'] = $wphrm_employee_sick_leave_limit;
            }else{
                //default to personal info
                $wphrmFormDetails['wphrm_employee_fname'] = $wphrm_employee_fname;
                $wphrmFormDetails['wphrm_employee_lname'] = $wphrm_employee_lname;
                $wphrmFormDetails['wphrm_employee_mname'] = $wphrm_employee_mname;
                $wphrmFormDetails['wphrm_employee_email'] = $wphrm_employee_email;
                $wphrmFormDetails['wphrm_employee_userid'] = $wphrm_employee_userid;
                $wphrmFormDetails['wphrm_employee_uniqueid'] = $wphrm_employee_uniqueid;
                $wphrmFormDetails['wphrm_employee_password'] = $password;
                $wphrmFormDetails['wphrm_employee_gender'] = $wphrm_employee_gender;
                $wphrmFormDetails['wphrm_employee_role'] = $Employeerole;
                $wphrmFormDetails['wphrm_employee_phone'] = $wphrm_employee_phone;
                $wphrmFormDetails['wphrm_employee_bod'] = $wphrm_employee_bod;
                $wphrmFormDetails['wphrm_employee_local_address'] = $wphrm_employee_local_address;
                $wphrmFormDetails['wphrm_employee_permanant_address'] = $wphrm_employee_permanant_address;
                $wphrmFormDetails['wphrm_employee_approving_officer'] = $wphrm_employee_approving_officer;
                $wphrm_employee_display_name = $wphrm_employee_fname;

                $wphrmFormDetails['wphrm_employee_race'] = $wphrm_employee_race;
                $wphrmFormDetails['wphrm_employee_mstatus'] = $wphrm_employee_mstatus;
                $wphrmFormDetails['wphrm_employee_religion'] = $wphrm_employee_religion;
                $wphrmFormDetails['wphrm_employee_home_phone'] = $wphrm_employee_home_phone;
            }

            if (in_array('manageOptionsEmployee', $wphrmGetPagePermissions)) {
                if (isset($wphrmFormDetails['wphrm_employee_status']) && $wphrmFormDetails['wphrm_employee_status'] == 'Active') {
                    $Employeerole = $Employeerole;
                } else {
                    $Employeerole = sanitize_text_field('Inactive');
                }
            }

            if (empty($wphrmErrors)) {
                $userdata = array(
                    'ID' => $wphrmEmployeeId,
                    'user_email' => $wphrm_employee_email,
                    'display_name' => $wphrm_employee_display_name,
                    'user_login' => $wphrm_employee_userid,
                    'first_name' => $wphrm_employee_fname,
                    'last_name' => $wphrm_employee_lname,
                    'role' => $Employeerole,
                );
                if ($password != '') {
                    $userdata['user_pass'] = $password;
                }
                wp_update_user($userdata);
                $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
                ;
                update_user_meta($wphrmEmployeeId, "wphrmEmployeeInfo", $wphrmFormDetailsData);
                $result['success'] = true;
                $result['ao'] = $wphrm_employee_approving_officer;
                $result['currentrole'] = $wphrmCurrentUserRole;
            } else {
                $result['error'] = $wphrmErrors;
            }
        } else {
            $wphrmFormDetails = array();
            if (!empty($_FILES['employee_profile']['tmp_name'])) {
                $wphrmFormDetails['employee_profile'] = $movefile_employee_profile['url'];
            }

            $wphrmFormDetails['wphrm_employee_fname'] = $wphrm_employee_fname;
            $wphrmFormDetails['wphrm_employee_lname'] = $wphrm_employee_lname;
            $wphrmFormDetails['wphrm_employee_mname'] = $wphrm_employee_mname;
            $wphrmFormDetails['wphrm_employee_email'] = $wphrm_employee_email;
            $wphrmFormDetails['wphrm_employee_uniqueid'] = $wphrm_employee_uniqueid;
            $wphrmFormDetails['wphrm_employee_userid'] = $wphrm_employee_userid;
            $wphrmFormDetails['wphrm_employee_password'] = $password;
            $wphrmFormDetails['wphrm_employee_department'] = $wphrm_employee_department;
            $wphrmFormDetails['wphrm_employee_designation'] = $wphrm_employee_designation;
            $wphrmFormDetails['wphrm_employee_joining_date'] = $wphrm_employee_joining_date;
            $wphrmFormDetails['wphrm_employee_gender'] = $wphrm_employee_gender;
            $wphrmFormDetails['wphrm_employee_role'] = $Employeerole;
            $wphrmFormDetails['wphrm_employee_status'] = $wphrm_employee_status;
            $wphrmFormDetails['wphrm_employee_phone'] = $wphrm_employee_phone;
            $wphrmFormDetails['wphrm_employee_bod'] = $wphrm_employee_bod;
            $wphrmFormDetails['wphrm_employee_local_address'] = $wphrm_employee_local_address;
            $wphrmFormDetails['wphrm_employee_permanant_address'] = $wphrm_employee_permanant_address;

            /*J@F*/
            $wphrmFormDetails['wphrm_employee_mstatus'] = $wphrm_employee_mstatus;
            $wphrmFormDetails['wphrm_employee_race'] = $wphrm_employee_race;
            $wphrmFormDetails['wphrm_employee_religion'] = $wphrm_employee_religion;
            $wphrmFormDetails['wphrm_employee_home_phone'] = $wphrm_employee_home_phone;
            $wphrmFormDetails['wphrm_employee_reporting_manager'] = $wphrm_employee_reporting_manager;
            $wphrmFormDetails['wphrm_employee_entitled_leave'] = $wphrm_employee_entitled_leave;
            $wphrmFormDetails['wphrm_employee_auto_approve_leave'] = $wphrm_employee_auto_approve_leave;
            $wphrmFormDetails['wphrm_employee_auto_approve_leave_limit'] = $wphrm_employee_auto_approve_leave_limit;
            $wphrmFormDetails['wphrm_employee_sick_leave_limit'] = $wphrm_employee_sick_leave_limit;
            $wphrmFormDetails['wphrm_employee_leaving_date'] = $wphrm_employee_leaving_date;
            $wphrmFormDetails['wphrm_employee_probation_period'] = $wphrm_employee_probation_period;
            $wphrmFormDetails['wphrm_employee_level'] = $wphrm_employee_level;
            /*J@F END*/

            if (email_exists($wphrm_employee_email)) {
                $wphrmErrors = __('This email id already exists.', 'wphrm'); // Email address already registered
            }
            if (username_exists($wphrm_employee_userid)) {
                $wphrmErrors = __('Userid already taken', 'wphrm'); // Username already registered
            }
            if (empty($wphrmErrors)) {
                $wphrmNewUserId = wp_insert_user(array(
                    'user_email' => $wphrm_employee_email,
                    'user_pass' => $password,
                    'display_name' => $wphrm_employee_userid,
                    'user_login' => $wphrm_employee_userid,
                    'first_name' => $wphrm_employee_fname,
                    'user_registered' => date('Y-m-d H:i:s'),
                    'last_name' => $wphrm_employee_lname,
                    'role' => $Employeerole,
                ));
                $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
                update_user_meta($wphrmNewUserId, "wphrmEmployeeInfo", $wphrmFormDetailsData);

                $this->WPHRMSendEmail2(
                    $wphrmNewUserId,
                    'employee_created',
                    array(
                        'replacement' => array(
                            'first_name' => $wphrm_employee_fname,
                            'last_name' => $wphrm_employee_lname,
                            'email' => $wphrm_employee_email,
                            'user_id' => $wphrm_employee_userid,
                            'password' => $password,
                            'site_url' => site_url()
                        )
                    )
                );

                $result['success'] = $wphrmNewUserId;
                $result['currentrole'] = $wphrmCurrentUserRole;
            } else {
                $result['error'] = $wphrmErrors;
            }
        }
        $result['action_type'] = $info_type;
        echo json_encode($result);
        exit;
    }

    /**     *   WPHRM File Upload * */
    function WPHRMHandleUpload($file_handler, $post_id, $set_thu = false) {
        if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK)
            __return_false();
        require_once(ABSPATH . "wp-admin" . '/includes/image.php');
        require_once(ABSPATH . "wp-admin" . '/includes/file.php');
        require_once(ABSPATH . "wp-admin" . '/includes/media.php');
        $attach_id = media_handle_upload($file_handler, $post_id);
        if ($set_thu)
            set_post_thumbnail($post_id, $attach_id);
        return $attach_id;
    }

    /** Add Employee documents Details function. * */
    public function WPHRMEmployeeDocumentInfo() {
        global $current_user;
        $wphrmCurrentUserRole = implode(',', $current_user->roles);
        $result = array();
        $wphrmEmployeeId = intval($_POST['wphrm_employee_id']);
        $wphrmFormDetails['resume'] = '';
        $wphrmFormDetails['offerLetter'] = '';
        $wphrmFormDetails['joiningLetter'] = '';
        $wphrmFormDetails['contract'] = '';
        $wphrmFormDetails['IDProof'] = '';

        if (!empty($_FILES['resume']['name'])) {
            $employeeResumes = wp_check_filetype($_FILES['resume']['name']);
            $employeeResume = $employeeResumes['ext'];
            if ($employeeResume == 'png' || $employeeResume == 'jpg' || $employeeResume == 'jpeg' || $employeeResume == 'txt' || $employeeResume == 'docx' || $employeeResume == 'DOCX' || $employeeResume == 'pdf' || $employeeResume == 'TXT' || $employeeResume == 'doc' || $employeeResume == 'DOC' || $employeeResume == 'PDF' || $employeeResume == 'PNG' || $employeeResume == 'JPG' || $employeeResume == 'JPEG') {
                $uploadedResumeFile = $_FILES['resume'];
                $uploadResumeOverrides = array('test_form' => false);
                $movefileResume = wp_handle_upload($uploadedResumeFile, $uploadResumeOverrides);
            } else {
                $result['error'] = __('This is invalid file format.', 'wphrm');
            }
        }
        if (!empty($_FILES['offerLetter']['name'])) {
            $employeeOfferLetters = wp_check_filetype($_FILES['offerLetter']['name']);
            $employeeOfferLetter = $employeeOfferLetters['ext'];
            if ($employeeOfferLetter == 'PNG' || $employeeOfferLetter == 'JPG' || $employeeOfferLetter == 'JPEG' || $employeeOfferLetter == 'TXT' || $employeeOfferLetter == 'DOCX' || $employeeOfferLetter == 'PDF' || $employeeOfferLetter == 'png' || $employeeOfferLetter == 'jpg' || $employeeOfferLetter == 'doc' || $employeeOfferLetter == 'DOC' || $employeeOfferLetter == 'jpeg' || $employeeOfferLetter == 'txt' || $employeeOfferLetter == 'docx' || $employeeOfferLetter == 'pdf') {
                $uploadedOfferLetterFile = $_FILES['offerLetter'];
                $uploadOfferLetterOverrides = array('test_form' => false);
                $movefileOfferLetter = wp_handle_upload($uploadedOfferLetterFile, $uploadOfferLetterOverrides);
            } else {
                $result['error'] = __('This is invalid file format.', 'wphrm');
            }
        }
        if (!empty($_FILES['joiningLetter']['name'])) {
            $employeeJoiningLetters = wp_check_filetype($_FILES['joiningLetter']['name']);
            $employeeJoiningLetter = $employeeJoiningLetters['ext'];
            if ($employeeJoiningLetter == 'PNG' || $employeeJoiningLetter == 'JPG' || $employeeJoiningLetter == 'doc' || $employeeJoiningLetter == 'DOC' || $employeeJoiningLetter == 'JPEG' || $employeeJoiningLetter == 'TXT' || $employeeJoiningLetter == 'DOCX' || $employeeJoiningLetter == 'PDF' || $employeeJoiningLetter == 'png' || $employeeJoiningLetter == 'jpg' || $employeeJoiningLetter == 'jpeg' || $employeeJoiningLetter == 'txt' || $employeeJoiningLetter == 'docx' || $employeeJoiningLetter == 'pdf') {
                $uploadedJoiningLetterFile = $_FILES['joiningLetter'];
                $uploadJoiningLetterOverrides = array('test_form' => false);
                $movefileJoiningLetter = wp_handle_upload($uploadedJoiningLetterFile, $uploadJoiningLetterOverrides);
            } else {
                $result['error'] = __('This is invalid file format.', 'wphrm');
            }
        }
        if (!empty($_FILES['contract']['name'])) {
            $employeeContracts = wp_check_filetype($_FILES['contract']['name']);
            $employeeContract = $employeeContracts['ext'];
            if ($employeeContract == 'PNG' || $employeeContract == 'JPG' || $employeeContract == 'JPEG' || $employeeContract == 'doc' || $employeeContract == 'DOC' || $employeeContract == 'TXT' || $employeeContract == 'DOCX' || $employeeContract == 'PDF' || $employeeContract == 'png' || $employeeContract == 'jpg' || $employeeContract == 'jpeg' || $employeeContract == 'txt' || $employeeContract == 'docx' || $employeeContract == 'pdf') {
                $uploadedContractFile = $_FILES['contract'];
                $uploadContractOverrides = array('test_form' => false);
                $movefileContract = wp_handle_upload($uploadedContractFile, $uploadContractOverrides);
            } else {
                $result['error'] = __('This is invalid file format.', 'wphrm');
            }
        }
        if (!empty($_FILES['IDProof']['name'])) {
            $employeeIDProofs = wp_check_filetype($_FILES['IDProof']['name']);
            $employeeIDProof = $employeeIDProofs['ext'];
            if ($employeeIDProof == 'PNG' || $employeeIDProof == 'JPG' || $employeeIDProof == 'JPEG' || $employeeIDProof == 'TXT' || $employeeIDProof == 'doc' || $employeeIDProof == 'DOC' || $employeeIDProof == 'DOCX' || $employeeIDProof == 'PDF' || $employeeIDProof == 'png' || $employeeIDProof == 'jpg' || $employeeIDProof == 'jpeg' || $employeeIDProof == 'txt' || $employeeIDProof == 'docx' || $employeeIDProof == 'pdf') {
                $uploadedIDProofFile = $_FILES['IDProof'];
                $uploadIDProofOverrides = array('test_form' => false);
                $movefileIDProof = wp_handle_upload($uploadedIDProofFile, $uploadIDProofOverrides);
            } else {
                $result['error'] = __('This is invalid file format.', 'wphrm');
            }
        }

        if (!empty($wphrmEmployeeId) && ($_FILES["documentValues"] || $_FILES['resume']['name'] || $_FILES['offerLetter']['name'] || $_FILES['joiningLetter']['name'] || $_FILES['contract']['name'] || $_FILES['IDProof']['name'])) {
            $wphrmFormDetails = array();
            $wphrmFormDetails = $this->WPHRMGetUserDatas($wphrmEmployeeId, 'wphrmEmployeeDocumentInfo');
            if (!empty($_FILES['resume']['tmp_name'])) {
                $wphrmFormDetails['resume'] = sanitize_url($movefileResume['url']);
            } else if (isset($wphrmFormDetails['resume']) && !empty($wphrmFormDetails['resume'])) {
                $wphrmFormDetails['resume'] = $wphrmFormDetails['resume'];
            }
            if (!empty($_FILES['offerLetter']['tmp_name'])) {
                $wphrmFormDetails['offerLetter'] = sanitize_url($movefileOfferLetter['url']);
            } else if (isset($wphrmFormDetails['offerLetter']) && !empty($wphrmFormDetails['offerLetter'])) {
                $wphrmFormDetails['offerLetter'] = $wphrmFormDetails['offerLetter'];
            }
            if (!empty($_FILES['joiningLetter']['tmp_name'])) {
                $wphrmFormDetails['joiningLetter'] = sanitize_url($movefileJoiningLetter['url']);
            } else if (isset($wphrmFormDetails['joiningLetter']) && $wphrmFormDetails['joiningLetter']) {
                $wphrmFormDetails['joiningLetter'] = $wphrmFormDetails['joiningLetter'];
            }
            if (!empty($_FILES['contract']['tmp_name'])) {
                $wphrmFormDetails['contract'] = sanitize_url($movefileContract['url']);
            } else if (isset($wphrmFormDetails['contract']) && $wphrmFormDetails['contract']) {
                $wphrmFormDetails['contract'] = $wphrmFormDetails['contract'];
            }
            if (!empty($_FILES['IDProof']['tmp_name'])) {
                $wphrmFormDetails['IDProof'] = sanitize_url($movefileIDProof['url']);
            } else if (isset($wphrmFormDetails['IDProof']) && $wphrmFormDetails['IDProof']) {
                $wphrmFormDetails['IDProof'] = $wphrmFormDetails['IDProof'];
            }

            $files = $_FILES["documentValues"];
            foreach ($files['name'] as $key => $value) {
                if ($files['name'][$key]) {
                    $file = array(
                        'name' => $files['name'][$key],
                        'type' => $files['type'][$key],
                        'tmp_name' => $files['tmp_name'][$key],
                        'error' => $files['error'][$key],
                        'size' => $files['size'][$key]
                    );
                    $_FILES = array("documentValues" => $file);
                    foreach ($_FILES as $file => $array) {
                        $movefileAddDocuments[] = $this->WPHRMHandleUpload($file, $pid);
                    }
                }
            }
            if (isset($wphrmFormDetails['documentsfieldsvalue']) && $wphrmFormDetails['documentsfieldsvalue'] != '') {
                $wphrmFormDetails['documentsfieldslebal'] = $_POST['documentsfieldslebal'];
                $wphrmFormDetails['documentsfieldsvalue'] = $movefileAddDocuments;
            } else {
                $wphrmFormDetails['documentsfieldslebal'] = $_POST['documentsfieldslebal'];
                $wphrmFormDetails['documentsfieldsvalue'] = $movefileAddDocuments;
            }
            $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
            update_user_meta($wphrmEmployeeId, "wphrmEmployeeDocumentInfo", $wphrmFormDetailsData);
            $result['success'] = true;
        } else {
            $wphrmFormDetails = array();
            if ($_FILES['resume']['name'] || $_FILES['offerLetter']['name'] || $_FILES['joiningLetter']['name'] || $_FILES['contract']['name'] || $_FILES['IDProof']['name']) {
                $wphrmFormDetails['resume'] = sanitize_url($movefileResume['url']);
                $wphrmFormDetails['offerLetter'] = sanitize_url($movefileOfferLetter['url']);
                $wphrmFormDetails['joiningLetter'] = sanitize_url($movefileJoiningLetter['url']);
                $wphrmFormDetails['contract'] = sanitize_url($movefileContract['url']);
                $wphrmFormDetails['IDProof'] = sanitize_url($movefileIDProof['url']);
                $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
                $result['success'] = true;
            } else {
                $result['error'] = __("Please select file.", '');
            }
        }
        echo json_encode($result);
        exit;
    }

    /** WPHRM Get Attechment URl function. * */
    public function WPHRMGetAttechment($postID) {
        if (!empty($postID)) {
            $URL = wp_get_attachment_url($postID);
        } else {
            $URL = '';
        }
        return $URL;
    }

    /** WPHRM Get Attechment Title function. * */
    public function WPHRMGetAttechmentTitle($attach_id) {
        if (!empty($attach_id)) {
            $attachment_title = get_the_title($attach_id);
        } else {
            $attachment_title = '';
        }
        return $attachment_title;
    }

    /** Add Employee Salary Details function. * */
    public function WPHRMEmployeeSalaryInfo() {

        $wphrmEmployeeId = intval($_POST['wphrm_employee_id']);
        global $current_user;
        $wphrmCurrentUserRole = implode(',', $current_user->roles);
        $result = array();
        if (!empty($wphrmEmployeeId)) :
        $wphrmFormDetails = array();
        $wphrmFormDetails = $this->WPHRMGetUserDatas($wphrmEmployeeId, 'wphrmEmployeeSalaryInfo');
        $wphrmFormDetails['current-salary'] = sanitize_text_field($_POST['current-salary']);
        $wphrmFormDetails['SalaryFieldsLebal'] = $this->WPHRMSanitize($_POST['salary-fields-lebal']);
        $wphrmFormDetails['SalaryFieldsvalue'] = $this->WPHRMSanitize($_POST['salary-fields-value']);
        $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
        update_user_meta($wphrmEmployeeId, "wphrmEmployeeSalaryInfo", $wphrmFormDetailsData);
        $result['success'] = true;
        else :
        $wphrmFormDetails = array();
        $wphrmFormDetails['current-salary'] = sanitize_text_field($_POST['current-salary']);
        $wphrmFormDetails['SalaryFieldsLebal'] = $this->WPHRMSanitize($_POST['salary-fields-lebal']);
        $wphrmFormDetails['SalaryFieldsvalue'] = $this->WPHRMSanitize($_POST['salary-fields-value']);
        $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
        update_user_meta($wphrmEmployeeId, "wphrmEmployeeSalaryInfo", $wphrmFormDetailsData);
        $result['success'] = true;
        endif;
        echo json_encode($result);
        exit;
    }

    /*J@F*/
    /** Add Employee Family Details function. * */
    public function WPHRMEmployeeFamilyInfo() {

        $wphrmEmployeeId = intval($_POST['wphrm_employee_id']);
        global $current_user;
        $wphrmCurrentUserRole = implode(',', $current_user->roles);
        $result = array();
        if (!empty($wphrmEmployeeId)) : // for update
        $wphrmFormDetails = array();
        $wphrmFormDetails = $this->WPHRMGetUserDatas($wphrmEmployeeId, 'wphrmEmployeeFamilyInfo');

        $family_members = array();
        $family_members['family-member-names'] = ($_POST['family-member-name']);
        $family_members['family-member-relations'] = ($_POST['family-member-relations']);
        $family_members['family-member-work_related'] = ($_POST['family-member-work_related']);
        $family_members['family-member-company_name'] = ($_POST['family-member-company_name']);
        $family_members['family-member-occupation'] = ($_POST['family-member-occupation']);

        $wphrmFormDetails['family-members'] = $family_members;

        $employee_children = array();
        $employee_children['child-names'] = ($_POST['child-name']);
        $employee_children['child-nric'] = ($_POST['child-nric']);
        $employee_children['child-dob'] = ($_POST['child-dob']);
        $employee_children['child-citizenship'] = ($_POST['child-citizenship']);
        $employee_children['child-age'] = ($_POST['child-age']);
        $employee_children['child-gender'] = ($_POST['child-gender']);
        $employee_children['child-nationality'] = ($_POST['child-nationality']);

        $wphrmFormDetails['employee-children'] = $employee_children;

        $emergency = array();
        $emergency['emergency-name'] = ($_POST['emergency-name']);
        $emergency['emergency-relations'] = ($_POST['emergency-relations']);
        $emergency['emergency-number'] = ($_POST['emergency-number']);

        $wphrmFormDetails['employee-emergency-contact'] = $emergency;

        $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
        update_user_meta($wphrmEmployeeId, "wphrmEmployeeFamilyInfo", $wphrmFormDetailsData);
        $result['success'] = true;
        else :
        $wphrmFormDetails = array();

        $family_members = array();
        $family_members['family-member-names'] = ($_POST['family-member-name']);
        $family_members['family-member-relations'] = ($_POST['family-member-relations']);
        $family_members['family-member-work_related'] = ($_POST['family-member-work_related']);
        $family_members['family-member-company_name'] = ($_POST['family-member-company_name']);
        $family_members['family-member-occupation'] = ($_POST['family-member-occupation']);

        $wphrmFormDetails['family-members'] = $family_members;

        $employee_children = array();
        $employee_children['child-names'] = ($_POST['child-name']);
        $employee_children['child-nric'] = ($_POST['child-nric']);
        $employee_children['child-dob'] = ($_POST['child-dob']);
        $employee_children['child-citizenship'] = ($_POST['child-citizenship']);
        $employee_children['child-age'] = ($_POST['child-age']);
        $employee_children['child-gender'] = ($_POST['child-gender']);
        $employee_children['child-nationality'] = ($_POST['child-nationality']);

        $wphrmFormDetails['employee-children'] = $employee_children;

        $emergency = array();
        $emergency['emergency-name'] = ($_POST['emergency-name']);
        $emergency['emergency-relations'] = ($_POST['emergency-relations']);
        $emergency['emergency-number'] = ($_POST['emergency-number']);

        $wphrmFormDetails['employee-emergency-contact'] = $emergency;

        $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
        update_user_meta($wphrmEmployeeId, "wphrmEmployeeFamilyInfo", $wphrmFormDetailsData);
        $result['success'] = true;
        endif;
        $result['post_data'] = $_POST;
        echo json_encode($result);
        exit;
    }
    /*J@F END*/

    /** Add Employee bank Details function. * */
    public function WPHRMEmployeeBankInfo() {
        $wphrmEmployeeId = intval($_POST['wphrm_employee_id']);
        $result = array();
        if (!empty($wphrmEmployeeId)) :
        $wphrmFormDetails = array();
        $wphrmFormDetails = $this->WPHRMGetUserDatas($wphrmEmployeeId, 'wphrmEmployeeBankInfo');
        $wphrmFormDetails['wphrmbankfieldslebal'] = $this->WPHRMSanitize($_POST['bank-fields-lebal']);
        $wphrmFormDetails['wphrmbankfieldsvalue'] = $this->WPHRMSanitize($_POST['bank-fields-value']);
        $wphrmFormDetails['wphrm_employee_bank_account_name'] = sanitize_text_field($_POST['wphrm_employee_bank_account_name']);
        $wphrmFormDetails['wphrm_employee_bank_account_no'] = sanitize_text_field($_POST['wphrm_employee_bank_account_no']);
        $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
        update_user_meta($wphrmEmployeeId, "wphrmEmployeeBankInfo", $wphrmFormDetailsData);
        $result['success'] = true;
        else :
        $wphrmFormDetails = array();
        $wphrmFormDetails = $this->WPHRMGetUserDatas($wphrmEmployeeId, 'wphrmEmployeeBankInfo');
        $wphrmFormDetails['wphrmbankfieldslebal'] = $this->WPHRMSanitize($_POST['bank-fields-lebal']);
        $wphrmFormDetails['wphrmbankfieldsvalue'] = $this->WPHRMSanitize($_POST['bank-fields-value']);
        $wphrmFormDetails['wphrm_employee_bank_account_name'] = sanitize_text_field($_POST['wphrm_employee_bank_account_name']);
        $wphrmFormDetails['wphrm_employee_bank_account_no'] = sanitize_text_field($_POST['wphrm_employee_bank_account_no']);
        $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
        update_user_meta($wphrmEmployeeId, "wphrmEmployeeBankInfo", $wphrmFormDetailsData);
        $result['success'] = true;
        endif;
        echo json_encode($result);
        exit;
    }

    /** Add Employee other Details function. * */
    public function WPHRMEmployeeOtherInfo() {
        $wphrmEmployeeId = intval($_POST['wphrm_employee_id']);
        if (isset($_POST['wphrm_vehicle_type']) && $_POST['wphrm_vehicle_type'] != '') :
        $wphrmVehicleType = $_POST['wphrm_vehicle_type'];
        else:
        $wphrmVehicleType = '';
        endif;
        if (isset($_POST['wphrm_employee_vehicle']) && $_POST['wphrm_employee_vehicle'] != '') :
        $wphrmEmployeeVehicle = $_POST['wphrm_employee_vehicle'];
        else:
        $wphrmEmployeeVehicle = '';
        endif;
        $wphrmFormDetails['wphrm_vehicle_type'] = '';
        $wphrmFormDetails['wphrm_employee_vehicle'] = '';
        global $current_user;
        $wphrmCurrentUserRole = implode(',', $current_user->roles);
        $result = array();
        if (!empty($wphrmEmployeeId)) :
        $wphrmFormDetails = array();
        $wphrmFormDetails = $this->WPHRMGetUserDatas($wphrmEmployeeId, 'wphrmEmployeeOtherInfo');
        $wphrmFormDetails['wphrmotherfieldslebal'] = $this->WPHRMSanitize($_POST['other-fields-lebal']);
        $wphrmFormDetails['wphrmotherfieldsvalue'] = $this->WPHRMSanitize($_POST['other-fields-value']);
        $wphrmFormDetails['wphrm_employee_vehicle'] = sanitize_text_field($wphrmEmployeeVehicle);
        $wphrmFormDetails['wphrm_vehicle_type'] = sanitize_text_field($wphrmVehicleType);
        $wphrmFormDetails['wphrm_employee_vehicle_expiration'] = sanitize_text_field($_POST['wphrm_employee_vehicle_expiration']);
        $wphrmFormDetails['wphrm_employee_vehicle_model'] = sanitize_text_field($_POST['wphrm_employee_vehicle_model']);
        $wphrmFormDetails['wphrm_employee_vehicle_registrationno'] = sanitize_text_field($_POST['wphrm_employee_vehicle_registrationno']);
        $wphrmFormDetails['wphrm_t_shirt_size_male'] = sanitize_text_field($_POST['wphrm_t_shirt_size_male']);
        $wphrmFormDetails['wphrm_t_shirt_size_female'] = sanitize_text_field($_POST['wphrm_t_shirt_size_female']);
        $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
        update_user_meta($wphrmEmployeeId, "wphrmEmployeeOtherInfo", $wphrmFormDetailsData);
        $result['success'] = true;
        else :
        $wphrmFormDetails = array();
        $wphrmFormDetails = $this->WPHRMGetUserDatas($wphrmEmployeeId, 'wphrmEmployeeBankInfo');
        $wphrmFormDetails['wphrmotherfieldslebal'] = $this->WPHRMSanitize($_POST['other-fields-lebal']);
        $wphrmFormDetails['wphrmotherfieldsvalue'] = $this->WPHRMSanitize($_POST['other-fields-value']);
        $wphrmFormDetails['wphrm_employee_vehicle'] = sanitize_text_field($wphrmEmployeeVehicle);
        $wphrmFormDetails['wphrm_vehicle_type'] = sanitize_text_field($wphrmVehicleType);
        $wphrmFormDetails['wphrm_employee_vehicle_expiration'] = sanitize_text_field($_POST['wphrm_employee_vehicle_expiration']);
        $wphrmFormDetails['wphrm_employee_vehicle_model'] = sanitize_text_field($_POST['wphrm_employee_vehicle_model']);
        $wphrmFormDetails['wphrm_employee_vehicle_registrationno'] = sanitize_text_field($_POST['wphrm_employee_vehicle_registrationno']);
        $wphrmFormDetails['wphrm_t_shirt_s_maleize'] = sanitize_text_field($_POST['wphrm_t_shirt_s_maleize']);
        $wphrmFormDetails['wphrm_t_shirt_size_female'] = sanitize_text_field($_POST['wphrm_t_shirt_size_female']);
        $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
        $result['success'] = true;
        endif;
        echo json_encode($result);
        exit;
    }

    /** Add Employee Department function. * */
    public function WPHRMDepartmentInfo() {
        global $current_user, $wpdb;
        $wphrmDepartmentId = '';
        if (isset($_POST['wphrm_department_id']) && $_POST['wphrm_department_id'] != '') :
        $wphrmDepartmentId = esc_sql($_POST['wphrm_department_id']);  // esc
        endif;
        $wphrmCurrentUserRole = implode(',', $current_user->roles);
        $result = array();
        if (!empty($wphrmDepartmentId)) :
        $wphrmFormDetails = array();
        $wphrm_department = $wpdb->get_row("SELECT * FROM $this->WphrmDepartmentTable WHERE `departmentID` = $wphrmDepartmentId");
        $wphrmFormDetails = unserialize(base64_decode($wphrm_department->departmentName));
        $wphrmFormDetails['departmentName'] = sanitize_text_field($_POST['editdepartment_name']);
        $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
        $id = $wpdb->query("UPDATE $this->WphrmDepartmentTable SET `departmentName`='$wphrmFormDetailsData' WHERE `departmentID`= $wphrmDepartmentId");
        $result['success'] = true;
        else :
        $wphrmFormDetails = array();
        if ($_POST['departmentName'] != '') {
            foreach ($_POST['departmentName'] as $wphrmDepartmentName) {
                if (!empty($wphrmDepartmentName)) {
                    $wphrmFormDetails['departmentName'] = sanitize_text_field($wphrmDepartmentName);
                    $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
                    $wpdb->query("INSERT INTO $this->WphrmDepartmentTable (`departmentName`) VALUES('$wphrmFormDetailsData')");
                }
            }
        }
        $result['success'] = true;

        endif;
        echo json_encode($result);
        exit;
    }

    /** Add Employee Department function. * */
    public function WPHRMDesignationInfo() {
        global $current_user, $wpdb;
        $wphrmDesignationId = '';
        if (isset($_POST['wphrm_designation_id']) && $_POST['wphrm_designation_id'] != '') :
        $wphrmDesignationId = esc_sql($_POST['wphrm_designation_id']);  // esc
        endif;
        $wphrmCurrentUserRole = implode(',', $current_user->roles);
        $result = array();
        if (!empty($wphrmDesignationId)) :
            $wphrmFormDetails = array();
            $wphrm_designation = $wpdb->get_row("SELECT * FROM $this->WphrmDesignationTable WHERE `designationID` = $wphrmDesignationId");
            $wphrmFormDetails = unserialize(base64_decode($wphrm_designation->designationName));
            $wphrmFormDetails['designationName'] = sanitize_text_field($_POST['editdesignation']);
            $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
            $id = $wpdb->query("UPDATE $this->WphrmDesignationTable SET  `designationName`='$wphrmFormDetailsData'  WHERE `designationID`= $wphrmDesignationId");
            $result['success'] = true;
        else :
            $wphrmFormDetails = array();
            if ($_POST['designation_name'] != '') {
                foreach ($_POST['designation_name'] as $wphrmDesignation) {
                    if (!empty($wphrmDesignation)) {
                        $wphrmFormDetails['designationName'] = sanitize_text_field($wphrmDesignation);
                        $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
                        $wpdb->query("INSERT INTO $this->WphrmDesignationTable (`designationName`, `departmentID`) VALUES('$wphrmFormDetailsData','" . $_POST['departmentID'] . "')");
                    }
                }
            }
            $result['success'] = true;
        endif;
        echo json_encode($result);
        exit;
    }

    /**
    Leave type function.
    J@F Modified 2017-05-08
    * */
    function WPHRMLeaveEntitlement() {
        global $wpdb;
        $result = array();
        $wphrmLeaveEntitlementId = '';
        /*$years_of_service = 0;
        $staff = 0;
        $supervisor = 0;
        $manager = 0;
        $senior_manager = 0;*/
        $years_of_service = sanitize_text_field($_POST['wphrm_yos']);
        $staff = sanitize_text_field($_POST['wphrm_staff']);
        $supervisor = sanitize_text_field($_POST['wphrm_supervisor']);
        $manager = sanitize_text_field($_POST['wphrm_manager']);
        $senior_manager = sanitize_text_field($_POST['wphrm_senior_manager']);
        
        if (isset($_POST['wphrm_leaveentitlement_id']) && $_POST['wphrm_leaveentitlement_id'] != '') :
            $wphrmLeaveEntitlementId = esc_sql($_POST['wphrm_leaveentitlement_id']);  // esc
        endif;
        
        if (!empty($wphrmLeaveEntitlementId)){
            $res = $wpdb->update(
                $this->WphrmEmployeeLevelTable,
                array(
                    'years_of_service' => $years_of_service,
                    'staff' => $staff,
                    'supervisor' => $supervisor,
                    'manager' => $manager,
                    'senior_manager' => $senior_manager,
                ),
                array( 'id' => $wphrmLeaveEntitlementId )
            );

            $result['success'] = $res ? true : false;
        }else{
            $res = $wpdb->insert(
                $this->WphrmEmployeeLevelTable,
                array(
                    'years_of_service' => $years_of_service,
                    'staff' => $staff,
                    'supervisor' => $supervisor,
                    'manager' => $manager,
                    'senior_manager' => $senior_manager,
                )
            );
            $result['success'] = $res ? true : false;
        }
        echo json_encode($result);
        exit;
    }
    public function WPHRMLeavetypeInfo() {
        global $current_user, $wpdb;
        $wphrmLeaveTypeId = '';
        $leaveType = '';
        $numOfLeave = '';
        $leaveType = sanitize_text_field($_POST['leaveType']);
        $wphrmPeriod = sanitize_text_field($_POST['wphrm_period']);
        $numOfLeave = sanitize_text_field($_POST['numberOfLeave']);
        /*J@F*/
        $require_file_attachment = filter_var($_POST['require_file_attachment'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        $notice_period = empty($_POST['notice_period']) ? 0 : sanitize_text_field($_POST['notice_period']);
        $leave_rules = sanitize_text_field($_POST['leave_rules']);
        $leave_description = sanitize_text_field($_POST['leave_description']);
        /*J@F END*/
        if (isset($_POST['wphrm_leavetype_id']) && $_POST['wphrm_leavetype_id'] != '') :
        $wphrmLeaveTypeId = esc_sql($_POST['wphrm_leavetype_id']);  // esc
        endif;
        $wphrmCurrentUserRole = implode(',', $current_user->roles);
        $result = array();
        if (!empty($wphrmLeaveTypeId)){
            $wphrmFormDetails = array();
            $wphrm_leavetype = $wpdb->get_row("SELECT * FROM $this->WphrmLeaveTypeTable WHERE `id` = $wphrmLeaveTypeId");

            $res = $wpdb->update(
                $this->WphrmLeaveTypeTable,
                array(
                    'leaveType' => $leaveType,
                    'period' => $wphrmPeriod,
                    'numberOfLeave' => $numOfLeave,
                    'require_file_attachment' => $require_file_attachment,
                    'notice_period' => $notice_period,
                    'leave_rules' => $leave_rules,
                    'leave_description' => $leave_description,
                ),
                array( 'id' => $wphrmLeaveTypeId )
            );

            $result['success'] = $res ? true : false;
        }else{
            $wphrmFormDetails = array();
            $res = $wpdb->insert(
                $this->WphrmLeaveTypeTable,
                array(
                    'leaveType' => $leaveType,
                    'period' => $wphrmPeriod,
                    'numberOfLeave' => $numOfLeave,
                    'require_file_attachment' => $require_file_attachment,
                    'notice_period' => $notice_period,
                    'leave_rules' => $leave_rules,
                    'leave_description' => $leave_description,
                )
            );

            $result['success'] = $res ? true : false;

            do_action('after_leave_type_insert', $wpdb->insert_id );
        }
        echo json_encode($result);
        exit;
    }
    /*J@F*/
    public function WPHRMGetLeaveTypeInfo() {
        global $current_user, $wpdb;
        $response = array( 'status' => false);
        if(!empty($_REQUEST['leave_id'])){
            $wphrmLeaveTypeId =($_REQUEST['leave_id']);
            $wphrm_leavetype = $wpdb->get_row("SELECT * FROM $this->WphrmLeaveTypeTable WHERE `id` = '$wphrmLeaveTypeId'", ARRAY_A);
            $response['status'] = $wphrm_leavetype ? true : false;
            $response['leave'] = $wphrm_leavetype;

            $limit = $wphrm_leavetype['numberOfLeave'];
            if(in_array($wphrm_leavetype['leave_rules'], array_keys($this->get_employee_types()))){
                $limit = $this->get_employee_max_leave( $current_user->ID, $wphrm_leavetype['leave_rules'] );
            }
            
            $rule = $wphrm_leavetype['leave_rules'];
            $wphrm_leaverule = $wpdb->get_row( "SELECT * FROM $this->WphrmLeaveRulesTable WHERE `id` = '$rule'", ARRAY_A );
            
            $employee_type = null;
            $basic_info = $this->WPHRMGetUserDatas( $current_user->ID, 'wphrmEmployeeInfo' );
            if(isset($basic_info['wphrm_employee_level'])){
                $employee_type = $this->get_employee_type( $basic_info['wphrm_employee_level'] );
            }

            $leave_count = $this->get_user_leave_count($current_user->ID, $wphrmLeaveTypeId);
            $response['employee_limit'] = $limit;
            $response['employee_leave_used'] = $leave_count;
            $response['employee_type'] = $employee_type;
            $response['employee_claims'] = $this->get_medical_claim($current_user->ID);
            $response['maternity'] = $wphrm_leaverule['maternity'];
            $response['paternity'] = $wphrm_leaverule['paternity'];
            $response['lieu'] = $wphrm_leaverule['off_in_lieu'];
            $response['outstation'] = $wphrm_leaverule['outstation'];
            $response['examination'] = $wphrm_leaverule['examination'];
            echo json_encode($response);
        }else{
            echo json_encode($response);
        }
        exit;
    }
    /*J@F End*/

    /*     * Salary Request function. * */
    public function WPHRMSalaryRequest() {
        global $current_user, $wpdb;
        $wphrmemployeeId = '';
        $wphrmMonth = '';
        $wphrmYear = '';
        $result = array();
        if (isset($_POST['employee_id']) && $_POST['employee_id'] != '') :
        $wphrmemployeeId = sanitize_text_field($_POST['employee_id']);
        endif;
        if (isset($_POST['month']) && $_POST['month'] != '') :
        $wphrmMonth = sanitize_text_field($_POST['month']);
        endif;
        if (isset($_POST['year']) && $_POST['year'] != '') :
        $wphrmYear = sanitize_text_field($_POST['year']);
        endif;
        $wphrmCurrentUserRole = implode(',', $current_user->roles);
        $notification = '';
        $wphrmEmployeeInfo = $this->WPHRMGetUserDatas($wphrmemployeeId, 'wphrmEmployeeInfo');
        if (!empty($wphrmemployeeId)) :
        $requestdate = '01-' . $wphrmMonth . '-' . $wphrmYear;
        $requestdate = sanitize_text_field(date('F Y', strtotime($requestdate)));

        foreach($this->wphrmGetAdminId() as $hr_admin_id){
            $notification = array(
                'wphrmUserID' => sanitize_text_field($hr_admin_id),
                'wphrmFromId' => sanitize_text_field($wphrmemployeeId),
                'wphrmDesc' => sanitize_text_field($wphrmEmployeeInfo['wphrm_employee_fname']) . ' ' . sanitize_text_field($wphrmEmployeeInfo['wphrm_employee_lname']) . ' has requested a salary slip for the month of ' . $requestdate . '.',
                'notificationType' => sanitize_text_field('Salary Slip request'),
                'wphrmStatus' => sanitize_text_field('unseen'),
                'wphrmDate' => sanitize_text_field(date('Y-m-d')),
            );

            $wpdb->insert($this->WphrmNotificationsTable, $notification);
        }
        $result['success'] = __('Your salary slip generation request has been sent.', 'wphrm');
        endif;
        echo json_encode($result);
        exit;
    }
    /*     * Salary Request function. * */

    public function WPHRMSalaryWeekRequest() {
        global $current_user, $wpdb;
        $wphrmemployeeId = '';
        $wphrmMonth = '';
        $wphrmYear = '';
        $wphrmWeekNo = '';
        $result = array();
        if (isset($_POST['employee_id']) && $_POST['employee_id'] != '') :
        $wphrmemployeeId = sanitize_text_field($_POST['employee_id']);
        endif;
        if (isset($_POST['month']) && $_POST['month'] != '') :
        $wphrmMonth = sanitize_text_field($_POST['month']);
        endif;
        if (isset($_POST['year']) && $_POST['year'] != '') :
        $wphrmYear = sanitize_text_field($_POST['year']);
        endif;
        if (isset($_POST['weekNo']) && $_POST['weekNo'] != '') :
        $wphrmWeekNo = sanitize_text_field($_POST['weekNo']);
        endif;
        $wphrmCurrentUserRole = implode(',', $current_user->roles);
        $notification = '';
        $wphrmEmployeeInfo = $this->WPHRMGetUserDatas($wphrmemployeeId, 'wphrmEmployeeInfo');
        if (!empty($wphrmemployeeId)) {
            foreach($this->wphrmGetAdminId() as $hr_admin_id){
                $requestdate = '01-' . $wphrmMonth . '-' . $wphrmYear;
                $requestdate = sanitize_text_field(date('F Y', strtotime($requestdate)));
                $currentCount = $this->WPHRMWeekCount($wphrmWeekNo);
                $notification = array(
                    'wphrmUserID' => sanitize_text_field($hr_admin_id),
                    'wphrmFromId' => sanitize_text_field($wphrmemployeeId),
                    'wphrmDesc' => sanitize_text_field($wphrmEmployeeInfo['wphrm_employee_fname']) . ' ' . sanitize_text_field($wphrmEmployeeInfo['wphrm_employee_lname']) . ' has requested a salary slip for the '.$currentCount.'of month  ' . $requestdate . '.',
                    'notificationType' => sanitize_text_field('Salary Slip request'),
                    'wphrmStatus' => sanitize_text_field('unseen'),
                    'wphrmDate' => sanitize_text_field(date('Y-m-d')),
                );

                $wpdb->insert($this->WphrmNotificationsTable, $notification);
                $result['success'] = __('Your salary slip generation request has been sent.', 'wphrm');
            }
        }
        echo json_encode($result);
        exit;
    }

    /** Add Employee Delete Department function. * */
    public function WPHRMCustomDelete() {
        global $current_user, $wpdb;
        $WPHRMCustomDelete_id = esc_sql($_POST['WPHRMCustomDelete_id']);  // esc
        $wphrmTableName = $_POST['table_name'];
        $filedName = $_POST['filed_name'];
        $wphrmCurrentUserRole = implode(',', $current_user->roles);
        $result = array();
        if (!empty($WPHRMCustomDelete_id)) :
        $id = $wpdb->query("DELETE FROM `$wphrmTableName` WHERE `$filedName` = $WPHRMCustomDelete_id");
        $result['success'] = true;
        else :
        $result['error'] = false;
        endif;
        echo json_encode($result);
        exit;
    }

    /** Add Employee Delete designation ajax function. * */
    public function WPHRMDesignationAjax() {
        global $current_user, $wpdb;
        $list = '';
        $result = array();
        if (isset($_POST['id'])) {
            $wphrmDepartmentId = esc_sql($_POST['id']);  // esc
        }
        $wphrmCurrentUserRole = implode(',', $current_user->roles);
        if (!empty($wphrmDepartmentId)) :
        $wphrm_designation = $wpdb->get_results("SELECT * FROM $this->WphrmDesignationTable WHERE `departmentID` = $wphrmDepartmentId");
        foreach ($wphrm_designation as $wphrm_designation_result):
        $wphrmFormDetails1 = unserialize(base64_decode($wphrm_designation_result->designationName));
        $somthing['name'] = $wphrmFormDetails1['designationName'];
        $somthing['id'] = $wphrm_designation_result->designationID;
        $list[] = $somthing;
        endforeach;
        endif;
        $response['success'] = '1';
        $response['message'] = __('Designation Successfully Done', 'wphrm');
        $response['details'] = $list;
        echo json_encode($response);
        exit;
    }

    /** Add year wisw month get function. * */
    public function WPHRMSalaryMonthAjax() {
        global $wpdb;
        $monthList = array();
        $response = array();
        if (isset($_POST['year']) && isset($_POST['employeeID'])) {
            $wphrmYear = esc_sql($_POST['year']); // esc
            $employeeOtherId = esc_sql($_POST['employeeID']); // esc
            $wphrmGetDuplicate = $wpdb->get_results("SELECT * FROM $this->WphrmSalaryTable WHERE `employeeID` = $employeeOtherId AND year(`date`)='$wphrmYear' GROUP BY `date`");

            if (!empty($wphrmGetDuplicate)) :
            foreach ($wphrmGetDuplicate as $wphrmGetDuplicates):
            $monthList[]['wphrmMonths'] = date('F', strtotime($wphrmGetDuplicates->date));
            endforeach;
            endif;
            $response['success'] = '1';
            $response['message'] = __('Designation Successfully Done', 'wphrm');
            $response['details'] = $monthList;
        } else {
            $response['error'] = true;
        }
        echo json_encode($response);
        exit;
    }

    /** Add year wisw month get function. * */
    public function WPHRMSalaryWeekMonthAjax() {
        global $wpdb;
        $monthList = array();
        $response = array();
        if (isset($_POST['year']) && isset($_POST['employeeID'])) {
            $wphrmYear = esc_sql($_POST['year']); // esc
            $employeeOtherId = esc_sql($_POST['employeeID']); // esc
            $wphrmGetDuplicate = $wpdb->get_results("SELECT * FROM $this->WphrmWeeklySalaryTable WHERE `employeeID` = $employeeOtherId AND year(`date`)='$wphrmYear' GROUP BY `date`");

            if (!empty($wphrmGetDuplicate)) :
            foreach ($wphrmGetDuplicate as $wphrmGetDuplicates):
            $monthList[]['wphrmMonths'] = date('F', strtotime($wphrmGetDuplicates->date));
            endforeach;
            endif;
            $response['success'] = '1';
            $response['message'] = __('Designation Successfully Done', 'wphrm');
            $response['details'] = $monthList;
        } else {
            $response['error'] = true;
        }
        echo json_encode($response);
        exit;
    }

    /** Add year wisw month get function. * */
    public function WPHRMSalaryWeekMonthGetAjax() {
        global $wpdb;
        $monthList = array();
        $response = array();
        if (isset($_POST['year']) && isset($_POST['employeeID'])) {
            $wphrmYear = esc_sql($_POST['year']); // esc
            $wphrmMonth = esc_sql(date('m',strtotime($_POST['month']))); // esc
            $employeeOtherId = esc_sql($_POST['employeeID']); // esc
            $wphrmGetDuplicate = $wpdb->get_results("SELECT * FROM $this->WphrmWeeklySalaryTable WHERE `employeeID` = $employeeOtherId AND year(`date`)='$wphrmYear' AND month(`date`)='$wphrmMonth' GROUP BY `weekOn`");

            if (!empty($wphrmGetDuplicate)) :
            foreach ($wphrmGetDuplicate as $wphrmGetDuplicates):
            $monthList[]['wphrmMonths'] = $wphrmGetDuplicates->weekOn;
            endforeach;
            endif;
            $response['success'] = '1';
            $response['message'] = __('Designation Successfully Done', 'wphrm');
            $response['details'] = $monthList;
        } else {
            $response['error'] = true;
        }
        echo json_encode($response);
        exit;
    }

    public function WPHRMWeeksInMonth($year, $month, $start_day_of_week) {
        // Total number of days in the given month.
        $num_of_days = date("t", mktime(0, 0, 0, $month, 1, $year));
        // Count the number of times it hits $start_day_of_week.
        $num_of_weeks = 0;
        for ($i = 1; $i <= $num_of_days; $i++) {
            $day_of_week = date('w', mktime(0, 0, 0, $month, $i, $year));
            if ($day_of_week == $start_day_of_week)
                $num_of_weeks++;
        }

        return $num_of_weeks;
    }

    public function WPHRMWeekCount($count) {
        $weekName='';
        if($count == 1){
            $weekName = '1st Week ';
        }else if($count == 2){
            $weekName = '2nd Week ';
        }else if($count == 3){
            $weekName = '3rd Week ';
        }else if($count == 4){
            $weekName = '4th Week ';
        }else if($count == 5){
            $weekName = '5th Week ';
        }
        return $weekName;
    }

    /** Add multiple Weekends function. * */
    public function WPHRMwphrmAddyearInWeekendInfo() {
        global $current_user, $wpdb;
        $wphrmYear = '';
        if (isset($_POST['wphrmyear']) && $_POST['wphrmyear'] != '') {
            $wphrmYear = $_POST['wphrmyear'];
        }
        $wphrmWeekend = '';
        if (isset($_POST['wphrmWeekend']) && $_POST['wphrmWeekend'] != '') {
            $wphrmWeekend = $_POST['wphrmWeekend'];
            if ($wphrmWeekend == 'Sunday') {
                $wphrmWeekCounter = 0;
            } else if ($wphrmWeekend == 'Saturday') {
                $wphrmWeekCounter = 6;
            } else if ($wphrmWeekend == 'Friday') {
                $wphrmWeekCounter = 5;
            } else if ($wphrmWeekend == 'Thursday') {
                $wphrmWeekCounter = 4;
            } else if ($wphrmWeekend == 'Wednesday') {
                $wphrmWeekCounter = 3;
            } else if ($wphrmWeekend == 'Tuesday') {
                $wphrmWeekCounter = 2;
            } else if ($wphrmWeekend == 'Monday') {
                $wphrmWeekCounter = 1;
            }
        }

        $wphrmWeekend = esc_sql($wphrmWeekend);
        $wphrmTypeWeekend = '';
        if (isset($_POST['wphrmTypeWeekend']) && $_POST['wphrmTypeWeekend'] != '') {
            $wphrmTypeWeekend = $_POST['wphrmTypeWeekend'];
        }
        $wphrmType = '';
        if (isset($_POST['wphrmType']) && $_POST['wphrmType'] != '') {
            $wphrmType = $_POST['wphrmType'];
        }
        $monthS = $this->WPHRMGetMonths();
        $result = array();
        $holiday_dates = esc_sql(date('Y-m-d H:i:s'));
        foreach ($monthS as $monthSkey => $monthSs) {
            if ($monthSkey <= 12) {
                $days = $this->WPHRMDateForSpecificTwoDayOff($wphrmYear, $monthSkey, $wphrmWeekCounter, 7);
                foreach ($days as $daykey => $dayData) {
                    foreach ($wphrmType as $wphrmTypes) {
                        if ($daykey == $wphrmTypes) {
                            $holidayinserts = esc_sql($dayData); // esc
                            $id = $wpdb->query("SELECT * FROM $this->WphrmHolidaysTable WHERE `wphrmDate` = '" . $holidayinserts . "'");
                            if (!empty($id)) {

                            } else {
                                $wpdb->query("INSERT INTO $this->WphrmHolidaysTable (`wphrmDate`,`wphrmOccassion`,`createdAt`,`updatedAt`) VALUES('" . sanitize_text_field($holidayinserts) . "','" . sanitize_text_field($wphrmWeekend) . "','" . sanitize_text_field($holiday_dates) . "' ,'" . sanitize_text_field($holiday_dates) . "')");
                            }
                        }
                    }
                }
            }
        }
        $result['success'] = __('Weekends have been successfully added.', 'wphrm');
        echo json_encode($result);
        exit;
    }
    /** holidays function. * */
    public function WPHRMAddHolidays() {
        global $current_user, $wpdb;
        $wphrmCurrentUserRole = implode(',', $current_user->roles);
        $holidayDate = $_POST['holiday_date'];
        $occasion = $_POST['occasion'];
        $result = array();
        $holiday_dates = date('Y-m-d H:i:s');
        foreach ($holidayDate as $holi => $holidayinsert){
            foreach ($occasion as $occ => $occasioninsert){
                if ($holi == $occ) {
                    $holidayinserts = date('Y-m-d', strtotime($holidayinsert)); // esc
                    $holiday = $wpdb->get_row("SELECT * FROM $this->WphrmHolidaysTable WHERE `wphrmDate` = '" . $holidayinserts . "'");
                    if (!empty($holiday)) {
                        $result['error'] = __('Holiday already exists.', 'wphrm');
                        $wpdb->update(
                            $this->WphrmHolidaysTable,
                            array(
                                'wphrmOccassion' => $occasioninsert,
                                'type' => 'holiday',
                            ),
                            array( 'id' => $holiday->id)
                        );
                    } else {
                        if (!empty($occasioninsert)) {
                            $wpdb->insert(
                                $this->WphrmHolidaysTable,
                                array(
                                    'wphrmDate' => $holidayinserts,
                                    'wphrmOccassion' => $occasioninsert,
                                    'type' => 'holiday',
                                    'createdAt' => $holiday_dates,
                                    'updatedAt' => $holiday_dates,
                                )
                            );
                        }
                    }
                }
            }
        }

        $result['success'] = true;
        echo json_encode($result);
        exit;
    }
    /** attendance celender view. * */
    public function WPHRMEmployeeAttendanceData() {
        global $wpdb;
        $employee_id = '';
        $flag = true;
        $present = array();
        if (isset($_POST['employee_id']) && !empty($_POST['employee_id'])) {
            $employee_id = $_POST['employee_id']; // esc
        }
        $employeeHolidayResult = $employeeAttendanceResult = array();
        $holiday_dates = esc_sql(date('Y-m-d')); // esc
        if (isset($employee_id) && !empty($employee_id)) {
            $employee_holidays = $wpdb->get_results("select * from $this->WphrmHolidaysTable");
            $employee_attendance = $wpdb->get_results("select * from $this->WphrmAttendanceTable where `employeeID` = $employee_id and (`date` <= '$holiday_dates' or leaveApplicationId <> 0) ");

            foreach ($employee_attendance as $employee_attendancekey => $employee_attendances) {

                $data[] = $employee_attendances->halfDayType;

                //Check if this day leave
                $leave_status = '';
                if($employee_attendances->leaveApplicationId){
                    $leave_status = $this->get_leave_application_info($employee_attendances->leaveApplicationId, 'applicationStatus');
                }

                $textColor = '#FFF';
                if ($employee_attendances->status == 'absent') {
                    $title = $employee_attendances->status;
                    $background = 'rgb(198, 61, 15)';
                }
                if ($employee_attendances->status == 'absent' && $leave_status == 'approved') {
                    $title = 'On Leave';
                    $background = 'yellow';
                    $textColor = '#000';
                }
                if ($employee_attendances->halfDayType == 'halfday') {
                    $title = 'Half day ';
                    $background = 'yellow';
                    $textColor = '#000';
                }
                if ($employee_attendances->halfDayType == 'halfday' && $leave_status == 'approved') {
                    $title = 'Half day (Leave)';
                    $background = 'yellow';
                    $textColor = '#000';
                }
                if ($employee_attendances->status == 'present') {
                    $title = $employee_attendances->status;
                    $background = 'rgb(31, 137, 127)';
                }
                $employeeAttendanceResult[] = array(
                    'title' => '' . $title . '',
                    'start' => '' . $employee_attendances->date . '',
                    'backgroundColor' => '' . $background . '',
                    'textColor' => '' . $textColor . ''
                );
            }

            foreach ($employee_holidays as $employee_holidaykey => $employee_holiday) {
                $employeeHolidayResult[] = array('title' => '' . $employee_holiday->wphrmOccassion . '',
                                                 'start' => '' . $employee_holiday->wphrmDate . '',
                                                 'backgroundColor' => 'grey');
            }
        } else {
            $employee_holidays = $wpdb->get_results("select * from $this->WphrmHolidaysTable");
            $employee_attendance = $wpdb->get_results("select `status` ,`date` from $this->WphrmAttendanceTable where  `date` <= '$holiday_dates' group by date");

            foreach ($employee_holidays as $employee_holidaykey => $employee_holiday) {
                $employeeHolidayResult[] = array('title' => '' . $employee_holiday->wphrmOccassion . '',
                                                 'start' => '' . $employee_holiday->wphrmDate . '',
                                                 'backgroundColor' => 'grey');
            }

            $i = 0;
            foreach ($employee_attendance as $employee_attendances) {
                $present = '';
                $employee_attendanceCount = $wpdb->get_results("select `status`,'halfDayType',`date` from $this->WphrmAttendanceTable where  `date` = '$employee_attendances->date'");
                foreach ($employee_attendanceCount as $employee_attendanceCounts) {
                    $presents[] = $employee_attendanceCounts->status;
                    if ($employee_attendanceCounts->halfDayType != '')
                        $presenttype[] = $employee_attendanceCounts->halfDayType;
                }
                $present = array_merge($presents, $presenttype);
                $employee_attendanceAbsent = $wpdb->get_results("select count(id) as mydata  from $this->WphrmAttendanceTable where  `date` = '$employee_attendances->date' AND status ='absent'");
                $employee_attendancePresent = $wpdb->get_results("select count(id) as mydata1  from $this->WphrmAttendanceTable where  `date` = '$employee_attendances->date' AND status ='present'");
                $employee_attendanceHalf = $wpdb->get_results("select count(id) as mydatahalf  from $this->WphrmAttendanceTable where  `date` = '$employee_attendances->date' AND halfDayType ='halfday'");


                if (in_array('present', $present)) {
                    $employeeAttendanceResult[] = array('title' => 'Present - ' . $employee_attendancePresent[0]->mydata1,
                                                        'start' => '' . $employee_attendances->date . '',
                                                        'backgroundColor' => '#128807',
                                                        'url' => '?page=wphrm-employee-absent&condition=present&date=' . $employee_attendances->date
                                                       );
                } else {
                    $employeeAttendanceResult[] = array('title' => 'Present - 0',
                                                        'start' => '' . $employee_attendances->date . '',
                                                        'backgroundColor' => '#128807',
                                                       );
                }
                if (in_array('absent', $present)) {
                    $employeeAttendanceResult[] = array('title' => 'Absents - ' . $employee_attendanceAbsent[0]->mydata,
                                                        'start' => '' . $employee_attendances->date . '',
                                                        'backgroundColor' => '#ff9933',
                                                        'textColor' => '#000',
                                                        'url' => '?page=wphrm-employee-absent&condition=absent&date=' . $employee_attendances->date);
                } else {
                    $employeeAttendanceResult[] = array('title' => 'Absents - 0',
                                                        'start' => '' . $employee_attendances->date . '',
                                                        'backgroundColor' => '#ff9933',
                                                        'textColor' => '#000');
                }

                if (in_array('halfDayType', $present)) {
                    $employeeAttendanceResult[] = array('title' => 'Halfday - ' . $employee_attendanceHalf[0]->mydatahalf,
                                                        'start' => '' . $employee_attendances->date . '',
                                                        'backgroundColor' => '#fff',
                                                        'textColor' => '#000',
                                                        'url' => '?page=wphrm-employee-absent&condition=halfday&date=' . $employee_attendances->date);
                } else {
                    $employeeAttendanceResult[] = array('title' => 'Halfday - 0',
                                                        'start' => '' . $employee_attendances->date . '',
                                                        'backgroundColor' => '#fff',
                                                        'textColor' => '#000',
                                                       );
                }
            }
        }
        $my_attendance = array_merge($employeeAttendanceResult, $employeeHolidayResult);
        echo json_encode($my_attendance);
        exit;
    }

    /*J@F */
    /*Notice Calendar data*/
    function WPHRMCompanyNoticeData(){
        global $wpdb;
        $noticeData = array();

        $wphrmNotices = $wpdb->get_results("SELECT * FROM  $this->WphrmNoticeTable WHERE wphrmdepartment = '0' ORDER BY id DESC");

        $wphrmNotices = apply_filters('wphrm_company_notice_list', $wphrmNotices);
        if(!empty($wphrmNotices)){
            foreach($wphrmNotices as $wphrmNotice){
                $datetime = explode(' ', $wphrmNotice->wphrmdate );
                if(is_array($datetime)){
                    $noticeData[] = array(
                        'title' => ''.$wphrmNotice->wphrmtitle.'',
                        'start' => current($datetime),
                        'backgroundColor' => '#128807',
                        'textColor' => '#FFF',
                        'url' => admin_url('admin.php?page=wphrm-view-notice&notice_id='.$wphrmNotice->id),
                    );
                }
            }
        }

        echo json_encode($noticeData);
        exit;
    }
    function WPHRMDepartmentNoticeData(){
        global $wpdb;
        $noticeData = array();

        $department_id = empty($_POST['department']) ? 0 : esc_sql($_POST['department']);

        $wphrmNotices = $wpdb->get_results("SELECT * FROM  $this->WphrmNoticeTable WHERE wphrmdepartment = '$department_id' ORDER BY id DESC");
        $wphrmNotices = apply_filters('wphrm_department_notice_list', $wphrmNotices);
        if(!empty($wphrmNotices)){
            foreach($wphrmNotices as $wphrmNotice){
                $datetime = explode(' ', $wphrmNotice->wphrmdate );
                if(is_array($datetime)){
                    $noticeData[] = array(
                        'title' => ''.$wphrmNotice->wphrmtitle.'',
                        'start' => current($datetime),
                        'backgroundColor' => '#128807',
                        'textColor' => '#FFF',
                        'url' => admin_url('admin.php?page=wphrm-view-notice&notice_id='.$wphrmNotice->id),
                    );
                }
            }
        }

        echo json_encode($noticeData);
        exit;
    }
    /*J@F END*/

    /** employee attendance reports view. * */
    public function WPHRMEmployeeAttendanceReports() {
        global $wpdb;
        $wphrmEmployeeId = esc_sql($_POST['employee_id']); // esc
        $month = esc_sql($_POST['month']); // esc
        $year = esc_sql($_POST['year']); // esc
        $from = esc_sql($year . '-' . $month . '-' . '01'); // esc
        $to = esc_sql($year . '-' . $month . '-' . '31'); // esc
        $currentDate = date('Y-m-d');
        if (strtotime($to) > strtotime($currentDate)) {
            $actualDate = $currentDate;
        } else {
            $actualDate = $to;
        }

        $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $result = array();
        $present = array();
        $query_1 = "select * from $this->WphrmHolidaysTable where `wphrmDate` between '$from' AND '$to'";
        $employeeHolidays = $wpdb->get_results($query_1);
        $query_2 = "select * from $this->WphrmAttendanceTable where `employeeID` = $wphrmEmployeeId and `date` between '$from' AND '$actualDate'";
        $employeeAttendance = $wpdb->get_results($query_2);
        if (!empty($employeeHolidays)) {
            $holidaysReports = count($employeeHolidays);
            $workingday = $days - $holidaysReports;
        } else {
            $workingday = $days;
        }
        $leaves = 0;
        foreach ($employeeAttendance as $employee_attendancekey => $employeeAttendances) {
            if ($employeeAttendances->status == 'present' && $employeeAttendances->halfDayType != 'halfday') {
                $present[] = $employeeAttendances->status;
            }
            if ($employeeAttendances->status == 'absent') {
                $absents[] = $employeeAttendances->status;
                //check if this is leave and approved
                $leave_status = '';
                if($employeeAttendances->leaveApplicationId){
                    $leave_status = $this->get_leave_application_info($employeeAttendances->leaveApplicationId, 'applicationStatus');
                    $leaves += $leave_status == 'approved' ? 1 : 0;
                }
            }
            if ($employeeAttendances->halfDayType == 'halfday') {
                $halfday[] = $employeeAttendances->status;
                //check if this is leave and approved
                if($employeeAttendances->leaveApplicationId){
                    $leave_status = $this->get_leave_application_info($employeeAttendances->leaveApplicationId, 'applicationStatus');
                    $leaves += $leave_status == 'approved' ? 0.5 : 0;
                }
            }
        }
        if (!empty($halfday)) {
            $halfdays = count($halfday);
        } else {
            $halfdays = 0;
        }

        if (!empty($halfday)) {
            $halfdaysAbsent = count($halfday);
        } else {
            $halfdaysAbsent = 0;
        }
        if (!empty($absents)) {
            $halfdaysAbsent = $halfdaysAbsent / 2;
            $absent = $halfdaysAbsent + count($absents);
        } else {
            $halfdaysAbsent = $halfdaysAbsent / 2;
            $absent = $halfdaysAbsent + 0;
        }

        if (!empty($present)) {
            $presents = count($present);
            $halfdays = $halfdays / 2;
            $presents = $halfdays + count($present);
        } else {
            $halfdays = $halfdays / 2;
            $presents = $halfdays + 0;
        }

        $PerReport = ($presents * 100 ) / $workingday;
        $result['success'] = 'success';
        $result['Working'] = '' . $workingday . '/' . $presents . '';
        $result['PerReport'] = bcdiv($PerReport, 1, 2) . ' %';
        $result['workingDays'] = $workingday;
        $result['absent'] = $absent;
        $result['present'] = $presents;
        $result['leaves'] = $leaves;
        echo json_encode($result);
        exit;

    }

    /** Mark Attendance function. * */
    public function WPHRMEmployeeAttendanceMark() {
        global $current_user, $wpdb;
        $result = array();
        $attendanceCreateArray = '';
        $attendanceUpdateArray = '';
        $getAttendanceById = '';
        $wphrmCurrentUserRole = implode(',', $current_user->roles);
        $CreatedDates = esc_sql(date('Y-m-d H:i:s')); // esc
        $employee_ID = '';
        $leaveType = '';
        $reason = '';
        $attendance_mark = '';
        if (isset($_POST['checkbox'])) {
            $attendance_mark = esc_sql($_POST['checkbox']); // esc
        }
        if (isset($_POST['attendancedate']) && $_POST['attendancedate'] != '') {
            $attendanceDate = esc_sql($_POST['attendancedate']); // esc
        } else {
            $attendanceDate = esc_sql(date('Y-m-d')); // esc
        }
        if (isset($_POST['employees'])) {
            $employee_ID = esc_sql($_POST['employees']); // esc
        }
        if (isset($_POST['leaveType'])) {
            $leaveType = esc_sql($_POST['leaveType']); // esc
        }
        if (isset($_POST['reason'])) {
            $reason = esc_sql($_POST['reason']); // esc
        }
        if (isset($_POST['leaveOn'])) {
            $leaveOn = esc_sql($_POST['leaveOn']); // esc
        }
        $notification = array();
        $mark_manual = array();
        foreach ($employee_ID as $id) :
        $mark_manual[$id] = array();
        if (isset($attendance_mark[$id])) :
        $mark_manual[$id]['status'] = 'on';
        else :
        $mark_manual[$id]['status'] = 'off';
        endif;
        if (isset($leaveType[$id])) :
        $mark_manual[$id]['leaveType'] = $leaveType[$id];
        else :
        $mark_manual[$id]['leaveType'] = $leaveType[$id];
        endif;
        if (isset($reason[$id])) :
        $mark_manual[$id]['leaveReason'] = $reason[$id];
        else :
        $mark_manual[$id]['leaveReason'] = $reason[$id];
        endif;
        if (isset($leaveOn[$id])) :
        $mark_manual[$id]['leaveOn'] = $leaveOn[$id];
        else :
        $mark_manual[$id]['leaveOn'] = $leaveOn[$id];
        endif;

        endforeach;

        foreach ($mark_manual as $key => $attendance_marks):

        if ($attendance_marks['status'] == 'on') {
            if ($attendance_marks['leaveType'] != '') {
                $approvedStatus = 'approved';
            } else {
                $approvedStatus = '';
            }

            $getAttendanceById = $wpdb->get_row("select * from $this->WphrmAttendanceTable where `date` = '" . $attendanceDate . "' and `employeeID` ='" . $key . "'");
            if (empty($getAttendanceById)) {
                if ($attendance_marks['leaveOn'] != '') {
                    $data = array('halfDayType' => sanitize_text_field($attendance_marks['leaveOn']),
                                  'leaveType' => sanitize_text_field($attendance_marks['leaveType']),
                                  'reason' => sanitize_text_field($attendance_marks['leaveReason']),
                                  'status' => '',
                                  'applicationStatus' => $approvedStatus);
                } else {
                    $data = array(
                        'status' => sanitize_text_field('present'),
                        'halfDayType' => sanitize_text_field($attendance_marks['leaveOn']),
                        'leaveType' => sanitize_text_field($attendance_marks['leaveType']),
                        'reason' => sanitize_text_field($attendance_marks['leaveReason']),
                        'applicationStatus' => $approvedStatus);
                }

                $attendanceCreateArray = array(
                    'employeeID' => sanitize_text_field($key),
                    'date' => sanitize_text_field($attendanceDate),
                    'updatedBy' => sanitize_text_field($wphrmCurrentUserRole),
                    'createdAt' => sanitize_text_field($CreatedDates),
                    'updatedAt' => sanitize_text_field($CreatedDates),
                );

                $wpdb->insert($this->WphrmAttendanceTable, array_merge($attendanceCreateArray, $data));
            } else {

                if ($attendance_marks['leaveOn'] != '') {
                    $data = array(
                        'halfDayType' => sanitize_text_field($attendance_marks['leaveOn']),
                        'leaveType' => sanitize_text_field($attendance_marks['leaveType']),
                        'reason' => sanitize_text_field($attendance_marks['leaveReason']),
                        'status' => '',
                        'applicationStatus' => $approvedStatus);
                } else {
                    $data = array(
                        'status' => sanitize_text_field('present'),
                        'halfDayType' => sanitize_text_field($attendance_marks['leaveOn']),
                        'leaveType' => sanitize_text_field($attendance_marks['leaveType']),
                        'reason' => sanitize_text_field($attendance_marks['leaveReason']),
                        'applicationStatus' => $approvedStatus);
                }

                $attendanceUpdateArray = array(
                    'employeeID' => sanitize_text_field($key),
                    'date' => sanitize_text_field($attendanceDate),
                    'updatedBy' => sanitize_text_field($wphrmCurrentUserRole),
                    'updatedAt' => sanitize_text_field($CreatedDates),
                );
                $where_array = array('employeeID' => $key
                                     , 'date' => $attendanceDate);

                $wpdb->update($this->WphrmAttendanceTable, array_merge($attendanceUpdateArray, $data), $where_array);
            }
        } else {
            if ($attendance_marks['leaveType'] != '') {
                $approvedStatus = 'approved';
            } else {
                $approvedStatus = '';
            }
            $getAttendanceById = $wpdb->get_row("select * from $this->WphrmAttendanceTable where `date` = '" . $attendanceDate . "' and `employeeID` ='" . $key . "'");
            if (empty($getAttendanceById)) {

                if ($attendance_marks['leaveOn'] != '') {
                    $data = array(
                        'halfDayType' => sanitize_text_field($attendance_marks['leaveOn']),
                        'leaveType' => sanitize_text_field($attendance_marks['leaveType']),
                        'reason' => sanitize_text_field($attendance_marks['leaveReason']),
                        'status' => '',
                        'applicationStatus' => $approvedStatus);
                } else {
                    $data = array(
                        'status' => sanitize_text_field('absent'),
                        'halfDayType' => sanitize_text_field($attendance_marks['leaveOn']),
                        'leaveType' => sanitize_text_field($attendance_marks['leaveType']),
                        'reason' => sanitize_text_field($attendance_marks['leaveReason']),
                        'applicationStatus' => $approvedStatus);
                }
                $attendanceCreateArray = array(
                    'employeeID' => sanitize_text_field($key),
                    'date' => sanitize_text_field($attendanceDate),
                    'updatedBy' => sanitize_text_field($wphrmCurrentUserRole),
                    'createdAt' => sanitize_text_field($CreatedDates),
                    'updatedAt' => sanitize_text_field($CreatedDates),
                );

                $wpdb->insert($this->WphrmAttendanceTable, array_merge($attendanceCreateArray, $data));
            } else {
                if ($attendance_marks['leaveOn'] != '') {
                    $data = array('halfDayType' => sanitize_text_field($attendance_marks['leaveOn']),
                                  'leaveType' => sanitize_text_field($attendance_marks['leaveType']),
                                  'reason' => sanitize_text_field($attendance_marks['leaveReason']),
                                  'status' => '',
                                  'applicationStatus' => 'approved');
                } else {
                    $data = array('halfDayType' => sanitize_text_field($attendance_marks['leaveOn']),
                                  'status' => sanitize_text_field('absent'),
                                  'halfDayType' => sanitize_text_field($attendance_marks['leaveOn']),
                                  'leaveType' => sanitize_text_field($attendance_marks['leaveType']),
                                  'reason' => sanitize_text_field($attendance_marks['leaveReason']),
                                  'applicationStatus' => $approvedStatus);
                }
                $attendanceUpdateArray = array('employeeID' => sanitize_text_field($key),
                                               'date' => sanitize_text_field($attendanceDate),
                                               'updatedBy' => sanitize_text_field($wphrmCurrentUserRole),
                                               'createdAt' => sanitize_text_field($CreatedDates),
                                               'updatedAt' => sanitize_text_field($CreatedDates),
                                              );
                $where_array = array('employeeID' => $key,
                                     'date' => $attendanceDate);

                $wpdb->update($this->WphrmAttendanceTable, array_merge($attendanceUpdateArray, $data), $where_array);
            }
        }
        endforeach;
        $result['success'] = true;
        echo json_encode($result);
        exit;
    }

    /** WPHRM Mark Attendance Bulk function. * */
    public function WPHRMMarkAttendanceBulk() {
        global $current_user, $wpdb;
        $result = array();
        $employeeId = '';
        $fromDate = '';
        $toDate = '';
        $attendanceMark = '';
        $attendanceCreateArray = array();
        $wphrmCurrentUserRole = implode(',', $current_user->roles);
        $CreatedDates = esc_sql(date('Y-m-d H:i:s')); // esc
        if (isset($_POST['employee-id'])) {
            $employeeId = esc_sql($_POST['employee-id']); // esc
        }
        if (isset($_POST['from-date'])) {
            $fromDate = esc_sql($_POST['from-date']); // esc
        }
        if (isset($_POST['to-date']) && $_POST['to-date'] != '') {
            $toDate = esc_sql($_POST['to-date']); // esc
        }
        if (isset($_POST['attendance-mark'])) {
            $attendanceMark = esc_sql($_POST['attendance-mark']); // esc
        }

        if (!empty($employeeId)) {
            foreach ($employeeId as $key => $employeeIds):
            $dateBetweenArray = $this->createDateRangeArray(date('Y-m-d', strtotime($fromDate)), date('Y-m-d', strtotime($toDate)));
            $userGetApplications = $wpdb->get_results("SELECT * FROM $this->WphrmAttendanceTable WHERE `employeeID` = $employeeIds");
            $holidayArray = $wpdb->get_results("SELECT * FROM $this->WphrmHolidaysTable");
            foreach ($holidayArray as $holidayDates) {
                if (in_array($holidayDates->wphrmDate, $dateBetweenArray)) {
                    $allDates[] = $holidayDates->wphrmDate;
                }
            }

            foreach ($dateBetweenArray as $dateBetweenDates) {
                if (!in_array($dateBetweenDates, $allDates)) {
                    $userGetDates = $wpdb->get_row("SELECT * FROM $this->WphrmAttendanceTable WHERE `employeeID` = $employeeIds AND `date` = '$dateBetweenDates'");
                    if (empty($userGetDates)) {

                        $wpdb->query("INSERT INTO $this->WphrmAttendanceTable (`employeeID`,date,`toDate`, `status`, `appliedOn`, `createdAt`, `updatedAt`)
                                               VALUES('" . $employeeIds . "', '" . $dateBetweenDates . "', '0000-00-00','" . $attendanceMark . "' , '" . $dateBetweenDates . "', '" . $createUpdatedate . "', '" . $createUpdatedate . "')");
                    } else {
                        $wpdb->query("DELETE FROM `$this->WphrmAttendanceTable` WHERE `id` = $userGetDates->id");
                        $wpdb->query("INSERT INTO $this->WphrmAttendanceTable (`employeeID`,date,`toDate`, `status`, `appliedOn`, `createdAt`, `updatedAt`)
                                               VALUES('" . $employeeIds . "', '" . $dateBetweenDates . "', '0000-00-00','" . $attendanceMark . "' , '" . $dateBetweenDates . "', '" . $createUpdatedate . "', '" . $createUpdatedate . "')");
                    }
                }
            }
            endforeach;
            $result['success'] = __('Attendance mark in bulk has been successfully added.', 'wphrm');
            ;
        } else {
            $result['error'] = __(' Something went wrong.', 'wphrm');
            ;
        }
        echo json_encode($result);
        exit;
    }

    /** WPHRM Get Earning  function. * */
    public function WPHRMGetEarning($sa = '', $workingday = '', $worked = '') {
        $result = array();
        $wphrmEarningLabal = array();
        $wphrmEarningValue = array();
        $wphrmEarningInfo = $this->WPHRMGetSettings('wphrmEarningInfo');
        if (!empty($wphrmEarningInfo)) {
            $totalofEarningAmountPass = '';
            foreach ($wphrmEarningInfo['earningLebal'] as $earningLebal => $wphrmEarningsettingInfo) {
                foreach ($wphrmEarningInfo['earningtype'] as $earningtype => $wphrmEarningtype) {
                    foreach ($wphrmEarningInfo['earningamount'] as $earningamount => $earningamounts) {
                        if ($earningLebal == $earningtype && $earningLebal == $earningamount && $earningtype == $earningamount) {
                            $stotal = 0;
                            $main = 0;
                            $sapersant = 0;

                            $totalofEarningAmountPass = $earningamounts + $totalofEarningAmountPass;
                            $stotal = ($sa / $workingday);
                            $main = ($stotal * $worked);
                            $sapersant = ($main * $earningamounts) / 100;

                            $wphrmEarningLabal[] = $wphrmEarningsettingInfo;
                            $wphrmEarningValue[] = $sapersant;
                        }
                    }
                }
            }
        }
        $current = (100 - $totalofEarningAmountPass);
        $btotal = ($sa / $workingday);
        $mains = ($btotal * $worked);
        $currents = ($mains * $current) / 100;

        $wphrmEarningLabal[] = 'Miscellaneous';
        $wphrmEarningValue[] = $currents;
        $result = array('wphrmEarningLabal' => $wphrmEarningLabal, 'wphrmEarningValue' => $wphrmEarningValue);
        return $result;
    }

    /** WPHRM Get Deduction function. * */
    public function WPHRMGetDeduction($sa = '', $workingday = '', $worked = '') {
        $result = array();
        $wphrmDedutionLabal = array();
        $wphrmDedutionValue = array();
        $wphrmDeductionInfo = $this->WPHRMGetSettings('wphrmDeductionInfo');
        if (!empty($wphrmDeductionInfo)) {
            foreach ($wphrmDeductionInfo['deductionlebal'] as $deductionLebal => $wphrmDedutionsettingInfo) {
                foreach ($wphrmDeductionInfo['deductiontype'] as $deductiontype => $wphrmDeductiontype) {
                    foreach ($wphrmDeductionInfo['deductionamount'] as $deductionamount => $wphrmDeductionamountInfo) {
                        if ($deductionLebal == $deductiontype && $deductionLebal == $deductionamount && $deductiontype == $deductionamount) {
                            $stotal = 0;
                            $main = 0;
                            $sapersant = 0;

                            $stotal = ($sa / $workingday);
                            $main = ($stotal * $worked);
                            $sapersant = ($main * $wphrmDeductionamountInfo / 100);
                            $wphrmDedutionLabal[] = $wphrmDedutionsettingInfo;
                            $wphrmDedutionValue[] = $sapersant;
                        }
                    }
                }
            }
        }
        $result = array('wphrmDeductionLebal' => $wphrmDedutionLabal, 'wphrmDeductionValue' => $wphrmDedutionValue);
        return $result;
    }

    /** WPHRM Get Decimal Settings function. * */
    public function WPHRMGetDecimalSettings() {
        $wphrmdecimalInfo = $this->WPHRMGetSettings('wphrmSalarySlipInfo');
        if (isset($wphrmdecimalInfo['wphrm_currency_decimal']) && $wphrmdecimalInfo['wphrm_currency_decimal'] == '1') {
            $decimal = '.0';
        } else if (isset($wphrmdecimalInfo['wphrm_currency_decimal']) && $wphrmdecimalInfo['wphrm_currency_decimal'] == '2') {
            $decimal = '.00';
        } else if (isset($wphrmdecimalInfo['wphrm_currency_decimal']) && $wphrmdecimalInfo['wphrm_currency_decimal'] == '3') {
            $decimal = '.000';
        } else if (isset($wphrmdecimalInfo['wphrm_currency_decimal']) && $wphrmdecimalInfo['wphrm_currency_decimal'] == '4') {
            $decimal = '.0000';
        } else if (isset($wphrmdecimalInfo['wphrm_currency_decimal']) && $wphrmdecimalInfo['wphrm_currency_decimal'] == '5') {
            $decimal = '.00000';
        } else {
            $decimal = '.00';
        }
        if (isset($wphrmdecimalInfo['wphrm_currency_decimal']) && $wphrmdecimalInfo['wphrm_currency_decimal'] == '1') {
            $decimalvalue = $wphrmdecimalInfo['wphrm_currency_decimal'];
        } else {
            $decimalvalue = 2;
        }

        return array('decimalvalue' => $decimalvalue, 'decimal' => $decimal);
    }

    /** WPHRM Leave Counter function. * */
    public function WPHRMLeaveCounter($attendance_date = '', $employee_id = '', $between = '') {
        global $current_user, $wpdb;
        $attendance_date = $attendance_date;
        $result = array();
        $todaydate = esc_sql('0'); // esc
        $employeeAttendanceCount = esc_sql('0'); // esc
        $currentMonth = date('m'); // esc
        $wphrmEmployeeInfo = '';
        $wphrmEmployeeInfo = $this->WPHRMGetUserDatas($employee_id, 'wphrmEmployeeInfo');
        $current1 = date("Y-m-d");
        if ($between == 'between') {
            $dates = date('Y-m', strtotime($attendance_date));
            $attendance_dates = $dates . '-01';
            $days = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($attendance_date)), date('Y', strtotime(date("Y-m-d"))));
            $fromDates = $dates . '-' . $days;
            $query = "AND `applicationStatus`='approved' AND `date` between '$attendance_dates' AND '$fromDates'";
        } else {
            $query = " AND `date` <= '$attendance_date' AND `applicationStatus`='approved'";
        }

        if (isset($wphrmEmployeeInfo['wphrm_employee_joining_date'])) {
            $wphrmEmployeeJoiningDate = $wphrmEmployeeInfo['wphrm_employee_joining_date'];
        }
        $wphrmEmployeeJoiningDate = new DateTime($wphrmEmployeeJoiningDate);
        $today = new DateTime();
        $interval = $today->diff($wphrmEmployeeJoiningDate);
        $wphrmEmployeeJoiningToCurrentTotalYear = ((int) $interval->format('%y years') + 1);
        $curQuarter = ceil($currentMonth / 3);
        $leavesTypes = $wpdb->get_results("SELECT * FROM $this->WphrmLeaveTypeTable");
        foreach ($leavesTypes as $leavesType) {
            $leaveRemaining = '';
            $leaveTotal = '';
            $totalNoOfLeave = 0;
            if ($leavesType->period == 'Monthly') {
                $totalNoOfLeave = intval($leavesType->numberOfLeave * $currentMonth);
            } else if ($leavesType->period == 'Quarterly') {
                $totalNoOfLeave = intval($leavesType->numberOfLeave * $curQuarter);
            } else if ($leavesType->period == 'Yearly') {
                $totalNoOfLeave = intval($leavesType->numberOfLeave * $wphrmEmployeeJoiningToCurrentTotalYear);
            }
            $employeeLeaves = $wpdb->get_row("SELECT COUNT(id) AS leaveCounter FROM $this->WphrmAttendanceTable WHERE `status`='absent' AND `employeeID` ='" . $employee_id . "' AND `leaveType`='$leavesType->leaveType' $query");
            $employeeLeavesHalfday = $wpdb->get_row("SELECT COUNT(id) AS halfdayCounter FROM $this->WphrmAttendanceTable WHERE `halfDayType`='halfday' AND `employeeID` ='" . $employee_id . "' AND `leaveType`='$leavesType->leaveType' $query");
            $halfdayCounter = ($employeeLeavesHalfday->halfdayCounter / 2);
            $leaveTotal = $employeeLeaves->leaveCounter + $halfdayCounter;
            $leaveRemaining = $leaveTotal;

            if ($totalNoOfLeave >= $leaveTotal) {
                if ($between == 'between') {
                    $leaveRemainings[] = array('leavetype' => $leavesType->leaveType, 'leave' => $leaveRemaining);
                } else {
                    $leaveRemainings[] = array('leavetype' => $leavesType->leaveType, 'leave' => $totalNoOfLeave - $leaveRemaining);
                }
            } else {
                if ($between == 'between') {
                    $leaveRemainings[] = array('leavetype' => $leavesType->leaveType, 'leave' => $leaveRemaining);
                } else {
                    $leaveRemainings[] = array('leavetype' => $leavesType->leaveType, 'leave' => $totalNoOfLeave - $leaveRemaining);
                }
            }
        }

        $result = $leaveRemainings;
        return $result;
    }

    public function WPHRMNegValue($var) {
        $result = array();
        foreach ($var as $vars) {
            if ($vars < 0) {
                $neg = explode('-', $vars);
                $result[] = $neg[1];
            }
        }
        return $result;
    }

    /** WPHRM Employee Wise Attendance Count function. * */
    public function WPHRMEmployeeWiseAttendanceCount($days = '', $from = '', $to = '', $employee_id = '', $getMonth = '') {
        global $current_user, $wpdb;
        $result = array();
        $present = array();
        $presenhalf = array();
        $allPresent = array();
        $approoved = array();
        $presentss = 0;
        $approovedWorking = 0;
        $allPresents = 0;
        $holidays_reports = 0;
        $halfDays = array();
        $absentHalfDays = array();

        $beforeMonthLeaves = $this->WPHRMLeaveCounter($getMonth, $employee_id, '');
        $currentMonthLeaves = $this->WPHRMLeaveCounter($getMonth, $employee_id, 'between');

        foreach ($beforeMonthLeaves as $beforeMonthLeave) {
            foreach ($currentMonthLeaves as $currentMonthLeave) {
                if ($beforeMonthLeave['leavetype'] == $currentMonthLeave['leavetype']) {
                    $dataDetails[] = $beforeMonthLeave['leave'] - $currentMonthLeave['leave'];
                }
            }
        }
        foreach ($beforeMonthLeaves as $beforeMonthLeave) {
            foreach ($currentMonthLeaves as $currentMonthLeave) {
                if ($beforeMonthLeave['leavetype'] == $currentMonthLeave['leavetype']) {
                    $totaldetails[] = $currentMonthLeave['leave'];
                }
            }
        }
        $dataDetail = $this->WPHRMNegValue($dataDetails);
        $totaldetails = array_sum($totaldetails);
        $totalLeave = array_sum($dataDetail);
        $approvedleaves = $totaldetails - $totalLeave; //total leave minus approved leave
        $unapprovedLeave = ($totaldetails - $approvedleaves);

        $employee_holidays = $wpdb->get_results("SELECT * FROM $this->WphrmHolidaysTable WHERE `wphrmDate` between '$from' AND '$to'");
        $employee_attendance = $wpdb->get_results("SELECT * FROM $this->WphrmAttendanceTable WHERE `employeeID` = $employee_id AND `date` between '$from' AND '$to'");

        if (!empty($employee_holidays)) {
            $holidays_reports = count($employee_holidays);
            $workingday = $days - $holidays_reports;
        } else {
            $workingday = $days;
        }

        if (!empty($employee_attendance)) {
            foreach ($employee_attendance as $employee_attendancekey => $employee_attendances) {
                if ($employee_attendances->status == 'absent' && (empty($employee_attendances->leaveType)) && $employee_attendances->applicationStatus != 'approved') {
                    $present[] = $employee_attendances->status;
                }
                if ($employee_attendances->status == '' && ($employee_attendances->leaveType == '') && ($employee_attendances->halfDayType == 'halfday') && $employee_attendances->applicationStatus == 'approved') {
                    $absentHalfDays[] = $employee_attendances->halfDayType;
                }
            }
        }

        if (!empty($employee_attendance)) {
            foreach ($employee_attendance as $employee_attendancekey => $employee_attendances) {
                if ($employee_attendances->status == 'present') {
                    $allPresent[] = $employee_attendances->status;
                }
            }
        }

        $totalAbsents = count($present);
        if (!empty($present)) {
            $presents = count($present) + (count($absentHalfDays) / 2);
        } else {
            $presents = 0 + (count($absentHalfDays) / 2);
        }

        if (!empty($allPresent)) {
            $allPresents = count($allPresent) + $approvedleaves;
        } else {
            $allPresents = 0 + $approvedleaves;
        }

        $approovedWorking = ($allPresents - $presents);
        $totalofWorkingDays = ($days - $holidays_reports);
        $totalEarned = $approvedleaves;
        $totalLeavedCount = ($approvedleaves + $presents);
        $halfdayOfWork = (count($absentHalfDays) / 2);
        $getLeave = ($allPresents - $totalEarned) + ($totalEarned + $halfdayOfWork);

        if ($approvedleaves != 0) {
            $lineadd = __('+ Earned:', 'wphrm') . $totalEarned . '= ' . $totalLeavedCount;
            $lineaddno = __('+ Earned Leaves:', 'wphrm') . ($totalEarned) . ' = ';
        } else {
            $lineaddno = __(' , Total = ', 'wphrm');
            $lineadd = '';
        }

        $presentss = __(' Paid:', 'wphrm') . $presents . $lineadd;
        $worked = 'P: ' . ($halfdayOfWork + $allPresents - $totalEarned) . $lineaddno . $getLeave;
        $result = array('workingday' => ($workingday + $holidays_reports), 'totalofWorkingDays' => $totalofWorkingDays, 'presents' => $presentss, 'worked' => $worked, 'totalWorked' => ($getLeave + $holidays_reports));
        return $result;
    }
    public function WPHRMGenerateDefaultSalary() {
        $wphrmSalaryAccording = $this->WPHRMGetSettings('wphrsalaryDayOrHourlyInfo');
        if (isset($wphrmSalaryAccording['wphrm-according']) && $wphrmSalaryAccording['wphrm-according'] != 'Week') {
            $this->WPHRMGenerateDefaultRun();
        }
    }
    public function WPHRMGenerateDefaultRun() {
        global $wpdb;
        $result = array();
        $wphrmInformation = '';
        $wphrmHours = '';
        $today = date('d');
        $wphrmGenerationSetting = $this->WPHRMGetSettings('wphrmSalaryGenerationSettings');
        if (isset($wphrmGenerationSetting['monthdate'])) {
            $monthdate = $wphrmGenerationSetting['monthdate']; // esc
            if (isset($monthdate) && $monthdate != '') {
                $monthdates = $monthdate;
            } else {
                $monthdates = 1;
            }
            if ($monthdates < 9) {
                $gDate = '0' . $monthdates;
            } else {
                $gDate = $monthdates;
            }
        }
        if ($gDate == $today) {
            $getmonth = date('m'); // esc
            if ($getmonth == '01') {
                $generateYear = (date('Y') - 1);
                $generate_year = (date('Y') - 1); // esc
            } else {
                $generateYear = date('Y');
                $generate_year = date('Y'); // esc
            }
            $month = date('m', strtotime(date('Y-m') . " -1 month"));

            if (isset($wphrmGenerationSetting['informationtext'])) {
                $wphrmInformation = $wphrmGenerationSetting['informationtext']; // esc
            }
            $from = esc_sql($generate_year . '-' . $month . '-' . '01'); // esc
            $getMonth = date('Y-m-d', strtotime(date('Y-m-d') . " -1 month"));
            $to = esc_sql($generate_year . '-' . $month . '-' . '31'); // esc
            $days = cal_days_in_month(CAL_GREGORIAN, $month, $generate_year);
            $wphrmUsers = get_users();
            if (!empty($wphrmUsers)) {
                foreach ($wphrmUsers as $key => $userdata) {
                    $wphrmDeductionValue = '';
                    $wphrmEarningLebal = '';
                    $wphrmEarningValue = '';
                    $wphrmDeductionLebal = '';
                    $earningTotal = '';
                    $deductionTotal = '';
                    $wphrmDayOfWorking = '';
                    $wphrmWorkleave = '';
                    $wphrmDayOfWorked = '';
                    $wphrmAccountNo = '';
                    $totalofWorkingDays = '';
                    $sa = 0;
                    $employeeOtherId = $userdata->ID; // esc
                    $wphrmEmployeeInfo = $this->WPHRMGetUserDatas($employeeOtherId, 'wphrmEmployeeInfo');
                    if (isset($wphrmEmployeeInfo['wphrm_employee_status']) && $wphrmEmployeeInfo['wphrm_employee_status'] == 'Active') {
                        $wphrmEmployeeSalaryInfo = $this->WPHRMGetUserDatas($employeeOtherId, 'wphrmEmployeeSalaryInfo');
                        $wphrm_bank_info = $this->WPHRMGetUserDatas($employeeOtherId, 'wphrmEmployeeBankInfo');
                        if (isset($wphrmEmployeeSalaryInfo['current-salary'])) {
                            $sa = $wphrmEmployeeSalaryInfo['current-salary'];
                        }
                        if (isset($wphrm_bank_info['wphrm_employee_bank_account_no'])) {
                            $wphrmAccountNo = $wphrm_bank_info['wphrm_employee_bank_account_no']; // esc
                        }
                        $attndance = $this->WPHRMEmployeeWiseAttendanceCount($days, $from, $to, $employeeOtherId, $getMonth);
                        if (isset($attndance['workingday'])) {
                            $wphrmDayOfWorking = esc_sql($attndance['workingday']); // esc
                        }
                        if (isset($attndance['presents'])) {
                            $wphrmWorkleave = esc_sql($attndance['presents']); // esc
                        }
                        if (isset($attndance['totalWorked'])) {
                            $wphrmDayOfWorked = esc_sql($attndance['totalWorked']); // esc
                        }
                        if (isset($attndance['totalofWorkingDays'])) {
                            $totalofWorkingDays = esc_sql($attndance['totalofWorkingDays']); // esc
                        }
                        if (isset($attndance['worked'])) {
                            $wphrmworked = esc_sql($attndance['worked']); // esc
                        }


                        $wphrmEarningInfo = $this->WPHRMGetEarning($sa, $wphrmDayOfWorking, $wphrmDayOfWorked);
                        $wphrmEarningLebal = esc_sql($wphrmEarningInfo['wphrmEarningLabal']); // esc
                        $wphrmEarningValue = esc_sql($wphrmEarningInfo['wphrmEarningValue']); // esc

                        $wphrmEarningInfo = $this->WPHRMGetDeduction($sa, $wphrmDayOfWorking, $wphrmDayOfWorked);
                        $wphrmDeductionLebal = esc_sql($wphrmEarningInfo['wphrmDeductionLebal']); // esc
                        $wphrmDeductionValue = esc_sql($wphrmEarningInfo['wphrmDeductionValue']); // esc

                        $earningTotal = array_sum($wphrmEarningValue);
                        $deductionTotal = array_sum($wphrmDeductionValue);
                        if ($earningTotal > $deductionTotal) {
                            $earningGreaterThanDeduction = 'yes';
                        } else {
                            $earningGreaterThanDeduction = 'no';
                        }

                        if ($earningGreaterThanDeduction == 'yes') {
                            $generateDate = esc_sql($generateYear . '-' . $month . '-' . '01'); // esc
                            $notification = '';
                            $wphrmFormEarningDetails = array();
                            $wphrmFormDeductionDetails = array();
                            $wphrmFormDetails = array();

                            $wphrmEarningfiledskey = $wpdb->get_row("SELECT * FROM $this->WphrmSalaryTable WHERE `employeeID` = $employeeOtherId AND `employeeKey` = 'wphrmEarningfiledskey' AND `date`='$generateDate'");
                            if (!empty($wphrmEarningfiledskey)) :
                            $wphrmFormEarningDetails = unserialize(base64_decode($wphrmEarningfiledskey->employeeValue));
                            $wphrmFormEarningDetails['wphrmEarningLebal'] = $this->WPHRMSanitize($wphrmEarningLebal);
                            $wphrmFormEarningDetails['wphrmEarningValue'] = $this->WPHRMSanitize($wphrmEarningValue);
                            $wphrmFormEarningData = base64_encode(serialize($wphrmFormEarningDetails));
                            $wpdb->query("UPDATE $this->WphrmSalaryTable SET  `employeeValue`='$wphrmFormEarningData' WHERE `employeeID`= $employeeOtherId AND `employeeKey`= 'wphrmEarningfiledskey' AND `date`='$generateDate'");
                            else :
                            $wphrmFormEarningDetails['wphrmEarningLebal'] = $this->WPHRMSanitize($wphrmEarningLebal);
                            $wphrmFormEarningDetails['wphrmEarningValue'] = $this->WPHRMSanitize($wphrmEarningValue);
                            $wphrmFormEarningData = base64_encode(serialize($wphrmFormEarningDetails));
                            $wphrmEarningfiledskeyiNFORMATION = sanitize_text_field('wphrmEarningfiledskey'); // sanitize_text_field

                            $wpdb->query("INSERT INTO $this->WphrmSalaryTable (`employeeID`, `employeeKey`, `employeeValue`, `date`)
                                               VALUES($employeeOtherId, '$wphrmEarningfiledskeyiNFORMATION', '$wphrmFormEarningData', '$generateDate')");

                            endif;
                            $wphrmDeductionfiledskey = $wpdb->get_row("SELECT * FROM $this->WphrmSalaryTable WHERE `employeeID` = $employeeOtherId AND `employeeKey` = 'wphrmDeductionfiledskey' AND `date`='$generateDate'");
                            if (!empty($wphrmDeductionfiledskey)) {
                                $wphrmFormDeductionDetails = unserialize(base64_decode($wphrmDeductionfiledskey->employeeValue));
                                $wphrmFormDeductionDetails['wphrmDeductionLebal'] = $this->WPHRMSanitize($wphrmDeductionLebal);
                                $wphrmFormDeductionDetails['wphrmDeductionValue'] = $this->WPHRMSanitize($wphrmDeductionValue);
                                $wphrmFormDeductionData = base64_encode(serialize($wphrmFormDeductionDetails));
                                $wpdb->query("UPDATE $this->WphrmSalaryTable SET  `employeeValue`='$wphrmFormDeductionData' WHERE `employeeID`= $employeeOtherId AND `employeeKey`= 'wphrmDeductionfiledskey' AND `date`='$generateDate'");
                            } else {
                                $wphrmFormDeductionDetails['wphrmDeductionLebal'] = $this->WPHRMSanitize($wphrmDeductionLebal);
                                $wphrmFormDeductionDetails['wphrmDeductionValue'] = $this->WPHRMSanitize($wphrmDeductionValue);
                                $wphrmFormDeductionData = base64_encode(serialize($wphrmFormDeductionDetails));
                                $wphrmDeductionfiledskeyiNFORMATION = sanitize_text_field('wphrmDeductionfiledskey'); // sanitize_text_field
                                $wpdb->query("INSERT INTO $this->WphrmSalaryTable (`employeeID`, `employeeKey`, `employeeValue`, `date`)
                                               VALUES($employeeOtherId, '$wphrmDeductionfiledskeyiNFORMATION', '$wphrmFormDeductionData', '$generateDate')");
                            }
                            $wphrmTextInfo = $wpdb->get_row("SELECT * FROM $this->WphrmSalaryTable where `employeeID` = $employeeOtherId AND `employeeKey` = 'employeeSalaryNote' AND `date`='$generateDate'");
                            if (empty($wphrmTextInfo)) {
                                $wphrmFormDetails['wphrm_information'] = sanitize_text_field($wphrmInformation);
                                $wphrmFormDetails['wphrm_dayofworking'] = sanitize_text_field($totalofWorkingDays);
                                $wphrmFormDetails['wphrm_workleave'] = sanitize_text_field($wphrmWorkleave);
                                $wphrmFormDetails['wphrm_dayofworked'] = sanitize_text_field($wphrmworked);
                                $wphrmFormDetails['wphrm_Hours'] = sanitize_text_field($wphrmHours);
                                $wphrmFormDetails['wphrm_account_no'] = sanitize_text_field($wphrmAccountNo);
                                $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
                                $wpdb->query("INSERT INTO $this->WphrmSalaryTable (`employeeID`, `employeeKey`, `employeeValue`, `date`)
                                               VALUES($employeeOtherId, 'employeeSalaryGenerated', 'generated', '$generateDate')");
                                $id = $wpdb->query("INSERT INTO $this->WphrmSalaryTable (`employeeID`, `employeeKey`, `employeeValue`, `date`)
                                               VALUES($employeeOtherId, 'employeeSalaryNote', '$wphrmFormDetailsData', '$generateDate')");
                                $lastid = $wpdb->insert_id;
                                $id = $wpdb->query("UPDATE $this->WphrmSalaryTable SET `date`='$generateDate' WHERE `id`= $lastid AND `employeeKey`='employeeSalaryNote'");
                                $result['success'] = 'Salary slip data successfully updated';
                                $notification = array('wphrmUserID' => sanitize_text_field($employeeOtherId),
                                                      'wphrmDesc' => sanitize_text_field('Your salary has been generated for ' . date('F Y', strtotime($generateDate)) . '.'),
                                                      'notificationType' => sanitize_text_field('Salary Generated'),
                                                      'wphrmStatus' => sanitize_text_field('unseen'),
                                                      'wphrmDate' => sanitize_text_field(date('Y-m-d')),
                                                     );
                                $wpdb->insert($this->WphrmNotificationsTable, $notification);
                            } else {
                                $wphrmGenerate = $wpdb->get_row("SELECT * FROM $this->WphrmSalaryTable WHERE `employeeID` = $employeeOtherId AND `employeeKey` = 'employeeSalaryGenerated' AND `date`='$generateDate'");
                                if (empty($wphrmGenerate)) {
                                    $wpdb->query("INSERT INTO $this->WphrmSalaryTable (`employeeID`, `employeeKey`, `employeeValue`, `date`)
                                               VALUES($employeeOtherId, 'employeeSalaryGenerated', 'generated', '$generateDate'");
                                }
                                $wphrmFormDetails = unserialize(base64_decode($wphrmTextInfo->employeeValue));
                                $wphrmFormDetails['wphrm_information'] = sanitize_text_field($wphrmInformation);
                                $wphrmFormDetails['wphrm_dayofworking'] = sanitize_text_field($totalofWorkingDays);
                                $wphrmFormDetails['wphrm_workleave'] = sanitize_text_field($wphrmWorkleave);
                                $wphrmFormDetails['wphrm_dayofworked'] = sanitize_text_field($wphrmworked);
                                $wphrmFormDetails['wphrm_Hours'] = sanitize_text_field($wphrmHours);
                                $wphrmFormDetails['wphrm_account_no'] = sanitize_text_field($wphrmAccountNo);
                                $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
                                $id = $wpdb->query("UPDATE $this->WphrmSalaryTable SET `employeeValue`='$wphrmFormDetailsData' WHERE `employeeID`= $employeeOtherId AND `employeeKey` = 'employeeSalaryNote' AND `date`='$generateDate'");
                            }
                        }
                    }
                }
            }
        }
        return;
    }

    /** Employee Generate Salary Slip function. * */
    public function WPHRMGenerateSalary() {
        global $wpdb;
        $result = array();
        $wphrmInformation = '';
        $wphrmDayOfWorking = '';
        $wphrmWorkleave = '';
        $wphrmDayOfWorked = '';
        $wphrmAccountNo = '';
        $wphrmHours = '';
        $employeeOtherId = esc_sql($_POST['wphrm_emp_generate_id']); // esc
        $generateYear = esc_sql($_POST['wphrm_generate_year']); // esc
        $generateMonth = esc_sql($_POST['wphrm_generate_month']); // esc

        if (isset($_POST['wphrm_information']) && $_POST['wphrm_information'] != '') {
            $wphrmInformation = esc_sql($_POST['wphrm_information']); // esc
        }
        if (isset($_POST['wphrm_dayofworking']) && $_POST['wphrm_dayofworking'] != '') {
            $wphrmDayOfWorking = esc_sql($_POST['wphrm_dayofworking']); // esc
        }
        if (isset($_POST['wphrm_workleave']) && $_POST['wphrm_workleave'] != '') {
            $wphrmWorkleave = esc_sql($_POST['wphrm_workleave']); // esc
        }
        if (isset($_POST['wphrm_dayofworked']) && $_POST['wphrm_dayofworked'] != '') {
            $wphrmDayOfWorked = esc_sql($_POST['wphrm_dayofworked']); // esc
        }
        if (isset($_POST['wphrm_account_no']) && $_POST['wphrm_account_no'] != '') {
            $wphrmAccountNo = esc_sql($_POST['wphrm_account_no']); // esc
        }
        if (isset($_POST['wphrm_Hours']) && $_POST['wphrm_Hours'] != '') {
            $wphrmHours = esc_sql($_POST['wphrm_Hours']); // esc
        }
        $wphrmEarningLebal = esc_sql($_POST['wphrmEarningLebal']); // esc
        $wphrmEarningValue = esc_sql($_POST['wphrmEarningValue']); // esc

        $wphrmDeductionLebal = esc_sql($_POST['wphrmDeductionLebal']); // esc
        $wphrmDeductionValue = esc_sql($_POST['wphrmDeductionValue']); // esc
        $generateMonth = esc_sql(date('m', strtotime($generateMonth))); // esc
        $generateDate = esc_sql($generateYear . '-' . $generateMonth . '-' . '01'); // esc
        $notification = '';
        $wphrmFormEarningDetails = array();
        $wphrmFormDeductionDetails = array();
        $wphrmFormDetails = array();

        $wphrmEarningfiledskey = $wpdb->get_row("SELECT * FROM $this->WphrmSalaryTable WHERE `employeeID` = $employeeOtherId AND `employeeKey` = 'wphrmEarningfiledskey' AND `date`='$generateDate'");
        if (!empty($wphrmEarningfiledskey)) :
        $wphrmFormEarningDetails = unserialize(base64_decode($wphrmEarningfiledskey->employeeValue));
        $wphrmFormEarningDetails['wphrmEarningLebal'] = $this->WPHRMSanitize($wphrmEarningLebal);
        $wphrmFormEarningDetails['wphrmEarningValue'] = $this->WPHRMSanitize($wphrmEarningValue);
        $wphrmFormEarningData = base64_encode(serialize($wphrmFormEarningDetails));
        $wpdb->query("UPDATE $this->WphrmSalaryTable SET  `employeeValue`='$wphrmFormEarningData' WHERE `employeeID`= $employeeOtherId AND `employeeKey`= 'wphrmEarningfiledskey' AND `date`='$generateDate'");
        else :
        $wphrmFormEarningDetails['wphrmEarningLebal'] = $this->WPHRMSanitize($wphrmEarningLebal);
        $wphrmFormEarningDetails['wphrmEarningValue'] = $this->WPHRMSanitize($wphrmEarningValue);
        $wphrmFormEarningData = base64_encode(serialize($wphrmFormEarningDetails));
        $wphrmEarningfiledskeyiNFORMATION = sanitize_text_field('wphrmEarningfiledskey'); // sanitize_text_field

        $wpdb->query("INSERT INTO $this->WphrmSalaryTable (`employeeID`, `employeeKey`, `employeeValue`, `date`)
                                               VALUES($employeeOtherId, '$wphrmEarningfiledskeyiNFORMATION', '$wphrmFormEarningData', '$generateDate')");

        endif;
        $wphrmDeductionfiledskey = $wpdb->get_row("SELECT * FROM $this->WphrmSalaryTable WHERE `employeeID` = $employeeOtherId AND `employeeKey` = 'wphrmDeductionfiledskey' AND `date`='$generateDate'");
        if (!empty($wphrmDeductionfiledskey)) {
            $wphrmFormDeductionDetails = unserialize(base64_decode($wphrmDeductionfiledskey->employeeValue));
            $wphrmFormDeductionDetails['wphrmDeductionLebal'] = $this->WPHRMSanitize($wphrmDeductionLebal);
            $wphrmFormDeductionDetails['wphrmDeductionValue'] = $this->WPHRMSanitize($wphrmDeductionValue);
            $wphrmFormDeductionData = base64_encode(serialize($wphrmFormDeductionDetails));
            $wpdb->query("UPDATE $this->WphrmSalaryTable SET  `employeeValue`='$wphrmFormDeductionData' WHERE `employeeID`= $employeeOtherId AND `employeeKey`= 'wphrmDeductionfiledskey' AND `date`='$generateDate'");
        } else {
            $wphrmFormDeductionDetails['wphrmDeductionLebal'] = $this->WPHRMSanitize($wphrmDeductionLebal);
            $wphrmFormDeductionDetails['wphrmDeductionValue'] = $this->WPHRMSanitize($wphrmDeductionValue);
            $wphrmFormDeductionData = base64_encode(serialize($wphrmFormDeductionDetails));
            $wphrmDeductionfiledskeyiNFORMATION = sanitize_text_field('wphrmDeductionfiledskey'); // sanitize_text_field
            $wpdb->query("INSERT INTO $this->WphrmSalaryTable (`employeeID`, `employeeKey`, `employeeValue`, `date`)
                                               VALUES($employeeOtherId, '$wphrmDeductionfiledskeyiNFORMATION', '$wphrmFormDeductionData', '$generateDate')");
        }
        $wphrmTextInfo = $wpdb->get_row("SELECT * FROM $this->WphrmSalaryTable where `employeeID` = $employeeOtherId AND `employeeKey` = 'employeeSalaryNote' AND `date`='$generateDate'");
        if (empty($wphrmTextInfo)) {
            $wphrmFormDetails['wphrm_information'] = sanitize_text_field($wphrmInformation);
            $wphrmFormDetails['wphrm_dayofworking'] = sanitize_text_field($wphrmDayOfWorking);
            $wphrmFormDetails['wphrm_workleave'] = sanitize_text_field($wphrmWorkleave);
            $wphrmFormDetails['wphrm_dayofworked'] = sanitize_text_field($wphrmDayOfWorked);
            $wphrmFormDetails['wphrm_Hours'] = sanitize_text_field($wphrmHours);
            $wphrmFormDetails['wphrm_account_no'] = sanitize_text_field($wphrmAccountNo);
            $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
            $wpdb->query("INSERT INTO $this->WphrmSalaryTable (`employeeID`, `employeeKey`, `employeeValue`, `date`)
                                               VALUES($employeeOtherId, 'employeeSalaryGenerated', 'generated', '$generateDate')");
            $id = $wpdb->query("INSERT INTO $this->WphrmSalaryTable (`employeeID`, `employeeKey`, `employeeValue`, `date`)
                                               VALUES($employeeOtherId, 'employeeSalaryNote', '$wphrmFormDetailsData', '$generateDate')");
            $lastid = $wpdb->insert_id;
            $id = $wpdb->query("UPDATE $this->WphrmSalaryTable SET `date`='$generateDate' WHERE `id`= $lastid AND `employeeKey`='employeeSalaryNote'");
            $result['success'] = 'Salary slip data successfully updated';
            $notification = array('wphrmUserID' => sanitize_text_field($employeeOtherId),
                                  'wphrmDesc' => sanitize_text_field('Your salary has been generated for ' . date('F Y', strtotime($generateDate)) . '.'),
                                  'notificationType' => sanitize_text_field('Salary Generated'),
                                  'wphrmStatus' => sanitize_text_field('unseen'),
                                  'wphrmDate' => sanitize_text_field(date('Y-m-d')),
                                 );
            $wpdb->insert($this->WphrmNotificationsTable, $notification);
        } else {
            $wphrmGenerate = $wpdb->get_row("SELECT * FROM $this->WphrmSalaryTable WHERE `employeeID` = $employeeOtherId AND `employeeKey` = 'employeeSalaryGenerated' AND `date`='$generateDate'");
            if (empty($wphrmGenerate)) {
                $wpdb->query("INSERT INTO $this->WphrmSalaryTable (`employeeID`, `employeeKey`, `employeeValue`, `date`)
                                               VALUES($employeeOtherId, 'employeeSalaryGenerated', 'generated', '$generateDate'");
            }
            $wphrmFormDetails = unserialize(base64_decode($wphrmTextInfo->employeeValue));
            $wphrmFormDetails['wphrm_information'] = sanitize_text_field($wphrmInformation);
            $wphrmFormDetails['wphrm_dayofworking'] = sanitize_text_field($wphrmDayOfWorking);
            $wphrmFormDetails['wphrm_workleave'] = sanitize_text_field($wphrmWorkleave);
            $wphrmFormDetails['wphrm_dayofworked'] = sanitize_text_field($wphrmDayOfWorked);
            $wphrmFormDetails['wphrm_Hours'] = sanitize_text_field($wphrmHours);
            $wphrmFormDetails['wphrm_account_no'] = sanitize_text_field($wphrmAccountNo);
            $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
            $id = $wpdb->query("UPDATE $this->WphrmSalaryTable SET `employeeValue`='$wphrmFormDetailsData' WHERE `employeeID`= $employeeOtherId AND `employeeKey` = 'employeeSalaryNote' AND `date`='$generateDate'");
            $result['success'] = 'Salary slip data successfully updated';
        }
        echo json_encode($result);
        exit;
    }
    /** Employee Generate Week Salary Slip function. * */
    public function WPHRMGenerateWeekSalary() {
        global $wpdb;
        $result = array();
        $wphrmInformation = '';
        $wphrmDayOfWorking = '';
        $wphrmWorkleave = '';
        $wphrmDayOfWorked = '';
        $wphrmAccountNo = '';
        $wphrmGenerateWeekNo = '';
        $wphrmHours = '';
        $employeeOtherId = esc_sql($_POST['wphrm_emp_generate_id']); // esc
        $generateYear = esc_sql($_POST['wphrm_generate_year']); // esc
        $generateMonth = esc_sql($_POST['wphrm_generate_month']); // esc
        $wphrmGenerateWeekNo = esc_sql($_POST['wphrm_generate_week_no']); // esc

        if (isset($_POST['wphrm_information']) && $_POST['wphrm_information'] != '') {
            $wphrmInformation = esc_sql($_POST['wphrm_information']); // esc
        }
        if (isset($_POST['wphrm_dayofworking']) && $_POST['wphrm_dayofworking'] != '') {
            $wphrmDayOfWorking = esc_sql($_POST['wphrm_dayofworking']); // esc
        }
        if (isset($_POST['wphrm_workleave']) && $_POST['wphrm_workleave'] != '') {
            $wphrmWorkleave = esc_sql($_POST['wphrm_workleave']); // esc
        }
        if (isset($_POST['wphrm_dayofworked']) && $_POST['wphrm_dayofworked'] != '') {
            $wphrmDayOfWorked = esc_sql($_POST['wphrm_dayofworked']); // esc
        }
        if (isset($_POST['wphrm_account_no']) && $_POST['wphrm_account_no'] != '') {
            $wphrmAccountNo = esc_sql($_POST['wphrm_account_no']); // esc
        }
        if (isset($_POST['wphrm_Hours']) && $_POST['wphrm_Hours'] != '') {
            $wphrmHours = esc_sql($_POST['wphrm_Hours']); // esc
        }
        $wphrmEarningLebal = esc_sql($_POST['wphrmEarningLebal']); // esc
        $wphrmEarningValue = esc_sql($_POST['wphrmEarningValue']); // esc

        $wphrmDeductionLebal = esc_sql($_POST['wphrmDeductionLebal']); // esc
        $wphrmDeductionValue = esc_sql($_POST['wphrmDeductionValue']); // esc
        $generateMonth = esc_sql(date('m', strtotime($generateMonth))); // esc
        $generateDate = esc_sql($generateYear . '-' . $generateMonth . '-' . '01'); // esc
        $notification = '';
        $wphrmFormEarningDetails = array();
        $wphrmFormDeductionDetails = array();
        $wphrmFormDetails = array();

        $wphrmEarningfiledskey = $wpdb->get_row("SELECT * FROM $this->WphrmWeeklySalaryTable WHERE `employeeID` = $employeeOtherId AND `employeeKey` = 'wphrmEarningfiledskey' AND `date`='$generateDate' AND `weekOn`= '$wphrmGenerateWeekNo'");
        if (!empty($wphrmEarningfiledskey)) :
        $wphrmFormEarningDetails = unserialize(base64_decode($wphrmEarningfiledskey->employeeValue));
        $wphrmFormEarningDetails['wphrmEarningLebal'] = $this->WPHRMSanitize($wphrmEarningLebal);
        $wphrmFormEarningDetails['wphrmEarningValue'] = $this->WPHRMSanitize($wphrmEarningValue);
        $wphrmFormEarningData = base64_encode(serialize($wphrmFormEarningDetails));
        $wpdb->query("UPDATE $this->WphrmWeeklySalaryTable SET  `employeeValue`='$wphrmFormEarningData' WHERE `employeeID`= $employeeOtherId AND `employeeKey`= 'wphrmEarningfiledskey' AND `date`='$generateDate' AND `weekOn`= '$wphrmGenerateWeekNo'");
        else :
        $wphrmFormEarningDetails['wphrmEarningLebal'] = $this->WPHRMSanitize($wphrmEarningLebal);
        $wphrmFormEarningDetails['wphrmEarningValue'] = $this->WPHRMSanitize($wphrmEarningValue);
        $wphrmFormEarningData = base64_encode(serialize($wphrmFormEarningDetails));
        $wphrmEarningfiledskeyiNFORMATION = sanitize_text_field('wphrmEarningfiledskey'); // sanitize_text_field

        $wpdb->query("INSERT INTO $this->WphrmWeeklySalaryTable (`employeeID`,`weekOn`, `employeeKey`, `employeeValue`, `date`)
                                               VALUES($employeeOtherId,$wphrmGenerateWeekNo, '$wphrmEarningfiledskeyiNFORMATION', '$wphrmFormEarningData', '$generateDate')");

        endif;
        $wphrmDeductionfiledskey = $wpdb->get_row("SELECT * FROM $this->WphrmWeeklySalaryTable WHERE `employeeID` = $employeeOtherId AND `employeeKey` = 'wphrmDeductionfiledskey' AND `date`='$generateDate' AND `weekOn`= '$wphrmGenerateWeekNo'");
        if (!empty($wphrmDeductionfiledskey)) {
            $wphrmFormDeductionDetails = unserialize(base64_decode($wphrmDeductionfiledskey->employeeValue));
            $wphrmFormDeductionDetails['wphrmDeductionLebal'] = $this->WPHRMSanitize($wphrmDeductionLebal);
            $wphrmFormDeductionDetails['wphrmDeductionValue'] = $this->WPHRMSanitize($wphrmDeductionValue);
            $wphrmFormDeductionData = base64_encode(serialize($wphrmFormDeductionDetails));
            $wpdb->query("UPDATE $this->WphrmWeeklySalaryTable SET  `employeeValue`='$wphrmFormDeductionData' WHERE `employeeID`= $employeeOtherId AND `employeeKey`= 'wphrmDeductionfiledskey' AND `date`='$generateDate' AND `weekOn`= '$wphrmGenerateWeekNo'");
        } else {
            $wphrmFormDeductionDetails['wphrmDeductionLebal'] = $this->WPHRMSanitize($wphrmDeductionLebal);
            $wphrmFormDeductionDetails['wphrmDeductionValue'] = $this->WPHRMSanitize($wphrmDeductionValue);
            $wphrmFormDeductionData = base64_encode(serialize($wphrmFormDeductionDetails));
            $wphrmDeductionfiledskeyiNFORMATION = sanitize_text_field('wphrmDeductionfiledskey'); // sanitize_text_field
            $wpdb->query("INSERT INTO $this->WphrmWeeklySalaryTable (`employeeID`,`weekOn`, `employeeKey`, `employeeValue`, `date`)
                                               VALUES($employeeOtherId,$wphrmGenerateWeekNo ,'$wphrmDeductionfiledskeyiNFORMATION', '$wphrmFormDeductionData', '$generateDate')");
        }
        $wphrmTextInfo = $wpdb->get_row("SELECT * FROM $this->WphrmWeeklySalaryTable where `employeeID` = $employeeOtherId AND `employeeKey` = 'employeeSalaryNote' AND `date`='$generateDate' AND `weekOn`= '$wphrmGenerateWeekNo'");
        if (empty($wphrmTextInfo)) {
            $wphrmFormDetails['wphrm_information'] = sanitize_text_field($wphrmInformation);
            $wphrmFormDetails['wphrm_dayofworking'] = sanitize_text_field($wphrmDayOfWorking);
            $wphrmFormDetails['wphrm_workleave'] = sanitize_text_field($wphrmWorkleave);
            $wphrmFormDetails['wphrm_dayofworked'] = sanitize_text_field($wphrmDayOfWorked);
            $wphrmFormDetails['wphrm_Hours'] = sanitize_text_field($wphrmHours);
            $wphrmFormDetails['wphrm_account_no'] = sanitize_text_field($wphrmAccountNo);
            $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
            $wpdb->query("INSERT INTO $this->WphrmWeeklySalaryTable (`employeeID`,`weekOn`, `employeeKey`, `employeeValue`, `date`)
                                               VALUES($employeeOtherId,$wphrmGenerateWeekNo, 'employeeSalaryGenerated', 'generated', '$generateDate')");
            $id = $wpdb->query("INSERT INTO $this->WphrmWeeklySalaryTable (`employeeID`,`weekOn`, `employeeKey`, `employeeValue`, `date`)
                                               VALUES($employeeOtherId, $wphrmGenerateWeekNo,'employeeSalaryNote', '$wphrmFormDetailsData', '$generateDate')");
            $lastid = $wpdb->insert_id;
            $id = $wpdb->query("UPDATE $this->WphrmWeeklySalaryTable SET `date`='$generateDate' WHERE `id`= $lastid AND `employeeKey`='employeeSalaryNote' AND `weekOn`= '$wphrmGenerateWeekNo'");
            $result['success'] = 'Salary slip data successfully updated';
            $notification = array('wphrmUserID' => sanitize_text_field($employeeOtherId),
                                  'wphrmDesc' => sanitize_text_field('Your salary has been generated for week ' . $wphrmGenerateWeekNo . ' ' . date('F Y', strtotime($generateDate)) . '.'),
                                  'notificationType' => sanitize_text_field('Salary Generated'),
                                  'wphrmStatus' => sanitize_text_field('unseen'),
                                  'wphrmDate' => sanitize_text_field(date('Y-m-d')),
                                 );
            $wpdb->insert($this->WphrmNotificationsTable, $notification);
        } else {
            $wphrmGenerate = $wpdb->get_row("SELECT * FROM $this->WphrmWeeklySalaryTable WHERE `employeeID` = $employeeOtherId AND `employeeKey` = 'employeeSalaryGenerated' AND `date`='$generateDate' AND `weekOn`= '$wphrmGenerateWeekNo'");
            if (empty($wphrmGenerate)) {
                $wpdb->query("INSERT INTO $this->WphrmWeeklySalaryTable (`employeeID`,`weekOn`, `employeeKey`, `employeeValue`, `date`)
                                               VALUES($employeeOtherId,$wphrmGenerateWeekNo, 'employeeSalaryGenerated', 'generated', '$generateDate'");
            }
            $wphrmFormDetails = unserialize(base64_decode($wphrmTextInfo->employeeValue));
            $wphrmFormDetails['wphrm_information'] = sanitize_text_field($wphrmInformation);
            $wphrmFormDetails['wphrm_dayofworking'] = sanitize_text_field($wphrmDayOfWorking);
            $wphrmFormDetails['wphrm_workleave'] = sanitize_text_field($wphrmWorkleave);
            $wphrmFormDetails['wphrm_dayofworked'] = sanitize_text_field($wphrmDayOfWorked);
            $wphrmFormDetails['wphrm_Hours'] = sanitize_text_field($wphrmHours);
            $wphrmFormDetails['wphrm_account_no'] = sanitize_text_field($wphrmAccountNo);
            $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
            $id = $wpdb->query("UPDATE $this->WphrmWeeklySalaryTable SET `employeeValue`='$wphrmFormDetailsData' WHERE `employeeID`= $employeeOtherId AND `employeeKey` = 'employeeSalaryNote' AND `date`='$generateDate' AND `weekOn`= '$wphrmGenerateWeekNo'");
            $result['success'] = 'Salary slip data successfully updated';
        }
        echo json_encode($result);
        exit;
    }
    /** Messages details functions. * */
    public function WPHRMAllMessagesInfo() {
        global $wpdb;
        $result = array();
        $wphrmMessagesTitle = esc_sql($_POST['wphrm_messages_title']); // esc
        $wphrmMessagesDesc = esc_sql($_POST['wphrm_messages_desc']); // esc
        $wphrmMessagesId = esc_sql($_POST['wphrm_messages_id']); // esc
        $wphrmAllMessagesInfo = $wpdb->get_row("SELECT * FROM $this->WphrmMessagesTable WHERE `id` = '$wphrmMessagesId' ");
        if (!empty($wphrmAllMessagesInfo)) {
            $wphrmMessagesTitle = sanitize_text_field($wphrmMessagesTitle); // sanitize_text_field
            $wphrmMessagesDesc = sanitize_text_field($wphrmMessagesDesc); // sanitize_text_field
            $id = $wpdb->query("UPDATE $this->WphrmMessagesTable SET `messagesTitle` = '$wphrmMessagesTitle', `messagesDesc` ='$wphrmMessagesDesc' WHERE `id`= '$wphrmMessagesId'");
            $result['success'] = true;
        } else {
            $result['error'] = __('Something went wrong.', 'wphrm');
        }
        echo json_encode($result);
        exit;
    }
    /** Setting Function for Add Currency . * */
    public function WPHRMExpenseReportInfo() {
        global $wpdb;
        $message = array();
        $wphrmExpenseAmount = '';
        if ($_POST['wphrm_expense_amount'] != '') {
            $wphrmExpenseAmount = $_POST['wphrm_expense_amount'];
        }
        $wphrmExpenseReportSettings = $this->WPHRMGetSettings('wphrmExpenseReportInfo');
        if (!empty($wphrmExpenseReportSettings)) :
        $wphrmFormDetails = $wphrmExpenseReportSettings;
        $wphrmFormDetails['wphrm_expense_amount'] = sanitize_text_field($wphrmExpenseAmount);
        $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
        $id = $wpdb->query("UPDATE $this->WphrmSettingsTable SET `settingValue`= '$wphrmFormDetailsData' WHERE `settingKey`='wphrmExpenseReportInfo'");
        $message['success'] = true;
        else :
        $wphrmFormDetails['wphrm_expense_amount'] = sanitize_text_field($wphrmExpenseAmount);
        $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
        $id = $wpdb->query("INSERT INTO $this->WphrmSettingsTable (`settingKey`, `settingValue`)
                                               VALUES('wphrmExpenseReportInfo','$wphrmFormDetailsData')");
        if ($id) {
            $message['success'] = true;
        } else {
            $message['error'] = __('Something went wrong.', 'wphrm');
        }
        endif;
        echo json_encode($message);
        exit;
    }
    /** Remove Full Generated Salary Slip . * */
    public function WPHRMRemoveSalarySlip() {
        global $wpdb;
        $result = array();
        $employeeOtherId = esc_sql($_POST['wphrm_employeeOther_id']); // esc
        $generateYear = esc_sql($_POST['wphrm_generate_year']); // esc
        $generateMonth = $_POST['wphrm_generate_month'];
        $generateDate = $generateYear . '-' . $generateMonth . '-01';
        $generateDate = esc_sql(date('Y-m-d', strtotime($generateDate))); // esc
        if (!empty($employeeOtherId)) :
        $id = $wpdb->query("DELETE FROM `$this->WphrmSalaryTable` WHERE `employeeID` = $employeeOtherId  AND `date`='$generateDate'");
        if ($id) {
            $result['success'] = true;
        } else {
            $result['errors'] = $wphrmErrors;
        }
        endif;
        echo json_encode($result);
        exit;
    }
    /** Remove Full Generated Salary Week Slip . * */
    public function WPHRMRemoveSalaryWeekSlip() {
        global $wpdb;
        $result = array();
        $employeeOtherId = esc_sql($_POST['wphrm_employeeOther_id']); // esc
        $generateYear = esc_sql($_POST['wphrm_generate_year']); // esc
        $generateMonth = $_POST['wphrm_generate_month'];
        $wphrmCreateWeekSalaryWeekNo = $_POST['wphrmCreateWeekSalaryWeekNo'];
        $generateDate = $generateYear . '-' . $generateMonth . '-01';
        $generateDate = esc_sql(date('Y-m-d', strtotime($generateDate))); // esc
        if (!empty($employeeOtherId)) :
        $id = $wpdb->query("DELETE FROM `$this->WphrmWeeklySalaryTable` WHERE `employeeID` = $employeeOtherId  AND `date`='$generateDate' AND `weekOn`= '$wphrmCreateWeekSalaryWeekNo'");
        if ($id) {
            $result['success'] = true;
        } else {
            $result['errors'] = $wphrmErrors;
        }
        endif;
        echo json_encode($result);
        exit;
    }
    /** Setting Function for Add Currency . * */
    public function WPHRMGeneralSettingsInfo() {
        global $wpdb;
        $message = array();
        $wphrmCompanyFullName = '';
        $wphrmCompanyEmail = '';
        $wphrmCompanyPhone = '';
        $wphrmCompanyAddress = '';
        $wphrmCurrency = '';
        if (!empty($_FILES['wphrm_company_logo']['name'])) {
            $wphrmCompanyLogo = wp_check_filetype($_FILES['wphrm_company_logo']['name']);
            $wphrmCompanyLogos = $wphrmCompanyLogo['ext'];
            if ($wphrmCompanyLogos == 'PNG' || $wphrmCompanyLogos == 'JPG' || $wphrmCompanyLogos == 'JPEG' || $wphrmCompanyLogos == 'GIF' ||
                $wphrmCompanyLogos == 'png' || $wphrmCompanyLogos == 'jpg' || $wphrmCompanyLogos == 'jpeg' || $wphrmCompanyLogos == 'gif') {
                $wphrmCompanyLogosFile = $_FILES['wphrm_company_logo'];
                $wphrmCompanyLogosOverrides = array('test_form' => false);
                $wphrmCompanyLogosProfile = wp_handle_upload($wphrmCompanyLogosFile, $wphrmCompanyLogosOverrides);
            } else {
                $result['error'] = __('This is invalid file format.', 'wphrm');
                echo json_encode($result);
                exit;
            }
        }
        if ($_POST['wphrm_company_full_name'] != '') {
            $wphrmCompanyFullName = $_POST['wphrm_company_full_name'];
        }
        if ($_POST['wphrm_company_email'] != '') {
            $wphrmCompanyEmail = $_POST['wphrm_company_email'];
        }
        if ($_POST['wphrm_company_phone'] != '') {
            $wphrmCompanyPhone = $_POST['wphrm_company_phone'];
        }
        if ($_POST['wphrm_company_address'] != '') {
            $wphrmCompanyAddress = $_POST['wphrm_company_address'];
        }
        if ($_POST['wphrm_currency'] != '') {
            $wphrmCurrency = $_POST['wphrm_currency'];
        }
        $wphrm_general_settings = $this->WPHRMGetSettings('wphrmGeneralSettingsInfo');
        if (!empty($wphrm_general_settings)) :
        $wphrmFormDetails = $wphrm_general_settings;
        if (!empty($_FILES['wphrm_company_logo']['tmp_name'])) {
            $wphrmFormDetails['wphrm_company_logo'] = sanitize_text_field($wphrmCompanyLogosProfile['url']);
        } else {
            $wphrmFormDetails['wphrm_company_logo'] = sanitize_text_field($wphrmFormDetails['wphrm_company_logo']);
        }
        $wphrmFormDetails['wphrm_company_full_name'] = sanitize_text_field($wphrmCompanyFullName);
        $wphrmFormDetails['wphrm_company_email'] = sanitize_text_field($wphrmCompanyEmail);
        $wphrmFormDetails['wphrm_company_phone'] = sanitize_text_field($wphrmCompanyPhone);
        $wphrmFormDetails['wphrm_company_address'] = sanitize_text_field($wphrmCompanyAddress);
        $wphrmFormDetails['wphrm_currency'] = sanitize_text_field($wphrmCurrency);
        $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
        $id = $wpdb->query("UPDATE $this->WphrmSettingsTable SET `settingValue`= '$wphrmFormDetailsData' WHERE `settingKey`='wphrmGeneralSettingsInfo'");
        $message['success'] = true;
        else :
        $wphrmFormDetails['wphrm_company_logo'] = sanitize_text_field($wphrmCompanyLogosProfile['url']);
        $wphrmFormDetails['wphrm_company_full_name'] = sanitize_text_field($wphrmCompanyFullName);
        $wphrmFormDetails['wphrm_company_email'] = sanitize_text_field($wphrmCompanyEmail);
        $wphrmFormDetails['wphrm_company_phone'] = sanitize_text_field($wphrmCompanyPhone);
        $wphrmFormDetails['wphrm_company_address'] = sanitize_text_field($wphrmCompanyAddress);
        $wphrmFormDetails['wphrm_currency'] = sanitize_text_field($wphrmCurrency);
        $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
        $id = $wpdb->query("INSERT INTO $this->WphrmSettingsTable (`settingKey`, `settingValue`) VALUES('wphrmGeneralSettingsInfo','$wphrmFormDetailsData')");
        if ($id) {
            $message['success'] = true;
        } else {
            $message['error'] = __('Something went wrong.', 'wphrm');
        }
        endif;
        echo json_encode($message);
        exit;
    }

    /** from date to date array fuction * */
    function createDateRangeArray($strDateFrom, $strDateTo) {
        $aryRange = array();
        $iDateFrom = mktime(1, 0, 0, substr($strDateFrom, 5, 2), substr($strDateFrom, 8, 2), substr($strDateFrom, 0, 4));
        $iDateTo = mktime(1, 0, 0, substr($strDateTo, 5, 2), substr($strDateTo, 8, 2), substr($strDateTo, 0, 4));
        if ($iDateTo >= $iDateFrom) {
            array_push($aryRange, date('Y-m-d', $iDateFrom)); // first entry
            while ($iDateFrom < $iDateTo) {
                $iDateFrom+=86400; // add 24 hours
                array_push($aryRange, date('Y-m-d', $iDateFrom));
            }
        }
        return $aryRange;
    }
    /** Leave Application function. * */
    public function WPHRMLeaveApplicationsInfo() {
        global $current_user, $wpdb;
        $result = array();
        $wphrmLeaveApplicationId = '';
        $wphrmNotificationSettings = '';
        $wphrmapplicationLeavedate = '';
        $wphrmapplicationLeavedateTo = '';
        $wphrmApplicationStatus = '';
        $wphrmapplicationLeavetype = '';
        $applicationAppliedon = '';
        $wphrmApplicationReason = '';
        $wphrmApplication_comment = '';
        $wphrmEmployeeID = '';
        $appliedDate = array();
        $createUpdatedate = date('Y-m-d H:i:s');
        $createDate = date('Y-m-d');
        if (isset($_REQUEST['wphrm_leave_application_id']) && $_REQUEST['wphrm_leave_application_id'] != '') :
        $wphrmLeaveApplicationId = sanitize_text_field($_REQUEST['wphrm_leave_application_id']);
        endif;

        if ($_REQUEST['wphrm-from-leve-day-type'] != '') {
            $wphrmLeveFromDayType = $_REQUEST['wphrm-from-leve-day-type']; // esc
        }
        if ($_REQUEST['wphrm-to-leve-day-type'] != '') {
            $wphrmLeveToDayType = $_REQUEST['wphrm-to-leve-day-type']; // esc
        }

        if (isset($_REQUEST['applicationStatus']) && $_REQUEST['applicationStatus'] != '') :

        $wphrmApplicationStatus = sanitize_text_field($_REQUEST['applicationStatus']);
        endif;

        if (isset($_REQUEST['application_leavetype']) && $_REQUEST['application_leavetype'] != '') :
        $wphrmapplicationLeavetype = sanitize_text_field($_REQUEST['application_leavetype']);
        endif;

        if (isset($_REQUEST['application_reason']) && $_REQUEST['application_reason'] != '') :
        $wphrmApplicationReason = sanitize_text_field($_REQUEST['application_reason']);
        endif;
        if (isset($_REQUEST['application_comment']) && $_REQUEST['application_comment'] != '') :
        $wphrmApplication_comment = sanitize_text_field($_REQUEST['application_comment']);
        endif;

        if (isset($_REQUEST['application_appliedon']) && $_REQUEST['application_appliedon'] != '') :
        $applicationAppliedon = sanitize_text_field(date('Y-m-d', strtotime($_REQUEST['application_appliedon'])));
        endif;

        if (isset($_REQUEST['wphrm_employeeID']) && $_REQUEST['wphrm_employeeID'] != '') :
        $wphrmEmployeeID = sanitize_text_field($_REQUEST['wphrm_employeeID']);
        endif;

        if (isset($_REQUEST['application_leavedate']) && $_REQUEST['application_leavedate'] != '') :
        $wphrmapplicationLeavedate = sanitize_text_field(date('Y-m-d', strtotime($_REQUEST['application_leavedate'])));
        endif;
        if (isset($_REQUEST['application_leavedate_to']) && $_REQUEST['application_leavedate_to'] != '') :
        $wphrmapplicationLeavedateTo = sanitize_text_field(date('Y-m-d', strtotime($_REQUEST['application_leavedate_to'])));
        endif;

        $wphrmCurrentUserRole = implode(',', $current_user->roles);
        if (!empty($wphrmLeaveApplicationId)) {
            $wphrmapplicationLeavedateTo = empty($wphrmapplicationLeavedateTo) ? $wphrmapplicationLeavedate : $wphrmapplicationLeavedateTo;
            if ($wphrmapplicationLeavedate != '' && $wphrmapplicationLeavedateTo != '') {
                if (isset($wphrmApplicationStatus) && $wphrmApplicationStatus == 'approved') {
                    $dateBetweenArray = $this->createDateRangeArray(date('Y-m-d', strtotime($wphrmapplicationLeavedate)), date('Y-m-d', strtotime($wphrmapplicationLeavedateTo)));
                    $userGetApplications = $wpdb->get_results("SELECT * FROM $this->WphrmAttendanceTable WHERE `employeeID` = $wphrmEmployeeID");
                    $holidayArray = $wpdb->get_results("SELECT * FROM $this->WphrmHolidaysTable");

                    foreach ($holidayArray as $holidayDates) {
                        if (in_array($holidayDates->wphrmDate, $dateBetweenArray)) {
                            $allDates[] = $holidayDates->wphrmDate;
                        }
                    }

                    foreach ($userGetApplications as $userGetApplication) {
                        if (!in_array($userGetApplication->date, $allDates)) {
                            $appliedDate[] = $userGetApplication->date;
                        }
                    }

                    foreach ($dateBetweenArray as $dateBetweenDates) {

                        //check if the new application date is already on the attendance table and holiday list
                        if (!in_array($dateBetweenDates, $all_attendance_holiday)) {
                            $userGetDates = $wpdb->get_row("SELECT * FROM $this->WphrmAttendanceTable WHERE `employeeID` = $wphrmEmployeeID AND `date` = '$dateBetweenDates'");
                            $all_attendance_holiday = array_merge($allDates, $appliedDate);
                            if (empty($userGetDates)) {

                                //set the half day type
                                $halfday_type = array(0,0);
                                if($wphrmLeveFromDayType){
                                    $halfday_type[0] = 1;
                                }
                                if($wphrmLeveToDayType){
                                    $halfday_type[1] = 1;
                                }

                                $ires = $wpdb->insert(
                                    $this->WphrmAttendanceTable,
                                    array(
                                        'employeeID' => $wphrmEmployeeID,
                                        'date' => $dateBetweenDates,
                                        'toDate' => '0000-00-00',
                                        'halfDayType' => implode(',', $halfday_type),
                                        'status' => 'absent',
                                        'leaveType' => $wphrmapplicationLeavetype,
                                        'reason' => $wphrmApplicationReason,
                                        'adminComments' => $wphrmApplication_comment,
                                        'appliedOn' => $applicationAppliedon,
                                        'applicationStatus' => $wphrmApplicationStatus,
                                        'leaveApplicationId' => $wphrmLeaveApplicationId,
                                        'updatedBy' => $current_user->ID,
                                        'createdAt' => $createUpdatedate,
                                        'updatedAt' => $createUpdatedate,
                                    )
                                );

                            } else {
                                $wpdb->update(
                                    $this->WphrmAttendanceTable,
                                    array(
                                        'reason' => $wphrmApplicationReason,
                                        'adminComments' => $wphrmApplication_comment,
                                        'leaveType' => $wphrmapplicationLeavetype,
                                        'leaveApplicationId' => $wphrmLeaveApplicationId,
                                        'adminComments' => $wphrmApplication_comment,
                                        'updatedAt' => $createUpdatedate,
                                        'applicationStatus' => $wphrmApplicationStatus,
                                    ),
                                    array( 'id' => $userGetDates->id)
                                );
                            }
                        }
                    }
                    $res =  $wpdb->update(
                        $this->WphrmLeaveApplicationTable,
                        array(
                            'adminComments' => $wphrmApplication_comment,
                            'reason' => $wphrmApplicationReason,
                            'applicationStatus' => $wphrmApplicationStatus,
                        ),
                        array( 'id' => $wphrmLeaveApplicationId)
                    );

                    $wphrmEmployeeBasicInfo = $this->WPHRMGetUserDatas($wphrmEmployeeID, 'wphrmEmployeeInfo');
                    $employee_name = isset($wphrmEmployeeBasicInfo['wphrm_employee_fname']) ? $wphrmEmployeeBasicInfo['wphrm_employee_fname'] : '';
                    $employee_name .= isset($wphrmEmployeeBasicInfo['wphrm_employee_lname']) ? ' '.$wphrmEmployeeBasicInfo['wphrm_employee_lname'] : '';
                    $this->WPHRMSendEmail2(
                        $wphrmEmployeeID,
                        'employee_leave_approved',
                        array(
                            'replacement' => array(
                                'employee_name' => $employee_name,
                                'leave_date_from' => date('d F Y', strtotime($wphrmapplicationLeavedate)),
                                'leave_date_to' => date('d F Y', strtotime($wphrmapplicationLeavedateTo))
                            )
                        )
                    );

                    //Admin/HR/Officer notification
                    $hr_admin_officer = $this->WphrmGetAdminId();
                    //let add the approving officer
                    if(!empty($wphrmEmployeeBasicInfo['wphrm_employee_reporting_manager']) && is_array($wphrmEmployeeBasicInfo['wphrm_employee_reporting_manager'])){
                        $hr_admin_officer = array_merge($hr_admin_officer, $wphrmEmployeeBasicInfo['wphrm_employee_reporting_manager']);
                    }

                    $this->WPHRMSendEmail2(
                        $hr_admin_officer,
                        'hr_leave_application_approved',
                        array(
                            'replacement' => array(
                                'employee_name' => $employee_name,
                                'leave_date_from' => date('d F Y', strtotime($wphrmapplicationLeavedate)),
                                'leave_date_to' => date('d F Y', strtotime($wphrmapplicationLeavedateTo))
                            )
                        )
                    );
                } else if (isset($wphrmApplicationStatus) && $wphrmApplicationStatus == 'rejected') {
                    $wpdb->update(
                        $this->WphrmLeaveApplicationTable,
                        array(
                            'adminComments' => $wphrmApplication_comment,
                            'reason' => $wphrmApplicationReason,
                            'applicationStatus' => $wphrmApplicationStatus,
                        ),
                        array( 'id' => $wphrmLeaveApplicationId)
                    );
                    $this->WPHRMSendEmail2(
                        $wphrmEmployeeID,
                        'employee_leave_rejected',
                        array(
                            'replacement' => array(
                                'employee_name' => $employee_name,
                                'leave_date_from' => date('d F Y', strtotime($wphrmapplicationLeavedate)),
                                'leave_date_to' => date('d F Y', strtotime($wphrmapplicationLeavedateTo))
                            )
                        )
                    );
                }
            } else {
                $wpdb->update(
                    $this->WphrmLeaveApplicationTable,
                    array(
                        'adminComments' => $wphrmApplication_comment,
                        'applicationStatus' => $wphrmApplicationStatus,
                    ),
                    array( 'id' => $wphrmLeaveApplicationId)
                );
                $result['custom_message'] = 'No leave dates';
            }
            $wphrmNotificationSettings = $wpdb->get_row("SELECT * FROM  $this->WphrmSettingsTable WHERE `settingKey` = 'wphrmNotificationsSettingsInfo'");
            if (!empty($wphrmNotificationSettings)) {
                $wphrmNotificationSetting = unserialize(base64_decode($wphrmNotificationSettings->settingValue));
                if ($wphrmNotificationSetting['wphrm_leave_notification'] == 1) {
                    $notification = array(
                        'wphrmUserID' => sanitize_text_field($wphrmEmployeeID),
                        'wphrmFromId' => $current_user->ID,
                        'wphrmDesc' => __('Your leave application has been ', 'wphrm') . sanitize_text_field($wphrmApplicationStatus),
                        'notificationType' => 'Leave ' . sanitize_text_field($wphrmApplicationStatus),
                        'wphrmStatus' => sanitize_text_field('unseen'),
                        'wphrmDate' => sanitize_text_field(date('Y-m-d')),
                    );
                    $wpdb->insert($this->WphrmNotificationsTable, $notification);
                    $result['success'] = true;
                }
            }
        }else{
            $result['custom_message'] = 'No application ID set';
        }

        echo json_encode($result);
        exit;
    }
    /*Add/edit new leave application*/
    public function WPHRMUserLeaveApplicationsInfo() {
        global $wpdb;
        $message = array();
        $wphrmEmployeeID = '';
        $wphrmStatus = '';
        $wphrmApplicationStatus = '';
        $wphrmLeavetype = '';
        $wphrmLeavedate = '';
        $wphrmLeavedateTo = '';
        $wphrmReason = '';
        $attendanceID = '';
        $wphrmFromLeveDayType = '';
        $wphrmToLeveDayType = '';
        $appliedOn = date('Y-m-d');
        $notification = '';
        $appliedDate = array();
        $createUpdatedate = date('Y-m-d H:i:s');
        if ($_POST['wphrm_attendanceID'] != '') {
            $attendanceID = esc_sql($_POST['wphrm_attendanceID']); // esc
        }
        if ($_POST['wphrm-from-leve-day'] == '1' && $_POST['wphrm-to-leve-day'] == '0') {
            $wphrmLeveDayType = '1,0';  // esc
        }
        if ($_POST['wphrm-to-leve-day'] == '1' && $_POST['wphrm-from-leve-day'] == '0') {
            $wphrmLeveDayType = '0,1'; // esc
        }
        if ($_POST['wphrm-to-leve-day'] == '1' && $_POST['wphrm-from-leve-day'] == '1') {
            $wphrmLeveDayType = '1,1'; // esc
        }
        if ($_POST['wphrm-to-leve-day'] == '0' && $_POST['wphrm-from-leve-day'] == '0') {
            $wphrmLeveDayType = '0,0'; // esc
        }
        if ($_POST['wphrm_employeeID'] != '') {
            $wphrmEmployeeID = esc_sql($_POST['wphrm_employeeID']); // esc
        }
        if ($_POST['wphrm_status'] != '') {
            $wphrmStatus = esc_sql($_POST['wphrm_status']); // esc
        }
        
        if( $_POST['wphrm_leavetype'] == 62 || $_POST['wphrm_leavetype'] == 37 || $_POST['wphrm_leavetype'] == 38 || $_POST['wphrm_leavetype'] == 39 || $_POST['wphrm_leavetype'] == 61 || $_POST['wphrm_leavetype'] == 40 || $_POST['wphrm_leavetype'] == 41 || $_POST['wphrm_leavetype'] == 42 || $_POST['wphrm_leavetype'] == 43 ) {
            $wphrmApplicationStatus = 'approved';
        } else {
           if ($_POST['wphrm_application_status'] != '') {
                $wphrmApplicationStatus = esc_sql($_POST['wphrm_application_status']); // esc
            } 
        }
        
        if ($_POST['wphrm_leavetype'] != '') {
            $wphrmLeavetype = esc_sql($_POST['wphrm_leavetype']); // esc
        }
        if ($_POST['wphrm_leavedate'] != '') {
            $wphrmLeavedate = esc_sql(date('Y-m-d', strtotime($_POST['wphrm_leavedate']))); // esc
        }
        if ($_POST['wphrm_leavedate_to'] != '') {
            $wphrmLeavedateTo = esc_sql(date('Y-m-d', strtotime($_POST['wphrm_leavedate_to']))); // esc
        }
        if ($_POST['wphrm_reason'] != '') {
            $wphrmReason = esc_sql($_POST['wphrm_reason']); // esc
        }
        
        if ($_POST['wphrm_due_date'] != '') {
            $wphrmDuedate = esc_sql(date('Y-m-d', strtotime($_POST['wphrm_due_date']))); // esc
        }
        if ($_POST['wphrm_child_bdate'] != '') {
            $wphrmChildbdate = esc_sql(date('Y-m-d', strtotime($_POST['wphrm_child_bdate']))); // esc
        }
        if ($_POST['wphrm_work_date'] != '') {
            $wphrmWorkdate = esc_sql(date('Y-m-d', strtotime($_POST['wphrm_work_date']))); // esc
        }
        if ($_POST['wphrm_outstation_date'] != '') {
            $wphrmOutstationdate = esc_sql(date('Y-m-d', strtotime($_POST['wphrm_outstation_date']))); // esc
        }
        if ($_POST['wphrm_assign_country'] != '') {
            $wphrmAssigncountry = esc_sql($_POST['wphrm_assign_country']); // esc
        }
        if ($_POST['wphrm_examination_name'] != '') {
            $wphrmExamname = esc_sql($_POST['wphrm_examination_name']); // esc
        }
        if ($_POST['wphrm_examination_date'] != '') {
            $wphrmExamdate = esc_sql(date('Y-m-d', strtotime($_POST['wphrm_examination_date']))); // esc
        }
        if ($_POST['wphrm_examination_time'] != '') {
            $wphrmExamtime = esc_sql($_POST['wphrm_examination_time']); // esc
        }
        
        $wphrmMedicalClaim = '';
        if ($_POST['medical_claim'] != '') {
            $wphrmMedicalClaim = esc_sql($_POST['medical_claim']); // esc
        }
        $wphrmElderlyScreening = '';
        if ($_POST['elderly_screening'] != '') {
            $wphrmElderlyScreening = esc_sql($_POST['elderly_screening']); // esc
        }
        $wphrmEmployeeInfo = $this->WPHRMGetUserDatas($wphrmEmployeeID, 'wphrmEmployeeInfo');
        $userLeaveApplications = $wpdb->get_row("SELECT * FROM $this->WphrmLeaveApplicationTable WHERE `id` = '$attendanceID'");
        //update
        if (!empty($userLeaveApplications)) {
            $wpdb->update(
                $this->WphrmLeaveApplicationTable,
                array(
                    'halfDayType' => $wphrmLeveDayType,
                    'date' => $wphrmLeavedate,
                    'toDate' => $wphrmLeavedateTo,
                    'leaveType' => $wphrmLeavetype,
                    'reason' => $wphrmReason,
                    'medical_claim_amount' => $wphrmMedicalClaim,
                    'elderly_screening_amount' => $wphrmElderlyScreening,
                    'updatedAt' => $createUpdatedate,
                    'due_date' => $wphrmDuedate,
                    'child_bdate' => $wphrmChildbdate,
                    'work_date' => $wphrmWorkdate,
                    'outstation_date' => $wphrmOutstationdate,
                    'assign_country' => $wphrmAssigncountry,
                    'examination_name' => $wphrmExamname,
                    'examination_date' => $wphrmExamdate,
                    'examination_time' => $wphrmExamtime,
                ),
                array('id' => $attendanceID)
            );
            $message['success'] = true;
            echo json_encode($message);
            exit;
        } else { //add
            $last_attendance_id = 0;
            $userGetApplications = $wpdb->get_results("SELECT * FROM $this->WphrmLeaveApplicationTable WHERE `employeeID` = $wphrmEmployeeID AND `date` != '$appliedOn'");
            foreach ($userGetApplications as $userGetApplication) {
                if ($wphrmLeavedate == $userGetApplication->date) {
                    $appliedDate[] = $userGetApplication->date;
                }
            }
            if (empty($appliedDate)) {
                $userGetApplications = $wpdb->get_row("SELECT * FROM $this->WphrmLeaveApplicationTable WHERE `employeeID` = $wphrmEmployeeID AND `date` = '$appliedOn'");

                if (!empty($userGetApplications)) {
                    $wpdb->delete(
                        $this->WphrmLeaveApplicationTable,
                        array('id' => $userGetApplications->id)
                    );
                }

                $id = $wpdb->insert(
                    $this->WphrmLeaveApplicationTable,
                    array(
                        'employeeID' => sanitize_text_field($wphrmEmployeeID),
                        'date' => sanitize_text_field($wphrmLeavedate),
                        'toDate' => sanitize_text_field($wphrmLeavedateTo),
                        'halfDayType' => sanitize_text_field($wphrmLeveDayType),
                        'leaveType' => sanitize_text_field($wphrmLeavetype),
                        'reason' => sanitize_text_field($wphrmReason),
                        'medical_claim_amount' => $wphrmMedicalClaim,
                        'elderly_screening_amount' => $wphrmElderlyScreening,
                        'appliedOn' => sanitize_text_field($appliedOn),
                        'applicationStatus' => sanitize_text_field($wphrmApplicationStatus),
                        'createdAt' => sanitize_text_field($createUpdatedate),
                        'updatedAt' => sanitize_text_field($createUpdatedate),
                        'due_date' => sanitize_text_field($wphrmDuedate),
                        'child_bdate' => sanitize_text_field($wphrmChildbdate),
                        'work_date' => sanitize_text_field($wphrmWorkdate),
                        'outstation_date' => sanitize_text_field($wphrmOutstationdate),
                        'assign_country' => sanitize_text_field($wphrmAssigncountry),
                        'examination_name' => sanitize_text_field($wphrmExamname),
                        'examination_date' => sanitize_text_field($wphrmExamdate),
                        'examination_time' => sanitize_text_field($wphrmExamtime),
                    )
                );

                $last_attendance_id = $wpdb->insert_id;
                $wphrmNotificationSetting = $this->WPHRMGetSettings('wphrmNotificationsSettingsInfo');
                if ($wphrmNotificationSetting['wphrm_leave_notification'] == 1) {

                    $wphrmLeaveMonth = date('d F Y', strtotime($wphrmLeavedate));
                    $wphrmLeavedateTos = '';
                    if (strtotime($wphrmLeaveMonth) != strtotime($wphrmLeavedateTo)) {
                        if ($wphrmLeavedateTo != '') {
                            $wphrmLeavedateTos = ' to ' . date('d F Y', strtotime($wphrmLeavedateTo) . '.');
                        }
                    }

                    //$this->WPHRMSendEmail($wphrmEmployeeID, 'applied', array('from' => $wphrmLeaveMonth, 'to' => date('d F Y', strtotime($wphrmLeavedateTo))));
                    $employee_name = (isset($wphrmEmployeeInfo['wphrm_employee_fname']) ? $wphrmEmployeeInfo['wphrm_employee_fname'] : '') . '' .
                        (isset($wphrmEmployeeInfo['wphrm_employee_lname']) ? $wphrmEmployeeInfo['wphrm_employee_lname'] : '');

                    //Admin/HR/Officer notification
                    $hr_admin_officer = $this->WphrmGetAdminId();
                    //let add the approving officer
                    $wphrmEmployeeBasicInfo = $this->WPHRMGetUserDatas($wphrmEmployeeID, 'wphrmEmployeeInfo');
                    if(!empty($wphrmEmployeeBasicInfo['wphrm_employee_reporting_manager']) && is_array($wphrmEmployeeBasicInfo['wphrm_employee_reporting_manager'])){
                        $hr_admin_officer = array_merge($hr_admin_officer, $wphrmEmployeeBasicInfo['wphrm_employee_reporting_manager']);
                    }

                    $this->WPHRMSendEmail2(
                        $hr_admin_officer,
                        'hr_leave_application',
                        array(
                            'replacement' => array(
                                'employee_name' => $employee_name,
                                'leave_date_from' => $wphrmLeaveMonth,
                                'leave_date_to' => date('d F Y', strtotime($wphrmLeavedateTo))
                            )
                        )
                    );

                    foreach($hr_admin_officer as $hr_admin_id){
                        $notification = array(
                            'wphrmUserID' => sanitize_text_field($hr_admin_id),
                            'wphrmDesc' => sanitize_text_field($wphrmEmployeeInfo['wphrm_employee_fname'] . ' ' . $wphrmEmployeeInfo['wphrm_employee_lname'] . ' has requested a leave for ' . $wphrmLeaveMonth . $wphrmLeavedateTos . '.'),
                            'notificationType' => sanitize_text_field('Leave Request'),
                            'wphrmStatus' => sanitize_text_field('unseen'),
                            'wphrmDate' => sanitize_text_field( date('Y-m-d') ),
                            'wphrmDate' => sanitize_text_field( date('Y-m-d') ),
                        );
                        $wpdb->insert($this->WphrmNotificationsTable, $notification);
                    }
                }
                /*J@F*/
                if ($last_attendance_id) {
                    global $current_user;
                    get_currentuserinfo();
                    $upload_dir = wp_upload_dir();
                    $user_dirname = $upload_dir['basedir'].'/jaf-wphrm/'.$current_user->user_login;
                    if ( ! file_exists( $user_dirname ) ) {
                        wp_mkdir_p( $user_dirname );
                    }
                    $source = $_FILES['attendance-document']['tmp_name'];
                    $file_name = current_time('timestamp') . '__' . $_FILES['attendance-document']['name'];
                    $file_url = $upload_dir['baseurl'] .'/jaf-wphrm/'.$current_user->user_login .'/'. $file_name;
                    $destination = trailingslashit( $user_dirname ) . $file_name;
                    $upload_result = move_uploaded_file( $source, $destination );
                    if($upload_result){
                        $wpdb->insert(
                            $this->WphrmAttendanceFileTable,
                            array(
                                'attendance_id' => $last_attendance_id,
                                'file_name' => sanitize_text_field($file_name),
                                'file_path' => $destination,
                                'file_url' => $file_url,
                            )
                        );
                    }
                }
                /*J@F END*/


                if ($id) {
                    $message['success'] = true;
                } else {
                    $message['error'] = __('Something went wrong.', 'wphrm');
                }
            } else {
                $message['error'] = __(' Leave has already been applied for this date.', 'wphrm');
            }
        }
        $message['post_data'] = $_POST;
        $message['file_data'] = $_FILES;
        echo json_encode($message);
        exit;
    }
    /** Setting Function for salary slip . * */
    public function WPHRMSalarySlipInfo() {
        global $wpdb;
        $message = array();
        $wphrmLogoAlign = '';
        $wphrmSlipContent = '';
        $wphrmFooterContentAlign = '';
        $wphrmBorderColor = '';
        $wphrmBackgroundColor = '';
        $wphrmFontColor = '';
        $wphrmCurrencyDecimal = '';
        if ($_POST['wphrm_logo_align'] != '') {
            $wphrmLogoAlign = $_POST['wphrm_logo_align'];
        }
        if (isset($_POST['wphrm_slip_content']) && $_POST['wphrm_slip_content'] != '') {
            $wphrmSlipContent = $_POST['wphrm_slip_content'];
        }
        if ($_POST['wphrm_footer_content_align'] != '') {
            $wphrmFooterContentAlign = $_POST['wphrm_footer_content_align'];
        }
        if ($_POST['wphrm_border_color'] != '') {
            $wphrmBorderColor = $_POST['wphrm_border_color'];
        }
        if ($_POST['wphrm_background_color'] != '') {
            $wphrmBackgroundColor = $_POST['wphrm_background_color'];
        }
        if ($_POST['wphrm_font_color'] != '') {
            $wphrmFontColor = $_POST['wphrm_font_color'];
        }
        if ($_POST['wphrm_currency_decimal'] != '') {
            $wphrmCurrencyDecimal = $_POST['wphrm_currency_decimal'];
        }
        $wphrm_salary_slip = $this->WPHRMGetSettings('wphrmSalarySlipInfo');
        if (!empty($wphrm_salary_slip)) :
        $wphrmFormDetails = $wphrm_salary_slip;
        $wphrmFormDetails['wphrm_logo_align'] = sanitize_text_field($wphrmLogoAlign);
        $wphrmFormDetails['wphrm_slip_content'] = sanitize_text_field($wphrmSlipContent);
        $wphrmFormDetails['wphrm_footer_content_align'] = sanitize_text_field($wphrmFooterContentAlign);
        $wphrmFormDetails['wphrm_border_color'] = '#' . sanitize_text_field($wphrmBorderColor);
        $wphrmFormDetails['wphrm_background_color'] = '#' . sanitize_text_field($wphrmBackgroundColor);
        $wphrmFormDetails['wphrm_font_color'] = '#' . sanitize_text_field($wphrmFontColor);
        $wphrmFormDetails['wphrm_currency_decimal'] = sanitize_text_field($wphrmCurrencyDecimal);
        $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
        $id = $wpdb->query("UPDATE $this->WphrmSettingsTable SET `settingValue`= '$wphrmFormDetailsData' WHERE `settingKey`='wphrmSalarySlipInfo'");
        $_SESSION['wphrm-salary-slip-msg'] = __('Updated successfully..', 'wphrm');
        $message['success'] = true;
        else :
        $wphrmFormDetails['wphrm_logo_align'] = sanitize_text_field($wphrmLogoAlign);
        $wphrmFormDetails['wphrm_slip_content'] = sanitize_text_field($wphrmSlipContent);
        $wphrmFormDetails['wphrm_footer_content_align'] = sanitize_text_field($wphrmFooterContentAlign);
        $wphrmFormDetails['wphrm_border_color'] = '#' . sanitize_text_field($wphrmBorderColor);
        $wphrmFormDetails['wphrm_background_color'] = '#' . sanitize_text_field($wphrmBackgroundColor);
        $wphrmFormDetails['wphrm_font_color'] = '#' . sanitize_text_field($wphrmFontColor);
        $wphrmFormDetails['wphrm_currency_decimal'] = sanitize_text_field($wphrmCurrencyDecimal);
        $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
        $id = $wpdb->query("INSERT INTO $this->WphrmSettingsTable (`settingKey`, `settingValue`)
                                               VALUES('wphrmSalarySlipInfo','$wphrmFormDetailsData')");
        if ($id) {
            $_SESSION['wphrm-salary-slip-msg'] = __('Registered successfully..', 'wphrm');
            $message['success'] = true;
        } else {
            $message['error'] = __('Something went wrong.', 'wphrm');
        }
        endif;
        echo json_encode($message);
        exit;
    }

    /** Setting Function for all notifications . * */
    public function WPHRMNotificationsSettingsInfo() {
        global $wpdb;
        $message = array();
        $wphrmNoticeNotification = '';
        $wphrmLeaveNotification = '';
        if (isset($_POST['wphrm_notice_notification']) && $_POST['wphrm_notice_notification'] != '') {
            $wphrmNoticeNotification = $_POST['wphrm_notice_notification'];
        }
        if (isset($_POST['wphrm_leave_notification']) && $_POST['wphrm_leave_notification'] != '') {
            $wphrmLeaveNotification = $_POST['wphrm_leave_notification'];
        }

        $wphrm_notifications_settings = $this->WPHRMGetSettings('wphrmNotificationsSettingsInfo');
        if (!empty($wphrm_notifications_settings)) :
        $wphrmFormDetails = $wphrm_notifications_settings;
        $wphrmFormDetails['wphrm_notice_notification'] = sanitize_text_field($wphrmNoticeNotification);
        $wphrmFormDetails['wphrm_leave_notification'] = sanitize_text_field($wphrmLeaveNotification);
        $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
        $id = $wpdb->query("UPDATE $this->WphrmSettingsTable SET `settingValue`= '$wphrmFormDetailsData' WHERE `settingKey`='wphrmNotificationsSettingsInfo'");
        $message['success'] = true;
        else :
        $wphrmFormDetails['wphrm_notice_notification'] = sanitize_text_field($wphrmNoticeNotification);
        $wphrmFormDetails['wphrm_leave_notification'] = sanitize_text_field($wphrmLeaveNotification);
        $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
        $id = $wpdb->query("INSERT INTO $this->WphrmSettingsTable (`settingKey`, `settingValue`)
                                               VALUES('wphrmNotificationsSettingsInfo', '$wphrmFormDetailsData')");
        if ($id) {
            $message['success'] = true;
        } else {
            $message['error'] = __('Something went wrong.', 'wphrm');
        }
        endif;
        echo json_encode($message);
        exit;
    }

    /** Hide/Show Employee Information Sections settings * */
    public function WPHRMHideShowEmployeeSectionInfo() {
        global $wpdb;
        $message = array();
        $wphrmBankAccountDetails = '';
        $wphrmSalaryDetails = '';
        $wphrmDocumentsDetails = '';
        $wphrmLeaveNotification = '';

        if (isset($_POST['bank-checked-name']) && $_POST['bank-checked-name'] != '') {
            $wphrmBankAccountDetails = $_POST['bank-checked-name'];
        }
        if (isset($_POST['salary-checked-name']) && $_POST['salary-checked-name'] != '') {
            $wphrmSalaryDetails = $_POST['salary-checked-name'];
        }
        if (isset($_POST['documents-checked-name']) && $_POST['documents-checked-name'] != '') {
            $wphrmDocumentsDetails = $_POST['documents-checked-name'];
        }
        if (isset($_POST['other-checked-name']) && $_POST['other-checked-name'] != '') {
            $wphrmOtherDetails = $_POST['other-checked-name'];
        }

        $wphrmHideShowEmployeeSectionSettings = $this->WPHRMGetSettings('WPHRMHideShowEmployeeSectionInfo');
        if (!empty($wphrmHideShowEmployeeSectionSettings)) :
        $wphrmFormDetails['bank-account-details'] = sanitize_text_field($wphrmBankAccountDetails);
        $wphrmFormDetails['salary-details'] = sanitize_text_field($wphrmSalaryDetails);
        $wphrmFormDetails['documents-details'] = sanitize_text_field($wphrmDocumentsDetails);
        $wphrmFormDetails['other-details'] = sanitize_text_field($wphrmOtherDetails);
        $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
        $id = $wpdb->query("UPDATE $this->WphrmSettingsTable SET `settingValue`= '$wphrmFormDetailsData' WHERE `settingKey`='WPHRMHideShowEmployeeSectionInfo'");
        $message['success'] = __('Employee section settings have been successfully Updated.', 'wphrm');
        ;
        else :
        $wphrmFormDetails['bank-account-details'] = sanitize_text_field($wphrmBankAccountDetails);
        $wphrmFormDetails['salary-details'] = sanitize_text_field($wphrmSalaryDetails);
        $wphrmFormDetails['documents-details'] = sanitize_text_field($wphrmDocumentsDetails);
        $wphrmFormDetails['other-details'] = sanitize_text_field($wphrmOtherDetails);
        $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
        $id = $wpdb->query("INSERT INTO $this->WphrmSettingsTable (`settingKey`, `settingValue`)
                                               VALUES('WPHRMHideShowEmployeeSectionInfo', '$wphrmFormDetailsData')");
        if ($id) {
            $message['success'] = __('Employee section settings have been successfully Added.', 'wphrm');
        } else {
            $message['error'] = __('Something went wrong.', 'wphrm');
        }
        endif;
        echo json_encode($message);
        exit;
    }

    /** Setting Function for all notifications . * */
    public function WPHRMFinancialsInfo() {
        global $wpdb;
        $message = array();
        $wphrmTableName = $this->WphrmFinancialsTable;
        $wphrmFinancialsId = '';
        $wphrmItem = '';
        $wphrmAmount = '';
        $wphrmStatus = '';
        $wphrmFinancialsDate = '';
        if (isset($_POST['finacials_id']) && $_POST['finacials_id'] != '') {
            $wphrmFinancialsId = esc_sql($_POST['finacials_id']); // esc
        }
        if (isset($_POST['wphrm-item']) && $_POST['wphrm-item'] != '') {
            $wphrmItem = esc_sql($_POST['wphrm-item']); // esc
        }
        if (isset($_POST['wphrm-amount']) && $_POST['wphrm-amount'] != '') {
            $wphrmAmount = esc_sql($_POST['wphrm-amount']); // esc
        }
        if (isset($_POST['wphrm-status']) && $_POST['wphrm-status'] != '') {
            $wphrmStatus = esc_sql($_POST['wphrm-status']); // esc
        }
        if (isset($_POST['wphrm-financials-date']) && $_POST['wphrm-financials-date'] != '') {
            $wphrmFinancialsDate = esc_sql($_POST['wphrm-financials-date']); // esc
        }
        $dateFinal = esc_sql(date('Y-m-d', strtotime($wphrmFinancialsDate))); // esc
        $wphrmFinancials = $wpdb->get_row("SELECT * FROM $this->WphrmFinancialsTable WHERE `id` = '$wphrmFinancialsId'");
        if (!empty($wphrmFinancials)) :
        $whereArray = array('id' => $wphrmFinancialsId);
        $updateFinancialsArray = array('wphrmItem' => sanitize_text_field($wphrmItem),
                                       'wphrmAmounts' => sanitize_text_field($wphrmAmount),
                                       'wphrmStatus' => sanitize_text_field($wphrmStatus),
                                       'wphrmDate' => sanitize_text_field($dateFinal)
                                      );
        $id = $wpdb->update($this->WphrmFinancialsTable, $updateFinancialsArray, $whereArray);
        $message['success'] = true;
        else :
        $addFinancialsArray = array('wphrmItem' => sanitize_text_field($wphrmItem),
                                    'wphrmAmounts' => sanitize_text_field($wphrmAmount),
                                    'wphrmStatus' => sanitize_text_field($wphrmStatus),
                                    'wphrmDate' => sanitize_text_field($dateFinal)
                                   );
        $id = $wpdb->insert($this->WphrmFinancialsTable, $addFinancialsArray);
        if ($id) {
            $message['success'] = true;
        } else {
            $message['error'] = __('Something went wrong.', 'wphrm');
        }
        endif;
        echo json_encode($message);
        exit();
    }
    /** Setting Function for all notifications . * */
    public function WPHRMNoticeInfo() {
        global $wpdb, $current_user;
        $message = array();
        $wphrmNoticeTitle = '';
        $wphrmNoticeDesc = '';
        $wphrmNoticeId = '';
        $wphrmNotificationSetting = $this->WPHRMGetSettings('wphrmNotificationsSettingsInfo');
        $notification = '';
        if (isset($_POST['wphrm_notice_title']) && $_POST['wphrm_notice_title'] != '') {
            $wphrmNoticeTitle = esc_sql($_POST['wphrm_notice_title']); // esc
        }
        if (isset($_POST['wphrm_notice_desc']) && $_POST['wphrm_notice_desc'] != '') {
            $wphrmNoticeDesc = ($_POST['wphrm_notice_desc']); // esc
        }
        if (isset($_POST['wphrm_notice_id']) && $_POST['wphrm_notice_id'] != '') {
            $wphrmNoticeId = esc_sql($_POST['wphrm_notice_id']); // esc
        }

        /*J@F*/
        if (isset($_POST['wphrm_notice_date']) && $_POST['wphrm_notice_date'] != '') {
            $wphrmNoticeDate = esc_sql($_POST['wphrm_notice_date']); // esc
            $wphrmNoticeDate = date('Y-m-d', strtotime($wphrmNoticeDate));
        }
        $wphrmNoticeDepartment = 0;
        if (isset($_POST['wphrm_notice_department']) && $_POST['wphrm_notice_department'] != '') {
            $wphrmNoticeDepartment = esc_sql($_POST['wphrm_notice_department']); // esc
        }
        if (isset($_POST['wphrm_invitation_notice']) && $_POST['wphrm_invitation_notice'] != '') {
            $wphrmInvitationNotice = isset($_POST['wphrm_invitation_notice']) ? esc_sql($_POST['wphrm_invitation_notice']) : ''; // esc
        }
        if (!empty($_POST['wphrm_invitation_recipient'])) {
            $wphrmInvitationRecipient_array = $_POST['wphrm_invitation_recipient']; // esc
            $wphrmInvitationRecipient = implode(',', $wphrmInvitationRecipient_array); // esc
        }
        /*J@F END*/

        $wphrmcreatedDate = esc_sql(date('Y-m-d H:i:s')); // esc
        $wphrmNotice = $wpdb->get_row("SELECT * FROM  $this->WphrmNoticeTable where id = '$wphrmNoticeId'");
        if (!empty($wphrmNotice)){

            $wphrmNoticeTitle = sanitize_text_field($wphrmNoticeTitle); // sanitize_text_field
            $wphrmNoticeDesc = sanitize_post($wphrmNoticeDesc, 'edit'); // sanitize_text_field
            $wpdb->update(
                $this->WphrmNoticeTable,
                array(
                    'wphrmtitle' => $wphrmNoticeTitle,
                    'wphrmdesc' => $wphrmNoticeDesc,
                    'wphrmdate' => $wphrmNoticeDate,
                    'wphrmdepartment' => $wphrmNoticeDepartment,
                    'wphrminvitationnotice' => $wphrmInvitationNotice,
                    'wphrminvitationsender' => $current_user->ID,
                    'wphrminvitationrecipient' => $wphrmInvitationRecipient,
                ),
                array(
                    'id' => $wphrmNoticeId
                )
            );
            $message['success'] = true;
            $message['do'] = 'update';
        }else{
            $wphrmNoticeTitle = sanitize_text_field($wphrmNoticeTitle); // sanitize_text_field
            $wphrmNoticeDesc = sanitize_post($wphrmNoticeDesc); // sanitize_text_field
            $id = $wpdb->query("INSERT INTO $this->WphrmNoticeTable (`wphrmtitle`, `wphrmdesc`, `wphrmcreatedDate`, `wphrmdate`, `wphrmdepartment`, `wphrminvitationnotice`, `wphrminvitationsender`, `wphrminvitationrecipient` )
                                               VALUES('$wphrmNoticeTitle', '$wphrmNoticeDesc', '$wphrmcreatedDate', '$wphrmNoticeDate', '$wphrmNoticeDepartment', '$wphrmInvitationNotice', '$current_user->ID', '$wphrmInvitationRecipient' )");

            if ($id) {
                $message['success'] = true;
            } else {
                $message['error'] = __('Something went wrong.', 'wphrm');
            }

            //this will create notification to the users
            if ($wphrmNotificationSetting['wphrm_notice_notification'] == 1) {
                $wphrmUsers = $this->WPHRMGetEmployees();
                foreach ($wphrmUsers as $key => $userdata) {
                    //user under the department and selected can get the notification
                    if( !(!empty($wphrmInvitationRecipient_array) && is_array($wphrmInvitationRecipient_array) && in_array($userdata->ID, $wphrmInvitationRecipient_array) ) &&  !(!empty($wphrmNoticeDepartment) && $this->user_in_department($userdata->ID, $wphrmNoticeDepartment)) ) continue;

                    $notification_message = __('A new item has been added to the notice board.', 'wphrm');
                    if($wphrmInvitationNotice){
                        $notification_message = __('You have recieved an invation. Go to notice and click attend.', 'wphrm');
                    }

                    //create notification for user
                    $notification = array(
                        'wphrmUserID' => sanitize_text_field($userdata->ID),
                        'wphrmDesc' => $notification_message,
                        'notificationType' => sanitize_text_field('Notice Board'),
                        'wphrmStatus' => sanitize_text_field('unseen'),
                        'wphrmDate' => sanitize_text_field(date('Y-m-d')),
                    );
                    $wpdb->insert($this->WphrmNotificationsTable, $notification);
                }
            }
            $message['do'] = 'add';

        }
        $message['post_data'] = $_POST;
        echo json_encode($message);
        exit;
    }

    /** Get notifications . * */
    public function WPHRMNotificationInfo() {
        global $current_user, $wpdb;
        $wphrmResults = array();
        $result = array();
        $wphrmUserId = esc_sql($current_user->ID); // esc
        $wphrmGeneralSettingsInfo = esc_sql('wphrmGeneralSettingsInfo'); // esc
        $message = array();
        $getNotifications = $wpdb->get_results("SELECT * FROM $this->WphrmNotificationsTable WHERE `wphrmUserID`='$wphrmUserId' AND `wphrmStatus`='unseen'");
        if (!empty($getNotifications)) {
            $getlogo = $wpdb->get_row("SELECT * FROM $this->WphrmSettingsTable WHERE `settingKey`='$wphrmGeneralSettingsInfo'");
            $wphrmFormDetails = unserialize(base64_decode($getlogo->settingValue));
            foreach ($getNotifications as $getNotification) {
                $dateformat = esc_sql(date('d-m-Y', strtotime($getNotification->wphrmDate))); // esc
                $result[] = array('id' => $getNotification->id, 'title' => $getNotification->notificationType . ' ' . $dateformat, 'desc' => $getNotification->wphrmDesc, 'logo' => $wphrmFormDetails['wphrm_company_logo']);
            }
        }
        echo json_encode($result);
        exit;
    }

    /** change Status notifications . * */
    public function WPHRMNotificationStatusChangeInfo() {
        global $wpdb;
        $message = array();
        $wphrmtype = '';
        if (isset($_POST['type']) && $_POST['type'] != '') {
            $wphrmtype = esc_sql($_POST['type']); // esc
        }
        $notificationId = esc_sql($_POST['notification_id']); // esc
        if (!empty($wphrmtype)) {

            $id = $wpdb->query("UPDATE $this->WphrmNotificationsTable SET `wphrmFromId`= '" . sanitize_text_field('0') . "' WHERE `id`='$notificationId'");
            echo json_encode($id);
            exit;
        } else {
            $id = $wpdb->query("UPDATE $this->WphrmNotificationsTable SET `wphrmStatus`= '" . sanitize_text_field('seen') . "' WHERE `id`='$notificationId'");
            echo json_encode($id);
            exit;
        }
    }

    /** Setting Function for user Permission. * */
    public function WPHRMUserPermissionInfo() {
        global $wpdb;
        $message = array();
        $wphrmUserPermission = '';
        if (isset($_POST['wphrm_user_permission']) && $_POST['wphrm_user_permission'] != '') {
            $wphrmUserPermission = $_POST['wphrm_user_permission'];
        }
        $wphrmUserPermissions = $this->WPHRMGetSettings('wphrmUserPermissionInfo');
        if (!empty($wphrmUserPermissions)) :
        $wphrmFormDetails = $wphrmUserPermissions;
        $wphrmFormDetails['wphrm_user_permission'] = sanitize_text_field($wphrmUserPermission);
        $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
        $id = $wpdb->query("UPDATE $this->WphrmSettingsTable SET `settingValue`= '$wphrmFormDetailsData'  WHERE `settingKey`='wphrmUserPermissionInfo'");
        $message['success'] = true;
        else :
        $wphrmFormDetails = unserialize(base64_decode($wphrmUserPermissions->settingValue));
        $wphrmFormDetails['wphrm_user_permission'] = sanitize_text_field($wphrmUserPermission);
        $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
        $id = $wpdb->query("INSERT INTO $this->WphrmSettingsTable (`settingKey`, `settingValue`)
                                               VALUES('wphrmUserPermissionInfo', '$wphrmFormDetailsData')");
        if ($id) {
            $message['success'] = true;
        } else {
            $message['error'] = __('Something went wrong.', 'wphrm');
        }
        endif;
        echo json_encode($message);
        exit;
    }

    /** Add Earning Lebals function. * */
    public function wphrmAddEarningLabelInfo() {
        global $current_user, $wpdb;

        $wphrmearning = '';
        $wphrmearningtype = '';
        $wphrmearningamount = '';
        $wphrmdeductiontype = '';
        $wphrmdeductionamount = '';
        if (isset($_POST['wphrmearninglebal'])) {
            $wphrmearning = $_POST['wphrmearninglebal'];
        }
        if (isset($_POST['wphrmearningtype'])) {
            $wphrmearningtype = $_POST['wphrmearningtype'];
        }
        if (isset($_POST['wphrmearningamount'])) {
            $wphrmearningamount = $_POST['wphrmearningamount'];
        }
        $wphrmDeductionLebal = '';
        if (isset($_POST['wphrmdeductionlebal'])) {
            $wphrmDeductionLebal = $_POST['wphrmdeductionlebal'];
        }
        if (isset($_POST['wphrmdeductiontype'])) {
            $wphrmdeductiontype = $_POST['wphrmdeductiontype'];
        }
        if (isset($_POST['wphrmdeductionamount'])) {
            $wphrmdeductionamount = $_POST['wphrmdeductionamount'];
        }

        $wphrmearningQuerys = $this->WPHRMGetSettings('wphrmEarningInfo');
        $wphrmdeductinQuerys = $this->WPHRMGetSettings('wphrmDeductionInfo');
        $wphrmFormEarningDetails = array();
        $wphrmFormDeductionDetails = array();

        if (!empty($wphrmearningQuerys)) :
        $wphrmFormEarningDetails['earningLebal'] = $this->WPHRMSanitize($wphrmearning);
        $wphrmFormEarningDetails['earningtype'] = $this->WPHRMSanitize($wphrmearningtype);
        $wphrmFormEarningDetails['earningamount'] = $this->WPHRMSanitize($wphrmearningamount);
        $wphrmFormEarningData = base64_encode(serialize($wphrmFormEarningDetails));
        $wpdb->query("UPDATE  `$this->WphrmSettingsTable` SET `settingValue`='" . $wphrmFormEarningData . "' WHERE `settingKey` = 'wphrmEarningInfo'");
        $_SESSION['WP-HRM-earning-msg'] = __('Updated successfully..', '');
        $result['success'] = true;
        else :

        $wphrmFormEarningDetails['earningLebal'] = $this->WPHRMSanitize($wphrmearning);
        $wphrmFormEarningDetails['earningtype'] = $this->WPHRMSanitize($wphrmearningtype);
        $wphrmFormEarningDetails['earningamount'] = $this->WPHRMSanitize($wphrmearningamount);
        $wphrmFormEarningData = base64_encode(serialize($wphrmFormEarningDetails));
        $id = $wpdb->query("INSERT INTO $this->WphrmSettingsTable (`settingKey`, `settingValue`) VALUES('wphrmEarningInfo','$wphrmFormEarningData')");
        $result['success'] = true;
        endif;
        if (!empty($wphrmdeductinQuerys)) :
        $wphrmFormDeductionDetails['deductionlebal'] = $this->WPHRMSanitize($wphrmDeductionLebal);
        $wphrmFormDeductionDetails['deductiontype'] = $this->WPHRMSanitize($wphrmdeductiontype);
        $wphrmFormDeductionDetails['deductionamount'] = $this->WPHRMSanitize($wphrmdeductionamount);
        $wphrmFormDeductionData = base64_encode(serialize($wphrmFormDeductionDetails));
        $wpdb->query("UPDATE  `$this->WphrmSettingsTable`  SET   `settingValue` ='" . $wphrmFormDeductionData . "' WHERE `settingKey` = 'wphrmDeductionInfo'");
        $_SESSION['WP-HRM-deduction-msg'] = __('Updated successfully..', '');
        $result['success'] = true;
        else :
        $wphrmFormDeductionDetails['deductionlebal'] = $this->WPHRMSanitize($wphrmDeductionLebal);
        $wphrmFormDeductionDetails['deductiontype'] = $this->WPHRMSanitize($wphrmdeductiontype);
        $wphrmFormDeductionDetails['deductionamount'] = $this->WPHRMSanitize($wphrmdeductionamount);
        $wphrmFormDeductionData = base64_encode(serialize($wphrmFormDeductionDetails));
        $id = $wpdb->query("INSERT INTO $this->WphrmSettingsTable (`settingKey`, `settingValue`) VALUES('wphrmDeductionInfo','$wphrmFormDeductionData')");
        $result['success'] = true;
        endif;
        echo json_encode($result);
        exit;
    }

    /**  Bank detail fields info function. * */
    public function wphrmAddBnakDetailsLabelInfo() {
        global $current_user, $wpdb;
        $wphrmBankfieldsLebal = '';
        if (isset($_POST['wphrmBankfieldsLebal'])) {
            $wphrmBankfieldsLebal = $_POST['wphrmBankfieldsLebal'];
        }
        $wphrmearningQuerys = $this->WPHRMGetSettings('Bankfieldskey');
        $wphrmFormBankfieldDetails = array();
        if (!empty($wphrmearningQuerys)) :
        $wphrmFormBankfieldDetails['Bankfieldslebal'] = $this->WPHRMSanitize($wphrmBankfieldsLebal);
        $wphrmFormBankfieldData = base64_encode(serialize($wphrmFormBankfieldDetails));
        $wpdb->query("UPDATE  `$this->WphrmSettingsTable` SET `settingValue` ='" . $wphrmFormBankfieldData . "' WHERE `settingKey` = 'Bankfieldskey'");
        $_SESSION['WP-HRM-Bank-field-msg'] = __('Updated successfully..', '');
        $result['success'] = true;
        else :
        $wphrmFormBankfieldDetails['Bankfieldslebal'] = $this->WPHRMSanitize($wphrmBankfieldsLebal);
        $wphrmFormBankfieldData = base64_encode(serialize($wphrmFormBankfieldDetails));
        $id = $wpdb->query("INSERT INTO $this->WphrmSettingsTable (`settingKey`, `settingValue`) VALUES('Bankfieldskey','$wphrmFormBankfieldData')");
        $result['success'] = true;
        endif;
        echo json_encode($result);
        exit;
    }

    /**  Other detail fields info function. * */
    public function wphrmAddOtherDetailsLabelInfo() {
        global $current_user, $wpdb;
        $wphrmOtherfieldsLebal = '';
        if (isset($_POST['wphrmOtherfieldsLebal'])) {
            $wphrmOtherfieldsLebal = $_POST['wphrmOtherfieldsLebal'];
        }
        $wphrmearningQuerys = $this->WPHRMGetSettings('Otherfieldskey');
        $wphrmFormOtherfieldDetails = array();
        if (!empty($wphrmearningQuerys)) :
        $wphrmFormOtherfieldDetails['Otherfieldslebal'] = $this->WPHRMSanitize($wphrmOtherfieldsLebal);
        $wphrmFormotherfieldData = base64_encode(serialize($wphrmFormOtherfieldDetails));
        $wpdb->query("UPDATE  `$this->WphrmSettingsTable` SET `settingValue`='" . $wphrmFormotherfieldData . "' WHERE `settingKey` = 'Otherfieldskey'");
        $_SESSION['WP-HRM-Bank-field-msg'] = __('Updated successfully..', '');
        $result['success'] = true;
        else :
        $wphrmFormBankfieldDetails['Otherfieldslebal'] = $this->WPHRMSanitize($wphrmOtherfieldsLebal);
        $wphrmFormBankfieldData = base64_encode(serialize($wphrmFormBankfieldDetails));
        $id = $wpdb->query("INSERT INTO $this->WphrmSettingsTable (`settingKey`, `settingValue`) VALUES('Otherfieldskey','$wphrmFormBankfieldData')");
        $result['success'] = true;
        endif;
        echo json_encode($result);
        exit;
    }

    /**  Document Upload fields info function. * */
    public function WPHRMAddDocumentsfieldslebalInfo() {
        global $current_user, $wpdb;
        $documentsfieldsLebal = '';
        $wphrmDefaultDocumentsLabel = '';
        if (isset($_POST['documentsfieldsLebal'])) {
            $documentsfieldsLebal = $_POST['documentsfieldsLebal'];
        }
        if (isset($_POST['wphrmDefaultDocumentsLabel'])) {
            $wphrmDefaultDocumentsLabel = $_POST['wphrmDefaultDocumentsLabel'];
        }
        $wphrmemployeeDocumentsQuerys = $this->WPHRMGetSettings('employeeDocumentsfieldskey');
        $wphrmFormDocumentsfieldDetails = array();
        if (!empty($wphrmemployeeDocumentsQuerys)) :
        $wphrmFormDocumentsfieldDetails['documentsfieldslebal'] = $this->WPHRMSanitize($documentsfieldsLebal);
        $wphrmFormDocumentsfieldData = base64_encode(serialize($wphrmFormDocumentsfieldDetails));
        $wpdb->query("UPDATE  `$this->WphrmSettingsTable` SET `settingValue`='" . $wphrmFormDocumentsfieldData . "' WHERE `settingKey` = 'employeeDocumentsfieldskey'");
        $_SESSION['WP-HRM-Bank-field-msg'] = __('Updated successfully..', '');
        $result['success'] = true;
        else :
        $wphrmFormDocumentsfieldDetails['documentsfieldslebal'] = $this->WPHRMSanitize($documentsfieldsLebal);
        $wphrmFormDocumentsfieldData = base64_encode(serialize($wphrmFormDocumentsfieldDetails));
        $id = $wpdb->query("INSERT INTO $this->WphrmSettingsTable (`settingKey`, `settingValue`) VALUES('employeeDocumentsfieldskey','$wphrmFormDocumentsfieldData')");
        $result['success'] = true;
        endif;

        $defaultDocumentLebalSettings = $this->WPHRMGetSettings('defaultDocumentLebal');
        $wphrmDefaultDocumentLebalSettings = array();
        if (!empty($defaultDocumentLebalSettings)) :
        $wphrmDefaultDocumentLebalSettings['defaultDocumentLebal'] = $this->WPHRMSanitize($wphrmDefaultDocumentsLabel);
        $wphrmDefaultDocumentLebalSettingsData = base64_encode(serialize($wphrmDefaultDocumentLebalSettings));
        $wpdb->query("UPDATE  `$this->WphrmSettingsTable` SET `settingValue`='" . $wphrmDefaultDocumentLebalSettingsData . "' WHERE `settingKey` = 'defaultDocumentLebal'");
        $result['success'] = true;
        else :
        $wphrmDefaultDocumentLebalSettings['defaultDocumentLebal'] = $this->WPHRMSanitize($wphrmDefaultDocumentsLabel);
        $wphrmDefaultDocumentLebalSettingsData = base64_encode(serialize($wphrmDefaultDocumentLebalSettings));
        $id = $wpdb->query("INSERT INTO $this->WphrmSettingsTable (`settingKey`, `settingValue`) VALUES('defaultDocumentLebal','$wphrmDefaultDocumentLebalSettingsData')");
        $result['success'] = true;
        endif;

        echo json_encode($result);
        exit;
    }

    /**  Bank detail fields info function. * */
    public function wphrmSalaryDetailsFieldsSettingsinfo() {
        global $current_user, $wpdb;
        $salaryFieldsLebal = '';
        if (isset($_POST['salaryFieldsLebal'])) {
            $salaryFieldsLebal = $_POST['salaryFieldsLebal'];
        }
        $wphrmearningQuerys = $this->WPHRMGetSettings('salarydetailfieldskey');
        $wphrmFormsalaryfieldDetails = array();
        if (!empty($wphrmearningQuerys)) :
        $wphrmFormsalaryfieldDetails['salarydetailfieldlabel'] = $this->WPHRMSanitize($salaryFieldsLebal);
        $wphrmFormsalaryfieldData = base64_encode(serialize($wphrmFormsalaryfieldDetails));
        $wpdb->query("UPDATE `$this->WphrmSettingsTable` SET `settingValue` ='" . $wphrmFormsalaryfieldData . "' WHERE `settingKey` = 'salarydetailfieldskey'");
        $_SESSION['WP-HRM-Salary-field-msg'] = __('Updated successfully..', '');
        $result['success'] = true;
        else :
        $wphrmFormsalaryfieldDetails['salarydetailfieldlabel'] = $this->WPHRMSanitize($salaryFieldsLebal);
        $wphrmFormsalaryfieldData = base64_encode(serialize($wphrmFormsalaryfieldDetails));
        $id = $wpdb->query("INSERT INTO $this->WphrmSettingsTable (`settingKey`, `settingValue`) VALUES('salarydetailfieldskey','$wphrmFormsalaryfieldData')");
        $result['success'] = true;
        endif;
        echo json_encode($result);
        exit;
    }

    /** Setting Function for page Permission. * */
    public function WPHRMPagePermissionInfo() {
        global $wpdb;
        $message = array();
        $wphrmTableName = $this->WphrmSettingsTable;
        $wphrmKeySettings = 'employee_permissions_info';
        $wphrmUserPermissions = $this->WPHRMGetSettings('$wphrmKeySettings');
        if (!empty($wphrmUserPermissions)) :
        $wphrmFormDetails = $wphrmUserPermissions;
        $wphrmFormDetails['wphrm_module_employee']['page'] = sanitize_text_field($_POST['wphrm_employee']);
        $wphrmFormDetails['wphrm_module_departments']['page'] = sanitize_text_field($_POST['wphrmDepartments']);
        $wphrmFormDetails['wphrm_module_holidays']['page'] = sanitize_text_field($_POST['wphrm_holidays']);
        $wphrmFormDetails['wphrm_module_attendances']['page'] = sanitize_text_field($_POST['wphrm_attendances']);
        $wphrmFormDetails['wphrm_module_leave_applications']['page'] = sanitize_text_field($_POST['wphrm_leave_applications']);
        $wphrmFormDetails['wphrm_module_financials']['page'] = sanitize_text_field($_POST['wphrm_financials']);
        $wphrmFormDetails['wphrm_module_notice']['page'] = sanitize_text_field($_POST['wphrm_notice']);
        $wphrmFormDetails['wphrm_module_settings']['page'] = sanitize_text_field($_POST['wphrm_settings']);
        $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
        $id = $wpdb->query("UPDATE $this->WphrmSettingsTable SET `settingValue`= '$wphrmFormDetailsData' WHERE `settingKey`='$wphrmKeySettings'");
        $_SESSION['wphrm-notice-msg'] = __('Updated successfully.', 'wphrm');
        $message['success'] = true;
        else :
        $wphrmFormDetails['wphrm_module_employee']['page'] = sanitize_text_field($_POST['wphrm_employee']);
        $wphrmFormDetails['wphrm_module_departments']['page'] = sanitize_text_field($_POST['wphrmDepartments']);
        $wphrmFormDetails['wphrm_module_holidays']['page'] = sanitize_text_field($_POST['wphrm_holidays']);
        $wphrmFormDetails['wphrm_module_attendances']['page'] = sanitize_text_field($_POST['wphrm_attendances']);
        $wphrmFormDetails['wphrm_module_leave_applications']['page'] = sanitize_text_field($_POST['wphrm_leave_applications']);
        $wphrmFormDetails['wphrm_module_financials']['page'] = sanitize_text_field($_POST['wphrm_financials']);
        $wphrmFormDetails['wphrm_module_notice']['page'] = sanitize_text_field($_POST['wphrm_notice']);
        $wphrmFormDetails['wphrm_module_settings']['page'] = sanitize_text_field($_POST['wphrm_settings']);
        $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
        $wphrmFormDetailsData = sanitize_text_field($wphrmFormDetailsData); // sanitize_text_field
        $id = $wpdb->query("INSERT INTO $this->WphrmSettingsTable (`settingKey`, `settingValue`) VALUES('$wphrmKeySettings','$wphrmFormDetailsData')");
        if ($id) {
            $_SESSION['wphrm-page-permissions-msg'] = __('Registered successfully..', 'wphrm');
            $message['success'] = true;
        } else {
            $message['error'] = __('Something went wrong.', 'wphrm');
        }
        endif;
        echo json_encode($message);
        exit;
    }

    /**  Other detail fields info function. * */
    public function wphrsalaryDayOrHourlyInfo() {
        global $current_user, $wpdb;
        $wphrmAccording = '';
        if (isset($_POST['wphrm-according'])) {
            $wphrmAccording = $_POST['wphrm-according'];
        }
        $wphrmAccordingQuerys = $this->WPHRMGetSettings('wphrsalaryDayOrHourlyInfo');
        $wphrmFormDetails = array();
        if (!empty($wphrmAccordingQuerys)) :
        $wphrmFormDetails['wphrm-according'] = sanitize_text_field($wphrmAccording);
        $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
        $wpdb->query("UPDATE  `$this->WphrmSettingsTable` SET `settingValue`='" . $wphrmFormDetailsData . "' WHERE `settingKey` = 'wphrsalaryDayOrHourlyInfo'");
        $_SESSION['WP-HRM-According-msg'] = __('Updated successfully..', '');
        $result['success'] = true;
        else :
        $wphrmFormDetails['wphrm-according'] = sanitize_text_field($wphrmAccording);
        $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
        $id = $wpdb->query("INSERT INTO $this->WphrmSettingsTable (`settingKey`, `settingValue`) VALUES('wphrsalaryDayOrHourlyInfo','$wphrmFormDetailsData')");
        $result['success'] = true;
        endif;
        echo json_encode($result);
        exit;
    }

    /**  WPHRM Salary Generation Settings function. * */
    public function WPHRMSalaryGenerationSettings() {
        global $current_user, $wpdb;
        $wphrmMonthdate = '';
        $informationtext = '';
        if (isset($_POST['monthdate'])) {
            $wphrmMonthdate = $_POST['monthdate'];
        }
        if (isset($_POST['informationtext'])) {
            $informationtext = $_POST['informationtext'];
        }
        $wphrmAccordingQuerys = $this->WPHRMGetSettings('wphrmSalaryGenerationSettings');
        $wphrmFormDetails = array();
        if (!empty($wphrmAccordingQuerys)) :
        $wphrmFormDetails['monthdate'] = sanitize_text_field($wphrmMonthdate);
        $wphrmFormDetails['informationtext'] = sanitize_text_field($informationtext);
        $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
        $wpdb->query("UPDATE  `$this->WphrmSettingsTable` SET `settingValue`='" . $wphrmFormDetailsData . "' WHERE `settingKey` = 'wphrmSalaryGenerationSettings'");
        $result['success'] = true;
        else :
        $wphrmFormDetails['monthdate'] = sanitize_text_field($wphrmMonthdate);
        $wphrmFormDetails['informationtext'] = sanitize_text_field($informationtext);
        $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
        $id = $wpdb->query("INSERT INTO $this->WphrmSettingsTable (`settingKey`, `settingValue`) VALUES('wphrmSalaryGenerationSettings','$wphrmFormDetailsData')");
        $result['success'] = true;
        endif;
        echo json_encode($result);
        exit;
    }

    /** Add currency. * */
    public function wphrmaddcurrencysettingsinfo() {
        global $current_user, $wpdb;
        $currencyName = '';
        $currencySign = '';
        $wphrmCurrencyId = '';

        if (isset($_POST['currency-name'])) {
            $currencyName = sanitize_text_field($_POST['currency-name']);
        }
        if (isset($_POST['currency-sign'])) {
            $currencySign = $_POST['currency-sign'];
        }

        if (isset($_POST['currency-desc'])) {
            $currencyDesc = $_POST['currency-desc'];
        }

        if (isset($_POST['id']) && $_POST['id'] != '') {
            $wphrmCurrencyId = esc_sql($_POST['id']);  // esc
        }

        $result = array();
        if (!empty($wphrmCurrencyId)) :
        $wphrmFormDetails = array();
        $wphrm_leavetype = $wpdb->get_row("SELECT * FROM $this->WphrmCurrencyTable WHERE `id` = $wphrmCurrencyId");
        $id = $wpdb->query("UPDATE $this->WphrmCurrencyTable SET `currencyName`='$currencyName',`currencySign`='$currencySign', `currencyDesc` ='$currencyDesc' WHERE `id` = $wphrmCurrencyId");
        $result['success'] = true;
        else :
        $wphrmFormDetails = array();
        $id = $wpdb->query("INSERT INTO $this->WphrmCurrencyTable (`currencyName`, `currencySign`, `currencyDesc`) VALUES('$currencyName', '$currencySign', '$currencyDesc')");
        $result['success'] = true;
        endif;
        echo json_encode($result);
        exit;
    }

    /** Load Ajax for currency. * */
    public function WPHRMLoadCurrencyData() {
        global $current_user, $wpdb;
        $result = array();
        $data = array();
        if (isset($_POST['id']) && $_POST['id'] != '') {
            $wphrmCurrencyId = esc_sql($_POST['id']);  // esc
        }

        if (!empty($wphrmCurrencyId)) {
            $wphrmCurrency = $wpdb->get_row("SELECT * FROM $this->WphrmCurrencyTable WHERE `id` = $wphrmCurrencyId");
            $data = array('currencyName' => $wphrmCurrency->currencyName, 'currencySign' => $wphrmCurrency->currencySign, 'currencyDesc' => $wphrmCurrency->currencyDesc);
        }

        $result['success'] = $data;
        echo json_encode($result);
        exit;
    }

    /** Setting Function change password . * */
    public function WPHRMChangePasswordInfo() {
        global $current_user, $wpdb;
        require_once( ABSPATH . 'wp-includes/class-phpass.php' );
        $user_id = esc_sql($current_user->ID); // esc
        $message = array();
        $table_name = 'wp_users';
        $wphrm_current_password = '';
        $wphrm_conform_password = '';
        $wphrm_conform_passwords = '';
        $wphrm_current_passwords = '';
        if ($_POST['wphrm_current_password'] != '') {
            $wphrm_current_password = esc_sql($_POST['wphrm_current_password']); // esc
        }
        if ($_POST['wphrm_conform_password'] != '') {
            $wphrm_conform_password = $_POST['wphrm_conform_password'];
            $wphrm_conform_passwords = md5(esc_sql($wphrm_conform_password));
        }
        $wphrm_general_settings = $wpdb->get_row("SELECT * FROM $table_name WHERE `ID` = $user_id");
        $password = $wphrm_current_password;
        $hash = $wphrm_general_settings->user_pass;
        $wp_hasher = new PasswordHash(10, TRUE);
        $check = $wp_hasher->CheckPassword($password, $hash);
        if ($check == 1) {
            $id = $wpdb->query("UPDATE $table_name SET `user_pass`='" . sanitize_text_field($wphrm_conform_passwords) . "' WHERE `ID`=$user_id");
            $message['success'] = true;
        } else {
            $message['error'] = __('Password does not match.', 'wphrm');
        }
        echo json_encode($message);
        exit;
    }

    /** User Login * */
    public function WPHRMLoginUser($user_login, $user = null) {
        global $current_user;
        if (!$user) {
            $user = get_user_by('login', $user_login);
        }

        if (!$user) {
            return;
        }
        $wphrmUserRole = implode(',', $user->roles);
        $details = $this->WPHRMGetUserWiseData($user->ID, $wphrmUserRole);
        if (isset($wphrmUserRole) && $wphrmUserRole != 'administrator') {
            if (empty($details)) {
                $this->WPHRMDefaultRoleToLogin('adddefaultrole', $wphrmUserRole, $user->ID);
            }
        }
        $wphrmEmployeeBasicInfo = $this->WPHRMGetUserDatas($user->ID, 'wphrmEmployeeInfo');
        if (isset($wphrmEmployeeBasicInfo['wphrm_employee_status']) && $wphrmEmployeeBasicInfo['wphrm_employee_status'] == 'Inactive') :

        wp_clear_auth_cookie();
        $login_url = site_url('wp-login.php', 'login');
        $login_url = add_query_arg('disabled', '1', $login_url);
        wp_redirect($login_url);
        exit;
        endif;
    }

    /** User Account Disabled Message * */
    public function WPHRMUserLoginMessage($message) {
        // Show the error message if it seems to be a disabled user
        if (isset($_GET['disabled']) && $_GET['disabled'] == 1)
            $message = '<div id="login_error">' . apply_filters('ja_disable_users_notice', __('Account has been disabled.', 'wphrm')) . '</div>';
        return $message;
    }

    /** WPHRM Default Attendance Run * */
    public function WPHRMDefaultAttendanceRun() {
        $wphrmAutomaticAttendance = $this->WPHRMGetSettings('wphrmAutomaticAttendance');
        if (isset($wphrmAutomaticAttendance['automatic-attendance']) && $wphrmAutomaticAttendance['automatic-attendance'] == 'on') {
            $this->WPHRMAutomaticAttendanceMark();
        }
        if (!isset($wphrmAutomaticAttendance['automatic-attendance'])) {
            $this->WPHRMAutomaticAttendanceMark();
        }
    }

    /** Setting Function for Attendance autometic enable or disable. * */
    public function WPHRMAutomaticAttendanceInfo() {
        global $wpdb;
        $message = array();
        $automaticAttendance = '';
        if (isset($_POST['automatic-attendance']) && $_POST['automatic-attendance'] != '') {
            $automaticAttendance = $_POST['automatic-attendance'];
        }
        $wphrmAutomaticAttendance = $this->WPHRMGetSettings('wphrmAutomaticAttendance');
        if (!empty($wphrmAutomaticAttendance)) :
        $wphrmFormDetails = $wphrmAutomaticAttendance;
        $wphrmFormDetails['automatic-attendance'] = sanitize_text_field($automaticAttendance);
        $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
        $id = $wpdb->query("UPDATE $this->WphrmSettingsTable SET `settingValue`= '$wphrmFormDetailsData'  WHERE `settingKey`='wphrmAutomaticAttendance'");
        $message['success'] = __('Automatic attendance mark settings have been successfully updated.', 'wphrm');
        else :
        $wphrmFormDetails = unserialize(base64_decode($wphrmAutomaticAttendance->settingValue));
        $wphrmFormDetails['automatic-attendance'] = sanitize_text_field($automaticAttendance);
        $wphrmFormDetailsData = base64_encode(serialize($wphrmFormDetails));
        $id = $wpdb->query("INSERT INTO $this->WphrmSettingsTable (`settingKey`, `settingValue`)
                                               VALUES('wphrmAutomaticAttendance', '$wphrmFormDetailsData')");
        if ($id) {
            $message['success'] = __('Automatic attendance mark settings have been successfully updated.', 'wphrm');
        } else {
            $message['error'] = __('Something went wrong.', 'wphrm');
        }
        endif;
        echo json_encode($message);
        exit;
    }

    /** WPHRM Automatic Attendance Mark * */
    public function WPHRMAutomaticAttendanceMark() {
        global $wpdb;
        $currentDate = esc_sql(date('Y-m-d')); // esc
        $insertCurrentDate = date('Y-m-d h:i:s'); // esc
        $holidayDatas = '';
        $employeeIDs = array();
        $allEmployee = array();

        $lastUpdatedDate = $wpdb->get_row("select `date` from $this->WphrmAttendanceTable where `status` = 'present' GROUP BY `date` ORDER BY `date` DESC");
        if (!empty($lastUpdatedDate) && isset($lastUpdatedDate->date)) {
            $checkMainDate = date("Y-m-d", strtotime("+1days", strtotime($lastUpdatedDate->date)));
            $dateBetweenArray = $this->createDateRangeArray($checkMainDate, $currentDate);
            $employee_holidays = $wpdb->get_results("select `wphrmDate` from $this->WphrmHolidaysTable");
            if (!empty($employee_holidays)) {
                foreach ($employee_holidays as $employee_holiday) {
                    if (isset($employee_holiday->wphrmDate)) {
                        $holidayDatas[] = $employee_holiday->wphrmDate;
                    }
                }
            }
            $allEmployee = $this->WPHRMGetEmployees();
            if (!empty($allEmployee)) {
                foreach ($allEmployee as $allEmployees) {
                    if (isset($allEmployees->data->ID)) {
                        $wphrmEmployeeInfo = $this->WPHRMGetUserDatas($allEmployees->data->ID, 'wphrmEmployeeInfo');
                        if (isset($wphrmEmployeeInfo['wphrm_employee_status']) && $wphrmEmployeeInfo['wphrm_employee_status'] == 'Active') {
                            $employeeIDs[] = $allEmployees->data->ID;
                        }
                    }
                }
                foreach ($dateBetweenArray as $dateBetweenDate) {
                    if (!in_array($dateBetweenDate, $holidayDatas)) {
                        foreach ($employeeIDs as $employeeID) {
                            $getDetail = $wpdb->get_row("select * from $this->WphrmAttendanceTable where `employeeID`='$employeeID' AND `date` ='$dateBetweenDate'");
                            if (empty($getDetail)) {
                                $wpdb->insert($this->WphrmAttendanceTable, array('employeeID' => sanitize_text_field($employeeID), 'date' => sanitize_text_field($dateBetweenDate),
                                                                                 'toDate' => '0000-00-00', 'status' => 'present', 'createdAt' => sanitize_text_field($insertCurrentDate), 'updatedAt' => sanitize_text_field($insertCurrentDate)));
                            }
                        }
                    }
                }
            }
        }return;
    }

    /** WPHRM Leave Counter function. * */
    public function WPHRMIndividualLeaveCounter($attendance_date = '', $employee_id = '', $between = '', $leaveTyped = '') {
        global $current_user, $wpdb;
        $attendance_date = $attendance_date;
        $result = array();
        $todaydate = esc_sql('0'); // esc
        $employeeAttendanceCount = esc_sql('0'); // esc
        $currentMonth = date('m'); // esc
        $wphrmEmployeeInfo = '';
        $wphrmEmployeeInfo = $this->WPHRMGetUserDatas($employee_id, 'wphrmEmployeeInfo');
        $current1 = date("Y-m-d");
        if ($between == 'between') {
            $dates = date('Y-m', strtotime($attendance_date));
            $attendance_dates = $dates . '-01';
            $days = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($attendance_date)), date('Y', strtotime(date("Y-m-d"))));
            $fromDates = $dates . '-' . $days;
            $query = "AND `applicationStatus`='approved' AND `date` between '$attendance_dates' AND '$fromDates'";
        } else {
            $query = " AND `date` <= '$attendance_date' AND `applicationStatus`='approved'";
        }

        if (isset($wphrmEmployeeInfo['wphrm_employee_joining_date'])) {
            $wphrmEmployeeJoiningDate = $wphrmEmployeeInfo['wphrm_employee_joining_date'];
        }
        $wphrmEmployeeJoiningDate = new DateTime($wphrmEmployeeJoiningDate);
        $today = new DateTime();
        $interval = $today->diff($wphrmEmployeeJoiningDate);
        $wphrmEmployeeJoiningToCurrentTotalYear = ((int) $interval->format('%y years') + 1);
        $curQuarter = ceil($currentMonth / 3);
        $leavesType = $wpdb->get_row("SELECT * FROM $this->WphrmLeaveTypeTable WHERE `leaveType`='$leaveTyped'");

        $leaveRemaining = '';
        $leaveTotal = '';
        $totalNoOfLeave = 0;
        if ($leavesType->period == 'Monthly') {
            $totalNoOfLeave = intval($leavesType->numberOfLeave * $currentMonth);
        } else if ($leavesType->period == 'Quarterly') {
            $totalNoOfLeave = intval($leavesType->numberOfLeave * $curQuarter);
        } else if ($leavesType->period == 'Yearly') {
            $totalNoOfLeave = intval($leavesType->numberOfLeave * $wphrmEmployeeJoiningToCurrentTotalYear);
        }
        $employeeLeaves = $wpdb->get_row("SELECT COUNT(id) AS leaveCounter FROM $this->WphrmAttendanceTable WHERE `status`='absent' AND `employeeID` ='" . $employee_id . "' AND `leaveType`='$leaveTyped' $query");
        $employeeLeavesHalfday = $wpdb->get_row("SELECT COUNT(id) AS halfdayCounter FROM $this->WphrmAttendanceTable WHERE `halfDayType`='halfday' AND `employeeID` ='" . $employee_id . "' AND `leaveType`='$leaveTyped' $query");
        $halfdayCounter = ($employeeLeavesHalfday->halfdayCounter / 2);
        $leaveTotal = $employeeLeaves->leaveCounter + $halfdayCounter;
        $leaveRemaining = $leaveTotal;

        if ($totalNoOfLeave >= $leaveTotal) {
            if ($between == 'between') {
                $leaveRemainings = array('leavetype' => $leavesType->leaveType, 'leave' => $leaveRemaining);
            } else {
                $leaveRemainings = array('leavetype' => $leavesType->leaveType, 'leave' => $totalNoOfLeave - $leaveRemaining);
            }
        } else {
            if ($between == 'between') {
                $leaveRemainings = array('leavetype' => $leavesType->leaveType, 'leave' => $leaveRemaining);
            } else {
                $leaveRemainings = array('leavetype' => $leavesType->leaveType, 'leave' => $totalNoOfLeave - $leaveRemaining);
            }
        }


        $result = $leaveRemainings;
        return $result;
    }

    public function WPHRMGetDefaultDocumentsLabel() {
        global $wpdb;
        $result = array();
        $defaultDocumentLebalSettings = $this->WPHRMGetSettings('defaultDocumentLebal');
        if (!empty($defaultDocumentLebalSettings)) {
            if (isset($defaultDocumentLebalSettings['defaultDocumentLebal'][0])) {
                $resume = $defaultDocumentLebalSettings['defaultDocumentLebal'][0];
            } else {
                $resume = 'Resume';
            }
            if (isset($defaultDocumentLebalSettings['defaultDocumentLebal'][1])) {
                $offerLetter = $defaultDocumentLebalSettings['defaultDocumentLebal'][1];
            } else {
                $offerLetter = 'Offer Letter';
            }
            if (isset($defaultDocumentLebalSettings['defaultDocumentLebal'][2])) {
                $joiningLetter = $defaultDocumentLebalSettings['defaultDocumentLebal'][2];
            } else {
                $joiningLetter = 'Joining Letter';
            }
            if (isset($defaultDocumentLebalSettings['defaultDocumentLebal'][3])) {
                $contractAndAgreement = $defaultDocumentLebalSettings['defaultDocumentLebal'][3];
            } else {
                $contractAndAgreement = 'Contract and Agreement';
            }
            if (isset($defaultDocumentLebalSettings['defaultDocumentLebal'][4])) {
                $iDProof = $defaultDocumentLebalSettings['defaultDocumentLebal'][4];
            } else {
                $iDProof = 'ID Proof';
            }
        } else {
            $resume = 'Resume';
            $offerLetter = 'Offer Letter';
            $joiningLetter = 'Joining Letter';
            $contractAndAgreement = 'Contract and Agreement';
            $iDProof = 'ID Proof';
        }
        $result = array('resume' => $resume, 'offerLetter' => $offerLetter, 'joiningLetter' => $joiningLetter, 'contractAndAgreement' => $contractAndAgreement, 'iDProof' => $iDProof);
        return $result;
    }

    public function WPHRMCheckHolidayBetweenDate() {
        global $wpdb;
        global $wphrm;
        global $current_user;
        $message = array();
        $employee_id = esc_sql($current_user->ID); // esc
        
        $emp = $this->get_user_complete_info( $employee_id );
        $leveltype = $emp->basic_info->wphrm_employee_level;
        $bod = $emp->basic_info->wphrm_employee_bod;
        $bdate = explode( '-', $bod );
        $year = date('Y');
        $age = $year - $bdate[2];
        
        $wphrm_leavetyped = '';
        if (isset($_POST['startDate'])) {
            $startDate = date('Y-m-d', strtotime($_POST['startDate']));
        }
        if (isset($_POST['endDate'])) {
            $endDate = date('Y-m-d', strtotime($_POST['endDate']));
        }

        if (isset($_POST['wphrm_leavetyped'])) {
            $wphrm_leavetyped = $_POST['wphrm_leavetyped'];
        }
        $dateBetweenArray = $this->createDateRangeArray($startDate, $endDate);
        $employee_holidays = $wpdb->get_results("select `wphrmDate` from $this->WphrmHolidaysTable");
        if (!empty($employee_holidays)) {
            foreach ($employee_holidays as $employee_holiday) {
                if (isset($employee_holiday->wphrmDate)) {
                    $holidayDatas[] = $employee_holiday->wphrmDate;
                }
            }
        }
        $totaldate = count($dateBetweenArray);
        foreach ($dateBetweenArray as $dateBetweenDate) {
            if (in_array($dateBetweenDate, $holidayDatas)) {
                $dateDatails[] = $dateBetweenDate;
            }
        }
        $finalHoliday = count($dateDatails);
        $finalDate = ($totaldate - $finalHoliday);

        if (isset($_POST['fromtype']) && $_POST['fromtype'] == 'false') {
            $fromtype = .50;
        } else {
            $fromtype = 0;
        }
        if (isset($_POST['totype']) && $_POST['totype'] == 'false') {
            $totype = .50;
        } else {
            $totype = 0;
        }


        $halfDay = ($fromtype + $totype);
        $finalDate = ($finalDate - $halfDay);
        //  echo $finalDate; exit();

        $beforeMonthLeaves = $this->WPHRMIndividualLeaveCounter(date('Y-m-d', strtotime(date('Y-m-d') . " -1 month")), $employee_id, '', $wphrm_leavetyped);
        $currentMonthLeaves = $this->WPHRMIndividualLeaveCounter(date('Y-m-d'), $employee_id, 'between', $wphrm_leavetyped);
        if ($beforeMonthLeaves['leavetype'] == $currentMonthLeaves['leavetype']) {
            $dataDetails = $beforeMonthLeaves['leave'] - $currentMonthLeaves['leave'];
        }
        if ($beforeMonthLeaves['leavetype'] == $currentMonthLeaves['leavetype']) {
            $totaldetails = $currentMonthLeaves['leave'];
        }
        $dataDetail = $this->WPHRMNegValue($dataDetails);
        $totaldetails = array_sum($totaldetails);
        $approvedleaves = $totaldetails - $dataDetails; //total leave minus approved leave
        $unapprovedLeave = ($totaldetails - $approvedleaves);
        $approvedleavesResult = 0;
        if ($approvedleaves < 0) {
            $neg = explode('-', $approvedleaves);
            $approvedleavesResult = $neg[1];
        }
        
        $leaveRule = $wpdb->get_row("SELECT * FROM $this->WphrmLeaveTypeTable WHERE id = $wphrm_leavetyped ");
        if( $leaveRule->leave_rules != 0 ) {
            $leaveRules =  $wpdb->get_row("SELECT * FROM $this->WphrmLeaveRulesTable WHERE id = $leaveRule->leave_rules ");
            if( $leaveRules ) {
                $reimbursement = $leaveRules->medical_claim_limit;
                $elderly = $leaveRules->elderly_screening_limit;
                $child_age_limit = $leaveRules->child_age_limit;
                $max_children_covered = $leaveRules->max_children_covered;
            }
        }

        if ($finalHoliday == 0) {
            $hday = __('None', 'wphrm');
        } else if ($finalHoliday == 1) {
            $hday = $finalHoliday . __(' day', 'wphrm');
        } else {
            $hday = $finalHoliday . __(' days', 'wphrm');
        }

        if ($finalDate == 0) {
            $lday = __('None', 'wphrm');
            $lday_count = $finalDate;
        } else if ($finalDate == 1) {
            $lday = $finalDate . __(' day', 'wphrm');
            $lday_count = $finalDate;
        } else {
            $lday = $finalDate . __(' days', 'wphrm');
            $lday_count = $finalDate;
        }

        if ($finalDate == 0) {
            $unpaid = __('None', 'wphrm');
        } else if ($finalDate == 1) {
            if ($approvedleavesResult >= $finalDate) {
                $unpaid = __('None', 'wphrm');
                $paidDay = $finalDate . __(' day', 'wphrm');
            } else {
                $unpaid = ($finalDate - $approvedleavesResult) . __(' day', 'wphrm');
                $paidDay = ($finalDate - $unpaid) . __(' day', 'wphrm');
            }
        } else {
            if ($approvedleavesResult >= $finalDate) {
                $unpaid = __('None', 'wphrm');
                $paidDay = ($finalDate) . __(' day', 'wphrm');
            } else {

                $unpaid = ($finalDate - $approvedleavesResult) . __(' days', 'wphrm');
                $paidDay = ($finalDate - $unpaid) . __(' days', 'wphrm');
            }
        }
        
        $response = array('status' => false, 'max_leave' => '');
        $leave_id = $_REQUEST['wphrm_leavetyped'];
        if( $leave_id == 12 ) {
            $wphrmEmployeeJoiningDate = new DateTime($emp->basic_info->wphrm_employee_joining_date);
            $today = new DateTime();
            $interval = $today->diff($wphrmEmployeeJoiningDate);
            $wphrmEmployeeJoiningToCurrentTotalYear = ((int) $interval->format('%y years'));
            
            $leave_entitlement = $wpdb->get_row("SELECT * FROM  $this->WphrmEmployeeLevelTable WHERE years_of_service = $wphrmEmployeeJoiningToCurrentTotalYear");
            if( $leveltype == 'senior_manager' ) {
                $max_leave = $leave_entitlement->senior_manager;
            } elseif( $leveltype == 'manager' ) {
                $max_leave = $leave_entitlement->manager;
            } elseif( $leveltype == 'supervisor' ) {
                $max_leave = $leave_entitlement->supervisor;
            } elseif( $leveltype == 'staff' ) {
                $max_leave = $leave_entitlement->staff;
            }
            
            $wphrmEmployeeInfo = $wphrm->WPHRMGetUserDatas($employee_id, 'wphrmEmployeeInfo');
            $annual_leave_history = isset($wphrmEmployeeInfo['wphrm_employee_leave_carried']) ? $wphrmEmployeeInfo['wphrm_employee_leave_carried'] : false;
            if(
                isset($annual_leave_history[$year-1]) && isset($annual_leave_history[$year-1]['can_carry_over'])
                && filter_var($annual_leave_history[$year-1]['can_carry_over'], FILTER_VALIDATE_BOOLEAN)
                && !empty($annual_leave_history[$year-1]['count'])
            ){
                $max_leave = $max_leave + $annual_leave_history[$year-1]['count'];
            }
            
            $leave_type = $this->get_leave_info($leave_id);
            $used_leave = $this->get_used_leave($employee_id, $leave_id);
            $message['total_leave_left'] = $max_leave - $used_leave;
            $message['max_leave_html'] = $used_leave.'/'.$max_leave;
        } else {
            $leave_type = $this->get_leave_info($leave_id);
            $max_leave = $this->get_employee_max_leave($employee_id, $leave_type->leave_rules, $leave_type);
            $used_leave = $this->get_used_leave($employee_id, $leave_id);
            $message['total_leave_left'] = $max_leave - $used_leave;
            $message['max_leave_html'] = $used_leave.'/'.$max_leave;
        }
                
        $message['success'] = true;
        $message['holiday'] = $hday;
        $message['leave'] = $lday;
        $message['leave_count'] = $lday_count;
        $message['age'] = $age;
        $message['leave_type'] = $wphrm_leavetyped;
        $message['reimbursement'] = $reimbursement;
        $message['elderly'] = $elderly;
        $message['child_age_limit'] = $child_age_limit;
        $message['max_children_covered'] = $max_children_covered;

        $message['paid'] = $paidDay;
        $message['unpaid'] = $unpaid;
        echo json_encode($message);
        exit;
    }

    /*J@F Customs*/
    function get_user_leave_count($user_id, $leave_id, $year = null){
        global $current_user, $wpdb;
        $year = $year ? $year : date('Y');
        $current_date = date("$year-m-d");
        $start_date = date("$year-m-d");
        $leavesType = $this->get_leave_info($leave_id);
        $employeeLeaves = $wpdb->get_row("SELECT COUNT(id) AS leaveCounter FROM $this->WphrmAttendanceTable WHERE `status`='absent' AND `employeeID` ='" . $user_id . "' AND `leaveType`='$leavesType->leaveType' AND (`date` >= '$start_date' AND `date` <= '$current_date' ) AND `applicationStatus`='approved'");
        $employeeLeavesHalfday = $wpdb->get_row("SELECT COUNT(id) AS halfdayCounter FROM $this->WphrmAttendanceTable WHERE `halfDayType`='halfday' AND `employeeID` ='" . $userdata->ID . "' AND `leaveType`='$leavesType->leaveType' AND (`date` >= '$start_date' AND `date` <= '$current_date' ) AND `applicationStatus`='approved'");
        $halfdayCounter = ($employeeLeavesHalfday->halfdayCounter / 2);
        $leaveTotal = $employeeLeaves->leaveCounter + $halfdayCounter;
        return $leaveTotal;
    }

    function get_leave_info($id, $column = null){
        global $wpdb, $wphrm;
        if(is_numeric($id)){
            $wphrm_leavetype = $wpdb->get_row("SELECT * FROM $this->WphrmLeaveTypeTable WHERE `id` = '$id'");
        }else{
            $wphrm_leavetype = $wpdb->get_row("SELECT * FROM $this->WphrmLeaveTypeTable WHERE `leaveType` = '$id'");
        }

        if(!$wphrm_leavetype){
            return '';
        }elseif($column){
            return $wphrm_leavetype->$column;
        }else{
            return $wphrm_leavetype;
        }
    }

    function get_user_department($user_id){
        $wphrmEmployeeInfo = $this->WPHRMGetUserDatas($user_id, 'wphrmEmployeeInfo');
        $dept_id = empty($wphrmEmployeeInfo['wphrm_employee_department']) ? 0 : $wphrmEmployeeInfo['wphrm_employee_department'];
        return $dept_id;
    }

    function get_user_info($user_id){
        $wphrmEmployeeInfo = $this->WPHRMGetUserDatas($user_id, 'wphrmEmployeeInfo');
        return is_array($wphrmEmployeeInfo) ? (object)$wphrmEmployeeInfo : null;
    }

    function confirmNoticeInvitation(){
        global $wpdb, $current_user;
        $response = array('status' => false, 'message' => '');
        $notice_id = empty($_REQUEST['notice_id']) ? false : esc_sql( $_REQUEST['notice_id'] );
        $status = empty($_REQUEST['status']) ? false : esc_sql( $_REQUEST['status'] );
        if($notice_id){

            $duplicate = $wpdb->get_row("SELECT * FROM `$this->WphrmInvitationAttendeeTable` WHERE `notice_id` = '$notice_id' AND  `user_id` = '$current_user->ID' ");

            if($duplicate){
                $response['message'] = 'You already '.strtoupper($duplicate->status).' on this invitation.';
            }else{
                $data = array(
                    'notice_id' => $notice_id,
                    'user_id' => $current_user->ID,
                    'status' => $status,
                );
                if($wpdb->insert($this->WphrmInvitationAttendeeTable, $data)){
                    $response['status'] = true;
                    if($status == 'attending'){
                        $response['message'] = 'You are added to the attendee list.';
                    }else{
                        $response['message'] = 'You declined to the invitation.';
                    }
                }
            }
        }else{
            $response['message'] = 'Error while sending the request';
        }
        echo json_encode($response); exit;
    }

    public function WPHRMFileDocuments() {
        include_once('jaf-wphrm-file-documents.php');
    }

    function WPHRMInvitationAttendee(){
        include_once('jaf-wphrm-attendee.php');
    }

    function get_department_list(){
        global $wpdb;
        $result = $wpdb->get_results("SELECT * FROM $this->WphrmDepartmentTable ");
        return $result;
    }
    function get_department_info($department_id){
        global $wpdb;
        $result = $wpdb->get_row("SELECT * FROM $this->WphrmDepartmentTable WHERE departmentID = '$department_id' ");
        $data = null;
        if($result){
            $data = unserialize(base64_decode($result->departmentName));
            $data['id'] = $result->departmentID;
        }
        return $data;
    }
    function get_designation_info($designation_id){
        global $wpdb;
        $result = $wpdb->get_row("SELECT * FROM $this->WphrmDesignationTable WHERE designationID = '$designation_id' ");
        $data = null;
        if($result){
            $data = unserialize(base64_decode($result->designationName));
            $data['id'] = $result->designationID;
        }
        return $data;
    }
    function get_designation_list(){
        global $wpdb;
        $result = $wpdb->get_results("SELECT * FROM $this->WphrmDesignationTable ");
        return $result;
    }
    function user_in_department($user_id, $departmen_id){
        $is_in = false;
        $wphrmEmployeeBasicInfo = $this->WPHRMGetUserDatas($user_id, 'wphrmEmployeeInfo');
        if($wphrmEmployeeBasicInfo['wphrm_employee_department'] == $departmen_id) {
            $is_in = true;
        }
        return $is_in;
    }

    function get_user_invitations($user_id){
        global $wpdb;
        $inv = $wpdb->get_results("SELECT * FROM $this->WphrmNoticeTable WHERE wphrminvitationnotice = 1 AND wphrminvitationsender = '$user_id' ");
        return $inv;
    }

    function get_user_invitation_reponse($user_id, $notice_id){
        global $wpdb;
        $respond = $wpdb->get_row("SELECT * FROM $this->WphrmInvitationAttendeeTable WHERE user_id = '$user_id' AND notice_id = '$notice_id' ");
        return $respond;
    }

    function get_country_list($options = array() ){
        $country_list = array();
        $options = array_merge(array('country'=>array(), 'exclude'=>array()), $options);
        $json_country = '[{"name": "Afghanistan","code": "AF"},{"name": "Åland Islands","code": "AX"},{"name": "Albania","code": "AL"},{"name": "Algeria","code": "DZ"},{"name": "American Samoa","code": "AS"},{"name": "AndorrA","code": "AD"},{"name": "Angola","code": "AO"},{"name": "Anguilla","code": "AI"},{"name": "Antarctica","code": "AQ"},{"name": "Antigua and Barbuda","code": "AG"},{"name": "Argentina","code": "AR"},{"name": "Armenia","code": "AM"},{"name": "Aruba","code": "AW"},{"name": "Australia","code": "AU"},{"name": "Austria","code": "AT"},{"name": "Azerbaijan","code": "AZ"},{"name": "Bahamas","code": "BS"},{"name": "Bahrain","code": "BH"},{"name": "Bangladesh","code": "BD"},{"name": "Barbados","code": "BB"},{"name": "Belarus","code": "BY"},{"name": "Belgium","code": "BE"},{"name": "Belize","code": "BZ"},{"name": "Benin","code": "BJ"},{"name": "Bermuda","code": "BM"},{"name": "Bhutan","code": "BT"},{"name": "Bolivia","code": "BO"},{"name": "Bosnia and Herzegovina","code": "BA"},{"name": "Botswana","code": "BW"},{"name": "Bouvet Island","code": "BV"},{"name": "Brazil","code": "BR"},{"name": "British Indian Ocean Territory","code": "IO"},{"name": "Brunei Darussalam","code": "BN"},{"name": "Bulgaria","code": "BG"},{"name": "Burkina Faso","code": "BF"},{"name": "Burundi","code": "BI"},{"name": "Cambodia","code": "KH"},{"name": "Cameroon","code": "CM"},{"name": "Canada","code": "CA"},{"name": "Cape Verde","code": "CV"},{"name": "Cayman Islands","code": "KY"},{"name": "Central African Republic","code": "CF"},{"name": "Chad","code": "TD"},{"name": "Chile","code": "CL"},{"name": "China","code": "CN"},{"name": "Christmas Island","code": "CX"},{"name": "Cocos (Keeling) Islands","code": "CC"},{"name": "Colombia","code": "CO"},{"name": "Comoros","code": "KM"},{"name": "Congo","code": "CG"},{"name": "Congo, Democratic Republic","code": "CD"},{"name": "Cook Islands","code": "CK"},{"name": "Costa Rica","code": "CR"},{"name": "Cote D\"Ivoire","code": "CI"},{"name": "Croatia","code": "HR"},{"name": "Cuba","code": "CU"},{"name": "Cyprus","code": "CY"},{"name": "Czech Republic","code": "CZ"},{"name": "Denmark","code": "DK"},{"name": "Djibouti","code": "DJ"},{"name": "Dominica","code": "DM"},{"name": "Dominican Republic","code": "DO"},{"name": "Ecuador","code": "EC"},{"name": "Egypt","code": "EG"},{"name": "El Salvador","code": "SV"},{"name": "Equatorial Guinea","code": "GQ"},{"name": "Eritrea","code": "ER"},{"name": "Estonia","code": "EE"},{"name": "Ethiopia","code": "ET"},{"name": "Falkland Islands (Malvinas)","code": "FK"},{"name": "Faroe Islands","code": "FO"},{"name": "Fiji","code": "FJ"},{"name": "Finland","code": "FI"},{"name": "France","code": "FR"},{"name": "French Guiana","code": "GF"},{"name": "French Polynesia","code": "PF"},{"name": "French Southern Territories","code": "TF"},{"name": "Gabon","code": "GA"},{"name": "Gambia","code": "GM"},{"name": "Georgia","code": "GE"},{"name": "Germany","code": "DE"},{"name": "Ghana","code": "GH"},{"name": "Gibraltar","code": "GI"},{"name": "Greece","code": "GR"},{"name": "Greenland","code": "GL"},{"name": "Grenada","code": "GD"},{"name": "Guadeloupe","code": "GP"},{"name": "Guam","code": "GU"},{"name": "Guatemala","code": "GT"},{"name": "Guernsey","code": "GG"},{"name": "Guinea","code": "GN"},{"name": "Guinea-Bissau","code": "GW"},{"name": "Guyana","code": "GY"},{"name": "Haiti","code": "HT"},{"name": "Heard Island and Mcdonald Islands","code": "HM"},{"name": "Holy See (Vatican City State)","code": "VA"},{"name": "Honduras","code": "HN"},{"name": "Hong Kong","code": "HK"},{"name": "Hungary","code": "HU"},{"name": "Iceland","code": "IS"},{"name": "India","code": "IN"},{"name": "Indonesia","code": "ID"},{"name": "Iran","code": "IR"},{"name": "Iraq","code": "IQ"},{"name": "Ireland","code": "IE"},{"name": "Isle of Man","code": "IM"},{"name": "Israel","code": "IL"},{"name": "Italy","code": "IT"},{"name": "Jamaica","code": "JM"},{"name": "Japan","code": "JP"},{"name": "Jersey","code": "JE"},{"name": "Jordan","code": "JO"},{"name": "Kazakhstan","code": "KZ"},{"name": "Kenya","code": "KE"},{"name": "Kiribati","code": "KI"},{"name": "Korea (North)","code": "KP"},{"name": "Korea (South)","code": "KR"},{"name": "Kosovo","code": "XK"},{"name": "Kuwait","code": "KW"},{"name": "Kyrgyzstan","code": "KG"},{"name": "Laos","code": "LA"},{"name": "Latvia","code": "LV"},{"name": "Lebanon","code": "LB"},{"name": "Lesotho","code": "LS"},{"name": "Liberia","code": "LR"},{"name": "Libyan Arab Jamahiriya","code": "LY"},{"name": "Liechtenstein","code": "LI"},{"name": "Lithuania","code": "LT"},{"name": "Luxembourg","code": "LU"},{"name": "Macao","code": "MO"},{"name": "Macedonia","code": "MK"},{"name": "Madagascar","code": "MG"},{"name": "Malawi","code": "MW"},{"name": "Malaysia","code": "MY"},{"name": "Maldives","code": "MV"},{"name": "Mali","code": "ML"},{"name": "Malta","code": "MT"},{"name": "Marshall Islands","code": "MH"},{"name": "Martinique","code": "MQ"},{"name": "Mauritania","code": "MR"},{"name": "Mauritius","code": "MU"},{"name": "Mayotte","code": "YT"},{"name": "Mexico","code": "MX"},{"name": "Micronesia","code": "FM"},{"name": "Moldova","code": "MD"},{"name": "Monaco","code": "MC"},{"name": "Mongolia","code": "MN"},{"name": "Montserrat","code": "MS"},{"name": "Morocco","code": "MA"},{"name": "Mozambique","code": "MZ"},{"name": "Myanmar","code": "MM"},{"name": "Namibia","code": "NA"},{"name": "Nauru","code": "NR"},{"name": "Nepal","code": "NP"},{"name": "Netherlands","code": "NL"},{"name": "Netherlands Antilles","code": "AN"},{"name": "New Caledonia","code": "NC"},{"name": "New Zealand","code": "NZ"},{"name": "Nicaragua","code": "NI"},{"name": "Niger","code": "NE"},{"name": "Nigeria","code": "NG"},{"name": "Niue","code": "NU"},{"name": "Norfolk Island","code": "NF"},{"name": "Northern Mariana Islands","code": "MP"},{"name": "Norway","code": "NO"},{"name": "Oman","code": "OM"},{"name": "Pakistan","code": "PK"},{"name": "Palau","code": "PW"},{"name": "Palestinian Territory, Occupied","code": "PS"},{"name": "Panama","code": "PA"},{"name": "Papua New Guinea","code": "PG"},{"name": "Paraguay","code": "PY"},{"name": "Peru","code": "PE"},{"name": "Philippines","code": "PH"},{"name": "Pitcairn","code": "PN"},{"name": "Poland","code": "PL"},{"name": "Portugal","code": "PT"},{"name": "Puerto Rico","code": "PR"},{"name": "Qatar","code": "QA"},{"name": "Reunion","code": "RE"},{"name": "Romania","code": "RO"},{"name": "Russian Federation","code": "RU"},{"name": "Rwanda","code": "RW"},{"name": "Saint Helena","code": "SH"},{"name": "Saint Kitts and Nevis","code": "KN"},{"name": "Saint Lucia","code": "LC"},{"name": "Saint Pierre and Miquelon","code": "PM"},{"name": "Saint Vincent and the Grenadines","code": "VC"},{"name": "Samoa","code": "WS"},{"name": "San Marino","code": "SM"},{"name": "Sao Tome and Principe","code": "ST"},{"name": "Saudi Arabia","code": "SA"},{"name": "Senegal","code": "SN"},{"name": "Serbia","code": "RS"},{"name": "Montenegro","code": "ME"},{"name": "Seychelles","code": "SC"},{"name": "Sierra Leone","code": "SL"},{"name": "Singapore","code": "SG"},{"name": "Slovakia","code": "SK"},{"name": "Slovenia","code": "SI"},{"name": "Solomon Islands","code": "SB"},{"name": "Somalia","code": "SO"},{"name": "South Africa","code": "ZA"},{"name": "South Georgia and the South Sandwich Islands","code": "GS"},{"name": "Spain","code": "ES"},{"name": "Sri Lanka","code": "LK"},{"name": "Sudan","code": "SD"},{"name": "Suriname","code": "SR"},{"name": "Svalbard and Jan Mayen","code": "SJ"},{"name": "Swaziland","code": "SZ"},{"name": "Sweden","code": "SE"},{"name": "Switzerland","code": "CH"},{"name": "Syrian Arab Republic","code": "SY"},{"name": "Taiwan, Province of China","code": "TW"},{"name": "Tajikistan","code": "TJ"},{"name": "Tanzania","code": "TZ"},{"name": "Thailand","code": "TH"},{"name": "Timor-Leste","code": "TL"},{"name": "Togo","code": "TG"},{"name": "Tokelau","code": "TK"},{"name": "Tonga","code": "TO"},{"name": "Trinidad and Tobago","code": "TT"},{"name": "Tunisia","code": "TN"},{"name": "Turkey","code": "TR"},{"name": "Turkmenistan","code": "TM"},{"name": "Turks and Caicos Islands","code": "TC"},{"name": "Tuvalu","code": "TV"},{"name": "Uganda","code": "UG"},{"name": "Ukraine","code": "UA"},{"name": "United Arab Emirates","code": "AE"},{"name": "United Kingdom","code": "GB"},{"name": "United States","code": "US"},{"name": "United States Minor Outlying Islands","code": "UM"},{"name": "Uruguay","code": "UY"},{"name": "Uzbekistan","code": "UZ"},{"name": "Vanuatu","code": "VU"},{"name": "Venezuela","code": "VE"},{"name": "VietNam","code": "VN"},{"name": "Virgin Islands, British","code": "VG"},{"name": "Virgin Islands, U.S.","code": "VI"},{"name": "Wallis and Futuna","code": "WF"},{"name": "Western Sahara","code": "EH"},{"name": "Yemen","code": "YE"},{"name": "Zambia","code": "ZM"},{"name": "Zimbabwe","code": "ZW"}]';
        $countries = json_decode($json_country, false);
        //show only this
        foreach($countries as $key => $country){
            if($options['country']){
                if(!in_array($country->code, $options['country'])) continue; //unset($countries[$key]);
                $country_list[$country->code] = $country;
            }
            if($options['exclude']){
                if(in_array($country->code, $options['exclude'])) continue; //unset($countries[$key]);
            }
            $country_list[$country->code] = $country;
        }
        return $country_list;
    }

    function get_simplex_countries(){
        return array('SG', 'MY', 'CN', 'VN', 'MM', 'MN');
    }

    function get_employee_types(){
        $employee_levels = array(
            'senior_manager' => array(
                'title' => 'Level 1 Senior Manager (SM-III to SM-I)',
                'entitlement' => 21,
                'cap' => 25,
                'entitle_medical_claim' => 500,
                'entitle_elderly_screening' => 200,
                'type_code' => array('n3')
            ),
            'manager' => array(
                'title' => 'Level 2 Manager (M-III to M-I)',
                'entitlement' => 18,
                'cap' => 21,
                'entitle_medical_claim' => 400,
                'entitle_elderly_screening' => 200,
                'type_code' => array('n2','n1','e1')
            ),
            'supervisor' => array(
                'title' => 'Level 3 Executive & Supervisor (NE-II to E-I)',
                'entitlement' => 14,
                'cap' => 18,
                'entitle_medical_claim' => 300,
                'entitle_elderly_screening' => 0,
                'type_code' => array('m1','m2','m3')
            ),
            'staff' => array(
                'title' => 'Level 4 Staff (NE-III)',
                'entitlement' => 7,
                'cap' => 14,
                'entitle_medical_claim' => 300,
                'entitle_elderly_screening' => 0,
                'type_code' => array('n3')
            ),
        );

        return $employee_levels;
    }
    function get_employee_type($level){
        $employee_levels = $this->get_employee_types();
        if(isset($employee_levels[$level])){
            return $employee_levels[$level];
        }else{
            return false;
        }
    }
    function get_user_complete_info($user_id){
        $user_details = array();
        $user_details['basic_info'] = (object) $this->WPHRMGetUserDatas( $user_id, 'wphrmEmployeeInfo' );
        $user_details['document_info'] = (object) $this->WPHRMGetUserDatas( $user_id, 'wphrmEmployeeDocumentInfo' );
        $user_details['salary_info'] = (object) $this->WPHRMGetUserDatas( $user_id, 'wphrmEmployeeSalaryInfo' );
        $user_details['bank_info'] = (object) $this->WPHRMGetUserDatas( $user_id, 'wphrmEmployeeBankInfo' );
        $user_details['family_info'] = (object) $this->WPHRMGetUserDatas( $user_id, 'wphrmEmployeeFamilyInfo' );
        $user_details['work_permit_info'] = (object) $this->WPHRMGetUserDatas( $user_id, 'wphrmEmployeeWorkPermitInfo' );
        $user_details['other_info'] = (object) $this->WPHRMGetUserDatas( $user_id, 'wphrmEmployeeOtherInfo' );
        return (object) $user_details;
    }

    function wphrm_employee_work_permit_form(){
        $response = array('status' => false, 'action_type' => '');
        $employee_id = empty($_POST['wphrm_employee_id']) ? false : intval($_POST['wphrm_employee_id']);

        $data = array(
            'wphrm_employee_work_permit_type' => sanitize_text_field($_POST['wphrm_employee_work_permit_type']),
            'wphrm_employee_nric' => sanitize_text_field($_POST['wphrm_employee_nric']),
            'wphrm_employee_permit_issued' => sanitize_text_field($_POST['wphrm_employee_permit_issued']),
            'wphrm_employee_permit_expiration' => sanitize_text_field($_POST['wphrm_employee_permit_expiration']),
            'wphrm_employee_nationality' => sanitize_text_field($_POST['wphrm_employee_nationality']),
            'wphrm_employee_country_of_birth' => sanitize_text_field($_POST['wphrm_employee_country_of_birth']),
            'wphrm_employee_passport' => sanitize_text_field($_POST['wphrm_employee_passport']),
            'wphrm_employee_passport_country' => sanitize_text_field($_POST['wphrm_employee_passport_country']),
            'wphrm_employee_country_origin' => sanitize_text_field($_POST['wphrm_employee_country_origin']),
            'wphrm_employee_race' => sanitize_text_field($_POST['wphrm_employee_race']),
        );

        if($employee_id){
            $old_data = get_user_meta($employee_id, 'wphrmEmployeeWorkPermitInfo', true);
            if($old_data == false){ //add new data
                $response['action_type'] = 'add';
                $serialize_data = base64_encode(serialize($data));
                if(update_user_meta($employee_id, 'wphrmEmployeeWorkPermitInfo', $serialize_data)){
                    $response['status'] = true;
                }
            }else{ //update existing
                $response['action_type'] = 'update';
                $update_data = $this->WPHRMGetUserDatas( $user_id, 'wphrmEmployeeWorkPermitInfo' );
                $update_data = array_merge($update_data, array_filter($data));
                $serialize_data = base64_encode(serialize($update_data));
                if(update_user_meta($employee_id, 'wphrmEmployeeWorkPermitInfo', $serialize_data)){
                    $response['status'] = true;
                }
            }
        }

        $response['post_data'] = $_POST;
        $response['old_data'] = $old_data;
        $response['old_data_us'] = $decode_result;
        echo json_encode($response);
        exit;
    }

    function echo_ne($value, $echo = true){
        $out = '';
        if(!empty($value)){
            $out = $value;
        }
        if($echo){
            echo $out;
        }else{
            return $out;
        }
    }
    function get_leave_rules() {
        global $wpdb;
        $results =  $wpdb->get_results("SELECT * FROM   $this->WphrmLeaveRulesTable ORDER BY leaveRule ASC");
        return $results;
    }
    function get_leave_rule($rule) {
        global $wpdb;
        $result = $wpdb->get_row("SELECT * FROM $this->WphrmLeaveRulesTable WHERE id = $rule ");
        return $result;
    }

    function get_employee_total_leave($user_id, $leave_id, $year = null){
        $leave_total = 0;
        $leave_info = $this->get_leave_info($leave_id);
        if($leave_info){
            $leave_rules = $this->get_leave_rules();
            $leave_total = $leave_info->numberOfLeave;
            if(!empty($leave_info->leave_rules) && in_array($leave_info->leave_rules, array_keys($leave_rules))){
                $leave_total = $this->get_employee_max_leave($user_id, $leave_info->leave_rules, $leave_info, $year);
            }
        }
        return $leave_total;
    }

    function get_employee_max_leave($user_id, $leave_rule, $leave_type = null, $year = null){
        global $wpdb, $wphrm;
        $year = empty($year) ? date('Y') : $year;
        $total = 0;
        $entitle = 0;
        $wphrmEmployeeInfo = $wphrm->WPHRMGetUserDatas($user_id, 'wphrmEmployeeInfo');

        $employee_joining_date = '';
        if (isset($wphrmEmployeeInfo['wphrm_employee_joining_date'])) {
            $employee_joining_date = $wphrmEmployeeInfo['wphrm_employee_joining_date'];
        }
        $employee_joining_date = new DateTime($employee_joining_date);

        $year_end_date = new DateTime( date($year.'-m-d', strtotime('Dec 31')) );

        $date_str_now = date("c", current_time('timestamp'));
        $date_now = new DateTime($date_str_now);
        $date_of_stay = date_diff($date_now, $employee_joining_date);

        switch($leave_rule){

            case 'annual_leave':
                {
                    //check if the employee is regular, if not he/she is not eligible for this leaeve then return 0
                    if(!$this->is_employee_regular($user_id)){
                        return 0;
                    }
                    //get the info for employee and his/her cap limit
                    $capped = 0;
                    if(!empty($wphrmEmployeeInfo['wphrm_employee_level'])){
                        $classification_rule = $this->get_employee_type( $wphrmEmployeeInfo['wphrm_employee_level'] );
                        $capped = $classification_rule['cap'];
                        $entitle = $classification_rule['entitlement'];
                    }else{
                        $capped = 0;
                    }
                    //calculate how many initial leaves
                    $probation_date = $this->get_probation_date($user_id);
                    $remaining_cal_months = 12 - $probation_date->format('n');
                    if($probation_date->format('Y') < $year){
                        //if employee probation date is previous year, month is automatic 12
                        $remaining_cal_months = 12;
                    }elseif($probation_date->format('j') <= 15 ){ //15 is the day, first half of the month
                        $remaining_cal_months += 1;
                    }
                    //$total = round( ($remaining_cal_months / 12) * $entitle );

                    //now count the number of year the employee has been in the company add it to the total
                    /*$date_of_stay = date_diff($date_now, $employee_joining_date);
                    $total += $date_of_stay->y;

                    //Set the limit of the calculated leave
                    if($total >= $capped){
                        $total = $capped;
                    }*/
                    
                    $emp = $this->get_user_complete_info( $user_id );
                    $leveltype = $emp->basic_info->wphrm_employee_level;
                    $leave_entitlement = $wpdb->get_row("SELECT * FROM  $this->WphrmEmployeeLevelTable WHERE years_of_service = $date_of_stay->y");
                    if( $leveltype == 'senior_manager' ) {
                        $max_leave = $leave_entitlement->senior_manager;
                    } elseif( $leveltype == 'manager' ) {
                        $max_leave = $leave_entitlement->manager;
                    } elseif( $leveltype == 'supervisor' ) {
                        $max_leave = $leave_entitlement->supervisor;
                    } elseif( $leveltype == 'staff' ) {
                        $max_leave = $leave_entitlement->staff;
                    }
                    $total = $max_leave;

                    //get the user unused annual leave from last year and add it here if approved
                    $annual_leave_history = isset($wphrmEmployeeInfo['wphrm_employee_leave_carried']) ? $wphrmEmployeeInfo['wphrm_employee_leave_carried'] : false;
                    if(
                        isset($annual_leave_history[$year-1]) && isset($annual_leave_history[$year-1]['can_carry_over'])
                        && filter_var($annual_leave_history[$year-1]['can_carry_over'], FILTER_VALIDATE_BOOLEAN)
                        && !empty($annual_leave_history[$year-1]['count'])
                    ){
                        $total = $max_leave + $annual_leave_history[$year-1]['count'];
                    }

                }

                break;
            case 'medical_leave':
                {
                    //check if the employee is regular, if not he/she is not eligible for this leaeve then return 0
                    if(!$this->is_employee_regular($user_id)){
                        return 0;
                    }

                    //get the info for employee and his/her cap limit
                    if(isset($leave_type->numberOfLeave) && $leave_type->numberOfLeave){
                        $capped = $leave_type->numberOfLeave;
                    }else{
                        $capped = 14;
                    }

                    $start_month = $employee_joining_date->format('Y');
                    if($start_month < date('Y')){
                        $start_month = 1; // he/she joined last year, so we start this year
                    }else{
                        $start_month = $employee_joining_date->format('m');
                    }

                    //calculate how many initial leaves
                    $probation_date = $this->get_probation_date($user_id);
                    $remaining_cal_months = 12 - $probation_date->format('n');
                    if($probation_date->format('Y') < $year){
                        //if employee probation date is previous year, month is automatic 12
                        $remaining_cal_months = 12;
                    }elseif($probation_date->format('j') <= 15 ){ //15 is the day, first half of the month
                        $remaining_cal_months += 1;
                    }
                    $total = round( ($remaining_cal_months / 12) * $capped );

                    if($total >= $capped){
                        $total = $capped;
                    }

                }
                break;

            case 'hospitality_leave':
                {
                    //check if the employee is regular, if not he/she is not eligible for this leaeve then return 0
                    if(!$this->is_employee_regular($user_id)){
                        return 0;
                    }

                    $hospitalization_days = !empty($leave_type->numberOfLeave) ? $leave_type->numberOfLeave : 60;
                    $total = $hospitalization_days;
                }
                break;

            case 'maternity_leave':
                {

                    $total = empty($leave_type->numberOfLeave) ? 0 : $leave_type->numberOfLeave;
                }
                break;

            case 'regular_employee_leave':
                {
                    //check if the employee is regular, if not he/she is not eligible for this leaeve then return 0
                    if(!$this->is_employee_regular($user_id)){
                        $total = 0;
                    }else{
                        $total = empty($leave_type->numberOfLeave) ? 0 : $leave_type->numberOfLeave;
                    }
                }
                break;

            case 'year_of_service':
                {
                    $service_period = date_diff($employee_joining_date, $date_now);
                    if($service_period->y >= 1) {
                        $total = empty($leave_type->numberOfLeave) ? 0 : $leave_type->numberOfLeave;
                    }else{
                        $total = 0;
                    }
                }
                break;

            case 'national_service_leave':
                {
                    $gender = $wphrmEmployeeInfo['wphrm_employee_gender'];
                    if( $this->is_employee_regular($user_id) && (strcasecmp($gender, 'Male') === 0 || strcasecmp($gender, 'Male') === 0) ){
                        $total = empty($leave_type->numberOfLeave) ? 0 : $leave_type->numberOfLeave;
                    }else{
                        $total = 0;
                    }
                }
                break;

            default:
                $total = empty($leave_type->numberOfLeave) ? 0 : $leave_type->numberOfLeave;
        }
        return $total;
    }

    function is_employee_regular($user_id){
        return $this->user_probation_period_ended($user_id);
    }

    function user_probation_period_ended($user_id){
        global $wpdb, $wphrm;
        $pass = false;
        $probation_period_months = 3;
        $probation_notice_period_months = 1;
        $probation_notice_period_duration_months = 1;

        $user_wphrmEmployeeInfo = $wphrm->WPHRMGetUserDatas($user_id, 'wphrmEmployeeInfo');
        $wphrm_employee_probation_period = empty($user_wphrmEmployeeInfo['wphrm_employee_probation_period']) ? $probation_period_months : $user_wphrmEmployeeInfo['wphrm_employee_probation_period'];
        if( ( is_array($user_wphrmEmployeeInfo)
             && !empty($user_wphrmEmployeeInfo['wphrm_employee_joining_date'])
             /*&& ( current_time('timestamp', true) >= strtotime( '+'. $wphrm_employee_probation_period .' months'. $user_wphrmEmployeeInfo['wphrm_employee_joining_date']) )*/ ) ){

            //get
            $date_str_now = date("c", current_time('timestamp'));
            $now = new DateTime($date_str_now);
            $date_of_employment = date_create( $user_wphrmEmployeeInfo['wphrm_employee_joining_date'] );
            $date_def = date_diff($now, $date_of_employment);

            $months = ($date_def->y * 12) + $date_def->m;

            if($months >= $wphrm_employee_probation_period){
                $pass  = true;
            }
            //$pass  = true;
        }
        return $pass;
    }

    function get_employment_period($user_id, $to_date = null){
        $wphrmEmployeeBasicInfo = (object)$this->WPHRMGetUserDatas($user_id, 'wphrmEmployeeInfo');
        if($to_date){
            $date_str_now = $to_date;
        }else{
            $date_str_now = date("c", current_time('timestamp'));
        }
        $now = new DateTime($date_str_now);
        $date_of_employment = date_create($wphrmEmployeeBasicInfo->wphrm_employee_joining_date);
        $date_def = date_diff($now, $date_of_employment);
        return $date_def;
    }

    function get_probation_date($user_id){
        $wphrmEmployeeBasicInfo = $this->WPHRMGetUserDatas($user_id, 'wphrmEmployeeInfo');
        $date_of_employment = date_create( $wphrmEmployeeBasicInfo['wphrm_employee_joining_date'] );
        $probation_period_months = 3;
        $wphrm_employee_probation_period = empty($user_wphrmEmployeeInfo['wphrm_employee_probation_period']) ? $probation_period_months : $user_wphrmEmployeeInfo['wphrm_employee_probation_period'];
        $probation_date = $date_of_employment->add( new DateInterval('P'.$probation_period_months.'M'));
        return $probation_date;
    }

    function get_attendance_info($user_id, $year, $month_from = 1, $month_to = 12) {
        global $wpdb;
        $year = esc_sql($year);
        $from = esc_sql($year . '-' . $month_from . '-' . '01'); // esc
        $to = esc_sql($year . '-' . $month_to . '-' . '31'); // esc
        $currentDate = date('Y-m-d');
        if (strtotime($to) > strtotime($currentDate)) {
            $actualDate = $currentDate;
        } else {
            $actualDate = $to;
        }

        $days = 0;
        for($i = $month_from; $i <= $month_to ; $i++){
            $days += cal_days_in_month(CAL_GREGORIAN, $i, $year);
        }

        $result = array();
        $present = array();

        //get the list of holidays
        $query_1 = "select * from $this->WphrmHolidaysTable where `wphrmDate` between '$from' AND '$to'";
        $employeeHolidays = $wpdb->get_results($query_1);

        //get the list of attendances
        $query_2 = "select * from $this->WphrmAttendanceTable where `employeeID` = $user_id and `date` between '$from' AND '$actualDate'";
        $employeeAttendance = $wpdb->get_results($query_2);

        //deduct holiday on working days
        if (!empty($employeeHolidays)) {
            $holidaysReports = count($employeeHolidays);
            $workingday = $days - $holidaysReports;
        } else {
            $workingday = $days;
        }

        $leaves = array();
        $leaves_types = array();

        //count the attendances of the employee here
        foreach ($employeeAttendance as $employee_attendancekey => $employeeAttendances) {
            if ($employeeAttendances->status == 'present' && $employeeAttendances->halfDayType != 'halfday') {
                $present[] = $employeeAttendances->status;
            }
            if ($employeeAttendances->status == 'absent') {
                $absents[] = $employeeAttendances->status;
            }
            if ($employeeAttendances->halfDayType == 'halfday') {
                $halfday[] = $employeeAttendances->status;
            }

            //count leaves
            //$leaves_types[] = $employee_attendancekey;
            $leaves[ "$employeeAttendances->leaveType" ] = $employeeAttendances;
        }

        //count all half day present
        if (!empty($halfday)) {
            $halfdays = count($halfday);
        } else {
            $halfdays = 0;
        }

        //count half day absent
        if (!empty($halfday)) {
            $halfdaysAbsent = count($halfday);
        } else {
            $halfdaysAbsent = 0;
        }

        //absents + halfday/2
        if (!empty($absents)) {
            $halfdaysAbsent = $halfdaysAbsent / 2;
            $absent = $halfdaysAbsent + count($absents);
        } else {
            $halfdaysAbsent = $halfdaysAbsent / 2;
            $absent = $halfdaysAbsent + 0;
        }

        if (!empty($present)) {
            $presents = count($present);
            $halfdays = $halfdays / 2;
            $presents = $halfdays + count($present);
        } else {
            $halfdays = $halfdays / 2;
            $presents = $halfdays + 0;
        }


        $PerReport = ($presents * 100 ) / $workingday;
        $result['user_id'] = $user_id;
        $result['Working'] = '' . $workingday . '/' . $presents . '';
        $result['PerReport'] = bcdiv($PerReport, 1, 2) . ' %';
        $result['workingDays'] = $workingday;
        $result['absent'] = $absent;
        $result['present'] = $presents;
        //$result['leaves_types'] = $leaves_types;
        $result['leaves'] = $leaves;
        $result['result_count'] = count($employeeAttendance);
        //$result['query'] = $query_2;
        return $result;

    }

    function calculate_unused_annual_leave(){
        global $wphrm;
        $response = array( 'status' => false);
        $year = empty($_POST['year']) ? '' : $_POST['year'];
        $user_id = empty($_POST['user_id']) ? '' : $_POST['user_id'];

        $data= $this->get_attendance_info($user_id, $year);
        $response['data'] = $data;
        $response['post_data'] = $_POST;
        
        $annual_leave_id = 'Annual Leave';
        $wphrmEmployeeInfo = $wphrm->WPHRMGetUserDatas($user_id, 'wphrmEmployeeInfo');
        $annual_leave_history = isset($wphrmEmployeeInfo['wphrm_employee_leave_carried']) ? $wphrmEmployeeInfo['wphrm_employee_leave_carried'] : false;
        if(isset($annual_leave_history[$year-1]) && isset($annual_leave_history[$year-1]['can_carry_over']) && filter_var($annual_leave_history[$year-1]['can_carry_over'], FILTER_VALIDATE_BOOLEAN) && !empty($annual_leave_history[$year-1]['count'])){
            $carried = $annual_leave_history[$year-1]['count'];
        }

        $max_leave = $this->get_employee_max_leave($user_id, 'annual_leave', null, $year);
        $used = isset($data['leaves'][$annual_leave_id]) ? count($data['leaves'][$annual_leave_id]) : 0 ;
        echo $year."\n";
        if( $carried && $carried > 0 ) { 
            echo 'Balance brought forward: '. $carried ." days\n"; 
        }
        echo 'Year Max Leave: '.$max_leave." days\n";
        echo 'Used Leave: '. $used ."\n";
        echo 'Unused Leave: '. ($max_leave - $used);

        //echo json_encode($response);
        exit;
    }

    function get_medical_claim($user_id){
        $response = array(
            'mc_eligible' => false,
            'es_eligible' => false,
            'mc_used' => 0,
            'mc_total' => 0,
            'es_used' => 0,
            'es_total' => 0,
        );

        //check if the employee is regular
        if(!$this->is_employee_regular($user_id)) return $response;

        $user_info = $this->get_user_complete_info($user_id);
        $emp_level = $user_info->basic_info->wphrm_employee_level;
        $employee_type = $this->get_employee_type($emp_level);
        //$employee_attendances = $this->get_attendance_info($user_id, date('Y'));

        $response['mc_eligible'] = true;
        $response['mc_used'] = $this->get_used_medical_claim($user_id, date('Y'));
        $response['mc_total'] = $employee_type['entitle_medical_claim'];

        //calculate the age of the employee base on DOB
        $date_of_birth = $user_info->basic_info->wphrm_employee_bod;
        $age = 0;
        if($date_of_birth){
            $date_of_birth = new DateTime($date_of_birth);
            $date_now = new DateTime(date('c'));
            $date_difference = date_diff($date_of_birth, $date_now);
            $age = $date_difference->y;
        }

        if($age >= 55 && $employee_type['entitle_elderly_screening'] > 0){
            $response['es_eligible'] = true;
            $response['es_used'] = $this->get_used_elderly_screening($user_id, date('Y'));
            $response['es_total'] = $employee_type['entitle_elderly_screening'];
        }

        return $response;
    }

    function get_used_medical_claim($user_id, $year){
        global $wpdb;
        $amount = $wpdb->get_var("SELECT SUM(medical_claim_amount) FROM $this->WphrmAttendanceTable WHERE employeeID = '$user_id' AND applicationStatus = 'approved' ");
        return $amount ? $amount : 0;
    }
    function get_used_elderly_screening($user_id, $year){
        global $wpdb;
        $amount = $wpdb->get_var("SELECT SUM(elderly_screening_amount) FROM $this->WphrmAttendanceTable WHERE employeeID = '$user_id' AND applicationStatus = 'approved' ");
        return $amount ? $amount : 0;
    }

    function get_total_leave_used($leave_total, $leave_type, $user_id){
        global $wpdb;
        if($leave_type->leave_rules == 'hospitality_leave'){
            $year_start_date = date('Y-01-01');
            $year_end_date = date('Y-12-31');
            $leave_id_that_use_medical_rules = $wpdb->get_results("SELECT * FROM $this->WphrmLeaveTypeTable WHERE leave_rules = 'medical_leave' ");
            $medical_leave_used = 0;
            foreach($leave_id_that_use_medical_rules as $lt){
                $medical_leave_used += $this->get_used_leave($user_id, $lt->id);
            }
            $leave_total = $leave_total + $medical_leave_used;
        }
        return $leave_total;
    }

    function employee_allowed_leaves($user_id){
        $basic_info = $this->WPHRMGetUserDatas( $user_id, 'wphrmEmployeeInfo' );
        $leaves = isset($basic_info['wphrm_employee_entitled_leave']) ? $basic_info['wphrm_employee_entitled_leave'] : array();
        return $leaves;
    }
    function get_used_leave($user_id, $leave_id, $year = null, $month_start = null, $month_end = null){
        global $wpdb;
        $year = $year ? $year : date('Y');
        $year_start_date = date($year.'-01-01');
        $year_end_date = date($year.'-12-31');

        $leaveday_count = 0.0;
        $employee_leaves = $wpdb->get_results("SELECT * FROM $this->WphrmLeaveApplicationTable WHERE `date` BETWEEN '$year_start_date' AND '$year_end_date' AND `employeeID` ='" . $user_id . "' AND `leaveType` = '$leave_id' AND `applicationStatus`='approved'");

        foreach($employee_leaves as $leave){
            $this_leave_count = 0.0;
            $from_date = new DateTime($leave->date.' 00:00:00');
            $to_date = new DateTime($leave->toDate.' 24:00:00');
            $leave_internvel = date_diff($from_date, $to_date);
            //count the number of days of their leave
            $this_leave_count = $leave_internvel->d;
            $half_day_leave_type = explode(',', $leave->halfDayType);

            //check if the leave is just for one day, and make sure this is whole day
            if($leave->toDate == '0000-00-00'){
                $this_leave_count = 1;
            }elseif( $leave->halfDayType == '0,0' && ($leave->toDate == '0000-00-00' or $leave_internvel->days == 1) ){
                $this_leave_count = 1;
            }

            //check if the first day is leave
            if(isset($half_day_leave_type[0]) && $half_day_leave_type[0] == 1){
                $this_leave_count -= 0.5;
            }
            //check if the last day is leave
            if(isset($half_day_leave_type[1]) && $half_day_leave_type[1] == 1 && ($leave->toDate != '0000-00-00' && $leave_internvel->days > 1)){
                $this_leave_count -= 0.5;
            }
            $leaveday_count += $this_leave_count;
        }
        return $leaveday_count;
    }

    function wphrm_get_leave_limit(){
        global $wpdb;
        global $current_user;
        $response = array('status' => false, 'max_leave' => '');
        $leave_id = $_REQUEST['wphrm_leavetyped'];
        if($leave_id){
            $response['status'] = true;
            $leave_type = $this->get_leave_info($leave_id);
            $max_leave = $this->get_employee_max_leave($current_user->ID, $leave_type->leave_rules, $leave_type);
            $used_leave = $this->get_used_leave($current_user->ID, $leave_id);
            $count = isset($attendances['leaves']["$leave_id"]) ? count($attendances['leaves']["$leave_id"]) : 0;
            $response['leave_rule'] = $attendances;
            $response['max_leave_html'] = $used_leave.'/'.$max_leave;
            $response['used_leave'] = $used_leave;
            $response['max_leave'] = $max_leave;
            $response['total_leave_left'] = $max_leave - $used_leave;
        
        
            if (isset($_POST['startDate'])) {
                $startDate = date('Y-m-d', strtotime($_POST['startDate']));
            }
            if (isset($_POST['endDate'])) {
                $endDate = date('Y-m-d', strtotime($_POST['endDate']));
            }
            $dateBetweenArray = $this->createDateRangeArray($startDate, $endDate);
            $employee_holidays = $wpdb->get_results("select `wphrmDate` from $this->WphrmHolidaysTable");
            if (!empty($employee_holidays)) {
                foreach ($employee_holidays as $employee_holiday) {
                    if (isset($employee_holiday->wphrmDate)) {
                        $holidayDatas[] = $employee_holiday->wphrmDate;
                    }
                }
            }
            $totaldate = count($dateBetweenArray);
            foreach ($dateBetweenArray as $dateBetweenDate) {
                if (in_array($dateBetweenDate, $holidayDatas)) {
                    $dateDatails[] = $dateBetweenDate;
                }
            }
            $finalHoliday = count($dateDatails);
            $finalDate = ($totaldate - $finalHoliday);

            $halfDay = ($fromtype + $totype);
            $finalDate = ($finalDate - $halfDay);

            if ($finalDate == 0) {
                $lday_count = $finalDate;
            } else if ($finalDate == 1) {
                $lday_count = $finalDate;
            } else {
                $lday_count = $finalDate;
            }
            $response['toLeave'] = $lday_count;


        }
        echo json_encode($response);
        exit;
    }

    function get_leave_application_info($leave_id, $column = null){
        global $wpdb;

        if($leave_id == 0 or empty($leave_id)) return false;
        $x = $wpdb->get_row("SELECT * FROM $this->WphrmLeaveApplicationTable WHERE id = '$leave_id' ");
        if($column != null){
            return $x->$column;
        }else{
            return $x;
        }
    }

    function wphrm_remove_weekends(){
        global $wpdb;
        $response = array('status' => false);
        $year = empty($_POST['wphrmyear']) ? '' : $_POST['wphrmyear'];
        $month = empty($_POST['wphrmmonth']) ? '' : $_POST['wphrmmonth'];
        $weekend = empty($_POST['wphrmWeekend']) ? '' : $_POST['wphrmWeekend'];
        if($year && $weekend){
            if($month){
                $date_start = $year.'-'.$month.'-01';
                $date_end = $year.'-'.$month.'-31';
            }else{
                $date_start = $year.'-01-01';
                $date_end = $year.'-12-31';
            }

            $count = $wpdb->query("DELETE FROM $this->WphrmHolidaysTable WHERE wphrmDate >= '$date_start' AND wphrmDate >= '$date_end' AND type ='weekend' "  );
            if($count){
                $response['status'] = true;
            }
        }
        echo json_encode($response);
        exit;
    }

    function wphrm_add_new_user_leave_type($leave_type_id){
        global $wpdb;
        $employee_list = $this->WPHRMGetAllEmployees();
        foreach($employee_list as $employee){
            $wphrmEmployeeInfo = $this->WPHRMGetUserDatas( $employee->ID, 'wphrmEmployeeInfo' );
            if($wphrmEmployeeInfo && isset($wphrmEmployeeInfo['wphrm_employee_entitled_leave']) && is_array($wphrmEmployeeInfo['wphrm_employee_entitled_leave'])){
                $wphrmEmployeeInfo['wphrm_employee_entitled_leave'][] = $leave_type_id;
                $wphrmFormDetailsData = base64_encode(serialize($wphrmEmployeeInfo));
                update_user_meta($employee->ID, "wphrmEmployeeInfo", $wphrmFormDetailsData);
            }
        }
    }

    function get_leave_disabled_dates($leave_id = null, $user_id = null, $format = 'm/d/Y'){
        global $wpdb;
        $date_array = array();
        $employee_holidays = $wpdb->get_results("SELECT wphrmDate FROM $this->WphrmHolidaysTable ", ARRAY_N);
        $employee_attendance = $wpdb->get_results("SELECT date FROM $this->WphrmAttendanceTable WHERE `employeeID` = '$user_id' ", ARRAY_N);
        $date_arr = array_merge($employee_holidays, $employee_attendance);
        foreach($date_arr as $date){
            $date_array[] = date($format, strtotime($date[0]));
        }

        $notice_period = $this->get_leave_info($leave_id, 'notice_period');
        if((int)$notice_period > 0){
            $current_date = date($format, current_time('timestamp'));
            $end_date = date($format, strtotime("+$notice_period day"));
            while (strtotime($current_date) <= strtotime($end_date)){
                $date_array[] = $current_date;
                $current_date = date($format, strtotime("+1 day", strtotime($current_date)));
            }
        }
        return $date_array;
    }

    function wphrm_disable_leave_dates(){
        global $wpdb, $current_user;
        $response = array( 'status' => false );
        $leave_id = empty($_REQUEST['leave_id']) ? '' : $_REQUEST['leave_id'];
        $format = 'm/d/Y';

        $response['disable_dates'] = array();
        $response['disable_dates'] = $this->get_leave_disabled_dates($leave_id, $current_user->ID, $format);

        $response['status'] = true;
        echo json_encode($response);
        exit;
    }

    public function WPHRMSendEmail2($wphrmEmployeeID, $action, $data = array()) {
        $message_details = array(
            'email' => '',
            'subject' => '',
            'message' => '',
            'headers' => '',
        );

        $URL = admin_url();
        $user_ids = array();
        if(is_array($wphrmEmployeeID)){
            $user_ids = $wphrmEmployeeID;
        }elseif(is_numeric($wphrmEmployeeID)){
            $user_ids = array($wphrmEmployeeID);
        }else{
            $user_ids = explode(',', $wphrmEmployeeID);
        }

        $wphrmGeneralSettingsInfo = $this->WPHRMGetSettings('wphrmGeneralSettingsInfo');
        $address = (isset($wphrmGeneralSettingsInfo['wphrm_company_address'])) ? esc_textarea($wphrmGeneralSettingsInfo['wphrm_company_address']) : '';

        foreach($user_ids as $user_id){
            $userDetails = $this->WPHRMUserDetails($user_id);
            $email_setting = get_option('wphrm_email_notifications', array());
            if (isset($email_setting[$action])){
                $message_details['subject'] = $email_setting[$action]['subject'];
                $body = $email_setting[$action]['content'];

                //replaces the placeholders
                $types = $this->get_email_notification_types();
                if(isset($types[$action]) && isset($data['replacement'])){
                    foreach($types[$action]['placeholder'] as $placeholder){
                        $key = str_replace('%', '', $placeholder);
                        if(isset($data['replacement'][$key])){
                            $body = str_replace($placeholder, $data['replacement'][$key], $body);
                        }
                    }
                }

                $email_template = file_get_contents(dirname(__FILE__).'/jaf_extend/partials/email-template.html', true);
                if($email_template){
                    $body = str_replace(
                        array('{{header}}',
                              '{{body}}',
                              '{{footer}}'),
                        array(
                            '',
                            nl2br($body),
                            $address),
                        $email_template
                    );
                }
                $message_details['message'] = $body;

                if(isset($userDetails['useremail'])){
                    $message_details['email'] = $userDetails['useremail'];
                }else{
                    $user_info = get_userdata($user_id);
                    $message_details['email']= $user_info->user_email;
                }
                $fromName = get_bloginfo('name');
                $fromEamil = get_bloginfo('admin_email');
            }

            $message_details['headers'] = "MIME-Version: 1.0\n" . "From: " . $fromName . " <" . $fromEamil . ">\n";
            $message_details['headers'] .= "Content-type:text/html;charset=UTF-8" . "\r\n";

            $message_details = apply_filters('wphrm_before_email_send', $message_details, $action, $user_id, $data);
            wp_mail($message_details['email'], $message_details['subject'], $message_details['message'], $message_details['headers']);
        }
    }

    function get_email_notification_types(){
        global $wpdb;
        $types = array(
            'employee_leave_approved' => array(
                'title' => 'Employee Leave Approved',
                'role' => array('employee'),
                'placeholder' => array( '%employee_name%', '%leave_date_from%', '%leave_date_to%' ),
            ),
            'employee_leave_rejected' => array(
                'title' => 'Employee Leave Rejected',
                'role' => array('employee'),
                'placeholder' => array( '%employee_name%', '%leave_date_from%', '%leave_date_to%' ),
            ),
            'employee_leave_application' => array(
                'title' => 'Employee Leave Application',
                'role' => array('employee'),
                'placeholder' => array( '%employee_name%', '%leave_date_from%', '%leave_date_to%' ),
            ),

            'employee_created' => array(
                'title' => 'Employee Account Created',
                'role' => array('employee'),
                'placeholder' => array( '%first_name%', '%last_name%', '%email%', '%user_id%', '%password%', '%site_url%' ),
            ),

            'hr_leave_application' => array(
                'title' => 'HR/Admin Leave Application Recieved',
                'role' => array('hr_manager', 'administrator'),
                'placeholder' => array( '%employee_name%', '%leave_date_from%', '%leave_date_to%' ),
            ),
            'hr_leave_application_approved' => array(
                'title' => 'HR/Admin Leave Application Approved',
                'role' => array('hr_manager', 'administrator'),
                'placeholder' => array( '%employee_name%', '%leave_date_from%', '%leave_date_to%' ),
            ),
        );

        $email_setting = $wpdb->get_var("SELECT settingValue FROM $this->WphrmSettingsTable WHERE settingKey = 'wphrmEmailNoticaitons' AND authorID = 'system_notification' ");
        if(empty($email_setting)){

        }else{

        }
        ksort($types);
        return $types;
    }

    function wphrm_get_email_notification_details(){
        global $wpdb;
        $response = array('status' => false);
        $response['notification_variable_html'] = 'None';
        $notification_key = empty($_REQUEST['email_notification_key']) ? '' : $_REQUEST['email_notification_key'];
        $types = $this->get_email_notification_types();
        $response['notification'] = array(
            'subject' => '',
            'content' => '',
        );
        if($notification_key && isset($types[$notification_key])){
            $response['notification_variable_html'] = '';
            foreach($types[$notification_key]['placeholder'] as $key => $placeholder){
                //$replacement = $types[$notification_key]['replacement'][$key];
                $response['notification_variable_html'] .= "$placeholder ";
            }
            //$email_setting = $wpdb->get_var("SELECT settingValue FROM $this->WphrmSettingsTable WHERE settingKey = 'wphrmEmailNoticaitons' AND authorID = 'system_notification' ");
            $email_setting = get_option('wphrm_email_notifications', array());
            if(!empty($email_setting) && is_array($email_setting)){
                if(isset($email_setting[$notification_key])){
                    $response['notification'] = array(
                        'subject' => $email_setting[$notification_key]['subject'],
                        'content' => $email_setting[$notification_key]['content'],
                    );
                }
            }
            $response['status'] = true;
        }
        echo json_encode($response);
        exit;
    }
    function wphrm_update_email_notication(){
        global $wpdb;
        $response = array('status' => false);
        $notification_key = empty($_REQUEST['email-notification-type']) ? '' : $_REQUEST['email-notification-type'];
        $notification_subject = empty($_REQUEST['email_notification_subject']) ? '' : $_REQUEST['email_notification_subject'];
        $notification_content = empty($_REQUEST['email_notification_content']) ? '' : $_REQUEST['email_notification_content'];
        if($notification_key){
            $email_setting = get_option('wphrm_email_notifications', array());
            if($email_setting){
                $email_setting[$notification_key] = array(
                    'subject' => $notification_subject,
                    'content' => $notification_content
                );
                update_option('wphrm_email_notifications', $email_setting);
                $response['status'] = true;
                $response['type'] = 'updated';
            }else{
                //the setting is not existing, create a new one
                $new_settings = array(
                    $notification_key => array(
                        'subject' => $notification_subject,
                        'content' => $notification_content
                    )
                );
                update_option('wphrm_email_notifications', $new_settings);
                $response['status'] = true;
                $response['type'] = 'added';
            }
        }
        echo json_encode($response);
        exit;
    }

    function WPHRMBulkDelete() {
        global $wpdb;
        $response = array();
        $toDelete = empty( $_REQUEST['bulk_delete_notifications'] ) ? array() : $_REQUEST['bulk_delete_notifications'];
        $ids = explode( ',', $toDelete );
        foreach( $toDelete as $id ) {
            $result = $wpdb->query("DELETE FROM `$this->WphrmNotificationsTable` WHERE `id`= '$id'");
        }
        if( $result ) {
            $response['success'] = true;
        } else {
            $response['success'] = false;
        }
        
        echo json_encode($response);
        exit;
    }
    function WPHRMBulkDelete2() {
        global $wpdb;
        $response = array();
        $toDelete = empty( $_REQUEST['bulk_delete_designation'] ) ? array() : $_REQUEST['bulk_delete_designation'];
        $ids = explode( ',', $toDelete );
        foreach( $toDelete as $id ) {
            $result = $wpdb->query("DELETE FROM `$this->WphrmDesignationTable` WHERE `designationID`= '$id'");
        }
        if( $result ) {
            $response['success'] = true;
        } else {
            $response['success'] = false;
        }
        
        echo json_encode($response);
        exit;
    }

    function custom_script_for_admin(){

    }

}

/** Create Object For WPHRM function. * */
$wphrm = new WPHRM();
/** Ajax Calling function. * */
add_action('wp_ajax_WPHRMEmployeeDetails', array(&$wphrm, 'WPHRMEmployeeDetails'));
add_action('wp_ajax_nopriv_WPHRMEmployeeDetails', array(&$wphrm, 'WPHRMEmployeeDetails'));
add_action('wp_ajax_WPHRMLeaveRule', array(&$wphrm, 'WPHRMLeaveRule'));
add_action('wp_ajax_nopriv_WPHRMLeaveRule', array(&$wphrm, 'WPHRMLeaveRule'));
add_action('wp_ajax_WPHRMEmployeeBasicInfo', array(&$wphrm, 'WPHRMEmployeeBasicInfo'));
add_action('wp_ajax_nopriv_WPHRMEmployeeBasicInfo', array(&$wphrm, 'WPHRMEmployeeBasicInfo'));
add_action('wp_ajax_WPHRMEmployeeDocumentInfo', array(&$wphrm, 'WPHRMEmployeeDocumentInfo'));
add_action('wp_ajax_nopriv_WPHRMEmployeeDocumentInfo', array(&$wphrm, 'WPHRMEmployeeDocumentInfo'));
add_action('wp_ajax_WPHRMEmployeeSalaryInfo', array(&$wphrm, 'WPHRMEmployeeSalaryInfo'));
add_action('wp_ajax_nopriv_WPHRMEmployeeSalaryInfo', array(&$wphrm, 'WPHRMEmployeeSalaryInfo'));
add_action('wp_ajax_WPHRMEmployeeBankInfo', array(&$wphrm, 'WPHRMEmployeeBankInfo'));
add_action('wp_ajax_nopriv_WPHRMEmployeeBankInfo', array(&$wphrm, 'WPHRMEmployeeBankInfo'));
add_action('wp_ajax_WPHRMEmployeeOtherInfo', array(&$wphrm, 'WPHRMEmployeeOtherInfo'));
add_action('wp_ajax_nopriv_WPHRMEmployeeOtherInfo', array(&$wphrm, 'WPHRMEmployeeOtherInfo'));
add_action('wp_ajax_WPHRMDepartmentInfo', array(&$wphrm, 'WPHRMDepartmentInfo'));
add_action('wp_ajax_nopriv_WPHRMDepartmentInfo', array(&$wphrm, 'WPHRMDepartmentInfo'));
add_action('wp_ajax_WPHRMDesignationInfo', array(&$wphrm, 'WPHRMDesignationInfo'));
add_action('wp_ajax_nopriv_WPHRMDesignationInfo', array(&$wphrm, 'WPHRMDesignationInfo'));
add_action('wp_ajax_WPHRMHolidayMonthWise', array(&$wphrm, 'WPHRMHolidayMonthWise'));
add_action('wp_ajax_nopriv_WPHRMHolidayMonthWise', array(&$wphrm, 'WPHRMHolidayMonthWise'));
add_action('wp_ajax_WPHRMHolidayYearWise', array(&$wphrm, 'WPHRMHolidayYearWise'));
add_action('wp_ajax_nopriv_WPHRMHolidayYearWise', array(&$wphrm, 'WPHRMHolidayYearWise'));
add_action('wp_ajax_WPHRMCustomDelete', array(&$wphrm, 'WPHRMCustomDelete'));
add_action('wp_ajax_nopriv_WPHRMCustomDelete', array(&$wphrm, 'WPHRMCustomDelete'));
add_action('wp_ajax_WPHRMDesignationAjax', array(&$wphrm, 'WPHRMDesignationAjax'));
add_action('wp_ajax_nopriv_WPHRMDesignationAjax', array(&$wphrm, 'WPHRMDesignationAjax'));
add_action('wp_ajax_WPHRMAddHolidays', array(&$wphrm, 'WPHRMAddHolidays'));
add_action('wp_ajax_nopriv_WPHRMAddHolidays', array(&$wphrm, 'WPHRMAddHolidays'));
add_action('wp_ajax_WPHRMEmployeeAttendanceMark', array(&$wphrm, 'WPHRMEmployeeAttendanceMark'));
add_action('wp_ajax_nopriv_WPHRMEmployeeAttendanceMark', array(&$wphrm, 'WPHRMEmployeeAttendanceMark'));
add_action('wp_ajax_WPHRMEmployeeAttendanceData', array(&$wphrm, 'WPHRMEmployeeAttendanceData'));
add_action('wp_ajax_nopriv_WPHRMEmployeeAttendanceData', array(&$wphrm, 'WPHRMEmployeeAttendanceData'));

/*J@F*/
add_action('wp_ajax_WPHRMCompanyNoticeData', array(&$wphrm, 'WPHRMCompanyNoticeData'));
add_action('wp_ajax_nopriv_WPHRMCompanyNoticeData', array(&$wphrm, 'WPHRMCompanyNoticeData'));
add_action('wp_ajax_WPHRMDepartmentNoticeData', array(&$wphrm, 'WPHRMDepartmentNoticeData'));
add_action('wp_ajax_nopriv_WPHRMDepartmentNoticeData', array(&$wphrm, 'WPHRMDepartmentNoticeData'));
/*J@F*/

add_action('wp_ajax_WPHRMEmployeeAttendanceReports', array(&$wphrm, 'WPHRMEmployeeAttendanceReports'));
add_action('wp_ajax_nopriv_WPHRMEmployeeAttendanceReports', array(&$wphrm, 'WPHRMEmployeeAttendanceReports'));
add_action('wp_ajax_WPHRMLeaveEntitlement', array(&$wphrm, 'WPHRMLeaveEntitlement'));
add_action('wp_ajax_nopriv_WPHRMLeaveEntitlement', array(&$wphrm, 'WPHRMLeaveEntitlement'));
add_action('wp_ajax_WPHRMLeavetypeInfo', array(&$wphrm, 'WPHRMLeavetypeInfo'));
add_action('wp_ajax_nopriv_WPHRMLeavetypeInfo', array(&$wphrm, 'WPHRMLeavetypeInfo'));
add_action('wp_ajax_WPHRMLeaveApplicationsInfo', array(&$wphrm, 'WPHRMLeaveApplicationsInfo'));
add_action('wp_ajax_nopriv_WPHRMLeaveApplicationsInfo', array(&$wphrm, 'WPHRMLeaveApplicationsInfo'));
add_action('wp_ajax_WPHRMAjaxCalenderLoad', array(&$wphrm, 'WPHRMAjaxCalenderLoad'));
add_action('wp_ajax_nopriv_WPHRMAjaxCalenderLoad', array(&$wphrm, 'WPHRMAjaxCalenderLoad'));
add_action('wp_ajax_WPHRMEarningSalary', array(&$wphrm, 'WPHRMEarningSalary'));
add_action('wp_ajax_nopriv_WPHRMEarningSalary', array(&$wphrm, 'WPHRMEarningSalary'));
add_action('wp_ajax_WPHRMDeductionSalary', array(&$wphrm, 'WPHRMDeductionSalary'));
add_action('wp_ajax_nopriv_WPHRMDeductionSalary', array(&$wphrm, 'WPHRMDeductionSalary'));
add_action('wp_ajax_WPHRMRemoveEarning', array(&$wphrm, 'WPHRMRemoveEarning'));
add_action('wp_ajax_nopriv_WPHRMRemoveEarning', array(&$wphrm, 'WPHRMRemoveEarning'));
add_action('wp_ajax_WPHRMGenerateSalary', array(&$wphrm, 'WPHRMGenerateSalary'));
add_action('wp_ajax_nopriv_WPHRMGenerateSalary', array(&$wphrm, 'WPHRMGenerateSalary'));
add_action('wp_ajax_WPHRMRemoveSalarySlip', array(&$wphrm, 'WPHRMRemoveSalarySlip'));
add_action('wp_ajax_nopriv_WPHRMRemoveSalarySlip', array(&$wphrm, 'WPHRMRemoveSalarySlip'));
add_action('wp_ajax_WPHRMGeneralSettingsInfo', array(&$wphrm, 'WPHRMGeneralSettingsInfo'));
add_action('wp_ajax_nopriv_WPHRMGeneralSettingsInfo', array(&$wphrm, 'WPHRMGeneralSettingsInfo'));
add_action('wp_ajax_WPHRMChangePasswordInfo', array(&$wphrm, 'WPHRMChangePasswordInfo'));
add_action('wp_ajax_nopriv_WPHRMChangePasswordInfo', array(&$wphrm, 'WPHRMChangePasswordInfo'));
add_action('wp_ajax_WPHRMSalarySlipInfo', array(&$wphrm, 'WPHRMSalarySlipInfo'));
add_action('wp_ajax_nopriv_WPHRMSalarySlipInfo', array(&$wphrm, 'WPHRMSalarySlipInfo'));
add_action('wp_ajax_WPHRMNotificationsSettingsInfo', array(&$wphrm, 'WPHRMNotificationsSettingsInfo'));
add_action('wp_ajax_nopriv_WPHRMNotificationsSettingsInfo', array(&$wphrm, 'WPHRMNotificationsSettingsInfo'));
add_action('wp_ajax_WPHRMHideShowEmployeeSectionInfo', array(&$wphrm, 'WPHRMHideShowEmployeeSectionInfo'));
add_action('wp_ajax_nopriv_WPHRMHideShowEmployeeSectionInfo', array(&$wphrm, 'WPHRMHideShowEmployeeSectionInfo'));
add_action('wp_ajax_WPHRMNoticeInfo', array(&$wphrm, 'WPHRMNoticeInfo'));
add_action('wp_ajax_nopriv_WPHRMNoticeInfo', array(&$wphrm, 'WPHRMNoticeInfo'));
add_action('wp_ajax_WPHRMUserPermissionInfo', array(&$wphrm, 'WPHRMUserPermissionInfo'));
add_action('wp_ajax_nopriv_WPHRMUserPermissionInfo', array(&$wphrm, 'WPHRMUserPermissionInfo'));
add_action('wp_ajax_WPHRMPagePermissionInfo', array(&$wphrm, 'WPHRMPagePermissionInfo'));
add_action('wp_ajax_nopriv_WPHRMPagePermissionInfo', array(&$wphrm, 'WPHRMPagePermissionInfo'));
add_action('wp_ajax_WPHRMUserLeaveApplicationsInfo', array(&$wphrm, 'WPHRMUserLeaveApplicationsInfo'));
add_action('wp_ajax_nopriv_WPHRMUserLeaveApplicationsInfo', array(&$wphrm, 'WPHRMUserLeaveApplicationsInfo'));
add_action('wp_ajax_WPHRMAllMessagesInfo', array(&$wphrm, 'WPHRMAllMessagesInfo'));
add_action('wp_ajax_nopriv_WPHRMAllMessagesInfo', array(&$wphrm, 'WPHRMAllMessagesInfo'));
add_action('wp_ajax_WPHRMFinancialsInfo', array(&$wphrm, 'WPHRMFinancialsInfo'));
add_action('wp_ajax_nopriv_WPHRMFinancialsInfo', array(&$wphrm, 'WPHRMFinancialsInfo'));
add_action('wp_ajax_WPHRMNotificationInfo', array(&$wphrm, 'WPHRMNotificationInfo'));
add_action('wp_ajax_nopriv_WPHRMNotificationInfo', array(&$wphrm, 'WPHRMNotificationInfo'));
add_action('wp_ajax_WPHRMNotificationStatusChangeInfo', array(&$wphrm, 'WPHRMNotificationStatusChangeInfo'));
add_action('wp_ajax_nopriv_WPHRMNotificationStatusChangeInfo', array(&$wphrm, 'WPHRMNotificationStatusChangeInfo'));
add_action('wp_ajax_WPHRMAjaxFinancialGraphLoad', array(&$wphrm, 'WPHRMAjaxFinancialGraphLoad'));
add_action('wp_ajax_nopriv_WPHRMAjaxFinancialGraphLoad', array(&$wphrm, 'WPHRMAjaxFinancialGraphLoad'));
add_action('wp_ajax_WPHRMExpenseReportInfo', array(&$wphrm, 'WPHRMExpenseReportInfo'));
add_action('wp_ajax_nopriv_WPHRMExpenseReportInfo', array(&$wphrm, 'WPHRMExpenseReportInfo'));
add_action('wp_ajax_WPHRMSalaryRequest', array(&$wphrm, 'WPHRMSalaryRequest'));
add_action('wp_ajax_nopriv_WPHRMSalaryRequest', array(&$wphrm, 'WPHRMSalaryRequest'));
add_action('wp_ajax_WPHRMwphrmAddyearInWeekendInfo', array(&$wphrm, 'WPHRMwphrmAddyearInWeekendInfo'));
add_action('wp_ajax_nopriv_WPHRMwphrmAddyearInWeekendInfo', array(&$wphrm, 'WPHRMwphrmAddyearInWeekendInfo'));
add_action('wp_ajax_wphrmAddEarningLabelInfo', array(&$wphrm, 'wphrmAddEarningLabelInfo'));
add_action('wp_ajax_nopriv_wphrmAddEarningLabelInfo', array(&$wphrm, 'wphrmAddEarningLabelInfo'));
add_action('wp_ajax_wphrmAddBnakDetailsLabelInfo', array(&$wphrm, 'wphrmAddBnakDetailsLabelInfo'));
add_action('wp_ajax_nopriv_wphrmAddBnakDetailsLabelInfo', array(&$wphrm, 'wphrmAddBnakDetailsLabelInfo'));
add_action('wp_ajax_wphrmAddOtherDetailsLabelInfo', array(&$wphrm, 'wphrmAddOtherDetailsLabelInfo'));
add_action('wp_ajax_nopriv_wphrmAddOtherDetailsLabelInfo', array(&$wphrm, 'wphrmAddOtherDetailsLabelInfo'));
add_action('wp_ajax_wphrmSalaryDetailsFieldsSettingsinfo', array(&$wphrm, 'wphrmSalaryDetailsFieldsSettingsinfo'));
add_action('wp_ajax_nopriv_wphrmSalaryDetailsFieldsSettingsinfo', array(&$wphrm, 'wphrmSalaryDetailsFieldsSettingsinfo'));
add_action('wp_ajax_WphrmSalarySlipDuplicate', array(&$wphrm, 'WphrmSalarySlipDuplicate'));
add_action('wp_ajax_nopriv_WphrmSalarySlipDuplicate', array(&$wphrm, 'WphrmSalarySlipDuplicate'));
add_action('wp_ajax_WPHRMSalaryMonthAjax', array(&$wphrm, 'WPHRMSalaryMonthAjax'));
add_action('wp_ajax_nopriv_WPHRMSalaryMonthAjax', array(&$wphrm, 'WPHRMSalaryMonthAjax'));
add_action('wp_ajax_wphrsalaryDayOrHourlyInfo', array(&$wphrm, 'wphrsalaryDayOrHourlyInfo'));
add_action('wp_ajax_nopriv_wphrsalaryDayOrHourlyInfo', array(&$wphrm, 'wphrsalaryDayOrHourlyInfo'));

add_action('wp_ajax_wphrmaddcurrencysettingsinfo', array(&$wphrm, 'wphrmaddcurrencysettingsinfo'));
add_action('wp_ajax_nopriv_wphrmaddcurrencysettingsinfo', array(&$wphrm, 'wphrmaddcurrencysettingsinfo'));

add_action('wp_ajax_WPHRMMarkAttendanceBulk', array(&$wphrm, 'WPHRMMarkAttendanceBulk'));
add_action('wp_ajax_nopriv_WPHRMMarkAttendanceBulk', array(&$wphrm, 'WPHRMMarkAttendanceBulk'));

add_action('wp_ajax_WPHRMLoadCurrencyData', array(&$wphrm, 'WPHRMLoadCurrencyData'));
add_action('wp_ajax_nopriv_WPHRMLoadCurrencyData', array(&$wphrm, 'WPHRMLoadCurrencyData'));
add_action('wp_ajax_WPHRMSalaryGenerationSettings', array(&$wphrm, 'WPHRMSalaryGenerationSettings'));
add_action('wp_ajax_nopriv_WPHRMSalaryGenerationSettings', array(&$wphrm, 'WPHRMSalaryGenerationSettings'));

add_action('wp_ajax_WPHRMImportXml', array(&$wphrm, 'WPHRMImportXml'));
add_action('wp_ajax_nopriv_WPHRMImportXml', array(&$wphrm, 'WPHRMImportXml'));
add_action('wp_ajax_WPHRMCheckHolidayBetweenDate', array(&$wphrm, 'WPHRMCheckHolidayBetweenDate'));
add_action('wp_ajax_nopriv_WPHRMCheckHolidayBetweenDate', array(&$wphrm, 'WPHRMCheckHolidayBetweenDate'));

add_action('wp_ajax_wphrmAddDocumentsfieldslebalInfo', array(&$wphrm, 'WPHRMAddDocumentsfieldslebalInfo'));
add_action('wp_ajax_nopriv_wphrmAddDocumentsfieldslebalInfo', array(&$wphrm, 'WPHRMAddDocumentsfieldslebalInfo'));
add_action('wp_ajax_WPHRMAddRoles', array(&$wphrm, 'WPHRMAddRoles'));
add_action('wp_ajax_WPHRMAddRoles', array(&$wphrm, 'WPHRMAddRoles'));
add_action('wp_ajax_wphrmRoleWiseCapabilityGet', array(&$wphrm, 'wphrmRoleWiseCapabilityGet'));
add_action('wp_ajax_wphrmRoleWiseCapabilityGet', array(&$wphrm, 'wphrmRoleWiseCapabilityGet'));
add_action('wp_ajax_wphrmGetDefaultRolePages', array(&$wphrm, 'WPHRMGetDefaultRolePages'));
add_action('wp_ajax_wphrmGetDefaultRolePages', array(&$wphrm, 'WPHRMGetDefaultRolePages'));
add_action('wp_ajax_WPHRMChangeRoleWiseDisplay', array(&$wphrm, 'WPHRMChangeRoleWiseDisplay'));
add_action('wp_ajax_WPHRMChangeRoleWiseDisplay', array(&$wphrm, 'WPHRMChangeRoleWiseDisplay'));
add_action('wp_ajax_WPHRMAutomaticAttendanceInfo', array(&$wphrm, 'WPHRMAutomaticAttendanceInfo'));
add_action('wp_ajax_WPHRMAutomaticAttendanceInfo', array(&$wphrm, 'WPHRMAutomaticAttendanceInfo'));
add_action('wp_ajax_WPHRMGetRoles', array(&$wphrm, 'WPHRMGetRoles'));
add_action('wp_ajax_WPHRMGetRoles', array(&$wphrm, 'WPHRMGetRoles'));
add_action('wp_ajax_WPHRMRemoveSalaryWeekSlip', array(&$wphrm, 'WPHRMRemoveSalaryWeekSlip'));
add_action('wp_ajax_WPHRMRemoveSalaryWeekSlip', array(&$wphrm, 'WPHRMRemoveSalaryWeekSlip'));
add_action('wp_ajax_WPHRMGenerateWeekSalary', array(&$wphrm, 'WPHRMGenerateWeekSalary'));
add_action('wp_ajax_WPHRMGenerateWeekSalary', array(&$wphrm, 'WPHRMGenerateWeekSalary'));
add_action('wp_ajax_WPHRMSalaryWeekMonthAjax', array(&$wphrm, 'WPHRMSalaryWeekMonthAjax'));
add_action('wp_ajax_WPHRMSalaryWeekMonthAjax', array(&$wphrm, 'WPHRMSalaryWeekMonthAjax'));
add_action('wp_ajax_WPHRMSalaryWeekMonthGetAjax', array(&$wphrm, 'WPHRMSalaryWeekMonthGetAjax'));
add_action('wp_ajax_WPHRMSalaryWeekMonthGetAjax', array(&$wphrm, 'WPHRMSalaryWeekMonthGetAjax'));

add_action('wp_ajax_WPHRMSalaryWeekRequest', array(&$wphrm, 'WPHRMSalaryWeekRequest'));
add_action('wp_ajax_nopriv_WPHRMSalaryWeekRequest', array(&$wphrm, 'WPHRMSalaryWeekRequest'));

/*J@F*/
add_action('wp_ajax_WPHRMEmployeeFamilyInfo', array(&$wphrm, 'WPHRMEmployeeFamilyInfo'));
add_action('wp_ajax_nopriv_WPHRMEmployeeFamilyInfo', array(&$wphrm, 'WPHRMEmployeeFamilyInfo'));

add_action('wp_ajax_confirmNoticeInvitation', array(&$wphrm, 'confirmNoticeInvitation'));
/*J@F END*/

add_action('wp_ajax_WPHRMBulkDelete', array(&$wphrm, 'WPHRMBulkDelete'));
add_action('wp_ajax_WPHRMBulkDelete2', array(&$wphrm, 'WPHRMBulkDelete2'));
/*RTY END*/


if (!function_exists('WPHRMAddActionLink')) {
    add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'WPHRMAddActionLink', 10, 2);

    function WPHRMAddActionLink($actions, $plugin_file) {
        $action_links = array();
        $action_links = array(
            'forum' => array(
                'label' => __('Support', 'wphrm'),
                'url' => 'https://indigothemes.com/support',
            ),
            'docs' => array(
                'label' => __('Documentation', 'wphrm'),
                'url' => 'https://indigothemes.com/documentation/wphrm/',
            ),
            'settings' => array(
                'label' => __('Settings', 'wphrm'),
                'url' => admin_url('admin.php?page=wphrm-settings', __FILE__),
            ),
        );

        unset($actions['edit']);
        return WPHRMActionLinks($actions, $plugin_file, $action_links, 'before');
    }
}

if (!function_exists('WPHRMActionLinks')) {
    function WPHRMActionLinks($actions, $plugin_file, $action_links = array(), $position = 'after') {
        static $plugin;
        if (!isset($plugin)) {
            $plugin = plugin_basename(__FILE__);
        }
        if ($plugin === $plugin_file && !empty($action_links)) {
            foreach ($action_links as $key => $value) {
                $link = array($key => '<a href="' . $value['url'] . '">' . $value['label'] . '</a>');
                if ('after' === $position) {
                    $actions = array_merge($actions, $link);
                } else {
                    $actions = array_merge($link, $actions);
                }
            }//foreach
        }// if
        return $actions;
    }
}

if(!function_exists('jaf_get_medical_leave_rulas')){
    function jaf_get_medical_leave_rulas(){
        $employee_levels = array(
            'sm3_sm1' => array( 'title' => 'Level 1 Senior Management (SM-III to SM-I)', 'entitlement' => 21, 'cap' => 25 ),
            'm3_m1' => array( 'title' => 'Level 2 Management (M-III to M-I)', 'entitlement' => 18, 'cap' => 21 ),
            'ne2_e1' => array( 'title' => 'Level 3 Executive & Supervisor (NE-II to E-I)', 'entitlement' => 14, 'cap' => 18 ),
            'ne3' => array( 'title' => 'Level 4 Warehouse/Driver/Technician(NE-III)', 'entitlement' => 7, 'cap' => 14 ),
        );

        return $employee_levels;
    }
}
/*J@F*/
require_once('jaf_extend/class/class.annual_leave.php');
require_once('jaf_extend/jaf_wphrm_extend.php');
/*J@F*/