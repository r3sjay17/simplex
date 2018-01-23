<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $current_user, $wpdb;
$wphrmUserRole = implode(',', $current_user->roles);
$readonly_class = '';
$readonly = '';
$edit_mode = false;
$wphrm_messages_leave_rule_add = 'Leave Rule has been successfully added.';
$wphrm_messages_leave_rule_update = 'Leave Rule has been successfully updated.';
$wphrm_messages_leave_rule_delete = 'Leave Rule has been deleted!';
$wphrm_messages_leave_rule_error = 'Error!';
$wphrmGetPagePermissions = $this->WPHRMGetPagePermissions();

$leave_rules = $this->get_leave_rules();

?>

<!-- BEGIN PAGE HEADER-->
<div class="preloader">
<span class="preloader-custom-gif"></span>
</div>
<div id="add_leave_rule" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
                <h4 class="modal-title"><strong><i class="fa fa-edit"></i> <?php _e('Add Leave Rule', 'wphrm'); ?></strong></h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-success display-hide" id="wphrm_add_leaverule_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrm_messages_leave_rule_add); ?>
                    <button class="close" data-close="alert"></button>
                </div>
                <div class="alert alert-danger display-hide" id="wphrm_add_leaverule_error">
                    <button class="close" data-close="alert"></button>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    <form method="POST" accept-charset="UTF-8" class="form-horizontal" id="wphrm_add_leaverule_form" data-ajax_url="<?php echo site_url("wp-admin/admin-ajax.php") ?>">
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Leave Rule', 'wphrm'); ?>  </label>
                                <div class="col-md-8">
                                    <input class="form-control form-control-inline " name="wphrm-leaveRule" id="wphrm-leaveRule" type="text" value="" placeholder="<?php _e('Leave Rule Name', 'wphrm'); ?>" required />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Description', 'wphrm'); ?>  </label>
                                <div class="col-md-8">
                                    <textarea class="form-control form-control-inline icheck" name="wphrm-leaveRule-description" id="wphrm-leaveRule-description" type="checkbox" value="1"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Employee Type', 'wphrm'); ?></label>
                                <div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-employee-type-ed" name="wphrm-employee-type-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>
                                <div class="col-md-5">
                                    <?php  $employee_levels = $this->get_employee_types(); ?>
                                    <select class="form-control make-sumoselect SumoUnder" name="wphrm-employee-type[]" id="wphrm-employee-type" multiple="yes" required >
                                        <?php foreach($employee_levels as $key => $employee_level): ?>
                                        <option value="<?php echo $key; ?>" <?php selected($key, $selected_employee_level); ?> ><?php echo $employee_level['title']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Employment Status', 'wphrm'); ?></label>
                                <div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-es-ed" name="wphrm-es-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>
                                <div class="col-md-5">
                                    <select class="form-control select2me" name="wphrm-employment-status" id="wphrm-employment-status" required >
                                        <option value="">Select Employment Status</option>
                                        <option value="1">Regular Less Than 1 Year</option>
                                        <option value="2">Regular</option>
                                        <option value="3">Contractual/Part Time</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group hide yis-holder">
                                <label class="control-label col-md-3"><?php _e('Years in Service', 'wphrm'); ?></label>
                                <div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-yis-ed" name="wphrm-yis-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>
                                <div class="col-md-5">
                                    <input class="form-control form-control-inline " name="wphrm-yis" id="wphrm-yis" type="number" value="" placeholder="<?php _e('Years in Service', 'wphrm'); ?>" min="0" required />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Gender', 'wphrm'); ?></label>
                                <div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-gender-ed" name="wphrm-gender-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>
                                <div class="col-md-5">
                                    <select class="form-control select2me" name="wphrm-gender" id="wphrm-gender" required >
                                        <option value="">Select Gender</option>
                                        <option value="1">Male</option>
                                        <option value="2">Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Nationality', 'wphrm'); ?></label>
                                <div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-nationality-ed" name="wphrm-nationality-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>
                                <div class="col-md-5">
                                    <select class="form-control select2me" name="wphrm-nationality" id="wphrm-nationality" required >
                                        <option value="">Select Nationality</option>
                                        <option value="1">Citizen</option>
                                        <option value="2">Foreigner</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Age', 'wphrm'); ?></label>
                                <div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-age-ed" name="wphrm-age-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>
                                <div class="col-md-5">
                                    <input class="form-control form-control-inline " name="wphrm-age" id="wphrm-age" type="number" value="" placeholder="<?php _e('Age', 'wphrm'); ?>" min="0" required />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Marital Status', 'wphrm'); ?></label>
                                <div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-status-ed" name="wphrm-status-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>
                                <div class="col-md-5">
                                    <select class="form-control   make-sumoselect SumoUnder" name="wphrm-status[]" id="wphrm-status"  multiple="yes" required >
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Widow">Widow</option>
                                        <option value="Separated">Separated</option>
                                        <option value="Divorced">Divorced</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Max Children Covered', 'wphrm'); ?></label>
                                <div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-max-child-ed" name="wphrm-max-child-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>
                                <div class="col-md-5">
                                    <input class="form-control form-control-inline " name="wphrm-max-child" id="wphrm-max-child" type="number" value="" placeholder="<?php _e('Max Children Covered', 'wphrm'); ?>" min="0" required />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Child Age Limit', 'wphrm'); ?></label>
                                <div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-age-limit-ed" name="wphrm-age-limit-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>
                                <div class="col-md-5">
                                    <input class="form-control form-control-inline " name="wphrm-age-limit" id="wphrm-age-limit" type="number" value="" placeholder="<?php _e('Child Age Limit', 'wphrm'); ?>" min="0" required />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Child Nationality', 'wphrm'); ?></label>
                                <div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-child-nationality-ed" name="wphrm-child-nationality-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>
                                <div class="col-md-5">
                                    <select class="form-control select2me" name="wphrm-child-nationality" id="wphrm-child-nationality" required >
                                        <option value="">Select Nationality</option>
                                        <option value="1">Citizen</option>
                                        <option value="2">Foreigner</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Medical Claim Limit', 'wphrm'); ?></label>
                                <div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-claim-limit-ed" name="wphrm-claim-limit-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>
                                <div class="col-md-5">
                                    <input class="form-control form-control-inline " name="wphrm-claim-limit" id="wphrm-claim-limit" type="number" value="" placeholder="<?php _e('Medical Claim Limit', 'wphrm'); ?>" min="0" required />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Elderly Screening Limit', 'wphrm'); ?></label>
                                <div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-elderly-limit-ed" name="wphrm-elderly-limit-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>
                                <div class="col-md-5">
                                    <input class="form-control form-control-inline " name="wphrm-elderly-limit" id="wphrm-elderly-limit" type="number" value="" placeholder="<?php _e('Elderly Screening Limit', 'wphrm'); ?>" min="0" required />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Maternity', 'wphrm'); ?></label>
                                <!--<div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-maternity-ed" name="wphrm-maternity-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>-->
                                <div class="col-md-8">
                                    <input  name="wphrm-maternity" required type="radio" value="Yes" class="icheck wphrm-maternity" >&nbsp;<?php _e('Yes', 'wphrm'); ?> &nbsp;&nbsp;
                                    <input  name="wphrm-maternity" required checked type="radio" value="No" class="icheck wphrm-maternity" >&nbsp;<?php _e('No', 'wphrm'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Paternity', 'wphrm'); ?></label>
                                <!--<div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-paternity-ed" name="wphrm-paternity-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>-->
                                <div class="col-md-8">
                                    <input  name="wphrm-paternity" required type="radio" value="Yes" class="icheck wphrm-paternity" >&nbsp;<?php _e('Yes', 'wphrm'); ?> &nbsp;&nbsp;
                                    <input  name="wphrm-paternity" required checked type="radio" value="No" class="icheck wphrm-paternity" >&nbsp;<?php _e('No', 'wphrm'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Off-in-Lieu', 'wphrm'); ?></label>
                                <!--<div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-lieu-ed" name="wphrm-lieu-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>-->
                                <div class="col-md-8">
                                    <input  name="wphrm-lieu" required type="radio" value="Yes" class="icheck wphrm-lieu" >&nbsp;<?php _e('Yes', 'wphrm'); ?> &nbsp;&nbsp;
                                    <input  name="wphrm-lieu" required checked type="radio" value="No" class="icheck wphrm-lieu" >&nbsp;<?php _e('No', 'wphrm'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Outstation', 'wphrm'); ?></label>
                                <!--<div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-outstation-ed" name="wphrm-outstation-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>-->
                                <div class="col-md-8">
                                    <input  name="wphrm-outstation" required type="radio" value="Yes" class="icheck wphrm-outstation" >&nbsp;<?php _e('Yes', 'wphrm'); ?> &nbsp;&nbsp;
                                    <input  name="wphrm-outstation" required checked type="radio" value="No" class="icheck wphrm-outstation" >&nbsp;<?php _e('No', 'wphrm'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Examination', 'wphrm'); ?></label>
                                <!--<div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-examination-ed" name="wphrm-examination-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>-->
                                <div class="col-md-8">
                                    <input  name="wphrm-examination" required type="radio" value="Yes" class="icheck wphrm-examination" >&nbsp;<?php _e('Yes', 'wphrm'); ?> &nbsp;&nbsp;
                                    <input  name="wphrm-examination" required checked type="radio" value="No" class="icheck wphrm-examination" >&nbsp;<?php _e('No', 'wphrm'); ?>
                                </div>
                            </div>
                            <!--RTY END-->
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-offset-3 col-md-9">
                                    <button type="submit"  class="btn blue"><i class="fa fa-plus"></i><?php _e('Add', 'wphrm'); ?> </button>
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
<div id="edit_leave_rule" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
                <h4 class="modal-title"><strong><i class="fa fa-edit"></i> <?php _e('Edit Leave Rule', 'wphrm'); ?></strong></h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-success display-hide" id="wphrm_edit_leaverule_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrm_messages_leave_rule_update); ?>
                    <button class="close" data-close="alert"></button>
                </div>
                <div class="alert alert-danger display-hide" id="wphrm_edit_leaverule_error">
                    <button class="close" data-close="alert"></button>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    <form method="POST" accept-charset="UTF-8" class="form-horizontal" id="wphrm_edit_leaverule_form" data-ajax_url="<?php echo site_url("wp-admin/admin-ajax.php") ?>">
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Leave Rule', 'wphrm'); ?>  </label>
                                <div class="col-md-8">
                                    <input class="form-control form-control-inline " name="wphrm-leaveRule-edit" id="wphrm-leaveRule-edit" type="text" value="" placeholder="<?php _e('Leave Rule Name', 'wphrm'); ?>" required />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Description', 'wphrm'); ?>  </label>
                                <div class="col-md-8">
                                    <textarea class="form-control form-control-inline icheck" name="wphrm-leaveRule-description-edit" id="wphrm-leaveRule-description-edit" type="checkbox" value="1"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Employee Type', 'wphrm'); ?></label>
                                <div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-employee-type-ed-edit" name="wphrm-employee-type-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>
                                <div class="col-md-5">
                                    <?php  $employee_levels = $this->get_employee_types(); ?>
                                    <select class="form-control  make-sumoselect SumoUnder" name="wphrm-employee-type-edit[]" id="wphrm-employee-type-edit" multiple="yes" required >
                                        <?php foreach($employee_levels as $key => $employee_level): ?>
                                        <option value="<?php echo $key; ?>" <?php selected($key, $selected_employee_level); ?> ><?php echo $employee_level['title']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Employment Status', 'wphrm'); ?></label>
                                <div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-es-ed-edit" name="wphrm-es-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>
                                <div class="col-md-5">
                                    <select class="form-control select2me" name="wphrm-employment-status-edit" id="wphrm-employment-status-edit" required>
                                        <option value="">Select Employment Status</option>
                                        <option value="1">Regular Less Than 1 Year</option>
                                        <option value="2">Regular</option>
                                        <option value="3">Contractual/Part Time</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group hide yis-holder-edit">
                                <label class="control-label col-md-3"><?php _e('Years in Service', 'wphrm'); ?></label>
                                <div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-yis-ed-edit" name="wphrm-yis-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>
                                <div class="col-md-5">
                                    <input class="form-control form-control-inline " name="wphrm-yis-edit" id="wphrm-yis-edit" type="number" value="" placeholder="<?php _e('Years in Service', 'wphrm'); ?>" min="0" required />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Gender', 'wphrm'); ?></label>
                                <div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-gender-ed-edit" name="wphrm-gender-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>
                                <div class="col-md-5">
                                    <select class="form-control select2me" name="wphrm-gender-edit" id="wphrm-gender-edit" required >
                                        <option value="">Select Gender</option>
                                        <option value="1">Male</option>
                                        <option value="2">Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Nationality', 'wphrm'); ?></label>
                                <div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-nationality-ed-edit" name="wphrm-nationality-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>
                                <div class="col-md-5">
                                    <select class="form-control select2me" name="wphrm-nationality-edit" id="wphrm-nationality-edit" required >
                                        <option value="">Select Nationality</option>
                                        <option value="1">Citizen</option>
                                        <option value="2">Foreigner</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Age', 'wphrm'); ?></label>
                                <div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-age-ed-edit" name="wphrm-age-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>
                                <div class="col-md-5">
                                    <input class="form-control form-control-inline " name="wphrm-age-edit" id="wphrm-age-edit" type="number" value="" placeholder="<?php _e('Age', 'wphrm'); ?>" min="0" required />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Marital Status', 'wphrm'); ?></label>
                                <div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-status-ed-edit" name="wphrm-status-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>
                                <div class="col-md-5">
                                    <select class="form-control   make-sumoselect SumoUnder" name="wphrm-status-edit[]" id="wphrm-status-edit" multiple="yes" required >
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Widow">Widow</option>
                                        <option value="Separated">Separated</option>
                                        <option value="Divorced">Divorced</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Max Children Covered', 'wphrm'); ?></label>
                                <div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-max-child-ed-edit" name="wphrm-max-child-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>
                                <div class="col-md-5">
                                    <input class="form-control form-control-inline " name="wphrm-max-child-edit" id="wphrm-max-child-edit" type="number" value="" placeholder="<?php _e('Max Children Covered', 'wphrm'); ?>" min="0" required />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Child Age Limit', 'wphrm'); ?></label>
                                <div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-age-limit-ed-edit" name="wphrm-age-limit-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>
                                <div class="col-md-5">
                                    <input class="form-control form-control-inline " name="wphrm-age-limit-edit" id="wphrm-age-limit-edit" type="number" value="" placeholder="<?php _e('Child Age Limit', 'wphrm'); ?>" min="0" required />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Child Nationality', 'wphrm'); ?></label>
                                <div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-child-nationality-ed-edit" name="wphrm-child-nationality-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>
                                <div class="col-md-5">
                                    <select class="form-control select2me" name="wphrm-child-nationality-edit" id="wphrm-child-nationality-edit" required >
                                        <option value="">Select Nationality</option>
                                        <option value="1">Citizen</option>
                                        <option value="2">Foreigner</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Medical Claim Limit', 'wphrm'); ?></label>
                                <div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-claim-limit-ed-edit" name="wphrm-claim-limit-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>
                                <div class="col-md-5">
                                    <input class="form-control form-control-inline " name="wphrm-claim-limit-edit" id="wphrm-claim-limit-edit" type="number" value="" placeholder="<?php _e('Medical Claim Limit', 'wphrm'); ?>" min="0" required />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Elderly Screening Limit', 'wphrm'); ?></label>
                                <div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-elderly-limit-ed-edit" name="wphrm-elderly-limit-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>
                                <div class="col-md-5">
                                    <input class="form-control form-control-inline " name="wphrm-elderly-limit-edit" id="wphrm-elderly-limit-edit" type="number" value="" placeholder="<?php _e('Elderly Screening Limit', 'wphrm'); ?>" min="0" required />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Maternity', 'wphrm'); ?></label>
                                <!--<div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-maternity-ed" name="wphrm-maternity-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>-->
                                <div class="col-md-8">
                                    <input  name="wphrm-maternity-edit" required type="radio" value="Yes" class="wphrm-maternity-edit" >&nbsp;<?php _e('Yes', 'wphrm'); ?> &nbsp;&nbsp;
                                    <input  name="wphrm-maternity-edit" required type="radio" value="No" class="wphrm-maternity-edit" >&nbsp;<?php _e('No', 'wphrm'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Paternity', 'wphrm'); ?></label>
                                <!--<div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-paternity-ed" name="wphrm-paternity-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>-->
                                <div class="col-md-8">
                                    <input  name="wphrm-paternity-edit" required type="radio" value="Yes" class="wphrm-paternity-edit" >&nbsp;<?php _e('Yes', 'wphrm'); ?> &nbsp;&nbsp;
                                    <input  name="wphrm-paternity-edit" required type="radio" value="No" class="wphrm-paternity-edit" >&nbsp;<?php _e('No', 'wphrm'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Off-in-Lieu', 'wphrm'); ?></label>
                                <!--<div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-lieu-ed" name="wphrm-lieu-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>-->
                                <div class="col-md-8">
                                    <input  name="wphrm-lieu-edit" required type="radio" value="Yes" class="wphrm-lieu-edit" >&nbsp;<?php _e('Yes', 'wphrm'); ?> &nbsp;&nbsp;
                                    <input  name="wphrm-lieu-edit" required type="radio" value="No" class="wphrm-lieu-edit" >&nbsp;<?php _e('No', 'wphrm'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Outstation', 'wphrm'); ?></label>
                                <!--<div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-outstation-ed" name="wphrm-outstation-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>-->
                                <div class="col-md-8">
                                    <input  name="wphrm-outstation-edit" required type="radio" value="Yes" class="wphrm-outstation-edit" >&nbsp;<?php _e('Yes', 'wphrm'); ?> &nbsp;&nbsp;
                                    <input  name="wphrm-outstation-edit" required type="radio" value="No" class="wphrm-outstation-edit" >&nbsp;<?php _e('No', 'wphrm'); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Examination', 'wphrm'); ?></label>
                                <!--<div class="col-md-3">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-examination-ed" name="wphrm-examination-ed"  checked="checked" data-on-color="success" data-on-text="<?php _e('Enabled', 'wphrm'); ?>" data-off-text="<?php _e('Disabled', 'wphrm'); ?>" data-off-color="danger">
                                </div>-->
                                <div class="col-md-8">
                                    <input  name="wphrm-examination-edit" required type="radio" value="Yes" class="wphrm-examination-edit" >&nbsp;<?php _e('Yes', 'wphrm'); ?> &nbsp;&nbsp;
                                    <input  name="wphrm-examination-edit" required type="radio" value="No" class="wphrm-examination-edit" >&nbsp;<?php _e('No', 'wphrm'); ?>
                                </div>
                            </div>
                            </div>
                            <!--RTY END-->
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-offset-3 col-md-9">
                                    <button type="submit"  class="btn blue"><i class="fa fa-plus"></i><?php _e('Update', 'wphrm'); ?> </button>
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
<div id="deleteModal" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="alert alert-success display-hide" id="WPHRMCustomDelete_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrm_messages_leave_rule_delete); ?>
                <button class="close" data-close="alert"></button>
            </div>
            <div class="alert alert-danger display-hide" id="WPHRMCustomDelete_success">
                <button class="close" data-close="alert"></button>
            </div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
                <h4 class="modal-title"><?php _e('Confirmation', 'wphrm'); ?></h4>
            </div>
            <div class="modal-body" id="info"><p><?php _e('Are you sure you want to delete', 'wphrm'); ?>?</p></div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn red" id="delete"><i class="fa fa-trash"></i><?php _e('Delete', 'wphrm'); ?> </button>
                <button type="button" data-dismiss="modal" aria-hidden="true" class="btn default"><i class="fa fa-times"></i><?php _e('Cancel', 'wphrm'); ?></button>
            </div>
        </div>
    </div>
</div>
<h3 class="page-title">
    <?php _e('Leave Types', 'wphrm'); ?>
</h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li><i class="fa fa-home"></i><?php _e('Home', 'wphrm'); ?><i class="fa fa-angle-right"></i></li>
        <li><?php _e('Leave Types', 'wphrm'); ?> </li>
    </ul>
</div>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div style="padding-left: 0px; padding-right:20px; padding-top:0px;" class="col-md-12">
    <div class="row">
        <div class="col-md-12">
            <a class="btn green " href="#add_leave_rule" data-toggle="modal"><i class="fa fa-plus"></i><?php _e('Add New Leave Rule', 'wphrm'); ?></a>
            <a class="btn green " href="?page=wphrm-leaves-application"><i class="fa fa-arrow-left"></i><?php _e('Back', 'wphrm'); ?> </a>
            
            
            
            <ul class="nav nav-tabs">
                <li><a href="<?php echo site_url("/wp-admin/admin.php?page=wphrm-leaves-application") ?>">Leave Request</a></li>
                <li><a href="<?php echo site_url("/wp-admin/admin.php?page=wphrm-view-attendance") ?>">Attendance Management</a></li>
                <li><a href="<?php echo site_url("/wp-admin/admin.php?page=wphrm-leave-type") ?>">Leave Type Management</a></li>
                <li class="active"><a href="#leave_rules">Leave Rules Management</a></li>
                <li><a href="<?php echo site_url("/wp-admin/admin.php?page=wphrm-leaves-application") ?>&annual=1">Annual Leave Management</a></li>
            </ul>
            
            <div class="tab-content">
                <div id="leave_rules" class="tab-pane fade in active">
            
                    <div class="portlet box blue calendar">
                        <div class="portlet-title">
                            <div class="caption"> <i class="fa fa-list"></i><?php _e('List of Leave Types', 'wphrm'); ?>  </div>
                        </div>
                        <div class="portlet-body">
                            <table class="table table-striped table-bordered table-hover" id="wphrmDataTable">
                                <thead>
                                    <tr> <th><?php _e('R.No', 'wphrm'); ?></th>
                                        <th><?php _e('Leave Rule', 'wphrm'); ?></th>
                                        <th><?php _e('Employee Type', 'wphrm'); ?></th>
                                        <th><?php _e('Employement Status', 'wphrm'); ?></th>
                                        <th><?php _e('Years in Service', 'wphrm'); ?></th>
                                        <th><?php _e('Gender', 'wphrm'); ?></th>
                                        <th><?php _e('Nationality', 'wphrm'); ?></th>
                                        <th><?php _e('Age', 'wphrm'); ?></th>
                                        <th><?php _e('Marital Status', 'wphrm'); ?></th>
                                        <th><?php _e('Max Children Covered', 'wphrm'); ?></th>
                                        <th><?php _e('Child Age Limit', 'wphrm'); ?></th>
                                        <th><?php _e('Child Nationality', 'wphrm'); ?></th>
                                        <th><?php _e('Medical Claim Limit', 'wphrm'); ?></th>
                                        <th><?php _e('Elderly Screening Limit', 'wphrm'); ?></th>
                                        <th><?php _e('Actions', 'wphrm'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i=1;
                                    $wphrm_leaverules = $wpdb->get_results("SELECT * FROM   $this->WphrmLeaveRulesTable ORDER BY leaveRule ASC");
                                    
                                    foreach ($wphrm_leaverules as $key => $wphrm_leaverule) { ?>
                                        <tr>
                                            <td><?php echo esc_html($i); ?></td>
                                            <td><?php if( isset( $wphrm_leaverule->leaveRule ) ): echo esc_html( $wphrm_leaverule->leaveRule ); endif; ?></td>
                                            <td>
                                                <?php 
                                                    $emptype = explode( ',', $wphrm_leaverule->employeeType );
                                                    foreach( $emptype as $et ) {
                                                        switch( $et ) {
                                                            case 'senior_manager': echo 'Level 1 Senior Manager (SM-III to SM-I)<br>'; break;
                                                            case 'manager': echo 'Level 2 Manager (M-III to M-I)<br>'; break;
                                                            case 'supervisor' : echo 'Level 3 Executive & Supervisor (NE-II to E-I)<br>'; break;
                                                            case 'staff' : echo 'Level 4 Staff (NE-III)'; break;
                                                            default: echo esc_html( $wphrm_leaverule->employeeType ); break;
                                                        }
                                                    }
                                                ?>
                                            </td>
                                            <td>
                                                <?php  
                                                    switch( $wphrm_leaverule->employment_status ) {
                                                        case 1: echo 'Regular less than 1 year'; break;
                                                        case 2: echo 'Regular'; break;
                                                        case 3: echo 'Contractual/Part Time'; break;
                                                        default: echo 'N/A'; break;
                                                    }
                                                ?>
                                            </td>
                                            <td><?php echo esc_html( $wphrm_leaverule->years_in_service ) == 0 ? 'N/A' : esc_html( $wphrm_leaverule->years_in_service ) ?></td>
                                            <td>
                                                <?php  
                                                    switch( $wphrm_leaverule->gender ) {
                                                        case 1: echo 'Male'; break;
                                                        case 2: echo 'Female'; break;
                                                        default: echo 'N/A'; break;
                                                    }
                                                ?>
                                            </td>
                                            <td>
                                                <?php  
                                                    switch( $wphrm_leaverule->nationality ) {
                                                        case 1: echo 'Citizen'; break;
                                                        case 2: echo 'Foreigner'; break;
                                                        default: echo 'N/A'; break;
                                                    }
                                                ?>
                                            </td>
                                            <td><?php echo esc_html( $wphrm_leaverule->age ) == 0 ? 'N/A' : esc_html( $wphrm_leaverule->age ) ?></td>
                                            <td style="text-transform: capitalize"> 
                                                <?php 
                                                    $marital_s = explode( ',', $wphrm_leaverule->marital_status ); 
                                                    foreach( $marital_s as $marital ) {
                                                        echo $marital.'<br>';
                                                    }
                                                ?> 
                                            </td>
                                            <td><?php echo esc_html( $wphrm_leaverule->max_children_covered ) == 0 ? 'N/A' : esc_html( $wphrm_leaverule->max_children_covered ) ?></td>
                                            <td><?php echo esc_html( $wphrm_leaverule->child_age_limit ) == 0 ? 'N/A' : esc_html( $wphrm_leaverule->child_age_limit ) ?></td>
                                            <td>
                                                <?php  
                                                    switch( $wphrm_leaverule->child_nationality ) {
                                                        case 1: echo 'Citizen'; break;
                                                        case 2: echo 'Foreigner'; break;
                                                        default: echo 'N/A'; break;
                                                    }
                                                ?>
                                            </td>
                                            <td><?php echo esc_html( $wphrm_leaverule->medical_claim_limit ) == 0 ? 'N/A' : esc_html( $wphrm_leaverule->medical_claim_limit ) ?></td>
                                            <td><?php echo esc_html( $wphrm_leaverule->elderly_screening_limit ) == 0 ? 'N/A' : esc_html( $wphrm_leaverule->elderly_screening_limit ) ?></td>
                                            <td>
                                                <a class="btn purple" data-toggle="modal" title="View/Edit" href="#edit_leave_rule" onclick="leaveruleEdit(<?php if (isset($wphrm_leaverule->id)): echo esc_js($wphrm_leaverule->id); endif; ?>, '<?php if (isset($wphrm_leaverule->leaveRule)): echo esc_js($wphrm_leaverule->leaveRule); endif; ?>', '<?php if (isset($wphrm_leaverule->description)): echo esc_js($wphrm_leaverule->description); endif; ?>', '<?php if (isset($wphrm_leaverule->employeeType)): echo esc_js($wphrm_leaverule->employeeType); endif; ?>', '<?php if (isset($wphrm_leaverule->employment_status)): echo esc_js($wphrm_leaverule->employment_status); endif; ?>', '<?php if (isset($wphrm_leaverule->years_in_service)): echo esc_js($wphrm_leaverule->years_in_service); endif; ?>', '<?php echo $wphrm_leaverule->gender; ?>', '<?php echo $wphrm_leaverule->nationality; ?>', '<?php echo $wphrm_leaverule->age; ?>', '<?php echo $wphrm_leaverule->marital_status; ?>', '<?php echo $wphrm_leaverule->max_children_covered; ?>', '<?php echo $wphrm_leaverule->child_age_limit; ?>', '<?php echo $wphrm_leaverule->child_nationality; ?>', '<?php echo $wphrm_leaverule->medical_claim_limit; ?>', '<?php echo $wphrm_leaverule->elderly_screening_limit; ?>', '<?php echo empty($wphrm_leaverule->maternity)?'No':$wphrm_leaverule->maternity; ?>', '<?php echo empty($wphrm_leaverule->paternity)?'No':$wphrm_leaverule->paternity; ?>', '<?php echo empty($wphrm_leaverule->off_in_lieu)?'No':$wphrm_leaverule->off_in_lieu; ?>', '<?php echo empty($wphrm_leaverule->outstation)?'No':$wphrm_leaverule->outstation; ?>', '<?php echo empty($wphrm_leaverule->examination)?'No':$wphrm_leaverule->examination; ?>')"> <i class="fa fa-edit"></i></a>
                                                    <a class="btn red" href="javascript:;" title="Delete item" onclick="WPHRMCustomDelete(<?php if (isset($wphrm_leaverule->id)): echo esc_js($wphrm_leaverule->id); endif; ?>, '<?php echo esc_js($this->WphrmLeaveRulesTable) ?>', 'id')">
                                                   <i class="fa fa-trash"></i></a>
                                               </td>
                                        </tr>
                                    <?php $i++; } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                </div>
            </div>
            
        </div>
    </div>
</div>
<!-- END PAGE CONTENT-->
