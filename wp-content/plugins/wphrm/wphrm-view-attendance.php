<?php
if (!defined('ABSPATH'))
    exit;
wp_enqueue_style('wphrm-fullcalendar-css');
wp_enqueue_style('wphrm-bootstrap-select-css');
global $current_user, $wpdb;
$wphrmUserRole = implode(',', $current_user->roles);
$readonly_class = '';
$readonly = '';
$edit_mode = false;
$page = 'employees';
$employee_id = '';
if (isset($_REQUEST['employee_id']) && !empty($_REQUEST['employee_id'])) {
    $employee_id = $_REQUEST['employee_id'];
} else {
    $employee_id = $current_user->ID;
}
$wphrmEmployeeBasicInfo = get_user_meta($employee_id, 'wphrmEmployeeInfo', true);
$wphrmEmployeeBasicInfo = unserialize(base64_decode($wphrmEmployeeBasicInfo));
$wphrmGetPagePermissions = $this->WPHRMGetPagePermissions();
?>
<style>
    .fc-title {
        cursor: auto !important;
    }
</style>
<div class="preloader">
    <span class="preloader-custom-gif"></span>
</div>
<h3 class="page-title"><?php _e('Attendance', 'wphrm'); ?></h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li><i class="fa fa-home"></i><?php _e('Home', 'wphrm'); ?><i class="fa fa-angle-right"></i></li>
        <li><?php _e('Attendance of', 'wphrm'); ?></li>
        <li>
            <?php
            if (isset($wphrmEmployeeBasicInfo['wphrm_employee_fname'])) : echo esc_html($wphrmEmployeeBasicInfo['wphrm_employee_fname']);
            endif;
            if (isset($wphrmEmployeeBasicInfo['wphrm_employee_lname'])) : echo ' ' . esc_html($wphrmEmployeeBasicInfo['wphrm_employee_lname']);
            endif;
            ?>
        </li>
    </ul>
</div>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div style="padding-left: 0px; padding-right:20px; padding-top:0px;" class="col-md-12">
    <div class="row">
        <input type="hidden" id="employee_id" value="<?php echo esc_attr($employee_id); ?>">
        <div class="col-md-12">
            <?php if (in_array('manageOptionsAttendances', $wphrmGetPagePermissions)) { ?>
                <a class="btn green " href="?page=wphrm-attendances"><i class="fa fa-arrow-left"></i><?php _e('Back', 'wphrm'); ?></a>
            <?php } ?>
            
            <ul class="nav nav-tabs">
                <li><a href="<?php echo site_url("/wp-admin/admin.php?page=wphrm-leaves-application") ?>">Leave Requests</a></li>
                <li class="active"><a data-toggle="tab" href="#attendance_management">Attendance Management</a></li>
                <?php if (in_array('manageOptionsAttendances', $wphrmGetPagePermissions)) { ?>
                    <li><a href="<?php echo site_url("/wp-admin/admin.php?page=wphrm-leave-type") ?>">Leave Types Management</a></li>
                    <li><a href="<?php echo site_url("/wp-admin/admin.php?page=wphrm-leave-rules") ?>">Leave Rules Management</a></li>
                    <li><a href="<?php echo site_url("/wp-admin/admin.php?page=wphrm-leaves-application") ?>&annual=1">Annual Leave Management</a></li>
                <?php } ?>
            </ul>
            
            <div class="tab-content">
                <div id="attendance_management" class="tab-pane fade in active">
            
                    <div class="portlet box blue calendar">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-calendar"></i><?php
                                if (isset($wphrmEmployeeBasicInfo['wphrm_employee_fname'])) : echo esc_html($wphrmEmployeeBasicInfo['wphrm_employee_fname']);
                                endif;
                                if (isset($wphrmEmployeeBasicInfo['wphrm_employee_lname'])) : echo ' ' . esc_html($wphrmEmployeeBasicInfo['wphrm_employee_lname']);
                                endif;
                                ?>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="portlet-body">
                                <div class="row">
                                    <div class="col-md-4 col-sm-12">
                                        <form action="#" class="form-horizontal form-row-sepe">
                                            <div class="form-body">
                                                <h3><?php _e('Search Attendance', 'wphrm'); ?></h3>
                                                <?php if (in_array('manageOptionsAttendances', $wphrmGetPagePermissions)) { ?>
                                                    <div class="form-group">
                                                        <div class="col-md-10">
                                                            <h4><?php _e('Select Employee', 'wphrm'); ?></h4>
                                                            <select class="form-control input-large select2me" data-placeholder="<?php _e('Select Employee', 'wphrm'); ?>..." onchange="redirect_to()" id="changeEmployee" name="employeeID">
                                                                <?php
                                                                $wphrmUserRole = implode(',', $current_user->roles);
                                                                $wphrmUsers = get_users($wphrmUserRole);
                                                                foreach ($wphrmUsers as $key => $userdata) {
                                                                    foreach ($userdata->roles as $role => $roles) {
                                                                        if ($roles != 'administrator') {
                                                                            $wphrmEmployeeBasicInfos = get_user_meta($userdata->ID, 'wphrmEmployeeInfo', true);
                                                                            $wphrmEmployeeBasicInfoss = unserialize(base64_decode($wphrmEmployeeBasicInfos));
                                                                            if (isset($wphrmEmployeeBasicInfoss['wphrm_employee_status']) && $wphrmEmployeeBasicInfoss['wphrm_employee_status'] == 'Active') {
                                                                                ?>

                                                                                <option value="<?php echo esc_attr($userdata->ID); ?>" <?php
                                                                                if ($employee_id == $userdata->ID) {
                                                                                    echo esc_attr('selected ="selected"');
                                                                                }
                                                                                ?>><?php
                                                                                            if (isset($wphrmEmployeeBasicInfoss['wphrm_employee_fname'])) : echo esc_html($wphrmEmployeeBasicInfoss['wphrm_employee_fname']);
                                                                                            endif;
                                                                                            if (isset($wphrmEmployeeBasicInfoss['wphrm_employee_lname'])) : echo ' ' . esc_html($wphrmEmployeeBasicInfoss['wphrm_employee_lname']);
                                                                                            endif;
                                                                                            ?></option>
                                                                                <?php
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                <div class="form-group">
                                                    <div class="col-md-10">
                                                        <h4><?php _e('Month', 'wphrm'); ?></h4>
                                                        <select class ="form-control select2me"  data-live-search="true" id="monthSelect"  name="forMonth" onchange="changeMonthYear();return false;">
                                                            <?php
                                                            $month_array = $this->WPHRMGetMonths();
                                                            $current_month = date("m");
                                                            foreach ($month_array as $monthkey => $month_arrays) {
                                                                ?>
                                                                <option value="<?php echo esc_attr($monthkey); ?>" <?php
                                                                if ($current_month == $monthkey) {
                                                                    echo esc_attr('selected ="selected"');
                                                                }
                                                                ?>><?php echo esc_html($month_arrays); ?></option>
                                                                    <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-md-10">
                                                        <h4>Year</h4>
                                                        <select class ="form-control select2me"  data-live-search="true" id="yearSelect" name="forMonth" onchange="changeMonthYear();return false;">
                                                            <?php
                                                            if (isset($wphrmEmployeeBasicInfo['wphrm_employee_joining_date'])) :
                                                                $joining_year = date("Y", strtotime($wphrmEmployeeBasicInfo['wphrm_employee_joining_date']));
                                                                $current_year = date("Y");
                                                                $years_array = range($current_year, $joining_year);
                                                            else :
                                                                $current_year = date("Y");
                                                                $years_array = range($current_year, 1960);
                                                            endif;
                                                            foreach ($years_array as $years_key => $years_arrays) {
                                                                ?>
                                                                <option value="<?php echo esc_attr($years_arrays); ?>" <?php
                                                                if ($current_year == $years_arrays) {
                                                                    echo esc_attr('selected ="selected"');
                                                                }
                                                                ?>><?php echo esc_html($years_arrays); ?></option>
                                                                    <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-10">
                                                        <div class="alert alert-danger text-center">
                                                            <table class="leaves" style="margin-left: 50px;">
                                                                <tr>
                                                                    <td style="text-align: left;">
                                                                        <strong><?php _e('Total Working Days - ', 'wphrm'); ?> <font id="attendanceworkingday"> </font></strong>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="text-align: left;">
                                                                        <strong><?php _e('Total Present - ', 'wphrm'); ?> <font id="attendancePersent"> </font></strong>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="text-align: left;">
                                                                        <strong><?php _e('Total Absent - ', 'wphrm'); ?> <font id="attendanceAbsent"> </font></strong>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="text-align: left;">
                                                                        <strong title="Leave used before current date"><?php _e('Total Leave ? - ', 'wphrm'); ?> <font id="attendanceLeave"> </font></strong>
                                                                    </td>
                                                                </tr>

                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>                <!--/span-->
                                                <div class="row">
                                                    <div class="col-md-10">
                                                        <div class="alert alert-danger text-center">
                                                            <strong><?php _e('Attendance', 'wphrm'); ?> %</strong>
                                                            <div id="attendancePerReport"><?php _e('NA', 'wphrm'); ?> </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>

                                        <?php
                                        $attendance_date = esc_sql(date('Y-m-d')); // esc
                                        $todaydate = esc_sql('0'); // esc
                                        $employeeAttendanceCount = esc_sql('0'); // esc
                                        $currentMonth = date('m'); // esc

                                        $wphrmEmployeeInfo = $this->WPHRMGetUserDatas($employee_id, 'wphrmEmployeeInfo');

                                        if (isset($wphrmEmployeeInfo['wphrm_employee_status']) && $wphrmEmployeeInfo['wphrm_employee_status'] == 'Active') {
                                            $getAttendancebyId = $wpdb->get_row("select * from $this->WphrmAttendanceTable where `date` = '" . $attendance_date . "' and `employeeID` ='" . $employee_id . "'");
                                            $lastAbsent = $wpdb->get_results("select * from $this->WphrmAttendanceTable where  `employeeID` ='" . $employee_id . "' and `status` = 'absent' and `date` <= '$attendance_date' order by id desc");
                                            if (!empty($lastAbsent)) {
                                                if ($lastAbsent[0]->date == $attendance_date) {
                                                    $todaydate = 'today';
                                                } else {
                                                    $now = time(); // or your date as well
                                                    $yourDate = strtotime($lastAbsent[0]->date);
                                                    $datediff = $now - $yourDate;
                                                    $beforeday = floor($datediff / (60 * 60 * 24));
                                                    $todaydate = 'Before : ' . $beforeday . ' Day';
                                                }
                                            } else {
                                                $todaydate = '0';
                                            }
                                            ?>

                                            <!--<div class="row">
                                                <div class="col-md-10">
                                                    <div class="alert alert-danger text-center">

                                                        <?php
                                                        if (isset($wphrmEmployeeInfo['wphrm_employee_joining_date'])) {
                                                            $wphrmEmployeeJoiningDate = $wphrmEmployeeInfo['wphrm_employee_joining_date'];
                                                        }
                                                        $wphrmEmployeeJoiningDate = new DateTime($wphrmEmployeeJoiningDate);
                                                        $today = new DateTime();
                                                        $interval = $today->diff($wphrmEmployeeJoiningDate);
                                                        $wphrmEmployeeJoiningToCurrentTotalYear = ((int) $interval->format('%y years') + 1);
                                                        $curQuarter = ceil($currentMonth / 3);
                                                        $leavesTypes = $wpdb->get_results("SELECT * FROM $this->WphrmLeaveTypeTable ORDER BY leaveType ASC");
                                                        foreach ($leavesTypes as $leavesType) {
                                                            if(!in_array($leavesType->id, $this->employee_allowed_leaves($employee_id))) continue;

                                                            $totalNoOfLeave = 0;
                                                            if ($leavesType->period == 'Monthly') {
                                                                $totalNoOfLeave = intval($leavesType->numberOfLeave * $currentMonth);
                                                            } else if ($leavesType->period == 'Quarterly') {
                                                                $totalNoOfLeave = intval($leavesType->numberOfLeave * $curQuarter);
                                                            } else if ($leavesType->period == 'Yearly') {
                                                                $totalNoOfLeave = intval($leavesType->numberOfLeave * $wphrmEmployeeJoiningToCurrentTotalYear);
                                                            }

                                                            if( in_array($leavesType->leave_rules, array_keys($this->get_leave_rules()) ) ){
                                                                $totalNoOfLeave = $this->get_employee_max_leave($employee_id, $leavesType->leave_rules, $leavesType);
                                                            }

                                                            $employeeLeaves = $wpdb->get_row("SELECT COUNT(id) AS leaveCounter FROM $this->WphrmAttendanceTable WHERE `status`='absent' AND `employeeID` ='" . $employee_id . "' AND `leaveType`='$leavesType->leaveType' AND `date` <= '$attendance_date' AND `applicationStatus`='approved'");
                                                            $employeeLeavesHalfday = $wpdb->get_row("SELECT COUNT(id) AS halfdayCounter FROM $this->WphrmAttendanceTable WHERE `halfDayType`='halfday' AND `employeeID` ='" . $employee_id . "' AND `leaveType`='$leavesType->leaveType' AND `date` <= '$attendance_date' AND `applicationStatus`='approved'");

                                                            $halfdayCounter = ($employeeLeavesHalfday->halfdayCounter / 2);
                                                            $leaveTotal = $employeeLeaves->leaveCounter + $halfdayCounter;

                                                            $leaveTotal = $this->get_used_leave( $employee_id, $leavesType->id );
                                                            $leaveTotal = apply_filters('employee_attendance_after_calculate_leave_total', $leaveTotal, $leavesType, $employee_id);

                                                            $leaveRemaining = ($totalNoOfLeave - $leaveTotal);
                                                            if($leaveRemaining >0){
                                                                $leaveRemaining = $leaveRemaining;
                                                            }else{
                                                                $leaveRemaining=0;
                                                            }
                                                                echo esc_html($leavesType->leaveType) . ' :';
                                                                 if ($totalNoOfLeave >= $leaveTotal) { ?>
                                                        <strong style="cursor: pointer;" title="Total : <?php echo esc_attr($totalNoOfLeave).' , '; ?> Taken : <?php echo esc_attr($leaveTotal).' , '; ?> Remaining : <?php echo esc_attr($leaveRemaining); ?>"><?php echo esc_html($leaveTotal) . '/' . $totalNoOfLeave . '<br>'; ?></strong>
                                                                    <?php } else { ?>
                                                                       <strong style="cursor: pointer;" title="Total : <?php echo esc_attr($totalNoOfLeave).' , '; ?> Taken : <?php echo esc_attr($leaveTotal).' , '; ?> Remaining : <?php echo esc_attr($leaveRemaining); ?>"><?php echo esc_html($leaveTotal) . '/' . $totalNoOfLeave . '<br>'; ?></strong>
                                                                    <?php } ?>
                                                                </tr>
                                                            <?php } ?>
                                                    </div> </div></div>-->
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <div class="col-md-8 col-sm-12">
                                        <div id="Attendance_Calendar" class="has-toolbar">
                                        </div>
                                    </div>
                                </div>
                                <!-- END CALENDAR PORTLET-->
                            </div>
                        </div>
                    </div>
                    <!-- protlet end -->
                </div>
            </div>  
            <!-- tab body end -->
        </div>
    </div>
</div>
<!-- END PAGE CONTENT-->
<?php
wp_enqueue_script('wphrm-bootstrap-select-js');
wp_enqueue_script('wphrm-fullcalender-js');
wp_enqueue_script('wphrm-attendance-calenader-js');
wp_localize_script('wphrm-attendance-calenader-js', 'WPHRMJS', array('ajaxurl'=>admin_url( 'admin-ajax.php' )));
?>
<script>
    jQuery(document).ready(function () {
        Attendance_Calendar.init();
        showReport();
    });
</script>
