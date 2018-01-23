<?php

add_action('jaf_dashboard_before_calendar', 'add_leave_summary');
function add_leave_summary(){
    if(!current_user_can('administrator')) return;

    global $wpdb;

    wp_enqueue_style('fontawesome47');
    wp_enqueue_style('jaf-wphrm-css');

    $sick_leave_type = 'Sick Leave';

    $attendance_table = $wpdb->prefix.'wphrm_attendance';
    $leave_table = $wpdb->prefix.'wphrm_leave_application';

    //Total Present Employees
    $total_present_today = $wpdb->get_var("SELECT Count(*) FROM $attendance_table WHERE DATE(date) = DATE(NOW()) AND status = 'present' ");
    $total_present_week = $wpdb->get_var("SELECT Count(*) FROM $attendance_table WHERE WEEK(date) = WEEK(NOW()) AND status = 'present' ");
    $total_present_month = $wpdb->get_var("SELECT Count(*) FROM $attendance_table WHERE MONTH(date) = MONTH(NOW()) AND status = 'present' ");
    $total_present_year = $wpdb->get_var("SELECT Count(*) FROM $attendance_table WHERE YEAR(date) = YEAR(NOW()) AND status = 'present' ");

    //Total Absent Employees
    $total_absent_today = $wpdb->get_var("SELECT Count(*) FROM $attendance_table WHERE DATE(date) = DATE(NOW()) AND status = 'absent'");
    $total_absent_week = $wpdb->get_var("SELECT Count(*) FROM $attendance_table WHERE WEEK(date) = WEEK(NOW()) AND status = 'absent'");
    $total_absent_month = $wpdb->get_var("SELECT Count(*) FROM $attendance_table WHERE MONTH(date) = MONTH(NOW()) AND status = 'absent'");
    $total_absent_year = $wpdb->get_var("SELECT Count(*) FROM $attendance_table WHERE YEAR(date) = YEAR(NOW()) AND status = 'absent'");

    //Total Pending Leave Employees
    $total_pending_approval_today = $wpdb->get_var("SELECT Count(*) FROM $leave_table WHERE DATE(date) = DATE(NOW()) AND applicationStatus = 'pending' ");
    $total_pending_approval_week = $wpdb->get_var("SELECT Count(*) FROM $leave_table WHERE WEEK(date) = WEEK(NOW()) AND applicationStatus = 'pending' ");
    $total_pending_approval_month = $wpdb->get_var("SELECT Count(*) FROM $leave_table WHERE MONTH(date) = MONTH(NOW()) AND applicationStatus = 'pending' ");
    $total_pending_approval_year = $wpdb->get_var("SELECT Count(*) FROM $leave_table WHERE YEAR(date) = YEAR(NOW()) AND applicationStatus = 'pending' ");

    //Total Half day leave Employees
    $total_half_day_leave_today = $wpdb->get_var("SELECT Count(*) FROM $attendance_table WHERE DATE(date) = DATE(NOW()) AND status = 'absent' AND leaveType <> '' AND ( halfDayType = '1,0' OR halfDayType = '0,1' OR halfDayType = 'halfday' ) AND applicationStatus = 'approved' ");
    $total_half_day_leave_week = $wpdb->get_var("SELECT Count(*) FROM $attendance_table WHERE WEEK(date) = WEEK(NOW()) AND status = 'absent' AND leaveType <> '' AND ( halfDayType = '1,0' OR halfDayType = '0,1' OR halfDayType = 'halfday' ) AND applicationStatus = 'approved' ");
    $total_half_day_leave_month = $wpdb->get_var("SELECT Count(*) FROM $attendance_table WHERE MONTH(date) = MONTH(NOW()) AND status = 'absent' AND leaveType <> '' AND ( halfDayType = '1,0' OR halfDayType = '0,1' OR halfDayType = 'halfday' ) AND applicationStatus = 'approved' ");
    $total_half_day_leave_year = $wpdb->get_var("SELECT Count(*) FROM $attendance_table WHERE YEAR(date) = YEAR(NOW()) AND status = 'absent' AND leaveType <> '' AND ( halfDayType = '1,0' OR halfDayType = '0,1' OR halfDayType = 'halfday' ) AND applicationStatus = 'approved' ");

    //Total Onleave Employees
    $total_leave_today = $wpdb->get_var("SELECT Count(*) FROM $attendance_table WHERE DATE(date) = DATE(NOW()) AND status = 'absent' AND leaveType <> '' AND applicationStatus = 'approved' ");
    $total_leave_week = $wpdb->get_var("SELECT Count(*) FROM $attendance_table WHERE WEEK(date) = WEEK(NOW()) AND status = 'absent' AND leaveType <> '' AND applicationStatus = 'approved' ");
    $total_leave_month = $wpdb->get_var("SELECT Count(*) FROM $attendance_table WHERE MONTH(date) = MONTH(NOW()) AND status = 'absent' AND leaveType <> '' AND applicationStatus = 'approved' ");
    $total_leave_year = $wpdb->get_var("SELECT Count(*) FROM $attendance_table WHERE YEAR(date) = YEAR(NOW()) AND status = 'absent' AND leaveType <> '' AND applicationStatus = 'approved' ");

    //Total Sick/ Medical Leave Employees
    $total_medical_leave_today = $wpdb->get_var("SELECT Count(*) FROM $attendance_table WHERE DATE(date) = DATE(NOW()) AND status = 'absent' AND leaveType = '37' AND applicationStatus = 'approved' ||  DATE(date) = DATE(NOW()) AND status = 'absent' AND leaveType = '38' AND applicationStatus = 'approved' ||  DATE(date) = DATE(NOW()) AND status = 'absent' AND leaveType = '39' AND applicationStatus = 'approved' ||  DATE(date) = DATE(NOW()) AND status = 'absent' AND leaveType = '41' AND applicationStatus = 'approved' ||  DATE(date) = DATE(NOW()) AND status = 'absent' AND leaveType = '42' AND applicationStatus = 'approved' ||  DATE(date) = DATE(NOW()) AND status = 'absent' AND leaveType = '43' AND applicationStatus = 'approved' ||  DATE(date) = DATE(NOW()) AND status = 'absent' AND leaveType = '63' AND applicationStatus = 'approved' ||  DATE(date) = DATE(NOW()) AND status = 'absent' AND leaveType = '64' AND applicationStatus = 'approved' ");
    $total_medical_leave_week = $wpdb->get_var("SELECT Count(*) FROM $attendance_table WHERE WEEK(date) = WEEK(NOW()) AND status = 'absent' AND leaveType = '37' AND applicationStatus = 'approved' ||  WEEK(date) = WEEK(NOW()) AND status = 'absent' AND leaveType = '38' AND applicationStatus = 'approved' ||  WEEK(date) = WEEK(NOW()) AND status = 'absent' AND leaveType = '39' AND applicationStatus = 'approved' ||  WEEK(date) = WEEK(NOW()) AND status = 'absent' AND leaveType = '41' AND applicationStatus = 'approved' ||  WEEK(date) = WEEK(NOW()) AND status = 'absent' AND leaveType = '42' AND applicationStatus = 'approved' ||  WEEK(date) = WEEK(NOW()) AND status = 'absent' AND leaveType = '43' AND applicationStatus = 'approved' ||  WEEK(date) = WEEK(NOW()) AND status = 'absent' AND leaveType = '63' AND applicationStatus = 'approved' ||  WEEK(date) = WEEK(NOW()) AND status = 'absent' AND leaveType = '64' AND applicationStatus = 'approved' ");
    $total_medical_leave_month = $wpdb->get_var("SELECT Count(*) FROM $attendance_table WHERE MONTH(date) = MONTH(NOW()) AND status = 'absent' AND leaveType = '37' AND applicationStatus = 'approved' ||  MONTH(date) = MONTH(NOW()) AND status = 'absent' AND leaveType = '38' AND applicationStatus = 'approved' ||  MONTH(date) = MONTH(NOW()) AND status = 'absent' AND leaveType = '39' AND applicationStatus = 'approved' ||  MONTH(date) = MONTH(NOW()) AND status = 'absent' AND leaveType = '41' AND applicationStatus = 'approved' ||  MONTH(date) = MONTH(NOW()) AND status = 'absent' AND leaveType = '42' AND applicationStatus = 'approved' ||  MONTH(date) = MONTH(NOW()) AND status = 'absent' AND leaveType = '43' AND applicationStatus = 'approved' ||  MONTH(date) = MONTH(NOW()) AND status = 'absent' AND leaveType = '63' AND applicationStatus = 'approved' ||  MONTH(date) = MONTH(NOW()) AND status = 'absent' AND leaveType = '64' AND applicationStatus = 'approved' ");
    $total_medical_leave_year = $wpdb->get_var("SELECT Count(*) FROM $attendance_table WHERE YEAR(date) = YEAR(NOW()) AND status = 'absent' AND leaveType = '37' AND applicationStatus = 'approved' ||  YEAR(date) = YEAR(NOW()) AND status = 'absent' AND leaveType = '38' AND applicationStatus = 'approved' ||  YEAR(date) = YEAR(NOW()) AND status = 'absent' AND leaveType = '39' AND applicationStatus = 'approved' ||  YEAR(date) = YEAR(NOW()) AND status = 'absent' AND leaveType = '41' AND applicationStatus = 'approved' ||  YEAR(date) = YEAR(NOW()) AND status = 'absent' AND leaveType = '42' AND applicationStatus = 'approved' ||  YEAR(date) = YEAR(NOW()) AND status = 'absent' AND leaveType = '43' AND applicationStatus = 'approved' ||  YEAR(date) = YEAR(NOW()) AND status = 'absent' AND leaveType = '63' AND applicationStatus = 'approved' ||  YEAR(date) = YEAR(NOW()) AND status = 'absent' AND leaveType = '64' AND applicationStatus = 'approved' ");

    $link = admin_url('admin.php?page=wphrm-attendances');

    $header = array( '', 'Present', 'Absent', 'Half Day', 'Leave', 'Sick Leave', 'Pending approval' );
    $thead = '<thead><tr>';
    foreach($header as $head){
        $thead .= '<th style="text-align: center">'.$head.'</th>';
    }
    $thead .= '</tr></thead>';

    $tbody_data = array(
        array( 'Today', $total_present_today, $total_absent_today, $total_half_day_leave_today, $total_leave_today, $total_medical_leave_today, $total_pending_approval_today ),
        array( 'This week', $total_present_week, $total_absent_week, $total_half_day_leave_week, $total_leave_week, $total_medical_leave_week, $total_pending_approval_week ),
        array( 'This month', $total_present_month, $total_absent_month, $total_half_day_leave_month, $total_leave_month, $total_medical_leave_month, $total_pending_approval_month ),
        array( 'This year', $total_present_year, $total_absent_year, $total_half_day_leave_year, $total_leave_year, $total_medical_leave_year, $total_pending_approval_year ),
    );

    $tbody = '<tbody>';
    foreach($tbody_data as $data){
        $thead .= '<tr>
                        <th>'.$data[0].'</th>
                        <th style="text-align: center">'.$data[1].'</th>
                        <th style="text-align: center">'.$data[2].'</th>
                        <th style="text-align: center">'.$data[3].'</th>
                        <th style="text-align: center">'.$data[4].'</th>
                        <th style="text-align: center">'.$data[5].'</th>
                        <th style="text-align: center">'.$data[6].'</th>
                   </tr>';
    }
    $tbody .= '</tbody>';

    echo '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
             <a class="dashboard-stat dashboard-stat-light green compact" href="'.$link.'">
                <div class="visual"><i class="fa fa-calendar-check-o" aria-hidden="true"></i></div>
                    <div class="details"><div class="number">Attendance Summary</div></div>
                    <div class="row">
                        <div class="col-md-12 ">
                        <div class="panel">
                            <table class="table table-hover table-condensed table-striped">
                                '.$thead.'
                                '.$tbody.'
                            </table>
                        </div>
                    </div>
                </div>
            </a>
            </div>';
}

add_action('jaf_dashboard_before_calendar', 'add_probition_alert');
function add_probition_alert(){
    if(!current_user_can('administrator')) return;
    global $wpdb;

    wp_enqueue_style('fontawesome47');
    wp_enqueue_style('jaf-wphrm-css');

    $probation_period_months = 6;
    $probation_notice_period_months = 1;
    $probation_notice_period_duration_months = 1;

    $user_for_probation = '';
    $wphrmUsers =  get_users();
    $count = 0;
    if(!empty($wphrmUsers)) {
        foreach ($wphrmUsers as $key => $userdata) {
            $user_wphrmEmployeeInfo = get_user_meta( $userdata->ID, 'wphrmEmployeeInfo', true);
            if($user_wphrmEmployeeInfo){
                $user_wphrmEmployeeInfo = unserialize(base64_decode($user_wphrmEmployeeInfo));
            }else{
                continue;
            }
            //check if the user is going to end its probation period
            $wphrm_employee_probation_period = empty($user_wphrmEmployeeInfo['wphrm_employee_probation_period']) ? $probation_period_months : $user_wphrmEmployeeInfo['wphrm_employee_probation_period'];
            if( !( is_array($user_wphrmEmployeeInfo)
                  && !empty($user_wphrmEmployeeInfo['wphrm_employee_joining_date'])
                  && ( current_time('timestamp', true) > strtotime( '+'. $wphrm_employee_probation_period - $probation_notice_period_months .' months'. $user_wphrmEmployeeInfo['wphrm_employee_joining_date'])
                     && current_time('timestamp', true) <= strtotime( '+'. $wphrm_employee_probation_period + $probation_notice_period_duration_months.' months'. $user_wphrmEmployeeInfo['wphrm_employee_joining_date']) ) ) ){
                continue;
            }
            $count++;
            $wphrmEmployeeFirstName = get_user_meta($userdata->data->ID, 'first_name', true);
            $wphrmEmployeeLastName = get_user_meta($userdata->data->ID, 'last_name', true);
            $user_for_probation .= '<tr>
                <td>'.$count.'</td>
                <td>'.$wphrmEmployeeFirstName.' '.$wphrmEmployeeLastName.'</td>
                <td>'.date('M d, Y', strtotime($user_wphrmEmployeeInfo['wphrm_employee_joining_date'])).'</td>
                <td>'.date('M d, Y', strtotime( '+'. $wphrm_employee_probation_period .' months'. $user_wphrmEmployeeInfo['wphrm_employee_joining_date'])).'</td>
            </tr>';
        }
    }
    if($count == 0){
        $user_for_probation .= '<tr><td>None to today</td></tr>';
    }

    echo '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
             <a class="dashboard-stat dashboard-stat-light blue-soft compact" href="'.'#'.'">
                <div class="visual"><i class="fa fa-angle-double-up" aria-hidden="true"></i></div>
                    <div class="details"><div class="number">Probation Alert</div></div>
                    <div class="row">
                        <div class="col-md-12 ">
                            <div class="panel">
                                <table class="table table-hover table-condensed table-striped">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Name</th>
                                            <th>Date Joined</th>
                                            <th>Probation End</th>
                                        </tr>
                                    </thead>
                                        '.$user_for_probation.'
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>';
}



add_filter('wphrm_employee_bod', 'filter_wphrm_employee_bod');
function filter_wphrm_employee_bod($bod){
    $bod = date('M d', strtotime(esc_html($bod)));
    return $bod;
}
