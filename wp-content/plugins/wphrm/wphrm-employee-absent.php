<?php
if (!defined('ABSPATH'))
    exit;
global $wpdb;
$wphrmUsers = $this->WPHRMGetEmployees();
$condition = '';
if (isset($_REQUEST['condition']) && $_REQUEST['condition'] == 'absent') {
    $condition = "AND  `status` = 'absent'";
} else if (isset($_REQUEST['condition']) && $_REQUEST['condition'] == 'present') {
    $condition = "AND  `status` = 'present'";
} else if (isset($_REQUEST['condition']) && $_REQUEST['condition'] == 'halfday') {
    $condition = "AND  `status` = '' AND `halfDayType`= 'halfday'";
}
?> 
<!-- BEGIN PAGE CONTENT-->
<div class="preloader">
    <span class="preloader-custom-gif"></span>
</div>
<div style="padding-left: 0px; padding-right:20px; padding-top:0px;" class="col-md-12">
    <!-- BEGIN PAGE HEADER-->
    <h3 class="page-title"><?php
        if (isset($_REQUEST['condition']) && $_REQUEST['condition'] == 'absent') {
            _e('Employees Absence', 'wphrm');
        } else if (isset($_REQUEST['condition']) && $_REQUEST['condition'] == 'present') {
            _e('Employees Present', 'wphrm');
        } else if (isset($_REQUEST['condition']) && $_REQUEST['condition'] == 'halfday') {
            _e('Employees Half Day', 'wphrm');
        }
        ?></h3>
    <div class="page-bar">        
        <ul class="page-breadcrumb">
            <li><i class="fa fa-home"></i><?php _e('Home', 'wphrm'); ?><i class="fa fa-angle-right"></i></li>
            <li> <?php
                if (isset($_REQUEST['condition']) && $_REQUEST['condition'] == 'absent') {
                    _e('Employees Absence', 'wphrm');
                } else if (isset($_REQUEST['condition']) && $_REQUEST['condition'] == 'present') {
                    _e('Employees Present', 'wphrm');
                } else if (isset($_REQUEST['condition']) && $_REQUEST['condition'] == 'halfday') {
                    _e('Employees Half Day', 'wphrm');
                }
                ?></li>        
        </ul>
    </div>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-12">
            <a class="btn green " href="?page=wphrm-dashboard"><i class="fa fa-arrow-left"></i><?php _e('Back', 'wphrm'); ?> </a>
            <div class="portlet box blue calendar">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-list"></i>
                        <?php
                        if (isset($_REQUEST['condition']) && $_REQUEST['condition'] == 'absent') {
                            _e(' List of Employees Absence', 'wphrm');
                        } else if (isset($_REQUEST['condition']) && $_REQUEST['condition'] == 'present') {
                            _e('List of Employees Present', 'wphrm');
                        } else if (isset($_REQUEST['condition']) && $_REQUEST['condition'] == 'halfday') {
                            _e('List of Employees Half Day ', 'wphrm');
                        }
                        ?>

                    </div>
                </div>
                <div class="portlet-body">
                    <table class="wphrmtable table table-striped table-bordered table-hover" id="wphrmDataTable">
                        <thead>
                            <tr>
                                <th><?php _e('S.No', 'wphrm'); ?></th>
                                <th><?php _e('EmployeeID', 'wphrm'); ?></th>
                                <th class="text-center"><?php _e('Image', 'wphrm'); ?></th>
                                <th><?php _e('Name', 'wphrm'); ?></th>
                                <th><?php _e('Date', 'wphrm'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            $attendance_date = esc_sql($_REQUEST['date']); // esc
                            $currentMonth = esc_sql(date('m')); // esc
                            foreach ($wphrmUsers as $key => $userdata) {
                                $wphrmEmployeeInfo = $this->WPHRMGetUserDatas($userdata->ID, 'wphrmEmployeeInfo');
                                $userdataID = esc_sql($userdata->ID); // esc
                                if (isset($wphrmEmployeeInfo['wphrm_employee_status']) && $wphrmEmployeeInfo['wphrm_employee_status'] == 'Active') {
                                    $getAttendancebyId = $wpdb->get_row("select * from $this->WphrmAttendanceTable where `date` = '" . $attendance_date . "' and `employeeID` ='" . $userdataID . "' $condition");
                                    if (!empty($getAttendancebyId)) {
                                        ?>
                                        <tr id="row">
                                            <td><?php echo esc_html($i); ?></td>
                                            <td>
                                                <?php
                                                if (isset($wphrmEmployeeInfo['wphrm_employee_uniqueid'])) :
                                                    echo esc_html($wphrmEmployeeInfo['wphrm_employee_uniqueid']);
                                                endif;
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if (isset($wphrmEmployeeInfo['employee_profile']) && $wphrmEmployeeInfo['employee_profile'] != '') { ?>
                                                    <img src="<?php
                                                    if (isset($wphrmEmployeeInfo['employee_profile'])) : echo esc_html($wphrmEmployeeInfo['employee_profile']);
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
                                            <td> <?php
                                                if (isset($wphrmEmployeeInfo['wphrm_employee_fname'])) : echo esc_html($wphrmEmployeeInfo['wphrm_employee_fname']);
                                                endif;
                                                ?><?php
                                                if (isset($wphrmEmployeeInfo['wphrm_employee_lname'])) : echo ' ' . esc_html($wphrmEmployeeInfo['wphrm_employee_lname']);
                                                endif;
                                                ?></td>
                                            
                                            <td><?php
                                                if (isset($getAttendancebyId->date)) : echo esc_html(date('d-m-Y', strtotime($getAttendancebyId->date)));
                                                endif;
                                                ?></td>
                                        </tr>
                                        <?php
                                        $i++;
                                    }
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END PAGE CONTENT-->