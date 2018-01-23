<?php

if (!defined('ABSPATH'))
    exit;
global $current_user, $wpdb;
$wphrm_messages_Salary_Details_update = $this->WPHRMGetMessage(7);
if (isset($_REQUEST['wphrm-employee-id']) && !empty($_REQUEST['wphrm-employee-id']) && isset($_REQUEST['wphrm-create-week-salary-year']) && !empty($_REQUEST['wphrm-create-week-salary-year']) && isset($_REQUEST['wphrm-create-week-salary-month']) && !empty($_REQUEST['wphrm-create-week-salary-month'])) :
    $employee_id = esc_sql($_REQUEST['wphrm-employee-id']); // esc
    $generate_month = esc_sql($_REQUEST['wphrm-create-week-salary-month']); // esc
   
    $generate_year = esc_sql($_REQUEST['wphrm-create-week-salary-year']); // esc
    $pagetitle = __('Employee Other Details', 'wphrm');
    $wphrm_info = $this->WPHRMGetUserDatas($employee_id, 'wphrmEmployeeInfo');
    $wphrm_bank_info = $this->WPHRMGetUserDatas($employee_id, 'wphrmEmployeeBankInfo');
    $wphrmEmployeeSalaryInfo = $this->WPHRMGetUserDatas($employee_id, 'wphrmEmployeeSalaryInfo');
    if (isset($wphrmEmployeeSalaryInfo['current-salary'])) {
        $sa = $wphrmEmployeeSalaryInfo['current-salary'];
    }else{
        $sa=0;
    }
    $month = esc_sql(date('m', strtotime($generate_month))); // esc
    $from = esc_sql($generate_year . '-' . $month . '-' . '01'); // esc
    $to = esc_sql($generate_year . '-' . $month . '-' . '31'); // esc
    $employeeSalaryNote = esc_sql('employeeSalaryNote'); // esc
    $wphrmSalaryAccording = $this->WPHRMGetSettings('wphrsalaryDayOrHourlyInfo');
    if (isset($wphrmSalaryAccording['wphrm-according']) && $wphrmSalaryAccording['wphrm-according'] == '') {
        $wphrmSalaryAccording['wphrm-according'] = 'Day';
    }
    $days = cal_days_in_month(CAL_GREGORIAN, $month, $generate_year);

    $duplicateMonth = '';
    $duplicateFrom = '';
    $wphrm_generated_salary = $wpdb->get_results("SELECT * FROM $this->WphrmWeeklySalaryTable WHERE `employeeID` = $employee_id AND date(`date`)='$from' AND `weekOn`= '".$_REQUEST['wphrm-create-week-salary-week-no']."'");
    $wphrm_text_infos = $wpdb->get_row("SELECT * FROM $this->WphrmWeeklySalaryTable WHERE `employeeID` = $employee_id AND `employeeKey` = '$employeeSalaryNote' AND `date` ='$from' AND `weekOn`= '".$_REQUEST['wphrm-create-week-salary-week-no']."'");
    if ((isset($_REQUEST['wphrm-duplicate-to-salary-week-year']) && ($_REQUEST['wphrm-duplicate-to-salary-week-year'] != '')) && (isset($_REQUEST['wphrm-duplicate-to-salary-week-month']) && ($_REQUEST['wphrm-duplicate-to-salary-week-month'] != '') ) && (isset($_REQUEST['wphrm-duplicate-to-salary-week-no']) && ($_REQUEST['wphrm-duplicate-to-salary-week-no'] != '') )) {
        $duplicateMonth = esc_sql(date('m', strtotime($_REQUEST['wphrm-duplicate-to-salary-week-month'])));
        $duplicateFrom = esc_sql($_REQUEST['wphrm-duplicate-to-salary-week-year'] . '-' . $duplicateMonth . '-' . '01'); // esc
        $wphrm_generated_salary = $wpdb->get_results("SELECT * FROM $this->WphrmWeeklySalaryTable WHERE `employeeID` = $employee_id AND date(`date`)='$duplicateFrom' AND `weekOn`= '".$_REQUEST['wphrm-duplicate-to-salary-week-no']."'");
        $wphrm_text_infos = $wpdb->get_row("SELECT * FROM $this->WphrmWeeklySalaryTable WHERE `employeeID` = $employee_id AND `employeeKey` = '$employeeSalaryNote' AND `date` ='$duplicateFrom' AND `weekOn`= '".$_REQUEST['wphrm-duplicate-to-salary-week-no']."'");
    }
    $wphrm_text_info = '';
    if (!empty($wphrm_text_infos)) {
        $wphrm_text_info = unserialize(base64_decode($wphrm_text_infos->employeeValue));
    }
    $wphrm_currencyinfo = $this->WPHRMGetSettings('wphrmGeneralSettingsInfo');

    if (!empty($wphrm_currencyinfo)) {
        $wphrmCurrencyinfos = explode("-", $wphrm_currencyinfo['wphrm_currency']);
        $wphrmCurrencyinfo = $wphrmCurrencyinfos[0];
    }
    $decimals = $this->WPHRMGetDecimalSettings();
    if (isset($decimals['decimal'])): 
        $decimal = $decimals['decimal'];
        else :
        $decimal = 2;
    endif;
     if (isset($decimals['decimalvalue'])): 
        $decimalvalue = $decimals['decimalvalue'];
        else :
        $decimalvalue = 2;
    endif;
    $getDate=$from;
    $attndance = $this->WPHRMEmployeeWiseAttendanceCount($days, $from, $to, $employee_id,$getDate);
    $workingday = $attndance['workingday'];
    $totalofWorkingDays = $attndance['totalofWorkingDays'];
    $presents = $attndance['presents'];
    $worked = $attndance['worked'];
    $totalWorked = $attndance['totalWorked'];
endif;
?>
<input type="hidden"  id="decimalpointvalue" value="<?php echo $decimal ?>"  >
<input type="hidden" name="wphrmdecimalInfo"  class="wphrmdecimalInfo" id="wphrmdecimalInfo" value="<?php echo esc_attr($decimalvalue);
?>" />
<div class="preloader">
    <span class="preloader-custom-gif"></span>
</div>
<div style="padding-left: 0px; padding-right:20px; padding-top:0px;" class="col-md-12">
    <input type="hidden" id="image_url" value="<?php echo esc_attr(plugins_url('assets/images/Remove.png', __FILE__)) ?>">
    <!-- BEGIN PAGE HEADER-->
    <h3 class="page-title"><?php _e('Salary', 'wphrm'); ?></h3>
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li><i class="fa fa-home"></i><?php _e('Home', 'wphrm'); ?><i class="fa fa-angle-right"></i></li>
            <li></i><?php _e('Salary', 'wphrm'); ?></li>
            <li><?php echo esc_html($generate_month . ' ' . $generate_year, 'wphrm'); ?></li>
        </ul>
    </div>
    <div class="portlet box blue calendar">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-calendar"></i> &nbsp;<?php
                if (isset($wphrm_info['wphrm_employee_fname'])): echo esc_html($wphrm_info['wphrm_employee_fname']) . ' ' . esc_html($wphrm_info['wphrm_employee_lname']);
                endif;
                ?>
            </div>
        </div>
        <div class="slip-wrap">
            <!-- slip Delete Modal -->
            <div id="myModaldelete" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog">
                    <div class="modal-content"> 
                        <div class="alert alert-success display-hide" id="WPHRMCustomDelete_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrm_messages_leave_type_delete->messagesDesc); ?>
                            <button class="close" data-close="alert"></button>
                        </div>
                        <div class="alert alert-danger display-hide" id="WPHRMCustomDelete_success">
                            <button class="close" data-close="alert"></button>
                        </div>
                        <form id="frm_salary_week_delete" class="search-form" method="post">
                            <input type="hidden" name="employeedelete_id" id="employeedelete_id" value="<?php
                            if (isset($employee_id)): echo esc_attr($employee_id);
                            endif;
                            ?>" />
                            <input type="hidden" name="generate_month_delete" id="generate_month_delete" value="<?php
                            if (isset($generate_month)): echo esc_attr($generate_month);
                            endif;
                            ?>" />
                            <input type="hidden" name="generate_year_delete" id="generate_year_delete" value="<?php
                            if (isset($generate_year)): echo esc_attr($generate_year);
                            endif;
                            ?>" />
                            <input type="hidden" name="wphrm-create-week-salary-week-no" id="wphrm-create-week-salary-week-no" value="<?php
                            if (isset($_REQUEST['wphrm-create-week-salary-week-no'])): echo esc_attr($_REQUEST['wphrm-create-week-salary-week-no']);
                            endif;
                            ?>" />
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
                                <h4 class="modal-title"><?php _e('Confirmation', 'wphrm'); ?></h4>
                            </div>
                            <div class="modal-body" id="info"><p><?php _e('Are you sure you want to delete this salary slip', 'wphrm'); ?>?</p></div>
                            <div class="modal-footer">
                                <button type="submit" class="btn red" ><i class="fa fa-trash"></i><?php _e('Delete', 'wphrm'); ?></button>
                                <button type="button" data-dismiss="modal" class="btn default"><?php _e('Cancel', 'wphrm'); ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-12 title-padding">
                <div class="row back-row">
                    <div style="padding-left: 0px;" class="col-md-4">
                        <a href="?page=wphrm-select-financials-week&employee_id=<?php echo esc_attr($employee_id); ?>" class="btn blue"><i class="fa fa-arrow-left"></i><?php _e('Back', 'wphrm'); ?></a>
                        <a data-toggle="modal" data-target="#myModaldelete" class="btn red"><i class="fa fa-trash"></i><?php _e('Delete', 'wphrm'); ?></a></div>
                    <div class="col-md-4 salary-slip">
                        <h1><?php 
                        $currentCount = $this->WPHRMWeekCount($_REQUEST['wphrm-create-week-salary-week-no']);
                        _e('Create Salary Slip '.$currentCount.' of ' . $generate_month . ' ' . $generate_year, 'wphrm'); ?></h1>
                    </div>
                    <div class="col-md-4"></div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="slip-user-info">
                            <h2><?php
                                if (isset($wphrm_info['wphrm_employee_fname'])) : echo esc_html($wphrm_info['wphrm_employee_fname']);
                                endif;
                                if (isset($wphrm_info['wphrm_employee_lname'])) : echo ' ' . esc_html($wphrm_info['wphrm_employee_lname']);
                                endif;
                                ?>
                            </h2>
                            <h2><?php _e('Employee ID', 'wphrm'); ?> :
                                <span style="font-weight: 400;"><?php
                                    if (isset($wphrm_info['wphrm_employee_uniqueid'])) : echo ' ' . esc_html($wphrm_info['wphrm_employee_uniqueid']);
                                    endif;
                                    ?> </span>
                            </h2>
                        </div>
                        <div class="slip-user-table">
                            <div class="alert alert-success display-hide" id="wphrmGenerateSalary_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrm_messages_Salary_Details_update); ?>
                                <button class="close" data-close="alert"></button>
                            </div>
                            <div class="alert alert-danger display-hide" id="wphrmGenerateSalary_error"> 
                                <button class="close" data-close="alert"></button>
                            </div>
                            <div class="row"> 
                                <form id="frm_salary_week_generate" name="myform" class="search-form" method="post">
                                    <div class="col-md-12 col-sm-6 col-xs-12 deductions-table">
                                        <table class="table">
                                            <thead>
                                            <th style="width: 100%; text-align: center;background: #E7EDF2;">
                                                <h2><?php _e('Work Information', 'wphrm'); ?></h2>
                                            </th>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                    <div class="">
                                       
                                            <div style="text-align: center;" class="col-md-3">
                                                <?php _e('Total  Working Days', 'wphrm'); ?>
                                                <input form="myform" style="text-align: center;" class="earningDeduction " type="text" name="dayofworking" id="dayofworking"  value="<?php
                                                if (isset($wphrm_text_info['wphrm_dayofworking'])) : echo esc_attr($wphrm_text_info['wphrm_dayofworking']);
                                                endif;
                                                ?>">
                                            </div>
                                            <div style="text-align: center;" class="col-md-3">
                                                <?php _e('Total Leaves', 'wphrm'); ?>
                                                <input   form="myform" style="text-align: center;" class="earningDeduction " type="text" name="workleave" id="workleave" value="<?php
                                                if (isset($wphrm_text_info['wphrm_workleave'])) : echo esc_attr($wphrm_text_info['wphrm_workleave']);
                                                endif;
                                                ?>">
                                            </div>
                                            <div style="text-align: center;" class="col-md-3">
                                                <?php _e('Total Days Worked', 'wphrm'); ?>
                                                <input style="width: 100% !important; text-align: center;"  form="myform" class="earningDeduction " type="text"  name="dayofworked" id="dayofworked"  value="<?php
                                                if (isset($wphrm_text_info['wphrm_dayofworked'])) : echo esc_attr($wphrm_text_info['wphrm_dayofworked']);
                                                endif;
                                                ?>">
                                            </div>
                                        

                                        <div style="text-align: center;" class="col-md-3">
                                            <?php _e('Account Number', 'wphrm'); ?>
                                            <?php if (isset($wphrm_text_info['wphrm_account_no']) && $wphrm_text_info['wphrm_account_no'] != '') { ?>
                                                <input form="myform" class="earningDeduction" type="text"  name="wphrm_account_no" id="wphrm_account_no"  value="<?php
                                                if (isset($wphrm_text_info['wphrm_account_no'])) : echo esc_attr($wphrm_text_info['wphrm_account_no']);
                                                endif;
                                                ?>">
                                                   <?php }else { ?>
                                                <input form="myform" class="earningDeduction" type="text"  name="wphrm_account_no" id="wphrm_account_no"  value="<?php
                                                if (isset($wphrm_bank_info['wphrm_employee_bank_account_no'])) : echo esc_attr($wphrm_bank_info['wphrm_employee_bank_account_no']);
                                                endif;
                                                ?>">
                                                   <?php } ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12 col-xs-12 earnings-table">
                                        <table class="table">
                                            <thead>
                                                <tr style="background:#E7EDF2;">
                                                    <th>
                                                        <h2><?php _e('Earnings', 'wphrm'); ?></h2>
                                                    </th>
                                                    <th style="padding-right: 0px;">
                                                        <h2 style="text-align:left; margin-right:15px;"><?php _e('Amount', 'wphrm'); ?></h2>
                                                    </th>
                                                    <th></th>
                                                </tr>
                                                <tr>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $wphrmEarningtotal = '';
                                                $i = 0;
                                                $j = 1;
                                                $wphrmEarningfiledskeyinformation = esc_sql('wphrmEarningfiledskey'); // esc
                                                $wphrmEarningfiledskey = $wpdb->get_row("SELECT * FROM $this->WphrmWeeklySalaryTable where `employeeID`=$employee_id AND `employeeKey`='$wphrmEarningfiledskeyinformation' AND `date`='$from' AND `weekOn`= '".$_REQUEST['wphrm-create-week-salary-week-no']."'");
                                                if ($duplicateMonth != '' && $duplicateFrom != '') {
                                                    $wphrmEarningfiledskey = $wpdb->get_row("SELECT * FROM $this->WphrmWeeklySalaryTable where `employeeID`=$employee_id AND `employeeKey`='$wphrmEarningfiledskeyinformation' AND `date`='$duplicateFrom' AND `weekOn`= '".$_REQUEST['wphrm-duplicate-to-salary-week-no']."'");
                                                }
                                                if (!empty($wphrmEarningfiledskey)) {
                                                    $wphrmEarningInfo = unserialize(base64_decode($wphrmEarningfiledskey->employeeValue));
                                                    if (isset($wphrmEarningInfo['wphrmEarningLebal']) && isset($wphrmEarningInfo['wphrmEarningValue'])) {
                                                        foreach ($wphrmEarningInfo['wphrmEarningLebal'] as $earningkey1 => $wphrmEarninglebal) {
                                                            foreach ($wphrmEarningInfo['wphrmEarningValue'] as $earningkey => $wphrmEarningvalue) {
                                                                if ($earningkey1 == $earningkey) {
                                                                    $wphrmEarningtotal = $wphrmEarningvalue + $i;
                                                                    $i = $wphrmEarningtotal;
                                                                    ?> 
                                                                    <tr class="<?php echo 'removefiled' . esc_attr($j) . 'earninglabel'; ?>">
                                                                        <td>
                                                                            <input type="text"  class="earningDeductionlebal"  name="wphrm-earning-lebal[]" value="<?php
                                                                            if (isset($wphrmEarninglebal)): echo esc_attr($wphrmEarninglebal);
                                                                            endif;
                                                                            ?>">
                                                                        </td>
                                                                        <td  style="padding-right: 0px; float:right;">
                                                                            <input type="text" class="earningDeduction earningcal validationonnumber two-digits" onkeyup="calculateEarningSum()"  name="wphrm-earning-value[]" value="<?php
                                                                            if (isset($wphrmEarningvalue)): echo esc_attr(round($wphrmEarningvalue, $decimalvalue));
                                                                            endif;
                                                                            ?>">
                                                                        </td>
                                                                        <td style="text-align: center;">
                                                                            <a onclick="deleteEarningAndDedutions('<?php echo esc_js($j) . 'earninglabel'; ?>');" data-loading-text="Updating..."  class="btn red"> <i class='fa fa-trash' aria-hidden='true'></i></a>
                                                                        </td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                                $j++;
                                                            }
                                                        }

                                                        $wphrmEarningsettingInfo = $this->WPHRMGetSettings('wphrmEarningInfo');
                                                        if (!empty($wphrmEarningsettingInfo)) {
                                                            foreach ($wphrmEarningsettingInfo['earningLebal'] as $wphrmEarningSettings) {
                                                                if (!in_array($wphrmEarningSettings, $wphrmEarningInfo['wphrmEarningLebal'])) {
                                                                    ?>
                                                                    <tr class="<?php echo 'removefiled' . esc_attr($j) . 'earningmatchlabel'; ?>">
                                                                        <td>
                                                                            <input type="text"  class="earningDeductionlebal" name="wphrm-earning-lebal[]" value="<?php
                                                                            if (isset($wphrmEarningSettings)): echo esc_attr($wphrmEarningSettings);
                                                                            endif;
                                                                            ?>">
                                                                        </td>
                                                                        <td  style="padding-right: 0px; float:right;">
                                                                            <input type="text" class="earningDeduction earningcal validationonnumber two-digits" value="" onkeyup="calculateEarningSum()" name="wphrm-earning-value[]" >
                                                                        </td>
                                                                        <td style="text-align: center;">
                                                                            <a   onclick="deleteEarningAndDedutions('<?php echo esc_js($j) . 'earningmatchlabel'; ?>');" data-loading-text="Updating..."  class="btn red">
                                                                                <i class='fa fa-trash' aria-hidden='true'></i></a>
                                                                        </td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                                $j++;
                                                            }
                                                        }
                                                    }
                                                } else {

                                                    $wphrmEarningInfo = $this->WPHRMGetSettings('wphrmEarningInfo');
                                                    if ((isset($wphrmEarningInfo['earningLebal']) && $wphrmEarningInfo['earningLebal']) && (isset($wphrmEarningInfo['earningtype']) && $wphrmEarningInfo['earningtype']) && (isset($wphrmEarningInfo['earningamount']) && $wphrmEarningInfo['earningamount']) ) {
                                                        $totalofEarningAmountPass = '';
                                                        foreach ($wphrmEarningInfo['earningLebal'] as $earningLebal => $wphrmEarningsettingInfo) {
                                                            foreach ($wphrmEarningInfo['earningtype'] as $earningtype => $wphrmEarningtype) {
                                                                foreach ($wphrmEarningInfo['earningamount'] as $earningamount => $earningamounts) {
                                                                    if ($earningLebal == $earningtype && $earningLebal == $earningamount && $earningtype == $earningamount) {
                                                                        $stotal = 0;
                                                                        $main = 0;
                                                                        $sapersant = 0;
                                                                        ?>
                                                                        <tr class="<?php echo 'removefiled' . esc_attr($j) . 'earningdefaultlabel'; ?>">
                                                                            <td>
                                                                                <input type="text"  class="earningDeductionlebal"  name="wphrm-earning-lebal[]" value="<?php
                                                                                if (isset($wphrmEarningsettingInfo)): echo esc_attr($wphrmEarningsettingInfo);
                                                                                endif;
                                                                                ?>">
                                                                            </td>

                                                                            <td  style="padding-right: 0px; float:right;">
                                                                                
                                                                                <input type="text" class="earningDeduction earningcal validationonnumber two-digits" onkeyup="calculateEarningSum()" name="wphrm-earning-value[]" value="">    
                                                                            </td>
                                                                            <td style="text-align: center;">
                                                                                <a   onclick="deleteEarningAndDedutions('<?php echo esc_js($j) . 'earningdefaultlabel'; ?>');" data-loading-text="Updating..."  class="btn red">
                                                                                    <i class='fa fa-trash' aria-hidden='true'></i></a>
                                                                            </td>
                                                                        </tr>
                                                                        <?php
                                                                        $j++;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    } 
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <input type="text" name="wphrm-earning-lebal[]" class="earningDeductionlebal" value="<?php _e('Miscellaneous', 'wphrm'); ?>">
                                                        </td>

                                                        <td  style="padding-right: 0px; float:right;">
                                                            <input type="text" class="earningDeduction earningcal validationonnumber two-digits" onkeyup="calculateEarningSum()" name="wphrm-earning-value[]" value="">    
                                                        </td>
                                                        <td>
                                                        </td>
                                                    </tr>
                                                <?php }
                                                ?>
                                                <tr id="earninginsertBefore"></tr>
                                                <tr>
                                                    <td colspan="3">
                                                        <div class="btn blue" style="margin: 5px 7px 6px 0px;" id="add-more-earning"><i class="fa fa-plus"></i><?php _e('Add More Earnings', 'wphrm'); ?>

                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>

                                    </div>
                                    <div class="col-md-6 col-sm-12 col-xs-12 deductions-table">
                                        <table class="table">
                                            <thead>
                                                <tr style="background:#E7EDF2;">
                                                    <th>
                                                        <h2><?php _e('Deductions', 'wphrm'); ?></h2>
                                                    </th>
                                                    <th style="padding-right: 0px;">
                                                        <h2 style="text-align:left;"><?php _e('Amount', 'wphrm'); ?></h2>
                                                    </th>
                                                    <th></th>
                                                </tr>
                                                <tr>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr><td></td></tr>
                                                <?php
                                                $wphrmDeductiontotal = '';
                                                $i = 0;
                                                $j = 1;

                                                $wphrmDeductionfiledskeyInformation = esc_sql('wphrmDeductionfiledskey'); // esc
                                                $wphrmDeductionfiledskey = $wpdb->get_row("SELECT * FROM $this->WphrmWeeklySalaryTable where `employeeID` = $employee_id AND `employeeKey` = '$wphrmDeductionfiledskeyInformation' AND `date`='$from' AND `weekOn`= '".$_REQUEST['wphrm-create-week-salary-week-no']."'");
                                                if ($duplicateMonth != '' && $duplicateFrom != '') {
                                                    $wphrmDeductionfiledskey = $wpdb->get_row("SELECT * FROM $this->WphrmWeeklySalaryTable where `employeeID` = $employee_id AND `employeeKey` = '$wphrmDeductionfiledskeyInformation' AND `date`='$duplicateFrom' AND `weekOn`= '".$_REQUEST['wphrm-create-week-salary-week-no']."'");
                                                }

                                                if (!empty($wphrmDeductionfiledskey)) {
                                                    $wphrmDeductionInfo = unserialize(base64_decode($wphrmDeductionfiledskey->employeeValue));
                                                    if (isset($wphrmDeductionInfo['wphrmDeductionLebal']) && isset($wphrmDeductionInfo['wphrmDeductionValue'])) {
                                                        foreach ($wphrmDeductionInfo['wphrmDeductionLebal'] as $earningkey1 => $wphrmDeductionlebal) {
                                                            foreach ($wphrmDeductionInfo['wphrmDeductionValue'] as $earningkey => $wphrmDeductionvalue) {
                                                                if ($earningkey1 == $earningkey) {
                                                                    $wphrmDeductiontotal = $wphrmDeductionvalue + $i;
                                                                    $i = $wphrmDeductiontotal;
                                                                    ?>
                                                                    <tr class="<?php echo 'removefiled' . esc_attr($j) . 'deductionlabel'; ?>"> <td>
                                                                            <input type="text"  class="earningDeductionlebal"  name="wphrm-deduction-lebal[]" value="<?php
                                                                            if (isset($wphrmDeductionlebal)): echo esc_attr($wphrmDeductionlebal);
                                                                            endif;
                                                                            ?>">
                                                                        </td>
                                                                        <td  style="padding-right: 0px; float:right;">
                                                                            <input type="text" class="earningDeduction deductioncal validationonnumber two-digits"  onkeyup="calculateDeductionSum()"  name="wphrm-deduction-value[]" value="<?php
                                                                            if (isset($wphrmDeductionvalue)): echo esc_attr(round($wphrmDeductionvalue, $decimalvalue));
                                                                            endif;
                                                                            ?>">
                                                                        </td>
                                                                        <td style="text-align: center;">
                                                                            <a   onclick="deleteEarningAndDedutions('<?php echo esc_js($j) . 'deductionlabel'; ?>');" data-loading-text="Updating..."  class="btn red">
                                                                                <i class='fa fa-trash' aria-hidden='true'></i></a>
                                                                        </td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                            }
                                                            $j++;
                                                        }
                                                    }
                                                } else {
                                                    $wphrmDeductionInfo = $this->WPHRMGetSettings('wphrmDeductionInfo');
                                                    if ((isset($wphrmDeductionInfo['deductionlebal']) && $wphrmDeductionInfo['deductionlebal']) && (isset($wphrmDeductionInfo['deductiontype']) && $wphrmDeductionInfo['deductiontype']) && (isset($wphrmDeductionInfo['deductionamount']) && $wphrmDeductionInfo['deductionamount']) ) {
                                                        foreach ($wphrmDeductionInfo['deductionlebal'] as $deductionLebal => $wphrmDedutionsettingInfo) {
                                                            foreach ($wphrmDeductionInfo['deductiontype'] as $deductiontype => $wphrmDeductiontype) {
                                                                foreach ($wphrmDeductionInfo['deductionamount'] as $deductionamount => $wphrmDeductionamountInfo) {
                                                                    if ($deductionLebal == $deductiontype && $deductionLebal == $deductionamount && $deductiontype == $deductionamount) {

                                                                        // print_r($wphrmDeductionInfo); exit();
                                                                        $stotal = 0;
                                                                        $main = 0;
                                                                        $sapersant = 0;
                                                                        ?>
                                                                        <tr class=" <?php echo 'removefiled' . esc_attr($j) . 'deductiondefaultlabel'; ?>">
                                                                            <td>
                                                                                <input type="text"  class="earningDeductionlebal"  name="wphrm-deduction-lebal[]" value=" <?php
                                                                                if (isset($wphrmDedutionsettingInfo)): echo esc_attr($wphrmDedutionsettingInfo);
                                                                                endif;
                                                                                ?>">
                                                                            </td>
                                                                            <td  style="padding-right: 0px; float:right;">
                                                                               
                                                                                <input type="text" class="earningDeduction deductioncal validationonnumber two-digits" value="" onkeyup="calculateDeductionSum()"  name="wphrm-deduction-value[]" >
                                                                            </td>
                                                                            <td style="text-align: center;">
                                                                                <a   onclick="deleteEarningAndDedutions('<?php echo esc_js($j) . 'deductiondefaultlabel'; ?>');" data-loading-text="Updating..."  class="btn red">
                                                                                    <i class='fa fa-trash' aria-hidden='true'></i></a>
                                                                            </td>
                                                                        </tr>
                                                                        <?php
                                                                        $j++;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                ?>
                                                <tr id="deductioninsertBefore"></tr>
                                                <?php $net_amount = ($wphrmEarningtotal) - ($wphrmDeductiontotal); ?>
                                                <tr>
                                                    <td colspan="3">

                                                        <div class="btn blue" style="margin: 5px 7px 6px 0px;" id="add-more-Deduction"><i class="fa fa-plus"></i><?php _e('Add More Deductions', 'wphrm'); ?>
                                                        </div>
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>

                                    </div>
                                    <table class="table">
                                        <thead>
                                            <tr>  <th style="border-top: 2px solid #cfd8dc;padding: 9px 0 12px 17px; background: #E7EDF2;" >
                                                    <h2><?php _e('Total Earnings : ', 'wphrm'); ?><input type="hidden" id="inputearningcalsum"><font id="callEarningTotal"><?php
                                                        if (isset($wphrmCurrencyinfo)): echo esc_attr($wphrmCurrencyinfo) . '<span id="earningcalsum"> ' . esc_attr($wphrmEarningtotal) . '</span>';
                                                        endif;
                                                        ?></font></h2>

                                                </th>

                                                <th style="border-top: 2px solid #cfd8dc;padding: 9px 0 12px 17px; background: #E7EDF2;" >
                                                    <h2><?php _e('Total Deductions : ', 'wphrm'); ?><input type="hidden" id="inputdeductioncalsum"><font id="callEarningTotal"><?php
                                                        if (isset($wphrmCurrencyinfo)): echo esc_html($wphrmCurrencyinfo) . '<span id="deductioncalsum"> ' . esc_html($wphrmDeductiontotal) . "</span>";
                                                        endif;
                                                        ?></font></h2>

                                                </th>

                                            </tr>

                                        </thead></table>                  
                                    <div class="col-xs-12 net-pay-table" style="padding:10px 25px 0px 0px; " c>
                                        <div class="net-pay">
                                            <h1><?php _e('Net Pay', 'wphrm'); ?> : <?php
                                                if (isset($wphrmCurrencyinfo)): echo esc_html($wphrmCurrencyinfo) . ' ' . '<span id="netTotal"> ' . esc_html($net_amount) . "</span>";
                                                endif;
                                                ?></h1>
                                        </div>
                                        <input type="hidden" name="emp_generate_id" id="emp_generate_id" value="<?php
                                               if (isset($employee_id)): echo esc_attr($employee_id);
                                               endif;
                                               ?>" />
                                        <input type="hidden" name="generate_month" id="generate_month" value="<?php
                                               if (isset($generate_month)): echo esc_attr($generate_month);
                                               endif;
                                               ?>" />
                                        <input type="hidden" name="generate_year" id="generate_year" value="<?php
                                               if (isset($generate_year)): echo esc_attr($generate_year);
                                               endif;
                                               ?>" />
                                        <input type="hidden" name="generate_week_no" id="generate_week_no" value="<?php
                                               if (isset($_REQUEST['wphrm-create-week-salary-week-no'])): echo esc_attr($_REQUEST['wphrm-create-week-salary-week-no']);
                                               endif;
                                               ?>" />
                                        <div class="form-group col-xs-12">
                                            <h2><?php
                                                _e('Information', 'wphrm');
                                                echo '/';
                                                _e('Note', 'wphrm');
                                                ?>:</h2>
                                            <textarea class="form-control" id="information" type="text" placeholder="<?php _e('Information', 'wphrm'); ?>" name="information" ><?php
                                                if (isset($wphrm_text_info['wphrm_information'])) : echo esc_attr($wphrm_text_info['wphrm_information']);
                                                endif;
                                                ?></textarea>

                                        </div>
                                        <div class="form-group col-xs-12" style="text-align: center;">
                                            <button class="btn green" type="submit"><i class="fa fa-check"></i><?php _e('Generate Salary Slip', 'wphrm'); ?></button>
                                        </div>
                                    </div>                            

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>