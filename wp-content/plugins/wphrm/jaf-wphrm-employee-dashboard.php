<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $current_user, $wpdb, $wphrm;

$currentMonth = date('m');
$nextMonth = date('m', strtotime('first day of +1 month'));

$wphrmGeneralSettingsInfo = $this->WPHRMGetSettings('wphrmGeneralSettingsInfo');

$current_user_id = $current_user->ID;
$userInformation = get_userdata( $current_user_id );
$wphrmEmployeeBasicInfo = (object)$this->WPHRMGetUserDatas($current_user_id, 'wphrmEmployeeInfo');
$wphrmEmployeeDocumentsInfo = (object)$this->WPHRMGetUserDatas($current_user_id, 'wphrmEmployeeDocumentInfo');
$wphrmEmployeeSalaryInfo = $this->WPHRMGetUserDatas($current_user_id, 'wphrmEmployeeSalaryInfo');
$employee_complete_info = $this->get_user_complete_info($current_user_id);

if(!empty($wphrmEmployeeBasicInfo->wphrm_employee_designation)){
    $designation = $wpdb->get_var("SELECT designationName FROM  $this->WphrmDesignationTable WHERE designationID = $wphrmEmployeeBasicInfo->wphrm_employee_designation ");
    if($designation){
        $designation = unserialize(base64_decode($designation));
    }
}
$employee_managers =  get_users();

$days_work = '';
if(isset($wphrmEmployeeBasicInfo->wphrm_employee_joining_date)){
    $date_str_now = date("c", current_time('timestamp'));
    $now = new DateTime($date_str_now);
    $date_of_employment = date_create($wphrmEmployeeBasicInfo->wphrm_employee_joining_date);
    $date_def = date_diff($now, $date_of_employment);
    $days_work = sprintf(_n("%s year", "%s years", $date_def->y),$date_def->y ) . ' '. sprintf(_n("%s month", "%s months", $date_def->m),$date_def->m ) . ' ' . sprintf(_n("%s day", "%s days", $date_def->d), $date_def->d ) ;
}

//get attendance report
$attendance_info = $this->get_attendance_info($current_user->ID, date('Y'), date('m'));

//get next leave date
$next_approved_leave = $wpdb->get_row("SELECT * FROM $this->WphrmAttendanceTable Where employeeID = $current_user->ID AND date >= NOW() AND leaveType IS NOT NULL ORDER BY date ASC");

//get next leave date
$total_medical_claim = $wpdb->get_row("SELECT * FROM $this->WphrmAttendanceTable Where employeeID = $current_user->ID AND date >= NOW() AND leaveType IS NOT NULL ORDER BY date ASC");

//birthdays
$wphrmUsers = $this->WPHRMGetAllEmployees();
$datas = array();
foreach($wphrmUsers as $wphrmUser){
    if(!in_array('administrator', $wphrmUser->roles)){
        $datas[] = $wphrmUser->ID;
    }
}

$wphrmNotices = $wpdb->get_results("SELECT * FROM  $this->WphrmNoticeTable ORDER BY id DESC");
$wphrmNotices = apply_filters('wphrm_employee_notice_list', $wphrmNotices);

//holidays
$upcoming_holidays = $wpdb->get_results("SELECT * FROM $this->WphrmHolidaysTable WHERE wphrmDate > NOW() ORDER BY wphrmDate ASC LIMIT 0,5");

//last absent
$last_absent = $wpdb->get_row("SELECT * FROM $this->WphrmAttendanceTable WHERE employeeID = $current_user->ID AND status = 'absent' AND date <= NOW() ORDER BY date DESC");
$last_absent_ellapse = 0;
if($last_absent){
    $date_str_now = date("c", current_time('timestamp'));
    $now = new DateTime($date_str_now);
    $date_of_employment = date_create($last_absent->date);
    $date_def = date_diff($now, $date_of_employment);
    $last_absent_ellapse = sprintf(_n("%s day ago", "%s days ago", $date_def->y),$date_def->d );
}
?>



<div id="employee-dashboard-tab" class="employee-dashboard-tab col-md-12">
    <div class="panel panel-default">
        <div class="panel-body" >
            <div class="dashboard-title-bar" >
                <h2 class="pull-left">Simplex</h2>
                <div class="hide hidden"><?php var_dump($wphrmEmployeeBasicInfo); ?></div>
                <ul  class="nav nav-pills navbar-right">
                    <li class="active">
                        <a  href="#home" data-toggle="tab">HOME</a>
                    </li>
                    <li>
                        <a href="<?php echo admin_url('admin.php?page=wphrm-leaves-application'); ?>" data-toggle="tabx">LEAVE</a>
                    </li>
                    <li>
                        <a href="<?php echo admin_url('admin.php?page=wphrm-employee-view-details'); ?>" data-toggle="tabx">MY ACCOUNT</a>
                    </li>
                </ul>
                <div class="clearfix" ></div>
            </div>
            <div class="clearfix" ></div>
            <div class="tab-content clearfix">
                <div class="tab-pane active" id="home">
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <div class="panel panel-default inner-panel">
                                <div class="panel-body">

                                    <div class="profile-picture" >
                                        <?php if (isset($wphrmEmployeeBasicInfo->employee_profile) && $wphrmEmployeeBasicInfo->employee_profile != ''): ?>
                                        <img src="<?php echo (isset($wphrmEmployeeBasicInfo->employee_profile)) ?  esc_attr($wphrmEmployeeBasicInfo->employee_profile) : ''; ?>" width="200"><br>
                                        <?php else: ?>
                                        <?php  if (isset($wphrmEmployeeBasicInfo->wphrm_employee_gender) && $wphrmEmployeeBasicInfo->wphrm_employee_gender == 'Male'): ?>
                                        <img src="<?php echo esc_attr(plugins_url('assets/images/default-male.jpeg', __FILE__)); ?>" width="200">
                                        <?php else: ?>
                                        <img src="<?php echo esc_attr(plugins_url('assets/images/default-female.jpeg', __FILE__)); ?>" width="200">
                                        <?php endif; ?>
                                        <?php endif; ?>
                                    </div>

                                    <div class="employee-basic-info" >
                                        <h4><?php echo $wphrmEmployeeBasicInfo->wphrm_employee_fname ?> <?php echo $wphrmEmployeeBasicInfo->wphrm_employee_lname ?></h4>
                                        <?php if(!empty($designation['designationName'])): ?>
                                        <div class="employee-designation"><?php echo empty($designation['designationName']) ? '' : $designation['designationName']; ?></div>
                                        <?php endif; ?>
                                        <?php if($days_work): ?>
                                        <div class="employee-work-time" ><span class="bold">at work for:</span> <?php echo $days_work; ?></div>
                                        <?php endif; ?>
                                    </div>

                                </div>
                            </div>
                            <div class="panel panel-default inner-panel">
                                <div class="panel-heading"><i class="fa fa-briefcase"></i> Company Details</div>
                                <div class="panel-body">
                                    <table class="table" >
                                        <tr>
                                            <td>Employee ID</td>
                                            <td><?php echo $wphrmEmployeeBasicInfo->wphrm_employee_userid; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Department</td>
                                            <td><?php
                                                $emp_department = $this->get_department_info($wphrmEmployeeBasicInfo->wphrm_employee_department);
                                                echo $emp_department['departmentName'];
                                                ?></td>
                                        </tr>
                                        <tr>
                                            <td>Designation</td>
                                            <td><?php
                                                $emp_designation = $this->get_designation_info($wphrmEmployeeBasicInfo->wphrm_employee_designation);
                                                echo $emp_designation['designationName'];
                                                ?></td>
                                        </tr>
                                        <tr>
                                            <td>Date of Joining</td>
                                            <td><?php echo $wphrmEmployeeBasicInfo->wphrm_employee_joining_date; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Employee Type</td>
                                            <td>
                                                <?php  
                                                    $employee_levels = $this->get_employee_types();
                                                    $selected_employee_level = isset($wphrmEmployeeBasicInfo->wphrm_employee_level) ? $wphrmEmployeeBasicInfo->wphrm_employee_level : ''; 
                                                    echo isset($employee_levels[$selected_employee_level]) ? $employee_levels[$selected_employee_level]['title'] : '';        
                                                ?>
                                            
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Reporting Manager(s)</td>
                                            <td><?php  
                                                    $reporting_manager = isset($wphrmEmployeeBasicInfo->wphrm_employee_reporting_manager) && is_array($wphrmEmployeeBasicInfo->wphrm_employee_reporting_manager) ? $wphrmEmployeeBasicInfo->wphrm_employee_reporting_manager : array(); ?>

                                                    <!-- <ul>
                                                        <?php
                                                        $x = 0;
                                                        if($reporting_manager && is_array($reporting_manager)):
                                                        foreach ($employee_managers as $key => $userdata): ?>
                                                        <?php if(!in_array( $userdata->ID, $reporting_manager) ) { continue; } else { $x++;  } ?>
                                                        <?php $wphrmEmployeeFirstName = get_user_meta($userdata->data->ID, 'first_name', true); ?>
                                                        <?php $wphrmEmployeeLastName = get_user_meta($userdata->data->ID, 'last_name', true); ?>
                                                        <li><?php echo $x.'. '?> <?php echo (!empty($wphrmEmployeeFirstName) || !empty($wphrmEmployeeLastName)) ? _e($wphrmEmployeeFirstName. ' '.$wphrmEmployeeLastName, 'wphrm') : $userdata->display_name; ?></li>
                                                        <?php
                                                        endforeach;
                                                        endif;
                                                        ?>
                                                    </ul> -->
                                                    <ol style="margin-left: 1em;">
                                                        <?php
                                                        foreach( $reporting_manager as $to_report ) {
                                                        $emp_info = $this->WPHRMGetUserDatas( $to_report, 'wphrmEmployeeInfo' );
                                                        $user_info = get_userdata( $to_report );
                                                        $wphrmEmployeeFirstName = get_user_meta( $to_report, 'first_name', true);
                                                        $wphrmEmployeeLastName = get_user_meta( $to_report, 'last_name', true);
                                                        echo '<li>';
                                                            if( $emp_info['wphrm_employee_department'] == 4 ) {
                                                                echo 'Administrator';
                                                            } elseif( $emp_info['wphrm_employee_department'] == 7 ) {
                                                                echo 'Human Resource';
                                                            } else {
                                                                echo (!empty($wphrmEmployeeFirstName) || !empty($wphrmEmployeeLastName)) ? _e($wphrmEmployeeFirstName. ' '.$wphrmEmployeeLastName, 'wphrm') : $user_info->display_name;
                                                            }
                                                        echo '</li>';
                                                    }
                                                        ?>
                                                    </ol>
                                                
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 col-sm-6 personal-details-panel">
                            <div class="panel panel-default inner-panel">
                                <div class="panel-heading"><i class="fa fa-pencil"></i> Personal Details</div>
                                <div class="panel-body">
                                    <table class="table" >
                                        <tr>
                                            <td>Name</td>
                                            <td><?php echo $wphrmEmployeeBasicInfo->wphrm_employee_fname; ?> <?php echo $wphrmEmployeeBasicInfo->wphrm_employee_lname; ?></td>
                                        </tr>
                                        <tr>
                                            <td>DOB</td>
                                            <td><?php echo date('d-F-Y', strtotime($wphrmEmployeeBasicInfo->wphrm_employee_bod)); ?></td>
                                        </tr>
                                        <tr>
                                            <td>NRIC/FIN</td>
                                            <td><?php echo $employee_complete_info->work_permit_info->wphrm_employee_nric; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Gender</td>
                                            <td><?php echo $wphrmEmployeeBasicInfo->wphrm_employee_gender; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Email</td>
                                            <td><?php echo $wphrmEmployeeBasicInfo->wphrm_employee_email; ?></td>
                                        </tr>

                                        <tr>
                                            <td>Marital Status</td>
                                            <td><?php echo $wphrmEmployeeBasicInfo->wphrm_employee_mstatus; ?></td>
                                        </tr>
                                        <!-- <tr>
                                            <td>Religion</td>
                                            <td><?php echo $wphrmEmployeeBasicInfo->wphrm_employee_religion; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Race</td>
                                            <td><?php echo $employee_complete_info->work_permit_info->wphrm_employee_race; ?></td>
                                        </tr>

                                        <?php foreach( $employee_complete_info->other_info->wphrmotherfieldslebal as $labelkey => $otherlabel ) { ?>
                                        <?php foreach( $employee_complete_info->other_info->wphrmotherfieldsvalue as $valuekey => $othervalue ) { ?>
                                            <?php if( $labelkey == $valuekey ) { ?>
                                            <tr>
                                                <td><?php echo $otherlabel ?></td>
                                                <td><?php echo $othervalue; ?></td>
                                            </tr>
                                            <?php } ?>
                                        <?php } ?>
                                        <?php } ?> -->

                                        <tr>
                                            <td>Passport Number</td>
                                            <td><?php echo $employee_complete_info->work_permit_info->wphrm_employee_passport; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Passport Date of Issue</td>
                                            <td><?php if( !empty( $employee_complete_info->work_permit_info->wphrm_employee_passport_issued ) ) { echo date('d-F-Y', strtotime( $employee_complete_info->work_permit_info->wphrm_employee_passport_issued )); } ?></td>
                                        </tr>
                                        <tr>
                                            <td>Passport Expiration</td>
                                            <td><?php if( !empty( $employee_complete_info->work_permit_info->wphrm_employee_passport_expiration ) ) { echo date('d-F-Y', strtotime( $employee_complete_info->work_permit_info->wphrm_employee_passport_expiration )); } ?></td>
                                        </tr>
                                        <!-- <tr>
                                            <td>Vehicle</td>
                                            <td><?php echo $employee_complete_info->other_info->wphrm_vehicle_type; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Vehicle Number</td>
                                            <td><?php echo $employee_complete_info->other_info->wphrm_employee_vehicle_registrationno; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Vehicle IU Number</td>
                                            <td><?php echo $employee_complete_info->other_info->wphrm_employee_vehicle_model; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Vehicle Expiration</td>
                                            <td><?php echo $employee_complete_info->other_info->wphrm_employee_vehicle_expiration; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Phone</td>
                                            <td><?php echo $wphrmEmployeeBasicInfo->wphrm_employee_phone; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Local Address</td>
                                            <td><?php echo $wphrmEmployeeBasicInfo->wphrm_employee_local_address; ?></td>
                                        </tr> -->
                                        <tr>
                                            <td>Address</td>
                                            <td><?php echo $wphrmEmployeeBasicInfo->wphrm_employee_local_address; ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6 notice-panel">
                            <div class="panel panel-default inner-panel">
                                <div class="panel-heading"><i class="fa fa-bullhorn"></i> Notice Board</div>
                                <div class="panel-body">
                                    <?php
                                    $is_notice = false;
                                    if(!empty($wphrmNotices)): ?>
                                    <ul class="notice-items" >
                                        <?php foreach($wphrmNotices as $key => $wphrmNotice):?>
                                        <?php $notice_time = strtotime($wphrmNotice->wphrmdate); ?>
                                        <?php if( $currentMonth == date('m', $notice_time) || $nextMonth == date('m', $notice_time) ) { $is_notice = true;?>
                                            <li class="notice-item <?php echo isset($wphrmNotice->wphrminvitationnotice) && $wphrmNotice->wphrminvitationnotice == 1 ? 'is-invitation' : ''; ?>">
                                                <?php //var_dump($wphrmNotice);?>
                                                <div class="notice-date pull-left">
                                                    <span class="notice-day"><?php echo date('d', $notice_time); ?></span>
                                                    <span class="notice-month-year"><?php echo date('m', $notice_time); ?>, <?php echo date('Y', $notice_time); ?></span>
                                                </div>
                                                <div class="notice-info">
                                                    <div class="notice-title"><a href="<?php echo admin_url('admin.php?page=wphrm-view-notice&notice_id='.$wphrmNotice->id); ?>" ><?php echo $wphrmNotice->wphrmtitle; ?></a></div>
                                                </div>
                                                <div class="clearfix"></div>
                                            </li>
                                        <?php } else { $is_notice = false; } ?>
                                        <?php endforeach; ?>
                                    </ul>
                                    <?php else: ?>
                                    <p class="description">No notice to display.</p>
                                    <?php endif; ?>
                                    <?php if( !$is_notice ): ?>
                                    <p class="description">No notice to display.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-5 col-sm-6 leave-balance-panel">
                            <div class="panel panel-default inner-panel">
                                <div class="panel-heading"><i class="fa fa-list"></i> Leave Balance</div>
                                <div class="panel-body">
                                   <?php 
                                    $employee_id = get_current_user_id();
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

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="alert alert-info">

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
                                                        if( $this->employee_allowed_leaves($employee_id) ) {
                                                            if(!in_array($leavesType->id, $this->employee_allowed_leaves($employee_id))) continue;
                                                        } else {
                                                            continue;
                                                        }

                                                        $totalNoOfLeave = 0;
                                                        if ($leavesType->period == 'Monthly') {
                                                            $totalNoOfLeave = intval($leavesType->numberOfLeave * $currentMonth);
                                                        } else if ($leavesType->period == 'Quarterly') {
                                                            $totalNoOfLeave = intval($leavesType->numberOfLeave * $curQuarter);
                                                        } else if ($leavesType->period == 'Yearly') {
                                                            $totalNoOfLeave = intval($leavesType->numberOfLeave * $wphrmEmployeeJoiningToCurrentTotalYear);
                                                        }

                                                        //if( in_array($leavesType->leave_rules, array_keys($this->get_leave_rules()) ) ){
                                                            $totalNoOfLeave = $this->get_employee_max_leave($employee_id, $leavesType->leave_rules, $leavesType);
                                                        //}

                                                        $employeeLeaves = $wpdb->get_row("SELECT COUNT(id) AS leaveCounter FROM $this->WphrmAttendanceTable WHERE `status`='absent' AND `employeeID` ='" . $employee_id . "' AND `leaveType`='$leavesType->leaveType' AND `date` <= '$attendance_date' AND `applicationStatus`='approved'");
                                                        $employeeLeavesHalfday = $wpdb->get_row("SELECT COUNT(id) AS halfdayCounter FROM $this->WphrmAttendanceTable WHERE `halfDayType`='halfday' AND `employeeID` ='" . $employee_id . "' AND `leaveType`='$leavesType->leaveType' AND `date` <= '$attendance_date' AND `applicationStatus`='approved'");

                                                        $halfdayCounter = ($employeeLeavesHalfday->halfdayCounter / 2);
                                                        $leaveTotal = $employeeLeaves->leaveCounter + $halfdayCounter;

                                                        $leaveTotal = $this->get_used_leave( $employee_id, $leavesType->id );
                                                        //$leaveTotal = apply_filters('employee_attendance_after_calculate_leave_total', $leaveTotal, $leavesType, $employee_id);

                                                        $leaveRemaining = ($totalNoOfLeave - $leaveTotal);
                                                        if($leaveRemaining >0){
                                                            $leaveRemaining = $leaveRemaining;
                                                        }else{
                                                            $leaveRemaining=0;
                                                        }
                                                        

                                                        if($leavesType->id == 35 || $leavesType->leaveType == 'Annual Leave' || $leavesType->leaveType == 'annual leave' || $leavesType->leaveType == 'Annual leave') { 
                                                            /*$yos = ((int) $interval->format('%y years') + 1);
                                                            $emp = $this->get_user_complete_info( get_current_user_id() );
                                                            $leveltype = $emp->basic_info->wphrm_employee_level;
                                                            $leave_entitlement_count = $wpdb->get_var("SELECT COUNT(*) FROM  $this->WphrmEmployeeLevelTable WHERE years_of_service = $yos");
                                                            if( $leave_entitlement_count > 0 ) {
                                                                $leave_entitlement = $wpdb->get_row("SELECT * FROM  $this->WphrmEmployeeLevelTable WHERE years_of_service = $yos");
                                                            } else {
                                                                $leave_entitlement = $wpdb->get_row("SELECT * FROM  $this->WphrmEmployeeLevelTable ORDER BY id DESC LIMIT 1");
                                                            }
                                                            if( $leveltype == 'senior_manager' ) {
                                                                $max_leave = $leave_entitlement->senior_manager;
                                                            } elseif( $leveltype == 'manager' ) {
                                                                $max_leave = $leave_entitlement->manager;
                                                            } elseif( $leveltype == 'supervisor' ) {
                                                                $max_leave = $leave_entitlement->supervisor;
                                                            } elseif( $leveltype == 'staff' ) {
                                                                $max_leave = $leave_entitlement->staff;
                                                            }*/
                                                            
                                                            $year = date('Y');
                                                            $annual_leave_history = isset($wphrmEmployeeInfo['wphrm_employee_leave_carried']) ? $wphrmEmployeeInfo['wphrm_employee_leave_carried'] : false;
                                                            if(
                                                                isset($annual_leave_history[$year-1]) && isset($annual_leave_history[$year-1]['can_carry_over'])
                                                                && filter_var($annual_leave_history[$year-1]['can_carry_over'], FILTER_VALIDATE_BOOLEAN)
                                                                && !empty($annual_leave_history[$year-1]['count']) && $annual_leave_history[$year-1]['expiry'] > date('m/d/Y')
                                                            ){
                                                                $carried_leave = $annual_leave_history[$year-1]['count'];
                                                            }
                                                            $max_leave = (int)$this->get_employee_total_leave( $employee_id, $leavesType->id );
                                                            $remaining = (int)$max_leave - (int)$leaveTotal;
                                                            
                                                            echo esc_html($leavesType->leaveType) . ' :';
                                                            echo '<div class="leave-details-holder">';
                                                                echo '<span>Total: <strong>'.esc_html($max_leave).'</strong> day(s)</span>';
                                                                echo '<span>Used: <strong>'.esc_html($leaveTotal).'</strong> (MTD - Month to Date)</span>';
                                                                echo '<span>Balance: <strong>'.esc_html($remaining).'</strong> ('.($max_leave - $carried_leave).' - '.$year.')</span>';

                                                                if( isset($annual_leave_history[$year-1]) && isset($annual_leave_history[$year-1]['can_carry_over']) && filter_var($annual_leave_history[$year-1]['can_carry_over'], FILTER_VALIDATE_BOOLEAN) && !empty($annual_leave_history[$year-1]['count']) && $annual_leave_history[$year-1]['expiry'] > date('m/d/Y') ) {
                                                                    echo '<span>Bring forward <strong>'.esc_html($annual_leave_history[$year-1]['count']).'</strong> day(s) from ('.($year-1).'), expiry '.date( 'dS M Y', strtotime($annual_leave_history[$year-1]['expiry']) ).'.</span>';
                                                                }

                                                            echo '</div>';

                                                    ?>
                                                            <!-- <strong style="cursor: pointer;" title="Total : <?php echo esc_attr($max_leave).' , '; ?> Taken : <?php echo esc_attr($leaveTotal).' , '; ?> Remaining : <?php echo esc_attr($remaining); ?>"><?php echo esc_html($leaveTotal) . '/' . $max_leave . '<br>'; ?></strong> -->
                                                    <?php } elseif( $leavesType->id == 45 || $leavesType->id == 48 || $leavesType->id == 49 || $leavesType->id == 50 ) {
                                                            echo esc_html($leavesType->leaveType) . ' :';
                                                            global $wphrm;
                                                            $ytoday = date('Y') - 1;
                                                            $wphrmEmployeeInfo = $wphrm->WPHRMGetUserDatas($employee_id, 'wphrmEmployeeInfo');
                                                            $wphrm_employee_maternity_carried = isset($wphrmEmployeeInfo['wphrm_employee_maternity_carried']) ? $wphrmEmployeeInfo['wphrm_employee_maternity_carried'] : array();
                                                            $expiry = strtotime( $wphrm_employee_maternity_carried[$ytoday]['expiry'] );
                                                            $date_today = strtotime( date( 'm/d/Y' ) ); 

                                                            if( $expiry < $date_today ) {
                                                                $max_leave = $wphrm_employee_maternity_carried[$ytoday]['count'] + $totalNoOfLeave;
                                                            } else {
                                                                $max_leave = $totalNoOfLeave;
                                                            }
                                                            $m_remaining = (int)$max_leave - (int)$leaveTotal;
                                                    ?>
                                                            <strong style="cursor: pointer;" title="Total : <?php echo esc_attr($max_leave).' , '; ?> Taken : <?php echo esc_attr($leaveTotal).' , '; ?> Remaining : <?php echo esc_attr($m_remaining); ?>"><?php echo esc_html($leaveTotal) . '/' . $max_leave . '<br>'; ?></strong>
                                                        <?php
                                                        } elseif( $leavesType->id == 39 || $leavesType->id == 41 || $leavesType->id == 42 || $leavesType->id == 43 ) {
                                                            echo esc_html($leavesType->leaveType) . ' :';
                                                            if( $leavesType->id == 39 ) {
                                                                $leaveTotal = $this->get_used_leave($employee_id, 39) + $this->get_used_leave($employee_id, 37) + $this->get_used_leave($employee_id, 40);
                                                            } elseif( $leavesType->id == 41 ) {
                                                                $leaveTotal = $this->get_used_leave($employee_id, 41) + $this->get_used_leave($employee_id, 63) + $this->get_used_leave($employee_id, 40);
                                                            } elseif( $leavesType->id == 42 ) {
                                                                $leaveTotal = $this->get_used_leave($employee_id, 42) + $this->get_used_leave($employee_id, 38) + $this->get_used_leave($employee_id, 40);
                                                            } elseif( $leavesType->id == 43 ) {
                                                                $leaveTotal = $this->get_used_leave($employee_id, 43) + $this->get_used_leave($employee_id, 64) + $this->get_used_leave($employee_id, 40);
                                                            } else {
                                                                $leaveTotal = $this->get_used_leave($employee_id, $leavesType->id);
                                                            }
                                                            
                                                            $m_remaining = (int)$totalNoOfLeave - (int)$leaveTotal;
                                                    ?>
                                                            <strong style="cursor: pointer;" title="Total : <?php echo esc_attr($totalNoOfLeave).' , '; ?> Taken : <?php echo esc_attr($leaveTotal).' , '; ?> Remaining : <?php echo esc_attr($m_remaining); ?>"><?php echo esc_html($leaveTotal) . '/' . $totalNoOfLeave . '<br>'; ?></strong>
                                                        <?php
                                                        } else {
                                                            echo esc_html($leavesType->leaveType) . ' :';
                                                            if ($totalNoOfLeave >= $leaveTotal) { ?>
                                                            <strong style="cursor: pointer;" title="Total : <?php echo esc_attr($totalNoOfLeave).' , '; ?> Taken : <?php echo esc_attr($leaveTotal).' , '; ?> Remaining : <?php echo esc_attr($leaveRemaining); ?>"><?php echo esc_html($leaveTotal) . '/' . $totalNoOfLeave . '<br>'; ?></strong>
                                                                <?php } else { ?>
                                                            <strong style="cursor: pointer;" title="Total : <?php echo esc_attr($totalNoOfLeave).' , '; ?> Taken : <?php echo esc_attr($leaveTotal).' , '; ?> Remaining : <?php echo esc_attr($leaveRemaining); ?>"><?php echo esc_html($leaveTotal) . '/' . $totalNoOfLeave . '<br>'; ?></strong>
                                                                <?php } ?>
                                                            </tr>
                                                        <?php }
                                                    } ?>

                                                    <div class="medical-balance-holder">
                                                        <?php 
                                                            $employee_allowed_leaves = $this->employee_allowed_leaves($employee_id);
                                                            $wphrm_leavetypes = $wpdb->get_results("SELECT * FROM  $this->WphrmLeaveTypeTable ORDER BY leaveType ASC");
                                                            foreach ($wphrm_leavetypes as $key => $wphrm_leavetype) {
                                                                if( !in_array( $wphrm_leavetype->id, $employee_allowed_leaves ) ) continue;
                                                                if( $wphrm_leavetype->id == 37 || $wphrm_leavetype->id == 38 || $wphrm_leavetype->id == 39 || $wphrm_leavetype->id == 41 || $wphrm_leavetype->id == 42 || $wphrm_leavetype->id == 43 || $wphrm_leavetype->id == 63 || $wphrm_leavetype->id == 64 ) {
                                                                    $leaveRules =  $wpdb->get_row("SELECT * FROM $this->WphrmLeaveRulesTable WHERE id = $wphrm_leavetype->leave_rules ");
                                                                    if( $leaveRules ) {
                                                                        $reimbursement = $leaveRules->medical_claim_limit;
                                                                    }
                                                                }
                                                                if( $wphrm_leavetype->id == 40 || $wphrm_leavetype->id == 71 ) {
                                                                    $leaveRules =  $wpdb->get_row("SELECT * FROM $this->WphrmLeaveRulesTable WHERE id = $wphrm_leavetype->leave_rules ");
                                                                    if( $leaveRules ) {
                                                                        $elderly = $leaveRules->elderly_screening_limit;
                                                                    }
                                                                }
                                                            }

                                                            $wphrmEmployeeInfo = $wphrm->WPHRMGetUserDatas($employee_id, 'wphrmEmployeeInfo');
                                                            $wphrm_claimed_medical = isset($wphrmEmployeeInfo['wphrm_employee_medical_reimbursement']) ? $wphrmEmployeeInfo['wphrm_employee_medical_reimbursement'] : array();
                                                            $yearToday = date('Y');

                                                            $claimed = 0;
                                                            $year_claimed = $wpdb->get_results( "SELECT * FROM $this->WphrmLeaveApplicationTable WHERE employeeID = $employee_id AND leaveType = 37 OR  employeeID = $employee_id AND leaveType = 38 OR  employeeID = $employee_id AND leaveType = 39 OR  employeeID = $employee_id AND leaveType = 41 OR  employeeID = $employee_id AND leaveType = 42 OR  employeeID = $employee_id AND leaveType = 43 OR  employeeID = $employee_id AND leaveType = 63 OR  employeeID = $employee_id AND leaveType = 64" );
                                                            foreach( $year_claimed as $yclaimed ) {
                                                                $claimed = $claimed + $yclaimed->medical_claim_amount;
                                                            }
                                                            $total_claimed = ( $claimed + $wphrm_claimed_medical[$yearToday]['amount'] );
                                                            $total_reimbursement = ((int)$reimbursement + (int)$elderly);
                                                            echo '<br><span>Medical Claim: <strong>$'.$reimbursement.'</strong></span>';
                                                            if( $elderly > 0 ) {
                                                                echo '<br><span>Annual Checkup: <strong>$'.$elderly.'</strong></span>';
                                                            }
                                                            echo '<br><span>Medical Balance: <strong>$'.($total_reimbursement - $total_claimed).'</strong></span>';
                                                            echo '<br><span>Used: <strong>$'.$total_claimed.'</strong></span>';
                                                            echo '<br><span>Total: <strong>$'.$total_reimbursement.'</strong></span>';
                                                        ?>
                                                    </div>

                                                </div> 
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 col-sm-6 birthday-panel">
                            <div class="panel panel-default inner-panel">
                                <div class="panel-heading"><i class="fa fa-birthday-cake"></i> Birthdays</div>
                                <div class="panel-body">

                                    

                                    <!--<div class="attendance-summary">
                                        <div class="text-center">
                                            <div class="row" >
                                                <div class="col-sm-6">
                                                    <div><?php echo $attendance_info['present']; ?></div>
                                                    <div>ATTENDANCE</div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div><?php echo empty($next_approved_leave->date) ? 'N/A' : $next_approved_leave->date; ?></div>
                                                    <div>NEXT LEAVE</div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>-->

                                    <div class="current-upcoming-birthdays" >
                                        <!--<div class="heading"><h4><i class="fa fa-birthday-cake" aria-hidden="true"></i> Birthdays</h4></div>-->
                                        <?php
                                        $currentMonthDay = date('m-d');
                                        $countTotalEmployeeBirthDays = 0;
                                        $bdays = array();
                                        foreach ($wphrmUsers as $key => $userdata){
                                            $wphrmUserinfo = $this->WPHRMGetUserDatas($userdata->ID, 'wphrmEmployeeInfo');
                                            
                                            
                                            if (isset($wphrmUserinfo['wphrm_employee_bod'])){
                                                $employeebirthMonthDay = date('m-d', strtotime($wphrmUserinfo['wphrm_employee_bod']));
                                                if (strtotime($employeebirthMonthDay.'-'.date('Y').' 00:00:00') >= strtotime($currentMonthDay.'-'.date('Y').' 00:00:00')) {
                                                    
                                                     if (isset($wphrmUserinfo['employee_profile']) &&  $wphrmUserinfo['employee_profile']!= '') {   
                                                         if (isset($wphrmUserinfo['employee_profile'])) { $img = esc_attr($wphrmUserinfo['employee_profile']); }
                                                         //echo '<img src="'.$img.'" width="60">';
                                                    } else {
                                                        if ($wphrmUserinfo['wphrm_employee_gender'] == 'Male') {
                                                            $img = esc_attr(plugins_url('assets/images/default-male.jpeg', __FILE__));
                                                            //echo '<img src="'.esc_attr(plugins_url('assets/images/default-male.jpeg', __FILE__)).'" width="60">';
                                                         } else { 
                                                            $img = esc_attr(plugins_url('assets/images/default-female.jpeg', __FILE__));
                                                            //echo '<img src="'.esc_attr(plugins_url('assets/images/default-female.jpeg', __FILE__)).'" width="60">';
                                                         }
                                                    }
                                                    
                                                    if( $currentMonth == date('m', strtotime($wphrmUserinfo['wphrm_employee_bod'])) || $nextMonth == date('m', strtotime($wphrmUserinfo['wphrm_employee_bod'])) ) {
                                                        $bdays[] = array( 
                                                            'img' => $img, 
                                                            'emp_name' => $wphrmUserinfo['wphrm_employee_fname'].' '.$wphrmUserinfo['wphrm_employee_lname'],
                                                            'bdate' => date('m-d', strtotime($wphrmUserinfo['wphrm_employee_bod'])),
                                                            'birthdate' => date('d F', strtotime($wphrmUserinfo['wphrm_employee_bod']))
                                                        );
                                                    }
                                                    
                                                    $to_sort = 'bdate';
                                                    usort( $bdays, function( $a, $b ) use( &$to_sort ){ return $a[$to_sort] - $b[$to_sort]; } );
                                                    /*echo '<div class="birthday-item">';
                                                    
                                                        echo '<div class="name" >'.$wphrmUserinfo['wphrm_employee_fname'].' '.$wphrmUserinfo['wphrm_employee_lname'].'</div>
                                                        <div class="birthdate bold" >'.date('d F', strtotime($wphrmUserinfo['wphrm_employee_bod'])).'</div>
                                                    </div>';*/
                                                    $countTotalEmployeeBirthDays++;
                                                }
                                            }

                                            //if($countTotalEmployeeBirthDays >= 3) break;
                                        }
                                        
                                        if( empty( $bdays ) ) {
                                            echo 'No upcoming birthdays!';
                                        } else {
                                            foreach( $bdays as $bday ) {
                                                
                                                echo '<div class="birthday-item">
                                                        <img src="'.$bday['img'].'" width="60">
                                                        <div class="name" >'.$bday['emp_name'].'</div>
                                                        <div class="birthdate bold" >'.$bday['birthdate'].'</div>
                                                </div>';
                                            }
                                        }

                                        ?>
                                    </div>

                                </div>
                            </div>
                        </div>

                        
                        <div class="clearfix"></div>
                        <div class="col-md-8">
                            <div class="panel panel-default inner-panel">
                                <div class="panel-heading"><i class="fa fa-bars"></i> Department Calendar</div>
                                <div class="panel-body">
                                    <?php if($last_absent): ?>
                                    <div class="alert alert-info last-absent" >
                                        <span class="label" >Last absent: <span class="day-ellapse" ><?php echo $last_absent_ellapse; ?></span></span>
                                        <span class="absent-date pull-right"><?php echo date('d-M-Y', strtotime($last_absent->date) ); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <div id="Attendance_Calendar" class="has-toolbar"></div>
                                    <p></p>
                                    <div class="visible-sm visible-xs">
                                        <ul>
                                            <li><span class="cal-legend present">[blue-green]</span> - Present</li>

                                            <li><span class="cal-legend leave">[yellow]</span> - On Leave</li>

                                            <li><span class="cal-legend absent">[red]</span> - Absent</li>
                                        </ul>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="panel panel-default inner-panel panel-holidays">
                                <div class="panel-heading"><i class="fa fa-location-arrow"></i> Upcoming Holidays</div>
                                <div class="panel-body">

                                    <?php $color_list = array('red', 'green', 'green', 'yellow', 'gray'); ?>
                                    <?php if(!empty($upcoming_holidays)): ?>
                                    <ul class="holiday-items" >
                                        <?php foreach($upcoming_holidays as $key => $upcoming_holiday): ?>
                                        <li class="holiday-item <?php //echo $color_list[$key]; ?>">
                                            <div class="title"><?php echo $upcoming_holiday->wphrmOccassion; ?> <span class="day small">-<?php echo ucfirst($upcoming_holiday->type); ?></span>
                                                <span class="date pull-right"><?php echo date('d M Y', strtotime($upcoming_holiday->wphrmDate)); ?></span>
                                            </div>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <?php else: ?>
                                    <p class="description">No holidays to display.</p>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="tab-pane" id="leave">
                    <div class="row">
                        <div class="col-md-12">

                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="my-account">
                    <div class="row">
                        <div class="col-md-12">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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

<?php
$wphrmEmployeePassportExpiration = new DateTime( $employee_complete_info->work_permit_info->wphrm_employee_passport_expiration );
$wphrmEmployeeFINExpiration = new DateTime( $employee_complete_info->work_permit_info->wphrm_employee_permit_expiration );
$today = new DateTime();
$interval = $today->diff($wphrmEmployeePassportExpiration);
$interval2 = $today->diff($wphrmEmployeeFINExpiration);

if( empty( $employee_complete_info->work_permit_info->wphrm_employee_passport_expiration ) ) {
    $passport_expiration_remaining = 0;
} else {
    $passport_expiration_remaining = ((int) $interval->format('%m') + ($interval->format('%y') * 12));
}

if( empty( $employee_complete_info->work_permit_info->wphrm_employee_permit_expiration ) ) {
    $fin_expiration_remaining = 0;
} else {
    $fin_expiration_remaining = ((int) $interval2->format('%m') + ($interval2->format('%y') * 12));
}
?>
<input type="hidden" id="passport_expiration_remaining" value="<?php echo $passport_expiration_remaining?>">
<input type="hidden" id="fin_expiration_remaining" value="<?php echo $fin_expiration_remaining?>">
<div id="fin_passport_expiration" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false" style="margin-top: 5%">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
                <h4 class="modal-title"><strong><i class="fa fa-exclamation-triangle"></i> <?php _e('Expiration Notice', 'wphrm'); ?></strong></h4>
            </div>
            <div class="modal-body">
                <div class="portlet-body">
                    
                    <?php if( $passport_expiration_remaining < 7 && $passport_expiration_remaining != 0 ) : ?>
                        <h4>Passport: <strong><?php echo date('F j, Y', strtotime( $employee_complete_info->work_permit_info->wphrm_employee_passport_expiration )); ?></strong></h4>
                    <?php endif; ?>
                    
                    <?php if( $fin_expiration_remaining < 7 && $fin_expiration_remaining != 0 ) : ?>
                        <h4>FIN Pass: <strong><?php echo date('F j, Y', strtotime( $employee_complete_info->work_permit_info->wphrm_employee_permit_expiration )); ?></strong></h4>
                    <?php endif; ?>
                    
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>
</div>