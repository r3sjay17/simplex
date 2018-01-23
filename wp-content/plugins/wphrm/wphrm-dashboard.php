<?php
if ( ! defined( 'ABSPATH' ) ) exit;
wp_enqueue_style('wphrm-fullcalendar-css');
wp_enqueue_style('wphrm-bootstrap-select-css');
wp_enqueue_script('wphrm-graph-js');
global $current_user, $wpdb, $wp_query;
$wphrmCurrentuserId = $current_user->ID;
$wphrmUserRole = implode(',', $current_user->roles);
$usercounter = '';
$userBirthday = '';
$leavecounter = '';
$wphrmUsers = $this->WPHRMGetAllEmployees();
$datas=array();
foreach($wphrmUsers as $wphrmUser){
    if(!in_array('administrator', $wphrmUser->roles)){
        $datas[] = $wphrmUser->ID;
    }
}

$currentDate = esc_sql(date('Y-m-d')); // esc
$currentDateChange = esc_sql(date('d-m-Y')); // esc
$lastDateChanges = esc_sql(date('m-Y')); // esc
$last_date_change = esc_sql('31-' . $lastDateChanges); // esc
$usercounter = count($datas); // esc
$absent = esc_sql('absent'); // esc

$pending = esc_sql('pending'); // esc
$wphrmExpenseReportInformation = esc_sql('wphrmExpenseReportInfo'); // esc
$wphrmLeaveapplication = $wpdb->get_results("SELECT * FROM $this->WphrmLeaveApplicationTable WHERE `date` != '0000-00-00' AND `toDate` != '0000-00-00' AND applicationStatus = '$pending'");
foreach ($wphrmLeaveapplication as $key => $wphrmLeaveapplications) {
    $leavecounter = $leavecounter + count($wphrmLeaveapplications);
}
$wphrmExpenseReportInfo = array();
$wphrmExpenseReportInfos = $wpdb->get_row("SELECT * FROM $this->WphrmSettingsTable WHERE `settingKey` = '$wphrmExpenseReportInformation'");
if (!empty($wphrmExpenseReportInfos)) {
    $wphrmExpenseReportInfo = unserialize(base64_decode($wphrmExpenseReportInfos->settingValue));
}
$wphrmGetPagePermissions = $this->WPHRMGetPagePermissions();
$wphrmLetestNotice = $wpdb->get_results("SELECT * FROM $this->WphrmNoticeTable ORDER BY id DESC LIMIT 3");
$wphrmTotalCountNotices = $wpdb->get_row("SELECT COUNT(*) as totalNotice FROM $this->WphrmNoticeTable ORDER BY id");
$wphrmViewOnlyPermissionPages = $this->WPHRMViewOnlyPermissionPages();
//var_dump(get_option('page_on_front', 0));
?>

<style>
    .fc-title {
        cursor: pointer!important;
    }
    .buttonclass {
        float: right;position: relative;top: -40px;
    }
</style>
<div class="preloader">
    <span class="preloader-custom-gif"></span>
</div>
<div style="padding-left: 0px; padding-right:20px; padding-top:0px;" class="col-md-12">
    <!-- BEGIN PAGE HEADER-->
    <h3 class="page-title"><?php _e('Dashboard', 'wphrm'); ?></h3>
    <div class="page-bar" style="margin-bottom: 23px;">
        <?php

        if(isset($wphrmUserRole) && !in_array('administrator', $current_user->roles)){
            if(!in_array($wphrmUserRole, array('subscriber','editor'))){
                $changeRole = $this->WPHRMChangeRolePermission('status');
                if($changeRole == 'yes'){
                    $assignRole =  str_replace('_', ' ', $wphrmUserRole); ?>
        <a class="btn hide yellow buttonclass" onclick="wphrmChangeRole('adddatabaserole');"><?php _e('Switch to ', 'wphrm'); ?><?php echo strtoupper($assignRole); ?><?php _e(' Profile', 'wphrm'); ?></a>
        <?php }else{
        ?>
        <a class="btn hide blue buttonclass" onclick="wphrmChangeRole('adddefaultrole');"><?php _e('Switch to User Profile', 'wphrm'); ?></a>

        <?php  } } } ?>
        <ul class="page-breadcrumb">
            <li><i class="fa fa-home"></i><?php _e('Home', 'wphrm'); ?><i class="fa fa-angle-right"></i></li>
            <li><?php _e('Dashboard', 'wphrm'); ?></li>
        </ul>
    </div>
    <!-- END PAGE HEADER-->
    <!-- BEGIN DASHBOARD MODULES STATS -->
    <input type="hidden" id="employee_id" value="<?php echo esc_attr($current_user->ID); ?>">

    <!-- WP-HRM Attendances Module -->
    <?php if (in_array('manageOptionsDashboard', $wphrmGetPagePermissions)) { ?>

    <?php if (in_array('manageOptionsAttendances', $wphrmViewOnlyPermissionPages)) { ?>
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-light green" href="?page=wphrm-mark-attendance">
            <div class="visual"><i class="fa fa-book"></i></div>
            <div class="details">
                <div class="number"><?php echo esc_html(date('d-F-Y')); ?></div>
                <div class="desc"><?php _e("Today's Attendance Record", 'wphrm'); ?></div>
            </div>
        </a>
    </div>
    <?php } else if(isset($wphrmUserRole) && (in_array('administrator', $current_user->roles) || in_array('hr_manager', $current_user->roles))){ ?>
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-light green" href="?page=wphrm-mark-attendance">
            <div class="visual"><i class="fa fa-book"></i></div>
            <div class="details">
                <div class="number"><?php echo esc_html(date('d-F-Y')); ?></div>
                <div class="desc"><?php _e("Today's Attendance Record", 'wphrm'); ?></div>
            </div>
        </a>
    </div>
    <?php } ?>
    <?php if (in_array('manageOptionsEmployee', $wphrmViewOnlyPermissionPages)) { ?>
    <!-- WP-HRM Employees Module -->
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-light blue-soft" href="?page=wphrm-employees">
            <div class="visual"><i class="fa fa-user"></i></div>
            <div class="details">
                <div class="number"><?php echo esc_html($usercounter); ?></div>
                <div class="desc"><?php _e('Employees', 'wphrm'); ?></div>
            </div>
        </a>
    </div>
    <?php } else if(isset($wphrmUserRole) && (in_array('administrator', $current_user->roles) || in_array('hr_manager', $current_user->roles)) ){ ?>
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-light blue-soft" href="?page=wphrm-employees">
            <div class="visual"><i class="fa fa-user"></i></div>
            <div class="details">
                <div class="number"><?php echo esc_html($usercounter); ?></div>
                <div class="desc"><?php _e('Employees', 'wphrm'); ?></div>
            </div>
        </a>
    </div>
    <?php } ?>

    <?php if (in_array('manageOptionsLeaveApplications', $wphrmViewOnlyPermissionPages)) { ?>
    <!-- WP-HRM Leave Module -->
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-light red-soft" href="?page=wphrm-leaves-application">
            <div class="visual"><i class="fa fa-envelope"></i></div>
            <div class="details">
                <div class="number">
                    <?php
                    if ($leavecounter == '') {
                        _e('No', 'wphrm');
                    } else {
                        echo esc_html($leavecounter);
                    }
                    ?>
                </div>
                <div class="desc"><?php _e('Pending Leave Applications', 'wphrm'); ?></div>
            </div>
        </a>
    </div>
    <?php } else if(isset($wphrmUserRole) && (in_array('administrator', $current_user->roles) || in_array('hr_manager', $current_user->roles)) ){ ?>
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-light red-soft" href="?page=wphrm-leaves-application">
            <div class="visual"><i class="fa fa-envelope"></i></div>
            <div class="details">
                <div class="number">
                    <?php
                    if ($leavecounter == '') {
                        _e('No', 'wphrm');
                    } else {
                        echo esc_html($leavecounter);
                    }
                    ?>
                </div>
                <div class="desc"><?php _e('Pending Leave Applications', 'wphrm'); ?></div>
            </div>
        </a>
    </div>
    <?php } ?>

    <div class="clearfix"> </div>
    <div class="row">
        <!-- DASHBOARD ATTENDANCE MODULE -->
        <div class="col-md-8 col-sm-12">
            <div class="portlet box blue ">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-share font-blue-steel hide"></i>
                        <span class="caption-subject font-blue-steel bold uppercase"><?php _e("Attendance Record", 'wphrm'); ?></span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div id="Dashboard_Calendar" class="has-toolbar"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-12">
            <div class="xcol-md-12 xcol-sm-12">
            <div class="portlet box blue notice-board">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-share font-blue-steel hide"></i>
                        <span class="caption-subject font-blue-steel bold uppercase" ><?php _e('Notice Board', 'wphrm'); ?></span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="scroller" style="height: 300px;" data-always-visible="1" data-rail-visible="0">
                        <div class="cont-col2">
                            <div class="col-lg-12">
                                <?php
                                $countNotice = 1;
                                foreach ($wphrmLetestNotice as $wphrmLetestNotices) {
                                    if (isset($wphrmLetestNotices->wphrmtitle) && $wphrmLetestNotices->wphrmtitle != '') :
                                ?>
                                <div class="notices">
                                    <span class="notice-title"><?php  echo $countNotice .'. '. wp_trim_words($wphrmLetestNotices->wphrmtitle, 20, '...'); ?></span>
                                    <?php
                                    $wphrmAction = 'wphrm-add-notice';
                                    if (in_array('manageOptionsDashboard', $wphrmGetPagePermissions))  : $wphrmAction = 'wphrm-add-notice';
                                    else : $wphrmAction = 'wphrm-view-notice';
                                    endif;
                                    ?>
                                    <span class="notice-readmore">
                                        <a style="margin-left: 18px;" href="?page=<?php echo $wphrmAction; ?>&notice_id=<?php
                                    if (isset($wphrmLetestNotices->id)) : echo esc_html($wphrmLetestNotices->id);
                                    endif;
                                                                            ?>" data-toggle="modal"  class="btn btn-xs blue">
                                            <?php _e('Read More', 'wphrm'); ?>
                                        </a>
                                    </span>
                                    <div style="clear:both"></div>
                                </div>
                                <?php endif; ?>
                                <?php
                                    $countNotice++;
                                }
                                ?>
                                <br>
                                <?php if ($wphrmTotalCountNotices->totalNotice > 3) { ?>
                                <a style="float: left; margin-left: 18px;" href="?page=wphrm-notice" data-toggle="modal"  class="btn btn-xs blue">
                                    <?php _e('More Notices', 'wphrm'); ?>
                                </a>
                                <?php
                                                                                    } else {
        if ($wphrmTotalCountNotices->totalNotice == 0) {
                                ?>
                                <div class="col-sm-12" style="text-align:center"><strong><?php _e('No Notices Found.', 'wphrm'); ?></strong></div>
                                <?php
        }
    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="xcol-md-12 xcol-sm-12">
            <div class="portlet box blue birthday-board ">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-share font-blue-steel hide"></i>
                        <span class="caption-subject font-blue-steel bold uppercase" ><?php _e("Birthdays", 'wphrm'); ?></span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="scroller" style="height: 300px;" data-always-visible="1" data-rail-visible="0">
                        <div class="cont-col2">
                            <div class="col-sm-5">
                                <h2 style="font-size: 12px;"><?php _e("Today's Birthdays", 'wphrm'); ?></h2>
                                <p><?php _e('Date  : ', 'wphrm'); ?><?php echo esc_html(date('d-F', strtotime($currentDateChange))); ?></p>
                                <div class="overflowbirthday">
                                    <?php
                                    $countTotalEmployeeBirthDays = 0;
                                    foreach ($wphrmUsers as $key => $userdata) {
                                        $userRole = implode(',', $userdata->roles);
                                        $currentMonthDay = date('m-d');
                                        $wphrmUserinfo = $this->WPHRMGetUserDatas($userdata->ID, 'wphrmEmployeeInfo');
                                        if (isset($wphrmUserinfo['wphrm_employee_bod'])) {
                                            $employeebirthMonthDay = date('m-d', strtotime($wphrmUserinfo['wphrm_employee_bod']));
                                            if ($currentMonthDay == $employeebirthMonthDay) {
                                                $countTotalEmployeeBirthDays++;
                                    ?>
                                    <div>
                                        <div class="desc-img">
                                            <div class="desc">
                                                <div style="margin-bottom: 10px"><i class="fa fa-birthday-cake"></i>&nbsp; <?php
                                                if (isset($wphrmUserinfo['wphrm_employee_fname'])) : echo esc_html($wphrmUserinfo['wphrm_employee_fname']);
                                                endif;
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="birthday">
                                                <?php if (isset($wphrmUserinfo['employee_profile']) && $wphrmUserinfo['employee_profile'] != '') { ?>
                                                <img src="<?php
                                                        if (isset($wphrmUserinfo['employee_profile'])) : echo esc_html($wphrmUserinfo['employee_profile']);
                                                        endif;
                                                          ?>" width="80" style="margin-bottom: 11px;" />
                                                <?php
                                                    } else {
                                                        if ($wphrmUserinfo['wphrm_employee_gender'] == 'Male') {
                                                ?>
                                                <img style="margin-bottom: 11px;" src="<?php echo esc_attr(plugins_url('assets/images/default-male.jpeg', __FILE__)); ?>" width="80" />
                                                <?php } else { ?>
                                                <img style="margin-bottom: 11px;" src="<?php echo esc_attr(plugins_url('assets/images/default-female.jpeg', __FILE__)); ?>" width="80" />
                                                <?php
                                                             }
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                            }
                                        }
                                    }
                                    if ($countTotalEmployeeBirthDays == 0) {
                                    ?>
                                    <div class="col-sm-12 float-left"><strong><?php _e('No Birthdays Found.', 'wphrm'); ?></strong></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="col-sm-7 overflowbirthday" >
                                <h2 class="upcomin-h2"><?php _e("Upcoming Birthdays", 'wphrm'); ?></h2>
                                <table class="table table-striped table-bordered table-birthday table-hover">
                                    <thead>
                                        <tr>
                                            <th><?php _e('Pic', 'wphrm'); ?></th>
                                            <th><?php _e('Name', 'wphrm'); ?></th>
                                            <th> <i class="fa fa-birthday-cake"></i><?php _e('Date', 'wphrm'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $countTotalEmployeeupcomingBirthDays = 0;
                                        foreach ($wphrmUsers as $key => $userdata) {
                                            $userRole = implode(',', $userdata->roles);
                                            $currentyear = date('Y');
                                            $currentMonthDay = date('d-m-'.$currentyear.'');
                                            $endOFMonthDay = date('Y-m-d', strtotime("+30 days"));
                                            $wphrmUserinfo = $this->WPHRMGetUserDatas($userdata->ID, 'wphrmEmployeeInfo');
                                            if (isset($wphrmUserinfo['wphrm_employee_bod']) && $wphrmUserinfo['wphrm_employee_bod'] !='') {
                                                $employeebirthMonthDay = date('d-m', strtotime($wphrmUserinfo['wphrm_employee_bod']));
                                                $employeebirthMonthDay = $employeebirthMonthDay.'-'.$currentyear;

                                                if (strtotime($currentMonthDay) < strtotime($employeebirthMonthDay) &&  strtotime($endOFMonthDay) >= strtotime($employeebirthMonthDay)) {
                                                    $countTotalEmployeeupcomingBirthDays++;
                                        ?>
                                        <tr>
                                            <td>
                                                <?php if (isset($wphrmUserinfo['employee_profile']) &&  $wphrmUserinfo['employee_profile']!= '') { ?>
                                                <img src="<?php
                                            if (isset($wphrmUserinfo['employee_profile'])) : echo esc_attr($wphrmUserinfo['employee_profile']);
                                            endif;
                                                          ?>" width="60"><br>
                                                <?php
                                        } else {
                                            if ($wphrmUserinfo['wphrm_employee_gender'] == 'Male') {
                                                ?>
                                                <img src="<?php echo esc_attr(plugins_url('assets/images/default-male.jpeg', __FILE__)); ?>" width="60">
                                                <?php } else { ?>
                                                <img src="<?php echo esc_attr(plugins_url('assets/images/default-female.jpeg', __FILE__)); ?>" width="60">
                                                <?php
                                                             }
                                        }
                                                ?>
                                            </td>
                                            <td><?php
                                                    if (isset($wphrmUserinfo['wphrm_employee_fname'])) : echo esc_html($wphrmUserinfo['wphrm_employee_fname']);
                                                    endif;
                                                ?></td><td>
                                            <?php
                                                    if (isset($wphrmUserinfo['wphrm_employee_bod'])) : echo apply_filters('wphrm_employee_bod', $wphrmUserinfo['wphrm_employee_bod']);
                                                    endif;
                                            ?> </td>
                                        </tr>

                                        <?php
                                                }
                                            }
                                        }
                                        if ($countTotalEmployeeupcomingBirthDays == 0) {
                                        ?>
                                        <tr><td colspan="4">
                                            <div class="col-sm-12" style="text-align:left;"><strong><?php _e('No upcoming birthdays found in next 30 days.', 'wphrm'); ?></strong></div>
                                            </td></tr>
                                        <?php } ?>

                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
    <?php } ?>

    <!--JAF Custom Hooks-->
    <?php do_action('jaf_dashboard_before_calendar'); ?>

    <?php if(!in_array('manageOptionsDashboard', $wphrmGetPagePermissions)){ include 'jaf-wphrm-employee-dashboard.php'; return; } ?>
    <!-- DASHBOARD BIRTHDAYS MODULE -->
    

    <div class="clearfix"> </div>
    <!-- END DASHBOARD MODULES STATS -->

    <?php
    wp_enqueue_script('wphrm-fullcalender-js');
    wp_enqueue_script('wphrm-dashboard-calenader-js');

    ?>
    <script>
        jQuery(function (argument) {
            jQuery('[type="checkbox"]').bootstrapSwitch();
        });
        jQuery(document).ready(function () {
            Dashboard_Calendar.init();
            jQuery('#financial_graph').bic_calendar({});
        });
    </script>

<?php $expiration_notice = 0; ?>
<div id="emp_fin_passport_expiration" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false" style="margin-top: 5%">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
                <h4 class="modal-title"><strong><i class="fa fa-exclamation-triangle"></i> <?php _e('Expiration Notice', 'wphrm'); ?></strong></h4>
            </div>
            <div class="modal-body">
                <div class="portlet-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Passport</th>
                                <th>FIN Pass</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach( $datas as $emp ) {
                            $employee_complete_info = $this->get_user_complete_info( $emp );
                            $wphrmUserinfo = $this->WPHRMGetUserDatas($emp, 'wphrmEmployeeInfo');
                            $emp_name = $wphrmUserinfo['wphrm_employee_fname'].' '.$wphrmUserinfo['wphrm_employee_lname'];

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

                            if( $passport_expiration_remaining < 7 && $passport_expiration_remaining != 0 || $fin_expiration_remaining < 7 && $fin_expiration_remaining != 0 ) {
                                $expiration_notice++;
                            }

                            if( $passport_expiration_remaining < 7 && $passport_expiration_remaining != 0 || $fin_expiration_remaining < 7 && $fin_expiration_remaining != 0 ) { 
                                echo '<tr>';
                                    echo '<td>'.$emp_name.'</td>';
                                    echo '<td>';
                                        if( $passport_expiration_remaining < 7 && $passport_expiration_remaining != 0 ) { 
                                            echo date('F j, Y', strtotime( $employee_complete_info->work_permit_info->wphrm_employee_passport_expiration )); 
                                        } else { 
                                            echo '--'; 
                                        }
                                    echo '</td>';
                                    echo '<td>';
                                        if( $fin_expiration_remaining < 7 && $fin_expiration_remaining != 0 ) { 
                                            echo date('F j, Y', strtotime( $employee_complete_info->work_permit_info->wphrm_employee_permit_expiration )); 
                                        } else { 
                                            echo '--'; 
                                        }
                                    echo '</td>';
                                echo '</tr>';
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>
</div>
<input type="hidden" id="emp_has_expiration" value="<?php echo $expiration_notice?>">