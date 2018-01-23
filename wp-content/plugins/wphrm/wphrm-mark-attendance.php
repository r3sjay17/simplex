<?php
if (!defined('ABSPATH'))
    exit;
global $current_user, $wpdb, $wp_query;
$wphrmUserRole = implode(',', $current_user->roles);
$wphrmUsers = $this->WPHRMGetEmployees();
$wphrm_messages_markAttendance = $this->WPHRMGetMessage(16);

if (isset($_REQUEST['attendancedate']) && !empty($_REQUEST['attendancedate'])) {
    $attendance_date = esc_sql(date('Y-m-d', strtotime($_REQUEST['attendancedate'])));
} else {
    $attendance_date = esc_sql(date('Y-m-d'));
}
?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="preloader">
    <span class="preloader-custom-gif"></span>
</div>


<div style="padding-left: 0px;" class="col-md-12">
    <h3 class="page-title">
        <?php if (isset($_REQUEST['attendancedate']) && !empty($_REQUEST['attendancedate']) || isset($_REQUEST['status']) && !empty($_REQUEST['status'])) { ?>
            <?php _e('Edit Mark Attendance', 'wphrm'); ?>
        <?php } else { ?> 
            <?php _e('Mark Attendance', 'wphrm'); ?>   
        <?php } ?>
    </h3>
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <?php _e('Home', 'wphrm'); ?>
                <i class="fa fa-angle-right"></i>
            </li>

            <li><?php _e('Attendance', 'wphrm'); ?><i class="fa fa-angle-right"></i></li>

            <?php if (isset($_REQUEST['attendancedate']) && !empty($_REQUEST['attendancedate']) || isset($_REQUEST['status']) && !empty($_REQUEST['status'])) { ?>
                <li><?php _e('Edit Attendance mark', 'wphrm'); ?></li>
            <?php } else { ?> 
                <li><?php _e('Attendance mark', 'wphrm'); ?></li> 
            <?php } ?>

        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <a class="btn green " href="?page=wphrm-attendances"><i class="fa fa-arrow-left"></i><?php _e('Back', 'wphrm'); ?> </a>
           
            <div class="portlet box blue ">
                <div class="portlet-title">
                    <div class="caption">
                        <?php if (isset($_REQUEST['attendancedate']) && !empty($_REQUEST['attendancedate']) || isset($_REQUEST['status']) && !empty($_REQUEST['status'])) { ?>
                            <i class="fa fa-edit"></i><?php _e('Edit Mark attendance of', 'wphrm'); ?>
                        <?php } else { ?> 
                            <i class="fa fa-pencil"></i><?php _e('Mark attendance of', 'wphrm'); ?>
                        <?php } ?> : <?php echo esc_html(date('d-F-Y', strtotime($attendance_date))) ?>        
                    </div>
                    <div class="actions">
                        <a href="javascript:;" onclick="jQuery('#wphrmEmployeeAttendanceMark_frm').submit();"  data-loading-text="Updating..." class="demo-loading-btn btn btn-sm btn-default wphrmEmployeeAttendanceMark">
                            <i class="fa fa-save"></i><?php _e('Submit', 'wphrm'); ?></a>
                    </div>
                </div>
                <div class="portlet-body form">
                    <div class="alert alert-success display-hide" id="employee_attendance_mark_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrm_messages_markAttendance); ?>
                        <button class="close" data-close="alert"></button>
                    </div>
                    <div class="alert alert-danger display-hide" id="employee_attendance_mark_error">
                        <button class="close" data-close="alert"></button>
                    </div>
                    <?php if (isset($_REQUEST['status']) && !empty($_REQUEST['status'])) { ?>
                        <div class="col-md-12 col-sm-12"  style="text-align: right; margin-left: 15px;">                          
                            <label style="font-weight: normal; font-size: 14px;">
                                <?php _e('Select Date', 'wphrm'); ?> : <input style="width: 225px;" placeholder="<?php _e('Modify marked attendance', 'wphrm'); ?> " data-date-format="dd-mm-yyyy" id="mark-attendance-date" class="before-current-date form-control input-small input-inline">
                            </label>
                        </div>
                    <?php } ?>
                    <form method="POST"  id='wphrmEmployeeAttendanceMark_frm' accept-charset="UTF-8" class="form-horizontal">
                        <input type="hidden"  name="attendancedate" value="<?php echo esc_attr($attendance_date); ?>">
                        <table class="table table-striped table-bordered table-hover" id="wphrmDataTable">
                            <thead>
                                <tr>
                                    <th><?php _e('S.No', 'wphrm'); ?></th>
                                    <th><?php _e('EmployeeID', 'wphrm'); ?></th>
                                    <th><?php _e('Name', 'wphrm'); ?></th>
                                    <th><?php _e('Status', 'wphrm'); ?></th>
                                    <th><?php _e('Leave Type and Reason', 'wphrm'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                $employeeAttendanceCount = 0;

                                foreach ($wphrmUsers as $key => $userdata) {
                                    
                                    $wphrmEmployeeInfo = $this->WPHRMGetUserDatas($userdata->ID, 'wphrmEmployeeInfo');
                                    $userdataID = esc_sql($userdata->ID); // esc
                                    if (isset($wphrmEmployeeInfo['wphrm_employee_status']) && $wphrmEmployeeInfo['wphrm_employee_status'] == 'Active') {
                                        $getattendanceby_id = $wpdb->get_row("SELECT * FROM $this->WphrmAttendanceTable WHERE `date` = '" . $attendance_date . "' AND `employeeID` ='" . $userdataID . "'");
                                        ?>
                                        <tr>
                                            <td><?php echo esc_html($i); ?></td>
                                            <td><?php
                                                if (isset($wphrmEmployeeInfo['wphrm_employee_uniqueid'])) : echo esc_html($wphrmEmployeeInfo['wphrm_employee_uniqueid']);
                                                endif;
                                                ?>
                                            </td>
                                            <td><?php
                                                if (isset($wphrmEmployeeInfo['wphrm_employee_fname'])) : echo esc_html($wphrmEmployeeInfo['wphrm_employee_fname']);
                                                endif;
                                                ?><?php
                                                if (isset($wphrmEmployeeInfo['wphrm_employee_lname'])) : echo ' ' . esc_html($wphrmEmployeeInfo['wphrm_employee_lname']);
                                                endif;
                                                ?>
                                            </td>
                                            <td>
                                                <?php if (isset($getattendanceby_id) && $getattendanceby_id->status != 'present') { ?>
                                                    <input type="checkbox"  id="checkbox<?php echo esc_attr($userdata->ID); ?>" onchange="showHide('<?php echo esc_js($userdata->ID); ?>');return false;"  class="make-switch" name="checkbox[<?php echo esc_attr($userdata->ID); ?>]"  data-on-color="success" data-on-text="<?php _e('P', 'wphrm'); ?>" data-off-text="<?php _e('A', 'wphrm'); ?>" data-off-color="danger">
                                                <?php } else {
                                                    ?>
                                                    <input type="checkbox"  id="checkbox<?php echo esc_attr($userdata->ID); ?>" onchange="showHide('<?php echo esc_js($userdata->ID); ?>');return false;" checked class="make-switch" name="checkbox[<?php echo esc_attr($userdata->ID); ?>]"  data-on-color="success" data-on-text="<?php _e('P', 'wphrm'); ?>" data-off-text="<?php _e('A', 'wphrm'); ?>" data-off-color="danger">
                                                <?php } ?>
                                                <input type="hidden"  name="employees[]" value="<?php echo esc_attr($userdata->ID); ?>">
                                            </td>
                                              
                                           
                                            <td>
                                            <?php if (isset($getattendanceby_id->status) && $getattendanceby_id->status == 'absent' || isset($getattendanceby_id->halfDayType) && $getattendanceby_id->halfDayType != '') { ?>
                                                 <select class="form-control" id="leaveOn<?php echo esc_attr($userdata->ID); ?>"  name="leaveOn[<?php echo esc_attr($userdata->ID); ?>]">
                                                        <option value=""><?php _e('Full day leave', 'wphrm'); ?></option>
                                                        <option value="halfday" <?php
                                                        if (isset($getattendanceby_id->halfDayType) && $getattendanceby_id->halfDayType == 'halfday') {
                                                            echo 'selected=selected';
                                                        }
                                                        ?> ><?php _e('Half day leave', 'wphrm'); ?></option>
                                                    </select>
                                                
                                                    <select class="form-control" style="display: block;" onchange="halfDayToggle(<?php echo esc_js($userdata->ID); ?>, this.value)" id="leaveType<?php echo esc_attr($userdata->ID); ?>" name="leaveType[<?php echo esc_attr($userdata->ID); ?>]">
                                                        <option value=""><?php _e('Select leave type', 'wphrm'); ?></option>
                                                        <?php
                                                        $selected = '';

                                                        $wphrm_leavetypes = $wpdb->get_results("SELECT * FROM $this->WphrmLeaveTypeTable");
                                                        foreach ($wphrm_leavetypes as $key => $wphrm_leavetype) {
                                                            ?>
                                                            <?php echo esc_attr($wphrm_leavetype->leaveType); ?>
                                                            <option value="<?php
                                                                    if (isset($wphrm_leavetype->leaveType)) : echo esc_attr($wphrm_leavetype->leaveType);
                                                                    endif;
                                                                    ?>"<?php
                                                                    if (isset($wphrm_leavetype->leaveType) && $getattendanceby_id->leaveType == $wphrm_leavetype->leaveType) {
                                                                        echo 'selected="selected"';
                                                                    }
                                                                    ?>>
                                                                        <?php
                                                                        if (isset($wphrm_leavetype->leaveType)) : echo esc_html($wphrm_leavetype->leaveType);
                                                                        endif;
                                                                        ?>
                                                            </option>
                                                    <?php } ?>
                                                    </select>
                                                    <input type="text" class="form-control" style="<?php echo esc_attr($style); ?>" id="reason<?php echo esc_attr($userdata->ID); ?>" name="reason[<?php echo esc_attr($userdata->ID); ?>]" placeholder="<?php _e('Absent Reason', 'wphrm'); ?>" 
                                                           value="<?php if (isset($getattendanceby_id->reason)) : echo esc_attr($getattendanceby_id->reason); endif; ?>">
                                                    
                                                    <?php } else { ?>
                                                    
                                                    <select class="form-control leaveOn"  id="leaveOn<?php echo esc_attr($userdata->ID); ?>"  name="leaveOn[<?php echo esc_attr($userdata->ID); ?>]">
                                                        <option value=""><?php _e('Full day leave', 'wphrm'); ?></option>
                                                        <option value="halfday" <?php
                                                        if (isset($getattendanceby_id->halfDayType) && $getattendanceby_id->halfDayType == 'halfday') {
                                                            echo 'selected=selected';
                                                        }
                                                        ?> ><?php _e('Half day leave', 'wphrm'); ?></option>
                                                    </select>
                                                    
                                                    <select class="form-control leaveType" style="display: block;" onchange="halfDayToggle(<?php echo esc_js($userdata->ID); ?>, this.value)" id="leaveType<?php echo esc_attr($userdata->ID); ?>" name="leaveType[<?php echo esc_attr($userdata->ID); ?>]">
                                                        <option value=""><?php _e('Select leave type', 'wphrm'); ?></option>
                                                        <?php
                                                        $selected = '';
                                                        $wphrm_leavetypes = $wpdb->get_results("SELECT * FROM $this->WphrmLeaveTypeTable");
                                                        foreach ($wphrm_leavetypes as $key => $wphrm_leavetype) {
                                                            if (isset($wphrm_leavetype->leaveType) && $getattendanceby_id->leaveType == $wphrm_leavetype->leaveType) {
                                                                $selected = 'selected';
                                                            } else {
                                                                $selected = '';
                                                            }
                                                            ?>
                                                            <?php echo esc_attr($wphrm_leavetype->leaveType); ?>
                                                            <option value="<?php
                                                                        if (isset($wphrm_leavetype->leaveType)) : echo esc_attr($wphrm_leavetype->leaveType);
                                                                        endif;
                                                                        ?>"<?php $selected; ?>><?php
                                                        if (isset($wphrm_leavetype->leaveType)) : echo esc_attr($wphrm_leavetype->leaveType);
                                                        endif;
                                                        ?>
                                                            </option>
                                                    <?php } ?>
                                                    </select>
                                                    <input type="text" class="form-control reason" style="<?php echo esc_attr($style); ?>" id="reason<?php echo esc_attr($userdata->ID); ?>" name="reason[<?php echo esc_attr($userdata->ID); ?>]" placeholder="Absent Reason"
                                                           value="<?php if (isset($getattendanceby_id->reason)) : echo esc_attr($getattendanceby_id->reason); endif; ?>">
                                                    
                                        <?php } ?>   
                                           
                                        </td> </tr> <?php
                                        $employeeAttendanceCount++;
                                    }
                                    $i++;
                                }
                                if ($employeeAttendanceCount == 0) {
                                    ?>
                                    <tr>
                                        <td colspan="8"><?php _e('No attendance data found in database.', 'wphrm'); ?>
                                        </td><td class="collapse"></td><td class="collapse"></td><td class="collapse"></td><td class="collapse"></td><td class="collapse"></td><td class="collapse"></td><td class="collapse"></td>
                                    </tr>
<?php } ?>

                            </tbody>

                            <tr style="text-align: center;">
                                <td colspan="6">
                                    <button class="btn green wphrmEmployeeAttendanceMark" style="margin-top: 14px;" type="submit"><i class="fa fa-check"></i><?php _e('Submit', 'wphrm'); ?></button>
                                </td> </tr>
                        </table>

                    </form>
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div></div>
<!-- END PAGE CONTENT-->
<script>
    jQuery(function (argument) {
        jQuery('[type="checkbox"]').bootstrapSwitch();
        jQuery('#my-select').searchableOptionList({maxHeight: '250px'});
    });
</script>
