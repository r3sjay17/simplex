<?php
if (!defined('ABSPATH'))
    exit;
global $current_user, $wpdb, $wp_query;
$wphrmUserRole = implode(',', $current_user->roles);
$wphrmUsers = get_users();
$wphrmGetPagePermissions = $this->WPHRMGetPagePermissions();
?>
<!-- BEGIN PAGE HEADER-->
<div class="preloader">
    <span class="preloader-custom-gif"></span>
</div>

<div id="wphrm-Add-weekand" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
                <h4 class="modal-title"><strong><i class="fa fa-plus"></i><?php _e('Mark Attendance in Bulk', 'wphrm'); ?></strong></h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-success display-hide" id="markAttendanceBulkSuccess">
                    <button class="close" data-close="alert"></button>
                </div>
                <div class="alert alert-danger display-hide" id="markAttendanceBulkError">
                    <button class="close" data-close="alert"></button>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    <form action="#" method="post" id="mark-attendance-bulk" class="form-horizontal">
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label col-md-4"><?php _e('Employee Name', 'wphrm'); ?>  </label>
                                <div class="col-md-8">
                                    <p><select id="my-select" name="employee-id[]"  multiple="multiple" >
                                            <?php
                                            foreach ($wphrmUsers as $key => $userdata) {

                                                        $wphrmEmployeeBasicInfos = get_user_meta($userdata->ID, 'wphrmEmployeeInfo', true);
                                                        $wphrmEmployeeBasicInfoss = unserialize(base64_decode($wphrmEmployeeBasicInfos));
                                                        if (isset($wphrmEmployeeBasicInfoss['wphrm_employee_status']) && $wphrmEmployeeBasicInfoss['wphrm_employee_status'] == 'Active') {
                                                        ?>
                                                        <option value="<?php echo esc_attr($userdata->ID); ?>"><?php
                                                            if (isset($wphrmEmployeeBasicInfoss['wphrm_employee_fname'])) : echo esc_html($wphrmEmployeeBasicInfoss['wphrm_employee_fname']);
                                                            endif;
                                                            if (isset($wphrmEmployeeBasicInfoss['wphrm_employee_lname'])) : echo ' ' . esc_html($wphrmEmployeeBasicInfoss['wphrm_employee_lname']);
                                                            endif;
                                                            ?></option><?php
                                                    }
                                                }

                                            ?>
                                        </select></p>
                                </div>
                            </div>
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="control-label col-md-4"><?php _e('From Date', 'wphrm'); ?>  </label>
                                    <div class="col-md-8">
                                        <input class="form-control form-control-inline input-medium date-picker" id="set-from-date" data-date-format="dd-mm-yyyy" name="from-date"  type="text" value="" placeholder="From Date"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="control-label col-md-4"><?php _e('To Date', 'wphrm'); ?>  </label>
                                    <div class="col-md-8">
                                        <input class="form-control form-control-inline input-medium date-picker" id="set-to-date" data-date-format="dd-mm-yyyy" name="to-date"  type="text" value="" placeholder="To Date"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4"><?php _e('Attendance Mark', 'wphrm'); ?>  </label>
                                <div class="col-md-8">
                                    <select  class="form-control" id="attendance-mark" name="attendance-mark">
                                        <option value="present"><?php _e('Present', 'wphrm'); ?></option>
                                        <option value="absent"><?php _e('Absent', 'wphrm'); ?></option>

                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit"  class=" btn blue"> <i class="fa fa-plus"></i><?php _e('Submit', 'wphrm'); ?></button>
                            <button type="button" data-dismiss="modal" id="" aria-hidden="true" class="btn red"><i class="fa fa-times"></i><?php _e('Cancel', 'wphrm'); ?></button>
                        </div>
                    </form>
                    <!-- END FORM-->
                </div>
            </div>
        </div></div></div>

<h3 class="page-title"><?php _e('Attendance Management', 'wphrm'); ?></h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li><i class="fa fa-home"></i><?php _e('Home', 'wphrm'); ?><i class="fa fa-angle-right"></i></li>
        <li><?php _e('Attendance Management', 'wphrm'); ?></li>
    </ul>
</div>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div style="padding-left: 0px; padding-right:20px; padding-top:0px;" class="col-md-12">
    <div class="row">
        <div class="col-md-12">
            <?php if (in_array('manageOptionsAttendances', $wphrmGetPagePermissions)) { ?>
                <a class="btn green " href="?page=wphrm-mark-attendance" data-toggle="modal"><i class="fa fa-plus"></i><?php _e('Mark Attendance ', 'wphrm'); ?> </a>
                <a class="btn green " href="?page=wphrm-mark-attendance&status=edit" data-toggle="modal"><i class="fa fa-edit"></i><?php _e('Edit Attendance ', 'wphrm'); ?> </a>
                <a class="btn green " style="float: right;" href="#wphrm-Add-weekand"  data-toggle="modal"><i class="fa fa-edit"></i><?php _e('Mark Bulk Attendance', 'wphrm'); ?> </a>
            <?php } ?>
            <div class="portlet box blue calendar">
                <div class="portlet-title">
                    <?php if (in_array('manageOptionsAttendances', $wphrmGetPagePermissions)) { ?>
                        <div class="caption"><i class="fa fa-list"></i><?php _e('List of Attendance', 'wphrm'); ?></div>
                    <?php } else { ?>
                        <div class="caption"><i class="fa fa-edit"></i><?php _e('Attendance Details', 'wphrm'); ?></div>
                    <?php } ?>
                </div>
                <div class="portlet-body">
                    <?php if (in_array('manageOptionsAttendances', $wphrmGetPagePermissions)) { ?>
                        <table class="wphrmtable table table-striped table-bordered table-hover" id="wphrmDataTable" >
                        <?php } else { ?>
                            <table class="wphrmtable table table-striped table-bordered table-hover"><?php } ?>
                            <thead>
                                <tr>
                                    <th><?php _e('EmployeeID', 'wphrm'); ?></th>
                                    <th class="text-center"><?php _e('Image', 'wphrm'); ?></th>
                                    <th><?php _e('Name', 'wphrm'); ?></th>
                                    <th><?php _e('Employee Type', 'wphrm'); ?></th>
                                    <th><?php _e('Joined', 'wphrm'); ?></th>
                                    <th><?php _e('Last Absent', 'wphrm'); ?></th>
                                    <th><?php _e('Leaves', 'wphrm'); ?></th>
                                    <th><?php _e('Availment', 'wphrm'); ?></th>
                                    <th><?php _e('Status', 'wphrm'); ?></th>
                                    <th><?php _e('Actions', 'wphrm'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $attendance_date = esc_sql(date('Y-m-d')); // esc
                                $todaydate = esc_sql('0'); // esc
                                $employeeAttendanceCount = esc_sql('0'); // esc
                                $currentMonth = date('m'); // esc

                                foreach ($wphrmUsers as $key => $userdata) {
                                    foreach ($userdata->roles as $role => $roles) {
                                   if ($roles != 'administrator') {
                                    $wphrmEmployeeInfo = $this->WPHRMGetUserDatas($userdata->ID, 'wphrmEmployeeInfo');
                                    if (isset($wphrmEmployeeInfo['wphrm_employee_status']) && $wphrmEmployeeInfo['wphrm_employee_status'] == 'Active') {
                                        $getAttendancebyId = $wpdb->get_row("select * from $this->WphrmAttendanceTable where `date` = '" . $attendance_date . "' and `employeeID` ='" . $userdata->ID . "'");
                                        $lastAbsent = $wpdb->get_results("select max(`date`) as dateCounter from $this->WphrmAttendanceTable where  `employeeID` ='" . $userdata->ID . "' and (`status` = 'absent' or  `halfDayType` = 'halfday') and `date` <= '$attendance_date' order by id desc");

                                        if (!empty($lastAbsent[0]->dateCounter)) {
                                            if ($lastAbsent[0]->dateCounter == $attendance_date) {
                                                $todaydate = 'Today';
                                            } else {
                                                $now = time(); // or your date as well
                                                $yourDate = date('d-F-Y', strtotime($lastAbsent[0]->dateCounter));
                                                $todaydate = $yourDate;
                                            }
                                        } else {
                                            $todaydate = '-';
                                        }
                                        ?>

                                        <tr id="row">
                                            <td>
                                                <?php
                                                if (isset($wphrmEmployeeInfo['wphrm_employee_userid'])) :
                                                    echo esc_html($wphrmEmployeeInfo['wphrm_employee_userid']);
                                                endif;
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if (isset($wphrmEmployeeInfo['employee_profile']) && $wphrmEmployeeInfo['employee_profile'] != '') { ?>
                                                    <img src="<?php
                                                    if (isset($wphrmEmployeeInfo['employee_profile'])) : echo esc_attr($wphrmEmployeeInfo['employee_profile']);
                                                    endif;
                                                    ?>" width="100"><br>
                                                         <?php
                                                     } else {
                                                         if (isset($wphrmEmployeeInfo['wphrm_employee_gender']) && $wphrmEmployeeInfo['wphrm_employee_gender'] == 'Male') {
                                                             ?>
                                                        <img src="<?php echo esc_attr(plugins_url('assets/images/default-male.jpeg', __FILE__)); ?>" width="100">
                                                    <?php } else { ?>
                                                        <img src="<?php echo esc_attr(plugins_url('assets/images/default-female.jpeg', __FILE__)); ?>" width="100">
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </td>
                                            <td> <a  href="?page=wphrm-view-attendance&employee_id=<?php echo esc_attr($userdata->ID); ?>">
                                                    <?php
                                                    if (isset($wphrmEmployeeInfo['wphrm_employee_fname'])) : echo esc_html($wphrmEmployeeInfo['wphrm_employee_fname']);
                                                    endif;
                                                    ?><?php
                                                    if (isset($wphrmEmployeeInfo['wphrm_employee_lname'])) : echo ' ' . esc_html($wphrmEmployeeInfo['wphrm_employee_lname']);
                                                    endif;
                                                    ?></a></td>
                                            <td><?php if(isset($wphrmEmployeeInfo['wphrm_employee_level'])) { $leave_rule = $this->get_employee_type($wphrmEmployeeInfo['wphrm_employee_level']); echo $leave_rule['title']; } ?> </td>
                                            <td><?php if(isset($wphrmEmployeeInfo['wphrm_employee_joining_date'])) echo date('d-M-Y', strtotime($wphrmEmployeeInfo['wphrm_employee_joining_date'])); ?> </td>
                                            <td><?php echo esc_html($todaydate); ?> </td>
                                            <td >
                                                <table class="leaves" style="width: 100%">
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
                                                        $leaveRemaining='';
                                                        $leaveTotal='';
                                                        $totalNoOfLeave = 0;
                                                        if ($leavesType->period == 'Monthly') {
                                                            $totalNoOfLeave = intval($leavesType->numberOfLeave * $currentMonth);
                                                        } else if ($leavesType->period == 'Quarterly') {
                                                            $totalNoOfLeave = intval($leavesType->numberOfLeave * $curQuarter);
                                                        } else if ($leavesType->period == 'Yearly') {
                                                            $totalNoOfLeave = intval($leavesType->numberOfLeave * $wphrmEmployeeJoiningToCurrentTotalYear);
                                                        }

                                                        $year_start_date = date('Y-01-01');
                                                        $year_end_date = date('Y-12-31');

                                                        $employeeLeaves = $wpdb->get_row("SELECT COUNT(id) AS leaveCounter FROM $this->WphrmAttendanceTable WHERE `status`='absent' AND `employeeID` ='" . $userdata->ID . "' AND `leaveType`='$leavesType->id' AND ( `date` >= '$year_start_date' AND `date` <= '$year_end_date' ) AND `applicationStatus`='approved'");
                                                        $employeeLeavesHalfday = $wpdb->get_row("SELECT COUNT(id) AS halfdayCounter FROM $this->WphrmAttendanceTable WHERE `halfDayType`='halfday' AND `employeeID` ='" . $userdata->ID . "' AND `leaveType`='$leavesType->id' AND ( `date` >= '$year_start_date' AND `date` <= '$year_end_date' ) AND `applicationStatus`='approved'");

                                                        $employee_leave_entitlement = empty($wphrmEmployeeInfo['wphrm_employee_entitled_leave']) ? array() : $wphrmEmployeeInfo['wphrm_employee_entitled_leave'];
                                                        if(!in_array($leavesType->id, $employee_leave_entitlement)) continue;
                                                        /*Modify the leavetotal if the leave type is set to special*/
                                                        if( in_array($leavesType->leave_rules, array_keys($this->get_leave_rules()) ) ){
                                                            $totalNoOfLeave = $this->get_employee_max_leave($userdata->ID, $leavesType->leave_rules, $leavesType);
                                                        }

                                                        //count used leaves
                                                        /*$halfdayCounter = ($employeeLeavesHalfday->halfdayCounter / 2);
                                                        $leaveTotal = $employeeLeaves->leaveCounter + $halfdayCounter;
                                                        if( $leavesType->leave_rules  ){
                                                            $leaveTotal = apply_filters('after_calculate_leave_total', $leaveTotal, $leavesType, $userdata->ID);
                                                        }*/

                                                        $leaveTotal = $this->get_used_leave( $userdata->ID, $leavesType->id );
                                                        $leaveTotal = apply_filters('after_calculate_leave_total', $leaveTotal, $leavesType, $userdata->ID);

                                                        $leaveRemaining = $totalNoOfLeave - $leaveTotal;
                                                        if($leaveRemaining > 0){
                                                            $leaveRemaining = $leaveRemaining;
                                                        }else{
                                                            $leaveRemaining=0;
                                                        }

                                                        ?><tr>
                                                            <td style="text-align: right;"> <?php echo esc_html($leavesType->leaveType) . ' : '; ?></td>
                                                                <?php if ($totalNoOfLeave >= $leaveTotal) { ?>
                                                                <td>&nbsp;<span style="cursor: pointer;" title="Total : <?php echo esc_attr($totalNoOfLeave).' , '; ?> Taken : <?php echo esc_attr($leaveTotal).' , '; ?> Remaining : <?php echo esc_attr($leaveRemaining); ?>" class="label label-sm label-success"><?php echo esc_html($leaveTotal) . '/' . $totalNoOfLeave . '<br>'; ?></span></td>
                                                            <?php } else { ?>
                                                                <td align="right">&nbsp;<span style="cursor: pointer;" title="Total : <?php echo esc_attr($totalNoOfLeave).' , '; ?> Taken : <?php echo esc_attr($leaveTotal).' , '; ?> Remaining : <?php echo esc_attr($leaveRemaining); ?>" class="label label-sm label-danger"><?php echo esc_html($leaveTotal) . '/' . $totalNoOfLeave . '<br>'; ?></span></td>
                                                            <?php } ?>
                                                        </tr>
                                                    <?php } ?>
                                                </table>
                                            </td>
                                            <td>
                                                <?php
                                                //calculate medical claims of the employee
                                                $mc_data = $this->get_medical_claim($userdata->ID);
                                                if($mc_data){
                                                    if($mc_data['mc_eligible']){
                                                        echo '<div >Medical Claims: '.$mc_data['mc_used'].'/'.$mc_data['mc_total'].'</div>';
                                                    }else{
                                                        echo '<div >Medical Claims: N/a</div>';
                                                    }

                                                    if($mc_data['es_eligible']){
                                                        echo '<div >Elderly Screening: '.$mc_data['es_used'].'/'.$mc_data['es_total'].'</div>';
                                                    }else{
                                                        echo '<div >Elderly Screening: N/a</div>';
                                                    }
                                                }else{
                                                    echo 'Not eligible';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php if (isset($wphrmEmployeeInfo['wphrm_employee_status']) && $wphrmEmployeeInfo['wphrm_employee_status'] == 'Active') { ?>
                                                    <span class="label label-sm label-success"><?php _e('Active', 'wphrm'); ?> </span>
                                                <?php } else { ?>
                                                    <span class="label label-sm label-danger"><?php _e('Inactive', 'wphrm'); ?></span>
                                                <?php } ?>
                                            </td>
                                            <td class="">
                                                <a class="btn purple" href="?page=wphrm-view-attendance&employee_id=<?php echo esc_attr($userdata->ID); ?>">
                                                    <i class="fa fa-eye"></i><?php _e('View', 'wphrm'); ?>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php
                                        $employeeAttendanceCount++;
                                    }
                                    }
                                }
                                }
                                if ($employeeAttendanceCount == 0) {
                                    ?>
                                    <tr>
                                        <td colspan="8"><?php _e('No attendance data found in database.', 'wphrm'); ?>
                                        </td><td class="collapse"></td><td class="collapse"></td><td class="collapse"></td><td class="collapse"></td><td class="collapse"></td><td class="collapse"></td><td class="collapse"></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(function () {
        jQuery('#my-select').searchableOptionList({maxHeight: '250px'});
    });
</script>
<!-- END PAGE CONTENT-->
