<?php
if (!defined('ABSPATH'))
    exit;
global $current_user, $wpdb;
$wphrmUserRole = implode(',', $current_user->roles);
$wphrmUserAllRoles = $this->WPHRMGetUserRoles();
$wphrmGeneralSettingsInfo = $this->WPHRMGetSettings('wphrmGeneralSettingsInfo');
$wphrmFrontEndInfo = $this->WPHRMGetSettings('wphrmFrontEndInfo');
$wphrmGenerationSetting = $this->WPHRMGetSettings('wphrmSalaryGenerationSettings');
$wphrmSalarySlipInfo = $this->WPHRMGetSettings('wphrmSalarySlipInfo');
$wphrmNotificationsSettingsInfo = $this->WPHRMGetSettings('wphrmNotificationsSettingsInfo');
$wphrmUserPermissionInfo = $this->WPHRMGetSettings('wphrmUserPermissionInfo');
$wphrmExpenseReportInfo = $this->WPHRMGetSettings('wphrmExpenseReportInfo');
$wphrmSalaryAccording = $this->WPHRMGetSettings('wphrsalaryDayOrHourlyInfo');
$WPHRMAutomaticAttendanceMark = $this->WPHRMGetSettings('wphrmAutomaticAttendance');
$WPHRMHideShowEmployeeSectionInfo = $this->WPHRMGetSettings('WPHRMHideShowEmployeeSectionInfo');
$wphrmEarningInfo = '';
$wphrmDeductionInfo = '';
$wphrm_messages_General_Settings = $this->WPHRMGetMessage(27);
$wphrm_messages_notification_settings = $this->WPHRMGetMessage(28);
$wphrm_messages_changepassword_settings = $this->WPHRMGetMessage(29);
$wphrm_messages_salary_slip_setting = $this->WPHRMGetMessage(30);
$wphrm_messages_user_permissions_settings = $this->WPHRMGetMessage(31);
$wphrm_messages_settings = $this->WPHRMGetMessage(34);
$wphrmExpenseReportAmount = $this->WPHRMGetMessage(35);
$wphrmMessagesAddEarnings = $this->WPHRMGetMessage(38);
$wphrmMessagesUpdateEarnings = $this->WPHRMGetMessage(39);
$wphrmMessagesAddDeductions = $this->WPHRMGetMessage(40);
$wphrmMessagesUpdateDeductions = $this->WPHRMGetMessage(41);
$wphrmRemoveLebal = $this->WPHRMGetMessage(42);
$wphrmDefaultDocumentsLabel = $this->WPHRMGetDefaultDocumentsLabel();
?>

<!-- BEGIN PAGE HEADER-->
<div class="preloader">
    <span class="preloader-custom-gif"></span>
</div>

<style>body .ui-tooltip {
    display: table !important;
    border-width: 2px;
    }</style>

<div id="currencysettings" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
                <h4 class="modal-title"><strong><i class="fa fa-plus"></i> &nbsp;<?php _e('Currency Settings', 'wphrm'); ?></strong></h4>

            </div>
            <div class="modal-body">
                <div class="portlet-body form">
                    <div class="alert alert-success display-hide" id="wphrcurrencySuccess"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrm_messages_settings); ?>
                        <button class="close" data-close="alert"></button>
                    </div>
                    <div class="alert alert-danger display-hide" id="wphrcurrencyError">
                        <button class="close" data-close="alert"></button>
                    </div>
                    <!-- BEGIN FORM-->
                    <form method="POST"  accept-charset="UTF-8" class="form-horizontal wphrcurrencyForm" id="">
                        <div class="form-body">
                            <div class="form-group">
                                <div class="col-md-9">
                                    <input class="form-control form-control-inline " name="currency-sign" id="currency-sign" type="text" value="" placeholder="<?php _e('ASCII Value of Currency Sign : ', 'wphrm'); ?>" />
                                </div>
                                <div class="col-md-3">
                                    <a style="float: right;" href="http://symbologic.info/currency.htm"  class="btn blue" target="_blank"><i class="fa fa-link"></i><?php _e('Check Here', 'wphrm'); ?></a>
                                </div>
                            </div>
                        </div>
                        <div class="form-body">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <input class="form-control form-control-inline " name="currency-name" id="currency-name" type="text" value="" placeholder="<?php _e('Currency Name : USD', 'wphrm'); ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="form-body">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <input class="form-control form-control-inline " name="currency-desc" id="currency-desc" type="text" value="" placeholder="<?php _e('Currency Description ', 'wphrm'); ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class=" col-md-6">
                                    <button type="submit"  class="btn blue"><i class="fa fa-edit"></i><?php _e('Submit', 'wphrm'); ?></button>
                                    <button type="button" data-dismiss="modal" class="btn red"><i class="fa fa-times"></i><?php _e('Cancel', 'wphrm'); ?></button>
                                </div>
                                <div class=" col-md-6">
                                    <button style="float: right;" type="button" data-toggle="modal" data-dismiss="modal" href="#Editcurrencysettings" class="btn blue"><i class="fa fa-edit"></i><?php _e('Edit Currency', 'wphrm'); ?></button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- END FORM-->
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>
</div>


<div id="Editcurrencysettings" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
                <h4 class="modal-title"><strong><i class="fa fa-edit"></i> &nbsp;<?php _e('Edit Currency Settings', 'wphrm'); ?></strong></h4>

            </div>
            <div class="modal-body">
                <div class="portlet-body form">
                    <div class="alert alert-success display-hide" id="wphrcurrencySuccess"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrm_messages_settings); ?>
                        <button class="close" data-close="alert"></button>
                    </div>
                    <div class="alert alert-danger display-hide" id="wphrcurrencyError">
                        <button class="close" data-close="alert"></button>
                    </div>
                    <!-- BEGIN FORM-->
                    <form method="POST"  accept-charset="UTF-8" class="form-horizontal wphrmEditCurrencyForm" id="">
                        <div class="form-body">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <?php $currency_symbols = $wpdb->get_results("SELECT * FROM $this->WphrmCurrencyTable"); ?>
                                    <select  name="currency-load" id="currencyloaddata">
                                        <option value="" ><?php _e('Select Currency', 'wphrm'); ?></option>
                                        <?php
                                        foreach ($currency_symbols as $key => $currency) {
                                            $check = strstr($currency->currencySign, ';');
                                            if ($check != '') {
                                                $colon = '';
                                            } else {
                                                $colon = ';';
                                            }
                                        ?>
                                        <option value="<?php echo $currency->id ?>" ><?php echo $currency->currencySign . $colon . ' -  ' . esc_html($currency->currencyName); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="form-body">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <input class="form-control form-control-inline " name="currency-sign" id="edit-currency-sign" type="text" value="" placeholder="<?php _e('Currency Sign : ', 'wphrm'); ?> &#v36;" />
                                </div>
                            </div>
                        </div>
                        <div class="form-body">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <input class="form-control form-control-inline " name="currency-name" id="edit-currency-name" type="text" value="" placeholder="<?php _e('Currency Name : USD', 'wphrm'); ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="form-body">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <input class="form-control form-control-inline " name="currency-desc" id="edit-currency-desc" type="text" value="" placeholder="<?php _e('Currency Description ', 'wphrm'); ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="form-actions edit-loadaction">
                            <div class="row">
                                <div class="col-md-offset-3 col-md-9">
                                    <button type="submit"  class="btn blue"><i class="fa fa-edit"></i><?php _e('Submit', 'wphrm'); ?></button>
                                    <button type="button" data-dismiss="modal" class="btn red"><i class="fa fa-times"></i><?php _e('Cancel', 'wphrm'); ?></button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- END FORM-->
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>
</div>


<input type="hidden" id="image_url" value="<?php echo esc_attr(plugins_url('assets/images/Remove.png', __FILE__)) ?>">
<div id="EditEarning" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
            <h4 class="modal-title"><strong><i class="fa fa-edit"></i> <?php _e('Edit Earning', 'wphrm'); ?></strong></h4>
        </div>
        <div class="modal-body">
            <div class="portlet-body form">
                <div class="alert alert-success display-hide" id="wphrmEditEarningLebalsuccess"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrmMessagesUpdateEarnings); ?>
                    <button class="close" data-close="alert"></button>
                </div>
                <div class="alert alert-danger display-hide" id="wphrmEditEarningLebalerror">
                    <button class="close" data-close="alert"></button>
                </div>
                <!-- BEGIN FORM-->
                <form method="POST"  accept-charset="UTF-8" class="form-horizontal" id="">
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3"><?php _e('Edit Earning Label', 'wphrm'); ?></label>
                            <div class="col-md-8">
                                <input type="text" class="form-control form-control-inline " name="wphrmEditearning" id="wphrmlabelname" placeholder="<?php _e('Edit Earning Lebal', 'wphrm'); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="button" id="wphrmaddlabelname" class="demo-loading-btn btn blue"><i class="fa fa-edit"></i><?php _e('Edit Earning', 'wphrm'); ?></button>
                                <button type="button" data-dismiss="modal" aria-hidden="true" class="btn red"><i class="fa fa-times"></i><?php _e('Cancel', 'wphrm'); ?></button>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- END FORM-->
            </div>
        </div>
        <!-- END EXAMPLE TABLE PORTLET-->
    </div>
    </div>
</div>

<div id="edit_message" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
                <h4 class="modal-title"><strong><i class="fa fa-edit"></i> &nbsp;<?php _e('Edit Message', 'wphrm'); ?></strong></h4>
            </div>
            <div class="modal-body">
                <div class="portlet-body form">
                    <div class="alert alert-success display-hide" id="wphrmAllMessagesInfo_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrm_messages_settings); ?>
                        <button class="close" data-close="alert"></button>
                    </div>
                    <div class="alert alert-danger display-hide" id="wphrmAllMessagesInfo_error">
                        <button class="close" data-close="alert"></button>
                    </div>
                    <!-- BEGIN FORM-->
                    <form method="POST"  accept-charset="UTF-8" class="form-horizontal wphrm_edit_department" id="wphrmAllMessagesInfo_form">
                        <div class="form-body">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <input name="wphrm_messages_id" id="wphrm_messages_id" type="hidden" />
                                    <input class="form-control form-control-inline " name="wphrm_messages_title" id="wphrm_messages_title" type="text" value="" placeholder="<?php _e('Messages title', 'wphrm'); ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="form-body">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <input class="form-control form-control-inline " name="wphrm_messages_desc" id="wphrm_messages_desc" type="text" value="" placeholder="<?php _e('Messages Description', 'wphrm'); ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-offset-3 col-md-9">
                                    <button type="submit"  class="btn blue"><i class="fa fa-edit"></i><?php _e('Edit Message', 'wphrm'); ?></button>
                                    <button type="button" data-dismiss="modal" class="btn red"><i class="fa fa-times"></i><?php _e('Cancel', 'wphrm'); ?></button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- END FORM-->
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>
</div>

<div id="deleteModal" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="alert alert-success display-hide" id="WPHRMCustomDelete_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrmRemoveLebal); ?>
                <button class="close" data-close="alert"></button>
            </div>
            <div class="alert alert-danger display-hide" id="WPHRMCustomDelete_error">
                <button class="close" data-close="alert"></button>
            </div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
                <h4 class="modal-title"><?php _e('Confirmation', 'wphrm'); ?></h4>
            </div>
            <div class="modal-body" id="info"><p></p></div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn red" id="delete"><i class="fa fa-trash"></i><?php _e('Delete', 'wphrm'); ?></button>
                <button type="button" data-dismiss="modal" aria-hidden="true" class="btn default"><i class="fa fa-times"></i><?php _e('Cancel', 'wphrm'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="col-md-12">
    <h3 class="page-title"><?php _e('Settings', 'wphrm'); ?></h3>
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li><i class="fa fa-home"></i><?php _e('Home', 'wphrm'); ?><i class="fa fa-angle-right"></i></li>
            <li><?php _e('Settings', 'wphrm'); ?></li>
        </ul>
    </div>


    <ul class="tab hrmClass">
        <li><a href="javascript:void(0)" class="tablinks active" onclick="wphrmOpenSettings(event, 'general-settings')"><?php _e('General Settings', 'wphrm'); ?></a></li>
        <li><a href="javascript:void(0)" class="tablinks" onclick="wphrmOpenSettings(event, 'employee-settings')"><?php _e('Employee Page Settings', 'wphrm'); ?></a></li>
        <li><a href="javascript:void(0)" class="tablinks" onclick="wphrmOpenSettings(event, 'import-export-settings')"><?php _e('Import/Export Settings', 'wphrm'); ?></a></li>
        <?php if (isset($current_user->roles) &&  in_array('administrator', $current_user->roles)) { ?>
        <li><a href="javascript:void(0)" class="tablinks" onclick="wphrmOpenSettings(event, 'permission-settings')"><?php _e('Permission Settings', 'wphrm'); ?></a></li>
        <?php } ?>
        <li><a href="javascript:void(0)" class="tablinks" onclick="wphrmOpenSettings(event, 'message-settings')"><?php _e('Message Settings', 'wphrm'); ?></a></li>
        <li><a href="javascript:void(0)" class="tablinks" onclick="wphrmOpenSettings(event, 'email-settings')"><?php _e('Email Notifications', 'wphrm'); ?></a></li>

    </ul>

    <div id="general-settings" class="tabcontent " style="display: block;">
        <div class="row ">
            <div class="col-md-6 col-sm-6">
                <div class="portlet box blue" style="border: none;">
                    <div class="portlet-title">
                        <div class="caption"><i class="fa fa-cog fa-fw"></i><?php _e('Company Informations', 'wphrm'); ?></div>
                        <div class="actions">
                            <a href="javascript:;" onclick="jQuery('#wphrmGeneralSettingsInfo_form').submit();" data-loading-text="Updating..." class="demo-loading-btn btn btn-sm btn-default ">
                                <i class="fa fa-save"></i><?php _e('Save', 'wphrm'); ?></a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="alert alert-success display-hide" id="general_settings_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrm_messages_General_Settings); ?>
                            <button class="close" data-close="alert"></button>
                        </div>
                        <div class="alert alert-danger display-hide" id="general_settings_error">
                            <button class="close" data-close="alert"></button>
                        </div>
                        <form method="POST"  accept-charset="UTF-8" class="form-horizontal" id="wphrmGeneralSettingsInfo_form" enctype="multipart/form-data">
                            <div class="form-body">
                                <div class="form-group ">
                                    <label class="control-label col-md-4"><?php _e('Company Logo', 'wphrm'); ?></label>
                                    <div class="col-md-8">
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <div class="fileinput-new thumbnail" style="width: auto; height: 150px;">
                                                <?php if (isset($wphrmGeneralSettingsInfo['wphrm_company_logo']) && $wphrmGeneralSettingsInfo['wphrm_company_logo'] != '') { ?>
                                                <img src="<?php
    if (isset($wphrmGeneralSettingsInfo['wphrm_company_logo'])) : echo esc_attr($wphrmGeneralSettingsInfo['wphrm_company_logo']);
    endif;
                                                          ?>"  height="150"/>
                                                <?php } else { ?>
                                                <img src="<?php echo esc_attr(plugins_url('assets/images/logo.png', __FILE__)); ?>"  height="150"/>
                                                <?php } ?>
                                            </div>
                                            <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;">
                                            </div>
                                            <div>
                                                <span class="btn default btn-file">
                                                    <span class="fileinput-new"><?php _e('Select Image', 'wphrm'); ?> </span>
                                                    <span class="fileinput-exists"><?php _e('Change', 'wphmr'); ?> </span>
                                                    <input type="file" name="wphrm_company_logo" id="wphrm_company_logo" />
                                                </span>
                                                <a href="#" class="btn red fileinput-exists" data-dismiss="fileinput"><?php _e('Remove', 'wphrm'); ?></a>
                                            </div><br>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group " style="position: relative;top: -9px;">
                                    <div class="col-md-4">
                                    </div>
                                    <div class="col-md-1"><span class="label label-danger span-padding"><?php _e('NOTE', 'wphrm'); ?> !</span></div> <div class="col-md-6 notice-info"><?php _e("Only 'jpeg', 'jpg', 'png' filetypes are allowed and size should be 117px*30px.", 'wphrm'); ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e('Company Full Name', 'wphrm'); ?><span class="required"></span></label>
                                    <div class="col-md-8">
                                        <input class="form-control" name="wphrm_company_full_name" type="text" id="wphrm_company_full_name" value="<?php
                                                                                                                                                   if (isset($wphrmGeneralSettingsInfo['wphrm_company_full_name'])) : echo esc_attr($wphrmGeneralSettingsInfo['wphrm_company_full_name']);
                                                                                                                                                   endif;
                                                                                                                                                   ?>" autocapitalize="none"  />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e('Email', 'wphrm'); ?><span class="required"></span></label>
                                    <div class="col-md-8">
                                        <input class="form-control"  name="wphrm_company_email" type="text" id="wphrm_company_email" value="<?php
                                                                                                                                            if (isset($wphrmGeneralSettingsInfo['wphrm_company_email'])) : echo esc_attr($wphrmGeneralSettingsInfo['wphrm_company_email']);
                                                                                                                                            endif;
                                                                                                                                            ?>" autocapitalize="none"  />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e('Phone', 'wphrm'); ?><span class="required"></span></label>
                                    <div class="col-md-8">
                                        <input class="form-control"  name="wphrm_company_phone" type="text" id="wphrm_company_phone" value="<?php
                                                                                                                                            if (isset($wphrmGeneralSettingsInfo['wphrm_company_phone'])) : echo esc_attr($wphrmGeneralSettingsInfo['wphrm_company_phone']);
                                                                                                                                            endif;
                                                                                                                                            ?>" autocapitalize="none"  />
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e('Address', 'wphrm'); ?></label>
                                    <div class="col-md-8">
                                        <textarea class="form-control" name="wphrm_company_address" rows="2" id="wphrm_company_address"><?php
                                            if (isset($wphrmGeneralSettingsInfo['wphrm_company_address'])) : echo esc_textarea($wphrmGeneralSettingsInfo['wphrm_company_address']);
                                            endif;
                                            ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <div class="col-md-6 col-sm-6">
                <div class="portlet box  blue" style="border: none;">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-key"></i><?php _e('Change Password', 'wphrm'); ?>
                        </div>
                        <div class="actions">
                            <a href="javascript:;" onclick="jQuery('#wphrmChangePasswordInfo_form').submit();" data-loading-text="Updating..." class="demo-loading-btn btn btn-sm btn-default ">
                                <i class="fa fa-save" ></i> <?php _e('Save', 'wphrm'); ?>
                            </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="alert alert-success display-hide" id="wphrmChangePasswordInfo_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrm_messages_changepassword_settings); ?>
                            <button class="close" data-close="alert"></button>
                        </div>
                        <div class="alert alert-danger display-hide" id="wphrmChangePasswordInfo_error">
                            <button class="close" data-close="alert"></button>
                        </div>
                        <form method="POST"  accept-charset="UTF-8" class="form-horizontal" id="wphrmChangePasswordInfo_form">
                            <div id="alert_bank"></div>
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e('Current Password', 'wphrm'); ?></label>
                                    <div class="col-md-8">
                                        <input class="form-control" name="wphrm_current_password" type="password" id="wphrm_employee_bank_account_name" autocapitalize="none"  />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e('New Password', 'wphrm'); ?></label>
                                    <div class="col-md-8">
                                        <input class="form-control" name="wphrm_new_password" type="password" id="wphrm_new_password" autocapitalize="none"  />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e('Confirm Password', 'wphrm'); ?></label>
                                    <div class="col-md-8">
                                        <input class="form-control" name="wphrm_conform_password" type="password" id="wphrm_conform_password" autocapitalize="none"  />
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="portlet box  blue" style="border: none;">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-bell"></i> <?php _e('Notifications', 'wphrm'); ?>
                        </div>
                        <div class="actions">
                            <a href="javascript:;"  onclick="jQuery('#wphrmNotificationsSettingsInfo_form').submit();" data-loading-text="Updating..."  class="demo-loading-btn btn btn-sm btn-default ">
                                <i class="fa fa-save" ></i> <?php _e('Save', 'wphrm'); ?> </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="alert alert-success display-hide" id="wphrm_notifications_settings_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrm_messages_notification_settings); ?>
                            <button class="close" data-close="alert"></button>
                        </div>
                        <div class="alert alert-danger display-hide" id="wphrm_notifications_settings_error">
                            <button class="close" data-close="alert"></button>
                        </div>
                        <form method="POST"  accept-charset="UTF-8" class="form-horizontal" id="wphrmNotificationsSettingsInfo_form">
                            <div id="alert_bank"></div>
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e('Notice Board', 'wphrm'); ?>:</label>
                                    <div class="col-md-8">
                                        <input  type="checkbox" value="1"   class="make-switch" name="wphrm_notice_notification" <?php
                                               if (isset($wphrmNotificationsSettingsInfo['wphrm_notice_notification']) && $wphrmNotificationsSettingsInfo['wphrm_notice_notification'] == '1') : echo esc_attr('checked');
                                               endif;
                                               ?> data-on-color="success" data-on-text="<?php _e('Yes', 'wphrm'); ?>" data-off-text="<?php _e('No', 'wphrm'); ?>" data-off-color="danger">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e('Leave Application', 'wphrm'); ?>:</label>
                                    <div class="col-md-8">
                                        <input  type="checkbox" value="1"   class="make-switch" name="wphrm_leave_notification" <?php
                                               if (isset($wphrmNotificationsSettingsInfo['wphrm_leave_notification']) && $wphrmNotificationsSettingsInfo['wphrm_leave_notification'] == '1') : echo esc_attr('checked');
                                               endif;
                                               ?> data-on-color="success" data-on-text="<?php _e('Yes', 'wphrm'); ?>" data-off-text="<?php _e('No', 'wphrm'); ?>" data-off-color="danger">
                                    </div>
                                </div>
                                </form>
                            </div>
                    </div>
                </div>
                <div class="portlet box  blue" style="border: none;">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-eye"></i> <?php _e('Automatic Attendance', 'wphrm'); ?>
                        </div>
                        <div class="actions">
                            <a href="javascript:;"  onclick="jQuery('#wphrmhide-automatic-attendance-form').submit();" data-loading-text="Updating..."  class="demo-loading-btn btn btn-sm btn-default ">
                                <i class="fa fa-save" ></i> <?php _e('Save', 'wphrm'); ?> </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="alert alert-success display-hide" id="automatic_attendance_success">
                            <button class="close" data-close="alert"></button>
                        </div>
                        <div class="alert alert-danger display-hide" id="automatic_attendance_error">
                            <button class="close" data-close="alert"></button>
                        </div>
                        <form method="POST"  accept-charset="UTF-8" class="form-horizontal" id="wphrmhide-automatic-attendance-form">
                            <div id="alert_bank"></div>
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e('Automatic Attendance Status', 'wphrm'); ?>:</label>
                                    <div class="col-md-8">
                                        <?php if (isset($WPHRMAutomaticAttendanceMark['automatic-attendance']) && $WPHRMAutomaticAttendanceMark['automatic-attendance'] == 'on') { ?>
                                        <input  type="checkbox"   class="make-switch automatic-attendance-checked" name="automatic-attendance"
                                               data-on-color="success" checked data-on-text="<?php _e('Enable', 'wphrm'); ?>" data-off-text="<?php _e('Disable', 'wphrm'); ?>" data-off-color="danger">
                                        <?php } else if (isset($WPHRMAutomaticAttendanceMark['automatic-attendance']) && $WPHRMAutomaticAttendanceMark['automatic-attendance'] == 'off') { ?>
                                        <input  type="checkbox"   class="make-switch automatic-attendance-checked" name="automatic-attendance"
                                               data-on-color="success" data-on-text="<?php _e('Enable', 'wphrm'); ?>" data-off-text="<?php _e('Disable', 'wphrm'); ?>" data-off-color="danger">
                                        <?php }else{ ?>
                                        <input  type="checkbox"   class="make-switch automatic-attendance-checked" name="automatic-attendance"
                                               data-on-color="success" checked data-on-text="<?php _e('Enable', 'wphrm'); ?>" data-off-text="<?php _e('Disable', 'wphrm'); ?>" data-off-color="danger">
                                        <?php  } ?>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>

    </div>
    <div id="employee-settings" class="tabcontent">
        <div class="row ">
            <div class="col-md-6 col-sm-6">
                <div class="portlet box blue" style="border: none;">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-eye"></i> <?php _e('Employee Information Sections', 'wphrm'); ?>
                        </div>
                        <div class="actions">
                            <a href="javascript:;"  onclick="jQuery('#wphrmhide-show-employee-section-form').submit();" data-loading-text="Updating..."  class="demo-loading-btn btn btn-sm btn-default ">
                                <i class="fa fa-save" ></i> <?php _e('Save', 'wphrm'); ?> </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="alert alert-success display-hide" id="wphrm_hide_show_section_success">
                            <button class="close" data-close="alert"></button>
                        </div>
                        <div class="alert alert-danger display-hide" id="wphrm_hide_show_section_error">
                            <button class="close" data-close="alert"></button>
                        </div>
                        <form method="POST"  accept-charset="UTF-8" class="form-horizontal" id="wphrmhide-show-employee-section-form">
                            <div id="alert_bank"></div>
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e('Documents', 'wphrm'); ?>:</label>

                                    <div class="col-md-8">
                                        <?php if (isset($WPHRMHideShowEmployeeSectionInfo['documents-details']) && $WPHRMHideShowEmployeeSectionInfo['documents-details'] == '0') { ?>
                                        <input  type="checkbox"    class="make-switch document-checked" name="documents-details"
                                               data-on-color="success"  data-on-text="<?php _e('Show', 'wphrm'); ?>" data-off-text="<?php _e('Hide', 'wphrm'); ?>" data-off-color="danger">
                                        <?php } else { ?>
                                        <input  type="checkbox"    class="make-switch document-checked" name="documents-details"
                                               data-on-color="success" checked="checked" data-on-text="<?php _e('Show', 'wphrm'); ?>" data-off-text="<?php _e('Hide', 'wphrm'); ?>" data-off-color="danger">
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e('Other Details', 'wphrm'); ?>:</label>
                                    <div class="col-md-8">
                                        <?php if (isset($WPHRMHideShowEmployeeSectionInfo['other-details']) && $WPHRMHideShowEmployeeSectionInfo['other-details'] == '0') { ?>
                                        <input  type="checkbox"    class="make-switch other-checked" name="other-details"
                                               data-on-color="success"  data-on-text="<?php _e('Show', 'wphrm'); ?>" data-off-text="<?php _e('Hide', 'wphrm'); ?>" data-off-color="danger">
                                        <?php } else { ?>
                                        <input  type="checkbox"    class="make-switch other-checked" name="other-details"
                                               data-on-color="success" checked="checked" data-on-text="<?php _e('Show', 'wphrm'); ?>" data-off-text="<?php _e('Hide', 'wphrm'); ?>" data-off-color="danger">
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" value="" class="bank-checked-yes" name="bank-checked-name">
                            <input type="hidden" value="" class="salary-checked-yes" name="salary-checked-name">
                            <input type="hidden" value="" class="documents-checked-yes" name="documents-checked-name">
                            <input type="hidden" value="" class="other-checked-yes" name="other-checked-name">
                        </form>

                    </div>
                </div>

            </div>
            <div class="col-md-6 col-sm-6">

                <div class="portlet box  blue" style="border: none;">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-upload"></i><?php _e('Add More Upload Documents', 'wphrm'); ?>
                        </div>
                        <div class="actions">
                            <a href="javascript:;"  onclick="jQuery('#wphrmEmployeeDocumentsForm').submit();" data-loading-text="Updating..."  class="demo-loading-btn btn btn-sm btn-default ">
                                <i class="fa fa-save" ></i><?php _e('Save', 'wphrm'); ?> </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="alert alert-success display-hide" id="wphrmdocumentFields_success"><i class='fa fa-check-square' aria-hidden='true'></i><?php _e('Documents label Settings has been successfully updated.', 'wphrm'); ?>
                            <button class="close" data-close="alert"></button>
                        </div>
                        <div class="alert alert-danger display-hide" id="wphrmdocumentFields_error">
                            <button class="close" data-close="alert"></button>
                        </div>
                        <form method="POST"  accept-charset="UTF-8" class="form-horizontal" id="wphrmEmployeeDocumentsForm" enctype="multipart/form-data">
                            <div class="form-body">
                                <h3 class="page-title" style="text-align: center;">  <?php _e('Upload Employee Documents', 'wphrm'); ?> </h3>
                                <div class="form-group">
                                    <div class="col-md-10">
                                        <input class="form-control form-control-inline" name="wphrmDefaultDocumentsLabel[]" id="documentsfieldslebal" value="<?php echo esc_html($wphrmDefaultDocumentsLabel['resume']); ?>"  placeholder="<?php _e('Document Field Label', 'wphrm'); ?>"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-10">
                                        <input class="form-control form-control-inline" name="wphrmDefaultDocumentsLabel[]" id="documentsfieldslebal" value="<?php echo esc_html($wphrmDefaultDocumentsLabel['offerLetter']); ?>"  placeholder="<?php _e('Document Field Label', 'wphrm'); ?>"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-10">
                                        <input class="form-control form-control-inline" name="wphrmDefaultDocumentsLabel[]" id="documentsfieldslebal" value="<?php echo esc_html($wphrmDefaultDocumentsLabel['joiningLetter']); ?>"  placeholder="<?php _e('Document Field Label', 'wphrm'); ?>"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-10">
                                        <input class="form-control form-control-inline" name="wphrmDefaultDocumentsLabel[]" id="documentsfieldslebal" value="<?php echo esc_html($wphrmDefaultDocumentsLabel['contractAndAgreement']); ?>"  placeholder="<?php _e('Document Field Label', 'wphrm'); ?>"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-10">
                                        <input class="form-control form-control-inline" name="wphrmDefaultDocumentsLabel[]" id="documentsfieldslebal" value="<?php echo esc_html($wphrmDefaultDocumentsLabel['iDProof']); ?>"  placeholder="<?php _e('Document Field Label', 'wphrm'); ?>"/>
                                    </div>
                                </div>
                                <?php
                                $employeeDocumentsfieldsInfo = $this->WPHRMGetSettings('employeeDocumentsfieldskey');
                                if (!empty($employeeDocumentsfieldsInfo)) {
                                    $i = 1;
                                    foreach ($employeeDocumentsfieldsInfo['documentsfieldslebal'] as $employeeDocumentsfieldsInfos) {
                                ?>
                                <div class="form-group <?php echo 'removefiled' . esc_attr($i) . 'Documentsfieldslebal'; ?>">
                                    <div class="col-md-8">
                                        <input class="form-control form-control-inline" name="documentsfieldslebal[]" id="documentsfieldslebal" value="<?php
                                        if (isset($employeeDocumentsfieldsInfos)): echo trim(esc_attr($employeeDocumentsfieldsInfos));
                                        endif;
                                        ?>"  placeholder="<?php _e('Document Field Label', 'wphrm'); ?>"/>
                                    </div>
                                    <div class="col-md-2">
                                        <a   onclick="deleteEarningAndDedutions('<?php echo esc_js($i) . 'Documentsfieldslebal'; ?>');" data-loading-text="Updating..."  class="btn red">
                                            <i class='fa fa-trash' aria-hidden='true'></i></a>
                                    </div>
                                </div>

                                <?php
                                        $i++;
                                    }
                                }
                                ?>
                                <div id="documentsfieldslebalBefore"></div>
                                <button type="button" class="btn btn-sm blue form-control-inline" id="documentsfieldslebaladd" style="text-align: center;">
                                    <i class="fa fa-plus"></i><?php _e('Add More', 'wphrm'); ?>
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
                <div class="portlet box blue" style="border: none;">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-link"></i><?php _e('Add More Other Details Fields', 'wphrm'); ?>
                        </div>
                        <div class="actions">
                            <a href="javascript:;"  onclick="jQuery('#wphrmotherDetailsFieldsInfoForm').submit();" data-loading-text="Updating..."  class="demo-loading-btn btn btn-sm btn-default ">
                                <i class="fa fa-save" ></i><?php _e('Save', 'wphrm'); ?> </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="alert alert-success display-hide" id="wphrmotherfield_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrmMessagesUpdateEarnings); ?>
                            <button class="close" data-close="alert"></button>
                        </div>
                        <div class="alert alert-danger display-hide" id="wphrmotherfield_error">
                            <button class="close" data-close="alert"></button>
                        </div>
                        <form method="POST"  accept-charset="UTF-8" class="form-horizontal" id="wphrmotherDetailsFieldsInfoForm" enctype="multipart/form-data">
                            <div class="form-body">
                                <h3 class="page-title" style="text-align: center;">  <?php _e('Other Details Fields', 'wphrm'); ?> </h3>
                                <?php
                                $otherfieldskeyInfo = $this->WPHRMGetSettings('Otherfieldskey');
                                if (!empty($otherfieldskeyInfo)) {
                                    $i = 1;
                                    foreach ($otherfieldskeyInfo['Otherfieldslebal'] as $otherfieldsSettings) {
                                ?>
                                <div class="form-group <?php echo 'removefiled' . esc_attr($i) . 'otherfieldslebal'; ?>">
                                    <div class="col-md-8">
                                        <input class="form-control form-control-inline" name="other-fields-lebal[]" id="other-fields-lebal" value="<?php
                                        if (isset($otherfieldsSettings)): echo trim(esc_attr($otherfieldsSettings));
                                        endif;
                                                                                                                                                   ?>"  placeholder="<?php _e('Other Field Label', 'wphrm'); ?>"/>
                                    </div>
                                    <div class="col-md-2">
                                        <a   onclick="deleteEarningAndDedutions('<?php echo esc_js($i) . 'otherfieldslebal'; ?>');" data-loading-text="Updating..."  class="btn red">
                                            <i class='fa fa-trash' aria-hidden='true'></i></a>
                                    </div>
                                </div>

                                <?php
                                        $i++;
                                    }
                                }
                                ?>
                                <div id="other-fields-lebal-Before"></div>
                                <button type="button" class="btn btn-sm blue form-control-inline" id="add-other-fields-lebal" style="text-align: center;">
                                    <i class="fa fa-plus"></i><?php _e('Add More', 'wphrm'); ?>
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

        </div></div>
    <div id="salary-settings" class="tabcontent">
        <div class="row ">
            <div class="col-md-6 col-sm-6">
                <div class="portlet box blue" style="border: none;">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-clock-o"></i><?php _e('Salary By Month/ Week/ Hours', 'wphrm'); ?>
                        </div>
                        <div class="actions">
                            <a href="javascript:;"  onclick="jQuery('#wphrsalaryDayOrHourlyForm').submit();" data-loading-text="Updating..."  class="demo-loading-btn btn btn-sm btn-default ">
                                <i class="fa fa-save" ></i><?php _e('Save', 'wphrm'); ?> </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="alert alert-success display-hide" id="wphrsalaryDayOrHourlySuccess"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrm_messages_salary_slip_setting); ?>
                            <button class="close" data-close="alert"></button>
                        </div>
                        <div class="alert alert-danger display-hide" id="wphrsalaryDayOrHourlyError">
                            <button class="close" data-close="alert"></button>
                        </div>
                        <form method="POST"  accept-charset="UTF-8" class="form-horizontal" id="wphrsalaryDayOrHourlyForm">
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e('Based on', 'wphrm'); ?></label>
                                    <div class="col-md-8">
                                        <div class="radio-list" data-error-container="#form_2_membership_error">
                                            <input  style="margin: 9px 4px 10px;" name="wphrm-according" type="radio" <?php
                                                   if (isset($wphrmSalaryAccording['wphrm-according']) && $wphrmSalaryAccording['wphrm-according'] == 'Day') : echo esc_attr('checked');
                                                   endif;
                                                   ?> id="wphrm-according" value="Day" checked>&nbsp;<?php _e('Days', 'wphrm'); ?> &nbsp;&nbsp;&nbsp;&nbsp;

                                            <input  style="margin: 9px 4px 10px;" name="wphrm-according" type="radio" <?php
                                                   if (isset($wphrmSalaryAccording['wphrm-according']) && $wphrmSalaryAccording['wphrm-according'] == 'Week') : echo esc_attr('checked');
                                                   endif;
                                                   ?> id="wphrm-according" value="Week">&nbsp;<?php _e('Week', 'wphrm'); ?> &nbsp;&nbsp;&nbsp;&nbsp;

                                            <input style="margin: 9px 4px 10px;" name="wphrm-according" type="radio" id="wphrm-according" <?php
                                                   if (isset($wphrmSalaryAccording['wphrm-according']) && $wphrmSalaryAccording['wphrm-according'] == 'Hourly') : echo esc_attr('checked');
                                                   endif;
                                                   ?> value="Hourly" >&nbsp;<?php _e('Hours', 'wphrm'); ?>
                                        </div></div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="portlet box blue" style="border: none;">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-money"></i><?php _e('Salary Slip Layout', 'wphrm'); ?>
                        </div>
                        <div class="actions">
                            <a href="javascript:;"  onclick="jQuery('#wphrmSalarySlipInfo_form').submit();" data-loading-text="Updating..."  class="demo-loading-btn btn btn-sm btn-default ">
                                <i class="fa fa-save" ></i><?php _e('Save', 'wphrm'); ?> </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="alert alert-success display-hide" id="wphrmSalarySlipInfo_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrm_messages_salary_slip_setting); ?>
                            <button class="close" data-close="alert"></button>
                        </div>
                        <div class="alert alert-danger display-hide" id="wphrmSalarySlipInfo_error">
                            <button class="close" data-close="alert"></button>
                        </div>
                        <div class="form-body">
                            <form method="POST"  accept-charset="UTF-8" class="form-horizontal" id="wphrmSalarySlipInfo_form">
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e('Header Logo Align', 'wphrm'); ?></label>
                                    <div class="col-md-8">
                                        <select class="bs-select form-control" data-show-subtext="true" name="wphrm_logo_align">
                                            <option value="">--<?php _e('Select', 'wphrm'); ?>--</option>
                                            <option <?php
                                                    if (isset($wphrmSalarySlipInfo['wphrm_logo_align']) && $wphrmSalarySlipInfo['wphrm_logo_align'] == 'left') : echo esc_attr('selected = "selected"');
                                                    endif;
                                                    ?> value="left"><?php _e('Left', 'wphmr'); ?></option>
                                            <option <?php
                                                    if (isset($wphrmSalarySlipInfo['wphrm_logo_align']) && $wphrmSalarySlipInfo['wphrm_logo_align'] == 'center') : echo esc_attr('selected = "selected"');
                                                    endif;
                                                    ?> value="center"><?php _e('Center', 'wphrm'); ?></option>
                                            <option <?php
                                                    if (isset($wphrmSalarySlipInfo['wphrm_logo_align']) && $wphrmSalarySlipInfo['wphrm_logo_align'] == 'right') : echo esc_attr('selected = "selected"');
                                                    endif;
                                                    ?> value="right"><?php _e('Right', 'wphmr'); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e('Content', 'wphrm'); ?></label>
                                    <div class="col-md-8">
                                        <textarea class="form-control" placeholder="<?php _e('Content', 'wphrm'); ?>" name="wphrm_slip_content" rows="2" id="wphrm_slip_content"><?php
                                            if (isset($wphrmSalarySlipInfo['wphrm_slip_content'])) : echo esc_textarea($wphrmSalarySlipInfo['wphrm_slip_content']);
                                            endif;
                                            ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e('Footer Content Align', 'wphrm'); ?></label>
                                    <div class="col-md-8">
                                        <select class="bs-select form-control" data-show-subtext="true" name="wphrm_footer_content_align">
                                            <option value="">--<?php _e('Select', 'wphrm'); ?>--</option>
                                            <option <?php
                                                    if (isset($wphrmSalarySlipInfo['wphrm_footer_content_align']) && $wphrmSalarySlipInfo['wphrm_footer_content_align'] == 'left') : echo esc_attr('selected = "selected"');
                                                    endif;
                                                    ?> value="left"><?php _e('Left', 'wphrm'); ?></option>
                                            <option <?php
                                                    if (isset($wphrmSalarySlipInfo['wphrm_footer_content_align']) && $wphrmSalarySlipInfo['wphrm_footer_content_align'] == 'right') : echo esc_attr('selected = "selected"');
                                                    endif;
                                                    ?> value="right"><?php _e('Right', 'wphrm'); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <!--   Default border color #CFD8DC-->
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e('Border Color', 'wphrm'); ?></label>
                                    <div class="col-md-8">
                                        <input class="form-control jscolor"    name="wphrm_border_color" type="text" id="wphrm_border_color" value="<?php
                                                                                                                                                    if (isset($wphrmSalarySlipInfo['wphrm_border_color'])) : echo esc_attr($wphrmSalarySlipInfo['wphrm_border_color']);
                                                                                                                                                    endif;
                                                                                                                                                    ?>" autocapitalize="none"  />
                                    </div>
                                </div>
                                <!--  Default h1 color #ECEFF1-->
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e('H1 Background Color', 'wphrm'); ?></label>
                                    <div class="col-md-8">
                                        <input class="form-control jscolor"  name="wphrm_background_color" type="text" id="wphrm_background_color" value="<?php
                                if (isset($wphrmSalarySlipInfo['wphrm_background_color'])) : echo esc_attr($wphrmSalarySlipInfo['wphrm_background_color']);
                                endif;
                                ?>" autocapitalize="none"  />
                                    </div>
                                </div>
                                <!--   Default font color #546E7A-->
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e('Font Color', 'wphrm'); ?></label>
                                    <div class="col-md-8">
                                        <input class="form-control jscolor"  name="wphrm_font_color" type="text" id="wphrm_font_color" value="<?php
                                                                                                                                              if (isset($wphrmSalarySlipInfo['wphrm_font_color'])) : echo esc_attr($wphrmSalarySlipInfo['wphrm_font_color']);
                                                                                                                                              endif;
                                                                                                                                              ?>" autocapitalize="none"  />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e('Currency Decimal', 'wphrm'); ?></label>
                                    <div class="col-md-8">
                                        <input class="form-control"  name="wphrm_currency_decimal" type="text" id="wphrm_currency_decimal" value="<?php
                                                                                                                                                  if (isset($wphrmSalarySlipInfo['wphrm_currency_decimal']) && $wphrmSalarySlipInfo['wphrm_currency_decimal'] != '') : echo esc_attr($wphrmSalarySlipInfo['wphrm_currency_decimal']);
                                                                                                                                                  else :
                                                                                                                                                  echo '2';
                                                                                                                                                  endif;
                                                                                                                                                  ?>" autocapitalize="none"  />
                                    </div>
                                </div>
                            </form>
                            <form id="salary_slip_settings_reset" class="search-form" method="post">
                                <input name="font_color" type="hidden"  id="font_color" value="546e7a">
                                <input name="logo_align" type="hidden"  id="logo_align" value="left">
                                <input name="footer_align" type="hidden"  id="footer_align" value="right">
                                <input name="border_color" type="hidden"  id="border_color" value="cfd8dc">
                                <input name="h1_color"  type="hidden"  id="h1_color" value="ECEFF1">
                                <input name="currency_decimal"  type="hidden"  id="currency_decimal" value="2">
                                <div class="form-group " style=" text-align: center;">
                                    <label class="col-md control-label"></label>
                                    <div class="col-md" style="margin-left: -76px;">
                                        <button class="btn blue" type="submit" ><i class="fa fa-refresh fa-6"></i><?php _e('Reset', 'wphrm'); ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-md-6 col-sm-6">

                <div class="portlet box  blue" style="border: none;">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-cog fa-fw"></i><?php _e('Salary Slip Fields', 'wphrm'); ?>
                        </div>
                        <div class="actions">
                            <a href="javascript:;" onclick="jQuery('#wphrmSalarySlipFieldsInfoForm').submit();" data-loading-text="Updating..."  class="demo-loading-btn btn btn-sm btn-default ">
                                <i class="fa fa-save" ></i> <?php _e('Save', 'wphrm'); ?> </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="alert alert-success display-hide" id="wphrmsalaryslipfield_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrmMessagesUpdateEarnings); ?>
                            <button class="close" data-close="alert"></button>
                        </div>
                        <div class="alert alert-danger display-hide" id="wphrmsalaryslipfield_error">
                            <button class="close" data-close="alert"></button>
                        </div>
                        <form method="POST"  accept-charset="UTF-8" class="form-horizontal" id="wphrmSalarySlipFieldsInfoForm" enctype="multipart/form-data">
                            <div class="form-body">
                                <h3 class="page-title" style="text-align: center;">  <?php _e('Earnings', 'wphrm'); ?> </h3>
                                <?php
                                $wphrmEarningInfo = $this->WPHRMGetSettings('wphrmEarningInfo');

                                if (!empty($wphrmEarningInfo)) {
                                    $i = 1;
                                    if (isset($wphrmEarningInfo['earningLebal']) && isset($wphrmEarningInfo['earningtype']) && isset($wphrmEarningInfo['earningamount']))
                                        foreach ($wphrmEarningInfo['earningLebal'] as $earningLebal => $wphrmEarningsettingInfo) {
                                            foreach ($wphrmEarningInfo['earningtype'] as $earningtype => $wphrmEarningtype) {
                                                foreach ($wphrmEarningInfo['earningamount'] as $earningamount => $wphrmEarningamountInfo) {
                                                    if ($earningLebal == $earningtype && $earningLebal == $earningamount && $earningtype == $earningamount) {
                                ?>
                                <div class="form-group  <?php echo 'removefiled' . esc_attr($i) . 'earningLebal'; ?>">
                                    <div class="col-md-4">
                                        <input class="form-control form-control-inline" name="earninglebal[]" id="earninglebal" value="<?php
                                                        if (isset($wphrmEarningsettingInfo)): echo trim(esc_attr($wphrmEarningsettingInfo));
                                                        endif;
                                                                                                                                       ?>"  placeholder="<?php _e('Earming Label', 'wphmr'); ?>"/>
                                    </div>

                                    <div class="col-md-4">
                                        <input disabled class="form-control form-control-inline" value="<?php _e('% of Current Salary', 'wphmr'); ?>"/>
                                        <input type="hidden" name="earningtype[]" id="earningtype" value="<?php echo $wphrmEarningtype; ?>"/>
                                    </div>

                                    <div class="col-md-2">
                                        <input class="form-control form-control-inline wphrm-salary-persanage validationonnumber" name="earningamount[]" id="earninglebal" value="<?php
                                                        if (isset($wphrmEarningamountInfo)): echo trim(esc_attr($wphrmEarningamountInfo));
                                                        endif;
                                                        ?>"/>
                                    </div>

                                    <div class="col-md-1">
                                        <a   onclick="deleteEarningAndDedutions('<?php echo esc_js($i) . 'earningLebal'; ?>');" data-loading-text="Updating..."  class="btn red">
                                            <i class='fa fa-trash' aria-hidden='true'></i></a>

                                    </div>

                                </div>

                                <?php
                                                        $i++;
                                                    }
                                                }
                                            }
                                        }
                                }
                                ?>
                                <div id="earninglebalinsertBefore"></div>
                                <button type="button" class="btn btn-sm blue form-control-inline" id="addearninglebal" style="text-align: center;">
                                    <i class="fa fa-plus"></i><?php _e('Add More', 'wphrm'); ?>
                                </button>
                            </div>
                            <div class="form-body">
                                <h3 class="page-title" style="text-align: center;">  <?php _e('Deductions', 'wphrm'); ?> </h3>
                                <?php
                                $wphrmDeductionInfo = $this->WPHRMGetSettings('wphrmDeductionInfo');

                                if (isset($wphrmDeductionInfo['deductionlebal']) && isset($wphrmDeductionInfo['deductiontype']) && isset($wphrmDeductionInfo['deductionamount'])) {
                                    $i = 1;
                                    foreach ($wphrmDeductionInfo['deductionlebal'] as $deductionLebal => $wphrmDedutionsettingInfo) {
                                        foreach ($wphrmDeductionInfo['deductiontype'] as $deductiontype => $wphrmDeductiontype) {
                                            foreach ($wphrmDeductionInfo['deductionamount'] as $deductionamount => $wphrmDeductionamountInfo) {
                                                if ($deductionLebal == $deductiontype && $deductionLebal == $deductionamount && $deductiontype == $deductionamount) {
                                ?>
                                <div class="form-group <?php echo 'removefiled' . esc_attr($i) . 'deductionlebal'; ?>">
                                    <div class="col-md-4">
                                        <input class="form-control form-control-inline" name="deductionlebal[]" id="deductionlebal" value="<?php
                                                    if (isset($wphrmDedutionsettingInfo)): echo trim(esc_attr($wphrmDedutionsettingInfo));
                                                    endif;
                                                                                                                                           ?>"  placeholder="Deductions Label"/>
                                    </div>

                                    <div class="col-md-4">
                                        <input disabled class="form-control form-control-inline" value="<?php _e('% of Current Salary', 'wphmr'); ?>"/>
                                        <input type="hidden" name="deductiontype[]" id="deductiontype" value="<?php echo $wphrmDeductiontype; ?>"/>
                                    </div>

                                    <div class="col-md-2">
                                        <input class="form-control form-control-inline wphrm-salary-persanage validationonnumber" name="deductionamount[]" id="deductionamount" value="<?php
                                                    if (isset($wphrmDeductionamountInfo)): echo trim(esc_attr($wphrmDeductionamountInfo));
                                                    endif;
                                                    ?>"/>
                                    </div>

                                    <div class="col-md-2">
                                        <a   onclick="deleteEarningAndDedutions('<?php echo esc_js($i) . 'deductionlebal'; ?>');" data-loading-text="Updating..."  class="btn red">
                                            <i class='fa fa-trash' aria-hidden='true'></i></a>

                                    </div>
                                </div>

                                <?php
                                                    $i++;
                                                }
                                            }
                                        }
                                    }
                                }
                                ?>
                                <div id="deductionlebalinsertBefore"></div>
                                <button type="button" class="btn btn-sm blue form-control-inline" id="adddeductionlebal" style="text-align: center;">
                                    <i class="fa fa-plus"></i><?php _e('Add More', 'wphrm'); ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <link rel="stylesheet" href="https://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />
                <div class="portlet box  blue" style="border: none;">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-download"></i><?php _e('Salary Generation ', 'wphrm'); ?>
                        </div>
                        <div class="actions">
                            <a href="javascript:;"  onclick="jQuery('#wphrSalaryGenerationForm').submit();" data-loading-text="Updating..."  class="demo-loading-btn btn btn-sm btn-default ">
                                <i class="fa fa-save" ></i><?php _e('Save', 'wphrm'); ?> </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="alert alert-success display-hide" id="wphrSalaryGenerationSuccess"><i class='fa fa-check-square' aria-hidden='true'></i><?php _e('Salary generation settings have been successfully updated. ', 'wphrm'); ?>
                            <button class="close" data-close="alert"></button>
                        </div>
                        <div class="alert alert-danger display-hide" id="wphrSalaryGenerationError">
                            <button class="close" data-close="alert"></button>
                        </div>
                        <form method="POST"  accept-charset="UTF-8" class="form-horizontal" id="wphrSalaryGenerationForm">
                            <div class="form-body">
                                <div class="form-group " style="margin-top: 13px;">
                                    <div class="col-md-2"><span class="label label-danger span-padding" style="margin-left: 45px;"><?php _e('NOTE', 'wphrm'); ?> !</span></div>
                                    <div class="col-md-8 notice-info"><?php _e("In order to generate salary slips automatically, please enter <a href='?page=wphrm-employees' id='content'></a>current salary in their profile.", 'wphrm'); ?></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e('Day of the month', 'wphrm'); ?></label>
                                    <div class="col-md-8">
                                        <input name="monthdate" placeholder="<?php _e('0 to 31 days', 'wphrm'); ?>" style="padding: 7px; height: 39px;"class="form-control form-control-inline" value="<?php
                                if (isset($wphrmGenerationSetting['monthdate']) && $wphrmGenerationSetting['monthdate']) : echo esc_textarea($wphrmGenerationSetting['monthdate']);
                                else :
                                    echo 1;
                                endif;
                                ?>" type="number" min="1" max="31" step="1">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e('Default Information on salary slips', 'wphrm'); ?></label>
                                    <div class="col-md-8">
                                        <textarea class="form-control" name="informationtext" rows="2" id="informationtext"><?php
                                            if (isset($wphrmGenerationSetting['informationtext'])) : echo esc_textarea($wphrmGenerationSetting['informationtext']);
                                            endif;
                                            ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div id="import-export-settings" class="tabcontent">
        <div class="row ">
            <div class="col-md-6 col-sm-6">
                <div class="portlet box blue" style="border: none;">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-file"></i><?php _e('Export Database', 'wphrm'); ?>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="alert alert-success display-hide" id="Export_success"><i class='fa fa-check-square' aria-hidden='true'></i>
                            <button class="close" data-close="alert"></button>
                        </div>
                        <div class="alert alert-danger display-hide" id="Export_error">
                            <button class="close" data-close="alert"></button>
                        </div>
                        <form  accept-charset="UTF-8" class="form-horizontal" id="">
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e('Export File', 'wphrm'); ?></label>
                                    <div class="col-md-8">
                                        <a class="btn blue" onclick="wphrmImportAndExport();" ><i class="fa fa-file"></i><?php _e('Export', 'wphrm'); ?></a>
                                    </div>

                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <div class="col-md-6 col-sm-6">
                <div class="portlet box  blue" style="border: none;">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-file"></i><?php _e('Import Database', 'wphrm'); ?>
                        </div>
                    </div>
                    <div class="portlet-body" style="padding: 14px;">
                        <div class="alert alert-success display-hide" id="import_success"><i class='fa fa-check-square' aria-hidden='true'></i>
                            <button class="close" data-close="alert"></button>
                        </div>
                        <div class="alert alert-danger display-hide" id="import_error">
                            <button class="close" data-close="alert"></button>
                        </div>
                        <form method="POST"  accept-charset="UTF-8" class="form-horizontal" id="wphrmimportxmlInfo_frm">
                            <div class="form-body">
                                <div class="form-group add-maergin">
                                    <div class="col-md-9">
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <div class="input-group input-large">
                                                <div class="form-control uneditable-input" data-trigger="fileinput">
                                                    <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename"><?php
                                                    if (isset($resumeDir) && $resumeDir != '') : $resumeExt = pathinfo($resumeDir, PATHINFO_EXTENSION);
                                                    echo esc_html(mb_strimwidth($resumeDir, 0, 10) . '....' . $resumeExt);
                                                    endif;
                                                    ?></span>
                                                </div>
                                                <span class="input-group-addon btn default btn-file">
                                                    <span class="fileinput-new">
                                                        <?php _e('Select file', 'wphrm'); ?> </span>
                                                    <span class="fileinput-exists">
                                                        <?php _e('Change', 'wphrm'); ?> </span>
                                                    <input type="file" name="wphrm_import" class="documents-Upload" id="wphrm_import">
                                                </span>
                                                <a href="#" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput">
                                                    <?php _e('Remove', 'wphrm'); ?> </a>
                                            </div>
                                        </div></div>
                                    <div class="col-md-2">
                                        <a class="btn blue" style="margin-left: 19px;" href="#warningmsg"  data-toggle="modal"><i class="fa fa-file"></i><?php _e('Import', 'wphrm'); ?></a>
                                    </div>

                                </div>

                            </div>
                            <div id="warningmsg" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
                                            <h4 class="modal-title"><?php _e('Confirmation', 'wphrm'); ?></h4>
                                        </div>
                                        <div class="modal-body" id="info"><p><?php _e('Note : Current database will be replaced with imported database.', 'wphrm'); ?></p></div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn blue"  ><i class="fa fa-check"></i><?php _e('I agree', 'wphrm'); ?> </button>
                                            <button type="button" data-dismiss="modal" id="checkdata" aria-hidden="true" class="btn default"><i class="fa fa-times"></i><?php _e('Cancel', 'wphrm'); ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>

            </div>

        </div>
    </div>

    <div id="permission-settings" class="tabcontent">
        <div class="row ">
            <div class="col-md-6 col-sm-6">
                <?php if (isset($current_user->roles) &&  in_array('administrator', $current_user->roles)) { ?>
                <div class="portlet box  blue" style="border: none;">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-user"></i><?php _e('Role Permissions', 'wphrm'); ?>
                        </div>
                        <div class="actions">
                            <a href="javascript:;"  onclick="jQuery('#wphrmRolePermissionForm').submit();" data-loading-text="Updating..."  class="demo-loading-btn btn btn-sm btn-default ">
                                <i class="fa fa-save" ></i><?php _e('Save', 'wphrm'); ?> </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="alert alert-success display-hide" id="addroles_settings_success"><i class='fa fa-check-square' aria-hidden='true'></i>
                            <button class="close" data-close="alert"></button>
                        </div>
                        <div class="alert alert-danger display-hide" id="addroles_settings_error">
                            <button class="close" data-close="alert"></button>
                        </div>
                        <div class="form-body">

                            <form method="POST"  accept-charset="UTF-8" class="form-horizontal" id="wphrmRolePermissionForm">
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e('Role Action', 'wphrm'); ?></label>
                                    <div class="col-md-8">
                                        <a class="btn blue" onclick="wphrmRoleAction('add')"><i class="fa fa-plus"></i><?php _e('Add', 'wphrm'); ?> </a>
                                        <a class="btn blue"  onclick="wphrmRoleAction('edit')"><i class="fa fa-edit"></i><?php _e('Edit', 'wphrm'); ?> </a>
                                    </div>
                                </div>
                                <div class="edit-role">
                                    <div class="form-group" >
                                        <label class="col-md-4 control-label"><?php _e('Roles', 'wphrm'); ?></label>
                                        <div class="col-md-8">
                                            <select class="bs-select form-control wphrm_user_permission" data-show-subtext="true" name="wphrm_user_permission" id="rolenameget">

                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="add-role">
                                    <div class="form-group">
                                        <label class="col-md-4 control-label"><?php _e('Role Name', 'wphrm'); ?></label>
                                        <div class="col-md-8">
                                            <input class="form-control"  name="wphrm_rolename" type="text" id="wphrm_rolename">
                                        </div>
                                    </div>
                                </div>
                                <table class="table table-striped table-bordered table-hover role-permission-show" id="role-permission-show">
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div id="message-settings" class="tabcontent">
        <div class="row ">
            <div class="col-md-12 col-sm-12">

                <div class="portlet box blue" style="border: none;">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-envelope"></i><?php _e('Messages', 'wphrm'); ?>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="form-body">
                            <table class="table table-striped table-bordered table-hover" id="wphrmDataTable">
                                <thead>
                                    <tr> <th><?php _e('S.No', 'wphrm'); ?></th>
                                        <th><?php _e('Message titles', 'wphrm'); ?></th>
                                        <th><?php _e('Message Descriptions', 'wphrm'); ?></th>
                                        <th><?php _e('Action', 'wphrm'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $wphrm_all_messages = $wpdb->get_results("SELECT * FROM $this->WphrmMessagesTable ORDER BY id ASC");
                                    foreach ($wphrm_all_messages as $key => $wphrm_all_message) {
                                    ?>
                                    <tr>
                                        <td><?php echo esc_html($i); ?></td>
                                        <td> <?php
                                        if (isset($wphrm_all_message->messagesTitle)) : echo esc_html($wphrm_all_message->messagesTitle);
                                        endif;
                                            ?> </td>
                                        <td> <?php
                                        if (isset($wphrm_all_message->messagesDesc)) : echo esc_html($wphrm_all_message->messagesDesc);
                                        endif;
                                            ?> </td>
                                        <td>
                                            <a class="btn purple" data-toggle="modal" href="#edit_message" onclick="edit_messages(<?php
                                        if (isset($wphrm_all_message->id)) : echo esc_js($wphrm_all_message->id);
                                        endif;
                                                                                                                    ?>, '<?php
                                        if (isset($wphrm_all_message->messagesTitle)) : echo trim(esc_js($wphrm_all_message->messagesTitle));
                                        endif;
                                                                                                                    ?>', '<?php
                                        if (isset($wphrm_all_message->messagesDesc)) : echo trim(esc_js($wphrm_all_message->messagesDesc));
                                        endif;
                                                                                                                    ?>')"> <i class="fa fa-edit"></i><?php _e('View', 'wphrm'); ?>/<?php _e('Edit', 'wphrm'); ?> </a>
                                        </td>
                                    </tr>
                                    <?php
                                        $i++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="email-settings" class="tabcontent">
        <div class="row ">
            <div class="col-md-12 col-sm-12">

                <div class="portlet box blue" style="border: none;">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-envelope"></i><?php _e('Email Notifications', 'wphrm'); ?>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="form-body">

                            <form action="" method="POST" role="form" id="wphrm_email_notification_settings_form" >
                                <legend>Update email notification contents</legend>
                                <div class="form-group">
                                    <label for="">Select Notication type</label>
                                    <?php $email_notifications = $this->get_email_notification_types(); ?>
                                    <select class="form-control" id="email-notification-type" name="email-notification-type" autocomplete="off" >
                                        <option value="" >Please select</option>
                                        <?php foreach($email_notifications as $key => $notification): ?>
                                        <option value="<?php echo $key; ?>" ><?php echo isset($notification['title']) ? $notification['title'] : '' ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label >Email Subject</label>
                                    <input type="text" value="" class="form-control" name="email_notification_subject" >
                                </div>
                                <div class="form-group">
                                    <label >Email Content</label>
                                    <textarea name="email_notification_content" class="form-control" rows="15" ></textarea>
                                    <p class="description">HTML is allowed.</p>
                                </div>
                                <div class="form-group">
                                    <label >Email Variables</label>
                                    <p class="description alert alert-info" id="email_notification_variables"></p>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>


<?php $tooltiphover = plugins_url('assets/images/tooltiphover.png', __FILE__); ?>
<script src="https://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
<script>
    jQuery(document).ready(function() {
        jQuery("#content").html('<a id="riverroad" href="?page=wphrm-employees" title="" > employees </a>');
        jQuery("#content #riverroad").tooltip({ content: '<img src="<?php echo $tooltiphover; ?>" />' });

    });
    jQuery(function (argument) {
        jQuery('[type="checkbox"]').bootstrapSwitch();
    });
</script>
