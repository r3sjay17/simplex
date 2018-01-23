<?php
if (!defined('ABSPATH'))
    exit;
global $wpdb;
$salaryTotal = 0;
$wphrmEarningtotal = '';
$salaryTotalEarning = '';
$wphrmDeductiontotal = '';
$salaryTotalDeduction = '';
$wphrmUsers = $this->WPHRMGetEmployees();
$wphrmGeneralSettingsInfo = $this->WPHRMGetSettings('wphrmGeneralSettingsInfo');
$currency = explode('-', $wphrmGeneralSettingsInfo['wphrm_currency']);
$wphrmEarningfiledskeyinformation = esc_sql('wphrmEarningfiledskey'); // esc
$wphrmEarningfiledskey = $wpdb->get_results("SELECT * FROM $this->WphrmSalaryTable WHERE  `employeeKey`='$wphrmEarningfiledskeyinformation'");
foreach ($wphrmEarningfiledskey as $wphrmEarningfiledskeys) {
    $wphrmEarningInfo = unserialize(base64_decode($wphrmEarningfiledskeys->employeeValue));
    foreach ($wphrmEarningInfo['wphrmEarningValue'] as $wphrmEarningInfos) {
        $wphrmEarningtotal = $wphrmEarningInfos + $salaryTotalEarning;
        $salaryTotalEarning = $wphrmEarningtotal;
    }
}

$wphrmDeductionFiledsKey = esc_sql('wphrmDeductionfiledskey'); // esc
$wphrmDeductionfiledskey = $wpdb->get_results("SELECT * FROM $this->WphrmSalaryTable WHERE  `employeeKey`='$wphrmDeductionFiledsKey'");
foreach ($wphrmDeductionfiledskey as $wphrmDeductionfiledskeys) {
    $wphrmDeductionInfo = unserialize(base64_decode($wphrmDeductionfiledskeys->employeeValue));
    foreach ($wphrmDeductionInfo['wphrmDeductionValue'] as $wphrmDeductionInfos) {
        $wphrmDeductiontotal = $wphrmDeductionInfos + $salaryTotalDeduction;
        $salaryTotalDeduction = $wphrmDeductiontotal;
    }
}
$salaryTotal = ($salaryTotalEarning - $salaryTotalDeduction);
$wphrmGetPagePermissions = $this->WPHRMGetPagePermissions();
$wphrmSalaryAccording = $this->WPHRMGetSettings('wphrsalaryDayOrHourlyInfo');
?>
<!-- BEGIN PAGE HEADER-->
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="preloader">
    <span class="preloader-custom-gif"></span>
</div>
<div id="wphrm-generate-salary" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
                <h4 class="modal-title"><strong><i class="fa fa-file-excel-o"></i><?php _e('Generate Salary Report', 'wphrm'); ?></strong></h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-success display-hide" id="reportSuccess">
                    <button class="close" data-close="alert"></button>
                </div>
                <div class="alert alert-danger display-hide" id="reportError">
                    <button class="close" data-close="alert"></button>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    <form action="?page=wphrm-employee-salary-reports" method="post"  class="form-horizontal">
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label col-md-4"><?php _e('Employee Name', 'wphrm'); ?>  </label>
                                <div class="col-md-8">
                                    <p><select id="my-select" name="employee-id[]"  multiple="multiple" required>
                                            <?php
                                            $wphrmUserRole = implode(',', $current_user->roles);
                                            $wphrmUsers = get_users($wphrmUserRole);
                                            foreach ($wphrmUsers as $key => $userdata) {
                                                
                                                        $wphrmEmployeeBasicInfos = get_user_meta($userdata->ID, 'wphrmEmployeeInfo', true);
                                                        $wphrmEmployeeBasicInfoss = unserialize(base64_decode($wphrmEmployeeBasicInfos));
                                                        ?>
                                                        <option value="<?php echo esc_attr($userdata->ID); ?>"><?php
                                                            if (isset($wphrmEmployeeBasicInfoss['wphrm_employee_fname'])) : echo esc_html($wphrmEmployeeBasicInfoss['wphrm_employee_fname']);
                                                            endif;
                                                            if (isset($wphrmEmployeeBasicInfoss['wphrm_employee_lname'])) : echo ' ' . esc_html($wphrmEmployeeBasicInfoss['wphrm_employee_lname']);
                                                            endif;
                                                            ?></option>
                                                                <?php
                                                   
                                            }
                                            ?>
                                                        
                                        </select></p>
                                </div>
                            </div>
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="control-label col-md-4"><?php _e('From Date', 'wphrm'); ?>  </label>
                                    <div class="col-md-8">
                                        <input required="" class="form-control form-control-inline input-medium month-year"  data-date-format="dd-mm-yyyy" name="from-date"  type="text" value="" placeholder="From Date"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="control-label col-md-4"><?php _e('To Date', 'wphrm'); ?>  </label>
                                    <div class="col-md-8">
                                        <input required="" class="form-control form-control-inline input-medium month-year"  data-date-format="dd-mm-yyyy" name="to-date"  type="text" value="" placeholder="To Date"/>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        <div class="modal-footer">
                            <button type="submit"  class=" btn blue"> <i class="fa fa-file-excel-o"></i><?php _e('Generate Excel', 'wphrm'); ?></button>
                            <button type="button" data-dismiss="modal" id="" aria-hidden="true" class="btn red"><i class="fa fa-times"></i><?php _e('Cancel', 'wphrm'); ?></button>
                        </div>
                    </form>
                    <!-- END FORM-->
                </div>
            </div>
        </div></div></div>


<div style="padding-left: 0px; padding-right:20px; padding-top:0px;" class="col-md-12">
    <h3 class="page-title"><?php _e('Salary Management', 'wphrm'); ?></h3>
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li><i class="fa fa-home"></i><?php _e('Home', 'wphrm'); ?><i class="fa fa-angle-right"></i></li>
            <li><?php _e('Salary Management', 'wphrm'); ?></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php if (isset($wphrmSalaryAccording['wphrm-according']) && $wphrmSalaryAccording['wphrm-according'] == 'Week'){
            } else{ ?>
                <a class="btn green " href="#wphrm-generate-salary"  data-toggle="modal"><i class="fa fa-edit"></i><?php _e('Generate Salary Report', 'wphrm'); ?> </a>
            <?php }?>
            <div class="portlet box blue calendar">
                <div class="portlet-title">
                    <div class="caption"> <i class="fa fa-list"></i><?php _e('List of Employees', 'wphrm'); ?> </div>
                </div>
                <div class="portlet-body">
                     <?php if (isset($wphrmSalaryAccording['wphrm-according']) && $wphrmSalaryAccording['wphrm-according'] != 'Week'){ ?>
                    <div class="col-md-12 col-sm-12"  style="text-align: right; margin-left: 15px;">                          
                        <label style="font-weight: normal; font-size: 14px;">
                            <?php _e('Total Salary', 'wphrm'); ?> : &nbsp;<input style="background: none repeat scroll 0 0 #fff;
    color: #444;width: 225px;" disabled class="form-control input-small input-inline"  value="<?php if(isset($currency[0])){ echo esc_attr($currency[0]); }else{ echo '&#36;'; } ?> <?php if(isset($salaryTotal)){ echo esc_attr($salaryTotal); } ?>">
                        </label>
                    </div>
                     <?php } ?>
                    <table class="table table-striped table-bordered table-hover" id="wphrmDataTable">
                        <thead>
                            <tr>
                                <th><?php _e('S.No', 'wphrm'); ?></th>
                                <th class="text-center"><?php _e('Name', 'wphrm'); ?></th>
                                <th><?php _e('Department', 'wphrm'); ?></th>
                                <th><?php _e('Email', 'wphrm'); ?></th>
                                <th><?php _e('Phone No', 'wphrm'); ?>.</th>
                                <th><?php _e('Status', 'wphrm'); ?></th>
                                <th><?php _e('Pending Requests', 'wphrm'); ?></th>
                                <th><?php _e('Actions', 'wphrm'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $salaryEmployeeCounter = 0;
                            $employeeDepartmentsLoads = '';
                            $wphrmDepartments = '';
                            $wphrmDepartmentInfo = '';
                            if (!empty($wphrmUsers)) {
                                $j = 1;
                                foreach ($wphrmUsers as $key => $userdata) {
                                  
                                    $wphrmEmployeeInfo = $this->WPHRMGetUserDatas($userdata->data->ID, 'wphrmEmployeeInfo');
                                    $wphrmEmployeeFirstName = get_user_meta($userdata->data->ID, 'first_name', true);
                                    $wphrmEmployeeLastName = get_user_meta($userdata->data->ID, 'last_name', true);
                                    if (isset($wphrmEmployeeInfo['wphrm_employee_status']) && $wphrmEmployeeInfo['wphrm_employee_status'] == 'Active') {
                                        if (isset($wphrmEmployeeInfo['wphrm_employee_department']) && $wphrmEmployeeInfo['wphrm_employee_department'] != '') {
                                            $employeeDepartmentsLoads = esc_sql($wphrmEmployeeInfo['wphrm_employee_department']); // esc
                                            $wphrmDepartments = $wpdb->get_row("SELECT * FROM  $this->WphrmDepartmentTable  WHERE `departmentID` = '$employeeDepartmentsLoads'");
                                            $wphrmTotalSalary = $wpdb->get_row("SELECT * FROM  $this->WphrmSalaryTable  WHERE `employeeValue` = 'generated' AND `employeeID`  = '$userdata->ID'");

                                            // echo "<pre>"; print_r($wphrmTotalSalary); 
                                            if ($wphrmDepartments != '') {
                                                $wphrmDepartmentInfo = unserialize(base64_decode($wphrmDepartments->departmentName));
                                            }
                                        }
                                        ?>
                                        <tr id="row">
                                            <td><?php echo esc_html($j); ?></td>
                                            <td class="text-center"><?php if (isset($wphrmEmployeeFirstName)) : echo esc_html($wphrmEmployeeFirstName);
                            endif;
                            if (isset($wphrmEmployeeLastName)) : echo ' ' . esc_html($wphrmEmployeeLastName);
                            endif; ?>
                                            </td>
                                            <td><?php
                                                if (isset($wphrmDepartmentInfo['departmentName'])) : echo esc_html($wphrmDepartmentInfo['departmentName']);
                                                endif;
                                                ?>
                                            </td>
                                            <td> <?php
                                                if (isset($wphrmEmployeeInfo['wphrm_employee_email'])) : echo esc_html($wphrmEmployeeInfo['wphrm_employee_email']);
                                                endif;
                                                ?>
                                            </td>
                                            <td><?php
                                                if (isset($wphrmEmployeeInfo['wphrm_employee_phone'])) : echo esc_html($wphrmEmployeeInfo['wphrm_employee_phone']);
                                                endif;
                                                ?>
                                            </td>
                                            <td>
                                                <?php if (isset($wphrmEmployeeInfo['wphrm_employee_status']) && $wphrmEmployeeInfo['wphrm_employee_status'] == 'Active') { ?>  
                                                    <span class="label label-sm label-success"><?php _e('Active', 'wphrm'); ?></span>
                                                <?php } else { ?>
                                                    <span class="label label-sm label-danger"><?php _e('Inactive', 'wphrm'); ?></span>
                                                <?php } ?>
                                            </td>
                                            <td><?php
                                                $i = 1;
                                                $wphrmSalaryAccording = $this->WPHRMGetSettings('wphrsalaryDayOrHourlyInfo');
                                                $salarySliprequestinfomation = esc_sql('Salary Slip request'); // esc
                                                $wphrmNotification = $wpdb->get_results("SELECT * FROM  $this->WphrmNotificationsTable  WHERE `wphrmFromId` = '$userdata->ID' AND `notificationType`= '$salarySliprequestinfomation' ORDER BY id DESC LIMIT 2");
                                                if (!empty($wphrmNotification)) {
                                                    foreach ($wphrmNotification as $keys => $wphrmNotifications) {
                                                        $string = $wphrmNotifications->wphrmDesc;
                                                        $split = explode(" ", $string);
                                                        $year = esc_sql($split[count($split) - 1]); // esc
                                                        $month = esc_sql($split[count($split) - 2]); // esc
                                                        $matchDate = date($year . '-' . $month . '-01');
                                                        $wphrmCheckgenerated = $wpdb->get_row("SELECT * FROM  $this->WphrmSalaryTable  WHERE `date` = '$matchDate' AND `employeeValue`= 'generated'");
                                                        if (empty($wphrmCheckgenerated)) {
                                                         if (isset($wphrmSalaryAccording['wphrm-according']) && $wphrmSalaryAccording['wphrm-according'] == 'Week') {     
                                                            ?>
                                                <a class="text-decoration" onclick="WPHRMNotificationStatusWeekChangeInfo(<?php echo esc_js($wphrmNotifications->id); ?>,'<?php echo esc_js($userdata->ID); ?>')"><span class="label label-sm label-danger"><?php echo $month . ' ' . str_replace('.', ' ', $year); ?></a>
                                                <?php }else{ ?>          
                                                <a class="text-decoration" onclick="WPHRMNotificationStatusChangeInfo(<?php echo esc_js($wphrmNotifications->id); ?>, '<?php echo esc_js($userdata->ID); ?>', '<?php echo esc_js($month); ?>', '<?php echo esc_js($year); ?>')"><span class="label label-sm label-danger"><?php echo $month . ' ' . str_replace('.', ' ', $year); ?></a><br>
                                                        <?php } }
                                                    }
                                                } else {
                                                    
                                                } ?>
                                            </td>
                                            <td class="">
                                                <?php if (isset($wphrmSalaryAccording['wphrm-according']) && $wphrmSalaryAccording['wphrm-according'] == 'Week'){
                                                 ?>
                                                <a class="btn purple" href='?page=wphrm-select-financials-week&employee_id=<?php echo esc_attr($userdata->ID); ?>'>
                                                    <i class="fa fa-info"></i><?php _e('Salary Details', 'wphrm'); ?>
                                                </a>
                                                <?php }else{ ?>
                                                   <a class="btn purple" href='?page=wphrm-select-financials-month&employee_id=<?php echo esc_attr($userdata->ID); ?>'>
                                                    <i class="fa fa-info"></i><?php _e('Salary Details', 'wphrm'); ?>
                                                </a> 
                                               <?php } ?>
                                                
                                          <?php
                                         if (isset($wphrmSalaryAccording['wphrm-according']) && $wphrmSalaryAccording['wphrm-according'] != 'Week'){
                                               
                                          if (!empty($wphrmTotalSalary)) { ?>
                                                    <a class="btn blue" href='?page=wphrm-total-salary&employee_id=<?php echo esc_attr($userdata->ID); ?>'>
                                                        <i class="fa fa-money"></i><?php _e('Total Salary', 'wphrm'); ?>
                                                    </a>
                                        <?php } else { ?>
                                                    <a class="btn blue" disabled>
                                                        <i class="fa fa-money"></i><?php _e('Total Salary', 'wphrm'); ?>
                                                    </a>
                                         <?php } } ?>
                                            </td>
                                        </tr>
                                        <?php
                                        $salaryEmployeeCounter++;
                                        $j++;
                                    }
                                }
                            }
                            if ($salaryEmployeeCounter == 0) {
                                ?>
                                <tr>
                                    <td colspan="8"><?php _e('No salary data found in database.', 'wphrm'); ?>
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