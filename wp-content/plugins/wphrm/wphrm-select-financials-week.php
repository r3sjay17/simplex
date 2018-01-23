<?php
if (!defined('ABSPATH'))
    exit;
global $current_user, $wpdb;
$wphrmMessagesAddEmployee = $this->WPHRMGetMessage(25);
$wphrmUserRole = implode(',', $current_user->roles);
$wphrmGetPagePermissions = $this->WPHRMGetPagePermissions();

if ((isset($_REQUEST['employee_id']) && $_REQUEST['employee_id'] != '')) {
    if (isset($_REQUEST['employee_id'])) {
        $employee_id = esc_sql($_REQUEST['employee_id']); // esc
    }
} else {
    $employee_id = esc_sql($current_user->ID); // esc
}


$employeeSalaryGeneratedInformation = esc_sql('employeeSalaryGenerated'); // esc
$wphrm_info = $this->WPHRMGetUserDatas($employee_id, 'wphrmEmployeeInfo');
if (!empty($wphrm_info)) {
    $wphrm_bank_info = $this->WPHRMGetUserDatas($employee_id, 'wphrmEmployeeBankInfo');
    $wphrm_salary_info = $this->WPHRMGetUserDatas($employee_id, 'wphrmEmployeeSalaryInfo');
    $employee_departments_loads = '';
    $wphrmDepartments = '';
    $wphrmDepartmentInfo = '';
    $employee_designation_loads = '';
    $wphrm_designation = '';
    $wphrmDesignationInfo = '';

    $wphrmGeneratedSalary = $wpdb->get_results("SELECT * FROM $this->WphrmWeeklySalaryTable WHERE `employeeID` = $employee_id AND  `employeeKey` = '$employeeSalaryGeneratedInformation' Group By year(`date`)");
    $wphrmGenerate = $wpdb->get_results("SELECT * FROM $this->WphrmWeeklySalaryTable WHERE `employeeID` = $employee_id");
    if (isset($wphrm_info['wphrm_employee_department']) && $wphrm_info['wphrm_employee_department'] != '') {
        $employee_departments_loads = esc_sql($wphrm_info['wphrm_employee_department']); // esc
        $wphrmDepartments = $wpdb->get_row("SELECT * FROM $this->WphrmDepartmentTable WHERE `departmentID` = '$employee_departments_loads'");
        if ($wphrmDepartments != '') {
            $wphrmDepartmentInfo = unserialize(base64_decode($wphrmDepartments->departmentName));
        }
    }
    if (isset($wphrm_info['wphrm_employee_designation']) && $wphrm_info['wphrm_employee_designation'] != '') {
        $employee_designation_loads = esc_sql($wphrm_info['wphrm_employee_designation']); // esc
        $wphrm_designation = $wpdb->get_row("SELECT * FROM $this->WphrmDesignationTable WHERE `designationID` = '$employee_designation_loads'");
        if ($wphrm_designation != '') {
            $wphrmDesignationInfo = unserialize(base64_decode($wphrm_designation->designationName));
        }
    }
} else {
    ?>
    <h4><?php _e('Invalid Employee Request', 'wphrm'); ?></h4>
    <a class="btn green " href="?page=wphrm-salary" ><i class="fa fa-arrow-left"></i><?php _e('Back', 'wphrm'); ?></a>
    <?php wp_die(); ?>
    <?php
}

$wphrmMessagesDuplicate = $this->WPHRMGetMessage(40);
$wphrmEmployeeJoiningYear = date('Y');
$wphrmEmployeeJoiningMonth = date('m');
if (isset($wphrm_info['wphrm_employee_joining_date'])) :
    $wphrmEmployeeJoiningYear = date('Y', strtotime($wphrm_info['wphrm_employee_joining_date']));
    $wphrmEmployeeJoiningMonth = date('m', strtotime($wphrm_info['wphrm_employee_joining_date']));
endif;
wp_enqueue_script('wphrm-bic-calendar-js');
wp_localize_script('wphrm-bic-calendar-js', 'WPHRMEmployeeDatas', array('ajaxurl' => admin_url('admin-ajax.php'), 'wphrmEmployeeID' => $employee_id, 'wphrmEmployeeJoiningYear' => $wphrmEmployeeJoiningYear));
?>
<script>
    jQuery(document).ready(function () {
        jQuery('#calendari_lateral').bic_calendar({});
    });
</script> 
<div class="preloader">
    <span class="preloader-custom-gif"></span>
</div>
<div id="duplicateModal" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">           

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
                <h4 class="modal-title"><?php _e('Confirmation', 'wphrm'); ?></h4>
            </div>
            <div class="alert alert-success display-hide" id="Duplicate_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrmMessagesDuplicate); ?>
                <button class="close" data-close="alert"></button>
            </div>
            <div class="modal-body" id="info">
                <p><?php _e('Do you want to duplicate the salary slip ?', 'wphrm'); ?></p>
                <div class="modal-footer">
                    <button type="button" class="btn blue" onclick="wphrmyesDuplicate()" id="wphrmyesDuplicate"><i class="fa fa-check"></i><?php _e('Yes', 'wphrm'); ?> </button>
                    <button type="button"  aria-hidden="true" class="btn default" onclick="redirecttoWeeksalary()" ><i class="fa fa-times"></i><?php _e('No', 'wphrm'); ?></button>
                </div>
                <div id="wphrmyearchoose">
                    <div class="portlet-body form">
                        <!-- BEGIN FORM-->                 
                        <div class="form-body">
                            <form name="wphrm-duplicate-week-salary" id="wphrm-duplicate-week-salary" method="GET" action="" >
                                <input type="hidden" name="page" value="wphrm-salary-slip-details-week" />
                                <input type="hidden" name="wphrm-employee-id" value="<?php echo esc_attr($employee_id); ?>" />
                                <input type="hidden" name="wphrm-create-week-salary-month" value="" />
                                <input type="hidden" name="wphrm-create-week-salary-week-no" value="" />
                                <input type="hidden" name="wphrm-create-week-salary-year" class="yeardata" value="" />
                                
                                
                                <div class="form-group">
                                    <label class="control-label col-md-3"><?php _e('Year', 'wphrm'); ?>  </label>
                                    <div class="col-md-9">
                                        <select class ="form-control select2me"  data-live-search="true" id="wphrm-duplicate-to-salary-week-year" name="wphrm-duplicate-to-salary-week-year">
                                            <option value=""><?php _e('Select Year', 'wphrm'); ?></option>   
                                            <?php foreach ($wphrmGeneratedSalary as $wphrmGeneratedSalarys) { ?>
                                                <option value="<?php echo esc_attr(date('Y', strtotime($wphrmGeneratedSalarys->date))); ?>" ><?php echo esc_html(date('Y', strtotime($wphrmGeneratedSalarys->date))); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-3"><?php _e('Month', 'wphrm'); ?>  </label>
                                    <div class="col-md-9">
                                        <select class ="form-control select2me"  data-live-search="true" id="wphrm-duplicate-to-salary-week-month" name="wphrm-duplicate-to-salary-week-month">
                                            <option value=""><?php _e('Select Month', 'wphrm'); ?></option>                                           
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="control-label col-md-3"><?php _e('Week', 'wphrm'); ?>  </label>
                                    <div class="col-md-9">
                                        <select class ="form-control select2me"  data-live-search="true" id="wphrm-duplicate-to-salary-week-no" name="wphrm-duplicate-to-salary-week-no">
                                            <option value=""><?php _e('Select Week', 'wphrm'); ?></option>                                           
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer" style="border-top:none;"> <br>
                            <div class="form-group">
                                <button type="submit"  class=" btn blue" onclick="CreateDuplicateWeekSalarySlip();"> <i class="fa fa-files-o"></i><?php _e('Duplicate', 'wphrm'); ?></button>
                                <button type="button" data-dismiss="modal" onclick="CloseSalarySlip();" aria-hidden="true" class="btn red"><i class="fa fa-times"></i><?php _e('Cancel', 'wphrm'); ?></button>
                            </div>
                        </div>
                        <!-- END FORM-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- BEGIN PAGE CONTENT-->
<div style="padding-left: 0px; padding-right:20px; padding-top:0px;" class="col-md-12">
    <h3 class="page-title">
        <?php _e('Employee Salary', 'wphrm'); ?>
    </h3>
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li><i class="fa fa-home"></i><?php _e('Home', 'wphrm'); ?><i class="fa fa-angle-right"></i></li>
            <li><?php _e('Salary', 'wphrm'); ?><i class="fa fa-angle-right"></i></li>
            <li><strong><?php
                    if (isset($wphrm_info['wphrm_employee_fname'])) : echo esc_html($wphrm_info['wphrm_employee_fname']) . ' ' . esc_html($wphrm_info['wphrm_employee_lname']);
                    endif;
                    ?></strong></li>
        </ul>
    </div>
    <?php if (in_array('manageOptionsSalary', $wphrmGetPagePermissions)) { ?>
        <a class="btn green " href="?page=wphrm-salary" ><i class="fa fa-arrow-left"></i><?php _e('Back', 'wphrm'); ?></a>
    <?php } ?>
    <div class="alert alert-success display-hide" id="salary_info_success"><i class='fa fa-check-square' aria-hidden='true'></i>
        <button class="close" data-close="alert"></button>
    </div>
    <?php if (isset($_REQUEST['mailmsg']) && $_REQUEST['mailmsg'] == 'yes') { ?>
        <div class="alert alert-success"><i class='fa fa-check-square' aria-hidden='true'></i>
            <?php echo esc_html($wphrmMessagesAddEmployee); ?><button class="close" data-close="alert"></button>
        </div>
    <?php } ?>
    <div class="alert alert-danger display-hide" id="salary_info_error">
        <button class="close" data-close="alert"></button>
    </div>
    <div class="portlet box blue calendar">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-calendar"></i> &nbsp;<?php
                if (isset($wphrm_info['wphrm_employee_fname'])) : echo esc_html($wphrm_info['wphrm_employee_fname']) . ' ' . esc_html($wphrm_info['wphrm_employee_lname']);
                endif;
                ?>
            </div>
        </div>
        <div class="portlet-body">
            <div id="calendari_lateral"></div>            
            <input class="yeardata " type="hidden">
            <input class="wphrm_emp_id" type="hidden" value="<?php echo esc_attr($employee_id); ?>">
            <input type="hidden" class="image_url" value="<?php echo esc_attr(plugins_url('assets/images/', __FILE__)) ?>">
            <div class="row">
                <div class="col-md-4">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php _e('Name', 'wphrm'); ?></td>
                                <td><?php
                                    if (isset($wphrm_info['wphrm_employee_fname'])) : echo esc_html($wphrm_info['wphrm_employee_fname']);
                                    endif;
                                    ?></td>
                            </tr>
                            <tr>
                                <td><?php _e('Employee Id', 'wphrm'); ?></td>
                                <td><?php
                                    if (isset($wphrm_info['wphrm_employee_uniqueid'])) : echo esc_html($wphrm_info['wphrm_employee_uniqueid']);
                                    endif;
                                    ?></td>
                            </tr>
                            <tr>
                                <td><?php _e("Father's/Husband's Name", "wphrm"); ?></td>
                                <td><?php
                                    if (isset($wphrm_info['wphrm_employee_fathername'])) : echo esc_html($wphrm_info['wphrm_employee_fathername']);
                                    endif;
                                    ?></td>
                            </tr>
                            <tr>
                                <td><?php _e('Date of Joining', 'wphrm'); ?></td>
                                <td><?php
                                    if (isset($wphrm_info['wphrm_employee_joining_date'])) : echo esc_html($wphrm_info['wphrm_employee_joining_date']);
                                    endif;
                                    ?></td>
                            </tr>
                            <tr>
                                <td><?php _e('Department', 'wphrm'); ?></td>
                                <td><?php
                                    if (isset($wphrmDepartmentInfo['departmentName'])) : echo esc_html($wphrmDepartmentInfo['departmentName']);
                                    endif;
                                    ?></td>
                            </tr>
                            <tr>
                                <td><?php _e('Designation', 'wphrm'); ?></td>
                                <td><?php
                                    if (isset($wphrmDesignationInfo['designationName'])) : echo esc_html($wphrmDesignationInfo['designationName']);
                                    endif;
                                    ?></td>
                            </tr>
                            <tr>
                                <td><?php _e('Bank Account No', 'wphrm'); ?>.</td>
                                <td><?php
                                    if (isset($wphrm_bank_info['wphrm_employee_bank_account_no'])) : echo esc_html($wphrm_bank_info['wphrm_employee_bank_account_no']);
                                    endif;
                                    ?></td>
                            </tr>
                        </tbody>
                    </table>
                    
                </div>
                <div class="col-md-8">
                    <div class="ajax_calender_load">
                        <div class="portlet-body">
                            <div class="table-scrollable">
                                 <style> .btn-green {
    background: #45B6AF;
    color: #fff; padding: 1px 1px;} 
                .btn-blue {
    background: #0B92C8;
    color: #fff; padding: 1px 1px; }.fa.fa-pencil {
                                    margin-left: 3px;
                                    }
                                 .fa.fa-download {
                                    position: relative;
                                     left: -1px;
                                    }</style> 
                                <?php
                                $months = $this->WPHRMGetMonths();
                                $currentMonth = date('F');
                                $current_month = date('m');
                                $i = 1;
                                $wphrm_generated = array();
                                $wphrm_generatedreturn = array();
                                $currentYear = date('Y');
                                $currentDate = date('Y-m-') . '01';
                                $wphrm_generated_salary = $wpdb->get_results("SELECT * FROM $this->WphrmWeeklySalaryTable WHERE `employeeID` = $employee_id AND year(`date`)='$currentYear' AND `employeeKey` ='employeeSalaryGenerated'");

                                foreach ($wphrm_generated_salary as $G => $generated_salary) {
                                    $wphrm_generated[] = date('F', strtotime($generated_salary->date));
                                }
                                ?>
                                <table class="table table-striped table-bordered table-hover" >
                                    <thead>
                                        <tr>
                                            <th> # </th>
                                            <th><?php _e('Months', 'wphrm'); ?></th>
                                            <th><?php _e('Action', 'wphrm'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr id="row102"></tr>
                                        <?php
                                        foreach ($months as $k => $month) {
                                            if (($current_month >= $k) && ($wphrmEmployeeJoiningYear <= $currentYear || $wphrmEmployeeJoiningMonth <= $k)) {
                                                if (!empty($wphrm_generated)) {
                                                    $wphrm_generatedreturn = $this->wphrmCurrentMonth($wphrm_generated, $months);
                                                    ?>
                                                    <tr id="row102">
                                                        <td> <?php echo $i; ?> </td>
                                                        <td> <?php echo $month; ?></td>
                                                        <td>
                                                            <?php if (isset($wphrm_generatedreturn[$k]) == $month) { ?> 
                                                                 <table>
                                                                   <?php   if (in_array('manageOptionsSalary', $wphrmGetPagePermissions)) {
                                                                        $monthInWeek = $this->WPHRMWeeksInMonth($currentYear, $k, '6');
                                                                        $monthInSalaryGenerated = $this->WPHRMMonthInSalaryGenerated($employee_id, $k, $currentYear);
                                                                        $count = 1;
                                                                        for ($count; $count <= $monthInWeek;) {
                                                                            if (in_array($count, $monthInSalaryGenerated)) {
                                                                                ?>
                                                                                <td>
                                                                                    <table style="margin-right: 13px;"><tr>
                                                                                           
                                                                                            <td><button type="button" onclick="createWeekSalary('<?php echo $month; ?>','<?php echo $count; ?>')" href="#" class="btn btn-xs btn-green">
                                                                                                    &nbsp;<?php $currentCount = $this->WPHRMWeekCount($count); echo $currentCount; ?> <i class="fa fa-pencil"></i>
                                                                                                </button></td>

                                                                                                <td><button style="position: relative; left: -1px;" type="button" onclick="wphrm_pdf_week_generator('wphrm-salary-slip-week-pdf', <?php echo $employee_id; ?>, '<?php echo $month ?>', '<?php echo $count ?>')" href="#" class="btn btn-xs btn-green">
                                                                                                    <i class="fa fa-download"></i>
                                                                                                </button></td></tr>
                                                                                    </table>
                                                                                </td>   
                                                                            <?php } else { ?>
                                                                                <td><table style="margin-right: 13px;"><tr>
                                                                                        <?php if(!empty($wphrmGenerate)) { ?>
                                                                                            <td><button type="button" onclick="wphrmDuplicateWeek('<?php echo $month; ?>','<?php echo $count; ?>')" href="#duplicateModal" data-toggle="modal" class="btn btn-xs btn-blue">
                                                                                                    &nbsp;<?php $currentCount = $this->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-plus"></i>
                                                                                                </button></td>
                                                                                            <?php }else{ ?>
                                                                                              <td><button type="button" onclick="createWeekSalary('<?php echo $month; ?>','<?php echo $count; ?>')" href="#" class="btn btn-xs btn-blue">
                                                                                                    &nbsp;<?php $currentCount = $this->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-plus"></i>
                                                                                                </button></td>  
                                                                                            <?php } ?>
                                                                                        
                                                                                        </tr></table>   
                                                                                </td>
                                                                                <?php
                                                                            }
                                                                            $count++;
                                                                            } } 
                                                                    else {    
                                                                        $monthInWeek = $this->WPHRMWeeksInMonth($currentYear, $k, '6');
                                                                        $monthInSalaryGenerated = $this->WPHRMMonthInSalaryGenerated($employee_id, $k, $currentYear);
                                                                        $count = 1;
                                                                        for ($count; $count <= $monthInWeek;) {
                                                                            if (in_array($count, $monthInSalaryGenerated)) {
                                                                                ?>
                                                                                <td>
                                                                                    <table style="margin-right: 13px;"><tr>
                                                                                            <td><button type="button" onclick="wphrm_pdf_week_generator('wphrm-salary-slip-week-pdf', <?php echo $employee_id; ?>, '<?php echo $month ?>', '<?php echo $count ?>')" href="#" class="btn btn-xs btn-green">
                                                                                                    &nbsp;<?php $currentCount = $this->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-download"></i>
                                                                                                </button></td>
                                                                                            </tr>
                                                                                    </table>
                                                                                </td>   
                                                                            <?php } else { ?>
                                                                                <td><table style="margin-right: 13px;"><tr>
                                                                                            <td>
                                                                                                <button type="button" onclick="salary_Week_request(<?php echo esc_js($employee_id); ?>, '<?php echo esc_js($month) ?>','<?php echo esc_js($count) ?>')" href="#" class="btn btn-xs btn-blue">
                                                                                    &nbsp;<?php $currentCount = $this->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-file-text-o"></i>
                                                                                </button></td></tr></table>   
                                                                                </td>
                                                                                <?php
                                                                            }
                                                                            $count++;
                                                                        }
                                                                     } ?>

                                                                </table> 
                                                                <?php
                                                            } else {
                                                                if ($wphrmEmployeeJoiningYear == $currentYear && $wphrmEmployeeJoiningMonth <= $k) {
                                                                    if (in_array('manageOptionsSalary', $wphrmGetPagePermissions)) { ?>
                                                                  <table>
                                                                    <?php
                                                                        $monthInWeek = $this->WPHRMWeeksInMonth($currentYear, $k, '6');
                                                                        $monthInSalaryGenerated = $this->WPHRMMonthInSalaryGenerated($employee_id, $k, $currentYear);
                                                                        $count = 1;
                                                                        for ($count; $count <= $monthInWeek;) {
                                                                            if (in_array($count, $monthInSalaryGenerated)) {
                                                                                ?>
                                                                                <td>
                                                                                    <table style="margin-right: 13px;"><tr>
                                                                                           
                                                                                            <td><button type="button" onclick="createWeekSalary('<?php echo $month; ?>','<?php echo $count; ?>')" href="#" class="btn btn-xs">
                                                                                                    &nbsp;<?php $currentCount = $this->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-pencil"></i>
                                                                                                </button></td>

                                                                                            <td><button type="button" style="position: relative; left: -1px;" onclick="wphrm_pdf_week_generator('wphrm-salary-slip-week-pdf', <?php echo $employee_id; ?>, '<?php echo $month ?>', '<?php echo $count ?>')" href="#" class="btn btn-xs btn-green">
                                                                                                    <i class="fa fa-download"></i>
                                                                                                </button></td></tr>
                                                                                    </table>
                                                                                </td>   
                                                                            <?php } else { ?>
                                                                                <td><table style="margin-right: 13px;"><tr>
                                                                                            <?php if(!empty($wphrmGenerate)) { ?>
                                                                                            <td><button type="button"  onclick="wphrmDuplicateWeek('<?php echo $month; ?>','<?php echo $count; ?>')" href="#duplicateModal" data-toggle="modal" class="btn btn-xs btn-blue">
                                                                                                    &nbsp;<?php $currentCount = $this->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-plus"></i>
                                                                                                </button></td>
                                                                                            <?php }else{ ?>
                                                                                              <td><button type="button" onclick="createWeekSalary('<?php echo $month; ?>','<?php echo $count; ?>')" href="#" class="btn btn-xs btn-blue">
                                                                                                    &nbsp;<?php $currentCount = $this->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-plus"></i>
                                                                                                </button></td>  
                                                                                            <?php } ?></tr></table>   
                                                                                </td>
                                                                                <?php
                                                                            }
                                                                            $count++;
                                                                        }
                                                                   ?>

                                                                </table> 
                                                                        
                                                                        
                                                                  <?php  } else { ?>
                                                                        <table>
                                                                    <?php
                                                                        $monthInWeek = $this->WPHRMWeeksInMonth($currentYear, $k, '6');
                                                                        $monthInSalaryGenerated = $this->WPHRMMonthInSalaryGenerated($employee_id, $k, $currentYear);
                                                                        $count = 1;
                                                                        for ($count; $count <= $monthInWeek;) {
                                                                            if (in_array($count, $monthInSalaryGenerated)) {
                                                                                ?>
                                                                                <td>
                                                                                    <table style="margin-right: 13px;"><tr>
                                                                                             <td><button type="button" onclick="wphrm_pdf_week_generator('wphrm-salary-slip-week-pdf', <?php echo $employee_id; ?>, '<?php echo $month ?>', '<?php echo $count ?>')" href="#" class="btn btn-xs btn-green">
                                                                                                   &nbsp;<?php $currentCount = $this->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-download"></i>
                                                                                                </button></td></tr>
                                                                                    </table>
                                                                                </td>   
                                                                            <?php } else { ?>
                                                                                <td><table style="margin-right: 13px;"><tr>
                                                                                     <td>
                                                                                        <button type="button" onclick="salary_Week_request(<?php echo esc_js($employee_id); ?>, '<?php echo esc_js($month) ?>','<?php echo esc_js($count) ?>')" href="#" class="btn btn-xs btn-blue">
                                                                                    &nbsp;<?php $currentCount = $this->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-file-text-o"></i>
                                                                                </button></td></tr></table>   
                                                                                </td>
                                                                                <?php
                                                                            }
                                                                            $count++;
                                                                        }
                                                                   ?>
                                                                </table> 
                                                                   <?php }
                                                                } else {
                                                                    if ($wphrmEmployeeJoiningYear < $currentYear) {
                                                                        if (in_array('manageOptionsSalary', $wphrmGetPagePermissions)) {
                                                                            ?>
                                                                             <table>
                                                                    <?php
                                                                        $monthInWeek = $this->WPHRMWeeksInMonth($currentYear, $k, '6');
                                                                        $monthInSalaryGenerated = $this->WPHRMMonthInSalaryGenerated($employee_id, $k, $currentYear);
                                                                        $count = 1;
                                                                        for ($count; $count <= $monthInWeek;) {
                                                                            if (in_array($count, $monthInSalaryGenerated)) {
                                                                                ?>
                                                                                <td>
                                                                                    <table style="margin-right: 13px;"><tr>
                                                                                           
                                                                                            <td><button type="button" onclick="createWeekSalary('<?php echo $month; ?>','<?php echo $count; ?>')" href="#" class="btn btn-xs btn-green">
                                                                                                    &nbsp;<?php $currentCount = $this->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-pencil"></i>
                                                                                                </button></td>

                                                                                            <td><button type="button" style="position: relative; left: -1px;" onclick="wphrm_pdf_week_generator('wphrm-salary-slip-week-pdf', <?php echo $employee_id; ?>, '<?php echo $month ?>', '<?php echo $count ?>')" href="#" class="btn btn-xs btn-green">
                                                                                                    <i class="fa fa-download"></i>
                                                                                                </button></td></tr>
                                                                                    </table>
                                                                                </td>   
                                                                            <?php } else { ?>
                                                                                <td><table style="margin-right: 13px;"><tr>
                                                                                            <?php if(!empty($wphrmGenerate)) { ?>
                                                                                            <td><button type="button" onclick="wphrmDuplicateWeek('<?php echo $month; ?>','<?php echo $count; ?>')" href="#duplicateModal" data-toggle="modal" class="btn btn-xs btn-blue">
                                                                                                    &nbsp;<?php $currentCount = $this->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-plus"></i>
                                                                                                </button></td>
                                                                                            <?php }else{ ?>
                                                                                              <td><button type="button" onclick="createWeekSalary('<?php echo $month; ?>','<?php echo $count; ?>')" href="#" class="btn btn-xs btn-blue">
                                                                                                    &nbsp;<?php $currentCount = $this->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-plus"></i>
                                                                                                </button></td>  
                                                                                            <?php } ?></tr></table>   
                                                                                </td>
                                                                                <?php
                                                                            }
                                                                            $count++;
                                                                        }
                                                                   ?>

                                                                </table> 
                                                                            <?php
                                                                        } else { ?>
                                                                 <table>
                                                                    <?php
                                                                        $monthInWeek = $this->WPHRMWeeksInMonth($currentYear, $k, '6');
                                                                        $monthInSalaryGenerated = $this->WPHRMMonthInSalaryGenerated($employee_id, $k, $currentYear);
                                                                        $count = 1;
                                                                        for ($count; $count <= $monthInWeek;) {
                                                                            if (in_array($count, $monthInSalaryGenerated)) {
                                                                                ?>
                                                                                <td>
                                                                                    <table style="margin-right: 13px;"><tr>
                                                                                             <td><button type="button" onclick="wphrm_pdf_week_generator('wphrm-salary-slip-week-pdf', <?php echo $employee_id; ?>, '<?php echo $month ?>', '<?php echo $count ?>')" href="#" class="btn btn-xs btn-green">
                                                                                                    &nbsp;<?php $currentCount = $this->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-download"></i>
                                                                                                </button></td></tr>
                                                                                    </table>
                                                                                </td>   
                                                                            <?php } else { ?>
                                                                                <td><table style="margin-right: 13px;"><tr>
                                                                                     <td>
                                                                                        <button type="button" onclick="salary_Week_request(<?php echo esc_js($employee_id); ?>, '<?php echo esc_js($month) ?>','<?php echo esc_js($count) ?>')" href="#" class="btn btn-xs btn-blue">
                                                                                    &nbsp;<?php $currentCount = $this->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-file-text-o"></i>
                                                                                </button></td></tr></table>   
                                                                                </td>
                                                                                <?php
                                                                            }
                                                                            $count++;
                                                                        }
                                                                   ?>
                                                                </table> 
                                                                      <?php  }
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                <?php } else { ?>
                                                    <tr id="row102">
                                                        <td> <?php echo $i; ?> </td>
                                                        <td> <?php echo $month; ?></td>
                                                        <td>
                                                            <?php
                                                            if ($wphrmEmployeeJoiningYear == $currentYear && $wphrmEmployeeJoiningMonth <= $k) {
                                                                if (in_array('manageOptionsSalary', $wphrmGetPagePermissions)) {
                                                                   ?>
                                                             <table>
                                                                    <?php
                                                                        $monthInWeek = $this->WPHRMWeeksInMonth($currentYear, $k, '6');
                                                                        $monthInSalaryGenerated = $this->WPHRMMonthInSalaryGenerated($employee_id, $k, $currentYear);
                                                                        $count = 1;
                                                                        for ($count; $count <= $monthInWeek;) {
                                                                            if (in_array($count, $monthInSalaryGenerated)) {
                                                                                ?>
                                                                                <td>
                                                                                    <table style="margin-right: 13px;"><tr>
                                                                                           
                                                                                            <td><button type="button" onclick="createWeekSalary('<?php echo $month; ?>','<?php echo $count; ?>')" href="#" class="btn btn-xs btn-green">
                                                                                                    &nbsp;<?php $currentCount = $this->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-pencil"></i>
                                                                                                </button></td>

                                                                                            <td><button type="button" style="position: relative; left: -1px;" onclick="wphrm_pdf_week_generator('wphrm-salary-slip-week-pdf', <?php echo $employee_id; ?>, '<?php echo $month ?>', '<?php echo $count ?>')" href="#" class="btn btn-xs btn-green">
                                                                                                    <i class="fa fa-download"></i>
                                                                                                </button></td></tr>
                                                                                    </table>
                                                                                </td>   
                                                                            <?php } else { ?>
                                                                                <td><table style="margin-right: 13px;"><tr>
                                                                                            <?php if(!empty($wphrmGenerate)) { ?>
                                                                                            <td><button type="button" onclick="wphrmDuplicateWeek('<?php echo $month; ?>','<?php echo $count; ?>')" href="#duplicateModal" data-toggle="modal" class="btn btn-xs btn-blue">
                                                                                                   &nbsp;<?php $currentCount = $this->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-plus"></i>
                                                                                                </button></td>
                                                                                            <?php }else{ ?>
                                                                                              <td><button type="button" onclick="createWeekSalary('<?php echo $month; ?>','<?php echo $count; ?>')" href="#" class="btn btn-xs btn-blue">
                                                                                                    &nbsp;<?php $currentCount = $this->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-plus"></i>
                                                                                                </button></td>  
                                                                                            <?php } ?></tr></table>   
                                                                                </td>
                                                                                <?php
                                                                            }
                                                                            $count++;
                                                                        }
                                                                   ?>

                                                                </table> 
                                                                <?php } else { ?>
                                                                    <table>
                                                                    <?php
                                                                        $monthInWeek = $this->WPHRMWeeksInMonth($currentYear, $k, '6');
                                                                        $monthInSalaryGenerated = $this->WPHRMMonthInSalaryGenerated($employee_id, $k, $currentYear);
                                                                        $count = 1;
                                                                        for ($count; $count <= $monthInWeek;) {
                                                                            if (in_array($count, $monthInSalaryGenerated)) {
                                                                                ?>
                                                                                <td>
                                                                                    <table style="margin-right: 13px;"><tr>
                                                                                             <td><button type="button" onclick="wphrm_pdf_week_generator('wphrm-salary-slip-week-pdf', <?php echo $employee_id; ?>, '<?php echo $month ?>', '<?php echo $count ?>')" href="#" class="btn btn-xs btn-green">
                                                                                                    &nbsp;<?php $currentCount = $this->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-download"></i>
                                                                                                </button></td></tr>
                                                                                    </table>
                                                                                </td>   
                                                                            <?php } else { ?>
                                                                                <td><table style="margin-right: 13px;"><tr>
                                                                                     <td>
                                                                                        <button type="button" onclick="salary_Week_request(<?php echo esc_js($employee_id); ?>, '<?php echo esc_js($month) ?>','<?php echo esc_js($count) ?>')" href="#" class="btn btn-xs btn-blue">
                                                                                    &nbsp;<?php $currentCount = $this->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-file-text-o"></i>
                                                                                </button></td></tr></table>   
                                                                                </td>
                                                                                <?php
                                                                            }
                                                                            $count++;
                                                                        }
                                                                   ?>
                                                                </table> 
                                                               <?php  }
                                                            } else {
                                                                if ($wphrmEmployeeJoiningYear < $currentYear) {
                                                                    if (in_array('manageOptionsSalary', $wphrmGetPagePermissions)) {
                                                                        ?>
                                                                        <table>
                                                                    <?php
                                                                        $monthInWeek = $this->WPHRMWeeksInMonth($currentYear, $k, '6');
                                                                        $monthInSalaryGenerated = $this->WPHRMMonthInSalaryGenerated($employee_id, $k, $currentYear);
                                                                        $count = 1;
                                                                        for ($count; $count <= $monthInWeek;) {
                                                                            if (in_array($count, $monthInSalaryGenerated)) {
                                                                                ?>
                                                                                <td>
                                                                                    <table style="margin-right: 13px;"><tr>
                                                                                           
                                                                                            <td><button type="button" onclick="createWeekSalary('<?php echo $month; ?>','<?php echo $count; ?>')" href="#" class="btn btn-xs btn-green">
                                                                                                    &nbsp;<?php $currentCount = $this->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-pencil"></i>
                                                                                                </button></td>

                                                                                            <td><button type="button" style="position: relative; left: -1px;" onclick="wphrm_pdf_week_generator('wphrm-salary-slip-week-pdf', <?php echo $employee_id; ?>, '<?php echo $month ?>', '<?php echo $count ?>')" href="#" class="btn btn-xs btn-green">
                                                                                                    <i class="fa fa-download"></i>
                                                                                                </button></td></tr>
                                                                                    </table>
                                                                                </td>   
                                                                            <?php } else { ?>
                                                                                <td><table style="margin-right: 13px;"><tr>
                                                                                            <?php if(!empty($wphrmGenerate)) { ?>
                                                                                            <td><button type="button" onclick="wphrmDuplicateWeek('<?php echo $month; ?>','<?php echo $count; ?>')" href="#duplicateModal" data-toggle="modal" class="btn btn-xs btn-blue">
                                                                                                    &nbsp;<?php $currentCount = $this->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-plus"></i>
                                                                                                </button></td>
                                                                                            <?php }else{ ?>
                                                                                              <td><button type="button" onclick="createWeekSalary('<?php echo $month; ?>','<?php echo $count; ?>')" href="#" class="btn btn-xs btn-blue">
                                                                                                    &nbsp;<?php $currentCount = $this->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-plus"></i>
                                                                                                </button></td>  
                                                                                            <?php } ?></tr></table>   
                                                                                </td>
                                                                                <?php
                                                                            }
                                                                            $count++;
                                                                        }
                                                                   ?>

                                                                </table>
                                                                        <?php
                                                                    } else { ?>
                                                                         <table>
                                                                    <?php
                                                                        $monthInWeek = $this->WPHRMWeeksInMonth($currentYear, $k, '6');
                                                                        $monthInSalaryGenerated = $this->WPHRMMonthInSalaryGenerated($employee_id, $k, $currentYear);
                                                                        $count = 1;
                                                                        for ($count; $count <= $monthInWeek;) {
                                                                            if (in_array($count, $monthInSalaryGenerated)) {
                                                                                ?>
                                                                                <td>
                                                                                    <table style="margin-right: 13px;"><tr>
                                                                                             <td><button type="button" onclick="wphrm_pdf_week_generator('wphrm-salary-slip-week-pdf', <?php echo $employee_id; ?>, '<?php echo $month ?>', '<?php echo $count ?>')" href="#" class="btn btn-xs btn-green">
                                                                                                   &nbsp;<?php $currentCount = $this->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-download"></i>
                                                                                                </button></td></tr>
                                                                                    </table>
                                                                                </td>   
                                                                            <?php } else { ?>
                                                                                <td><table style="margin-right: 13px;"><tr>
                                                                                     <td>
                                                                                        <button type="button" onclick="salary_Week_request(<?php echo esc_js($employee_id); ?>, '<?php echo esc_js($month) ?>','<?php echo esc_js($count) ?>')" href="#" class="btn btn-xs btn-blue">
                                                                                    &nbsp;<?php $currentCount = $this->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-file-text-o"></i>
                                                                                </button></td></tr></table>   
                                                                                </td>
                                                                                <?php
                                                                            }
                                                                            $count++;
                                                                        }
                                                                   ?>
                                                                </table>    
                                                                  <?php  }
                                                                }
                                                            }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                <tr id="row102">
                                                    <td><?php echo $i; ?></td>
                                                    <td><?php echo $month; ?></td>
                                                    <td></td>
                                                </tr>
                                                <?php
                                            } $i++;
                                        }
                                        ?>
                                    </tbody>
                                </table>                                
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <form name="wphrm-create-week-salary" id="wphrm-create-week-salary" method="GET" action="" >
                        <input type="hidden" name="page"  value="wphrm-salary-slip-details-week" />
                        <input type="hidden" name="wphrm-employee-id"  value="<?php echo esc_attr($employee_id); ?>" />
                        <input type="hidden" name="wphrm-create-week-salary-month" value="" />
                        <input type="hidden" name="wphrm-create-week-salary-week-no" value="" />
                        <input type="hidden" name="wphrm-create-week-salary-year" class="yeardata" value="" />
                    </form>
                </div>
            </div>
        </div>
    </div>    
    <!-- END PAGE CONTENT-->
    <div id="deleteModal" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
                    <h4 class="modal-title"><?php _e('Confirmation', 'wphrm'); ?></h4>
                </div>
                <div class="modal-body" id="info">
                    <p>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn default"><?php _e('Cancel', 'wphrm'); ?></button>
                    <button type="button" data-dismiss="modal" class="btn red" id="delete"><i class="fa fa-trash"></i><?php _e('Delete', 'wphrm'); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="salary-excel-Modal" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="alert alert-success display-hide" id="WPHRMCustomDelete_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrmMessagesUpdateDeparment); ?>
                <button class="close" data-close="alert"></button>
            </div>
            <div class="alert alert-danger display-hide" id="WPHRMCustomDelete_error">
                <button class="close" data-close="alert"></button>
            </div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
                <h4 class="modal-title"><?php _e('Financials', 'wphrm'); ?></h4>
            </div>
            <div class="modal-body" id="info"><?php _e('Please select date range.', 'wphrm'); ?></div>
            <div class="modal-footer">

                <button type="button" data-dismiss="modal" aria-hidden="true" class="btn default"><i class="fa fa-times"></i><?php _e('Cancel', 'wphrm'); ?></button>
            </div>
        </div>
    </div>
</div>