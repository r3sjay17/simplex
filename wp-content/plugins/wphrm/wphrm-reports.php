<?php
if (!defined('ABSPATH'))
    exit;
/** WP_HRM class use for all types report
 *   use EXCEL & PDF formate
 *   
 */
class WPHRMReporting {

// Constructor
    protected $wphrmMainClass;
    protected $departmentTable, $salaryTable, $designationTable, $settingsTable;

    public function __construct($wphrmMainClass) {
        $this->WPHRMObjectStart();
        $this->wphrmMainClass = $wphrmMainClass;
        $this->departmentTable = $this->wphrmMainClass->WphrmDepartmentTable;
        $this->salaryTable = $this->wphrmMainClass->WphrmSalaryTable;
        $this->designationTable = $this->wphrmMainClass->WphrmDesignationTable;
        $this->settingsTable = $this->wphrmMainClass->WphrmSettingsTable;
        $this->financialsTable = $this->wphrmMainClass->WphrmFinancialsTable;
        $this->DepartmentTable = $this->wphrmMainClass->WphrmDepartmentTable;
        $this->DesignationTable = $this->wphrmMainClass->WphrmDesignationTable;
        $this->WeeklySalaryTable = $this->wphrmMainClass->WphrmWeeklySalaryTable;
    }
    
   public function WPHRMObjectStart() {
        @ob_end_clean();
        if (ob_get_level() == 0) {
            ob_start();
        }
    }

    public function WPHRMGetSalarySlipPDF($wphrmEmployeeID, $wphrmDate) {
        ob_clean();
        global $current_user, $wpdb;
        $wphrmEmployeeID = esc_sql($wphrmEmployeeID); // esc
        $wphrmDate = esc_sql($wphrmDate); // esc
        $employeeSalaryNoteInformation = esc_sql('employeeSalaryNote'); // esc
        $wphrmEarningfiledskeyInformation = esc_sql('wphrmEarningfiledskey'); // esc
        $wphrmDeductionfiledskeyInformation = esc_sql('wphrmDeductionfiledskey'); // esc
        $wphrmEmployeeInfo = $this->wphrmMainClass->WPHRMGetUserDatas($wphrmEmployeeID, 'wphrmEmployeeInfo');
        $wphrmSalaryAccording = $this->wphrmMainClass->WPHRMGetSettings('wphrsalaryDayOrHourlyInfo');
        $wphrmEmployeeBankInfo = $this->wphrmMainClass->WPHRMGetUserDatas($wphrmEmployeeID, 'wphrmEmployeeBankInfo');
        $decimals = $this->wphrmMainClass->WPHRMGetDecimalSettings();
       if (isset($decimals['decimalvalue'])): 
        $decimal = $decimals['decimalvalue'];
        else :
        $decimal = 2;
         endif;
        $wphrmEmployeeTextInfo = $wpdb->get_row("SELECT * FROM  $this->salaryTable  WHERE `employeeID` = $wphrmEmployeeID AND  `employeeKey` = '$employeeSalaryNoteInformation' AND `date` = '$wphrmDate'");
       
        if (!empty($wphrmEmployeeTextInfo)) :
            $wphrmEmployeeTextInfo = unserialize(base64_decode($wphrmEmployeeTextInfo->employeeValue));

        endif;
        if (isset($wphrmEmployeeInfo['wphrm_employee_department']) && $wphrmEmployeeInfo['wphrm_employee_department'] != '') {
            $employeeDepartmentsLoads = $wphrmEmployeeInfo['wphrm_employee_department'];
            $wphrmDepartments = $wpdb->get_row("SELECT * FROM  $this->departmentTable  WHERE `departmentID` = '$employeeDepartmentsLoads'");
            if ($wphrmDepartments != '') {
                $wphrmDepartmentInfo = unserialize(base64_decode($wphrmDepartments->departmentName));
            }
        }

        if (isset($wphrmEmployeeInfo['wphrm_employee_designation']) && $wphrmEmployeeInfo['wphrm_employee_designation'] != '') {
            $employeeDesignationLoads = $wphrmEmployeeInfo['wphrm_employee_designation'];
            $wphrmDesignation = $wpdb->get_row("SELECT * FROM  $this->designationTable  WHERE `designationID` = '$employeeDesignationLoads'");

            if ($wphrmDesignation != '') {
                $wphrmDesignationInfo = unserialize(base64_decode($wphrmDesignation->designationName));
            }
        }
        $wphrmEarningSalary = $wpdb->get_row("SELECT * FROM  $this->salaryTable  WHERE `employeeID` = $wphrmEmployeeID AND `date` ='$wphrmDate' AND `employeeKey` ='$wphrmEarningfiledskeyInformation'");
        $wphrmDeductionSalary = $wpdb->get_row("SELECT * FROM  $this->salaryTable  WHERE `employeeID` = $wphrmEmployeeID AND `date` ='$wphrmDate' AND `employeeKey` ='$wphrmDeductionfiledskeyInformation'");
        
        $wphrmInfoText = $wpdb->get_row("SELECT * FROM  $this->salaryTable  WHERE `employeeID` = $wphrmEmployeeID AND `date` ='$wphrmDate' AND `employeeKey` ='$employeeSalaryNoteInformation'");
        $wphrmCompanyInfoSV = $this->wphrmMainClass->WPHRMGetSettings('wphrmGeneralSettingsInfo');
        $wphrmSalarySlipInfos = $this->wphrmMainClass->WPHRMGetSettings('wphrmSalarySlipInfo');
        $logo = '';
        if (isset($wphrmSalarySlipInfos['wphrm_logo_align']) && $wphrmSalarySlipInfos['wphrm_logo_align'] != '') {
            $logo = $wphrmSalarySlipInfos['wphrm_logo_align'];
        }
        $footer_tag_contain = '';
        if (isset($wphrmSalarySlipInfos['wphrm_slip_content']) && $wphrmSalarySlipInfos['wphrm_slip_content'] != '') {
            $footer_tag_contain = $wphrmSalarySlipInfos['wphrm_slip_content'];
        }
        $footer_tag_align = '';
        if (isset($wphrmSalarySlipInfos['wphrm_footer_content_align']) && $wphrmSalarySlipInfos['wphrm_footer_content_align'] != '') {
            $footer_tag_align = $wphrmSalarySlipInfos['wphrm_footer_content_align'];
        }
        $page_border_color = '';
        if (isset($wphrmSalarySlipInfos['wphrm_border_color']) && $wphrmSalarySlipInfos['wphrm_border_color'] != '') {
            $page_border_color = $wphrmSalarySlipInfos['wphrm_border_color'];
        }
        $h1_color = '';
        if (isset($wphrmSalarySlipInfos['wphrm_background_color']) && $wphrmSalarySlipInfos['wphrm_background_color'] != '') {
            $h1_color = $wphrmSalarySlipInfos['wphrm_background_color'];
        }
        $font_color = '';
        if (isset($wphrmSalarySlipInfos['wphrm_font_color']) && $wphrmSalarySlipInfos['wphrm_font_color'] != '') {
            $font_color = $wphrmSalarySlipInfos['wphrm_font_color'];
        }
        $wphrm_company_logo = '';
        if (isset($wphrmCompanyInfoSV['wphrm_company_logo']) && $wphrmCompanyInfoSV['wphrm_company_logo'] != '') {
            $wphrm_company_logo = $wphrmCompanyInfoSV['wphrm_company_logo'];
        }
        $wphrm_company_phone = '';
        if (isset($wphrmCompanyInfoSV['wphrm_company_phone']) && $wphrmCompanyInfoSV['wphrm_company_phone'] != '') {
            $wphrm_company_phone = $wphrmCompanyInfoSV['wphrm_company_phone'];
        }
        $wphrm_company_address = '';
        if (isset($wphrmCompanyInfoSV['wphrm_company_address']) && $wphrmCompanyInfoSV['wphrm_company_address'] != '') {
            $wphrm_company_address = $wphrmCompanyInfoSV['wphrm_company_address'];
        }
        $wphrm_company_email = '';
        if (isset($wphrmCompanyInfoSV['wphrm_company_email']) && $wphrmCompanyInfoSV['wphrm_company_email'] != '') {
            $wphrm_company_email = $wphrmCompanyInfoSV['wphrm_company_email'];
        }
        $wphrm_currency = '';
        $wphrm_currencys = '';
        if (isset($wphrmCompanyInfoSV['wphrm_currency']) && $wphrmCompanyInfoSV['wphrm_currency'] != '') {
            $wphrm_currencys = $wphrmCompanyInfoSV['wphrm_currency'];
            $selected_currency = explode('-', $wphrm_currencys);
            $wphrm_currency = $selected_currency[0];
        }
        $wphrmEmployeeSalaryInfo = $this->wphrmMainClass->WPHRMGetUserDatas($wphrmEmployeeID, 'wphrmEmployeeSalaryInfo');
        ob_clean();
        ?>
        <style>
            body {
                font-family: "<?php
                if (isset($font_family)): echo esc_html($font_family);
                endif;
                ?>";
                font-size: 11px; color: <?php
                if (!empty($font_color)) {
                    echo esc_html($font_color);
                } else {
                    echo esc_html('#546e7a');
                }
                ?>; 
                line-height: 25px;
                letter-spacing: 0.5px;
            }
            * { margin: 0;padding: 0; }
            h1 { font-size: 18px;font-weight: 700; }
            h2 { font-size: 14px;font-weight: 700; }
            .space { padding: 0 15px; }
        </style>
        <?php $data['style'] = ob_get_contents(); ob_clean(); ?>
        <link href='https://fonts.googleapis.com/css?family=Noto+Sans:400,700' rel='stylesheet' type='text/css'>
        <table style="width: 100%; border-collapse: collapse; "  align="center" >
            <tbody>
                <tr>
                    <?php if ($logo == 'left') { ?>
                        <td colspan="2" style="padding: 20px 0px;">
                            <img src="<?php
                            if (!empty($wphrm_company_logo)) {
                                echo esc_attr($wphrm_company_logo);
                            } else {
                                echo esc_attr(plugins_url('assets/images/Logo_3.png'));
                            }
                            ?>" height="100" width="auto">
                        </td>
                        <td colspan="2" style="padding: 20px 0px;"  align="right">
                            <?php
                            $wphrm_company = explode(",", $wphrm_company_address);
                            foreach ($wphrm_company as $key => $wphrm_company_data) {
                                if ($key == 3) {
                                    echo esc_html($wphrm_company_data) . ',<br>';
                                } else {
                                    echo esc_html($wphrm_company_data) . ',';
                                }
                            }
                            ?>
                            <br><?php _e('Phone', 'wphrm'); ?>:
                            <?php
                            if (isset($wphrm_company_phone)): echo esc_html($wphrm_company_phone);
                            endif;
                            ?>
                            <br> <?php _e('Email', 'wphrm'); ?>: <?php
                            if (isset($wphrm_company_email)): echo esc_html($wphrm_company_email);
                            endif;
                            ?>
                        </td>
                    <?php } else if ($logo == 'center') { ?>
                        <td colspan="2" style="padding: 20px 0px; text-align: right;">
                            <img src="<?php
                            if (isset($wphrm_company_logo)): echo esc_attr($wphrm_company_logo);
                            endif;
                            ?>" height="100" width="auto">
                        </td>
                        <td colspan="2" style="padding: 20px 0px;"  align="right">
                            <?php
                            $wphrm_company = explode(",", $wphrm_company_address);
                            foreach ($wphrm_company as $key => $wphrm_company_data) {
                                if ($key == 3) {
                                    echo esc_html($wphrm_company_data) . ',<br>';
                                } else {
                                    echo esc_html($wphrm_company_data) . ',';
                                }
                            }
                            ?>
                            <br><?php _e('Phone', 'wphrm'); ?>: <?php
                            if (isset($wphrm_company_phone)): echo esc_html($wphrm_company_phone);
                            endif;
                            ?>
                            <br> <?php _e('Email', 'wphrm'); ?>: <?php
                            if (isset($wphrm_company_email)): echo esc_html($wphrm_company_email);
                            endif;
                            ?>
                        </td>
                    <?php } else if ($logo == 'right') { ?>
                        <td colspan="2" style="padding: 20px 0px;"  >
                            <?php
                            $wphrm_company = explode(",", $wphrm_company_address);
                            foreach ($wphrm_company as $key => $wphrm_company_data) {
                                if ($key == 3) {
                                    echo esc_html($wphrm_company_data) . ',<br>';
                                } else {
                                    echo esc_html($wphrm_company_data) . ',';
                                }
                            }
                            ?>
                            <br><?php _e('Phone', 'wphrm'); ?>: <?php
                            if (isset($wphrm_company_phone)): echo esc_html($wphrm_company_phone);
                            endif;
                            ?>
                            <br> <?php _e('Email', 'wphrm'); ?>: <?php
                            if (isset($wphrm_company_email)): echo esc_html($wphrm_company_email);
                            endif;
                            ?>
                        </td>
                        <td colspan="2" style="padding: 20px 0px;" align="right"><img src="<?php echo esc_attr($wphrm_company_logo) ?>" height="100" width="260"></td>
                    <?php } else { ?> 
                        <td colspan="2" style="padding: 20px 0px;"><img src="<?php echo esc_attr($wphrm_company_logo); ?>" height="100" width="260"></td>
                        <td colspan="2" style="padding: 20px 0px;"  align="right">
                            <?php
                            $wphrm_company = explode(",", $wphrm_company_address);
                            foreach ($wphrm_company as $key => $wphrm_company_data) {
                                if ($key == 3) {
                                    echo esc_html($wphrm_company_data) . ',<br>';
                                } else {
                                    echo esc_html($wphrm_company_data) . ',';
                                }
                            }
                            ?>
                            <br><?php _e('Phone', 'wphrm'); ?>: <?php
                            if (isset($wphrm_company_phone)): echo esc_html($wphrm_company_phone);
                            endif;
                            ?>
                            <br> <?php _e('Email', 'wphrm'); ?>: <?php
                            if (isset($wphrm_company_email)): echo esc_html($wphrm_company_email);
                            endif;
                            ?>
                        </td>
                    <?php } ?>
                </tr>
                <tr>
                    <td colspan="4" style="padding: 40px 0px;border-top: 1px solid <?php
                    if (!empty($page_color)) {
                        echo esc_attr($page_color);
                    } else {
                        echo esc_attr('#cfd8dc');
                    }
                    ?>" align="center">
                        <h1 style="text-transform: uppercase; border-bottom: 1px solid; display: inline-block;" align="center">
                            <?php _e('salary slip for the month  of', 'wphrm'); ?><?php
                            if (isset($wphrmDate)): echo esc_html(date(' F Y', strtotime($wphrmDate)));
                            endif;
                            ?> 
                        </h1>
                    </td>
                </tr>
                <tr>  
                    <td colspan="2" style="vertical-align: top; width: 50%; padding-bottom: 20px;">
                        <table style="border-collapse: collapse; width:97%;" >
                            <tbody>
                                <tr>
                                    <td><?php _e('Name', 'wphrm'); ?><span style="float: right; width: 30px;"> :</span></td>
                                    <td style="float: right; width: 100px;"><?php
                                        if (isset($wphrmEmployeeInfo['wphrm_employee_fname'])) : echo esc_html($wphrmEmployeeInfo['wphrm_employee_fname']);
                                        endif;
                                        if (isset($wphrmEmployeeInfo['wphrm_employee_lname'])) : echo ' ' . esc_html($wphrmEmployeeInfo['wphrm_employee_lname']);
                                        endif;
                                        ?></td>
                                </tr>
                                <tr>
                                    <td><?php _e('Designation', 'wphrm'); ?><span style="float: right; width: 30px;" > :</span></td>
                                    <td style="float: right; width: 100px;"><?php
                                        if (isset($wphrmDesignationInfo['designationName'])) : echo esc_html($wphrmDesignationInfo['designationName']);
                                        endif;
                                        ?></td>
                                </tr>
                                <tr>
                                    <td><?php _e('Department', 'wphrm'); ?><span style="float: right; width: 30px;"> :</span></td>
                                    <td><?php
                                        if (isset($wphrmDepartmentInfo['departmentName'])) : echo esc_html($wphrmDepartmentInfo['departmentName']);
                                        endif;
                                        ?></td>
                                </tr>
                                <tr>
                                    <td><?php _e('Transferred to', 'wphrm'); ?><span style="float: right; width: 30px;"> :</span></td>
                                    <td>
                                        <?php
                                        if (isset($wphrmEmployeeTextInfo['wphrm_account_no'])) : echo esc_html($wphrmEmployeeTextInfo['wphrm_account_no']);
                                        endif;
                                        ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td colspan="2" style="vertical-align: top; width: 50%; padding-bottom: 20px;">
                        <table style="border-collapse: collapse; width: 97%;" >
                            <tbody>
                                <tr>
                                    <td><?php _e('Employee ID', 'wphrm'); ?><span style="float: right; width: 30px;"> :</span></td>
                                    <td><?php
                                        if (isset($wphrmEmployeeInfo['wphrm_employee_uniqueid'])) : echo esc_html($wphrmEmployeeInfo['wphrm_employee_uniqueid']);
                                        endif;
                                        ?></td>
                                </tr>
                               
                                <?php if (isset($wphrmSalaryAccording['wphrm-according']) && ($wphrmSalaryAccording['wphrm-according'] == 'Hourly')) { ?>
                                    <tr>
                                        <td><?php _e('Total Hours Worked', 'wphrm'); ?><span style="float: right; width: 30px;"> :</span></td>
                                        <td><?php
                                            if (isset($wphrmEmployeeTextInfo['wphrm_Hours'])) : echo esc_html($wphrmEmployeeTextInfo['wphrm_Hours']);
                                            endif;
                                            ?></td>
                                    </tr> 
                                <?php } else { ?>
                                    <tr>
                                        <td><?php _e('Total  Working Days', 'wphrm'); ?><span style="float: right; width: 30px;"> :</span></td>
                                        <td><?php
                                            if (isset($wphrmEmployeeTextInfo['wphrm_dayofworking'])) : echo esc_html($wphrmEmployeeTextInfo['wphrm_dayofworking']);
                                            endif;
                                            ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php _e('Leave Days', 'wphrm'); ?><span style="float: right; width: 30px;"> :</span></td>
                                        <td><?php
                                            if (isset($wphrmEmployeeTextInfo['wphrm_workleave'])) : echo esc_html($wphrmEmployeeTextInfo['wphrm_workleave']);
                                            endif;
                                            ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php _e('Total Days Worked', 'wphrm'); ?><span style="float: right; width: 30px;"> :</span></td>
                                        <td><?php
                                            if (isset($wphrmEmployeeTextInfo['wphrm_dayofworked'])) : echo esc_html($wphrmEmployeeTextInfo['wphrm_dayofworked']);
                                            endif;
                                            ?></td>
                                    </tr>



                                <?php } ?>
                                
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="vertical-align: top; width: 50%; padding-bottom: 20px;">
                        <table style="border-collapse: collapse; width: 97%;">
                            <tbody>
                                <tr>
                                    <td  style="padding: 8px 15px;margin: 40px 0px 0px; background: <?php
                                    if (!empty($h1_color)) {
                                        echo esc_attr($h1_color);
                                    } else {
                                        echo esc_attr('#ECEFF1');
                                    }
                                    ?>" align="left">
                                        <h2 style="background: <?php
                                        if (!empty($h1_color)) {
                                            echo esc_attr($h1_color);
                                        } else {
                                            echo esc_attr('#ECEFF1');
                                        }
                                        ?>!important;"><?php _e('Earnings', 'wphrm'); ?></h2>
                                    </td>
                                    <td  style="padding: 8px 15px;margin: 40px 0px 0px; background: <?php
                                    if (!empty($h1_color)) {
                                        echo esc_attr($h1_color);
                                    } else {
                                        echo esc_attr('#ECEFF1');
                                    }
                                    ?>" align="right">
                                        <h2 style="background: <?php
                                        if (!empty($h1_color)) {
                                            echo esc_attr($h1_color);
                                        } else {
                                            echo esc_attr('#ECEFF1');
                                        }
                                        ?> !important;"><?php _e('Amount', 'wphrm'); ?></h2>
                                    </td>
                                </tr>
                                <?php
                                if (!empty($wphrmEarningSalary)) {
                                    $wphrmEarningInfo = unserialize(base64_decode($wphrmEarningSalary->employeeValue));
                                    foreach ($wphrmEarningInfo['wphrmEarningLebal'] as $earningkey1 => $wphrmEarninglebal) {
                                        foreach ($wphrmEarningInfo['wphrmEarningValue'] as $earningkey => $wphrmEarningvalue) {
                                            if ($earningkey1 == $earningkey) {
                                                $wphrmEarningtotal = $wphrmEarningvalue + $i;
                                                $i = $wphrmEarningtotal;
                                                ?>
                                                <tr>
                                                    <td class="space" style="padding: 4px 15px;" ><?php
                                                        if (isset($wphrmEarninglebal)): echo esc_html($wphrmEarninglebal);
                                                        endif;
                                                        ?>
                                                    </td>
                                                    <td class="space" style="padding: 4px 15px;"  align="right"><?php
                                                        if (isset($wphrmEarningvalue)): echo esc_html(round($wphrmEarningvalue, $decimal));
                                                        endif;
                                                        ?>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </td>
                    <td colspan="2" style="vertical-align: top; width: 50%; padding-bottom: 20px;">
                        <table style="border-collapse: collapse; width: 97%;">
                            <tbody>
                                <tr>
                                    <td  style="padding: 8px 15px;margin: 40px 0px 0px; background: <?php
                                    if (!empty($h1_color)) {
                                        echo esc_attr($h1_color);
                                    } else {
                                        echo esc_attr('#ECEFF1');
                                    }
                                    ?>" align="left">
                                        <h2 style="background: <?php
                                        if (!empty($h1_color)) {
                                            echo esc_attr($h1_color);
                                        } else {
                                            echo esc_attr('#ECEFF1');
                                        }
                                        ?>!important;"><?php _e('Deductions', 'wphrm'); ?></h2>
                                    </td>
                                    <td  style="padding: 8px 15px;margin: 40px 0px 0px; background: <?php
                                    if (!empty($h1_color)) {
                                        echo esc_attr($h1_color);
                                    } else {
                                        echo esc_attr('#ECEFF1');
                                    }
                                    ?>" align="right">
                                        <h2 style="background: <?php
                                        if (!empty($h1_color)) {
                                            echo esc_attr($h1_color);
                                        } else {
                                            echo esc_attr('#ECEFF1');
                                        }
                                        ?>!important;"><?php _e('Amount', 'wphrm'); ?></h2>
                                    </td>
                                </tr>
                                <?php
                                $i = 0;
                                if (!empty($wphrmDeductionSalary)) {
                                    $wphrmDeductionInfo = unserialize(base64_decode($wphrmDeductionSalary->employeeValue));
                                    foreach ($wphrmDeductionInfo['wphrmDeductionLebal'] as $earningkey1 => $wphrmDeductionlebal) {
                                        foreach ($wphrmDeductionInfo['wphrmDeductionValue'] as $earningkey => $wphrmDeductionvalue) {
                                            if ($earningkey1 == $earningkey) {
                                                $wphrmDeductiontotal = $wphrmDeductionvalue + $i;
                                                $i = $wphrmDeductiontotal;
                                                ?>
                                                <tr>
                                                    <td class="space" style="padding: 4px 15px;" align="left"><?php
                                                        if (isset($wphrmDeductionlebal)): echo esc_html($wphrmDeductionlebal);
                                                        endif;
                                                        ?></td>
                                                    <td class="space" style="padding: 4px 15px;" align="right">
                                                        <?php
                                                        if (isset($wphrmDeductionvalue)): echo esc_html(round($wphrmDeductionvalue, $decimal));
                                                        endif;
                                                        ?>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="vertical-align: top; width: 50%; padding-bottom: 20px;">
                        <table style="border-collapse: collapse; width: 97%; border-top: 2px solid <?php
                        if (!empty($page_color)) {
                            echo esc_attr($page_color);
                        } else {
                            echo esc_attr('#cfd8dc');
                        }
                        ?>" >
                            <tbody>
                                <tr>
                                    <td style="padding-top: 8px;">
                                        <h2 style="padding: 8px 15px 15px;"><?php _e('Total Earnings', 'wphrm'); ?></h2>
                                    </td>
                                    <td align="right" style="padding-top: 8px;">
                                        <h2 style="padding: 8px 15px 15px;">
                                            <?php
                                            if (isset($wphrm_currency)): echo esc_html($wphrm_currency) . ' ' . esc_html(round($wphrmEarningtotal, $decimal));
                                            endif;
                                            ?>
                                        </h2>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td colspan="2" style="vertical-align: top; width: 50%; padding-bottom: 20px;" >
                        <table style="border-collapse: collapse; width: 97%; border-top: 2px solid <?php
                        if (!empty($page_color)) {
                            echo esc_attr($page_color);
                        } else {
                            echo esc_attr('#cfd8dc');
                        }
                        ?>" >
                            <tbody>
                                <tr>
                                    <td style="padding-top: 8px;" align="left">
                                        <h2 style="padding: 8px 15px;"><?php _e('Total Deductions', 'wphrm'); ?></h2>
                                    </td >
                                    <td align="right" style="padding-top: 8px;">
                                        <h2 style="padding: 8px 15px;">
                                            <?php
                                            if (isset($wphrm_currency)) : echo esc_html($wphrm_currency) . ' ' . esc_html(round($wphrmDeductiontotal, $decimal));
                                            endif;
                                            ?>
                                        </h2>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr><?php $net_amount = $wphrmEarningtotal - $wphrmDeductiontotal; ?>
                    <td colspan="4" style="padding: 8px 15px;background: <?php
                    if (!empty($h1_color)) {
                        echo esc_attr($h1_color);
                    } else {
                        echo esc_attr('#ECEFF1');
                    }
                    ?>" align="center">
                        <h1 style="background: <?php
                        if (!empty($h1_color)) {
                            echo esc_attr($h1_color);
                        } else {
                            echo esc_attr('#ECEFF1');
                        }
                        ?>; text-transform: uppercase; padding: 13px 15px;" align="center">
                            <?php _e('Net Pay ', 'wphrm'); ?>:<?php
                            if (isset($wphrm_currency)) : echo esc_html($wphrm_currency) . ' ' . esc_html(round($net_amount, $decimal));
                            endif;
                            ?>
                        </h1>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding: 10px 15px 15px;;" align="left">
                        <span style="font-weight: 700;"><?php _e('Information', 'wphrm'); ?>:</span> <?php
                        if (isset($wphrmEmployeeTextInfo['wphrm_information'])) : echo esc_html($wphrmEmployeeTextInfo['wphrm_information']);
                        endif;
                        ?>
                    </td>
                </tr>
                <tr>
                    <?php if ($footer_tag_align == 'left') { ?>
                        <td colspan="2" style="padding: 13px 15px;" align="left">
                            <a style="color: <?php
                            if (!empty($font_color)) {
                                echo esc_attr($font_color);
                            } else {
                                echo esc_attr('#546e7a');
                            }
                            ?>; text-decoration: none;" href="">
                               <?php
                               if (isset($footer_tag_contain)) : echo esc_attr($footer_tag_contain);
                               endif;
                               ?>
                            </a>
                        </td>
                        <td colspan="2" style="padding: 13px 15px;" align="right">
                            <?php _e('Generated on', 'wphrm'); ?>: <?php echo esc_html(date('l jS F Y')); ?>
                        </td>
                    <?php } else if ($footer_tag_align == 'right') { ?>
                        <td colspan="2" style="padding: 13px 15px;" align="left">
                            <?php _e('Generated on', 'wphrm'); ?> : <?php echo esc_html(date('l jS F Y')); ?>
                        </td>
                        <td colspan="2" style="padding: 13px 15px;" align="right">
                            <a style="color: <?php
                            if (!empty($font_color)) {
                                echo esc_attr($font_color);
                            } else {
                                echo esc_attr('#546e7a');
                            }
                            ?>; text-decoration: none;" href=""><?php
                               if (isset($footer_tag_contain)) : echo esc_html($footer_tag_contain);
                               endif;
                               ?></a>
                        </td>
                    <?php } else { ?>
                        <td colspan="2" style="padding: 13px 15px;" align="left">
                            <?php _e('Generated on', 'wphrm'); ?> : <?php echo esc_html(date('l jS F Y')); ?>
                        </td>
                        <td colspan="2" style="padding: 13px 15px;" align="right">
                            <a style="color: <?php
                            if (!empty($font_color)) {
                                echo esc_attr($font_color);
                            } else {
                                echo '#546e7a';
                            }
                            ?>; text-decoration: none;" href=""><?php
                               if (isset($footer_tag_contain)) : echo esc_html($footer_tag_contain);
                               endif;
                               ?></a>
                        </td> 
                    <?php } ?>
                </tr>
            </tbody>
        </table>
        <?php $data['html'] = ob_get_contents(); ob_clean(); return $data;
    }
    
    public function WPHRMGetSalarySlipWeekPDF($wphrmEmployeeID, $wphrmDate,$wphrmWeekNo) {
        ob_clean();
        global $current_user, $wpdb;
        $wphrmEmployeeID = esc_sql($wphrmEmployeeID); // esc
        $wphrmDate = esc_sql($wphrmDate); // esc
        $employeeSalaryNoteInformation = esc_sql('employeeSalaryNote'); // esc
        $wphrmEarningfiledskeyInformation = esc_sql('wphrmEarningfiledskey'); // esc
        $wphrmDeductionfiledskeyInformation = esc_sql('wphrmDeductionfiledskey'); // esc
        $wphrmEmployeeInfo = $this->wphrmMainClass->WPHRMGetUserDatas($wphrmEmployeeID, 'wphrmEmployeeInfo');
        $wphrmSalaryAccording = $this->wphrmMainClass->WPHRMGetSettings('wphrsalaryDayOrHourlyInfo');
        $wphrmEmployeeBankInfo = $this->wphrmMainClass->WPHRMGetUserDatas($wphrmEmployeeID, 'wphrmEmployeeBankInfo');
        $decimals = $this->wphrmMainClass->WPHRMGetDecimalSettings();
       if (isset($decimals['decimalvalue'])): 
        $decimal = $decimals['decimalvalue'];
        else :
        $decimal = 2;
         endif;
        $wphrmEmployeeTextInfo = $wpdb->get_row("SELECT * FROM  $this->WeeklySalaryTable  WHERE `employeeID` = $wphrmEmployeeID AND  `employeeKey` = '$employeeSalaryNoteInformation' AND `date` = '$wphrmDate' AND `weekOn`= '$wphrmWeekNo'");
       
        if (!empty($wphrmEmployeeTextInfo)) :
            $wphrmEmployeeTextInfo = unserialize(base64_decode($wphrmEmployeeTextInfo->employeeValue));

        endif;
        if (isset($wphrmEmployeeInfo['wphrm_employee_department']) && $wphrmEmployeeInfo['wphrm_employee_department'] != '') {
            $employeeDepartmentsLoads = $wphrmEmployeeInfo['wphrm_employee_department'];
            $wphrmDepartments = $wpdb->get_row("SELECT * FROM  $this->departmentTable  WHERE `departmentID` = '$employeeDepartmentsLoads'");
            if ($wphrmDepartments != '') {
                $wphrmDepartmentInfo = unserialize(base64_decode($wphrmDepartments->departmentName));
            }
        }

        if (isset($wphrmEmployeeInfo['wphrm_employee_designation']) && $wphrmEmployeeInfo['wphrm_employee_designation'] != '') {
            $employeeDesignationLoads = $wphrmEmployeeInfo['wphrm_employee_designation'];
            $wphrmDesignation = $wpdb->get_row("SELECT * FROM  $this->designationTable  WHERE `designationID` = '$employeeDesignationLoads'");

            if ($wphrmDesignation != '') {
                $wphrmDesignationInfo = unserialize(base64_decode($wphrmDesignation->designationName));
            }
        }
        $wphrmEarningSalary = $wpdb->get_row("SELECT * FROM  $this->WeeklySalaryTable  WHERE `employeeID` = $wphrmEmployeeID AND `date` ='$wphrmDate' AND `employeeKey` ='$wphrmEarningfiledskeyInformation' AND `weekOn`= '$wphrmWeekNo'");
        $wphrmDeductionSalary = $wpdb->get_row("SELECT * FROM  $this->WeeklySalaryTable  WHERE `employeeID` = $wphrmEmployeeID AND `date` ='$wphrmDate' AND `employeeKey` ='$wphrmDeductionfiledskeyInformation' AND `weekOn`= '$wphrmWeekNo'");
        
        $wphrmInfoText = $wpdb->get_row("SELECT * FROM  $this->WeeklySalaryTable  WHERE `employeeID` = $wphrmEmployeeID AND `date` ='$wphrmDate' AND `employeeKey` ='$employeeSalaryNoteInformation' AND `weekOn`= '$wphrmWeekNo'");
        $wphrmCompanyInfoSV = $this->wphrmMainClass->WPHRMGetSettings('wphrmGeneralSettingsInfo');
        $wphrmSalarySlipInfos = $this->wphrmMainClass->WPHRMGetSettings('wphrmSalarySlipInfo');
        $logo = '';
        if (isset($wphrmSalarySlipInfos['wphrm_logo_align']) && $wphrmSalarySlipInfos['wphrm_logo_align'] != '') {
            $logo = $wphrmSalarySlipInfos['wphrm_logo_align'];
        }
        $footer_tag_contain = '';
        if (isset($wphrmSalarySlipInfos['wphrm_slip_content']) && $wphrmSalarySlipInfos['wphrm_slip_content'] != '') {
            $footer_tag_contain = $wphrmSalarySlipInfos['wphrm_slip_content'];
        }
        $footer_tag_align = '';
        if (isset($wphrmSalarySlipInfos['wphrm_footer_content_align']) && $wphrmSalarySlipInfos['wphrm_footer_content_align'] != '') {
            $footer_tag_align = $wphrmSalarySlipInfos['wphrm_footer_content_align'];
        }
        $page_border_color = '';
        if (isset($wphrmSalarySlipInfos['wphrm_border_color']) && $wphrmSalarySlipInfos['wphrm_border_color'] != '') {
            $page_border_color = $wphrmSalarySlipInfos['wphrm_border_color'];
        }
        $h1_color = '';
        if (isset($wphrmSalarySlipInfos['wphrm_background_color']) && $wphrmSalarySlipInfos['wphrm_background_color'] != '') {
            $h1_color = $wphrmSalarySlipInfos['wphrm_background_color'];
        }
        $font_color = '';
        if (isset($wphrmSalarySlipInfos['wphrm_font_color']) && $wphrmSalarySlipInfos['wphrm_font_color'] != '') {
            $font_color = $wphrmSalarySlipInfos['wphrm_font_color'];
        }
        $wphrm_company_logo = '';
        if (isset($wphrmCompanyInfoSV['wphrm_company_logo']) && $wphrmCompanyInfoSV['wphrm_company_logo'] != '') {
            $wphrm_company_logo = $wphrmCompanyInfoSV['wphrm_company_logo'];
        }
        $wphrm_company_phone = '';
        if (isset($wphrmCompanyInfoSV['wphrm_company_phone']) && $wphrmCompanyInfoSV['wphrm_company_phone'] != '') {
            $wphrm_company_phone = $wphrmCompanyInfoSV['wphrm_company_phone'];
        }
        $wphrm_company_address = '';
        if (isset($wphrmCompanyInfoSV['wphrm_company_address']) && $wphrmCompanyInfoSV['wphrm_company_address'] != '') {
            $wphrm_company_address = $wphrmCompanyInfoSV['wphrm_company_address'];
        }
        $wphrm_company_email = '';
        if (isset($wphrmCompanyInfoSV['wphrm_company_email']) && $wphrmCompanyInfoSV['wphrm_company_email'] != '') {
            $wphrm_company_email = $wphrmCompanyInfoSV['wphrm_company_email'];
        }
        $wphrm_currency = '';
        $wphrm_currencys = '';
        if (isset($wphrmCompanyInfoSV['wphrm_currency']) && $wphrmCompanyInfoSV['wphrm_currency'] != '') {
            $wphrm_currencys = $wphrmCompanyInfoSV['wphrm_currency'];
            $selected_currency = explode('-', $wphrm_currencys);
            $wphrm_currency = $selected_currency[0];
        }
        $wphrmEmployeeSalaryInfo = $this->wphrmMainClass->WPHRMGetUserDatas($wphrmEmployeeID, 'wphrmEmployeeSalaryInfo');
        ob_clean();
        ?>
        <style>
            body {
                font-family: "<?php
                if (isset($font_family)): echo esc_html($font_family);
                endif;
                ?>";
                font-size: 11px; color: <?php
                if (!empty($font_color)) {
                    echo esc_html($font_color);
                } else {
                    echo esc_html('#546e7a');
                }
                ?>; 
                line-height: 25px;
                letter-spacing: 0.5px;
            }
            * { margin: 0;padding: 0; }
            h1 { font-size: 18px;font-weight: 700; }
            h2 { font-size: 14px;font-weight: 700; }
            .space { padding: 0 15px; }
        </style>
        <?php $data['style'] = ob_get_contents(); ob_clean(); ?>
        <link href='https://fonts.googleapis.com/css?family=Noto+Sans:400,700' rel='stylesheet' type='text/css'>
        <table style="width: 100%; border-collapse: collapse; "  align="center" >
            <tbody>
                <tr>
                    <?php if ($logo == 'left') { ?>
                        <td colspan="2" style="padding: 20px 0px;">
                            <img src="<?php
                            if (!empty($wphrm_company_logo)) {
                                echo esc_attr($wphrm_company_logo);
                            } else {
                                echo esc_attr(plugins_url('assets/images/Logo_3.png'));
                            }
                            ?>" height="100" width="auto">
                        </td>
                        <td colspan="2" style="padding: 20px 0px;"  align="right">
                            <?php
                            $wphrm_company = explode(",", $wphrm_company_address);
                            foreach ($wphrm_company as $key => $wphrm_company_data) {
                                if ($key == 3) {
                                    echo esc_html($wphrm_company_data) . ',<br>';
                                } else {
                                    echo esc_html($wphrm_company_data) . ',';
                                }
                            }
                            ?>
                            <br><?php _e('Phone', 'wphrm'); ?>:
                            <?php
                            if (isset($wphrm_company_phone)): echo esc_html($wphrm_company_phone);
                            endif;
                            ?>
                            <br> <?php _e('Email', 'wphrm'); ?>: <?php
                            if (isset($wphrm_company_email)): echo esc_html($wphrm_company_email);
                            endif;
                            ?>
                        </td>
                    <?php } else if ($logo == 'center') { ?>
                        <td colspan="2" style="padding: 20px 0px; text-align: right;">
                            <img src="<?php
                            if (isset($wphrm_company_logo)): echo esc_attr($wphrm_company_logo);
                            endif;
                            ?>" height="100" width="auto">
                        </td>
                        <td colspan="2" style="padding: 20px 0px;"  align="right">
                            <?php
                            $wphrm_company = explode(",", $wphrm_company_address);
                            foreach ($wphrm_company as $key => $wphrm_company_data) {
                                if ($key == 3) {
                                    echo esc_html($wphrm_company_data) . ',<br>';
                                } else {
                                    echo esc_html($wphrm_company_data) . ',';
                                }
                            }
                            ?>
                            <br><?php _e('Phone', 'wphrm'); ?>: <?php
                            if (isset($wphrm_company_phone)): echo esc_html($wphrm_company_phone);
                            endif;
                            ?>
                            <br> <?php _e('Email', 'wphrm'); ?>: <?php
                            if (isset($wphrm_company_email)): echo esc_html($wphrm_company_email);
                            endif;
                            ?>
                        </td>
                    <?php } else if ($logo == 'right') { ?>
                        <td colspan="2" style="padding: 20px 0px;"  >
                            <?php
                            $wphrm_company = explode(",", $wphrm_company_address);
                            foreach ($wphrm_company as $key => $wphrm_company_data) {
                                if ($key == 3) {
                                    echo esc_html($wphrm_company_data) . ',<br>';
                                } else {
                                    echo esc_html($wphrm_company_data) . ',';
                                }
                            }
                            ?>
                            <br><?php _e('Phone', 'wphrm'); ?>: <?php
                            if (isset($wphrm_company_phone)): echo esc_html($wphrm_company_phone);
                            endif;
                            ?>
                            <br> <?php _e('Email', 'wphrm'); ?>: <?php
                            if (isset($wphrm_company_email)): echo esc_html($wphrm_company_email);
                            endif;
                            ?>
                        </td>
                        <td colspan="2" style="padding: 20px 0px;" align="right"><img src="<?php echo esc_attr($wphrm_company_logo) ?>" height="100" width="260"></td>
                    <?php } else { ?> 
                        <td colspan="2" style="padding: 20px 0px;"><img src="<?php echo esc_attr($wphrm_company_logo); ?>" height="100" width="260"></td>
                        <td colspan="2" style="padding: 20px 0px;"  align="right">
                            <?php
                            $wphrm_company = explode(",", $wphrm_company_address);
                            foreach ($wphrm_company as $key => $wphrm_company_data) {
                                if ($key == 3) {
                                    echo esc_html($wphrm_company_data) . ',<br>';
                                } else {
                                    echo esc_html($wphrm_company_data) . ',';
                                }
                            }
                            ?>
                            <br><?php _e('Phone', 'wphrm'); ?>: <?php
                            if (isset($wphrm_company_phone)): echo esc_html($wphrm_company_phone);
                            endif;
                            ?>
                            <br> <?php _e('Email', 'wphrm'); ?>: <?php
                            if (isset($wphrm_company_email)): echo esc_html($wphrm_company_email);
                            endif;
                            ?>
                        </td>
                    <?php } ?>
                </tr>
                <tr>
                    <td colspan="4" style="padding: 40px 0px;border-top: 1px solid <?php
                    if (!empty($page_color)) {
                        echo esc_attr($page_color);
                    } else {
                        echo esc_attr('#cfd8dc');
                    }
                    ?>" align="center">
                        <h1 style="text-transform: uppercase; border-bottom: 1px solid; display: inline-block;" align="center">
                            <?php $currentCount = $this->wphrmMainClass->WPHRMWeekCount($wphrmWeekNo);
                            _e('Create Salary Slip '.$currentCount.' of ', 'wphrm'); ?><?php
                            if (isset($wphrmDate)): echo esc_html(date(' F Y', strtotime($wphrmDate)));
                            endif;
                            ?> 
                        </h1>
                    </td>
                </tr>
                <tr>  
                    <td colspan="2" style="vertical-align: top; width: 50%; padding-bottom: 20px;">
                        <table style="border-collapse: collapse; width:97%;" >
                            <tbody>
                                <tr>
                                    <td><?php _e('Name', 'wphrm'); ?><span style="float: right; width: 30px;"> :</span></td>
                                    <td style="float: right; width: 100px;"><?php
                                        if (isset($wphrmEmployeeInfo['wphrm_employee_fname'])) : echo esc_html($wphrmEmployeeInfo['wphrm_employee_fname']);
                                        endif;
                                        if (isset($wphrmEmployeeInfo['wphrm_employee_lname'])) : echo ' ' . esc_html($wphrmEmployeeInfo['wphrm_employee_lname']);
                                        endif;
                                        ?></td>
                                </tr>
                                <tr>
                                    <td><?php _e('Designation', 'wphrm'); ?><span style="float: right; width: 30px;" > :</span></td>
                                    <td style="float: right; width: 100px;"><?php
                                        if (isset($wphrmDesignationInfo['designationName'])) : echo esc_html($wphrmDesignationInfo['designationName']);
                                        endif;
                                        ?></td>
                                </tr>
                                <tr>
                                    <td><?php _e('Department', 'wphrm'); ?><span style="float: right; width: 30px;"> :</span></td>
                                    <td><?php
                                        if (isset($wphrmDepartmentInfo['departmentName'])) : echo esc_html($wphrmDepartmentInfo['departmentName']);
                                        endif;
                                        ?></td>
                                </tr>
                                <tr>
                                    <td><?php _e('Transferred to', 'wphrm'); ?><span style="float: right; width: 30px;"> :</span></td>
                                    <td>
                                        <?php
                                        if (isset($wphrmEmployeeTextInfo['wphrm_account_no'])) : echo esc_html($wphrmEmployeeTextInfo['wphrm_account_no']);
                                        endif;
                                        ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td colspan="2" style="vertical-align: top; width: 50%; padding-bottom: 20px;">
                        <table style="border-collapse: collapse; width: 97%;" >
                            <tbody>
                                <tr>
                                    <td><?php _e('Employee ID', 'wphrm'); ?><span style="float: right; width: 30px;"> :</span></td>
                                    <td><?php
                                        if (isset($wphrmEmployeeInfo['wphrm_employee_uniqueid'])) : echo esc_html($wphrmEmployeeInfo['wphrm_employee_uniqueid']);
                                        endif;
                                        ?></td>
                                </tr>
                                
                                <?php if (isset($wphrmSalaryAccording['wphrm-according']) && ($wphrmSalaryAccording['wphrm-according'] == 'Hourly')) { ?>
                                    <tr>
                                        <td><?php _e('Total Hours Worked', 'wphrm'); ?><span style="float: right; width: 30px;"> :</span></td>
                                        <td><?php
                                            if (isset($wphrmEmployeeTextInfo['wphrm_Hours'])) : echo esc_html($wphrmEmployeeTextInfo['wphrm_Hours']);
                                            endif;
                                            ?></td>
                                    </tr> 
                                <?php } else { ?>
                                    <tr>
                                        <td><?php _e('Total  Working Days', 'wphrm'); ?><span style="float: right; width: 30px;"> :</span></td>
                                        <td><?php
                                            if (isset($wphrmEmployeeTextInfo['wphrm_dayofworking'])) : echo esc_html($wphrmEmployeeTextInfo['wphrm_dayofworking']);
                                            endif;
                                            ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php _e('Leave Days', 'wphrm'); ?><span style="float: right; width: 30px;"> :</span></td>
                                        <td><?php
                                            if (isset($wphrmEmployeeTextInfo['wphrm_workleave'])) : echo esc_html($wphrmEmployeeTextInfo['wphrm_workleave']);
                                            endif;
                                            ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php _e('Total Days Worked', 'wphrm'); ?><span style="float: right; width: 30px;"> :</span></td>
                                        <td><?php
                                            if (isset($wphrmEmployeeTextInfo['wphrm_dayofworked'])) : echo esc_html($wphrmEmployeeTextInfo['wphrm_dayofworked']);
                                            endif;
                                            ?></td>
                                    </tr>



                                <?php } ?>
                                
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="vertical-align: top; width: 50%; padding-bottom: 20px;">
                        <table style="border-collapse: collapse; width: 97%;">
                            <tbody>
                                <tr>
                                    <td  style="padding: 8px 15px;margin: 40px 0px 0px; background: <?php
                                    if (!empty($h1_color)) {
                                        echo esc_attr($h1_color);
                                    } else {
                                        echo esc_attr('#ECEFF1');
                                    }
                                    ?>" align="left">
                                        <h2 style="background: <?php
                                        if (!empty($h1_color)) {
                                            echo esc_attr($h1_color);
                                        } else {
                                            echo esc_attr('#ECEFF1');
                                        }
                                        ?>!important;"><?php _e('Earnings', 'wphrm'); ?></h2>
                                    </td>
                                    <td  style="padding: 8px 15px;margin: 40px 0px 0px; background: <?php
                                    if (!empty($h1_color)) {
                                        echo esc_attr($h1_color);
                                    } else {
                                        echo esc_attr('#ECEFF1');
                                    }
                                    ?>" align="right">
                                        <h2 style="background: <?php
                                        if (!empty($h1_color)) {
                                            echo esc_attr($h1_color);
                                        } else {
                                            echo esc_attr('#ECEFF1');
                                        }
                                        ?> !important;"><?php _e('Amount', 'wphrm'); ?></h2>
                                    </td>
                                </tr>
                                <?php
                                if (!empty($wphrmEarningSalary)) {
                                    $wphrmEarningInfo = unserialize(base64_decode($wphrmEarningSalary->employeeValue));
                                    foreach ($wphrmEarningInfo['wphrmEarningLebal'] as $earningkey1 => $wphrmEarninglebal) {
                                        foreach ($wphrmEarningInfo['wphrmEarningValue'] as $earningkey => $wphrmEarningvalue) {
                                            if ($earningkey1 == $earningkey) {
                                                $wphrmEarningtotal = $wphrmEarningvalue + $i;
                                                $i = $wphrmEarningtotal;
                                                ?>
                                                <tr>
                                                    <td class="space" style="padding: 4px 15px;" ><?php
                                                        if (isset($wphrmEarninglebal)): echo esc_html($wphrmEarninglebal);
                                                        endif;
                                                        ?>
                                                    </td>
                                                    <td class="space" style="padding: 4px 15px;"  align="right"><?php
                                                        if (isset($wphrmEarningvalue)): echo esc_html(round($wphrmEarningvalue, $decimal));
                                                        endif;
                                                        ?>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </td>
                    <td colspan="2" style="vertical-align: top; width: 50%; padding-bottom: 20px;">
                        <table style="border-collapse: collapse; width: 97%;">
                            <tbody>
                                <tr>
                                    <td  style="padding: 8px 15px;margin: 40px 0px 0px; background: <?php
                                    if (!empty($h1_color)) {
                                        echo esc_attr($h1_color);
                                    } else {
                                        echo esc_attr('#ECEFF1');
                                    }
                                    ?>" align="left">
                                        <h2 style="background: <?php
                                        if (!empty($h1_color)) {
                                            echo esc_attr($h1_color);
                                        } else {
                                            echo esc_attr('#ECEFF1');
                                        }
                                        ?>!important;"><?php _e('Deductions', 'wphrm'); ?></h2>
                                    </td>
                                    <td  style="padding: 8px 15px;margin: 40px 0px 0px; background: <?php
                                    if (!empty($h1_color)) {
                                        echo esc_attr($h1_color);
                                    } else {
                                        echo esc_attr('#ECEFF1');
                                    }
                                    ?>" align="right">
                                        <h2 style="background: <?php
                                        if (!empty($h1_color)) {
                                            echo esc_attr($h1_color);
                                        } else {
                                            echo esc_attr('#ECEFF1');
                                        }
                                        ?>!important;"><?php _e('Amount', 'wphrm'); ?></h2>
                                    </td>
                                </tr>
                                <?php
                                $i = 0;
                                if (!empty($wphrmDeductionSalary)) {
                                    $wphrmDeductionInfo = unserialize(base64_decode($wphrmDeductionSalary->employeeValue));
                                    foreach ($wphrmDeductionInfo['wphrmDeductionLebal'] as $earningkey1 => $wphrmDeductionlebal) {
                                        foreach ($wphrmDeductionInfo['wphrmDeductionValue'] as $earningkey => $wphrmDeductionvalue) {
                                            if ($earningkey1 == $earningkey) {
                                                $wphrmDeductiontotal = $wphrmDeductionvalue + $i;
                                                $i = $wphrmDeductiontotal;
                                                ?>
                                                <tr>
                                                    <td class="space" style="padding: 4px 15px;" align="left"><?php
                                                        if (isset($wphrmDeductionlebal)): echo esc_html($wphrmDeductionlebal);
                                                        endif;
                                                        ?></td>
                                                    <td class="space" style="padding: 4px 15px;" align="right">
                                                        <?php
                                                        if (isset($wphrmDeductionvalue)): echo esc_html(round($wphrmDeductionvalue, $decimal));
                                                        endif;
                                                        ?>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="vertical-align: top; width: 50%; padding-bottom: 20px;">
                        <table style="border-collapse: collapse; width: 97%; border-top: 2px solid <?php
                        if (!empty($page_color)) {
                            echo esc_attr($page_color);
                        } else {
                            echo esc_attr('#cfd8dc');
                        }
                        ?>" >
                            <tbody>
                                <tr>
                                    <td style="padding-top: 8px;">
                                        <h2 style="padding: 8px 15px 15px;"><?php _e('Total Earnings', 'wphrm'); ?></h2>
                                    </td>
                                    <td align="right" style="padding-top: 8px;">
                                        <h2 style="padding: 8px 15px 15px;">
                                            <?php
                                            if (isset($wphrm_currency)): echo esc_html($wphrm_currency) . ' ' . esc_html(round($wphrmEarningtotal, $decimal));
                                            endif;
                                            ?>
                                        </h2>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td colspan="2" style="vertical-align: top; width: 50%; padding-bottom: 20px;" >
                        <table style="border-collapse: collapse; width: 97%; border-top: 2px solid <?php
                        if (!empty($page_color)) {
                            echo esc_attr($page_color);
                        } else {
                            echo esc_attr('#cfd8dc');
                        }
                        ?>" >
                            <tbody>
                                <tr>
                                    <td style="padding-top: 8px;" align="left">
                                        <h2 style="padding: 8px 15px;"><?php _e('Total Deductions', 'wphrm'); ?></h2>
                                    </td >
                                    <td align="right" style="padding-top: 8px;">
                                        <h2 style="padding: 8px 15px;">
                                            <?php
                                            if (isset($wphrm_currency)) : echo esc_html($wphrm_currency) . ' ' . esc_html(round($wphrmDeductiontotal, $decimal));
                                            endif;
                                            ?>
                                        </h2>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr><?php $net_amount = $wphrmEarningtotal - $wphrmDeductiontotal; ?>
                    <td colspan="4" style="padding: 8px 15px;background: <?php
                    if (!empty($h1_color)) {
                        echo esc_attr($h1_color);
                    } else {
                        echo esc_attr('#ECEFF1');
                    }
                    ?>" align="center">
                        <h1 style="background: <?php
                        if (!empty($h1_color)) {
                            echo esc_attr($h1_color);
                        } else {
                            echo esc_attr('#ECEFF1');
                        }
                        ?>; text-transform: uppercase; padding: 13px 15px;" align="center">
                            <?php _e('Net Pay ', 'wphrm'); ?>:<?php
                            if (isset($wphrm_currency)) : echo esc_html($wphrm_currency) . ' ' . esc_html(round($net_amount, $decimal));
                            endif;
                            ?>
                        </h1>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding: 10px 15px 15px;;" align="left">
                        <span style="font-weight: 700;"><?php _e('Information', 'wphrm'); ?>:</span> <?php
                        if (isset($wphrmEmployeeTextInfo['wphrm_information'])) : echo esc_html($wphrmEmployeeTextInfo['wphrm_information']);
                        endif;
                        ?>
                    </td>
                </tr>
                <tr>
                    <?php if ($footer_tag_align == 'left') { ?>
                        <td colspan="2" style="padding: 13px 15px;" align="left">
                            <a style="color: <?php
                            if (!empty($font_color)) {
                                echo esc_attr($font_color);
                            } else {
                                echo esc_attr('#546e7a');
                            }
                            ?>; text-decoration: none;" href="">
                               <?php
                               if (isset($footer_tag_contain)) : echo esc_attr($footer_tag_contain);
                               endif;
                               ?>
                            </a>
                        </td>
                        <td colspan="2" style="padding: 13px 15px;" align="right">
                            <?php _e('Generated on', 'wphrm'); ?>: <?php echo esc_html(date('l jS F Y')); ?>
                        </td>
                    <?php } else if ($footer_tag_align == 'right') { ?>
                        <td colspan="2" style="padding: 13px 15px;" align="left">
                            <?php _e('Generated on', 'wphrm'); ?> : <?php echo esc_html(date('l jS F Y')); ?>
                        </td>
                        <td colspan="2" style="padding: 13px 15px;" align="right">
                            <a style="color: <?php
                            if (!empty($font_color)) {
                                echo esc_attr($font_color);
                            } else {
                                echo esc_attr('#546e7a');
                            }
                            ?>; text-decoration: none;" href=""><?php
                               if (isset($footer_tag_contain)) : echo esc_html($footer_tag_contain);
                               endif;
                               ?></a>
                        </td>
                    <?php } else { ?>
                        <td colspan="2" style="padding: 13px 15px;" align="left">
                            <?php _e('Generated on', 'wphrm'); ?> : <?php echo esc_html(date('l jS F Y')); ?>
                        </td>
                        <td colspan="2" style="padding: 13px 15px;" align="right">
                            <a style="color: <?php
                            if (!empty($font_color)) {
                                echo esc_attr($font_color);
                            } else {
                                echo '#546e7a';
                            }
                            ?>; text-decoration: none;" href=""><?php
                               if (isset($footer_tag_contain)) : echo esc_html($footer_tag_contain);
                               endif;
                               ?></a>
                        </td> 
                    <?php } ?>
                </tr>
            </tbody>
        </table>
        <?php $data['html'] = ob_get_contents(); ob_clean(); return $data;
    }

    public function WPHRMGetProfitLoassReportDatas($wphrmFromDate, $wphrmToDate, $wphrmType) {
        global $wpdb;
        $wphrmGeneralSettingsInfo = $this->wphrmMainClass->WPHRMGetSettings('wphrmGeneralSettingsInfo');
        $wphrmDateStart = '';
        if ($wphrmFromDate != '') {
            $wphrmDateStart = date('Y-m-d', strtotime($wphrmFromDate));
        }
        $wphrmDateEnd = '';
        if ($wphrmToDate != '') {
            $wphrmDateEnd = date('Y-m-d', strtotime($wphrmToDate));
        }
        $wphrmMainSearch = '';
        if ($wphrmType == 'cash-in') {
            $wphrmMainSearch = 'Profit';
        } else if ($wphrmType == 'cash-out') {
            $wphrmMainSearch = 'Loss';
        }
        $datebetween = '';
        if (($wphrmDateEnd != '') && ($wphrmMainSearch != '')) {
            $datebetween = "where  (`wphrmDate` BETWEEN '$wphrmDateStart' AND '$wphrmDateEnd') and `wphrmStatus` = '$wphrmMainSearch'";
        }
        if (($wphrmDateEnd != '') && ($wphrmMainSearch == '')) {
            $datebetween = "where  (`wphrmDate` BETWEEN '$wphrmDateStart' AND '$wphrmDateEnd')";
        }
        if (($wphrmDateEnd == '') && ($wphrmMainSearch != '')) {
            $datebetween = "where  `wphrmStatus` = '$wphrmMainSearch'";
        }
        if (($wphrmDateEnd == '') && ($wphrmMainSearch == '')) {
            $datebetween = "";
        }
        //$datebetween = esc_sql($datebetween); // esc
        $datas['wphrm-finacials'] = $wpdb->get_results("SELECT * FROM  $this->financialsTable $datebetween");
        $datas['wphrm-num-row'] = $wpdb->num_rows;
        return $datas;
    }

    public function WPHRMGetProfitLossReportExcel($wphrmFromDate, $wphrmToDate, $wphrmType) {
        $wphrmFinacials = $this->WPHRMGetProfitLoassReportDatas($wphrmFromDate, $wphrmToDate, $wphrmType);
        $i = 1;
        ob_clean();
        $filename = 'profitLossReports-' . date('dmYHis');
        $numcolumn = $wphrmFinacials['wphrm-num-row'];
        header('Content-Disposition: attachment; filename=' . $filename . '.xls');
        header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
        header('Pragma: no-cache');
        header('Content-Type: application/x-msexcel; charset=windows-1251; format=attachment;');
        ?>
        <table>
            <tr>
                <td><?php _e('Serial No', 'wphrm'); ?></td>
                <td><?php _e('Item Name', 'wphrm'); ?></td>
                <td><?php _e('Amount', 'wphrm'); ?></td>
                <td><?php _e('Type', 'wphrm'); ?></td>
                <td><?php _e('Date', 'wphrm'); ?></td>            
            </tr>
            <?php foreach ($wphrmFinacials['wphrm-finacials'] as $data) { ?>
                <tr>
                    <td><?php echo esc_html($i); ?></td>
                    <td><?php echo esc_html($data->wphrmItem); ?></td>
                    <td><?php echo esc_html($data->wphrmAmounts); ?></td>
                    <?php if ($data->wphrmStatus == 'Profit') { ?>
                        <td><?php _e('Cash In', 'wphrm'); ?></td>
                    <?php } else { ?>
                        <td><?php _e('Cash Out', 'wphrm'); ?></td>
                    <?php } ?>
                    <td><?php echo esc_html(date('d-m-Y', strtotime($data->wphrmDate))); ?></td>
                </tr>
                <?php
                $i++;
            }
            ?>
        </table><?php
        exit;
    }

    public function WPHRMGetProfitLossDashboardReportExcel($wphrmFromDate, $wphrmToDate, $wphrmType) {
        $wphrmFinacials = $this->WPHRMGetProfitLoassReportDatas($wphrmFromDate, $wphrmToDate, $wphrmType);


        $filename = 'profitLossReports-' . date('dmYHis');
        ob_clean();
        $i = 1;
        $numcolumn = $wphrmFinacials['wphrm-num-row'];
        header('Content-Disposition: attachment; filename=' . $filename . '.xls');
        header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
        header('Pragma: no-cache');
        header('Content-Type: application/x-msexcel; charset=windows-1251; format=attachment;');
        ?>
        <table>
            <tr>
                <td><?php _e('Serial No', 'wphrm'); ?></td>
                <td><?php _e('Item Name', 'wphrm'); ?></td>
                <td><?php _e('Amount', 'wphrm'); ?></td>
                <td><?php _e('Cash In', 'wphrm'); ?></td>
                <td><?php _e('Cash Out', 'wphrm'); ?></td>
                <td><?php _e('Date', 'wphrm'); ?></td>
            </tr><?php
            $profit = '';
            $loss = '';
            $wphrmCashIn = '';
            $wphrmCashOut = '';
            $finalTotal = '';
            foreach ($wphrmFinacials['wphrm-finacials'] as $data) {
                if ($data->wphrmStatus == 'Profit') {
                    $wphrmCashIn = $data->wphrmAmounts + $profit;
                    $profit = $wphrmCashIn;
                } else {
                    $wphrmCashOut = $data->wphrmAmounts + $loss;
                    $loss = $wphrmCashOut;
                }
                ?>
                <tr>
                    <td><?php echo esc_html($i); ?></td>
                    <td><?php echo esc_html($data->wphrmItem); ?></td>
                    <td><?php echo esc_html($data->wphrmAmounts); ?></td>
                    <?php if ($data->wphrmStatus == 'Profit') { ?>
                        <td><?php echo esc_html($data->wphrmAmounts); ?></td>
                        <td></td>
                    <?php } else { ?>
                        <td></td>
                        <td><?php echo esc_html($data->wphrmAmounts); ?></td>
                    <?php } ?>
                    <td><?php echo esc_html(date('d-m-Y', strtotime($data->wphrmDate))); ?></td>
                </tr>
                <?php
                $i++;
            }
            $finalTotal = $wphrmCashIn - $wphrmCashOut;
            ?>
            <tr><td></td></tr>
            <tr>
                <td></td>
                <td></td>
                <td><?php _e('Total CashIn', 'wphrm'); ?></td>
                <td style='color:green'><?php echo esc_html($wphrmCashIn); ?></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td><?php _e('Total CashOut', 'wphrm'); ?></td>
                <td style='color:red'><?php echo esc_html($wphrmCashOut); ?></td>
            </tr>
            <tr></tr>
            <tr>
                <td></td>
                <td></td>
                <td><?php _e('Final Total', 'wphrm'); ?></td>
                <?php if ($wphrmCashIn >= $wphrmCashOut) { ?>
                    <td style='color:green'><?php echo esc_html($finalTotal); ?></td><td><?php _e('Profit', 'wphrm'); ?></td>
                <?php } else { ?>
                    <td style='color:green'><?php echo esc_html($finalTotal); ?></td><td><?php _e('Loss', 'wphrm'); ?></td>
                <?php } ?>
            </tr>
        </table><?php
        exit;
    }

    public function WPHRMGetProfitLossReportPdf($wphrmFromDate, $wphrmToDate, $wphrmType) {
        $wphrmFinacials = $this->WPHRMGetProfitLoassReportDatas($wphrmFromDate, $wphrmToDate, $wphrmType);
        ?>
        <html><head>
                <style>
                    body {
                        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 62.5%; color: #585858; padding: 22px 10px; padding-bottom: 55px;
                    }
                    #keywords {
                        margin: 0 auto; font-size: 1.2em; margin-bottom: 15px; width: 100%; margin-top: 75px;
                    }
                    #keywords thead tr th {
                        border: 1px solid #ddd; padding: 12px 30px; padding-left: 42px;
                    }
                    #keywords thead tr th span { padding-right: 20px; }
                    #keywords tbody tr td {
                        text-align: center; padding: 15px 10px; border: 1px solid #ddd;
                    }
                    .logo { display: inline-block; float: left; }
                    .main-header { text-align: center; font-size: 25px; margin-top: 50px; text-decoration: underline; color: #546E7A; text-transform: capitalize; font-weight: 400; margin-right: 10%; letter-spacing: 0.5px; }
                    .bottom-line { border-bottom: 2px solid #ddd; width: 97%; padding-top: 50px; position: absolute; }
                </style>
            </head>
            <body>
                <div class="main-header"><?php _e('Profit and loss reports', 'wphrm'); ?> - <?php echo esc_html(date('d-m-Y')); ?></div>
                <table id="keywords" cellspacing="0" cellpadding="0">
                    <thead>
                        <tr>
                            <th><span><?php _e('Item Name', 'wphrm'); ?></span></th>
                            <th><span><?php _e('Amount', 'wphrm'); ?></span></th>
                            <th><span><?php _e('Date', 'wphrm'); ?></span></th>
                            <th><span><?php _e('Financial Type', 'wphrm'); ?></span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($wphrmFinacials['wphrm-finacials'] as $key => $wphrmFinacial) { ?>
                            <tr>
                                <td class="lalign"><?php
                                    if (isset($wphrmFinacial->wphrmItem)) : echo esc_html($wphrmFinacial->wphrmItem);
                                    endif;
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if (isset($wphrmFinacial->wphrmAmounts)) : echo esc_html($wphrmFinacial->wphrmAmounts);
                                    endif;
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if (isset($wphrmFinacial->wphrmDate)) : echo esc_html(date('d-m-Y', strtotime($wphrmFinacial->wphrmDate)));
                                    endif;
                                    ?>
                                </td>
                                <td>
                                    <?php if (isset($wphrmFinacial->wphrmStatus) && $wphrmFinacial->wphrmStatus == 'Profit') { ?>
                                        <?php _e('Cash In', 'wphrm'); ?>
                                    <?php } else { ?>
                                        <?php _e('Cash Out', 'wphrm'); ?>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </body></html>
        <?php
    }

    public function WPHRMGetTotalSalaryPdf($wphrmFromDate, $wphrmToDate, $employeeId) {
        $wphrm_info = $this->wphrmMainClass->WPHRMGetUserDatas($employeeId, 'wphrmEmployeeInfo');
        $wphrmDateStart = date('Y-m-d', strtotime($wphrmFromDate . '-01'));
        $wphrmDateEnd = date('Y-m-d', strtotime($wphrmToDate . '-01'));
        global $current_user, $wpdb;
        ?>
        <html><head>
                <style>
                    body {
                        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 62.5%; color: #585858; padding: 22px 10px; padding-bottom: 55px;
                    }
                    #keywords {
                        margin: 0 auto; font-size: 1.2em; margin-bottom: 15px; width: 100%; margin-top: 75px;
                    }
                    #keywords thead tr th {
                        border: 1px solid #ddd; padding: 12px 30px; padding-left: 42px;
                    }
                    #keywords thead tr th span { padding-right: 20px; }
                    #keywords tbody tr td {
                        text-align: center; padding: 15px 10px; border: 1px solid #ddd;
                    }
                    .logo { display: inline-block; float: left; }
                    .main-header { text-align: center; font-size: 18px; margin-top: 50px; text-decoration: underline; color: #546E7A; text-transform: capitalize; font-weight: 400; margin-right: 10%; letter-spacing: 0.5px; }
                    .bottom-line { border-bottom: 2px solid #ddd; width: 97%; padding-top: 50px; position: absolute; }
                </style>
            </head>
            <body>
                <div class="main-header"><?php
                    echo esc_html($wphrm_info['wphrm_employee_fname']) . ' ' . esc_html($wphrm_info['wphrm_employee_lname']) . ' ';
                    _e('Total Salary Report', 'wphrm');
                    echo ' - ';
                    _e('From', 'wphrm');
                    echo ' : ' . esc_html($wphrmFromDate);
                    ?>
                    <?php
                    _e('To', 'wphrm');
                    echo ' : ' . esc_html($wphrmToDate);
                    ?></div>
                <table id="keywords" cellspacing="0" cellpadding="0">
                    <thead>
                        <tr>

                            <th><span><?php _e('S.No', 'wphrm'); ?></span></th>
                            <th><span><?php _e('Month', 'wphrm'); ?></span></th>
                            <th><span><?php _e('Earning', 'wphrm'); ?></span></th>
                            <th><span><?php _e('Deduction', 'wphrm'); ?></span></th>
                            <th><span><?php _e('Total Paid', 'wphrm'); ?></span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $j = 1;
                        $salaryEmployeeCounter = 0;
                        $wphrmEmployeeSalaryGenerated = esc_sql('employeeSalaryGenerated'); // esc
                        $wphrmDeductionFiledsKey = esc_sql('wphrmDeductionfiledskey'); // esc
                        $wphrmEarningFiledsKey = esc_sql('wphrmEarningfiledskey'); // esc
                        $wphrmSalaryGenerateds = $wpdb->get_results("SELECT * FROM $this->salaryTable WHERE (`date` BETWEEN '$wphrmDateStart' AND '$wphrmDateEnd') AND `employeeID`='$employeeId' AND `employeeKey`='$wphrmEmployeeSalaryGenerated' ");
                        $finaltotal = 0;
                        $finaltotalGet = 0;

                        foreach ($wphrmSalaryGenerateds as $wphrmSalaryGenerated) {
                            $earningSalaryTotal = 0;
                            $deductionSalaryTotal = 0;

                            $wphrmEarningFiledsKeyInfo = $wpdb->get_row("SELECT * FROM $this->salaryTable WHERE  `employeeID`='$employeeId' AND  `employeeKey`='$wphrmEarningFiledsKey' AND `date`='$wphrmSalaryGenerated->date'");
                            $wphrmEarningInfo = unserialize(base64_decode($wphrmEarningFiledsKeyInfo->employeeValue));
                            foreach ($wphrmEarningInfo['wphrmEarningValue'] as $wphrmEarningInfos) {
                                $wphrmEarningtotal = $wphrmEarningInfos + $earningSalaryTotal;
                                $earningSalaryTotal = $wphrmEarningtotal;
                            }
                            $wphrmDeductionFiledsKeyInfo = $wpdb->get_row("SELECT * FROM $this->salaryTable WHERE  `employeeID`='$employeeId' AND  `employeeKey`='$wphrmDeductionFiledsKey' AND `date`='$wphrmSalaryGenerated->date'");
                            $wphrmDeductionInfo = unserialize(base64_decode($wphrmDeductionFiledsKeyInfo->employeeValue));
                            foreach ($wphrmDeductionInfo['wphrmDeductionValue'] as $wphrmDeductionInfos) {
                                $wphrmDeductiontotal = $wphrmDeductionInfos + $deductionSalaryTotal;
                                $deductionSalaryTotal = $wphrmDeductiontotal;
                            }
                            $wphrmSalaryGeneratedDate = date('F Y', strtotime($wphrmSalaryGenerated->date));
                            $paidSalaryTotal = $earningSalaryTotal - $deductionSalaryTotal;

                            $finaltotal = $paidSalaryTotal + $finaltotalGet;
                            $finaltotalGet = $finaltotal;
                            ?>
                            <tr>
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
                            $j++;
                        }
                        ?><tr><td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><?php _e('Final Total', 'wphrm'); ?></td>
                            <td><?php
                                if (isset($currency[0])) {
                                    echo esc_attr($currency[0]) . ' ';
                                } else {
                                    echo '&#36; ';
                                }
                                ?><?php
                                if (isset($finaltotalGet)) : echo esc_html($finaltotalGet);
                                endif;
                                ?></td>
                        </tr>
                </table>
            </body></html>
        <?php
    }

    public function WPHRMGetSalaryReportExcel($wphrmFromDate, $wphrmToDate, $wphrmEmployeeID) {
        global $wpdb;
        $wphrmEmplyeeDatas = array();
        $wphrmFromDate = esc_sql(date('Y-m-', strtotime($wphrmFromDate)) . '01'); // esc
        $wphrmToDate = esc_sql(date('Y-m-', strtotime($wphrmToDate)) . '01'); // esc
        $wphrmEarningfiledskeyInformation = esc_sql('wphrmEarningfiledskey'); // esc
        $wphrmDeductionfiledskeyInformation = esc_sql('wphrmDeductionfiledskey'); // esc
        $wphrmEmployeeData = $this->wphrmMainClass->WPHRMGetUserDatas($wphrmEmployeeID, 'wphrmEmployeeInfo');
        $wphrmEarningSalary = $wpdb->get_results("SELECT * FROM $this->salaryTable WHERE `employeeID` = $wphrmEmployeeID AND (`employeeKey`='$wphrmEarningfiledskeyInformation' OR `employeeKey`='$wphrmDeductionfiledskeyInformation') AND `date` BETWEEN '$wphrmFromDate' AND '$wphrmToDate' ORDER BY month(`date`) ASC");
        foreach ($wphrmEarningSalary as $key => $earningSalary) :
            if ($earningSalary->employeeKey == 'wphrmEarningfiledskey') :
                $wphrmEmplyeeDatas[date('Y', strtotime($earningSalary->date))][date('m', strtotime($earningSalary->date))]['wphrmEarningfiledskey'] = unserialize(base64_decode($earningSalary->employeeValue));
                $wphrmExtraFields[] = unserialize(base64_decode($earningSalary->employeeValue));
            endif;
            if ($earningSalary->employeeKey == 'wphrmDeductionfiledskey') :
                $wphrmEmplyeeDatas[date('Y', strtotime($earningSalary->date))][date('m', strtotime($earningSalary->date))]['wphrmDeductionfiledskey'] = unserialize(base64_decode($earningSalary->employeeValue));
            endif;
        endforeach;
        $wphrmEarningDatas = array();
        $wphrmDeductionDatas = array();
        $wphrmEarningFields = array();
        $wphrmDeductionFields = array();
        $totalAmountSalary = 0;
        $wphrmTotalAmountPerMonth = array();
        $allDatas = array();

        foreach ($wphrmEmplyeeDatas as $year => $yearDatas) :
            foreach ($yearDatas as $month => $monthData) :
                $perMonthSalary = 0;
                foreach ($monthData['wphrmEarningfiledskey']['wphrmEarningLebal'] as $k => $field) :
                    $wphrmEarningDatas[$monthData['wphrmEarningfiledskey']['wphrmEarningLebal'][$k]] = $monthData['wphrmEarningfiledskey']['wphrmEarningValue'][$k];
                    $totalAmountSalary = intval($totalAmountSalary + $monthData['wphrmEarningfiledskey']['wphrmEarningValue'][$k]);
                    $perMonthSalary = intval($perMonthSalary + $monthData['wphrmEarningfiledskey']['wphrmEarningValue'][$k]);
                    if (!in_array($monthData['wphrmEarningfiledskey']['wphrmEarningLebal'][$k], $wphrmEarningFields)) :
                        array_push($wphrmEarningFields, $monthData['wphrmEarningfiledskey']['wphrmEarningLebal'][$k]);
                    endif;
                endforeach;
                foreach ($monthData['wphrmDeductionfiledskey']['wphrmDeductionLebal'] as $k => $field) :
                    $wphrmDeductionDatas[$monthData['wphrmDeductionfiledskey']['wphrmDeductionLebal'][$k]] = $monthData['wphrmDeductionfiledskey']['wphrmDeductionValue'][$k];
                    $totalAmountSalary = intval($totalAmountSalary - $monthData['wphrmDeductionfiledskey']['wphrmDeductionValue'][$k]);
                    $perMonthSalary = intval($perMonthSalary - $monthData['wphrmDeductionfiledskey']['wphrmDeductionValue'][$k]);
                    if (!in_array($monthData['wphrmDeductionfiledskey']['wphrmDeductionLebal'][$k], $wphrmDeductionFields)) :
                        array_push($wphrmDeductionFields, $monthData['wphrmDeductionfiledskey']['wphrmDeductionLebal'][$k]);
                    endif;
                endforeach;
                $allDatas[$year][$month]['wphrmEarningData'] = $wphrmEarningDatas;
                $allDatas[$year][$month]['wphrmDeductionData'] = $wphrmDeductionDatas;
                $allDatas[$year][$month]['wphrmTotalAmount'] = $perMonthSalary;
            endforeach;
        endforeach;

        /* print_r($wphrmEarningFields); exit();
          print_r($wphrmDeductionFields); */

        ob_clean();
        $filename = strtolower($wphrmEmployeeData['wphrm_employee_fname']) . '-' . strtolower($wphrmEmployeeData['wphrm_employee_lname']) . $wphrmFromDate . '-TO-' . $wphrmToDate;
        header('Content-Disposition: attachment; filename=' . $filename . '.xls');
        header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
        header('Pragma: no-cache');
        header('Content-Type: application/x-msexcel; charset=windows-1251; format=attachment;');
        ?>
        <html>
            <head>
                <style>
                    body {
                        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 62.5%; color: #585858; padding: 22px 10px; padding-bottom: 55px;
                    }
                    #keywords {
                        margin: 0 auto; font-size: 1.2em; margin-bottom: 15px; width: 100%; margin-top: 75px;
                    }
                    #keywords thead tr th {
                        border: 1px solid #ddd; padding: 12px 30px; padding-left: 42px;
                    }
                    #keywords thead tr th span { padding-right: 20px; }
                    #keywords tbody tr td {
                        text-align: center; padding: 15px 10px; border: 1px solid #ddd;
                    }
                    .logo { display: inline-block; float: left; }
                    .main-header { text-align: center; font-size: 25px; margin-top: 50px; text-decoration: underline; color: #546E7A; text-transform: capitalize; font-weight: 400; margin-right: 10%; letter-spacing: 0.5px; }
                    .bottom-line { border-bottom: 2px solid #ddd; width: 97%; padding-top: 50px; position: absolute; }
                </style>
            </head>
            <body>
                <div class="main-header">
                    <?php
                    echo esc_html($wphrmEmployeeData['wphrm_employee_fname']) . ' ' . esc_html($wphrmEmployeeData['wphrm_employee_lname']) . ' ';
                    _e('Salary Report', 'wphrm');
                    echo ' ';
                    _e('from', 'wphrm');
                    echo ' : ' . esc_html(date('d-m-Y', strtotime($wphrmFromDate)));
                    ?>
                    <?php
                    _e('to', 'wphrm');
                    echo ' ' . esc_html(date('d-m-Y', strtotime($wphrmToDate)));
                    ?> 
                </div>
                <table id="keywords" cellspacing="0" cellpadding="0">
                    <thead>
                        <tr>
                            <th colspan="4"><?php _e('Basic Info', 'wphrm'); ?></th>
                            <th colspan="<?php echo esc_html(count($wphrmEarningFields)); ?>"><?php _e('Earnings', 'wphrm'); ?></th>
                            <th colspan="<?php echo esc_html(count($wphrmDeductionFields)); ?>"><?php _e('Deductions', 'wphrm'); ?></th>
                        </tr>
                        <tr>                            
                            <th><span><?php _e('S.No', 'wphrm'); ?></span></th>
                            <th><span><?php _e('Year', 'wphrm'); ?></span></th>
                            <th><span><?php _e('Month', 'wphrm'); ?></span></th>
                            <th width="500"><span><?php _e('Total Paid Salary', 'wphrm'); ?></span></th>                            
                            <?php
                            // Salary Earning 
                            foreach ($wphrmEarningFields as $field) : echo '<th><span>' . esc_html($field) . '</span></th>';
                            endforeach;
                            ?>
                            <?php
                            // Salary Deduction 
                            foreach ($wphrmDeductionFields as $field) : echo '<th><span>' . esc_html($field) . '</span></th>';
                            endforeach;
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $wphrmSrNo = 1; ?>
                        <?php foreach ($allDatas as $year => $monthDatas) { ?>
                            <?php foreach ($monthDatas as $month => $datas) {
                                ?>
                                <tr>
                                    <td class="lalign"><?php echo esc_html($wphrmSrNo); ?></td>
                                    <td><?php echo esc_html($year); ?></td>
                                    <td><?php echo esc_html(date('F', strtotime($year . '-' . $month . '-01'))); ?></td>
                                    <td><?php echo esc_html($datas['wphrmTotalAmount']); ?></td>
                                    <?php foreach ($datas['wphrmEarningData'] as $field => $earningdata): ?>
                                        <td><?php echo esc_html($earningdata); ?></td>
                                    <?php endforeach; ?>
                                    <?php foreach ($datas['wphrmDeductionData'] as $field => $deductiondata): ?>
                                        <td><?php echo esc_html($deductiondata); ?></td>
                                    <?php endforeach; ?>
                                </tr>
                                <?php
                                $wphrmSrNo++;
                            }
                            ?>
                        <?php } ?>
                        <tr><td></td></tr>
                        <tr><td></td></tr>
                        <tr><td></td></tr>
                        <tr><td></td><td colspan="2"><?php _e('Total Pay', 'wphrm'); ?></td><td><?php echo esc_html($totalAmountSalary); ?></td></tr>
                    </tbody>
                </table>
            </body></html>
        <?php
        exit;
    }

    public function WPHRMGetTotalSalaryExcel($wphrmFromDate, $wphrmToDate, $employeeId) {
        $wphrm_info = $this->wphrmMainClass->WPHRMGetUserDatas($employeeId, 'wphrmEmployeeInfo');
        $wphrmDateStart = date('Y-m-d', strtotime($wphrmFromDate . '-01'));
        $wphrmDateEnd = date('Y-m-d', strtotime($wphrmToDate . '-01'));
        global $current_user, $wpdb;
        $filename = 'totalSalaryExcel-' . date('dmYHis');
        ob_clean();
        header('Content-Disposition: attachment; filename=' . $filename . '.xls');
        header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
        header('Pragma: no-cache');
        header('Content-Type: application/x-msexcel; charset=windows-1251; format=attachment;');
        ?>
        <table>
            <tr><th colspan="6"> <?php
                    echo esc_html($wphrm_info['wphrm_employee_fname']) . ' ' . esc_html($wphrm_info['wphrm_employee_lname']) . ' ';
                    _e('Salary Report', 'wphrm');
                    echo ' ';
                    _e('from', 'wphrm');
                    echo ' ' . esc_html($wphrmFromDate);
                    ?>
                    <?php
                    _e(' to ', 'wphrm');
                    echo ' ' . esc_html($wphrmToDate);
                    ?></th></tr>
            <tr></tr>
            <tr>
                <th><?php _e('S.No', 'wphrm'); ?></th>
                <th><?php _e('Month', 'wphrm'); ?></th>
                <th><?php _e('Earning', 'wphrm'); ?></th>
                <th><?php _e('Deduction', 'wphrm'); ?>.</th>
                <th><?php _e('Total Paid', 'wphrm'); ?></th>
            </tr><?php
            $j = 1;
            $salaryEmployeeCounter = 0;
            $wphrmEmployeeSalaryGenerated = esc_sql('employeeSalaryGenerated'); // esc
            $wphrmDeductionFiledsKey = esc_sql('wphrmDeductionfiledskey'); // esc
            $wphrmEarningFiledsKey = esc_sql('wphrmEarningfiledskey'); // esc
            $wphrmSalaryGenerateds = $wpdb->get_results("SELECT * FROM $this->salaryTable WHERE (`date` BETWEEN '$wphrmDateStart' AND '$wphrmDateEnd') AND `employeeID`='$employeeId' AND `employeeKey`='$wphrmEmployeeSalaryGenerated' ");
            $finaltotal = 0;
            $finaltotalGet = 0;

            foreach ($wphrmSalaryGenerateds as $wphrmSalaryGenerated) {
                $earningSalaryTotal = 0;
                $deductionSalaryTotal = 0;

                $wphrmEarningFiledsKeyInfo = $wpdb->get_row("SELECT * FROM $this->salaryTable WHERE  `employeeID`='$employeeId' AND  `employeeKey`='$wphrmEarningFiledsKey' AND `date`='$wphrmSalaryGenerated->date'");
                $wphrmEarningInfo = unserialize(base64_decode($wphrmEarningFiledsKeyInfo->employeeValue));
                foreach ($wphrmEarningInfo['wphrmEarningValue'] as $wphrmEarningInfos) {
                    $wphrmEarningtotal = $wphrmEarningInfos + $earningSalaryTotal;
                    $earningSalaryTotal = $wphrmEarningtotal;
                }
                $wphrmDeductionFiledsKeyInfo = $wpdb->get_row("SELECT * FROM $this->salaryTable WHERE  `employeeID`='$employeeId' AND  `employeeKey`='$wphrmDeductionFiledsKey' AND `date`='$wphrmSalaryGenerated->date'");
                $wphrmDeductionInfo = unserialize(base64_decode($wphrmDeductionFiledsKeyInfo->employeeValue));
                foreach ($wphrmDeductionInfo['wphrmDeductionValue'] as $wphrmDeductionInfos) {
                    $wphrmDeductiontotal = $wphrmDeductionInfos + $deductionSalaryTotal;
                    $deductionSalaryTotal = $wphrmDeductiontotal;
                }
                $wphrmSalaryGeneratedDate = date('F Y', strtotime($wphrmSalaryGenerated->date));
                $paidSalaryTotal = $earningSalaryTotal - $deductionSalaryTotal;

                $finaltotal = $paidSalaryTotal + $finaltotalGet;
                $finaltotalGet = $finaltotal;
                ?>
                <tr>
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
                $j++;
            }
            ?>
            <tr></tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <th><?php _e('Final Total', 'wphrm'); ?></th>
                <td><?php
                    if (isset($currency[0])) {
                        echo esc_attr($currency[0]) . ' ';
                    } else {
                        echo '&#36; ';
                    }
                    ?><?php
                    if (isset($finaltotalGet)) : echo esc_html($finaltotalGet);
                    endif;
                    ?></td>
            </tr>
            <?php exit; ?>
        </table>
        <?php
    }

    public function WPHRMGetEmloyeeSalaryExcel($wphrmFromDate, $wphrmToDate, $employeeId) {

        $wphrmDateStart = date('Y-m-d', strtotime($wphrmFromDate . '-01'));
        $wphrmDateEnd = date('Y-m-d', strtotime($wphrmToDate . '-01'));
        global $current_user, $wpdb;
        $filename = 'totalEmployeeSalaryExcel-' . date('dmYHis');
        ob_clean();
        header('Content-Disposition: attachment; filename=' . $filename . '.xls');
        header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
        header('Pragma: no-cache');
        header('Content-Type: application/x-msexcel; charset=windows-1251; format=attachment;');
        ?>
        <table>
            <tr><th colspan="6"> <?php
                    _e('Salary Report', 'wphrm');
                    echo ' ';
                    _e('from', 'wphrm');
                    echo ' : ' . esc_html($wphrmFromDate);
                    ?>
                    <?php
                    _e('to ', 'wphrm');
                    echo esc_html($wphrmToDate);
                    ?></th></tr>
            <tr></tr><tr></tr>

            <?php
            $lasttotalGet = 0;
            $maintotalGet = 0;
            foreach ($employeeId as $employeeIds) {
                $wphrm_info = $this->wphrmMainClass->WPHRMGetUserDatas($employeeIds, 'wphrmEmployeeInfo');
                $j = 1;
                $salaryEmployeeCounter = 0;
                $wphrmEmployeeSalaryGenerated = esc_sql('employeeSalaryGenerated'); // esc
                $wphrmDeductionFiledsKey = esc_sql('wphrmDeductionfiledskey'); // esc
                $wphrmEarningFiledsKey = esc_sql('wphrmEarningfiledskey'); // esc
                $wphrmSalaryGenerateds = $wpdb->get_results("SELECT * FROM $this->salaryTable WHERE (`date` BETWEEN '$wphrmDateStart' AND '$wphrmDateEnd') AND `employeeID`='$employeeIds' AND `employeeKey`='$wphrmEmployeeSalaryGenerated' ");
                $finaltotal = 0;
                $finaltotalGet = 0;

                if (!empty($wphrmSalaryGenerateds)) {
                    if (isset($wphrm_info['wphrm_employee_department']) && $wphrm_info['wphrm_employee_department'] != '') {
                        $employeeDepartmentsLoads = esc_sql($wphrm_info['wphrm_employee_department']); // esc
                        $wphrmDepartments = $wpdb->get_row("SELECT * FROM  $this->DepartmentTable  where `departmentID` = '$employeeDepartmentsLoads'");
                        if ($wphrmDepartments != '') {
                            $wphrmDepartmentInfo = unserialize(base64_decode($wphrmDepartments->departmentName));
                        }

                        $wphrmDesignation = $wpdb->get_row("SELECT * FROM  $this->DesignationTable where departmentID= '" . $wphrm_info['wphrm_employee_department'] . "'");
                        $designationInfo = unserialize(base64_decode($wphrmDesignation->designationName));
                    }
                    ?>
                    <tr><td colspan="6"> <?php
                            echo esc_html($wphrm_info['wphrm_employee_fname']) . ' ' . esc_html($wphrm_info['wphrm_employee_lname']) . ' ';
                            _e('Salary', 'wphrm');
                            echo ' - ';
                            if (isset($wphrmDepartmentInfo['departmentName'])) : echo esc_html($wphrmDepartmentInfo['departmentName']);
                            endif;
                            echo ' - ';
                            if (isset($designationInfo['designationName'])) : echo esc_html($designationInfo['designationName']);
                            endif;
                            ?>
                        </td></tr>
                    <tr>
                        <th><?php _e('S.No', 'wphrm'); ?></th>
                        <th><?php _e('Month', 'wphrm'); ?></th>
                        <th><?php _e('Earning', 'wphrm'); ?></th>
                        <th><?php _e('Deduction', 'wphrm'); ?>.</th>
                        <th><?php _e('Total Paid', 'wphrm'); ?></th>
                    </tr><?php
                    foreach ($wphrmSalaryGenerateds as $wphrmSalaryGenerated) {
                        $earningSalaryTotal = 0;
                        $deductionSalaryTotal = 0;

                        $wphrmEarningFiledsKeyInfo = $wpdb->get_row("SELECT * FROM $this->salaryTable WHERE  `employeeID`='$employeeIds' AND  `employeeKey`='$wphrmEarningFiledsKey' AND `date`='$wphrmSalaryGenerated->date'");
                        $wphrmEarningInfo = unserialize(base64_decode($wphrmEarningFiledsKeyInfo->employeeValue));
                        foreach ($wphrmEarningInfo['wphrmEarningValue'] as $wphrmEarningInfos) {
                            $wphrmEarningtotal = $wphrmEarningInfos + $earningSalaryTotal;
                            $earningSalaryTotal = $wphrmEarningtotal;
                        }
                        $wphrmDeductionFiledsKeyInfo = $wpdb->get_row("SELECT * FROM $this->salaryTable WHERE  `employeeID`='$employeeIds' AND  `employeeKey`='$wphrmDeductionFiledsKey' AND `date`='$wphrmSalaryGenerated->date'");
                        $wphrmDeductionInfo = unserialize(base64_decode($wphrmDeductionFiledsKeyInfo->employeeValue));
                        foreach ($wphrmDeductionInfo['wphrmDeductionValue'] as $wphrmDeductionInfos) {
                            $wphrmDeductiontotal = $wphrmDeductionInfos + $deductionSalaryTotal;
                            $deductionSalaryTotal = $wphrmDeductiontotal;
                        }
                        $wphrmSalaryGeneratedDate = date('F Y', strtotime($wphrmSalaryGenerated->date));
                        $paidSalaryTotal = $earningSalaryTotal - $deductionSalaryTotal;

                        $finaltotal = $paidSalaryTotal + $finaltotalGet;
                        $finaltotalGet = $finaltotal;
                        ?>
                        <tr>
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
                        $j++;
                    }
                    ?>

                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <th><?php _e('Total Paid', 'wphrm'); ?></th>
                        <td><?php
                            if (isset($currency[0])) {
                                echo esc_attr($currency[0]) . ' ';
                            } else {
                                echo '&#36; ';
                            }
                            ?><?php
                            if (isset($finaltotalGet)) : echo esc_html($finaltotalGet);
                            endif;
                            ?></td>
                    </tr>
                    <tr></tr> <tr></tr>
                    <?php
                }
                $lasttotalGet = $finaltotalGet + $maintotalGet;
                $maintotalGet = $lasttotalGet;
            }
            ?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <th><?php _e('Final Total', 'wphrm'); ?></th>
                <td><?php
                    if (isset($currency[0])) {
                        echo esc_attr($currency[0]) . ' ';
                    } else {
                        echo '&#36; ';
                    }
                    ?><?php
                    if (isset($maintotalGet)) : echo esc_html($maintotalGet);
                    endif;
                    ?></td>
            </tr><tr></tr>
            <?php exit; ?>
        </table>
        <?php
    }

}
?>