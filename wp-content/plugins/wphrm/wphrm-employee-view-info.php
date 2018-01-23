<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $current_user, $wpdb;
$wphrmUserRole = implode(',', $current_user->roles);
$readonly_class = '';
$readonly = 'readonly';
$edit_mode = false;
$wphrmGetPagePermissions = $this->WPHRMGetPagePermissions();
if (isset($_REQUEST['employee_id']) && $_REQUEST['employee_id'] != '') {
    $wphrm_employee_edit_id = $_REQUEST['employee_id'];
}
else {
    $wphrm_employee_edit_id = $current_user->ID;
}


$employee_country_array = $this->get_country_list( );

$userInformation = get_userdata( $wphrm_employee_edit_id );
$wphrmEmployeeBasicInfo = $this->WPHRMGetUserDatas($wphrm_employee_edit_id, 'wphrmEmployeeInfo');
$wphrmEmployeeDocumentsInfo = $this->WPHRMGetUserDatas($wphrm_employee_edit_id, 'wphrmEmployeeDocumentInfo');

$resumeDir = '';
if (isset($wphrmEmployeeDocumentsInfo['resume'] ) && $wphrmEmployeeDocumentsInfo['resume'] != '') {
    $rdirs = explode('/', $wphrmEmployeeDocumentsInfo['resume']);
    $resumeDir = $rdirs[count($rdirs) - 1];
}
$offerDir = '';
if (isset($wphrmEmployeeDocumentsInfo['offerLetter'] ) && $wphrmEmployeeDocumentsInfo['offerLetter'] != '') {
    $rdirs = explode('/', $wphrmEmployeeDocumentsInfo['offerLetter']);
    $offerDir = $rdirs[count($rdirs) - 1];
}
$joiningDir = '';
if (isset($wphrmEmployeeDocumentsInfo['joiningLetter'] ) && $wphrmEmployeeDocumentsInfo['joiningLetter'] != '') {
    $rdirs = explode('/', $wphrmEmployeeDocumentsInfo['joiningLetter']);
    $joiningDir = $rdirs[count($rdirs) - 1];
}
$contractDir = '';
if (isset($wphrmEmployeeDocumentsInfo['contract'] ) && $wphrmEmployeeDocumentsInfo['contract'] != '') {
    $rdirs = explode('/', $wphrmEmployeeDocumentsInfo['contract']);
    $contractDir = $rdirs[count($rdirs) - 1];
}
$idProofDir = '';
if (isset($wphrmEmployeeDocumentsInfo['IDProof'] ) && $wphrmEmployeeDocumentsInfo['IDProof'] != '') {
    $rdirs = explode('/', $wphrmEmployeeDocumentsInfo['IDProof']);
    $idProofDir = $rdirs[count($rdirs) - 1];
}

$employee_complete_info = $this->get_user_complete_info($wphrm_employee_edit_id);

$wphrmEmployeeSalaryInfo = $this->WPHRMGetUserDatas($wphrm_employee_edit_id, 'wphrmEmployeeSalaryInfo');
$wphrmEmployeeBankInfo = $this->WPHRMGetUserDatas($wphrm_employee_edit_id, 'wphrmEmployeeBankInfo');
$wphrmEmployeeOtherInfo = $this->WPHRMGetUserDatas($wphrm_employee_edit_id, 'wphrmEmployeeOtherInfo');
$wphrmDefaultDocumentsLabel = $this->WPHRMGetDefaultDocumentsLabel();
$wphrmEmployeeFirstName = get_user_meta($wphrm_employee_edit_id, 'first_name', true);
$wphrmEmployeeLastName = get_user_meta($wphrm_employee_edit_id, 'last_name', true);
$wphrmHideShowEmployeeSectionSettings = $this->WPHRMGetSettings('WPHRMHideShowEmployeeSectionInfo');


$wphrmEmployeeFamilyInfo = $this->WPHRMGetUserDatas($wphrm_employee_edit_id, 'wphrmEmployeeFamilyInfo');
?>
<div class="preloader">
    <span class="preloader-custom-gif"></span>
</div>
<input type="hidden" class="documents-hide-id" value="<?php if (isset($wphrmHideShowEmployeeSectionSettings['documents-details'])){ echo $wphrmHideShowEmployeeSectionSettings['documents-details']; } ?>">
<input type="hidden" class="bank-account-hide-id" value="<?php if (isset($wphrmHideShowEmployeeSectionSettings['bank-account-details'])){ echo $wphrmHideShowEmployeeSectionSettings['bank-account-details']; } ?>">
<input type="hidden" class="other-details-id" value="<?php if (isset($wphrmHideShowEmployeeSectionSettings['other-details'])){ echo $wphrmHideShowEmployeeSectionSettings['other-details']; } ?>">
<input type="hidden" class="salary-details-id" value="<?php if (isset($wphrmHideShowEmployeeSectionSettings['salary-details'])){ echo $wphrmHideShowEmployeeSectionSettings['salary-details']; } ?>">
<div style="padding-left: 0px; padding-right:20px; padding-top:0px;" class="col-md-12">
    <!-- BEGIN PAGE HEADER-->
    <h3 class="page-title"><?php _e('View Employee Informations', 'wphrm'); ?></h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-12">
            <div class="page-bar">
                <ul class="page-breadcrumb">
                    <li> <i class="fa fa-home"></i> <?php _e('Home', 'wphrm'); ?> <i class="fa fa-angle-right"></i> </li>
                    <li> <?php _e('Employee', 'wphrm'); ?> </li>
                    <li> <i class="fa fa-angle-double-right"></i><strong><?php echo esc_html($wphrmEmployeeFirstName).' '.esc_html($wphrmEmployeeLastName); ?></strong></li>
                </ul>
            </div>
            <?php if (in_array('manageOptionsEmployee', $wphrmGetPagePermissions)) { ?>
            <a class="btn green " href="?page=wphrm-employees"><i class="fa fa-arrow-left"></i><?php _e('Back', 'wphrm'); ?> </a>
            <?php if (isset($_REQUEST['page']) && $_REQUEST['page'] != 'wphrm-employee-info') { ?>
            <a class="btn green " href="?page=wphrm-employee-info&employee_id=<?php  echo esc_html($wphrm_employee_edit_id); ?>"><i class="fa fa-edit"></i><?php _e('Edit', 'wphrm'); ?> </a>
            <?php } else { ?>
            <a class="btn green " href="?page=wphrm-employee-view-details&employee_id=<?php echo esc_html($wphrm_employee_edit_id); ?>"><i class="fa fa-eye"></i><?php _e('View', 'wphrm'); ?> </a>
            <?php } } ?>

            <?php if (!in_array('manageOptionsEmployee', $wphrmGetPagePermissions)) { ?>
                <a class="btn green btn-edit-emp-info" href="#"><i class="fa fa-edit"></i> <?php _e('Edit', 'wphrm'); ?></a>
                <a class="btn green btn-save-emp-info display-hide" href="?page=wphrm-employee-view-details" style="float:right"><i class="fa fa-save"></i><?php _e('Save', 'wphrm'); ?> </a>
            <?php } ?>

            <div class="row viewing-row ">

                <div class="col-md-6 col-sm-12">
                    <div class="portlet box blue">
                        <div class="portlet-title">
                            <div class="caption"> <i class="fa fa-edit"></i><?php _e('Personal Details ', 'wphrm'); ?></div>
                        </div>
                        <div class="portlet-body">
                            <div class="alert alert-success display-hide" id="personal_details_success">
                                <button class="close" data-close="alert"></button>
                            </div>
                            <div class="alert alert-danger display-hide" id="error">
                                <button class="close" data-close="alert"></button>
                            </div>
                            <form method="POST"  accept-charset="UTF-8" class="form-horizontal" id="wphrm_employee_basic_info_form" enctype="multipart/form-data">
                                <input type="hidden" name="wphrm_employee_id" id="wphrm_employee_id"  value="<?php
                                                                                                             if (isset($wphrm_employee_edit_id)) : echo esc_attr($wphrm_employee_edit_id);
                                                                                                             endif;
                                                                                                             ?> "/>
                                <div class="form-body">
                                    <div class="form-group ">
                                        <label class="control-label col-md-4"><?php _e('Photo', 'wphrm'); ?></label>
                                        <div class="col-md-8">
                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-new thumbnail" style="width: 200px; height: auto;">
                                                    <?php if (isset($wphrmEmployeeBasicInfo['employee_profile']) && $wphrmEmployeeBasicInfo['employee_profile'] != '') { ?>
                                                    <img src="<?php if (isset($wphrmEmployeeBasicInfo['employee_profile'])) : echo esc_attr($wphrmEmployeeBasicInfo['employee_profile']);
                                                    endif; ?>" width="200"><br>
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
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label"><?php _e('First Name', 'wphrm'); ?><span class="required"></span></label>
                                        <div class="col-md-8">
                                            <input disabled class="form-control"  name="wphrm_employee_fname" type="text" id="wphrm_employee_fname" value="<?php
                                                   if (isset($wphrmEmployeeFirstName ) && $wphrmEmployeeFirstName !='') : echo esc_attr($wphrmEmployeeFirstName);
                                                else : echo esc_html($userInformation->data->user_nicename);
                                                endif;
                                                   ?>" autocapitalize="none"  />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label"><?php _e('Middle Name', 'wphrm'); ?></label>
                                        <div class="col-md-8">
                                            <input disabled class="form-control" name="wphrm_employee_mname" type="text" id="wphrm_employee_mname" value="<?php
                                                   if (isset($wphrmEmployeeBasicInfo['wphrm_employee_mname'])) : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_mname']);
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
                                        <label class="col-md-4 control-label"><?php _e('Email', 'wphrm'); ?></label>
                                        <div class="col-md-8">
                                            <input disabled class="form-control" name="wphrm_employee_email" type="text" id="wphrm_employee_email" value="<?php
                                                  if (isset($wphrmEmployeeBasicInfo['wphrm_employee_email']) && $wphrmEmployeeBasicInfo['wphrm_employee_email'] !='') : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_email']);
                                                else : echo esc_html($userInformation->data->user_email);
                                                endif;
                                                   ?>" autocapitalize="none"  />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label"><?php _e('Employee Id', 'wphrm'); ?></label>
                                        <div class="col-md-8">
                                            <input disabled name="wphrm_employee_uniqueid" type="text" id="wphrm_employee_uniqueid" value="<?php
                                                                                                                                           if (isset($wphrmEmployeeBasicInfo['wphrm_employee_userid']) && $wphrmEmployeeBasicInfo['wphrm_employee_userid'] != '') : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_userid']);
                                                                                                        else : if (isset($userInformation->data->user_login)): echo esc_html($userInformation->data->user_login);
                                                                                                        endif;
                                                                                                        endif;
                                                                                                                                           ?>" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label"><?php _e('Gender', 'wphrm'); ?></label>
                                        <div class="col-md-8">
                                            <input disabled name="wphrm_employee_gender" type="text" id="wphrm_employee_gender" value="<?php
                                                                                                                                           if (isset($wphrmEmployeeBasicInfo['wphrm_employee_gender']) && $wphrmEmployeeBasicInfo['wphrm_employee_gender'] != '') : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_gender']);
                                                                                                        else : if (isset($userInformation->data->user_login)): echo esc_html($userInformation->data->user_login);
                                                                                                        endif;
                                                                                                        endif;
                                                                                                                                           ?>" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label"><?php _e('Department', 'wphrm'); ?></label>
                                        <div class="col-md-8">
                                            <input disabled name="wphrm_employee_department" type="text" id="wphrm_employee_department" value="<?php
                                                                                                                                               if (isset($wphrmEmployeeBasicInfo['wphrm_employee_department'])){
                                                                                                                                                   $department_info = $wpdb->get_var("SELECT departmentName FROM  {$this->WphrmDepartmentTable} WHERE departmentID = '".$wphrmEmployeeBasicInfo['wphrm_employee_department']."'; ");
                                                                                                                                                   $department_info = unserialize(base64_decode($department_info));

                                                                                                                                                   echo $department_info['departmentName'] ? $department_info['departmentName'] : '';
                                                                                                                                               }
                                                                                                                                               ?>" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label"><?php _e('Designation', 'wphrm'); ?></label>
                                        <div class="col-md-8">
                                            <input disabled name="wphrm_employee_department" type="text" id="wphrm_employee_department" value="<?php
                                                                                                                                               if (isset($wphrmEmployeeBasicInfo['wphrm_employee_designation'])){
                                                                                                                                                   $department_info = $this->get_designation_info($wphrmEmployeeBasicInfo['wphrm_employee_designation']);
                                                                                                                                                   echo $department_info['designationName'];
                                                                                                                                               }
                                                                                                                                               ?>" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label"><?php _e('Phone Number', 'wphrm'); ?></label>
                                        <div class="col-md-8">
                                            <input  class="form-control" disabled name="wphrm_employee_phone" type="text" id="wphrm_employee_phone" value="<?php
                                                    if (isset($wphrmEmployeeBasicInfo['wphrm_employee_phone'])) : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_phone']);
                                                    endif;
                                                    ?>" autocapitalize="none" autocorrect="off" maxlength="10" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label"><?php _e('Alternative Phone Number (Home)', 'wphrm'); ?></label>
                                        <div class="col-md-8">
                                            <input  class="form-control" disabled name="wphrm_employee_home_phone" type="text" id="wphrm_employee_home_phone" value="<?php
                                                    if (isset($wphrmEmployeeBasicInfo['wphrm_employee_home_phone'])) : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_home_phone']);
                                                    endif;
                                                    ?>" autocapitalize="none" autocorrect="off" maxlength="10" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4"><?php _e('Date of Birth', 'wphrm'); ?></label>
                                        <div class="col-md-8">
                                            <div class="input-group input-medium date"  data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                                <input disabled="" class="form-control date-pickers"   type="text"  value="<?php
                                                                                                                           if (isset($wphrmEmployeeBasicInfo['wphrm_employee_bod'])) : echo  esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_bod']);
                                                                                                                           endif;
                                                                                                                           ?>" autocapitalize="none"  />
                                                <span class="input-group-btn">
                                                    <button class="btn default-date" type="button"><i class="fa fa-calendar" style="line-height: 1.9;"></i></button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!--J@F-->
                                    <div class="form-group">
                                        <label class="control-label col-md-4"><?php _e('Employee Race', 'wphrm'); ?></label>
                                        <div class="col-md-8">
                                            <input disabled="" class="form-control"   type="text"  value="<?php
                                                                                                          if (isset($wphrmEmployeeBasicInfo['wphrm_employee_race'])) : echo  esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_race']);
                                                                                                          endif;
                                                                                                          ?>" autocapitalize="none"  />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4"><?php _e('Marital Status', 'wphrm'); ?></label>
                                        <div class="col-md-8">
                                            <input disabled="" class="form-control"   type="text"  value="<?php
                                                                                                          if (isset($wphrmEmployeeBasicInfo['wphrm_employee_mstatus'])) : echo  esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_mstatus']);
                                                                                                          endif;
                                                                                                          ?>" autocapitalize="none"  />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-4"><?php _e('Religion', 'wphrm'); ?></label>
                                        <div class="col-md-8">
                                            <input disabled="" class="form-control"   type="text"  value="<?php
                                                                                                          if (isset($wphrmEmployeeBasicInfo['wphrm_employee_religion'])) : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_religion']); endif; ?>" autocapitalize="none"  />
                                        </div>
                                    </div>
                                    <!--J@F END-->

                                    <div class="form-group">
                                        <label class="col-md-4 control-label"><?php _e('Local Address', 'wphrm'); ?></label>
                                        <div class="col-md-8">
                                            <textarea disabled rows="3" class="form-control"   name="wphrm_employee_local_address" type="text" id="wphrm_employee_local_address" value="" autocapitalize="none" autocorrect="off"><?php
                                                if (isset($wphrmEmployeeBasicInfo['wphrm_employee_local_address'])) : echo esc_textarea($wphrmEmployeeBasicInfo['wphrm_employee_local_address']);
                                                endif;
                                                ?></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label"><?php _e('Permanent Address', 'wphrm'); ?></label>
                                        <div class="col-md-8">
                                            <textarea disabled rows="3" class="form-control"  name="wphrm_employee_permanant_address" type="text" id="wphrm_employee_permanant_address" value="" autocapitalize="none" autocorrect="off" ><?php
                                                if (isset($wphrmEmployeeBasicInfo['wphrm_employee_permanant_address'])) : echo esc_textarea($wphrmEmployeeBasicInfo['wphrm_employee_permanant_address']);
                                                endif;
                                                ?></textarea>
                                        </div>
                                    </div>
                            
                                    <div class="form-group">
                                        <label class="control-label col-md-4"><?php _e('Highest Educational Level', 'wphrm'); ?></label>
                                        <div class="col-md-8">
                                            <input disabled="" class="form-control"  name="wphrm_employee_educational_level" type="text" id="wphrm_employee_educational_level" value="<?php
                                              if (isset($wphrmEmployeeBasicInfo['wphrm_employee_educational_level'])) : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_educational_level']);
                                              endif;
                                                        ?>" autocapitalize="none"  />
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="control-label col-md-4"><?php _e('Completion of National Service (ORD)', 'wphrm'); ?></label>
                                        <div class="col-md-8">
                                            <input disabled="" class="form-control"  name="wphrm_employee_ord" type="text" id="wphrm_employee_ord" value="<?php
                                              if (isset($wphrmEmployeeBasicInfo['wphrm_employee_ord'])) : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_ord']);
                                              endif;
                                                        ?>" autocapitalize="none"  />
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="control-label col-md-4"><?php _e('ORD Start Date', 'wphrm'); ?></label>
                                        <div class="col-md-8">
                                            <input disabled="" class="form-control"  name="wphrm_ord_start_date" type="text" id="wphrm_ord_start_date" value="<?php
                                                      if (isset($wphrmEmployeeBasicInfo['wphrm_ord_start_date'])) : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_ord_start_date']);
                                                  endif;
                                                                ?>" autocapitalize="none"  />
                                        </div>
                                    </div>

                                    <div class="form-group ord-holder">
                                        <label class="control-label col-md-4"><?php _e('ORD End Date', 'wphrm'); ?></label>
                                        <div class="col-md-8">
                                            <input disabled="" class="form-control"  name="wphrm_ord_end_date" type="text" id="wphrm_ord_end_date" value="<?php
                                                      if (isset($wphrmEmployeeBasicInfo['wphrm_ord_end_date'])) : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_ord_end_date']);
                                                  endif;
                                                                ?>" autocapitalize="none"  />
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="control-label col-md-4"><?php _e('Completion of Relief of Duty (ROD)', 'wphrm'); ?></label>
                                        <div class="col-md-8">
                                            <input disabled="" class="form-control"  name="wphrm_employee_rod" type="text" id="wphrm_employee_rod" value="<?php
                                              if (isset($wphrmEmployeeBasicInfo['wphrm_employee_rod'])) : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_rod']);
                                              endif;
                                                        ?>" autocapitalize="none"  />
                                        </div>
                                    </div>

                                    <div class="form-group rod-holder">
                                        <label class="control-label col-md-4"><?php _e('ROD End Date', 'wphrm'); ?></label>
                                        <div class="col-md-8">
                                            <input disabled="" class="form-control"  name="wphrm_rod_end_date" type="text" id="wphrm_rod_end_date" value="<?php
                                                      if (isset($wphrmEmployeeBasicInfo['wphrm_rod_end_date'])) : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_rod_end_date']);
                                                  endif;
                                                                ?>" autocapitalize="none"  />
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="portlet box blue documents-hide-div">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-file-image-o"></i><?php _e('Documents', 'wphrm'); ?>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="portlet-body">
                                <div class="alert alert-success display-hide" id="employee_document">
                                    <button class="close" data-close="alert"></button>
                                </div>
                                <div class="alert alert-danger display-hide" id="employee_document_error">
                                    <button class="close" data-close="alert"></button>
                                </div>
                                <form method="POST"  accept-charset="UTF-8" class="form-horizontal" id="wphrmEmployeeDocumentInfo_form" enctype="multipart/form-data"><input name="_method" type="hidden" value="PATCH"><input name="_token" type="hidden" value="CKw97QC4WEEKjxHdCpA3oZBiucWKYo0778rEpuPz">
                                    <input type="hidden" name="wphrm_employee_id" id="wphrm_employee_id"  value="<?php
                                                                                                                 if (isset($wphrm_employee_edit_id)) : echo esc_attr($wphrm_employee_edit_id);
                                                                                                                 endif;
                                                                                                                 ?> "/>
                                    <div class="form-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo esc_html($wphrmDefaultDocumentsLabel['resume']); ?></label>
                                            <div class="col-md-6">
                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <div class="input-group input-large" >
                                                        <div disabled  class="form-control uneditable-input" data-trigger="fileinput">
                                                            <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename">
                                                            <?php
                                                            if (isset($resumeDir) && $resumeDir !='') : $resumeExt = pathinfo($resumeDir, PATHINFO_EXTENSION);
                                                            echo esc_html(mb_strimwidth($resumeDir , 0, 10).'....'.$resumeExt);
                                                            endif;
                                                            ?>
                                                            </span>
                                                        </div>

                                                    </div>
                                                </div>

                                            </div>
                                            <div class="col-md-3">
                                                <?php  if (isset($wphrmEmployeeDocumentsInfo['resume']) && $wphrmEmployeeDocumentsInfo['resume'] != ''){ ?>
                                                <a class="btn blue" target="blank" href="<?php  if (isset($wphrmEmployeeDocumentsInfo['resume'])) : echo esc_html($wphrmEmployeeDocumentsInfo['resume']); endif; ?>"><i class="fa fa-eye"></i><?php _e('View', 'wphrm'); ?></a>
                                                <?php  }  ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo esc_html($wphrmDefaultDocumentsLabel['offerLetter']); ?></label>
                                            <div class="col-md-6">
                                                <div  class="fileinput fileinput-new" data-provides="fileinput">
                                                    <div  class="input-group input-large">
                                                        <div disabled class="form-control uneditable-input" data-trigger="fileinput">
                                                            <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename">
                                                            <?php
                                                            if (isset($offerDir) && $offerDir !='') :
                                                            $offerExt = pathinfo($offerDir, PATHINFO_EXTENSION);
                                                            echo esc_html(mb_strimwidth($offerDir , 0, 10).'....'.$offerExt);
                                                            endif;
                                                            ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <?php  if (isset($wphrmEmployeeDocumentsInfo['offerLetter']) && $wphrmEmployeeDocumentsInfo['offerLetter'] != ''){ ?>
                                                <a class="btn blue" target="blank" href="<?php  if (isset($wphrmEmployeeDocumentsInfo['offerLetter'])) : echo esc_html($wphrmEmployeeDocumentsInfo['offerLetter']); endif; ?>"><i class="fa fa-eye"></i><?php _e('View', 'wphrm'); ?></a>
                                                <?php  }  ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo esc_html($wphrmDefaultDocumentsLabel['joiningLetter']); ?></label>
                                            <div class="col-md-6">
                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <div class="input-group input-large">
                                                        <div disabled class="form-control uneditable-input" data-trigger="fileinput">
                                                            <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename">
                                                            <?php
                                                            if (isset($joiningDir) && $joiningDir !='') :
                                                            $joiningDirExt = pathinfo($joiningDir, PATHINFO_EXTENSION);
                                                            echo esc_html(mb_strimwidth($joiningDir , 0, 10).'....'.$joiningDirExt);
                                                            endif;
                                                            ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <?php  if (isset($wphrmEmployeeDocumentsInfo['joiningLetter']) && $wphrmEmployeeDocumentsInfo['joiningLetter'] != ''){ ?>
                                                <a class="btn blue" target="blank" href="<?php  if (isset($wphrmEmployeeDocumentsInfo['joiningLetter'])) : echo esc_html($wphrmEmployeeDocumentsInfo['joiningLetter']); endif; ?>"><i class="fa fa-eye"></i><?php _e('View', 'wphrm'); ?></a>
                                                <?php  }  ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo esc_html($wphrmDefaultDocumentsLabel['contractAndAgreement']); ?></label>
                                            <div class="col-md-6">
                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <div class="input-group input-large">
                                                        <div disabled class="form-control uneditable-input" data-trigger="fileinput">
                                                            <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename"><?php
                                                            if (isset($contractDir) && $contractDir !='') :
                                                            $contractDirExt = pathinfo($contractDir, PATHINFO_EXTENSION);
                                                            echo esc_html(mb_strimwidth($contractDir , 0, 10).'....'.$contractDirExt);
                                                            endif;
                                                            ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <?php  if (isset($wphrmEmployeeDocumentsInfo['contract']) && $wphrmEmployeeDocumentsInfo['contract'] != ''){ ?>
                                                <a class="btn blue" target="blank" href="<?php  if (isset($wphrmEmployeeDocumentsInfo['contract'])) : echo esc_html($wphrmEmployeeDocumentsInfo['contract']); endif; ?>"><i class="fa fa-eye"></i><?php _e('View', 'wphrm'); ?></a>
                                                <?php  }  ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo esc_html($wphrmDefaultDocumentsLabel['iDProof']); ?></label>
                                            <div class="col-md-6">
                                                <div disabled class="fileinput fileinput-new" data-provides="fileinput">
                                                    <div class="input-group input-large">
                                                        <div  disabled class="form-control uneditable-input" data-trigger="fileinput">
                                                            <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename"><?php
                                                            if (isset($idProofDir) && $idProofDir !='') :
                                                            $idProofDirExt = pathinfo($idProofDir, PATHINFO_EXTENSION);
                                                            echo esc_html(mb_strimwidth($idProofDir , 0, 10).'....'.$idProofDirExt);
                                                            endif;
                                                            ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <?php  if (isset($wphrmEmployeeDocumentsInfo['IDProof']) && $wphrmEmployeeDocumentsInfo['IDProof'] != ''){ ?>
                                                <a class="btn blue" target="blank" href="<?php  if (isset($wphrmEmployeeDocumentsInfo['IDProof'])) : echo esc_html($wphrmEmployeeDocumentsInfo['IDProof']); endif; ?>"><i class="fa fa-eye"></i><?php _e('View', 'wphrm'); ?></a>
                                                <?php } ?></div>
                                        </div>
                                        <?php

                                        if (isset($wphrmEmployeeDocumentsInfo['documentsfieldslebal']) && $wphrmEmployeeDocumentsInfo['documentsfieldslebal'] != '' && isset($wphrmEmployeeDocumentsInfo['documentsfieldsvalue']) && $wphrmEmployeeDocumentsInfo['documentsfieldsvalue'] != '')
                                        {
                                            foreach ($wphrmEmployeeDocumentsInfo['documentsfieldslebal'] as $lebalkey => $documentsfieldslebal) {
                                                foreach ($wphrmEmployeeDocumentsInfo['documentsfieldsvalue'] as $valuekey => $documentsfieldsvalue) {
                                                    if ($lebalkey == $valuekey) {
                                        ?>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php _e($documentsfieldslebal, 'wphrm'); ?></label>
                                            <div class="col-md-6">
                                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                                    <div class="input-group input-large">
                                                        <div disabled class="form-control uneditable-input" data-trigger="fileinput">
                                                            <i class="fa fa-file fileinput-exists"></i>&nbsp; <span class="fileinput-filename">
                                                            <?php
                                                        if (isset($documentsfieldsvalue) && $documentsfieldsvalue !='') :
                                                        $url =  $this->WPHRMGetAttechment($documentsfieldsvalue);
                                                        $title =  $this->WPHRMGetAttechmentTitle($documentsfieldsvalue);
                                                        $resumeExt = pathinfo($url, PATHINFO_EXTENSION);
                                                        echo esc_html(mb_strimwidth($title , 0, 20).'....'.$resumeExt);
                                                        endif;
                                                            ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <?php  if (isset($url) && $url!= ''){ ?>
                                                <a class="btn blue" target="blank" href="<?php  if (isset($url)) : echo esc_html($url); endif; ?>"><i class="fa fa-eye"></i><?php _e('View', 'wphrm'); ?></a>
                                                <?php } ?></div>
                                        </div> <?php
                                                    }
                                                }
                                            }

                                        } ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div></div>

                <div class="col-md-6 col-sm-6">
                    <div class="portlet box blue">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-edit"></i><?php _e('Work Info', 'wphrm'); ?>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="alert alert-danger display-hide" id="work_details_error">
                                <button class="close" data-close="alert"></button>
                            </div>
                            <form method="POST" accept-charset="UTF-8" class="form-horizontal" id="" enctype="multipart/form-data">

                                <input type="hidden" name="wphrm_employee_id" id="wphrm_employee_id"  value="<?php echo (isset($wphrm_employee_edit_id)) ? esc_attr($wphrm_employee_edit_id) : ''; ?> "/>
                                <input type="hidden" name="info_type" value="work_info" >
                                <div class="form-body">
                                    <?php include_once 'jaf_work_info_view.php'; ?>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
            <div class="row viewing-row">

                <div class="col-md-6 col-sm-6">
                    <div class="portlet box red-sunglo">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-users"></i><?php _e('Family Background', 'wphrm'); ?>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="alert alert-success display-hide" id="family_details_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrmMessagesFamily); ?>
                                <button class="close" data-close="alert"></button>
                            </div>
                            <div class="alert alert-danger display-hide" id="family_details_success_error">
                                <button class="close" data-close="alert"></button>
                            </div>
                            <form method="POST"  accept-charset="UTF-8" class="form-horizontal" id="" autocomplete="off" >
                                <input type="hidden" name="wphrm_employee_id" id="wphrm_employee_id"  value="<?php
                                                                                                             if (isset($wphrm_employee_edit_id)) : echo esc_attr($wphrm_employee_edit_id);
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
                                                        <th>Working in Related Industries</th>
                                                        <th>Company Name</th>
                                                        <th>Occupation</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $family_members = empty($wphrmEmployeeFamilyInfo['family-members']) ? array() : $wphrmEmployeeFamilyInfo['family-members']; ?>
                                                    <?php $count = empty($family_members['family-member-names']) ? 0 : count($family_members['family-member-names']); ?>
                                                    <?php $family_info_keys = array_keys($wphrmEmployeeFamilyInfo); ?>
                                                    <?php if($count): ?>
                                                    <?php for( $i = 0; $i < $count; $i++ ): ?>
                                                    <tr>
                                                        <td>
                                                            <input readonly class="form-control family-member-name" name="family-member-name[]" type="text" id="" value="<?php echo empty($family_members['family-member-names'][$i]) ? '' : $family_members['family-member-names'][$i]; ?>" autocapitalize="none"  />
                                                        </td>
                                                        <td>
                                                            <input readonly class="form-control family-member-relations" name="family-member-relations[]" type="text" id="" value="<?php echo empty($family_members['family-member-relations'][$i]) ? '' : $family_members['family-member-relations'][$i]; ?>" autocapitalize="none"  />
                                                        </td>
                                                        <td>
                                                            <input readonly class="form-control family-member-work_related" name="family-member-work_related[]" type="text" id="" value="<?php echo empty($family_members['family-member-work_related'][$i]) ? '' : $family_members['family-member-work_related'][$i]; ?>" autocapitalize="none"  />
                                                        </td>
                                                        <td>
                                                            <input readonly class="form-control family-member-company_name" name="family-member-company_name[]" type="text" id="" value="<?php echo empty($family_members['family-member-company_name'][$i]) ? '' : $family_members['family-member-company_name'][$i]; ?>" autocapitalize="none"  />
                                                        </td>
                                                        <td>
                                                            <input readonly class="form-control family-member-occupation" name="family-member-occupation[]" type="text" id="" value="<?php echo empty($family_members['family-member-occupation'][$i]) ? '' : $family_members['family-member-occupation'][$i]; ?>" autocapitalize="none"  />
                                                        </td>
                                                    </tr>
                                                    <?php endfor; ?>
                                                    <?php else: ?>
                                                    <tr>
                                                        <td colspan="6">No Data yet</td>
                                                    </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>


                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <h5>Only for employees with children’s</h5>
                                        </div>
                                        <div class="col-md-12">

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
                                                    <?php if($count): ?>
                                                    <?php for( $i = 0; $i < $count; $i++ ): ?>
                                                    <tr>
                                                        <td>
                                                            <input readonly class="form-control child-name" name="child-name[]" type="text" id="" value="<?php echo empty($childrens['child-names'][$i]) ? '' : $childrens['child-names'][$i]; ?>" autocapitalize="none"  />
                                                        </td>
                                                        <td>
                                                            <input readonly class="form-control child-nric" name="child-nric[]" type="text" id="" value="<?php echo empty($childrens['child-nric'][$i]) ? '' : $childrens['child-nric'][$i]; ?>" autocapitalize="none"  />
                                                        </td>
                                                        <td>
                                                            <input readonly class="form-control child-dob child-bod" name="child-dob[]" type="text" id="" value="<?php echo empty($childrens['child-dob'][$i]) ? '' : $childrens['child-dob'][$i]; ?>" autocapitalize="none"  />
                                                        </td>
                                                        <td>
                                                            <input readonly class="form-control child-age" name="child-age[]" type="text" id="" value="<?php echo empty($childrens['child-age'][$i]) ? '' : $childrens['child-age'][$i]; ?>" autocapitalize="none"  />
                                                        </td>
                                                        <td>
                                                            <input readonly class="form-control child-gender" name="child-gender[]" type="text" id="" value="<?php echo empty($childrens['child-gender'][$i]) ? '' : $childrens['child-gender'][$i]; ?>" autocapitalize="none"  />
                                                        </td>
                                                        <td>
                                                            <input readonly class="form-control child-nationality" name="child-nationality[]" type="text" id="" value="<?php echo empty($childrens['child-nationality'][$i]) ? '' : $childrens['child-nationality'][$i]; ?>" autocapitalize="none"  />
                                                        </td>
                                                    </tr>
                                                    <?php endfor; ?>
                                                    <?php else: ?>
                                                    <tr>
                                                        <td colspan="6">No Data yet</td>
                                                    </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>


                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <h5>Contact Person in case of emergency</h5>
                                        </div>
                                        <div class="col-md-12">

                                            <table class="table-condensed table table-hover table-striped wphrmEmployeeFamilyInfo-table-children">
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
                                                    <?php if( !empty($wphrmEmployeeFamilyInfo['employee-emergency-contact']) ) : ?>
                                                    <tr>
                                                        <td>
                                                            <input readonly class="form-control emergency-name" name="emergency-name[]" type="text" id="" value="<?php echo empty($emergencies['emergency-name'][0]) ? '' : $emergencies['emergency-name'][0]; ?>" autocapitalize="none"  />
                                                        </td>
                                                        <td>
                                                            <input readonly class="form-control emergency-re" name="emergency-relations[]" type="text" id="" value="<?php echo empty($emergencies['emergency-relations'][0]) ? '' : $emergencies['emergency-relations'][0]; ?>" autocapitalize="none"  />
                                                        </td>
                                                        <td>
                                                            <input readonly class="form-control emergency-add" name="emergency-address[]" type="text" id="" value="<?php echo empty($emergencies['emergency-address'][0]) ? '' : $emergencies['emergency-address'][0]; ?>" autocapitalize="none"  />
                                                        </td>
                                                        <td>
                                                            <input readonly class="form-control emergency-number" name="emergency-number[]" type="text" id="" value="<?php echo empty($emergencies['emergency-number'][0]) ? '' : $emergencies['emergency-number'][0]; ?>" autocapitalize="none"  />
                                                        </td>
                                                        <td>
                                                            <input readonly class="form-control emergency-alt-number" name="emergency-alt-number[]" type="text" id="" value="<?php echo empty($emergencies['emergency-alt-number'][0]) ? '' : $emergencies['emergency-alt-number'][0]; ?>" autocapitalize="none"  />
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <input readonly class="form-control emergency-name-2" name="emergency-name-2[]" type="text" id="" value="<?php echo empty($emergencies['emergency-name-2'][0]) ? '' : $emergencies['emergency-name-2'][0]; ?>" autocapitalize="none"  />
                                                        </td>
                                                        <td>
                                                            <input readonly class="form-control emergency-re-2" name="emergency-relations-2[]" type="text" id="" value="<?php echo empty($emergencies['emergency-relations-2'][0]) ? '' : $emergencies['emergency-relations-2'][0]; ?>" autocapitalize="none"  />
                                                        </td>
                                                        <td>
                                                            <input readonly class="form-control emergency-add-2" name="emergency-address-2[]" type="text" id="" value="<?php echo empty($emergencies['emergency-address-2'][0]) ? '' : $emergencies['emergency-address-2'][0]; ?>" autocapitalize="none"  />
                                                        </td>
                                                        <td>
                                                            <input readonly class="form-control emergency-number-2" name="emergency-number-2[]" type="text" id="" value="<?php echo empty($emergencies['emergency-number-2'][0]) ? '' : $emergencies['emergency-number-2'][0]; ?>" autocapitalize="none"  />
                                                        </td>
                                                        <td>
                                                            <input readonly class="form-control emergency-alt-number-2" name="emergency-alt-number-2[]" type="text" id="" value="<?php echo empty($emergencies['emergency-alt-number-2'][0]) ? '' : $emergencies['emergency-alt-number-2'][0]; ?>" autocapitalize="none"  />
                                                        </td>
                                                    </tr>
                                                    <?php else: ?>
                                                    <tr>
                                                        <td colspan="6">No Data yet</td>
                                                    </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>


                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-sm-12">

                    <!--<div class="portlet box red-sunglo  bank-account-hide-div">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-bank"></i><?php _e('Bank Account Details', 'wphrm'); ?>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="alert alert-success display-hide" id="wphrm_bank_details">
                                <button class="close" data-close="alert"></button>
                            </div>
                            <div class="alert alert-danger display-hide" id="wphrm_bank_details_error">
                                <button class="close" data-close="alert"></button>
                            </div>
                            <form method="POST"  accept-charset="UTF-8" class="form-horizontal" id="wphrmEmployeeBankInfo_form">
                                <input type="hidden" name="wphrm_employee_id" id="wphrm_employee_id"  value="<?php
                                                                                                             if (isset($wphrm_employee_edit_id)) : echo esc_attr($wphrm_employee_edit_id);
                                                                                                             endif;
                                                                                                             ?> "/>
                                <div id="alert_bank"></div>
                                <div class="form-body">
                                    <div class="form-group">
                                        <label class="col-md-4 control-label"><?php _e('Account Holder Name', 'wphrm'); ?></label>
                                        <div class="col-md-8">
                                            <input disabled class="form-control"  name="wphrm_employee_bank_account_name" type="text" id="wphrm_employee_bank_account_name" value="<?php
                                                   if (isset($wphrmEmployeeBankInfo['wphrm_employee_bank_account_name'])) : echo esc_attr($wphrmEmployeeBankInfo['wphrm_employee_bank_account_name']);
                                                   endif; ?>" autocapitalize="none"  />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label"><?php _e('Account Number', 'wphrm'); ?></label>
                                        <div class="col-md-8">
                                            <input disabled class="form-control"  name="wphrm_employee_bank_account_no" type="text" id="wphrm_employee_bank_account_no" value="<?php
                                                   if (isset($wphrmEmployeeBankInfo['wphrm_employee_bank_account_no'])) : echo esc_attr($wphrmEmployeeBankInfo['wphrm_employee_bank_account_no']);
                                                   endif; ?>" autocapitalize="none"  />
                                        </div>
                                    </div>
                                    <?php
                                    if (isset($wphrmEmployeeBankInfo['wphrmbankfieldslebal']) && $wphrmEmployeeBankInfo['wphrmbankfieldslebal'] != '' && isset($wphrmEmployeeBankInfo['wphrmbankfieldsvalue']) && $wphrmEmployeeBankInfo['wphrmbankfieldsvalue'] != '') {
                                        foreach ($wphrmEmployeeBankInfo['wphrmbankfieldslebal'] as $lebalkey => $wphrmEmployeeSettingsBank) {
                                            foreach ($wphrmEmployeeBankInfo['wphrmbankfieldsvalue'] as $valuekey => $wphrmEmployeeSettingsvalue) {
                                                if ($lebalkey == $valuekey) { ?>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label"><?php _e($wphrmEmployeeSettingsBank, 'wphrm'); ?></label>
                                        <input disabled name="bank-fields-lebal[]" type="hidden" id="bank-fields-lebal" value="<?php
                                                                             if (isset($wphrmEmployeeSettingsBank)) : echo esc_attr($wphrmEmployeeSettingsBank); endif; ?>"/>
                                        <div class="col-md-8">
                                            <input disabled class="form-control" name="bank-fields-value[]" type="text" id="bank-fields-lebal" value="<?php
                                                                             if (isset($wphrmEmployeeSettingsvalue)) : echo esc_attr($wphrmEmployeeSettingsvalue); endif; ?>" autocapitalize="none"  />
                                        </div>
                                    </div>
                                    <?php }
                                            }
                                        }
                                        $wphrmBankFieldsInfo = $this->WPHRMGetSettings('Bankfieldskey');
                                        if (!empty($wphrmBankFieldsInfo)) {
                                            foreach ($wphrmBankFieldsInfo['Bankfieldslebal'] as $wphrmBankFieldsSettings) {
                                                if (!in_array($wphrmBankFieldsSettings, $wphrmEmployeeBankInfo['wphrmbankfieldslebal'])) { ?>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label"><?php _e($wphrmBankFieldsSettings, 'wphrm'); ?></label>
                                        <input disabled name="bank-fields-lebal[]" type="hidden" id="bank-fields-lebal" value="<?php
                                                                                                                                          if (isset($wphrmBankFieldsSettings)) : echo esc_attr($wphrmBankFieldsSettings); endif; ?>"/>
                                        <div disabled class="col-md-8">
                                            <input disabled class="form-control" name="bank-fields-value[]" type="text" id="bank-fields-lebal" value="" autocapitalize="none"  />
                                        </div>
                                    </div>
                                    <?php }
                                            }
                                        }
                                    }else {
                                        $wphrmBankfieldskeyInfo = $this->WPHRMGetSettings('Bankfieldskey');
                                        if (!empty($wphrmBankfieldskeyInfo)) {
                                            foreach ($wphrmBankfieldskeyInfo['Bankfieldslebal'] as $wphrmBanksettingInfo) { ?>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label"><?php _e($wphrmBanksettingInfo, 'wphrm'); ?></label>
                                        <input disabled name="bank-fields-lebal[]" type="hidden" id="bank-fields-lebal" value="<?php
                                                                                                                           if (isset($wphrmBanksettingInfo)) : echo esc_attr($wphrmBanksettingInfo); endif; ?>"/>
                                        <div class="col-md-8">
                                            <input disabled class="form-control" name="bank-fields-value[]" type="text" id="bank-fields-value" value="" autocapitalize="none"  />
                                        </div>
                                    </div>
                                    <?php }
                                        }
                                    } ?>
                                </div>
                            </form>
                        </div>
                    </div>-->
                    <div class="portlet box red-sunglo other-details-div">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-comment-o"></i><?php _e('Other Details', 'wphrm'); ?>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="alert alert-success display-hide" id="other_details_success">
                                <button class="close" data-close="alert"></button>
                            </div>
                            <div class="alert alert-danger display-hide" id="other_details_success_error">
                                <button class="close" data-close="alert"></button>
                            </div>
                            <form method="POST"  accept-charset="UTF-8" class="form-horizontal" id="wphrmEmployeeOtherInfo_form">
                                <input type="hidden" name="wphrm_employee_id" id="wphrm_employee_id"  value="<?php
                                                                                                             if (isset($wphrm_employee_edit_id)) : echo esc_attr($wphrm_employee_edit_id);
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
                                    ?>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label"><?php _e($wphrmEmployeeSettingsOther, 'wphrm'); ?></label>
                                        <input disabled name="other-fields-lebal[]" type="hidden" id="other-fields-lebal" value="<?php
                                                        if (isset($wphrmEmployeeSettingsOther)) : echo esc_attr($wphrmEmployeeSettingsOther);
                                                        endif;
                                                                                                                                 ?>"/>
                                        <div class="col-md-8">
                                            <input disabled class="form-control" name="other-fields-value[]" type="text" id="other-fields-lebal" value="<?php
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
                                        <input disabled name="other-fields-lebal[]" type="hidden" id="other-fields-lebal" value="<?php
                                                        if (isset($wphrmOtherFieldsSettings)) : echo esc_attr($wphrmOtherFieldsSettings);
                                                        endif;
                                                                                                                                 ?>"/>
                                        <div class="col-md-8">
                                            <input disabled class="form-control" name="other-fields-value[]" type="text" id="other-fields-lebal" value="" autocapitalize="none"  />
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
                                        <input disabled name="other-fields-lebal[]" type="hidden" id="other-fields-lebal" value="<?php
                                                    if (isset($wphrmOthersettingInfo)) : echo esc_attr($wphrmOthersettingInfo);
                                                    endif;
                                                                                                                                 ?>"/>
                                        <div class="col-md-8">
                                            <input disabled class="form-control" name="other-fields-value[]" type="text" id="other-fields-value" value="" autocapitalize="none"  />
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
                                            <input disabled class="form-control"  type="text" id="other-fields-lebal" value="<?php
                                                        if (isset($wphrmOtherSettingsvalue)) : echo esc_attr($wphrmOtherSettingsvalue);
                                                        endif; ?>" autocapitalize="none"  />
                                        </div>
                                    </div>
                                    <?php
                                                    }
                                                }
                                            }
                                            $wphrmOtherFieldsInfo = $this->WPHRMGetSettings('Otherfieldskey');
                                            if (!empty($wphrmOtherFieldsInfo)) {
                                                foreach ($wphrmOtherFieldsInfo['Otherfieldslebal'] as $wphrmOtherFieldsSettings) {
                                                    if (!in_array($wphrmOtherFieldsSettings, $wphrmEmployeeOtherInfo['wphrmotherfieldslebal'])) { ?>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label"><?php _e($wphrmOtherFieldsSettings, 'wphrm'); ?></label>
                                        <div class="col-md-8">
                                            <input disabled class="form-control"  type="text" id="other-fields-lebal" value="" autocapitalize="none"  />
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
                                            <input disabled class="form-control"  type="text" id="other-fields-value" value="" autocapitalize="none"  />
                                        </div>
                                    </div>
                                    <?php
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                    <?php if (isset($wphrmEmployeeOtherInfo['wphrm_employee_vehicle']) && $wphrmEmployeeOtherInfo['wphrm_employee_vehicle'] != '') : ?>
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="wphrm_vehicle_type" class="col-md-4 control-label"><?php _e('Vehicle Type', 'wphrm'); ?>:</label>
                                                <div class="col-md-8">
                                                    <input disabled class="form-control" type="text" name="wphrm_vehicle_type" id="wphrm_vehicle_type" value="<?php
                                                    if (isset($wphrmEmployeeOtherInfo['wphrm_vehicle_type'])) : echo esc_attr($wphrmEmployeeOtherInfo['wphrm_vehicle_type']); endif; ?>"/>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="wphrm_employee_vehicle_model" class="col-md-4 control-label"><?php _e('Make-Model', 'wphrm'); ?>:</label>
                                                <div class="col-md-8">
                                                    <input disabled class="form-control" type="text" name="wphrm_employee_vehicle_model" id="wphrm_employee_vehicle_model" value="<?php
                                                    if (isset($wphrmEmployeeOtherInfo['wphrm_employee_vehicle_model'])) : echo esc_attr($wphrmEmployeeOtherInfo['wphrm_employee_vehicle_model']); endif; ?>"/>
                                                </div>
                                            </div>

                                            <div class="form-group" style="margin-bottom:0px;">
                                                <label for="wphrm_employee_vehicle_registrationno" class="col-md-4 control-label"><?php _e('Registration No.', 'wphrm'); ?>:</label>
                                                <div class="col-md-8">
                                                    <input disabled class="form-control" type="text" name="wphrm_employee_vehicle_registrationno" id="wphrm_employee_vehicle_registrationno" value="<?php
                                                    if (isset($wphrmEmployeeOtherInfo['wphrm_employee_vehicle_registrationno'])) : echo esc_attr($wphrmEmployeeOtherInfo['wphrm_employee_vehicle_registrationno']); endif; ?>"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label"><?php _e('T-Shirt Size', 'wphrm'); ?></label>
                                        <div class="col-md-8">
                                            <?php $wphrmTShirtSizes = array('xxxs'=>'XXXS', 'xxs'=>'XXS', 'xs'=>'XS', 's'=>'S', 'm'=>'M', 'l'=>'L', 'xl'=>'XL', 'xxl'=>'XXL', 'xxxl'=>'XXXL'); ?>
                                            <input disabled class="form-control" type="text" name="wphrm_employee_vehicle_model" id="wphrm_employee_vehicle_model" value="<?php
                                           if (isset($wphrmEmployeeOtherInfo['wphrm_t_shirt_size']) && $wphrmEmployeeOtherInfo['wphrm_t_shirt_size']!='') : echo esc_attr($wphrmTShirtSizes[$wphrmEmployeeOtherInfo['wphrm_t_shirt_size']]);
                                           endif; ?>"/>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-md-4" >Passport Number</label>
                                        <div class="col-md-8" >
                                            <input class="form-control" name="wphrm_employee_passport" id="wphrm_employee_passport" value="<?php echo isset($employee_complete_info->work_permit_info->wphrm_employee_passport) ? $employee_complete_info->work_permit_info->wphrm_employee_passport : ''; ?>" autocapitalize="none" readonly type="text">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-4 control-label" scope="row"><?php _e('Passport Country', 'wphrm'); ?>:</label>
                                        <div class="col-md-8">
                                            <?php $selected_passport_country = isset($employee_complete_info->work_permit_info->wphrm_employee_passport_country) ? $employee_complete_info->work_permit_info->wphrm_employee_passport_country : ''; ?>
                                            <?php foreach($employee_country_array as $key => $country): ?>
                                                <?php if( $country->code == $selected_passport_country ) : ?>
                                                    <input class="form-control" value="<?php echo $country->name; ?>" autocapitalize="none" readonly type="text">
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="control-label col-md-4" >Passport Date of Issue</label>
                                        <div class="col-md-8" >
                                            <input class="form-control" value="<?php echo isset($employee_complete_info->work_permit_info->wphrm_employee_passport_issued) ? $employee_complete_info->work_permit_info->wphrm_employee_passport_issued : ''; ?>" autocapitalize="none" readonly type="text">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="control-label col-md-4" >Passport Expiration Date</label>
                                        <div class="col-md-8" >
                                            <input class="form-control" value="<?php echo isset($employee_complete_info->work_permit_info->wphrm_employee_passport_expiration) ? $employee_complete_info->work_permit_info->wphrm_employee_passport_expiration : ''; ?>" autocapitalize="none" readonly type="text">
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>

                    <!--<div class="portlet box red-sunglo  salary-details-div">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-money"></i><?php _e('Salary Details', 'wphrm'); ?>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="alert alert-success display-hide" id="salary_details_success">
                                <button class="close" data-close="alert"></button>
                            </div>
                            <div class="alert alert-danger display-hide" id="salary_details_success_error">
                                <button class="close" data-close="alert"></button>
                            </div>
                            <form method="POST"  accept-charset="UTF-8" class="form-horizontal" id="wphrmEmployeeSalaryInfo_form">

                                <div id="alert_bank"></div>
                                <div class="form-body">
                                    <?php
                                    if (isset($wphrmEmployeeSalaryInfo['SalaryFieldsLebal']) && $wphrmEmployeeSalaryInfo['SalaryFieldsLebal'] != '' && isset($wphrmEmployeeSalaryInfo['SalaryFieldsvalue']) && $wphrmEmployeeSalaryInfo['SalaryFieldsvalue'] != '') {
                                        foreach ($wphrmEmployeeSalaryInfo['SalaryFieldsLebal'] as $lebalkey => $wphrmEmployeeSettingsSalary) {
                                            foreach ($wphrmEmployeeSalaryInfo['SalaryFieldsvalue'] as $valuekey => $wphrmSalarySettingsvalue) {
                                                if ($lebalkey == $valuekey) {
                                    ?>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label"><?php _e($wphrmEmployeeSettingsSalary, 'wphrm'); ?></label>
                                        <input disabled name="salary-fields-lebal[]" type="hidden" id="salary-fields-lebal" value="<?php
                                                    if (isset($wphrmEmployeeSettingsSalary)) : echo esc_attr($wphrmEmployeeSettingsSalary); endif; ?>"/>
                                        <div class="col-md-8">
                                            <input disabled class="form-control" name="salary-fields-value[]" type="text" id="salary-fields-lebal" value="<?php
                                                    if (isset($wphrmSalarySettingsvalue)) : echo esc_attr($wphrmSalarySettingsvalue); endif; ?>" autocapitalize="none"  />
                                        </div>
                                    </div>
                                    <?php
                                                }
                                            }
                                        }
                                        $wphrmSalaryFieldsInfo = $this->WPHRMGetSettings('salarydetailfieldskey');
                                        if (!empty($wphrmSalaryFieldsInfo)) {
                                            foreach ($wphrmSalaryFieldsInfo['salarydetailfieldlabel'] as $wphrmSalaryFieldsSettings) {
                                                if (!in_array($wphrmSalaryFieldsSettings, $wphrmEmployeeSalaryInfo['SalaryFieldsLebal'])) {
                                    ?>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label"><?php _e($wphrmSalaryFieldsSettings, 'wphrm'); ?></label>
                                        <input disabled name="salary-fields-lebal[]" type="hidden" id="salary-fields-lebal" value="<?php
                                                    if (isset($wphrmSalaryFieldsSettings)) : echo esc_attr($wphrmSalaryFieldsSettings); endif; ?>"/>
                                        <div class="col-md-8">
                                            <input disabled class="form-control" name="salary-fields-value[]" type="text" id="salary-fields-lebal" value="" autocapitalize="none"  />
                                        </div>
                                    </div>
                                    <?php
                                                }
                                            }
                                        }
                                    } else {
                                        $wphrmSalaryfieldskeyInfo = $this->WPHRMGetSettings('salarydetailfieldskey');
                                        if (!empty($wphrmSalaryfieldskeyInfo)) {
                                            foreach ($wphrmSalaryfieldskeyInfo['salarydetailfieldlabel'] as $wphrmSalarysettingInfo) {
                                    ?>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label"><?php _e($wphrmSalarysettingInfo, 'wphrm'); ?></label>
                                        <input disabled name="salary-fields-lebal[]" type="hidden" id="salary-fields-lebal" value="<?php
                                                if (isset($wphrmSalarysettingInfo)) : echo esc_attr($wphrmSalarysettingInfo); endif; ?>"/>
                                        <div class="col-md-8">
                                            <input disabled class="form-control" name="salary-fields-value[]" type="text" id="salary-fields-value" value="" autocapitalize="none"  />
                                        </div>
                                    </div>
                                    <?php
                                            }
                                        }
                                    } ?>
                                </div>
                            </form>
                        </div>
                    </div>-->
                </div>
                
            </div>
                
        </div>
    </div>
</div>







<!-- for editing -->
<?php if (!in_array('manageOptionsEmployee', $wphrmGetPagePermissions)) { ?>
<div class="row clearfix editing-row display-hide">
    <div class="col-md-6 col-sm-6">
        <div class="portlet box red-sunglo other-details-div">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-comment-o"></i><?php _e('Other Details', 'wphrm'); ?>
                </div>
                <?php if (isset($wphrm_employee_edit_id) && $wphrm_employee_edit_id != '') { ?>
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
                <form method="POST"  accept-charset="UTF-8" class="form-horizontal wphrmEmployeeOtherInfo_form">
                    <input type="hidden" name="wphrm_employee_id" id="wphrm_employee_id"  value="<?php
                                                                                                 if (isset($wphrm_employee_edit_id)) : echo esc_attr($wphrm_employee_edit_id);
                                                                                                 endif;
                                                                                                 ?> "/>
                    <div id="alert_bank"></div>
                    <div class="form-body">
                        <?php
                            $wphrmEmployeeOtherInfo = $this->WPHRMGetUserDatas($wphrm_employee_edit_id, 'wphrmEmployeeOtherInfo');
                            if (isset($wphrmEmployeeOtherInfo['wphrmotherfieldslebal']) && $wphrmEmployeeOtherInfo['wphrmotherfieldslebal'] != '' && isset($wphrmEmployeeOtherInfo['wphrmotherfieldsvalue']) && $wphrmEmployeeOtherInfo['wphrmotherfieldsvalue'] != '') {
                                foreach ($wphrmEmployeeOtherInfo['wphrmotherfieldslebal'] as $lebalkey => $wphrmEmployeeSettingsOther) {
                                    foreach ($wphrmEmployeeOtherInfo['wphrmotherfieldsvalue'] as $valuekey => $wphrmOtherSettingsvalue) {
                                        if ($lebalkey == $valuekey) {

                        ?>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label"><?php _e($wphrmEmployeeSettingsOther, 'wphrm'); ?></label>
                                                <input name="other-fields-lebal[]" type="hidden" id="other-fields-lebal" value="<?php if (isset($wphrmEmployeeSettingsOther)) : echo esc_attr($wphrmEmployeeSettingsOther); endif; ?>"/>
                                                <div class="col-md-8">
                                                    <input class="form-control" name="other-fields-value[]" type="text" id="other-fields-lebal" value="<?php if (isset($wphrmOtherSettingsvalue)) : echo esc_attr($wphrmOtherSettingsvalue); endif; ?>" autocapitalize="none"  />
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
                                        <input class="form-control" type="text" name="wphrm_employee_vehicle_expiration" id="wphrm_employee_vehicle_expiration" value="<?php
                                            if (isset($wphrmEmployeeOtherInfo['wphrm_employee_vehicle_expiration'])) : echo esc_attr($wphrmEmployeeOtherInfo['wphrm_employee_vehicle_expiration']);
                                            endif;
                                            ?>"/>
                                    </div>
                                </div>

                                <div class="form-group" style="margin-bottom:0px;">
                                    <label class="col-md-4 control-label" scope="row"><?php _e('Registration No.', 'wphrm'); ?>:</label>
                                    <div class="col-md-8">
                                        <input class="form-control" type="text" name="wphrm_employee_vehicle_registrationno" id="wphrm_employee_vehicle_registrationno" value="<?php
                                            if (isset($wphrmEmployeeOtherInfo['wphrm_employee_vehicle_registrationno'])) : echo esc_attr($wphrmEmployeeOtherInfo['wphrm_employee_vehicle_registrationno']);
                                            endif;
                                            ?>"/>
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

        <div class="portlet box red-sunglo">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-edit"></i><?php _e('Passport Information', 'wphrm'); ?>
                </div>
            </div>
            <div class="portlet-body">
                <div class="alert alert-success display-hide" id="work_permit_details_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html('Work Details have been successfully updated.'); ?>
                    <button class="close" data-close="alert"></button>
                </div>
                <div class="alert alert-danger display-hide" id="work_details_error">
                    <button class="close" data-close="alert"></button>
                </div>
                <form method="POST" accept-charset="UTF-8" class="form-horizontal wphrm_employee_work_permit_form" enctype="multipart/form-data">

                    <input type="hidden" name="wphrm_employee_id" id="wphrm_employee_id"  value="<?php echo (isset($wphrm_employee_edit_id)) ? esc_attr($wphrm_employee_edit_id) : ''; ?> "/>

                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-4" >Passport Number</label>
                            <div class="col-md-8" >
                                <input class="form-control" name="wphrm_employee_passport" id="wphrm_employee_passport" value="<?php echo isset($employee_complete_info->work_permit_info->wphrm_employee_passport) ? $employee_complete_info->work_permit_info->wphrm_employee_passport : ''; ?>" autocapitalize="none" type="text">
                            </div>
                        </div>

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

                        <div class="form-group">
                            <label class="control-label col-md-4" >Passport Date of Issue</label>
                            <div class="col-md-8" >
                                <div class="input-group input-medium date date-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                    <input class="form-control" name="wphrm_employee_passport_issued" id="wphrm_employee_passport_issued" value="<?php echo isset($employee_complete_info->work_permit_info->wphrm_employee_passport_issued) ? $employee_complete_info->work_permit_info->wphrm_employee_passport_issued : ''; ?>" autocapitalize="none" type="text">
                                    <span class="input-group-btn">
                                        <button class="btn default-date" type="button"><i class="fa fa-calendar" style="line-height: 1.9;"></i></button>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-4" >Passport Expiration Date</label>
                            <div class="col-md-8" >
                                <div class="input-group input-medium date after-current-date" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                    <input class="form-control" name="wphrm_employee_passport_expiration" id="wphrm_employee_passport_expiration" value="<?php echo isset($employee_complete_info->work_permit_info->wphrm_employee_passport_expiration) ? $employee_complete_info->work_permit_info->wphrm_employee_passport_expiration : ''; ?>" autocapitalize="none" type="text">
                                    <span class="input-group-btn">
                                        <button class="btn default-date" type="button"><i class="fa fa-calendar" style="line-height: 1.9;"></i></button>
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>

                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-sm-6">
        <div class="portlet box red-sunglo">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-users"></i><?php _e('Family Background', 'wphrm'); ?>
                </div>
                <?php if (isset($wphrm_employee_edit_id) && $wphrm_employee_edit_id != '') { ?>
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
                <form method="POST"  accept-charset="UTF-8" class="form-horizontal wphrmEmployeeFamilyInfo_form" autocomplete="off" >
                    <input type="hidden" name="wphrm_employee_id" id="wphrm_employee_id"  value="<?php
                                                                                                 if (isset($wphrm_employee_edit_id)) : echo esc_attr($wphrm_employee_edit_id);
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
                                            <td width="25%">
                                                <!-- <input class="form-control child-dob" name="child-dob[]" type="text" id="" value="" autocapitalize="none"  /> -->
                                                <div class="input-group date date-picker bdate-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                                    <input class="form-control" name="child-dob[]" autocapitalize="none" type="text">
                                                    <span class="input-group-btn">
                                                        <button class="btn default-date" type="button"><i class="fa fa-calendar" style="line-height: 1.9;"></i></button>
                                                    </span>
                                                </div>
                                            </td>
                                            <td width="7%">
                                                <input class="form-control child-age" name="child-age[]" type="text" id="" value="" autocapitalize="none" readonly  />
                                            </td>
                                            <td>
                                                <!-- <input class="form-control child-gender" name="family-member-gender[]" type="text" id="" value="" autocapitalize="none"  /> -->
                                                <select class="form-control child-gender" name="family-member-gender[]">
                                                    <option value="">--Gender--</option>
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                </select>
                                            </td>
                                            <td>
                                                <!-- <input class="form-control child-nationality" name="family-member-nationality[]" type="text" id="" value="" autocapitalize="none"  /> -->
                                                <!-- <select class="form-control child-nationality" name="family-member-nationality[]" type="text" id="">
                                                    <option value="">--Nationality--</option>
                                                    <option value="Chinese">Chinese</option>
                                                    <option value="Malay">Malay</option>
                                                    <option value="Indian">Indian</option>
                                                    <option value="Others">Others</option>
                                                </select> -->
                                                <select class="form-control child-nationality" name="family-member-nationality[]" type="text" id="">
                                                    <option value="" >--Select--</option>
                                                    <?php foreach($employee_country_array as $key => $country): ?>
                                                    <option value="<?php echo $country->name; ?>"><?php _e($country->name, 'wphrm'); ?></option>
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
                                            <td width="25%">
                                                <!-- <input class="form-control child-dob child-bod" name="child-dob[]" type="text" id="" value="<?php //echo empty($childrens['child-dob'][$i]) ? '' : $childrens['child-dob'][$i]; ?>" autocapitalize="none"  /> -->
                                                <div class="input-group date date-picker bdate-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                                    <input class="form-control" name="child-dob[]" value="<?php echo empty($childrens['child-dob'][$i]) ? '' : $childrens['child-dob'][$i]; ?>" autocapitalize="none" type="text">
                                                    <span class="input-group-btn">
                                                        <button class="btn default-date" type="button"><i class="fa fa-calendar" style="line-height: 1.9;"></i></button>
                                                    </span>
                                                </div>
                                            </td>
                                            <td width="7%">
                                                <input class="form-control child-age" name="child-age[]" type="text" id="" value="<?php echo empty($childrens['child-age'][$i]) ? '' : $childrens['child-age'][$i]; ?>" autocapitalize="none" readonly  />
                                            </td>
                                            <td width="15%">
                                                <!-- <input class="form-control child-gender" name="child-gender[]" type="text" id="" value="<?php //echo empty($childrens['child-gender'][$i]) ? '' : $childrens['child-gender'][$i]; ?>" autocapitalize="none"  /> -->
                                                <select class="form-control child-gender" name="child-gender[]">
                                                    <option value="">--Gender--</option>
                                                    <option value="Male"  <?php selected($childrens['child-gender'][$i], "Male" ); ?> >Male</option>
                                                    <option value="Female"  <?php selected($childrens['child-gender'][$i], "Female" ); ?> >Female</option>
                                                </select>
                                            </td>
                                            <td width="15%">
                                                <!-- <input class="form-control child-nationality" name="child-nationality[]" type="text" id="" value="<?php //echo empty($childrens['child-nationality'][$i]) ? '' : $childrens['child-nationality'][$i]; ?>" autocapitalize="none"  /> -->
                                                <select class="form-control child-nationality" name="child-nationality[]">
                                                    <!-- <option value="">--Nationality--</option>
                                                    <option value="Chinese" <?php selected($childrens['child-nationality'][$i], "Chinese" ); ?> >Chinese</option>
                                                    <option value="Malay" <?php selected($childrens['child-nationality'][$i], "Malay" ); ?> >Malay</option>
                                                    <option value="Indian" <?php selected($childrens['child-nationality'][$i], "Indian" ); ?> >Indian</option>
                                                    <option value="Others" <?php selected($childrens['child-nationality'][$i], "Others" ); ?> >Others</option> -->
                                                    <option value="" >--Select--</option>
                                                    <?php foreach($employee_country_array as $key => $country): ?>
                                                    <option value="<?php echo $country->name; ?>" <?php selected( $childrens['child-nationality'][$i], $country->name ); ?> ><?php _e($country->name, 'wphrm'); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
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
                                    </tbody>
                                </table>


                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

</div>  
<?php } ?>