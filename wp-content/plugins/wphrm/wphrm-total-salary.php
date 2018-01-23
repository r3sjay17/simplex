<?php
if (!defined('ABSPATH'))
    exit;
global $wpdb;
$wphrmUsers = $this->WPHRMGetEmployees();
if ((isset($_REQUEST['employee_id']) && $_REQUEST['employee_id'] != '')) {
    if (isset($_REQUEST['employee_id'])) {
        $employeeId = esc_sql($_REQUEST['employee_id']); // esc
    }
    $wphrm_info = $this->WPHRMGetUserDatas($employeeId, 'wphrmEmployeeInfo');
    $wphrmGeneralSettingsInfo = $this->WPHRMGetSettings('wphrmGeneralSettingsInfo');
    $currency = explode('-', $wphrmGeneralSettingsInfo['wphrm_currency']);
    $salaryTotal = 0;
    $wphrmEarningtotal = '';
    $salaryTotalEarning = '';
    $wphrmDeductiontotal = '';
    $salaryTotalDeduction = '';
    $wphrmEarningfiledskeyinformation = esc_sql('wphrmEarningfiledskey'); // esc
    $wphrmEarningfiledskey = $wpdb->get_results("SELECT * FROM $this->WphrmSalaryTable WHERE  `employeeID`='$employeeId' AND `employeeKey`='$wphrmEarningfiledskeyinformation'");
    foreach ($wphrmEarningfiledskey as $wphrmEarningfiledskeys) {
        $wphrmEarningInfo = unserialize(base64_decode($wphrmEarningfiledskeys->employeeValue));
        foreach ($wphrmEarningInfo['wphrmEarningValue'] as $wphrmEarningInfos) {
            $wphrmEarningtotal = $wphrmEarningInfos + $salaryTotalEarning;
            $salaryTotalEarning = $wphrmEarningtotal;
        }
    }

    $wphrmDeductionFiledskeyInformation = esc_sql('wphrmDeductionfiledskey'); // esc
    $wphrmDeductionfiledskey = $wpdb->get_results("SELECT * FROM $this->WphrmSalaryTable WHERE `employeeID`='$employeeId' AND `employeeKey`='$wphrmDeductionFiledskeyInformation'");
    foreach ($wphrmDeductionfiledskey as $wphrmDeductionfiledskeys) {
        $wphrmDeductionInfo = unserialize(base64_decode($wphrmDeductionfiledskeys->employeeValue));
        foreach ($wphrmDeductionInfo['wphrmDeductionValue'] as $wphrmDeductionInfos) {
            $wphrmDeductiontotal = $wphrmDeductionInfos + $salaryTotalDeduction;
            $salaryTotalDeduction = $wphrmDeductiontotal;
        }
    }
    $salaryTotal = ($salaryTotalEarning - $salaryTotalDeduction);
}
?>
<!-- BEGIN PAGE HEADER-->
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="preloader">
    <span class="preloader-custom-gif"></span>
</div>
<div style="padding-left: 0px; padding-right:20px; padding-top:0px;" class="col-md-12">
    <h3 class="page-title"><?php _e('Total Salary', 'wphrm'); ?></h3>
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li><i class="fa fa-home"></i><?php _e('Home', 'wphrm'); ?><i class="fa fa-angle-right"></i></li>
            <li><strong><?php
                    if (isset($wphrm_info['wphrm_employee_fname'])) : echo esc_html($wphrm_info['wphrm_employee_fname']) . ' ' . esc_html($wphrm_info['wphrm_employee_lname']);
                    endif;
                    ?><?php _e(' Total Salary', 'wphrm'); ?></strong></li>

        </ul>
    </div>
    <a class="btn green " href="?page=wphrm-salary" ><i class="fa fa-arrow-left"></i><?php _e('Back', 'wphrm'); ?></a>
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box blue calendar">
                <div class="portlet-title">
                    <div class="caption"> <i class="fa fa-list"></i><?php _e("List of "); ?><?php
                        if (isset($wphrm_info['wphrm_employee_fname'])) : echo esc_html($wphrm_info['wphrm_employee_fname']) . ' ' . esc_html($wphrm_info['wphrm_employee_lname']);
                        endif;
                        ?><?php _e(' Total Salary', 'wphrm'); ?> </div>
                    <div class="actions">
                        <a href="javascript:;"  onclick="wphrmTotalSalaryReports('wphrm-pdf-reports','finacial-report')" data-loading-text="Updating..."  class="demo-loading-btn btn btn-sm red">
                            <i class="fa fa-download" ></i><?php _e('PDF', 'wphrm'); ?>
                        </a>
                        <a href="javascript:;"  onclick="wphrmTotalSalaryReports('wphrm-excel-reports','finacial-report')" data-loading-text="Updating..."  class="demo-loading-btn btn btn-sm yellow">
                            <i class="fa fa-download" ></i><?php _e('Excel', 'wphrm'); ?>
                        </a>
                    </div>
                </div>
                <div class="portlet-body">
                     <form name="wphrm-total-salary-reports" id="wphrm-profit-loss" action="?page=wphrm-total-salary-reports" method="post">
                        <div class="col-md-12 col-sm-12" style="text-align: center;">                          
                            <label style="font-weight: unset;">
                                <?php _e('From Date', 'wphrm'); ?> : <input  placeholder="<?php _e('From Date', 'wphrm'); ?> "  id="from-month-year" name="from-date" class="month-year form-control input-small input-inline">
                            </label>
                            <label style="font-weight: unset;">&nbsp;&nbsp;
                                <?php _e('To Date', 'wphrm'); ?> : <input placeholder="<?php _e('To Date', 'wphrm'); ?>"  id="to-month-year" name="to-date"  class="month-year form-control input-small input-inline">
                            </label>                                
                            <input type="hidden" name="wphrm-report-type" value="" id="wphrm-report-type" class="wphrm-report-type" />
                            <input type="hidden" name="wphrm-employee-id" value="<?php   if (isset($employeeId)) : echo esc_html($employeeId); endif;  ?>" id="wphrm-employee-id" class="wphrm-employee-id" />
                          <label style="font-size: 14px; font-weight: normal; margin-right: -15px; float: right;">
                            <?php _e('Total Salary Paid', 'wphrm'); ?> : &nbsp;<input style="background: none repeat scroll 0 0 #fff;
    color: #444;width: 225px;" disabled class="form-control input-small input-inline"  value="<?php if (isset($currency[0])) {
                                echo esc_attr($currency[0]);
                            } else {
                                echo '&#36;';
                            } ?> <?php if (isset($salaryTotal)) {
                                echo esc_attr($salaryTotal);
                            } ?>">
                        </label>
                        </div>
                     </form>
                    <br>
                    <br>
                    
                    <table class="table table-striped table-bordered table-hover" id="wphrmDataTable">
                        <thead>
                            <tr>
                                <th><?php _e('S.No', 'wphrm'); ?></th>
                                <th><?php _e('Month', 'wphrm'); ?></th>
                                <th><?php _e('Earning', 'wphrm'); ?></th>
                                <th><?php _e('Deduction', 'wphrm'); ?></th>
                                <th><?php _e('Total Paid', 'wphrm'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $j = 1;
                            $salaryEmployeeCounter = 0;
                            $wphrmEmployeeSalaryGenerated = esc_sql('employeeSalaryGenerated'); // esc
                            $wphrmDeductionFiledsKey = esc_sql('wphrmDeductionfiledskey'); // esc
                            $wphrmEarningFiledsKey = esc_sql('wphrmEarningfiledskey'); // esc
                            $wphrmSalaryGenerateds = $wpdb->get_results("SELECT * FROM $this->WphrmSalaryTable WHERE `employeeID`='$employeeId' AND `employeeKey`='$wphrmEmployeeSalaryGenerated' ORDER BY date DESC ");
                            foreach ($wphrmSalaryGenerateds as $wphrmSalaryGenerated) {
                                $earningSalaryTotal = 0;
                                $deductionSalaryTotal = 0;
                                $wphrmEarningFiledsKeyInfo = $wpdb->get_row("SELECT * FROM $this->WphrmSalaryTable WHERE  `employeeID`='$employeeId' AND  `employeeKey`='$wphrmEarningFiledsKey' AND `date`='$wphrmSalaryGenerated->date'");
                                $wphrmEarningInfo = unserialize(base64_decode($wphrmEarningFiledsKeyInfo->employeeValue));
                                foreach ($wphrmEarningInfo['wphrmEarningValue'] as $wphrmEarningInfos) {
                                    $wphrmEarningtotal = $wphrmEarningInfos + $earningSalaryTotal;
                                    $earningSalaryTotal = $wphrmEarningtotal;
                                }
                                $wphrmDeductionFiledsKeyInfo = $wpdb->get_row("SELECT * FROM $this->WphrmSalaryTable WHERE  `employeeID`='$employeeId' AND  `employeeKey`='$wphrmDeductionFiledsKey' AND `date`='$wphrmSalaryGenerated->date'");
                                $wphrmDeductionInfo = unserialize(base64_decode($wphrmDeductionFiledsKeyInfo->employeeValue));
                                foreach ($wphrmDeductionInfo['wphrmDeductionValue'] as $wphrmDeductionInfos) {
                                    $wphrmDeductiontotal = $wphrmDeductionInfos + $deductionSalaryTotal;
                                    $deductionSalaryTotal = $wphrmDeductiontotal;
                                }
                                $wphrmSalaryGeneratedDate = date('F Y', strtotime($wphrmSalaryGenerated->date));
                                $paidSalaryTotal = $earningSalaryTotal - $deductionSalaryTotal;
                                ?>
                                <tr id="row">
                                    <td><?php echo esc_html($j); ?></td>

                                    <td><?php
                                        if (isset($wphrmSalaryGeneratedDate)) : echo esc_html($wphrmSalaryGeneratedDate);
                                        endif;
                                        ?>
                                    </td>
                                    <td> <?php
                                        if (isset($currency[0])) {
                                            echo esc_attr($currency[0]) . ' ';
                                        } else {
                                            echo '&#36; ';
                                        }
                                        ?><?php
                                        if (isset($earningSalaryTotal)) : echo esc_html($earningSalaryTotal);
                                        endif;
                                        ?>
                                    </td>
                                    <td><?php
                                    if (isset($currency[0])) {
                                        echo esc_attr($currency[0]) . ' ';
                                    } else {
                                        echo '&#36; ';
                                    }
                                        ?>
                                        <?php
                                        if (isset($deductionSalaryTotal)) : echo esc_html($deductionSalaryTotal);
                                        endif;
                                        ?>
                                    </td>
                                    <td><?php
                                    if (isset($currency[0])) {
                                        echo esc_attr($currency[0]) . ' ';
                                    } else {
                                        echo '&#36; ';
                                    }
                                        ?>
                                <?php
                                if (isset($paidSalaryTotal)) : echo esc_html($paidSalaryTotal);
                                endif;
                                ?>
                                    </td>


                                </tr>
    <?php
    $salaryEmployeeCounter++;
    $j++;
}
if ($salaryEmployeeCounter == 0) {
    ?>
                                <tr>
                                    <td colspan="5"><?php _e('No salary data found in database.', 'wphrm'); ?>
                                    </td><td class="collapse"></td><td class="collapse"></td><td class="collapse"></td><td class="collapse"></td><td class="collapse"></td><td class="collapse"></td><td class="collapse"></td>
                                </tr>
<?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div><div id="financialModal" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
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
                <h4 class="modal-title"><?php _e('Total Salary', 'wphrm'); ?></h4>
            </div>
            <div class="modal-body" id="info"><?php _e('Please select date range.', 'wphrm'); ?></div>
            <div class="modal-footer">
               
                <button type="button" data-dismiss="modal" aria-hidden="true" class="btn default"><i class="fa fa-times"></i><?php _e('Cancel', 'wphrm'); ?></button>
            </div>
        </div>
    </div>
</div>
<!-- END PAGE CONTENT-->