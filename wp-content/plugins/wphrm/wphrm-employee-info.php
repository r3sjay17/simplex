<?php
if (!defined('ABSPATH'))
    exit;
global $current_user, $wpdb;
$wphrmEmployeeFirstName = '';
$wphrmUserRole = implode(',', $current_user->roles);
$readonly_class = '';
$readonly = 'readonly';
$edit_mode = false;
$wphrmUserAllRoles = $this->WPHRMGetUserRoles();
$wphrmGetPagePermissions = $this->WPHRMGetPagePermissions();
$wphrmEmployeeEditId = '';
if (isset($_REQUEST['employee_id']) && $_REQUEST['employee_id'] != '') {
    $wphrmEmployeeEditId = $_REQUEST['employee_id'];
} else {
    if (!in_array('manageOptionsEmployee', $wphrmGetPagePermissions)) {
        $wphrmEmployeeEditId = $current_user->ID;
    }
}
$userInformation = get_userdata($wphrmEmployeeEditId);
if (!empty($userInformation)) {
    $userInformationRoles = implode(',', $userInformation->roles);
} else {
    $userInformationRoles = array();
}

//echo $userInformationRoles; exit();
$wphrmEmployeeBasicInfo = $this->WPHRMGetUserDatas($wphrmEmployeeEditId, 'wphrmEmployeeInfo');
$wphrmEmployeeDocumentsInfo = $this->WPHRMGetUserDatas($wphrmEmployeeEditId, 'wphrmEmployeeDocumentInfo');
$wphrmMessagesPersonal = $this->WPHRMGetMessage(3);
$wphrmMessagesBank = $this->WPHRMGetMessage(4);
$wphrmMessagesDocuments = $this->WPHRMGetMessage(5);
$wphrmMessagesSalary = $this->WPHRMGetMessage(7);
$wphrmMessagesOther = $this->WPHRMGetMessage(6);
$wphrmHideShowEmployeeSectionSettings = $this->WPHRMGetSettings('WPHRMHideShowEmployeeSectionInfo');
$wphrmDefaultDocumentsLabel = $this->WPHRMGetDefaultDocumentsLabel();

$wphrmUserPermissionInfoformation = esc_sql('wphrmUserPermissionInfo'); // esc
$wphrmUserPermissionInfos = $wpdb->get_row("SELECT * FROM $this->WphrmSettingsTable WHERE `settingKey` = '$wphrmUserPermissionInfoformation'");
if (!empty($wphrmUserPermissionInfos)) {
    $wphrmUserPermissionInfo = unserialize(base64_decode($wphrmUserPermissionInfos->settingValue));
}
$resumeDir = '';
if (isset($wphrmEmployeeDocumentsInfo['resume']) && $wphrmEmployeeDocumentsInfo['resume'] != '') {
    $rdirs = explode('/', $wphrmEmployeeDocumentsInfo['resume']);
    $resumeDir = $rdirs[count($rdirs) - 1];
}
$offerDir = '';
if (isset($wphrmEmployeeDocumentsInfo['offerLetter']) && $wphrmEmployeeDocumentsInfo['offerLetter'] != '') {
    $rdirs = explode('/', $wphrmEmployeeDocumentsInfo['offerLetter']);
    $offerDir = $rdirs[count($rdirs) - 1];
}
$joiningDir = '';
if (isset($wphrmEmployeeDocumentsInfo['joiningLetter']) && $wphrmEmployeeDocumentsInfo['joiningLetter'] != '') {
    $rdirs = explode('/', $wphrmEmployeeDocumentsInfo['joiningLetter']);
    $joiningDir = $rdirs[count($rdirs) - 1];
}
$contractDir = '';
if (isset($wphrmEmployeeDocumentsInfo['contract']) && $wphrmEmployeeDocumentsInfo['contract'] != '') {
    $rdirs = explode('/', $wphrmEmployeeDocumentsInfo['contract']);
    $contractDir = $rdirs[count($rdirs) - 1];
}
$idProofDir = '';
if (isset($wphrmEmployeeDocumentsInfo['IDProof']) && $wphrmEmployeeDocumentsInfo['IDProof'] != '') {
    $rdirs = explode('/', $wphrmEmployeeDocumentsInfo['IDProof']);
    $idProofDir = $rdirs[count($rdirs) - 1];
}
$wphrmEmployeeSalaryInfo = $this->WPHRMGetUserDatas($wphrmEmployeeEditId, 'wphrmEmployeeSalaryInfo');
$wphrmEmployeeFirstName = get_user_meta($wphrmEmployeeEditId, 'first_name', true);
$wphrmEmployeeLastName = get_user_meta($wphrmEmployeeEditId, 'last_name', true);

$wphrmEmployeeBankInfo = $this->WPHRMGetUserDatas($wphrmEmployeeEditId, 'wphrmEmployeeBankInfo');
$wphrmEmployeeOtherInfo = $this->WPHRMGetUserDatas($wphrmEmployeeEditId, 'wphrmEmployeeOtherInfo');

$wphrmDesignationarr = array();
$wphrmDesignations = $wpdb->get_results("SELECT * FROM  $this->WphrmDesignationTable");
foreach ($wphrmDesignations as $key => $wphrmDesignation) {
    $wphrmDesignationarr[] = $wphrmDesignation->departmentID;
}

$employee_complete_info = $this->get_user_complete_info($wphrmEmployeeEditId);
/*J@F Codes*/
wp_enqueue_script('wphrm-jaf-custom-js');
$employee_statuses_array = null;
$employee_statuses = $wpdb->get_row("SELECT * FROM $this->WphrmSettingsTable WHERE `settingKey` = 'employee_statuses'");
if (!empty($employee_statuses)) {
    $employee_statuses_array = unserialize(base64_decode($employee_statuses->settingValue));
}else{
    $employee_statuses_array = array('Single', 'Married', 'Widow', 'Divorced', 'Separated');
}

$employee_races = null;
$employee_races_setting = $wpdb->get_row("SELECT * FROM $this->WphrmSettingsTable WHERE `settingKey` = 'employee_races'");
if (!empty($employee_races_setting)) {
    $employee_races = unserialize(base64_decode($employee_races_setting->settingValue));
}else{
    $employee_races = array('Singaporean', 'Canadian', 'Chinise', 'Taiwanese');
}

$employee_probition_periods = 6;
$employee_probition_period_setting = $wpdb->get_row("SELECT * FROM $this->WphrmSettingsTable WHERE `settingKey` = 'employee_probition_period'");
if (!empty($employee_probition_period_setting)) {
    $employee_probition_periods = unserialize(base64_decode($employee_probition_period_setting->settingValue));
}else{
    $employee_probition_periods = array(3,6);
}

//for other information
$employee_nationality_array = array();
$employee_nationalities = $wpdb->get_row("SELECT * FROM $this->WphrmSettingsTable WHERE `settingKey` = 'employee_nationality_list'");
if (!empty($employee_nationalities)) {
    $employee_nationality_array = unserialize(base64_decode($employee_nationalities->settingValue));
}else{
    $employee_nationality_array = $employee_races;
}
$employee_country_array = array();

$employee_country_array = $this->get_country_list( );
$employee_nationality = $this->get_nationality_list();

$wphrmMessagesFamily = 'Family Details Successfuly saved!';
$wphrmEmployeeFamilyInfo = $this->WPHRMGetUserDatas($wphrmEmployeeEditId, 'wphrmEmployeeFamilyInfo');


/*J@F END*/


?>


<div id="add_promotion_modal" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
                <h4 class="modal-title"><strong><i class="fa fa-edit"></i> <?php _e('Employee Promotion', 'wphrm'); ?></strong></h4>
            </div>
            <div class="modal-body">
                <div class="portlet-body form">
                    <div class="alert alert-success display-hide" id="wphrm_emp_promotion_success">
                        <i class='fa fa-check-square' aria-hidden='true'></i> Employee has been promoted!
                        <button class="close" data-close="alert"></button>
                    </div>
                    <div class="alert alert-danger display-hide" id="wphrm_emp_promotion_error">
                        <i class="fa fa-exclamation-triangle"></i> Error promoting employee!
                        <button class="close" data-close="alert"></button>
                    </div>
                    <!-- BEGIN FORM-->
                    <form method="POST"  accept-charset="UTF-8" class="form-horizontal" id="wphrm_emp_promotion_frm">
                        <div class="form-body">
                            <input  type="hidden" id="wphrm_emp_promotion_id" name="wphrm_emp_promotion_id" value="<?php echo esc_attr($wphrmEmployeeEditId); ?>"/>
                            <input  type="hidden" id="info_type" name="info_type" value="work_info"/>
                            
                            <div class="col-md-12 alert-holder label-danger" id="alert">The number of leave taken exceeded your leave balance. You are only allowed to use your remaining  days of leave</div>
                            <div class="clearfix"></div>

                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Initial Level', 'wphrm'); ?>  </label>
                                <div class="col-md-8">
                                    <?php  $employee_levels = $this->get_employee_types(); ?>   
                                    <!-- <input class="form-control wphrm_employee_initial_level" name="wphrm_employee_initial_level" readonly autocapitalize="none" type="text"> -->
                                    <select class="form-control select2me wphrm_employee_initial_level" name="wphrm_employee_initial_level" >
                                        <option value=""> <?php _e('-- not set --', 'wphrm'); ?></option>
                                        <?php foreach($employee_levels as $key => $employee_level): ?>
                                        <option value="<?php echo $key; ?>"><?php echo $employee_level['title']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Promotional Level', 'wphrm'); ?>  </label>
                                <div class="col-md-8">
                                    <select class="form-control select2me wphrm_employee_promotion_level" name="wphrm_employee_promotion_level" required >
                                        <option value=""> <?php _e('-- not set --', 'wphrm'); ?></option>
                                        <?php foreach($employee_levels as $key => $employee_level): ?>
                                        <option value="<?php echo $key; ?>"><?php echo $employee_level['title']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Date', 'wphrm'); ?>  </label>
                                <div class="col-md-8">
                                    <div class="input-group date date-picker bdate-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years" style="width: 100%; display: block;">
                                    <input class="form-control" name="wphrm_employee_promotion_date" autocapitalize="none" type="text" required>
                                    <span class="input-group-btn">
                                        <button class="btn default-date display-hide" type="button"><i class="fa fa-calendar" style="line-height: 1.9;"></i></button>
                                    </span>
                                </div>
                                </div>
                            </div>
                        
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-offset-3-XXX col-md-12 text-center">
                                    <button type="submit"  class="btn-submit btn blue"><i class="fa fa-edit"></i><?php _e('Promote', 'wphrm'); ?></button>
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



<!-- BEGIN PAGE HEADER-->
<div class="preloader">
    <span class="preloader-custom-gif"></span>
</div>
<input type="hidden" class="documents-hide-id" value="<?php
                                                      if (isset($wphrmHideShowEmployeeSectionSettings['documents-details'])) {
                                                          echo $wphrmHideShowEmployeeSectionSettings['documents-details'];
                                                      }
                                                      ?>">
<input type="hidden" class="bank-account-hide-id" value="<?php
                                                         if (isset($wphrmHideShowEmployeeSectionSettings['bank-account-details'])) {
                                                             echo $wphrmHideShowEmployeeSectionSettings['bank-account-details'];
                                                         }
                                                         ?>">
<input type="hidden" class="other-details-id" value="<?php
                                                     if (isset($wphrmHideShowEmployeeSectionSettings['other-details'])) {
                                                         echo $wphrmHideShowEmployeeSectionSettings['other-details'];
                                                     }
                                                     ?>">
<input type="hidden" class="salary-details-id" value="<?php
                                                      if (isset($wphrmHideShowEmployeeSectionSettings['salary-details'])) {
                                                          echo $wphrmHideShowEmployeeSectionSettings['salary-details'];
                                                      }
                                                      ?>">
<div style="padding-left: 0px; padding-right:20px; padding-top:0px;" class="col-md-12">
    <!-- BEGIN PAGE HEADER-->
    <h3 class="page-title"><?php _e('Employee', 'wphrm'); ?></h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-12">
            <div class="page-bar">
                <ul class="page-breadcrumb">
                    <li><i class="fa fa-home"></i><?php _e('Home', 'wphrm'); ?><i class="fa fa-angle-right"></i></li>
                    <li><?php _e('Employee', 'wphrm'); ?><i class="fa fa-angle-right"></i></li>
                    <li> <?php if (isset($wphrmEmployeeEditId) && $wphrmEmployeeEditId != '') { ?>
                        <?php _e('Edit Employee', 'wphrm'); ?>
                    <li> <i class="fa fa-angle-double-right"></i><strong><?php echo esc_html($wphrmEmployeeFirstName) . ' ' . esc_html($wphrmEmployeeLastName); ?></strong></li>
                    <?php } else { ?>
                    <?php _e('Add Employee', 'wphrm'); ?>
                    <?php } ?> </li>

                </ul>
        </div>
        <?php if (in_array('manageOptionsEmployee', $wphrmGetPagePermissions)) { ?>
        <a class="btn green " href="?page=wphrm-employees"><i class="fa fa-arrow-left"></i><?php _e('Back', 'wphrm'); ?> </a>
        <?php if (isset($_REQUEST['page']) && $_REQUEST['page'] != 'wphrm-employee-info' && isset($wphrmEmployeeEditId) && $wphrmEmployeeEditId != '') { ?>
        <a class="btn green " href="?page=wphrm-employee-info&employee_id=<?php echo esc_html($wphrmEmployeeEditId); ?>"><i class="fa fa-edit"></i><?php _e('Edit', 'wphrm'); ?> </a>
        <?php } else if (isset($_REQUEST['page']) && $_REQUEST['page'] != 'wphrm-employee-view-details' && isset($wphrmEmployeeEditId) && $wphrmEmployeeEditId != '') { ?>
        <a class="btn green " href="?page=wphrm-employee-view-details&employee_id=<?php echo esc_html($wphrmEmployeeEditId); ?>"><i class="fa fa-eye"></i><?php _e('View', 'wphrm'); ?> </a>
        <?php
                }
        ?>
            <?php if( isset($_GET['employee_id']) ) : ?>
                <a class="btn green btn-save-all-emp-info" href="?page=wphrm-employees" style="float:right"><i class="fa fa-save"></i><?php _e('Save', 'wphrm'); ?> </a>
            <?php endif; ?>
        <?php
        }
        ?>
        <div class="row ">
            <div class="col-md-6 col-sm-6">
                <div class="portlet box blue">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-edit"></i><?php _e('Personal Info', 'wphrm'); ?> <?php //echo empty($wphrmEmployeeEditId) ? ' <b>- SAVE THIS FIRST!</b>' : '' ?>
                        </div>

                        <!--<div class="actions">
                            <a href="javascript:;"  onclick="jQuery('#wphrm_employee_basic_info_form').submit();" data-loading-text="Updating..."  class="demo-loading-btn btn btn-sm btn-default ">
                                <i class="fa fa-save" ></i><?php _e('Save', 'wphrm'); ?>  </a>
                        </div>-->
                    </div>
                    <div class="portlet-body">
                        <div class="alert alert-success display-hide" id="personal_details_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrmMessagesPersonal); ?>
                            <button class="close" data-close="alert"></button>
                        </div>
                        <div class="alert alert-danger display-hide" id="personal_details_error">
                            <button class="close" data-close="alert"></button>
                        </div>
                        <form method="POST" accept-charset="UTF-8" class="form-horizontal" id="wphrm_employee_basic_info_form" enctype="multipart/form-data">

                            <input type="hidden" name="wphrm_employee_id" id="wphrm_employee_id"  value="<?php
                                                                                                         if (isset($wphrmEmployeeEditId)) : echo esc_attr($wphrmEmployeeEditId);
                                                                                                         endif;
                                                                                                         ?> "/>
                            <input type="hidden" name="info_type" value="personal_info" >

                            <div class="form-body">
                                <div class="form-group ">
                                    <label class="control-label col-md-4"><?php _e('Photo', 'wphrm'); ?> </label>
                                    <div class="col-md-8">
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <div class="fileinput-new thumbnail" style="width: 200px; height: auto;">
                                                <?php if (isset($wphrmEmployeeBasicInfo['employee_profile']) != '') { ?>
                                                <img src="<?php
    if (isset($wphrmEmployeeBasicInfo['employee_profile'])) : echo esc_attr($wphrmEmployeeBasicInfo['employee_profile']);
    endif;
                                                          ?>" width="200">
                                                <?php
}else {

    if (isset($wphrmEmployeeBasicInfo['wphrm_employee_gender']) && $wphrmEmployeeBasicInfo['wphrm_employee_gender'] == 'Male') {
                                                ?>
                                                <img src="<?php echo esc_attr(plugins_url('assets/images/default-male.jpeg', __FILE__)); ?>" width="200">
                                                <?php } else {
                                                ?>
                                                <img src="<?php echo esc_attr(plugins_url('assets/images/default-female.jpeg', __FILE__)); ?>" width="200">
                                                <?php
    }
}
                                                ?>
                                            </div>
                                            <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;">
                                            </div>
                                            <div>

                                                <span class="btn default btn-file">
                                                    <span class="fileinput-new">
                                                        <?php _e('Select image', 'wphrm'); ?></span>
                                                    <span class="fileinput-exists">
                                                        <?php _e('Change', 'wphrm'); ?></span>
                                                    <input type="file" name="employee_profile" id="employee_profile">
                                                </span>
                                                <a href="#" class="btn red fileinput-exists" data-dismiss="fileinput">
                                                    <?php _e('Remove', 'wphrm'); ?></a>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="col-md-4">
                                    </div>
                                    <div class="col-md-1"><span class="label label-danger span-padding"><?php _e('NOTE', 'wphrm'); ?> !</span></div> <div class="col-md-6 notice-info"><?php _e("Only 'jpeg', 'jpg', 'png' filetypes are allowed and size should be 117px*30px.", 'wphrm'); ?></div>
                                </div>

                                <?php if (in_array('manageOptionsEmployee', $wphrmGetPagePermissions)) { ?>
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e('First Name', 'wphrm'); ?><span class="required"></span></label>
                                    <div class="col-md-8">
                                        <input class="form-control"  name="wphrm_employee_fname" type="text" id="wphrm_employee_fname" value="<?php
                                                                                                        if (isset($wphrmEmployeeFirstName) && $wphrmEmployeeFirstName != '') : echo esc_attr($wphrmEmployeeFirstName);
                                                                                                        else : if (isset($userInformation->data->user_nicename)): echo esc_html($userInformation->data->user_nicename);
                                                                                                        endif;
                                                                                                        endif;
                                                                                                                                              ?>" autocapitalize="none"  />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e('Middle Name', 'wphrm'); ?></label>
                                    <div class="col-md-8">
                                        <input class="form-control" name="wphrm_employee_mname" type="text" id="wphrm_employee_mname" value="<?php if (isset($wphrmEmployeeBasicInfo['wphrm_employee_mname'])) : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_mname']); endif; ?>" autocapitalize="none"  />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e('Last Name', 'wphrm'); ?></label>
                                    <div class="col-md-8">
                                        <input class="form-control" name="wphrm_employee_lname" type="text" id="wphrm_employee_lname" value="<?php
                                                                                                        if (isset($wphrmEmployeeLastName)) : echo esc_attr($wphrmEmployeeLastName);
                                                                                                        endif;
                                                                                                                                             ?>" autocapitalize="none"  />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e('Email', 'wphrm'); ?></label>
                                    <div class="col-md-8">
                                        <input class="form-control" name="wphrm_employee_email" type="text" id="wphrm_employee_email" value="<?php
                                                                                                        if (isset($wphrmEmployeeBasicInfo['wphrm_employee_email']) && $wphrmEmployeeBasicInfo['wphrm_employee_email'] != '') : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_email']);
                                                                                                        else : if (isset($userInformation->data->user_email)): echo esc_html($userInformation->data->user_email);
                                                                                                        endif;
                                                                                                        endif;
                                                                                                                                             ?>" autocapitalize="none"  />
                                    </div>
                                </div>

                                <?php if(isset($wphrmUserRole) && $wphrmUserRole =='administrator'): ?>
                                <div class="form-group">
                                    <label class="control-label col-md-4"><?php _e('Approving Officer', 'wphrm'); ?>
                                    </label>
                                    <div class="col-md-8">
                                        <div class="radio-list" data-error-container="#form_2_membership_error">
                                            <!--<input  name="wphrm_employee_approving_officer" type="checkbox" <?php //checked(isset($wphrmEmployeeBasicInfo['wphrm_employee_approving_officer']) && $wphrmEmployeeBasicInfo['wphrm_employee_approving_officer'], 'Yes') ?> id="wphrm_employee_approving_officer" value="yes" class="icheck"> Is approving officer?-->
                                            <input  name="wphrm_employee_approving_officer" class="icheck" type="radio" <?php checked(isset($wphrmEmployeeBasicInfo['wphrm_employee_approving_officer']) && $wphrmEmployeeBasicInfo['wphrm_employee_approving_officer'] == 'Yes') ?> class="wphrm_employee_approving_officer" value="Yes">&nbsp;<?php _e('Yes', 'wphrm'); ?> &nbsp;&nbsp;&nbsp;&nbsp;
                                            <input  name="wphrm_employee_approving_officer" class="icheck" type="radio" class="wphrm_employee_approving_officer" <?php checked(isset($wphrmEmployeeBasicInfo['wphrm_employee_approving_officer']) && $wphrmEmployeeBasicInfo['wphrm_employee_approving_officer'] == 'No' || $wphrmEmployeeBasicInfo['wphrm_employee_approving_officer'] == '') ?> value="No" >&nbsp;<?php _e('No', 'wphrm'); ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if(isset($wphrmUserRole) && $wphrmUserRole =='administrator'){ ?>
                                <div class="form-group">
                                    <label class="control-label col-md-4"><?php _e('Role', 'wphrm'); ?>
                                    </label>
                                    <div class="col-md-8">
                                        <select class="bs-select form-control" data-show-subtext="true" name="wphrm_employee_role" id="wphrm_employee_role">
                                            <option value="">--<?php _e('Select', 'wphrm'); ?>--</option>
                                            <?php foreach ($wphrmUserAllRoles as $key => $wphrmUserRoles) { ?>
                                            <option <?php if(isset($wphrmEmployeeBasicInfo['wphrm_employee_role']) && $wphrmEmployeeBasicInfo['wphrm_employee_role']==$key): echo 'selected="selected"';   endif;  ?>
                                                    value="<?php echo esc_attr($key); ?>"><?php echo esc_html($wphrmUserRoles); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <?php }else{
                                ?>
                                <input  name="wphrm_employee_role" type="hidden" id="wphrm_employee_role" value="<?php
                                                                                                                                                 if (isset($wphrmEmployeeBasicInfo['wphrm_employee_role']) && $wphrmEmployeeBasicInfo['wphrm_employee_role']!='') : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_role']);
                                                                                                                                                 else :
                                                                                                                                                 echo 'subscriber';
                                                                                                                                                 endif;
                                                                                                                 ?>" class="form-control" />
                                <?php } ?>

                                <div class="form-group">
                                    <label class="control-label col-md-4"><?php _e('User ID / Employee ID', 'wphrm'); ?>
                                    </label>
                                    <div class="col-md-8">
                                        <input  name="wphrm_employee_userid" type="text" id="wphrm_employee_userid" value="<?php
                                                                                                        if (isset($wphrmEmployeeBasicInfo['wphrm_employee_userid']) && $wphrmEmployeeBasicInfo['wphrm_employee_userid'] != '') : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_userid']);
                                                                                                        else : if (isset($userInformation->data->user_login)): echo esc_html($userInformation->data->user_login);
                                                                                                        endif;
                                                                                                        endif;
                                                                                                                           ?>" class="form-control" />
                                    </div>
                                </div>
                                <?php if ($wphrmEmployeeEditId == '') { ?>
                                <div class="form-group">
                                    <label class="control-label col-md-4"><?php _e('Password', 'wphrm'); ?>  </label>
                                    <div class="col-md-8">
                                        <input name="wphrm_employee_password" id="wphrm_employee_password" type="password" value="" class="form-control" >
                                        <input id="methods" type="checkbox" class="form-control" /> Show password</label>&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" name="generate" id="generatePassword"  class="btn default"><i class="fa fa-cogs"></i>&nbsp;&nbsp;Generate Password</button>
                                </div>
                            </div>
                            <?php } ?>

                            <div class="form-group">
                                <label class="control-label col-md-4"><?php _e('Gender', 'wphrm'); ?>
                                </label>
                                <div class="col-md-8">
                                    <div class="radio-list" data-error-container="#form_2_membership_error">
                                        <input  name="wphrm_employee_gender" class="icheck" type="radio" <?php
                                                                                                        if (isset($wphrmEmployeeBasicInfo['wphrm_employee_gender']) && $wphrmEmployeeBasicInfo['wphrm_employee_gender'] == 'Male') : echo esc_attr('checked');
                                                                                                        endif;
                                               ?> id="wphrm_employee_gender" value="Male" checked>&nbsp;<?php _e('Male', 'wphrm'); ?> &nbsp;&nbsp;&nbsp;&nbsp;
                                        <input  name="wphrm_employee_gender" class="icheck" type="radio" id="wphrm_employee_gender" <?php
                                                                                                        if (isset($wphrmEmployeeBasicInfo['wphrm_employee_gender']) && $wphrmEmployeeBasicInfo['wphrm_employee_gender'] == 'Female') : echo esc_attr('checked');
                                                                                                        endif;
                                               ?> value="Female" >&nbsp;<?php _e('Female', 'wphrm'); ?>
                                    </div>
                                </div>
                            </div>

                            <?php } else { ?>
                            <div class="form-group">
                                <label class="col-md-4 control-label"><?php _e('First Name', 'wphrm'); ?><span class="required"></span></label>
                                <div class="col-md-8">
                                    <input disabled class="form-control"  name="wphrm_employee_fname" type="text" id="wphrm_employee_fname" value="<?php
                                          if (isset($wphrmEmployeeFirstName) && $wphrmEmployeeFirstName != '') : echo esc_attr($wphrmEmployeeFirstName);
                                          else : echo esc_html($userInformation->data->user_nicename);
                                          endif;
                                                                                                                                                   ?>" autocapitalize="none"  />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label"><?php _e('Last Name', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <input disabled class="form-control" name="wphrm_employee_lname" type="text" id="wphrm_employee_lname" value="<?php
                                          if (isset($wphrmEmployeeLastName)) : echo esc_attr($wphrmEmployeeLastName);
                                          endif;
                                                                                                                                                  ?>" autocapitalize="none"  />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label"><?php _e('Father Name', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <input disabled class="form-control" name="wphrm_employee_fathername" type="text" id="wphrm_employee_fathername" value="<?php
                                          if (isset($wphrmEmployeeBasicInfo['wphrm_employee_fathername'])) : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_fathername']);
                                          endif;
                                                ?>" autocapitalize="none"  />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label"><?php _e('Email', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <input disabled class="form-control" name="wphrm_employee_email" type="text" id="wphrm_employee_email" value="<?php
                                          if (isset($wphrmEmployeeBasicInfo['wphrm_employee_email']) && $wphrmEmployeeBasicInfo['wphrm_employee_email'] != '') : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_email']);
                                          else : echo esc_html($userInformation->data->user_email);
                                          endif;
                                                                                                                                                  ?>" autocapitalize="none"  />
                                </div>
                            </div>
                            <?php } ?>
                            <?php if (in_array('manageOptionsEmployee', $wphrmGetPagePermissions)) { ?>
                            <div class="form-group">
                                <label class="control-label col-md-4"><?php _e('Date of Birth', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <div class="input-group input-medium date before-current-date" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                        <input class="form-control"  name="wphrm_employee_bod" type="text" id="wphrm_employee_bod" value="<?php
                                                                                                    if (isset($wphrmEmployeeBasicInfo['wphrm_employee_bod'])) : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_bod']);
                                                                                                    endif;
                                                                                                                                          ?>" autocapitalize="none"  />
                                        <span class="input-group-btn">
                                            <button class="btn default-date" type="button" disabled ><i class="fa fa-calendar" style="line-height: 1.9;"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <?php } else { ?>
                            <div class="form-group">
                                <label class="control-label col-md-4"><?php _e('Date of Birth', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <div class="input-group input-medium"  data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                        <input disabled="" class="form-control"  name="wphrm_employee_bod" type="text" id="wphrm_employee_bod" value="<?php
                                          if (isset($wphrmEmployeeBasicInfo['wphrm_employee_bod'])) : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_bod']);
                                          endif;
                                                                                                                                                      ?>" autocapitalize="none"  />
                                        <span class="input-group-btn">
                                            <button class="btn default-date" type="button" disabled ><i class="fa fa-calendar" style="line-height: 1.9;"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>


                            <!--J@F-->

                            <?php if (in_array('manageOptionsEmployee', $wphrmGetPagePermissions)) { ?>
                            <?php $employee_statuses = get_option(''); ?>
                            <div class="form-group">
                                <label class="control-label col-md-4"><?php _e('Marital Status', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <?php if( is_array($employee_statuses_array) && !empty($employee_statuses_array) ): ?>
                                    <select class="form-control select2me" name="wphrm_employee_mstatus" id="wphrm_employee_mstatus">
                                        <option value=""> <?php _e('Select Marital Status', 'wphrm'); ?></option>
                                        <?php foreach($employee_statuses_array as $mstatus):?>
                                        <?php $currrent_mstatus = !empty($wphrmEmployeeBasicInfo['wphrm_employee_mstatus']) ? esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_mstatus']) : ''; ?>
                                        <option value="<?php echo $mstatus; ?>" <?php selected($currrent_mstatus, $mstatus ); ?> > <?php _e($mstatus, 'wphrm'); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4"><?php _e('Has child', 'wphrm'); ?>
                                </label>
                                <div class="col-md-8">
                                    <div class="radio-list" data-error-container="#form_2_membership_error">
                                        <input  name="wphrm_employee_has_child" class="wphrm_employee_has_child" type="radio" value="Yes" <?php
                                                                                                        if (isset($wphrmEmployeeBasicInfo['wphrm_employee_has_child']) && $wphrmEmployeeBasicInfo['wphrm_employee_has_child'] == 'Yes') : echo esc_attr('checked');
                                                                                                        endif;
                                               ?> >&nbsp;<?php _e('Yes', 'wphrm'); ?> &nbsp;&nbsp;&nbsp;&nbsp;
                                        <input  name="wphrm_employee_has_child" class="wphrm_employee_has_child" type="radio" value="No" <?php
                                                                                                        if (isset($wphrmEmployeeBasicInfo['wphrm_employee_has_child']) && $wphrmEmployeeBasicInfo['wphrm_employee_has_child'] == 'No') : echo esc_attr('checked'); endif; ?> >&nbsp;<?php _e('No', 'wphrm'); ?>
                                    </div>
                                </div>
                            </div>
                            <?php } else { ?>
                            <div class="form-group">
                                <label class="control-label col-md-4"><?php _e('Marital Status', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <div class="input-group input-medium"  data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                        <input disabled="" class="form-control"  name="wphrm_employee_mstatus" type="text" id="wphrm_employee_mstatus" value="<?php
                                          if (isset($wphrmEmployeeBasicInfo['wphrm_employee_mstatus'])) : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_mstatus']);
                                          endif;
                                                    ?>" autocapitalize="none"  />
                                        <span class="input-group-btn">
                                            <button class="btn default-date" type="button"><i class="fa fa-calendar" style="line-height: 1.9;"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>

                            <?php if (in_array('manageOptionsEmployee', $wphrmGetPagePermissions)) { ?>
                            <div class="form-group">
                                <label class="control-label col-md-4"><?php _e('Religion', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                        <!--<input class="form-control"  name="wphrm_employee_religion" type="text" id="wphrm_employee_religion" value="<?php
                                                                                                    /*if (isset($wphrmEmployeeBasicInfo['wphrm_employee_religion'])) : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_religion']);
                                                                                                    endif;*/
                                                                                                                                                    ?>" autocapitalize="none"  />-->
                                    <select class="form-control select2me"  name="wphrm_employee_religion" type="text" id="wphrm_employee_religion" aria-invalid="false">
                                        <?php 
                                            if (isset($wphrmEmployeeBasicInfo['wphrm_employee_religion'])) : 
                                                    $religion = esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_religion']);
                                            endif;
                                        ?>
                                        <option value="">Select Religion</option>
                                        <option value="Buddhism" <?php selected($religion, "Buddhism" ); ?> >Buddhism</option>
                                        <option value="Taoism" <?php selected($religion, "Taoism" ); ?> >Taoism</option>
                                        <option value="Islam" <?php selected($religion, "Islam" ); ?> >Islam</option>
                                        <option value="Christianity" <?php selected($religion, "Christianity" ); ?> >Christianity</option>
                                        <option value="Catholic" <?php selected($religion, "Catholic" ); ?> >Catholic</option>
                                        <option value="Hinduism" <?php selected($religion, "Hinduism" ); ?> >Hinduism</option>
                                        <option value="Free Thinker" <?php selected($religion, "Free Thinker" ); ?> >Free Thinker</option>
                                        <option value="Others" <?php selected($religion, "Others" ); ?> >Others</option>
                                    </select>
                                </div>
                            </div>
                            <?php } else { ?>
                            <div class="form-group">
                                <label class="control-label col-md-4"><?php _e('Religion', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <div class="input-group input-medium"  data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                        <input disabled="" class="form-control"  name="wphrm_employee_religion" type="text" id="wphrm_employee_religion" value="<?php
                                          if (isset($wphrmEmployeeBasicInfo['wphrm_employee_religion'])) : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_religion']);
                                          endif;
                                                    ?>" autocapitalize="none"  />
                                    </div>
                                </div>
                            </div>
                            <?php } ?>

                            <!--J@F END-->



                            <div class="form-group">
                                <label class="col-md-4 control-label"><?php _e('Phone Number', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <input  class="form-control"  name="wphrm_employee_phone" type="text" id="wphrm_employee_phone" value="<?php
                                                                                                                                           if (isset($wphrmEmployeeBasicInfo['wphrm_employee_phone'])) : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_phone']);
                                                                                                                                           endif;
                                                                                                                                           ?>" autocapitalize="none" autocorrect="off"  />
                                </div>
                            </div>


                            <!--J@F-->
                            <div class="form-group">
                                <label class="col-md-4 control-label"><?php _e('Alternative Phone Number (Home)', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <input  class="form-control"  name="wphrm_employee_home_phone" type="text" id="wphrm_employee_home_phone" value="<?php
                                                                                                                                                     if (isset($wphrmEmployeeBasicInfo['wphrm_employee_home_phone'])) : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_home_phone']);
                                                                                                                                                     endif;
                                                                                                                                                     ?>" autocapitalize="none" autocorrect="off"  />
                                </div>
                            </div>
                            <!--J@F END-->

                            <div class="form-group">
                                <label class="col-md-4 control-label"><?php _e('Local Address', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <textarea  rows="3" class="form-control" name="wphrm_employee_local_address" type="text" id="wphrm_employee_local_address" value="" autocapitalize="none" autocorrect="off"><?php
                                        if (isset($wphrmEmployeeBasicInfo['wphrm_employee_local_address'])) : echo esc_textarea($wphrmEmployeeBasicInfo['wphrm_employee_local_address']);
                                        endif;
                                        ?></textarea>
                                </div>
                            </div>
                            <?php if (in_array('manageOptionsEmployee', $wphrmGetPagePermissions)) { ?>

                            <div class="form-group">
                                <label class="col-md-4 control-label"><?php _e('Permanent Address', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <textarea  rows="3" class="form-control" name="wphrm_employee_permanant_address" style="margin-bottom: 3px;" type="text" id="wphrm_employee_permanant_address" value="" autocapitalize="none" autocorrect="off"><?php
                                                                                                    if (isset($wphrmEmployeeBasicInfo['wphrm_employee_permanant_address'])) : echo esc_textarea($wphrmEmployeeBasicInfo['wphrm_employee_permanant_address']);
                                                                                                    endif;
                                        ?></textarea>
                                    <button type="button"  onclick="copyLocalAddresss()"  class="btn default"><i class="fa fa-copy"></i>&nbsp;Copy Local Address</button>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label"><?php _e( 'Highest Educational Level', 'wphrm' ); ?></label>
                                <div class="col-md-8">
                                    <select class="form-control select2me"  name="wphrm_employee_educational_level" type="text" id="wphrm_employee_educational_level" aria-invalid="false">
                                        <?php 
                                            if (isset($wphrmEmployeeBasicInfo['wphrm_employee_educational_level'])) : 
                                                    $educational_level = esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_educational_level']);
                                            endif;
                                        ?>
                                        <option value="">Select Level</option>
                                        <option value="PHD or Doctorate" <?php selected($educational_level, "PHD or Doctorate" ); ?> >PHD or Doctorate</option>
                                        <option value="Master (MBA)" <?php selected($educational_level, "Master (MBA)" ); ?> >Master (MBA)</option>
                                        <option value="Bachelor Degree (With Merit)" <?php selected($educational_level, "Bachelor Degree (With Merit)" ); ?> >Bachelor Degree (With Merit)</option>
                                        <option value="Bachelor Degree" <?php selected($educational_level, "Bachelor Degree" ); ?> >Bachelor Degree</option>
                                        <option value="Diploma" <?php selected($educational_level, "Diploma" ); ?> >Diploma</option>
                                        <option value="A Level" <?php selected($educational_level, "A Level" ); ?> >A Level</option>
                                        <option value="Higher Nitec" <?php selected($educational_level, "Higher Nitec" ); ?> >Higher Nitec</option>
                                        <option value="Nitec" <?php selected($educational_level, "Nitec" ); ?> >Nitec</option>
                                        <option value="O Level" <?php selected($educational_level, "O Level" ); ?> >O Level</option>
                                        <option value="N Level" <?php selected($educational_level, "N Level" ); ?> >N Level</option>
                                        <option value="PSLE" <?php selected($educational_level, "PSLE" ); ?> >PSLE</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-4"><?php _e('Completion of National Service (ORD)', 'wphrm'); ?>
                                </label>
                                <div class="col-md-8">
                                    <div class="radio-list" data-error-container="#form_2_membership_error">
                                        <input  name="wphrm_employee_ord" class=" wphrm_employee_ord" type="radio" value="Yes" <?php
                                                                                                        if (isset($wphrmEmployeeBasicInfo['wphrm_employee_ord']) && $wphrmEmployeeBasicInfo['wphrm_employee_ord'] == 'Yes') : echo esc_attr('checked'); endif; ?> >&nbsp;<?php _e('Yes', 'wphrm'); ?> &nbsp;&nbsp;&nbsp;&nbsp;
                                        <input  name="wphrm_employee_ord" class=" wphrm_employee_ord" type="radio" value="No"  <?php
                                                                                                        if (isset($wphrmEmployeeBasicInfo['wphrm_employee_ord']) && $wphrmEmployeeBasicInfo['wphrm_employee_ord'] == 'No') : echo esc_attr('checked'); elseif(empty($wphrmEmployeeBasicInfo['wphrm_employee_ord'])) : echo 'checked'; endif; ?> >&nbsp;<?php _e('No', 'wphrm'); ?>
                                    </div>
                                </div>
                            </div>

                            <?php if(empty($wphrmEmployeeBasicInfo['wphrm_employee_ord']) || $wphrmEmployeeBasicInfo['wphrm_employee_ord'] == 'No') { $ord_hide = 'hide'; } ?>
                            <div class="form-group ord-holder <?php echo $ord_hide ?>">
                                <label class="control-label col-md-4"><?php _e('ORD Start Date', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <div class="input-group input-medium date date-picker"  data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                        <input class="form-control"  name="wphrm_ord_start_date" type="text" id="wphrm_ord_start_date" value="<?php
                                          if (isset($wphrmEmployeeBasicInfo['wphrm_ord_start_date'])) : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_ord_start_date']);
                                          endif;
                                                                                                                                                      ?>" autocapitalize="none"  />
                                        <span class="input-group-btn">
                                            <button class="btn default-date" type="button" disabled ><i class="fa fa-calendar" style="line-height: 1.9;"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group ord-holder <?php echo $ord_hide ?>">
                                <label class="control-label col-md-4"><?php _e('ORD End Date', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <div class="input-group input-medium date date-picker"  data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                        <input class="form-control"  name="wphrm_ord_end_date" type="text" id="wphrm_ord_end_date" value="<?php
                                          if (isset($wphrmEmployeeBasicInfo['wphrm_ord_end_date'])) : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_ord_end_date']);
                                          endif;
                                                                                                                                                      ?>" autocapitalize="none"  />
                                        <span class="input-group-btn">
                                            <button class="btn default-date" type="button" disabled ><i class="fa fa-calendar" style="line-height: 1.9;"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-4"><?php _e('Completion of Relief of Duty (ROD)', 'wphrm'); ?>
                                </label>
                                <div class="col-md-8">
                                    <div class="radio-list" data-error-container="#form_2_membership_error">
                                        <input  name="wphrm_employee_rod" class=" wphrm_employee_rod" type="radio" value="Yes" <?php
                                                                                                        if (isset($wphrmEmployeeBasicInfo['wphrm_employee_rod']) && $wphrmEmployeeBasicInfo['wphrm_employee_rod'] == 'Yes') : echo esc_attr('checked'); endif; ?> >&nbsp;<?php _e('Yes', 'wphrm'); ?> &nbsp;&nbsp;&nbsp;&nbsp;
                                        <input  name="wphrm_employee_rod" class=" wphrm_employee_rod" type="radio" value="No" <?php
                                                                                                        if (isset($wphrmEmployeeBasicInfo['wphrm_employee_rod']) && $wphrmEmployeeBasicInfo['wphrm_employee_rod'] == 'No') : echo esc_attr('checked'); elseif(empty($wphrmEmployeeBasicInfo['wphrm_employee_rod'])) : echo esc_attr('checked'); endif; ?> >&nbsp;<?php _e('No', 'wphrm'); ?>
                                    </div>
                                </div>
                            </div>

                            <?php if(empty($wphrmEmployeeBasicInfo['wphrm_employee_rod']) || $wphrmEmployeeBasicInfo['wphrm_employee_rod'] == 'No') { $rod_hide = 'hide'; } ?>
                            <div class="form-group rod-holder <?php echo $rod_hide ?>">
                                <label class="control-label col-md-4"><?php _e('ROD End Date', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <div class="input-group input-medium date date-picker"  data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                        <input class="form-control"  name="wphrm_rod_end_date" type="text" id="wphrm_rod_end_date" value="<?php
                                          if (isset($wphrmEmployeeBasicInfo['wphrm_rod_end_date'])) : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_rod_end_date']);
                                          endif;
                                                                                                                                                      ?>" autocapitalize="none"  />
                                        <span class="input-group-btn">
                                            <button class="btn default-date" type="button" disabled ><i class="fa fa-calendar" style="line-height: 1.9;"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>


                            <?php } else { ?>
                            <div class="form-group">
                                <label class="col-md-4 control-label"><?php _e('Permanent Address', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <textarea  rows="3" class="form-control" disabled><?php
                                          if (isset($wphrmEmployeeBasicInfo['wphrm_employee_permanant_address'])) : echo esc_textarea($wphrmEmployeeBasicInfo['wphrm_employee_permanant_address']);
                                          endif;
                                        ?></textarea>
                                </div>
                            </div>
                            <?php } ?>
                            </div>
                        </form>
                    
            <?php if( !isset( $_GET['employee_id'] ) ) : ?>
                <div class="actions" style="text-align:right">
                    <a href="javascript:;"  onclick="jQuery('#wphrm_employee_basic_info_form').submit();" data-loading-text="Updating..."  class="demo-loading-btn btn btn-sm btn-info blue ">
                        <i class="fa fa-save" ></i><?php _e('Save', 'wphrm'); ?>  </a>
                </div>
            <?php endif;?>
                    
                </div>
            </div>

            <?php if (in_array('manageOptionsEmployee', $wphrmGetPagePermissions)) { ?>
            <div class="portlet box blue documents-hide-div">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-file-image-o"></i><?php _e('Documents', 'wphrm'); ?>
                    </div>
                    <?php if (isset($wphrmEmployeeEditId) && $wphrmEmployeeEditId != '') { ?>
                    <div class="actions">
                        <a href="javascript:;"  onclick="jQuery('#wphrmEmployeeDocumentInfo_form').submit();" data-loading-text="Updating..."  class="demo-loading-btn btn btn-sm btn-default ">
                            <i class="fa fa-save" ></i><?php _e('Save', 'wphrm'); ?></a>
                    </div>
                    <?php } ?>
                </div>
                <div class="portlet-body">
                    <div class="portlet-body">
                        <div class="clearfix margin-top-10">
                            <p class="p-center"><span class="label label-danger"><?php _e('NOTE', 'wphrm'); ?> !</span>
                                &nbsp;&nbsp;<?php _e("Only 'jpeg', 'jpg', 'png', 'txt', 'pdf', 'doc' filetypes are allowed.", 'wphrm'); ?></p>
                        </div>
                        <button class="close" data-close="alert"></button>
                        <div class="alert alert-success display-hide" id="Documents_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrmMessagesDocuments); ?>
                        </div>
                        <div class="alert alert-danger display-hide" id="Documents_error">
                            <button class="close" data-close="alert"></button>
                        </div>
                        <form method="POST"  accept-charset="UTF-8" class="form-horizontal" id="wphrmEmployeeDocumentInfo_form" enctype="multipart/form-data"><input name="_method" type="hidden" value="PATCH"><input name="_token" type="hidden" value="CKw97QC4WEEKjxHdCpA3oZBiucWKYo0778rEpuPz">
                            <input type="hidden" name="wphrm_employee_id" id="wphrm_employee_id"  value="<?php
                                                                                    if (isset($wphrmEmployeeEditId)) : echo esc_attr($wphrmEmployeeEditId);
                                                                                    endif;
                                                                                                         ?> "/>
                            <div class="form-body">
                                <div class="form-group">

                                    <label class="control-label col-md-4"><?php echo esc_html($wphrmDefaultDocumentsLabel['resume']); ?></label>
                                    <div class="col-md-8">
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
                                                    <input type="file" name="resume" class="documents-Upload">
                                                </span>
                                                <a href="#" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput">
                                                    <?php _e('Remove', 'wphrm'); ?> </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4"><?php echo esc_html($wphrmDefaultDocumentsLabel['offerLetter']); ?></label>
                                    <div class="col-md-8">
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <div class="input-group input-large">
                                                <div class="form-control uneditable-input" data-trigger="fileinput">
                                                    <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename"><?php
                                                                                    if (isset($offerDir) && $offerDir != '') :
                                                                                    $offerExt = pathinfo($offerDir, PATHINFO_EXTENSION);
                                                                                    echo esc_html(mb_strimwidth($offerDir, 0, 10) . '....' . $offerExt);
                                                                                    endif;
                                                    ?></span>
                                                </div>
                                                <span class="input-group-addon btn default btn-file">
                                                    <span class="fileinput-new"><?php _e('Select file', 'wphrm'); ?></span>
                                                    <span class="fileinput-exists"><?php _e('Change', 'wphrm'); ?></span>
                                                    <input type="file" name="offerLetter" class="documents-Upload">
                                                </span>
                                                <a href="#" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput">
                                                    <?php _e('Remove', 'wphrm'); ?></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4"><?php echo esc_html($wphrmDefaultDocumentsLabel['joiningLetter']); ?></label>
                                    <div class="col-md-8">
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <div class="input-group input-large">
                                                <div class="form-control uneditable-input" data-trigger="fileinput">
                                                    <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename"><?php
                                                                                    if (isset($joiningDir) && $joiningDir != '') :
                                                                                    $joiningDirExt = pathinfo($joiningDir, PATHINFO_EXTENSION);
                                                                                    echo esc_html(mb_strimwidth($joiningDir, 0, 10) . '....' . $joiningDirExt);
                                                                                    endif;
                                                    ?>
                                                    </span>
                                                </div>
                                                <span class="input-group-addon btn default btn-file">
                                                    <span class="fileinput-new">
                                                        <?php _e('Select file', 'wphrm'); ?>  </span>
                                                    <span class="fileinput-exists">
                                                        <?php _e('Change', 'wphrm'); ?> </span>
                                                    <input type="file" name="joiningLetter" class="documents-Upload">
                                                </span>
                                                <a href="#" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput">
                                                    <?php _e('Remove', 'wphrm'); ?>  </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4"><?php echo esc_html($wphrmDefaultDocumentsLabel['contractAndAgreement']); ?></label>
                                    <div class="col-md-8">
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <div class="input-group input-large">
                                                <div class="form-control uneditable-input" data-trigger="fileinput">
                                                    <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename"><?php
                                                                                    if (isset($contractDir) && $contractDir != '') :
                                                                                    $contractDirExt = pathinfo($contractDir, PATHINFO_EXTENSION);
                                                                                    echo esc_html(mb_strimwidth($contractDir, 0, 10) . '....' . $contractDirExt);
                                                                                    endif;
                                                    ?>
                                                    </span>
                                                </div>
                                                <span class="input-group-addon btn default btn-file">
                                                    <span class="fileinput-new">
                                                        <?php _e('Select file', 'wphrm'); ?> </span>
                                                    <span class="fileinput-exists">
                                                        <?php _e('Change', 'wphrm'); ?>   </span>
                                                    <input type="file"  name="contract" class="documents-Upload">
                                                </span>
                                                <a href="#" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput">
                                                    <?php _e('Remove', 'wphrm'); ?>  </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-4"><?php echo esc_html($wphrmDefaultDocumentsLabel['iDProof']); ?></label>
                                    <div class="col-md-8">
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <div class="input-group input-large">
                                                <div class="form-control uneditable-input" data-trigger="fileinput">
                                                    <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename"><?php
                                                                                    if (isset($idProofDir) && $idProofDir != '') :
                                                                                    $idProofDirExt = pathinfo($idProofDir, PATHINFO_EXTENSION);
                                                                                    echo esc_html(mb_strimwidth($idProofDir, 0, 10) . '....' . $idProofDirExt);
                                                                                    endif;
                                                    ?>
                                                    </div>
                                                    <span class="input-group-addon btn default btn-file">
                                                        <span class="fileinput-new">
                                                            <?php _e('Select file', 'wphrm'); ?> </span>
                                                        <span class="fileinput-exists">
                                                            <?php _e('Change', 'wphrm'); ?> </span>
                                                        <input type="file" name="IDProof" class="documents-Upload">
                                                    </span>
                                                    <a href="#" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput">
                                                        <?php _e('Remove', 'wphrm'); ?>  </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                        </div>
                                    </div>


                                    <?php
                                                                                    if (isset($wphrmEmployeeDocumentsInfo['documentsfieldslebal']) && $wphrmEmployeeDocumentsInfo['documentsfieldslebal'] != '' && isset($wphrmEmployeeDocumentsInfo['documentsfieldsvalue']) && $wphrmEmployeeDocumentsInfo['documentsfieldsvalue'] != '') {
                                                                                        foreach ($wphrmEmployeeDocumentsInfo['documentsfieldslebal'] as $lebalkey => $documentsfieldslebal) {
                                                                                            foreach ($wphrmEmployeeDocumentsInfo['documentsfieldsvalue'] as $valuekey => $documentsfieldsvalue) {
                                                                                                if ($lebalkey == $valuekey) {
                                    ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-4"><?php _e($documentsfieldslebal, 'wphrm'); ?></label>
                                        <input name="documentsfieldslebal[]" type="hidden" id="documentsfieldslebal" value="<?php
                                                                                                    if (isset($documentsfieldslebal)) : echo esc_attr($documentsfieldslebal);
                                                                                                    endif;
                                                                                                                            ?>"/>
                                        <div class="col-md-8">
                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                <div class="input-group input-large">
                                                    <div class="form-control uneditable-input" data-trigger="fileinput">
                                                        <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename"><?php
                                                                                                    if (isset($documentsfieldsvalue) && $documentsfieldsvalue != '') :
                                                                                                    $url = $this->WPHRMGetAttechment($documentsfieldsvalue);
                                                                                                    $title = $this->WPHRMGetAttechmentTitle($documentsfieldsvalue);
                                                                                                    $resumeExt = pathinfo($url, PATHINFO_EXTENSION);
                                                                                                    echo esc_html(mb_strimwidth($title, 0, 20) . '....' . $resumeExt);
                                                                                                    endif;
                                                        ?></span>
                                                    </div>
                                                    <span class="input-group-addon btn default btn-file">
                                                        <span class="fileinput-new">
                                                            <?php _e('Select file', 'wphrm'); ?> </span>
                                                        <span class="fileinput-exists">
                                                            <?php _e('Change', 'wphrm'); ?> </span>
                                                        <input type="file" name="documentValues[]" class="documents-Upload">
                                                    </span>
                                                    <a href="#" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput">
                                                        <?php _e('Remove', 'wphrm'); ?> </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                        </div>
                                    </div> <?php
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                        $employeeDocumentsfieldskeyInfo = $this->WPHRMGetSettings('employeeDocumentsfieldskey');
                                                                                        if (!empty($employeeDocumentsfieldskeyInfo)) {
                                                                                            foreach ($employeeDocumentsfieldskeyInfo['documentsfieldslebal'] as $employeeDocumentsfieldskeyInfos) {
                                                                                                if (!in_array($employeeDocumentsfieldskeyInfos, $wphrmEmployeeDocumentsInfo['documentsfieldslebal'])) {
                                    ?>

                                    <div class="form-group">
                                        <label class="control-label col-md-4"><?php _e($employeeDocumentsfieldskeyInfos, 'wphrm'); ?></label>
                                        <input name="documentsfieldslebal[]" type="hidden" id="documentsfieldslebal" value="<?php
                                                                                                    if (isset($employeeDocumentsfieldskeyInfos)) : echo esc_attr($employeeDocumentsfieldskeyInfos);
                                                                                                    endif;
                                                                                                                            ?>"/>
                                        <div class="col-md-8">
                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                <div class="input-group input-large">
                                                    <div class="form-control uneditable-input" data-trigger="fileinput">
                                                        <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename"></span>
                                                    </div>
                                                    <span class="input-group-addon btn default btn-file">
                                                        <span class="fileinput-new">
                                                            <?php _e('Select file', 'wphrm'); ?> </span>
                                                        <span class="fileinput-exists">
                                                            <?php _e('Change', 'wphrm'); ?> </span>
                                                        <input type="file" name="documentValues[]" class="documents-Upload">
                                                    </span>
                                                    <a href="#" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput">
                                                        <?php _e('Remove', 'wphrm'); ?> </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                        </div>
                                    </div>
                                    <?php
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    } else {
                                                                                        $employeeDocumentsfieldskeyInfo = $this->WPHRMGetSettings('employeeDocumentsfieldskey');
                                                                                        if (!empty($employeeDocumentsfieldskeyInfo)) {
                                                                                            foreach ($employeeDocumentsfieldskeyInfo['documentsfieldslebal'] as $employeeDocumentsfieldskeyInfos) {
                                    ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-4"><?php _e($employeeDocumentsfieldskeyInfos, 'wphrm'); ?></label>
                                        <input name="documentsfieldslebal[]" type="hidden" id="documentsfieldslebal" value="<?php
                                                                                                if (isset($employeeDocumentsfieldskeyInfos)) : echo esc_attr($employeeDocumentsfieldskeyInfos);
                                                                                                endif;
                                                                                                                            ?>"/>
                                        <div class="col-md-8">
                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                <div class="input-group input-large">
                                                    <div class="form-control uneditable-input" data-trigger="fileinput">
                                                        <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename"></span>
                                                    </div>
                                                    <span class="input-group-addon btn default btn-file">
                                                        <span class="fileinput-new">
                                                            <?php _e('Select file', 'wphrm'); ?> </span>
                                                        <span class="fileinput-exists">
                                                            <?php _e('Change', 'wphrm'); ?> </span>
                                                        <input type="file" name="documentValues[]" class="documents-Upload">
                                                    </span>
                                                    <a href="#" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput">
                                                        <?php _e('Remove', 'wphrm'); ?> </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                        </div>
                                    </div>
                                    <?php
                                                                                            }
                                                                                        }
                                                                                    }
                                    ?>



                                </div>
                                </form>
                            </div>
                    </div>
                </div>
                <?php
                                                                                   }
            else {
                ?>
                <div class="portlet blue box documents-hide-div">
                    <div class="portlet-title">
                        <div class="caption"><i class="fa fa-file-image-o"></i><?php _e('Documents', 'wphrm'); ?></div>
                    </div>
                    <div class="portlet-body">
                        <div class="portlet-body">
                            <div class="alert alert-success display-hide" id="employee_document">
                                <button class="close" data-close="alert"></button>
                            </div>
                            <div class="alert alert-danger display-hide" id="employee_document_error">
                                <button class="close" data-close="alert"></button>
                            </div>
                            <form method="POST"  accept-charset="UTF-8" class="form-horizontal" id="wphrmEmployeeDocumentInfo_form" enctype="multipart/form-data">
                                <input type="hidden" name="wphrm_employee_id" id="wphrm_employee_id"  value="<?php
                if (isset($wphrmEmployeeEditId)) : echo esc_attr($wphrmEmployeeEditId);
                endif;
                                                                                                             ?> "/>
                                <div class="form-body">
                                    <div class="form-group">
                                        <label class="control-label col-md-4"><?php echo esc_html($wphrmDefaultDocumentsLabel['resume']); ?></label>
                                        <div class="col-md-8">
                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                <div class="input-group input-large" >
                                                    <div disabled  class="form-control uneditable-input" data-trigger="fileinput">
                                                        <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename">
                                                        <?php
                if (isset($resumeDir) && $resumeDir != '') : $resumeExt = pathinfo($resumeDir, PATHINFO_EXTENSION);
                echo esc_html(mb_strimwidth($resumeDir, 0, 10) . '....' . $resumeExt);
                endif;
                                                        ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4"><?php echo esc_html($wphrmDefaultDocumentsLabel['offerLetter']); ?></label>
                                        <div class="col-md-8">
                                            <div  class="fileinput fileinput-new" data-provides="fileinput">
                                                <div  class="input-group input-large">
                                                    <div disabled class="form-control uneditable-input" data-trigger="fileinput">
                                                        <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename">
                                                        <?php
                if (isset($offerDir) && $offerDir != '') :
                $offerExt = pathinfo($offerDir, PATHINFO_EXTENSION);
                echo esc_html(mb_strimwidth($offerDir, 0, 10) . '....' . $offerExt);
                endif;
                                                        ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4"><?php echo esc_html($wphrmDefaultDocumentsLabel['joiningLetter']); ?></label>
                                        <div class="col-md-8">
                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                <div class="input-group input-large">
                                                    <div disabled class="form-control uneditable-input" data-trigger="fileinput">
                                                        <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename">
                                                        <?php
                if (isset($joiningDir) && $joiningDir != '') :
                $joiningDirExt = pathinfo($joiningDir, PATHINFO_EXTENSION);
                echo esc_html(mb_strimwidth($joiningDir, 0, 10) . '....' . $joiningDirExt);
                endif;
                                                        ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4"><?php echo esc_html($wphrmDefaultDocumentsLabel['contractAndAgreement']); ?></label>
                                        <div class="col-md-8">
                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                <div class="input-group input-large">
                                                    <div disabled class="form-control uneditable-input" data-trigger="fileinput">
                                                        <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename"><?php
                if (isset($contractDir) && $contractDir != '') :
                $contractDirExt = pathinfo($contractDir, PATHINFO_EXTENSION);
                echo esc_html(mb_strimwidth($contractDir, 0, 10) . '....' . $contractDirExt);
                endif;
                                                        ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4"><?php echo esc_html($wphrmDefaultDocumentsLabel['iDProof']); ?></label>
                                        <div class="col-md-8">
                                            <div disabled class="fileinput fileinput-new" data-provides="fileinput">
                                                <div class="input-group input-large">
                                                    <div  disabled class="form-control uneditable-input" data-trigger="fileinput">
                                                        <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename"><?php
                if (isset($idProofDir) && $idProofDir != '') :
                $idProofDirExt = pathinfo($idProofDir, PATHINFO_EXTENSION);
                echo esc_html(mb_strimwidth($idProofDir, 0, 10) . '....' . $idProofDirExt);
                endif;
                                                        ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3"> </div>
                                    </div>
                                    <?php
                if (isset($wphrmEmployeeDocumentsInfo['documentsfieldslebal']) && $wphrmEmployeeDocumentsInfo['documentsfieldslebal'] != '' && isset($wphrmEmployeeDocumentsInfo['documentsfieldsvalue']) && $wphrmEmployeeDocumentsInfo['documentsfieldsvalue'] != '') {
                    foreach ($wphrmEmployeeDocumentsInfo['documentsfieldslebal'] as $lebalkey => $documentsfieldslebal) {
                        foreach ($wphrmEmployeeDocumentsInfo['documentsfieldsvalue'] as $valuekey => $documentsfieldsvalue) {
                            if ($lebalkey == $valuekey) {
                                    ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-4"><?php _e($documentsfieldslebal, 'wphrm'); ?></label>
                                        <div class="col-md-8">
                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                <div class="input-group input-large">
                                                    <div disabled class="form-control uneditable-input" data-trigger="fileinput">
                                                        <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename">
                                                        <?php
                                if (isset($documentsfieldsvalue) && $documentsfieldsvalue != '') :
                                $url = $this->WPHRMGetAttechment($documentsfieldsvalue);
                                $title = $this->WPHRMGetAttechmentTitle($documentsfieldsvalue);
                                $resumeExt = pathinfo($url, PATHINFO_EXTENSION);
                                echo esc_html(mb_strimwidth($title, 0, 20) . '....' . $resumeExt);
                                endif;
                                                        ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                        </div>
                                    </div> <?php
                            }
                        }
                    }
                }
                                    ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>

        <?php if( isset($_GET['employee_id']) ) : ?>
            
            <div class="col-md-6 col-sm-6">
                <div class="portlet blue box ">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-edit"></i><?php _e('Work Info', 'wphrm'); ?>
                        </div>

                        <!--<div class="actions">
                            <a href="javascript:;"  onclick="jQuery('#wphrm_employee_work_info_form').submit();" data-loading-text="Updating..."  class="demo-loading-btn btn btn-sm btn-default ">
                                <i class="fa fa-save" ></i><?php _e('Save', 'wphrm'); ?>
                            </a>
                        </div>-->
                    </div>
                    <div class="portlet-body">
                        <div class="alert alert-success display-hide" id="work_details_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html('Work Details have been successfully updated.'); ?>
                            <button class="close" data-close="alert"></button>
                        </div>
                        <div class="alert alert-danger display-hide" id="work_details_error">
                            <button class="close" data-close="alert"></button>
                        </div>
                        <form method="POST" accept-charset="UTF-8" class="form-horizontal" id="wphrm_employee_work_info_form" enctype="multipart/form-data">

                            <input type="hidden" name="wphrm_employee_id" id="wphrm_employee_id"  value="<?php echo (isset($wphrmEmployeeEditId)) ? esc_attr($wphrmEmployeeEditId) : ''; ?> "/>
                            <input type="hidden" name="info_type" value="work_info" >
                            <div class="form-body">
                                <?php include_once 'jaf_work_info_form.php'; ?>
                            </div>
                        </form>
                    </div>
                </div>

            </div>


            <div class="col-md-6 col-sm-6">
                <div class="portlet box blue">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-edit"></i><?php _e('ID Information', 'wphrm'); ?>
                        </div>

                        <!--<div class="actions">
                            <a href="javascript:;"  onclick="jQuery('#wphrm_employee_work_permit_form').submit();" data-loading-text="Updating..."  class="demo-loading-btn btn btn-sm btn-default ">
                                <i class="fa fa-save" ></i><?php _e('Save', 'wphrm'); ?>
                            </a>
                        </div>-->
                    </div>
                    <div class="portlet-body">
                        <div class="alert alert-success display-hide" id="work_permit_details_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html('Work Details have been successfully updated.'); ?>
                            <button class="close" data-close="alert"></button>
                        </div>
                        <div class="alert alert-danger display-hide" id="work_details_error">
                            <button class="close" data-close="alert"></button>
                        </div>
                        <form method="POST" accept-charset="UTF-8" class="form-horizontal" id="wphrm_employee_work_permit_form" enctype="multipart/form-data">

                            <input type="hidden" name="wphrm_employee_id" id="wphrm_employee_id"  value="<?php echo (isset($wphrmEmployeeEditId)) ? esc_attr($wphrmEmployeeEditId) : ''; ?> "/>

                            <div class="form-body">
                                <?php if (in_array('manageOptionsEmployee', $wphrmGetPagePermissions)): ?>
                                <div class="form-group">
                                    <label class="control-label col-md-4" >Employment Pass Type</label>
                                    <div class="col-md-8" >
                                       <!-- <input class="form-control" name="wphrm_employee_work_permit_type" id="wphrm_employee_work_permit_type" value="<?php //echo isset($employee_complete_info->work_permit_info->wphrm_employee_work_permit_type) ? $employee_complete_info->work_permit_info->wphrm_employee_work_permit_type : ''; ?>" autocapitalize="none" type="text">-->
                                        
                                        <select class="form-control select2me"  name="wphrm_employee_work_permit_type" type="text" id="wphrm_employee_work_permit_type">
                                            <?php 
                                                if (isset($employee_complete_info->work_permit_info->wphrm_employee_work_permit_type)) : 
                                                        $emp_pass_type = esc_attr($employee_complete_info->work_permit_info->wphrm_employee_work_permit_type);
                                                endif;
                                            ?>
                                            <option value="">Select Pass Type</option>
                                            <option value="Employment Pass" <?php selected($emp_pass_type, "Employment Pass" ); ?> >Employment Pass</option>
                                            <option value="S Pass" <?php selected($emp_pass_type, "S Pass" ); ?> >S Pass</option>
                                            <option value="Work Permit" <?php selected($emp_pass_type, "Work Permit" ); ?> >Work Permit</option>
                                        </select>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if (in_array('manageOptionsEmployee', $wphrmGetPagePermissions)): ?>
                                <div class="form-group">
                                    <label class="control-label col-md-4" >FIN/NRIC Number</label>
                                    <div class="col-md-8" >
                                        <input class="form-control" name="wphrm_employee_nric" id="wphrm_employee_nric" value="<?php echo isset($employee_complete_info->work_permit_info->wphrm_employee_nric) ? $employee_complete_info->work_permit_info->wphrm_employee_nric : ''; ?>" autocapitalize="none" type="text">
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if (in_array('manageOptionsEmployee', $wphrmGetPagePermissions)): ?>
                                <div class="form-group">
                                    <label class="control-label col-md-4" >Date of Issue</label>
                                    <div class="col-md-8" >
                                        <div class="input-group input-medium date date-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                            <input class="form-control" name="wphrm_employee_permit_issued" id="wphrm_employee_permit_issued" value="<?php echo isset($employee_complete_info->work_permit_info->wphrm_employee_permit_issued) ? $employee_complete_info->work_permit_info->wphrm_employee_permit_issued : ''; ?>" autocapitalize="none" type="text">
                                            <span class="input-group-btn">
                                                <button class="btn default-date" type="button" disabled ><i class="fa fa-calendar" style="line-height: 1.9;"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if (in_array('manageOptionsEmployee', $wphrmGetPagePermissions)): ?>
                                <div class="form-group">
                                    <label class="control-label col-md-4" >Expiration Date</label>
                                    <div class="col-md-8" >
                                        <div class="input-group input-medium date after-current-date" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                            <input class="form-control" name="wphrm_employee_permit_expiration" id="wphrm_employee_permit_expiration" value="<?php echo isset($employee_complete_info->work_permit_info->wphrm_employee_permit_expiration) ? $employee_complete_info->work_permit_info->wphrm_employee_permit_expiration : ''; ?>" autocapitalize="none" type="text">
                                            <span class="input-group-btn">
                                                <button class="btn default-date" type="button" disabled ><i class="fa fa-calendar" style="line-height: 1.9;"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if (in_array('manageOptionsEmployee', $wphrmGetPagePermissions)): ?>
                                <div class="form-group">
                                    <label class="control-label col-md-4" >Nationality</label>
                                    <div class="col-md-8" >
                                        <!--<input class="form-control" name="wphrm_employee_nationality" id="wphrm_employee_nationality" value="<?php //echo isset($employee_complete_info->work_permit_info->wphrm_employee_nationality) ? $employee_complete_info->work_permit_info->wphrm_employee_nationality : ''; ?>" autocapitalize="none" type="text">-->
                                        <select class="form-control select2me"  name="wphrm_employee_nationality" type="text" id="wphrm_employee_nationality">
                                            <?php 
                                                if (isset($employee_complete_info->work_permit_info->wphrm_employee_nationality)) : 
                                                        $nationalities = esc_attr($employee_complete_info->work_permit_info->wphrm_employee_nationality);
                                                endif;
                                            ?>
                                            <option value="">Select Nationality</option>
                                            <option value="Singaporean" <?php selected($nationalities, "Singaporean" ); ?> >Singaporean</option>
                                            <option value="Singapore Permanent Resident" <?php selected($nationalities, "Singapore Permanent Resident" ); ?> >Singapore Permanent Resident</option>
                                            <option value="Foreigner" <?php selected($nationalities, "Foreigner" ); ?> >Foreigner</option>
                                        </select>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if (in_array('manageOptionsEmployee', $wphrmGetPagePermissions)): ?>
                                <div class="form-group">
                                    <label class="col-md-4 control-label" scope="row"><?php _e('Country of Birth', 'wphrm'); ?>:</label>
                                    <div class="col-md-8">
                                        <?php $selected_cob = isset($employee_complete_info->work_permit_info->wphrm_employee_country_of_birth) ? $employee_complete_info->work_permit_info->wphrm_employee_country_of_birth : ''; ?>
                                        <select class="form-control" name="wphrm_employee_country_of_birth" id="wphrm_employee_country_of_birth" >
                                            <option value="" >Select Country</option>
                                            <?php foreach($employee_country_array as $key => $country): ?>
                                            <option value="<?php echo $country->code; ?>" <?php selected( $country->code, $selected_cob ); ?>><?php _e($country->name, 'wphrm'); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if (in_array('manageOptionsEmployee', $wphrmGetPagePermissions)): ?>
                                <div class="form-group">
                                    <label class="control-label col-md-4" >Passport Number</label>
                                    <div class="col-md-8" >
                                        <input class="form-control" name="wphrm_employee_passport" id="wphrm_employee_passport" value="<?php echo isset($employee_complete_info->work_permit_info->wphrm_employee_passport) ? $employee_complete_info->work_permit_info->wphrm_employee_passport : ''; ?>" autocapitalize="none" type="text">
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if (in_array('manageOptionsEmployee', $wphrmGetPagePermissions)): ?>
                                <div class="form-group">
                                    <label class="col-md-4 control-label" scope="row"><?php _e('Passport Country', 'wphrm'); ?>:</label>
                                    <div class="col-md-8">
                                        <?php $selected_passport_country = isset($employee_complete_info->work_permit_info->wphrm_employee_passport_country) ? $employee_complete_info->work_permit_info->wphrm_employee_passport_country : ''; ?>
                                        <select class="form-control" name="wphrm_employee_passport_country" id="wphrm_employee_passport_country" >
                                            <option value="" >Select Country</option>
                                            <?php foreach($employee_country_array as $key => $country): ?>
                                            <option value="<?php echo $country->code; ?>" <?php selected( $country->code, $selected_passport_country ); ?>><?php _e($country->name, 'wphrm'); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if (in_array('manageOptionsEmployee', $wphrmGetPagePermissions)): ?>
                                <div class="form-group">
                                    <label class="control-label col-md-4" >Passport Date of Issue</label>
                                    <div class="col-md-8" >
                                        <div class="input-group input-medium date date-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                            <input class="form-control" name="wphrm_employee_passport_issued" id="wphrm_employee_passport_issued" value="<?php echo isset($employee_complete_info->work_permit_info->wphrm_employee_passport_issued) ? $employee_complete_info->work_permit_info->wphrm_employee_passport_issued : ''; ?>" autocapitalize="none" type="text">
                                            <span class="input-group-btn">
                                                <button class="btn default-date" type="button" disabled ><i class="fa fa-calendar" style="line-height: 1.9;"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if (in_array('manageOptionsEmployee', $wphrmGetPagePermissions)): ?>
                                <div class="form-group">
                                    <label class="control-label col-md-4" >Passport Expiration Date</label>
                                    <div class="col-md-8" >
                                        <div class="input-group input-medium date after-current-date" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                            <input class="form-control" name="wphrm_employee_passport_expiration" id="wphrm_employee_passport_expiration" value="<?php echo isset($employee_complete_info->work_permit_info->wphrm_employee_passport_expiration) ? $employee_complete_info->work_permit_info->wphrm_employee_passport_expiration : ''; ?>" autocapitalize="none" type="text">
                                            <span class="input-group-btn">
                                                <button class="btn default-date" type="button" disabled ><i class="fa fa-calendar" style="line-height: 1.9;"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if (in_array('manageOptionsEmployee', $wphrmGetPagePermissions)): ?>
                                <div class="form-group">
                                    <label class="control-label col-md-4"><?php _e('Employee Race', 'wphrm'); ?></label>
                                    <div class="col-md-8">
                                        <?php
                                        if( is_array($employee_races) && !empty($employee_races) ):
                                        $current_employee_race = isset($employee_complete_info->work_permit_info->wphrm_employee_race) ? $employee_complete_info->work_permit_info->wphrm_employee_race : '';
                                        ?>
                                        <!-- <input type="text" class="form-control" value="<?php echo $current_employee_race; ?>" name="wphrm_employee_race" > -->
                                        <?php endif; ?>
                                        <select class="form-control select2me"  name="wphrm_employee_race" type="text" id="wphrm_employee_race">
                                            <option value="">Select Race</option>
                                            <option value="Singaporean" <?php selected($current_employee_race, "Singaporean" ); ?> >Singaporean</option>
                                            <option value="Chinese" <?php selected($current_employee_race, "Chinese" ); ?> >Chinese</option>
                                            <option value="Malay" <?php selected($current_employee_race, "Malay" ); ?> >Malay</option>
                                            <option value="Indian" <?php selected($current_employee_race, "Indian" ); ?> >Indian</option>
                                            <option value="Others" <?php selected($current_employee_race, "Others" ); ?> >Others</option>
                                        </select>
                                    </div>
                                </div>
                                <?php else: ?>
                                <?php endif; ?>
                            </div>

                        </form>
                    </div>
                </div>

            </div>

        <?php endif; ?>
    
        </div>
        
        <?php if( isset($_GET['employee_id']) ) : ?>
        
            <div class="row">
            
            <div class="col-md-6 col-sm-6">
                <div class="portlet box red-sunglo other-details-div">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-comment-o"></i><?php _e('Other Details', 'wphrm'); ?>
                        </div>
                        <?php if (isset($wphrmEmployeeEditId) && $wphrmEmployeeEditId != '') { ?>
                        <!--<div class="actions">
                            <a href="javascript:;"  onclick="jQuery('#wphrmEmployeeOtherInfo_form').submit();" data-loading-text="Updating..."  class="demo-loading-btn btn btn-sm btn-default ">
                                <i class="fa fa-save" ></i> <?php _e('Save', 'wphrm'); ?> </a>
                        </div>-->
                        <?php } ?>
                    </div>
                    <div class="portlet-body">
                        <div class="alert alert-success display-hide" id="other_details_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrmMessagesOther); ?>
                            <button class="close" data-close="alert"></button>
                        </div>
                        <div class="alert alert-danger display-hide" id="other_details_success_error">
                            <button class="close" data-close="alert"></button>
                        </div>
                        <form method="POST"  accept-charset="UTF-8" class="form-horizontal" id="wphrmEmployeeOtherInfo_form">
                            <input type="hidden" name="wphrm_employee_id" id="wphrm_employee_id"  value="<?php
                                                                                                         if (isset($wphrmEmployeeEditId)) : echo esc_attr($wphrmEmployeeEditId);
                                                                                                         endif;
                                                                                                         ?> "/>
                            <div id="alert_bank"></div>
                            <div class="form-body">
                                <?php
                                if (in_array('manageOptionsEmployee', $wphrmGetPagePermissions)) {
                                    if (isset($wphrmEmployeeOtherInfo['wphrmotherfieldslebal']) && $wphrmEmployeeOtherInfo['wphrmotherfieldslebal'] != '' && isset($wphrmEmployeeOtherInfo['wphrmotherfieldsvalue']) && $wphrmEmployeeOtherInfo['wphrmotherfieldsvalue'] != '') {
                                        foreach ($wphrmEmployeeOtherInfo['wphrmotherfieldslebal'] as $lebalkey => $wphrmEmployeeSettingsOther) {
                                            foreach ($wphrmEmployeeOtherInfo['wphrmotherfieldsvalue'] as $valuekey => $wphrmOtherSettingsvalue) {
                                                if ($lebalkey == $valuekey) {
                                                //if( $wphrmEmployeeSettingsOther == 'Gmail Account' ) { $wphrmEmployeeSettingsOther = 'Email Account'; }
                                ?>
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e($wphrmEmployeeSettingsOther, 'wphrm'); ?></label>
                                    <input name="other-fields-lebal[]" type="hidden" id="other-fields-lebal" value="<?php
                                                    if (isset($wphrmEmployeeSettingsOther)) : echo esc_attr($wphrmEmployeeSettingsOther);
                                                    endif;
                                                                                                                 ?>"/>
                                    <div class="col-md-8">
                                        <input class="form-control" name="other-fields-value[]" type="text" id="other-fields-lebal" value="<?php
                                                    if (isset($wphrmOtherSettingsvalue)) : echo esc_attr($wphrmOtherSettingsvalue);
                                                    endif;
                                                                                                                                           ?>" autocapitalize="none"  />
                                    </div>
                                </div>
                                <?php
                                                }
                                            }
                                        }
                                        $wphrmOtherFieldsInfo = $this->WPHRMGetSettings('Otherfieldskey');
                                        if (!empty($wphrmOtherFieldsInfo)) {
                                            foreach ($wphrmOtherFieldsInfo['Otherfieldslebal'] as $wphrmOtherFieldsSettings) {
                                                if (!in_array($wphrmOtherFieldsSettings, $wphrmEmployeeOtherInfo['wphrmotherfieldslebal'])) {
                                ?>
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e($wphrmOtherFieldsSettings, 'wphrm'); ?></label>
                                    <input name="other-fields-lebal[]" type="hidden" id="other-fields-lebal" value="<?php
                                                    if (isset($wphrmOtherFieldsSettings)) : echo esc_attr($wphrmOtherFieldsSettings);
                                                    endif;
                                                                                                                    ?>"/>
                                    <div class="col-md-8">
                                        <input class="form-control" name="other-fields-value[]" type="text" id="other-fields-lebal" value="" autocapitalize="none"  />
                                    </div>
                                </div>
                                <?php
                                                }
                                            }
                                        }
                                    }else {
                                        $wphrmOtherfieldskeyInfo = $this->WPHRMGetSettings('Otherfieldskey');
                                        if (!empty($wphrmOtherfieldskeyInfo)) {
                                            foreach ($wphrmOtherfieldskeyInfo['Otherfieldslebal'] as $wphrmOthersettingInfo) {
                                ?>
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e($wphrmOthersettingInfo, 'wphrm'); ?></label>
                                    <input name="other-fields-lebal[]" type="hidden" id="other-fields-lebal" value="<?php
                                                if (isset($wphrmOthersettingInfo)) : echo esc_attr($wphrmOthersettingInfo);
                                                endif;
                                                                                                                    ?>"/>
                                    <div class="col-md-8">
                                        <input class="form-control" name="other-fields-value[]" type="text" id="other-fields-value" value="" autocapitalize="none"  />
                                    </div>
                                </div>
                                <?php
                                            }
                                        }
                                    }
                                ?>
                                <?php
                                }else {
                                    if (isset($wphrmEmployeeOtherInfo['wphrmotherfieldslebal']) && $wphrmEmployeeOtherInfo['wphrmotherfieldslebal'] != '' && isset($wphrmEmployeeOtherInfo['wphrmotherfieldsvalue']) && $wphrmEmployeeOtherInfo['wphrmotherfieldsvalue'] != '') {
                                        foreach ($wphrmEmployeeOtherInfo['wphrmotherfieldslebal'] as $lebalkey => $wphrmEmployeeSettingsOther) {
                                            foreach ($wphrmEmployeeOtherInfo['wphrmotherfieldsvalue'] as $valuekey => $wphrmOtherSettingsvalue) {
                                                if ($lebalkey == $valuekey) {
                                ?>
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e($wphrmEmployeeSettingsOther, 'wphrm'); ?></label>
                                    <div class="col-md-8">
                                        <input class="form-control" disabled="" type="text" id="other-fields-lebal" value="<?php
                                                    if (isset($wphrmOtherSettingsvalue)) : echo esc_attr($wphrmOtherSettingsvalue);
                                                    endif;
                                                                                                                           ?>" autocapitalize="none"  />
                                    </div>
                                </div>
                                <?php
                                                }
                                            }
                                        }
                                        $wphrmOtherFieldsInfo = $this->WPHRMGetSettings('Otherfieldskey');
                                        if (!empty($wphrmOtherFieldsInfo)) {
                                            foreach ($wphrmOtherFieldsInfo['Otherfieldslebal'] as $wphrmOtherFieldsSettings) {
                                                if (!in_array($wphrmOtherFieldsSettings, $wphrmEmployeeOtherInfo['wphrmotherfieldslebal'])) {
                                ?>
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e($wphrmOtherFieldsSettings, 'wphrm'); ?></label>
                                    <div class="col-md-8">
                                        <input class="form-control" disabled=""  type="text" id="other-fields-lebal" value="" autocapitalize="none"  />
                                    </div>
                                </div>
                                <?php
                                                }
                                            }
                                        }
                                    } else {
                                        $wphrmOtherfieldskeyInfo = $this->WPHRMGetSettings('Otherfieldskey');
                                        if (!empty($wphrmOtherfieldskeyInfo)) {
                                            foreach ($wphrmOtherfieldskeyInfo['Otherfieldslebal'] as $wphrmOthersettingInfo) {
                                ?>
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e($wphrmOthersettingInfo, 'wphrm'); ?></label>
                                    <div class="col-md-8">
                                        <input class="form-control" disabled=""  type="text" id="other-fields-value" value="" autocapitalize="none"  />
                                    </div>
                                </div>
                                <?php
                                            }
                                        }
                                    }
                                }
                                ?>

                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e('Vehicle', 'wphrm'); ?></label>
                                    <div class="col-md-8">
                                        <label for="wphrm_employee_vehicle" class="control-label"><?php _e('Do you come by vehicle', 'wphrm'); ?></label>
                                        <input class="form-control icheck" style="margin-top:7px;" name="wphrm_employee_vehicle" type="checkbox" id="wphrm_employee_vehicle" <?php
                                               if (isset($wphrmEmployeeOtherInfo['wphrm_employee_vehicle']) != '') : echo esc_attr('checked');
                                               endif;
                                               ?> value="checked" autocapitalize="none"  />
                                    </div>
                                    <div class="col-md-12 wphrm_vehicle_details" style="margin-top:15px;" id="wphrm_vehicle_details">


                                        <div class="form-group">
                                            <label class="col-md-4 control-label" scope="row"><?php _e('Vehicle Type', 'wphrm'); ?>:</label>
                                            <div class="col-md-8">
                                                <select class="form-control" name="wphrm_vehicle_type" id="wphrm_vehicle_type" >
                                                    <option <?php
                                                            if (isset($wphrmEmployeeOtherInfo['wphrm_vehicle_type']) && $wphrmEmployeeOtherInfo['wphrm_vehicle_type'] == 'Bike') {
                                                                echo esc_attr('selected');
                                                            }
                                                            ?> value="Bike"><?php _e('Bike', 'wphrm'); ?></option>
                                                    <option <?php
                                                            if (isset($wphrmEmployeeOtherInfo['wphrm_vehicle_type']) && $wphrmEmployeeOtherInfo['wphrm_vehicle_type'] == 'Car') {
                                                                echo esc_attr('selected');
                                                            }
                                                            ?> value="Car"><?php _e('Car', 'wphrm'); ?></option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-4 control-label" scope="row"><?php _e('Vehicle Number', 'wphrm'); ?>:</label>
                                            <div class="col-md-8">
                                                <input class="form-control" type="text" name="wphrm_employee_vehicle_registrationno" id="wphrm_employee_vehicle_registrationno" value="<?php
                                                    if (isset($wphrmEmployeeOtherInfo['wphrm_employee_vehicle_registrationno'])) : echo esc_attr($wphrmEmployeeOtherInfo['wphrm_employee_vehicle_registrationno']);
                                                    endif;
                                                    ?>"/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-4 control-label" scope="row"><?php _e('Vehicle IU Number', 'wphrm'); ?>:</label>
                                            <div class="col-md-8">
                                                <input class="form-control" type="text" name="wphrm_employee_vehicle_model" id="wphrm_employee_vehicle_model" value="<?php
                                                    if (isset($wphrmEmployeeOtherInfo['wphrm_employee_vehicle_model'])) : echo esc_attr($wphrmEmployeeOtherInfo['wphrm_employee_vehicle_model']);
                                                    endif;
                                                    ?>"/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-4 control-label" scope="row"><?php _e('Vehicle Expiration', 'wphrm'); ?>:</label>
                                            <div class="col-md-8">
                                                <div class="input-group input-medium date after-current-date" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                                    <input class="form-control" name="wphrm_employee_vehicle_expiration" id="wphrm_employee_vehicle_expiration" value="<?php echo isset($wphrmEmployeeOtherInfo['wphrm_employee_vehicle_expiration']) ? $wphrmEmployeeOtherInfo['wphrm_employee_vehicle_expiration'] : ''; ?>" autocapitalize="none" type="text">
                                                    <span class="input-group-btn">
                                                        <button class="btn default-date" type="button" disabled ><i class="fa fa-calendar" style="line-height: 1.9;"></i></button>
                                                    </span>
                                                </div>
                                                <!-- <input class="form-control" type="text" name="wphrm_employee_vehicle_expiration" id="wphrm_employee_vehicle_expiration" value="<?php
                                                    if (isset($wphrmEmployeeOtherInfo['wphrm_employee_vehicle_expiration'])) : echo esc_attr($wphrmEmployeeOtherInfo['wphrm_employee_vehicle_expiration']);
                                                    endif;
                                                    ?>"/> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e('T-Shirt Size (Male)', 'wphrm'); ?></label>
                                    <div class="col-md-8">
                                        <?php $wphrmTShirtSizes = array('xxxs' => 'XXXS', 'xxs' => 'XXS', 'xs' => 'XS', 's' => 'S', 'm' => 'M', 'l' => 'L', 'xl' => 'XL', 'xxl' => 'XXL', 'xxxl' => 'XXXL'); ?>
                                        <select class="form-control" name="wphrm_t_shirt_size_male" id="wphrm_t_shirt_size_male">
                                            <option value=""><?php _e('Select your size', 'wphrm'); ?></option>
                                            <?php foreach ($wphrmTShirtSizes as $key => $size) : ?>
                                            <option <?php
                                                    if (isset($wphrmEmployeeOtherInfo['wphrm_t_shirt_size_male']) &&
                                                        $wphrmEmployeeOtherInfo['wphrm_t_shirt_size_male'] == $key) {
                                                        echo esc_attr('selected');
                                                    }
                                                    ?>
                                                    value="<?php echo esc_attr($key); ?>"><?php echo esc_html($size); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label"><?php _e('T-Shirt Size (Female)', 'wphrm'); ?></label>
                                    <div class="col-md-8">
                                        <?php $wphrmTShirtSizes = array('xxxs' => 'XXXS', 'xxs' => 'XXS', 'xs' => 'XS', 's' => 'S', 'm' => 'M', 'l' => 'L', 'xl' => 'XL', 'xxl' => 'XXL', 'xxxl' => 'XXXL'); ?>
                                        <select class="form-control" name="wphrm_t_shirt_size_male" id="wphrm_t_shirt_size_female">
                                            <option value=""><?php _e('Select your size', 'wphrm'); ?></option>
                                            <?php foreach ($wphrmTShirtSizes as $key => $size) : ?>
                                            <option <?php
                                                    if (isset($wphrmEmployeeOtherInfo['wphrm_t_shirt_size_female']) &&
                                                        $wphrmEmployeeOtherInfo['wphrm_t_shirt_size_female'] == $key) {
                                                        echo esc_attr('selected');
                                                    }
                                                    ?>
                                                    value="<?php echo esc_attr($key); ?>"><?php echo esc_html($size); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-sm-6">


                <?php if (in_array('manageOptionsEmployee', $wphrmGetPagePermissions)): ?>
                <div class="portlet box red-sunglo">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-users"></i><?php _e('Family Background', 'wphrm'); ?>
                        </div>
                        <?php if (isset($wphrmEmployeeEditId) && $wphrmEmployeeEditId != '') { ?>
                        <!--<div class="actions">
                            <a href="javascript:;"  onclick="jQuery('#wphrmEmployeeFamilyInfo_form').submit();" data-loading-text="Updating..."  class="demo-loading-btn btn btn-sm btn-default ">
                                <i class="fa fa-save" ></i><?php _e('Save', 'wphrm'); ?>  </a>
                        </div>-->
                        <?php } ?>
                    </div>
                    <div class="portlet-body">
                        <div class="alert alert-success display-hide" id="family_details_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrmMessagesFamily); ?>
                            <button class="close" data-close="alert"></button>
                        </div>
                        <div class="alert alert-danger display-hide" id="family_details_success_error">
                            <button class="close" data-close="alert"></button>
                        </div>
                        <form method="POST"  accept-charset="UTF-8" class="form-horizontal" id="wphrmEmployeeFamilyInfo_form" autocomplete="off" >
                            <input type="hidden" name="wphrm_employee_id" id="wphrm_employee_id"  value="<?php
                                                                                                         if (isset($wphrmEmployeeEditId)) : echo esc_attr($wphrmEmployeeEditId);
                                                                                                         endif;
                                                                                                         ?> "/>
                            <div id="alert_bank"></div>
                            <div class="form-body">

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <h5>Parents/Sibling/Spouse working</h5>
                                    </div>
                                    <div class="col-md-12">

                                        <table class="table-condensed table table-hover table-striped wphrmEmployeeFamilyInfo-table">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Relations</th>
                                                    <th>Working in Related Industries (Yes/No)? If yes, please specify:</th>
                                                    <th>Company Name</th>
                                                    <th>Occupation</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $family_members = empty($wphrmEmployeeFamilyInfo['family-members']) ? array() : $wphrmEmployeeFamilyInfo['family-members']; ?>
                                                <?php $count = empty($family_members['family-member-names']) ? 0 : count($family_members['family-member-names']); ?>
                                                <?php $family_info_keys = array_keys($wphrmEmployeeFamilyInfo); ?>
                                                <tr class="blank-template hide">
                                                    <td>
                                                        <input class="form-control family-member-name" name="family-member-name[]" type="text" id="" value="" autocapitalize="none"  />
                                                    </td>
                                                    <td>
                                                        <input class="form-control family-member-relations" name="family-member-relations[]" type="text" id="" value="" autocapitalize="none"  />
                                                    </td>
                                                    <td>
                                                        <input class="form-control family-member-work_related" name="family-member-work_related[]" type="text" id="" value="" autocapitalize="none"  />
                                                    </td>
                                                    <td>
                                                        <input class="form-control family-member-company_name" name="family-member-company_name[]" type="text" id="" value="" autocapitalize="none"  />
                                                    </td>
                                                    <td>
                                                        <input class="form-control family-member-occupation" name="family-member-occupation[]" type="text" id="" value="" autocapitalize="none"  />
                                                    </td>
                                                    <td>
                                                        <a class="tiny table-action-button add-new-row" ><i class="fa fa-plus" ></i></a>
                                                    </td>
                                                </tr>
                                                <?php for( $i = 0; $i < $count; $i++ ): ?>
                                                <tr>
                                                    <td>
                                                        <input class="form-control family-member-name" name="family-member-name[]" type="text" id="" value="<?php echo empty($family_members['family-member-names'][$i]) ? '' : $family_members['family-member-names'][$i]; ?>" autocapitalize="none"  />
                                                    </td>
                                                    <td>
                                                        <input class="form-control family-member-relations" name="family-member-relations[]" type="text" id="" value="<?php echo empty($family_members['family-member-relations'][$i]) ? '' : $family_members['family-member-relations'][$i]; ?>" autocapitalize="none"  />
                                                    </td>
                                                    <td>
                                                        <input class="form-control family-member-work_related" name="family-member-work_related[]" type="text" id="" value="<?php echo empty($family_members['family-member-work_related'][$i]) ? '' : $family_members['family-member-work_related'][$i]; ?>" autocapitalize="none"  />
                                                    </td>
                                                    <td>
                                                        <input class="form-control family-member-company_name" name="family-member-company_name[]" type="text" id="" value="<?php echo empty($family_members['family-member-company_name'][$i]) ? '' : $family_members['family-member-company_name'][$i]; ?>" autocapitalize="none"  />
                                                    </td>
                                                    <td>
                                                        <input class="form-control family-member-occupation" name="family-member-occupation[]" type="text" id="" value="<?php echo empty($family_members['family-member-occupation'][$i]) ? '' : $family_members['family-member-occupation'][$i]; ?>" autocapitalize="none"  />
                                                    </td>
                                                    <td>
                                                        <a class="tiny table-action-button <?php echo ($i < $count-1) ? 'remove-item-row' : 'add-new-row'; ?>" ><i class="fa <?php echo ($i < $count-1) ? 'fa-minus' : 'fa-plus'; ?>" ></i></a>
                                                    </td>
                                                </tr>
                                                <?php endfor; ?>
                                            </tbody>
                                        </table>


                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <h5>Only for employees with children’s</h5>
                                    </div>
                                    <div class="col-md-12 children-info-wrapper">

                                        <div class="alert alert-danger display-hide" id="children-info-message">
                                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                                            Please fill up your children information.
                                            <button class="close" data-close="alert"></button>
                                        </div>

                                        <table class="table-condensed table table-hover table-striped wphrmEmployeeFamilyInfo-table-children">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>NRIC</th>
                                                    <th>DOB</th>
                                                    <th>Age</th>
                                                    <th>Gender</th>
                                                    <th>Nationality</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $childrens = empty($wphrmEmployeeFamilyInfo['employee-children']) ? array() : $wphrmEmployeeFamilyInfo['employee-children']; ?>
                                                <?php $count = empty($childrens['child-names']) ? 0 : count($childrens['child-names']); ?>
                                                <?php $family_info_keys = array_keys($wphrmEmployeeFamilyInfo); ?>
                                                <tr class="blank-template hide">
                                                    <td>
                                                        <input class="form-control child-name" name="child-name[]" type="text" id="" value="" autocapitalize="none"  />
                                                    </td>
                                                    <td>
                                                        <input class="form-control child-nric" name="child-nric[]" type="text" id="" value="" autocapitalize="none"  />
                                                    </td>
                                                    <td>
                                                        <!-- <input class="form-control child-dob" name="child-dob[]" type="text" id="" value="" autocapitalize="none"  /> -->
                                                        <div class="input-group date date-picker bdate-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                                            <input class="form-control" name="child-dob[]" autocapitalize="none" type="text">
                                                            <span class="input-group-btn">
                                                                <button class="btn default-date display-hide" type="button"><i class="fa fa-calendar" style="line-height: 1.9;"></i></button>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input class="form-control child-age" name="child-age[]" type="text" id="" value="" autocapitalize="none" readonly  />
                                                    </td>
                                                    <td>
                                                        <!-- <input class="form-control child-gender" name="family-member-gender[]" type="text" id="" value="" autocapitalize="none"  /> -->
                                                        <select class="form-control child-gender" name="family-member-gender[]">
                                                            <option value="">--Select--</option>
                                                            <option value="Male">Male</option>
                                                            <option value="Female">Female</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <!-- <input class="form-control child-nationality" name="family-member-nationality[]" type="text" id="" value="" autocapitalize="none"  /> -->
                                                        <!-- <select class="form-control child-nationality" name="family-member-nationality[]" type="text" id="">
                                                            <option value="">--Select--</option>
                                                            <option value="Chinese">Chinese</option>
                                                            <option value="Malaysian">Malaysian</option>
                                                            <option value="Mongolian">Mongolian</option>
                                                            <option value="Burmese">Burmese</option>
                                                            <option value="Singaporean">Singaporean</option>
                                                            <option value="Vietnamese">Vietnamese</option>
                                                            <option value="Others">Others</option>
                                                        </select> -->
                                                        <select class="form-control child-nationality" name="child-nationality[]" >
                                                            <option value="" >--Select--</option>
                                                            <?php foreach($employee_nationality as $nationality): ?>
                                                            <option value="<?php echo $nationality; ?>"><?php _e($nationality, 'wphrm'); ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <a class="tiny table-action-button add-new-row" ><i class="fa fa-plus" ></i></a>
                                                    </td>
                                                </tr>
                                                <?php for( $i = 0; $i < $count; $i++ ): ?>
                                                <tr>
                                                    <td>
                                                        <input class="form-control child-name" name="child-name[]" type="text" id="" value="<?php echo empty($childrens['child-names'][$i]) ? '' : $childrens['child-names'][$i]; ?>" autocapitalize="none"  />
                                                    </td>
                                                    <td>
                                                        <input class="form-control child-nric" name="child-nric[]" type="text" id="" value="<?php echo empty($childrens['child-nric'][$i]) ? '' : $childrens['child-nric'][$i]; ?>" autocapitalize="none"  />
                                                    </td>
                                                    <td>
                                                        <!-- <input class="form-control child-dob child-bod" name="child-dob[]" type="text" id="" value="<?php //echo empty($childrens['child-dob'][$i]) ? '' : $childrens['child-dob'][$i]; ?>" autocapitalize="none"  /> -->
                                                        <div class="input-group date date-picker bdate-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                                            <input class="form-control" name="child-dob[]" value="<?php echo empty($childrens['child-dob'][$i]) ? '' : $childrens['child-dob'][$i]; ?>" autocapitalize="none" type="text">
                                                            <span class="input-group-btn">
                                                                <button class="btn default-date display-hide" type="button"><i class="fa fa-calendar" style="line-height: 1.9;"></i></button>
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input class="form-control child-age" name="child-age[]" type="text" id="" value="<?php echo empty($childrens['child-age'][$i]) ? '' : $childrens['child-age'][$i]; ?>" autocapitalize="none" readonly  />
                                                    </td>
                                                    <td>
                                                        <!-- <input class="form-control child-gender" name="child-gender[]" type="text" id="" value="<?php //echo empty($childrens['child-gender'][$i]) ? '' : $childrens['child-gender'][$i]; ?>" autocapitalize="none"  /> -->
                                                        <select class="form-control child-gender" name="child-gender[]">
                                                            <option value="">--Select--</option>
                                                            <option value="Male"  <?php selected($childrens['child-gender'][$i], "Male" ); ?> >Male</option>
                                                            <option value="Female"  <?php selected($childrens['child-gender'][$i], "Female" ); ?> >Female</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <!-- <input class="form-control child-nationality" name="child-nationality[]" type="text" id="" value="<?php //echo empty($childrens['child-nationality'][$i]) ? '' : $childrens['child-nationality'][$i]; ?>" autocapitalize="none"  /> -->
                                                        <!-- <select class="form-control child-nationality" name="child-nationality[]">
                                                            <option value="">--Select--</option>
                                                            <option value="Chinese" <?php selected($childrens['child-nationality'][$i], "Chinese" ); ?> >Chinese</option>
                                                            <option value="Malaysian" <?php selected($childrens['child-nationality'][$i], "Malaysian" ); ?> >Malaysian</option>
                                                            <option value="Mongolian" <?php selected($childrens['child-nationality'][$i], "Mongolian" ); ?> >Mongolian</option>
                                                            <option value="Burmese" <?php selected($childrens['child-nationality'][$i], "Burmese" ); ?> >Burmese</option>
                                                            <option value="Singaporean" <?php selected($childrens['child-nationality'][$i], "Singaporean" ); ?> >Singaporean</option>
                                                            <option value="Vietnamese" <?php selected($childrens['child-nationality'][$i], "Vietnamese" ); ?> >Vietnamese</option>
                                                            <option value="Others" <?php selected($childrens['child-nationality'][$i], "Others" ); ?> >Others</option>
                                                        </select> -->
                                                        <?php $selected_child_nationality = $childrens['child-nationality'][$i];?>
                                                            <select class="form-control child-nationality" name="child-nationality[]" >
                                                                <option value="" >--Select--</option>
                                                                <?php foreach($employee_nationality as $nationality): ?>
                                                                <option value="<?php echo $nationality; ?>" <?php selected( $nationality, $selected_child_nationality ); ?>><?php _e($nationality, 'wphrm'); ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                    </td>
                                                    <td>
                                                        <?php if( $i == $count - 1 ) { ?>
                                                            <a class="tiny table-action-button remove-item-row" ><i class="fa fa-minus" ></i></a>
                                                        <?php } ?>
                                                        <a class="tiny table-action-button <?php echo ($i < $count-1) ? 'remove-item-row' : 'add-new-row'; ?>" ><i class="fa <?php echo ($i < $count-1) ? 'fa-minus' : 'fa-plus'; ?>" ></i></a>
                                                    </td>
                                                </tr>
                                                <?php endfor; ?>
                                            </tbody>
                                        </table>


                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <h5>Contact person in case of emergency</h5>
                                    </div>
                                    <div class="col-md-12">
                                        
                                        <table class="table-condensed table table-hover table-striped wphrmEmployeeFamilyInfo-table-emergencies">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Relations</th>
                                                    <th>Address</th>
                                                    <th>Handphone Number</th>
                                                    <th>Alternative Number</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $emergencies = empty($wphrmEmployeeFamilyInfo['employee-emergency-contact']) ? array() : $wphrmEmployeeFamilyInfo['employee-emergency-contact']; ?>
                                                <?php $ecount = empty($emergencies['emergency-name']) ? 0 : count($emergencies['emergency-name']); v?>
                                                <?php $family_info_keys = array_keys($wphrmEmployeeFamilyInfo); ?>
                                                <!--<tr class="blank-template hide">
                                                    <td>
                                                        <input class="form-control emergency-name" name="emergency-name[]" type="text" id="" value="" autocapitalize="none"  />
                                                    </td>
                                                    <td>
                                                        <input class="form-control emergency-relations" name="emergency-relations[]" type="text" id="" value="" autocapitalize="none"  />
                                                    </td>
                                                    <td>
                                                        <input class="form-control emergency-number" name="emergency-number[]" type="text" id="" value="" autocapitalize="none"  />
                                                    </td>
                                                    <td>
                                                        <a class="tiny table-action-button add-new-row" ><i class="fa fa-plus" ></i></a>
                                                    </td>
                                                </tr>-->
                                                <?php //for( $x = 0; $x < $ecount; $x++ ): ?>
                                                <tr>
                                                    <td>
                                                        <input class="form-control emergency-name" name="emergency-name[]" type="text" id="" value="<?php echo empty($emergencies['emergency-name'][0]) ? '' : $emergencies['emergency-name'][0]; ?>" autocapitalize="none"  />
                                                    </td>
                                                    <td>
                                                        <input class="form-control emergency-re" name="emergency-relations[]" type="text" id="" value="<?php echo empty($emergencies['emergency-relations'][0]) ? '' : $emergencies['emergency-relations'][0]; ?>" autocapitalize="none"  />
                                                    </td>
                                                    <td>
                                                        <input class="form-control emergency-add" name="emergency-address[]" type="text" id="" value="<?php echo empty($emergencies['emergency-address'][0]) ? '' : $emergencies['emergency-address'][0]; ?>" autocapitalize="none"  />
                                                    </td>
                                                    <td>
                                                        <input class="form-control emergency-number" name="emergency-number[]" type="text" id="" value="<?php echo empty($emergencies['emergency-number'][0]) ? '' : $emergencies['emergency-number'][0]; ?>" autocapitalize="none"  />
                                                    </td>
                                                    <td>
                                                        <input class="form-control emergency-alt-number" name="emergency-alt-number[]" type="text" id="" value="<?php echo empty($emergencies['emergency-alt-number'][0]) ? '' : $emergencies['emergency-alt-number'][0]; ?>" autocapitalize="none"  />
                                                    </td>
                                                    <!--<td>
                                                        <a class="tiny table-action-button <?php //echo ($x < $ecount-1) ? 'remove-item-row' : 'add-new-row'; ?>" ><i class="fa <?php //echo ($x < $ecount-1) ? 'fa-minus' : 'fa-plus'; ?>" ></i></a>
                                                    </td>-->
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <input class="form-control emergency-name-2" name="emergency-name-2[]" type="text" id="" value="<?php echo empty($emergencies['emergency-name-2'][0]) ? '' : $emergencies['emergency-name-2'][0]; ?>" autocapitalize="none"  />
                                                    </td>
                                                    <td>
                                                        <input class="form-control emergency-re-2" name="emergency-relations-2[]" type="text" id="" value="<?php echo empty($emergencies['emergency-relations-2'][0]) ? '' : $emergencies['emergency-relations-2'][0]; ?>" autocapitalize="none"  />
                                                    </td>
                                                    <td>
                                                        <input class="form-control emergency-add-2" name="emergency-address-2[]" type="text" id="" value="<?php echo empty($emergencies['emergency-address-2'][0]) ? '' : $emergencies['emergency-address-2'][0]; ?>" autocapitalize="none"  />
                                                    </td>
                                                    <td>
                                                        <input class="form-control emergency-number-2" name="emergency-number-2[]" type="text" id="" value="<?php echo empty($emergencies['emergency-number-2'][0]) ? '' : $emergencies['emergency-number-2'][0]; ?>" autocapitalize="none"  />
                                                    </td>
                                                    <td>
                                                        <input class="form-control emergency-alt-number-2" name="emergency-alt-number-2[]" type="text" id="" value="<?php echo empty($emergencies['emergency-alt-number-2'][0]) ? '' : $emergencies['emergency-alt-number-2'][0]; ?>" autocapitalize="none"  />
                                                    </td>
                                                </tr>
                                                <?php //endfor; ?>
                                            </tbody>
                                        </table>


                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
                <?php else: ?>
                <?php endif; ?>
            </div>

            <div class="col-md-6 col-sm-6">
                <div class="portlet blue box ">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-edit"></i><?php _e('Leave Entitlement', 'wphrm'); ?>
                        </div>

                        <!--<div class="actions">
                            <a href="javascript:;"  onclick="jQuery('#wphrm_employee_work_info_form').submit();" data-loading-text="Updating..."  class="demo-loading-btn btn btn-sm btn-default ">
                                <i class="fa fa-save" ></i><?php //_e('Save', 'wphrm'); ?>
                            </a>
                        </div>-->
                    </div>
                    <div class="portlet-body">
                        
                        <div class="form-group">                            
                            <div class="leave-entitlement-holder">
                                <!--<label class="description">Select leaves for employee <a class="select-all-checkbox" data-checked="no" >Select All</a></label>-->
                                <?php
                                $userid = empty( $_GET['employee_id'] ) ? $current_user->ID : $_GET['employee_id'];
                                $is_regular = $this->is_employee_regular($userid);
                                if( $is_regular ) {
                                    $selected = empty($wphrmEmployeeBasicInfo['wphrm_employee_entitled_leave']) ? array() : $wphrmEmployeeBasicInfo['wphrm_employee_entitled_leave'];
                                    $leave_counts = isset($wphrmEmployeeBasicInfo['wphrm_employee_leave_count']) && is_array($wphrmEmployeeBasicInfo['wphrm_employee_leave_count']) ? $wphrmEmployeeBasicInfo['wphrm_employee_leave_count'] : array();
                                    $wphrm_leavetypes = $wpdb->get_results("SELECT * FROM  $this->WphrmLeaveTypeTable WHERE leave_rules != 0 ORDER BY leaveType ASC");
                                    foreach ($wphrm_leavetypes as $wphrm_leavetype) {

                                        $selected_employee_level = isset($wphrmEmployeeBasicInfo['wphrm_employee_level']) ? $wphrmEmployeeBasicInfo['wphrm_employee_level'] : '';


                                        $disable = false;
                                        $leave_rules = $this->get_leave_rule( $wphrm_leavetype->leave_rules );
                                        if( $leave_rules ) {
                                            $child_nationality = '';

                                            //date of birth
                                            $year_today = date('Y');
                                            $bdate = explode( '-', $wphrmEmployeeBasicInfo['wphrm_employee_bod'] );
                                            $dob = $year_today - $bdate[2]; 

                                            //joining date
                                            $date_today = date('d-m-Y');
                                            $joining_date = explode( '-', $wphrmEmployeeBasicInfo['wphrm_employee_joining_date'] );
                                            $date_hired = $year_today - $joining_date[2];

                                            //gender
                                            if( $leave_rules->gender == 1 ) {
                                                $emp_gender = 'Male';
                                            } elseif( $leave_rules->gender == 2 ) {
                                                $emp_gender = 'Female';
                                            } else {
                                                $emp_gender = 'N/A';
                                            } 

                                            $et = explode( ',', $leave_rules->employeeType );
                                            if( in_array( $selected_employee_level, $et ) || $leave_rules->employeeType == 'N/A' ) {
                                                $emp_type = true;
                                            } else {
                                                $emp_type = '';
                                            }

                                            /*if( $leave_rules->years_in_service != 0 && $leave_rules->years_in_service >= $date_hired || $leave_rules->years_in_service == 0 ) {
                                                $yis = true;
                                            } else {
                                                $yis = '';
                                            }*/
                                            $yis = true;


                                            if( $leave_rules->employment_status != 0 && $leave_rules->employment_status == 2 && $is_regular || $leave_rules->employment_status != 0 && $leave_rules->employment_status == 1 && $is_regular || $leave_rules->employment_status == 0 ) {
                                                $emp_status = true;
                                            } else {
                                                $emp_status = '';
                                            }
                                            if( $leave_rules->gender != 0 && $emp_gender == $wphrmEmployeeBasicInfo['wphrm_employee_gender'] || $leave_rules->gender == 0 ) {
                                                $gender = true;
                                            } else {
                                                $gender = '';
                                            }
                                            if( $leave_rules->nationality == 1 && $employee_complete_info->work_permit_info->wphrm_employee_nationality == 'Singaporean' || $leave_rules->nationality == 1 && $employee_complete_info->work_permit_info->wphrm_employee_nationality == 'Singapore Permanent Resident' || $leave_rules->nationality == 2 && $employee_complete_info->work_permit_info->wphrm_employee_nationality == 'Foreigner' || $leave_rules->nationality == 0 ) {
                                                $nationality = true;
                                            } else {
                                                $nationality = '';
                                            }
                                            if( $dob >= $leave_rules->age || $leave_rules->age == 0 ) {
                                                $age = true;
                                            } else {
                                                $age = '';
                                            }
                                            if( $leave_rules->marital_status != 'N/A' && $leave_rules->marital_status == $wphrmEmployeeBasicInfo['wphrm_employee_mstatus'] || $leave_rules->marital_status == 'N/A' ) {
                                                $marital_status = true;
                                            } else {
                                                $marital_status = '';
                                            }

                                            $i = 0;
                                            $child_age = 0;
                                            $child_nat = 0;
                                            for( $i = 0; $i < $count; $i++ ) {
                                                if( $childrens['child-nationality'][$i] == 'Singaporean' && $leave_rules->child_nationality == 1 && $employee_complete_info->work_permit_info->wphrm_employee_nationality == 'Singaporean' || $childrens['child-nationality'][$i] == 'Singaporean' && $leave_rules->child_nationality == 1 && $employee_complete_info->work_permit_info->wphrm_employee_nationality == 'Singapore Permanent Resident' ) {
                                                    $child_nat++;
                                                } elseif( $childrens['child-nationality'][$i] != 'Singaporean' && $leave_rules->child_nationality == 2 &&  $employee_complete_info->work_permit_info->wphrm_employee_nationality == 'Foreigner' ) {
                                                    if( $employee_complete_info->work_permit_info->wphrm_employee_nric != '' && $employee_complete_info->work_permit_info->wphrm_employee_work_permit_type != '' ) {
                                                        $child_nat++;
                                                    } else {
                                                        $child_nat = 0;
                                                    }                                                
                                                } elseif( $leave_rules->child_nationality == 0 ) {
                                                    $child_nat++;
                                                } else {
                                                    $child_nat++;
                                                }

                                                if( $leave_rules->child_age_limit > $childrens['child-age'][$i] && !empty( $childrens['child-age'][$i] ) || $leave_rules->child_age_limit == 0 ) {
                                                    $child_age++;
                                                }
                                            }

                                            if( $child_age > 0 ) {
                                                $child_age_limit = true;
                                            } else {
                                                $child_age_limit = false;
                                            }
                                            if( $child_nat > 0 ) {
                                                $child_nationality = true;
                                            } else {
                                                $child_nationality = false;
                                            }

                                            if( $emp_type && $emp_status && $yis && $gender && $nationality && $age && $marital_status && $child_nationality && $child_age_limit ) {
                                                $disable = true;
                                            } else {
                                                $disable = false;
                                                
                                            }


                                            if( $disable ) {

                                                //remove gender based leaves
                                                /*if( ($wphrm_leavetype->leave_rules == 'maternity_leave' && isset($wphrmEmployeeBasicInfo['wphrm_employee_gender']) && $wphrmEmployeeBasicInfo['wphrm_employee_gender'] == 'Male') ) continue;
                                                if( ($wphrm_leavetype->leave_rules == 'paternity_leave' && isset($wphrmEmployeeBasicInfo['wphrm_employee_gender']) && $wphrmEmployeeBasicInfo['wphrm_employee_gender'] == 'Female') ) continue;*/
                                    ?>
                                                <p>
                                                    <input class="form-control icheck" onclick="return false;" onkeydown="return false;" style="margin-top:7px;" name="wphrm_employee_entitled_leave[]" class="wphrm_employee_entitled_leave" value="<?php echo $wphrm_leavetype->id; ?>" autocapitalize="none" type="checkbox" checked >
                                                    <span class="leave-count-wrap hide"><input type="number" name="wphrm_employee_leave_count[<?php echo $wphrm_leavetype->id; ?>]" class="leave-count" value="<?php echo isset($leave_counts["$wphrm_leavetype->id"]) ? $leave_counts["$wphrm_leavetype->id"] : ''; ?>" min="0" max="99"></span>&nbsp;

                                                    <label for="" class="control-label"><?php 
                                                    if (isset($wphrm_leavetype->leaveType)) : echo esc_attr($wphrm_leavetype->leaveType);
                                                    endif;
                                                        ?></label>
                                                </p>
                                    <?php 
                                            } else {
                                                continue;
                                            }//end disable
                                        } // end leave_rules
                                    }
                                } else { 
                                    $selected = empty($wphrmEmployeeBasicInfo['wphrm_employee_entitled_leave']) ? array() : $wphrmEmployeeBasicInfo['wphrm_employee_entitled_leave'];
                                    $leave_counts = isset($wphrmEmployeeBasicInfo['wphrm_employee_leave_count']) && is_array($wphrmEmployeeBasicInfo['wphrm_employee_leave_count']) ? $wphrmEmployeeBasicInfo['wphrm_employee_leave_count'] : array();
                                    $wphrm_leavetypes = $wpdb->get_results("SELECT * FROM  $this->WphrmLeaveTypeTable WHERE leave_rules != 0 ORDER BY leaveType ASC");
                                    foreach ($wphrm_leavetypes as $wphrm_leavetype) {
                                        if( ($wphrm_leavetype->leave_rules == 'maternity_leave' && isset($wphrmEmployeeBasicInfo['wphrm_employee_gender']) && $wphrmEmployeeBasicInfo['wphrm_employee_gender'] == 'Male') ) continue;
                                        if( ($wphrm_leavetype->leave_rules == 'paternity_leave' && isset($wphrmEmployeeBasicInfo['wphrm_employee_gender']) && $wphrmEmployeeBasicInfo['wphrm_employee_gender'] == 'Female') ) continue;
                                        
                                        $disable = false;
                                        $leave_rules = $this->get_leave_rule( $wphrm_leavetype->leave_rules );
                                        if( $leave_rules ) {
                                            if( $leave_rules->employment_status == 1 || $leave_rules->employment_status == 3 ) {
                                ?>
                                        <p>
                                            <input class="form-control icheck" onclick="return false;" onkeydown="return false;" style="margin-top:7px;" name="wphrm_employee_entitled_leave[]" class="wphrm_employee_entitled_leave" value="<?php echo $wphrm_leavetype->id; ?>" autocapitalize="none" type="checkbox" checked >
                                            <span class="leave-count-wrap hide"><input type="number" name="wphrm_employee_leave_count[<?php echo $wphrm_leavetype->id; ?>]" class="leave-count" value="<?php echo isset($leave_counts["$wphrm_leavetype->id"]) ? $leave_counts["$wphrm_leavetype->id"] : ''; ?>" min="0" max="99"></span>&nbsp;

                                            <label for="" class="control-label"><?php 
                                            if (isset($wphrm_leavetype->leaveType)) : echo esc_attr($wphrm_leavetype->leaveType);
                                            endif;
                                                ?></label>
                                        </p>
                                <?php
                                            }
                                        }
                                    }
                                }//check if is regular
                            ?>
                                <p class="description hide">if blank, leave count will be taken from leave type settings (in exception of autocalculated leave entitlements, eg. annual leave).</p>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
        
        <?php endif; ?>
    </div>
</div>