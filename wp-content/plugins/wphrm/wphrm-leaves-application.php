<?php
if (!defined('ABSPATH')) exit;
global $current_user, $wpdb, $wp_query;
wp_enqueue_style('jaf-wphrm-css');
$wphrmCurrentuserId = $current_user->ID;
$wphrmUserRole = implode(',', $current_user->roles);
$wphrmLeaveUpdateMessages = $this->WPHRMGetMessage(32);
$wphrmLeaveDoneMessages = $this->WPHRMGetMessage(33);
$wphrmGetPagePermissions = $this->WPHRMGetPagePermissions(); 

if( isset( $_GET['annual'] ) ) {
    $annual = 'active';
    $request = '';
} else {
    $request = 'active';
    $annual = '';
}

//add user permission to approving officer
$wphrmEmployeeInfo = $this->WPHRMGetUserDatas($current_user->ID, 'wphrmEmployeeInfo');
if($wphrmEmployeeInfo && isset($wphrmEmployeeInfo['wphrm_employee_approving_officer']) && filter_var($wphrmEmployeeInfo['wphrm_employee_approving_officer'], FILTER_VALIDATE_BOOLEAN)){
    $wphrmGetPagePermissions[] = 'manageOptionsApproveLeaveApplications';
}
?>
<!-- BEGIN PAGE HEADER-->
<div class="preloader">
    <span class="preloader-custom-gif"></span>
</div>
<div id="add_static" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
                <h4 class="modal-title"><strong><i class="fa fa-edit"></i> <?php _e('Leave Application', 'wphrm'); ?></strong></h4>
            </div>
            <div class="modal-body">
                <div class="portlet-body form">
                    <div class="alert alert-success display-hide" id="wphrm_add_leave_applications_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrmLeaveDoneMessages); ?>
                        <button class="close" data-close="alert"></button>
                    </div>
                    <div class="alert alert-danger display-hide" id="wphrm_add_leave_applications_error">
                        <button class="close" data-close="alert"></button>
                    </div>
                    <!-- BEGIN FORM-->
                    <form method="POST"  accept-charset="UTF-8" class="form-horizontal" id="wphrm_user_leave_applications_frm">
                        <div class="form-body">
                            <input type="hidden" class="wphrm_leave_left" value="0">
                            <input type="hidden" class="wphrm_toleave" value="0">
                            <input type="hidden" class="wphrm_leave_limit" value="0">
                            <input type="hidden" class="mo_default_date" value="0">
                            <input type="hidden" class="mo_mutual_date" value="0">
                            <input  type="hidden" id="wphrm_employeeID" name="wphrm_employeeID" value="<?php
                            if (isset($wphrmCurrentuserId)): echo esc_attr($wphrmCurrentuserId);
                            endif;
                            ?>"/>
                            <input  type="hidden" id="wphrm_status" name="wphrm_status" value="absent"/>
                            <input  type="hidden" id="wphrm_application_status" name="wphrm_application_status" value="pending"/>
                            <input  type="hidden" id="wphrm_attendanceID" name="wphrm_attendanceID" />

                            <input  type="hidden" id="wphrm-from-leve-day" name="wphrm-from-leve-day" />
                            <input  type="hidden" id="wphrm-to-leve-day" name="wphrm-to-leve-day" />
                            
                            <div class="col-md-12 alert-holder label-danger" id="alert">The number of leave taken exceeded your leave balance. You are only allowed to use your remaining  days of leave</div>
                            <div class="clearfix"></div>
                            
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Leave Type', 'wphrm'); ?>  </label>
                                <div class="col-md-8">
                                    <?php $employee_allowed_leaves = $this->employee_allowed_leaves($wphrmCurrentuserId); ?>
                                    <select class="form-control wphrm_leavetype" id="wphrm_leavetyped" name="wphrm_leavetype" autocomplete='off' >
                                        <option value=""><?php _e('Select leave type', 'wphrm'); ?></option>
                                        <?php
                                        $selected = '';
                                        $wphrm_leavetypes = $wpdb->get_results("SELECT * FROM  $this->WphrmLeaveTypeTable ORDER BY leaveType ASC");

                                        foreach ($wphrm_leavetypes as $key => $wphrm_leavetype) {
                                            if(!in_array($wphrm_leavetype->id, $employee_allowed_leaves)) continue;
                                            ?>
                                            <option value="<?php
                                            if (isset($wphrm_leavetype->id)) : echo esc_attr($wphrm_leavetype->id);
                                            endif;
                                            ?>"><?php
                                                        if (isset($wphrm_leavetype->leaveType)) : echo esc_attr($wphrm_leavetype->leaveType);
                                                        endif;
                                                        ?></option>
                                        <?php } ?>
                                    </select>
                                    <p class="hide description leave-description"></p>
                                </div>
                            </div>

                            <table class="holidayDivHide leave-table">
                                <tr><th>
                                        <?php _e('Holidays/Weekends in between', 'wphrm'); ?> : &nbsp;</th> <td><span class="label label-sm label-success" id="holidayview"></span>
                                    </td>
                                </tr>
                                <tr><th>
                                        <?php _e('Time Period', 'wphrm'); ?> : </th> <td><span class="label label-sm label-info" id="leaveview"></span>
                                    </td>
                                </tr>
                                <tr class="hide"><th>
                                        <?php _e('Earned Leaves', 'wphrm'); ?> : </th> <td><span class="label label-sm label-success" id="leavepaid"></span>
                                    </td>
                                </tr>
                                <tr class="hide"><th>
                                        <?php _e('Paid Leaves', 'wphrm'); ?> : </th> <td><span class="label label-sm label-danger" id="leaveunpaid"></span>
                                    </td>
                                </tr>
                                <!--J@F-->
                                <tr>
                                    <th>
                                        <?php _e('Used/Limit', 'wphrm'); ?> : </th> <td><span class="label label-sm label-danger" id="leavelimit"></span>
                                    </td>
                                </tr>
                                <!--J@F-->
                                <tr class="hide">
                                    <th>
                                        <?php _e('Max Children Covered', 'wphrm'); ?> : </th> <td><span class="label label-sm label-danger" id="max_children"></span>
                                    </td>
                                </tr>
                                <tr class="hide">
                                    <th>
                                        <?php _e('Child Age Limit', 'wphrm'); ?> : </th> <td><span class="label label-sm label-danger" id="child_age"></span>
                                    </td>
                                </tr>
                                <tr class="hide">
                                    <th>
                                        <?php _e('Medical Reimbursement', 'wphrm'); ?> : </th> <td><span class="label label-sm label-danger" id="reimbursement"></span>
                                    </td>
                                </tr>
                                <tr class="hide">
                                    <th>
                                        <?php _e('Claimed Reimbursement', 'wphrm'); ?> : </th> <td><span class="label label-sm label-danger" id="claimed_reimbursement"></span>
                                    </td>
                                </tr>
                                <tr class="hide">
                                    <th>
                                        <?php _e('Elderly Screening Limit', 'wphrm'); ?> : </th> <td><span class="label label-sm label-danger" id="elderlylimit"></span>
                                    </td>
                                </tr>
                                <!--RTY-->
                            </table>
                            
                            <!--J@F-->
                            <div class="form-group leave-medical-claim hide">
                                <label class="control-label col-md-3"><?php _e('Medical Claim', 'wphrm'); ?>  </label>
                                <div class="col-md-8">
                                    <input type="number" name="medical_claim" class="form-control" >
                                    <p class="description">Please enter the medical claim amount. <span class="amount"></span></p>
                                </div>
                            </div>
                            <div class="form-group leave-elderly-screening hide">
                                <label class="control-label col-md-3"><?php _e('Elderly Screening', 'wphrm'); ?>  </label>
                                <div class="col-md-8">
                                    <input type="number" name="elderly_screening" class="" >
                                    <p class="description">Please enter the elderly screening amount. <span class="amount"></span></p>
                                </div>
                            </div>
                            <!--J@F END-->

                            <!--Maternity Leave-->
                            <div class="form-group maternity-wrapper hide">
                                <label class="control-label col-md-3"><?php _e('Expected Delivery Date', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <div class="input-group input-medium date after-current-date"  data-date-format="dd-mm-yyyy" >
                                        <input class="form-control" data-date-format="dd-mm-yyyy" name="wphrm_due_date" type="text" id="wphrm_due_date" value="" autocapitalize="none"  />
                                        <span class="input-group-btn">
                                            <button class="btn" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>
                                     <div class=""><p class="description" >Enter the child's date of birth or estimated delivery date, whichever is later.</p></div>
                                </div>
                            </div>
                            <!--end Maternity Leave-->
                            
                            
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('From Date', 'wphrm'); ?>  </label>
                                <div class="col-md-5">

                                    <div class="input-group input-medium date after-current-date toDateBetweenDate "  data-date-format="dd-mm-yyyy" >
                                        <input class="form-control leave-date-picker" data-date-format="dd-mm-yyyy" name="wphrm_leavedate" type="text" id="wphrm_leavedate" value="" autocapitalize="none"  />
                                        <span class="input-group-btn">
                                            <button class="btn" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>

                                    <!--<input class="form-control form-control-inline input-medium date after-current-date leave-date-picker fromDateBetweenDate" type="text" name="wphrm_leavedate" id="wphrm_leavedate" data-date-format="dd-mm-yyyy" placeholder="Leave From Date" />-->
                                    <div class=""><p class="description" >Set Full-day or Half-day on this date.</p></div>
                                </div>
                                <div class="col-md-2">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-from-leve-day-type fromDateBetweentype" name="wphrm-from-leve-day-type"  checked="checked" data-on-color="success" data-on-text="<?php _e('Full', 'wphrm'); ?>" data-off-text="<?php _e('Half', 'wphrm'); ?>" data-off-color="danger">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('To Date', 'wphrm'); ?>  </label>
                                <div class="col-md-5">

                                    <div class="input-group input-medium date after-current-date toDateBetweenDate "  data-date-format="dd-mm-yyyy" >
                                        <input class="form-control leave-date-picker" data-date-format="dd-mm-yyyy" name="wphrm_leavedate_to" type="text" id="wphrm_leavedate_to" value="" autocapitalize="none"  />
                                        <span class="input-group-btn">
                                            <button class="btn" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>

                                    <!--<input class="form-control form-control-inline input-medium date after-current-date leave-date-picker toDateBetweenDate"   type="text" name="wphrm_leavedate_to" id="wphrm_leavedate_to" placeholder="Leave To Date" />-->
                                    <div class=""><p class="description" >Set Full-day or Half-day on this date. If single day, half day for this option is ignored.</p></div>
                                </div>
                                <div class="col-md-2">
                                    <input  type="checkbox" value="1"   class="wphrm-make-switch wphrm-to-leve-day-type toDateBetweentype" name="wphrm-to-leve-day-type" checked="checked" data-on-color="success" data-on-text="<?php _e('Full', 'wphrm'); ?>" data-off-text="<?php _e('Half', 'wphrm'); ?>" data-off-color="danger">
                                </div>
                            </div>
                            
                            <!--Maternity Leave-->
                            <div class="form-group maternity-options-holder hide">
                                <label class="control-label col-md-12 options-label"><?php _e('Your other maternity leave options are:', 'wphrm'); ?></label>
                                <div class="col-md-12">
                                    <table class="maternity-options">
                                        <thead>
                                            <tr>
                                                <th width="10%"></th>
                                                <th width="40%">Option</th>
                                                <th width="50%">Dates</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><input  name="maternity-option" type="radio" id="default-option" value="default" class="xicheck" checked ></td>
                                                <td class="default-text"></td>
                                                <td class="default-date"></td>
                                            </tr>
                                            <tr>
                                                <td><input  name="maternity-option" type="radio" id="mutual-option" value="mutual" class="xicheck" ></td>
                                                <td class="mutual-text">By mutual agreement: Take the last 8 weeks flexibly within 12 months of delivery</td>
                                                <td class="mutual-date"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!--end Maternity Leave-->

                            <!--J@F-->
                            <!--<div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Upload Document', 'wphrm'); ?></label>
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
                                                <input type="file" id="attendance-document" name="attendance-document" class="documents-Upload">
                                            </span>
                                            <a href="#" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput">
                                                <?php _e('Remove', 'wphrm'); ?> </a>
                                        </div>
                                        <p class="description">Attach file to support your leave especially Sick/medical (this is a must for this type of leave) leave</p>
                                    </div>
                                </div>
                            </div>-->
                            <!--J@F-->

                            <div class="form-group paternity-holder hide">
                                <label class="control-label col-md-3"><?php _e('Child Date of Birth', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <div class="input-group input-medium date after-current-date"  data-date-format="dd-mm-yyyy" >
                                        <input class="form-control" data-date-format="dd-mm-yyyy" name="wphrm_child_bdate" type="text" id="wphrm_child_bdate" value="" autocapitalize="none"  />
                                        <span class="input-group-btn">
                                            <button class="btn" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group lieu-holder hide">
                                <label class="control-label col-md-3"><?php _e('Date of Work', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <div class="input-group input-medium date after-current-date"  data-date-format="dd-mm-yyyy" >
                                        <input class="form-control" data-date-format="dd-mm-yyyy" name="wphrm_work_date" type="text" id="wphrm_work_date" value="" autocapitalize="none"  />
                                        <span class="input-group-btn">
                                            <button class="btn" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group outstation-holder hide">
                                <label class="control-label col-md-3"><?php _e('Outstation Leave Date Range', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <div class="input-group input-medium date after-current-date"  data-date-format="dd-mm-yyyy" >
                                        <input class="form-control" data-date-format="dd-mm-yyyy" name="wphrm_outstation_date" type="text" id="wphrm_outstation_date" value="" autocapitalize="none"  />
                                        <span class="input-group-btn">
                                            <button class="btn" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group outstation-holder hide">
                                <label class="control-label col-md-3"><?php _e('Country of Assignment', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <input class="form-control" name="wphrm_assign_country" type="text" id="wphrm_assign_country" value="" autocapitalize="none"  />
                                </div>
                            </div>

                            <div class="form-group examination-holder hide">
                                <label class="control-label col-md-3"><?php _e('Name of Examination', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <input class="form-control" name="wphrm_examination_name" type="text" id="wphrm_examination_name" value="" autocapitalize="none"  />
                                </div>
                            </div>
                            <div class="form-group examination-holder hide">
                                <label class="control-label col-md-3"><?php _e('Date of Examination', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <div class="input-group input-medium date after-current-date"  data-date-format="dd-mm-yyyy" >
                                        <input class="form-control" data-date-format="dd-mm-yyyy" name="wphrm_examination_date" type="text" id="wphrm_examination_date" value="" autocapitalize="none"  />
                                        <span class="input-group-btn">
                                            <button class="btn" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group examination-holder hide">
                                <label class="control-label col-md-3"><?php _e('Time of Examination', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <input class="form-control" name="wphrm_examination_time" type="text" id="wphrm_examination_time" value="" autocapitalize="none"  />
                                </div>
                            </div>
                            
                            
                            
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Reason', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <textarea class="form-control form-control-inline " rows="2"  name="wphrm_reason" id="wphrm_reason" placeholder="Reason"></textarea>
                                    <p class="description" >Give some information why you are going to leave.</p>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-offset-3-XXX col-md-12 text-center">
                                    <button type="submit"  class="demo-loading-btn btn blue"><i class="fa fa-edit"></i><?php _e('Apply Leave', 'wphrm'); ?></button>
                                    <!--<button type="button" data-dismiss="modal" aria-hidden="true" class="btn red"><i class="fa fa-times"></i><?php _e('Cancel', 'wphrm'); ?></button>-->
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
<div id="edit_static" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
                <h4 class="modal-title"><strong><i class="fa fa-edit"></i><?php _e('Leave Application Edit', 'wphrm'); ?> </strong></h4>
            </div>
            <div class="modal-body">
                <div class="portlet-body form">
                    <div class="alert alert-success display-hide" id="wphrm_edit_application_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrmLeaveUpdateMessages); ?>
                        <button class="close" data-close="alert"></button>
                    </div>
                    <div class="alert alert-danger display-hide" id="wphrm_edit_application_error">
                        <button class="close" data-close="alert"></button>
                    </div>
                    <!-- BEGIN FORM-->
                    <form method="POST"  accept-charset="UTF-8" class="form-horizontal" id="wphrm_leave_applications_frm">
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Name', 'wphrm'); ?>  </label>
                                <div class="col-md-8">
                                    <input class="form-control form-control-inline " readonly type="text" value="" id="application_name" placeholder="Name" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Leave Type', 'wphrm'); ?>  </label>
                                <div class="col-md-8">
                                    <input class="form-control form-control-inline application_leavetype leave-label" readonly type="text" value="" placeholder="Leave Type" />
                                    <input class="form-control form-control-inline application_leavetype leave-value" type="hidden" name="application_leavetype"/>
                                    <input class="form-control form-control-inline wphrm-from-leve-day-type" type="hidden" name="wphrm-from-leve-day-type"/>
                                    <input class="form-control form-control-inline wphrm-to-leve-day-type" type="hidden" name="wphrm-to-leve-day-type"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('From Date', 'wphrm'); ?>  </label>
                                <div class="col-md-4">
                                    <input class="form-control form-control-inline application_leavedate" readonly type="text" value="" id="" placeholder="Leave From Date" />
                                    <input class="form-control form-control-inline application_leavedate" type="hidden" name="application_leavedate"/>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control form-control-inline wphrm-from" readonly type="text" value="" id=""  />

                                </div>
                                <div class="col-md-9 col-md-offset-3"><p class="description" >Set this to half if you want to set your leave to afternoon only</p></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('To Date', 'wphrm'); ?>  </label>
                                <div class="col-md-4">
                                    <input class="form-control form-control-inline application_leavedate_to" readonly type="text"  placeholder="Leave To Date" />
                                    <input class="form-control form-control-inline application_leavedate_to" type="hidden" name="application_leavedate_to"/>
                                </div>
                                <div class="col-md-4">
                                    <input class="form-control form-control-inline wphrm-to" readonly type="text" value=""  />

                                </div>
                                <div class="col-md-9 col-md-offset-3"><p class="description" >Set this to half if you want to set your leave to morning only</p></div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Reason', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <textarea class="form-control form-control-inline application_reason" rows="2" readonly type="text"  placeholder="Reason"></textarea>
                                    <input class="form-control form-control-inline application_reason" type="hidden" name="application_reason"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Applied On', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <input class="form-control form-control-inline application_appliedon" readonly type="text" value=""  placeholder="Applied On " />
                                    <input class="form-control form-control-inline application_appliedon" type="hidden" name="application_appliedon"/>
                                </div>
                            </div>

                            <!--J@F-->
                            <!--<div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Attached Document', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <ul id="leave_application_attachment">
                                    </ul>
                                </div>
                            </div>-->
                            <!--J@F-->

                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Leave Balance', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <input class="form-control" name="wphrm_leave_balance" type="text" id="wphrm_leave_balance" autocapitalize="none" readonly  />
                                </div>
                            </div>

                            <div class="form-group medical-holder hide">
                                <label class="control-label col-md-3"><?php _e('Reimbursement Amount', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <input class="form-control" name="wphrm_reimbursement_amount" type="text" id="wphrm_reimbursement_amount" autocapitalize="none" readonly  />
                                </div>
                            </div>

                            <div class="form-group maternity-holder hide">
                                <label class="control-label col-md-3"><?php _e('Expected Date of Delivery', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <input class="form-control" name="wphrm_due_date_admin" type="text" id="wphrm_due_date_admin" autocapitalize="none" readonly  />
                                </div>
                            </div>
                            <div class="form-group maternity-holder hide">
                                <label class="control-label col-md-3"><?php _e('Maternity Leave Option', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <textarea class="form-control wphrm_maternity_option_admin" rows="2" readonly type="text"  placeholder="Maternity Option"></textarea>
                                </div>
                            </div>

                            <div class="form-group paternity-holder hide">
                                <label class="control-label col-md-3"><?php _e('Child Date of Birth', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <input class="form-control" name="wphrm_child_bdate_admin" type="text" id="wphrm_child_bdate_admin" autocapitalize="none" readonly  />
                                </div>
                            </div>

                            <div class="form-group lieu-holder hide">
                                <label class="control-label col-md-3"><?php _e('Date of Work', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <input class="form-control" name="wphrm_work_date_admin" type="text" id="wphrm_work_date_admin" autocapitalize="none" readonly  />
                                </div>
                            </div>

                            <div class="form-group outstation-holder hide">
                                <label class="control-label col-md-3"><?php _e('Outstation Leave Date Range', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <input class="form-control" name="wphrm_outstation_date_admin" type="text" id="wphrm_outstation_date_admin" autocapitalize="none" readonly />
                                </div>
                            </div>
                            <div class="form-group outstation-holder hide">
                                <label class="control-label col-md-3"><?php _e('Country of Assignment', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <input class="form-control" name="wphrm_assign_country_admin" type="text" id="wphrm_assign_country_admin" readonly autocapitalize="none"  />
                                </div>
                            </div>

                            <div class="form-group examination-holder hide">
                                <label class="control-label col-md-3"><?php _e('Name of Examination', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <input class="form-control" name="wphrm_examination_name_admin" type="text" id="wphrm_examination_name_admin" readonly autocapitalize="none"  />
                                </div>
                            </div>
                            <div class="form-group examination-holder hide">
                                <label class="control-label col-md-3"><?php _e('Date of Examination', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <input class="form-control" name="wphrm_examination_date_admin" type="text" id="wphrm_examination_date_admin" readonly autocapitalize="none"  />
                                </div>
                            </div>
                            <div class="form-group examination-holder hide">
                                <label class="control-label col-md-3"><?php _e('Time of Examination', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <input class="form-control" name="wphrm_examination_time_admin" type="text" id="wphrm_examination_time_admin" readonly autocapitalize="none"  />
                                </div>
                            </div>
                            

                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Application Status', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <select id="applicationStatus" name="applicationStatus" class="form-control form-control-inline ">
                                        <option  value="approved"><?php _e('Approved', 'wphrm'); ?></option>
                                        <option value="rejected"><?php _e('Rejected', 'wphrm'); ?></option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Comments', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <textarea class="form-control form-control-inline" rows="2"  type="text" name="application_comment" placeholder="Comments"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-offset-3 col-md-9">
                                    <button type="submit" data-loading-text="Updating..." class="demo-loading-btn btn blue"><i class="fa fa-edit"></i><?php _e('Update', 'wphrm'); ?> </button>
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
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
                <h4 class="modal-title"><?php _e('Confirmation', 'wphrm'); ?></h4>
            </div>
            <div class="modal-body" id="info"><p><?php _e('Are you sure you want to delete', 'wphrm'); ?>?</p></div>
            <div class="modal-footer text-center">

                <button type="button" data-dismiss="modal" class="btn red" id="delete"><i class="fa fa-trash"></i><?php _e('Delete', 'wphrm'); ?> </button>
                <!--<button type="button" data-dismiss="modal" aria-hidden="true" class="btn default"><i class="fa fa-times"></i><?php _e('Cancel', 'wphrm'); ?></button>-->
            </div>
        </div>
    </div>
</div>
<div id="add_leave_entitlement" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
                <h4 class="modal-title"><strong><i class="fa fa-edit"></i> <?php _e('Add Leave Entitlement', 'wphrm'); ?></strong></h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-success display-hide" id="wphrm_add_leaveentitlement_success"><i class='fa fa-check-square' aria-hidden='true'></i> Leave Entitlement has been successfully added.
                    <button class="close" data-close="alert"></button>
                </div>
                <div class="alert alert-danger display-hide" id="wphrm_add_leaveentitlement_error"><i class="fa fa-exclamation-triangle" aria-hidden='true'></i> Error
                    <button class="close" data-close="alert"></button>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    <form method="POST" accept-charset="UTF-8" class="form-horizontal" id="wphrm_add_leaveentitlement_form" data-ajax_url="<?php echo site_url("wp-admin/admin-ajax.php");?>">
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Years of Service', 'wphrm'); ?>  </label>
                                <div class="col-md-8">
                                    <span>
                                        <input class="form-control" name="wphrm_yos" id="wphrm_yos" type="number" value="" min="0" required />
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('NE-III', 'wphrm'); ?>  </label>
                                <div class="col-md-8">
                                    <span>
                                        <input class="form-control" name="wphrm_staff" id="wphrm_staff" type="number" value="" min="0" />
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('NE-II to E-I', 'wphrm'); ?>  </label>
                                <div class="col-md-8">
                                    <span>
                                        <input class="form-control" name="wphrm_supervisor" id="wphrm_supervisor" type="number" value="" min="0" />
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('M-III to M-I', 'wphrm'); ?>  </label>
                                <div class="col-md-8">
                                    <span>
                                        <input class="form-control" name="wphrm_manager" id="wphrm_manager" type="number" value="" min="0" />
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('SM-III to SM-I', 'wphrm'); ?>  </label>
                                <div class="col-md-8">
                                    <span>
                                        <input class="form-control" name="wphrm_senior_manager" id="wphrm_senior_manager" type="number" value="" min="0" />
                                    </span>
                                </div>
                            </div>
                            <!--J@F END-->
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
<div id="edit_leave_entitlement" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
                <h4 class="modal-title"><strong><i class="fa fa-edit"></i> <?php _e('Edit Leave Entitlement', 'wphrm'); ?></strong></h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-success display-hide" id="wphrm_edit_leaveentitlement_success"><i class='fa fa-check-square' aria-hidden='true'></i> Leave Entitlement has been successfully updated.
                    <button class="close" data-close="alert"></button>
                </div>
                <div class="alert alert-danger display-hide" id="wphrm_edit_leaveentitlement_error"><i class="fa fa-exclamation-triangle" aria-hidden='true'></i> Error
                    <button class="close" data-close="alert"></button>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    <form method="POST" accept-charset="UTF-8" class="form-horizontal" id="wphrm_edit_leaveentitlement_form">
                        <div class="form-body">
                           <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Years of Service', 'wphrm'); ?>  </label>
                                <div class="col-md-8">
                                    <span>
                                        <input class="form-control" name="wphrm_yos" id="wphrm_yos_edit" type="number" value="" min="0" required />
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('NE-III', 'wphrm'); ?>  </label>
                                <div class="col-md-8">
                                    <span>
                                        <input class="form-control" name="wphrm_staff" id="wphrm_staff_edit" type="number" value="" min="0" />
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('NE-II to E-I', 'wphrm'); ?>  </label>
                                <div class="col-md-8">
                                    <span>
                                        <input class="form-control" name="wphrm_supervisor" id="wphrm_supervisor_edit" type="number" value="" min="0" />
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('M-III to M-I', 'wphrm'); ?>  </label>
                                <div class="col-md-8">
                                    <span>
                                        <input class="form-control" name="wphrm_manager" id="wphrm_manager_edit" type="number" value="" min="0" />
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('SM-III to SM-I', 'wphrm'); ?>  </label>
                                <div class="col-md-8">
                                    <span>
                                        <input class="form-control" name="wphrm_senior_manager" id="wphrm_senior_manager_edit" type="number" value="" min="0" />
                                    </span>
                                </div>
                            </div>
                            <!--J@F END-->
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
<h3 class="page-title">
    <?php _e('Leave Management', 'wphrm'); ?>
</h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li><i class="fa fa-home"></i><?php _e('Home', 'wphrm'); ?><i class="fa fa-angle-right"></i></li>
        <?php if(in_array('manageOptionsLeaveApplications', $wphrmGetPagePermissions)): ?>
        <li><?php _e('Leave Management', 'wphrm'); ?></li>
        <?php else: ?>
        <li><?php _e('My Attendance', 'wphrm'); ?></li>
        <?php endif; ?>
    </ul>
</div>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div style="padding-left: 0px; padding-right:20px; padding-top:0px;" class="col-md-12">
    <div class="row">
        <div class="col-md-12">
            <?php if (in_array('manageOptionsLeaveApplications', $wphrmGetPagePermissions)) { ?>
                <!--<a class="btn green" href="?page=wphrm-leave-type" data-toggle="modal"><i class="fa fa-repeat"></i><?php _e('Leave Types', 'wphrm'); ?></a>-->
                <?php //if( $annual == 'active' ) : ?>
            	   <a class="btn green " href="#add_leave_entitlement" data-toggle="modal"><i class="fa fa-plus"></i><?php _e('Add New Leave Entitlement', 'wphrm'); ?></a>
                <?php //endif;?>
            <?php } else { ?>
                <a class="btn green" data-toggle="modal" href="#add_static"><i class="fa fa-plus"></i><?php _e('Create Leave Application', 'wphrm'); ?></a>
            <?php } ?>

            <?php if (in_array('manageOptionsLeaveApplications', $wphrmGetPagePermissions) || in_array('manageOptionsApproveLeaveApplications', $wphrmGetPagePermissions)): ?>
            <ul class="nav nav-tabs">
                <li class="<?php echo $request?>"><a data-toggle="tab" href="#leave_management">Leave Requests</a></li>
                <li><a href="<?php echo site_url("/wp-admin/admin.php?page=wphrm-view-attendance") ?>">Attendance Management</a></li>
                <li><a href="<?php echo site_url("/wp-admin/admin.php?page=wphrm-leave-type") ?>">Leave Types Management</a></li>
                <li><a href="<?php echo site_url("/wp-admin/admin.php?page=wphrm-leave-rules") ?>">Leave Rules Management</a></li>
                <li class="<?php echo $annual?>"><a data-toggle="tab" href="#annual_leave_management">Annual Leave Management</a></li>
            </ul>
            
            <!--Admin-->
            <div class="tab-content">
                <div id="leave_management" class="tab-pane fade in <?php echo $request?>">
                    
                    <div class="portlet box blue calendar">
                        <div class="portlet-title">
                            <div class="caption"> <i class="fa fa-list"></i><?php _e('List of Leave Applications ', 'wphrm'); ?></div>
                        </div>
                        <div class="portlet-body">

                            <?php if (in_array('manageOptionsLeaveApplications', $wphrmGetPagePermissions) || in_array('manageOptionsApproveLeaveApplications', $wphrmGetPagePermissions)): ?>
                            <div class="panel">
                                <form action="<?php echo admin_url('admin.php'); ?>" method="GET" class="form-inline" role="form">
                                    <input type="hidden" value="wphrm-leaves-application" name="page" >
                                    <div class="form-group">
                                        <label class="sr-only" for="">Leave Type</label>
                                        <select class="form-control" id="filter_leavetyped" name="filter_leavetype" autocomplete='off' >
                                            <option value=""><?php _e('All', 'wphrm'); ?></option>
                                            <?php
                                            $selected = '';
                                            $wphrm_leavetypes = $wpdb->get_results("SELECT * FROM  $this->WphrmLeaveTypeTable ORDER BY leaveType ASC");
                                            foreach ($wphrm_leavetypes as $key => $wphrm_leavetype) {
                                                ?>
                                                <option value="<?php
                                                if (isset($wphrm_leavetype->id)) : echo esc_attr($wphrm_leavetype->id);
                                                endif;
                                                ?>" <?php selected(isset($_GET['filter_leavetype']) ? $_GET['filter_leavetype'] : '', $wphrm_leavetype->id); ?> ><?php
                                                            if (isset($wphrm_leavetype->leaveType)) : echo esc_attr($wphrm_leavetype->leaveType);
                                                            endif;
                                                            ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="sr-only" for="">Date Filter</label>
                                        <input type="date" class="form-control date-picker" data-date-format="yyyy-mm-dd" value="<?php echo isset($_GET['filter_date_start']) ? $_GET['filter_date_start'] : ''; ?>"  name="filter_date_start" placeholder="Date Start">
                                        <input type="date" class="form-control date-picker" data-date-format="yyyy-mm-dd" value="<?php echo isset($_GET['filter_date_end']) ? $_GET['filter_date_end'] : ''; ?>"  name="filter_date_end" placeholder="Date End">
                                    </div>
                                    <div class="form-group">
                                        <label class="sr-only" for="">Status</label>
                                        <select class="form-control" id="filter_status" name="filter_status" autocomplete='off' >
                                            <option value=""><?php _e('All', 'wphrm'); ?></option>
                                            <option value="approved" <?php selected(isset($_GET['filter_status']) ? $_GET['filter_status'] : '', 'approved'); ?> ><?php _e('Approved', 'wphrm'); ?></option>
                                            <option value="rejected" <?php selected(isset($_GET['filter_status']) ? $_GET['filter_status'] : '', 'rejected'); ?> ><?php _e('Rejected', 'wphrm'); ?></option>
                                            <option value="pending" <?php selected(isset($_GET['filter_status']) ? $_GET['filter_status'] : '', 'pending'); ?> ><?php _e('Pending', 'wphrm'); ?></option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="sr-only" for="">Status</label>
                                        <?php $employee_list = $this->WPHRMGetAllEmployees(); ?>
                                        <select class="form-control" id="filter_employee" name="filter_employee" autocomplete='off' >
                                            <option value=""><?php _e('All', 'wphrm'); ?></option>
                                            <?php
                                            foreach($employee_list as $employee):
                                            $wphrmEmployeeInfo = $this->WPHRMGetUserDatas( $employee->ID, 'wphrmEmployeeInfo' );
                                            //only the approving officer allowed
                                            if( (!(isset($wphrmEmployeeInfo['wphrm_employee_reporting_manager']) && is_array($wphrmEmployeeInfo['wphrm_employee_reporting_manager']) && in_array($current_user->ID, $wphrmEmployeeInfo['wphrm_employee_reporting_manager'])) && in_array('manageOptionsApproveLeaveApplications', $wphrmGetPagePermissions)) && !current_user_can('hr_manager') ) continue;

                                            if($current_user->ID == $employee->ID) continue;
                                            ?>
                                            <option value="<?php echo $employee->ID; ?>" <?php selected(isset($_GET['filter_employee']) ? $_GET['filter_employee'] : '', $employee->ID); ?> ><?php echo isset($wphrmEmployeeInfo['wphrm_employee_fname']) ? $wphrmEmployeeInfo['wphrm_employee_fname'] : $employee->display_name; ?> <?php echo isset($wphrmEmployeeInfo['wphrm_employee_lname']) ? $wphrmEmployeeInfo['wphrm_employee_lname'] : ''; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Filter List</button>
                                    <a type="reset" class="btn btn-primary" href="<?php echo admin_url('admin.php?page=wphrm-leaves-application'); ?>">Clear</a>
                                </form>
                            </div>
                            <?php endif; ?>
                                <table class="table table-striped table-bordered table-hover wphrmDataTableClass" id="wphrmDataTablex">
                                    <thead>
                                        <tr>
                                            <th><?php _e('S.No', 'wphrm'); ?></th>
                                            <th><?php _e('Emp. ID', 'wphrm'); ?></th>
                                            <th><?php _e('Name', 'wphrm'); ?></th>
                                            <th><?php _e('Leave Type', 'wphrm'); ?></th>
                                            <th><?php _e('Reason', 'wphrm'); ?></th>
                                            <th><?php _e('Leave Dates', 'wphrm'); ?></th>
                                            <th><?php _e('Availment', 'wphrm'); ?></th>
                                            <th><?php _e('Applied on', 'wphrm'); ?></th>
                                            <th><?php _e('Status', 'wphrm'); ?></th>
                                            <th><?php _e('Comment', 'wphrm'); ?></th>
                                            <!--<th><?php //_e('Actions', 'wphrm'); ?></th>-->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;

                                        $leave_application_where = "WHERE (`applicationStatus` != '' AND (`date` != '0000-00-00' ))";

                                        //filter condition here
                                         if(!empty($_GET['filter_leavetype'])){
                                             $leave_application_where .= " AND `leaveType` = '".esc_sql($_GET['filter_leavetype'])."' ";
                                         }
                                         if(!empty($_GET['filter_date_start'])){
                                             $leave_application_where .= " AND `date` >= '".date('Y-m-d', strtotime($_GET['filter_date_start']))."' ";
                                         }
                                         if(!empty($_GET['filter_date_end'])){
                                             $leave_application_where .= " AND `toDate` <= '".date('Y-m-d', strtotime($_GET['filter_date_end']))."' ";
                                         }
                                         if(!empty($_GET['filter_status'])){
                                             $leave_application_where .= " AND `applicationStatus` = '".esc_sql($_GET['filter_status'])."' ";
                                         }
                                         if(!empty($_GET['filter_employee'])){
                                             $leave_application_where .= " AND `employeeID` = '".esc_sql($_GET['filter_employee'])."' ";
                                         }

                                        $wphrm_leaveapplication = $wpdb->get_results("SELECT * FROM $this->WphrmLeaveApplicationTable $leave_application_where ORDER BY id DESC");


                                        if (!empty($wphrm_leaveapplication)) :
                                            foreach ($wphrm_leaveapplication as $key => $wphrm_leaveapplications) {
                                                $wphrmEmployeeInfo_load = get_user_meta($wphrm_leaveapplications->employeeID, 'wphrmEmployeeInfo', true);
                                                $wphrmEmployeeInfo = unserialize(base64_decode($wphrmEmployeeInfo_load));

                                                //only the approving officer allowed
                                                if( (!(isset($wphrmEmployeeInfo['wphrm_employee_reporting_manager']) && is_array($wphrmEmployeeInfo['wphrm_employee_reporting_manager']) && in_array($current_user->ID, $wphrmEmployeeInfo['wphrm_employee_reporting_manager'])) && in_array('manageOptionsApproveLeaveApplications', $wphrmGetPagePermissions)) && !current_user_can('hr_manager') ) continue;

                                                if($current_user->ID == $wphrm_leaveapplications->employeeID) continue;
                                                ?>
                                                <tr>
                                                    <td><?php echo esc_html($i); ?></td>
                                                    <td>
                                                        <?php
                                                        if (isset($wphrmEmployeeInfo['wphrm_employee_userid'])) : echo esc_html($wphrmEmployeeInfo['wphrm_employee_userid']);
                                                        endif;
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $leave_type = $this->get_leave_info( $wphrm_leaveapplications->leaveType );
                                                        $max_leave = $this->get_employee_max_leave( $wphrm_leaveapplications->employeeID, $leave_type->leave_rules, $leave_type);
                                                        $used_leave = $this->get_used_leave( $wphrm_leaveapplications->employeeID, $wphrm_leaveapplications->leaveType );
                                                        $remaining_leave = $used_leave.' / '.$max_leave;
                                                        ?>
                                                        <a class="btn" data-toggle="modal" href="#edit_static" onclick="applicationEdit(<?php ?><?php
                                                        if (isset($wphrm_leaveapplications->id)) : echo esc_js($wphrm_leaveapplications->id);
                                                        endif;
                                                        ?>, '<?php
                                                        if (isset($wphrm_leaveapplications->employeeID)) : echo esc_js($wphrm_leaveapplications->employeeID);
                                                        endif;
                                                        ?>', '<?php
                                                        if (isset($wphrmEmployeeInfo['wphrm_employee_fname'])) : echo esc_js($wphrmEmployeeInfo['wphrm_employee_fname']);
                                                        endif;
                                                        if (isset($wphrmEmployeeInfo['wphrm_employee_lname'])) : echo ' ' . esc_js($wphrmEmployeeInfo['wphrm_employee_lname']);
                                                        endif;
                                                        ?>', '<?php
                                                        if (isset($wphrm_leaveapplications->date)) : echo esc_js(date('d-m-Y', strtotime($wphrm_leaveapplications->date)));
                                                        endif;
                                                        ?>', '<?php
                                                        if (isset($wphrm_leaveapplications->toDate) && $wphrm_leaveapplications->toDate != '0000-00-00') : echo esc_js(date('d-m-Y', strtotime($wphrm_leaveapplications->toDate)));
                                                        endif;
                                                        ?>', '<?php
                                                        if (isset($wphrm_leaveapplications->leaveType)) :
                                                echo $wphrm_leaveapplications->leaveType.':'.$this->get_leave_info($wphrm_leaveapplications->leaveType, 'leaveType');
                                                        endif;
                                                        ?>', '<?php
                                                        if (isset($wphrm_leaveapplications->halfDayType)) : echo esc_js($wphrm_leaveapplications->halfDayType);
                                                        endif;
                                                        ?>', '<?php
                                                        if (isset($wphrm_leaveapplications->reason)) : echo esc_js($wphrm_leaveapplications->reason);
                                                        endif;
                                                        ?>', '<?php
                                                        if (isset($wphrm_leaveapplications->appliedOn)) : echo esc_js(date('d-m-Y', strtotime($wphrm_leaveapplications->appliedOn)));
                                                        endif;
                                                        ?>', '<?php
                                                        if (isset($wphrm_leaveapplications->applicationStatus)) : echo esc_js($wphrm_leaveapplications->applicationStatus);
                                                        endif;
                                                        ?>', '<?php echo esc_js($remaining_leave); ?>', '<?php
                                                        if (isset($wphrm_leaveapplications->medical_claim_amount)) : echo esc_js($wphrm_leaveapplications->medical_claim_amount);
                                                        endif;
                                                        ?>', '<?php
                                                        if ($wphrm_leaveapplications->due_date != "0000-00-00") : echo esc_js(date('d-m-Y', strtotime($wphrm_leaveapplications->due_date))); else : echo ''; 
                                                        endif;
                                                        ?>', '<?php
                                                        if (isset($wphrm_leaveapplications->maternity_option)) : echo esc_js($wphrm_leaveapplications->maternity_option); else : echo ''; 
                                                        endif;
                                                        ?>', '<?php
                                                        if ($wphrm_leaveapplications->child_bdate != "0000-00-00") : echo esc_js(date('d-m-Y', strtotime($wphrm_leaveapplications->child_bdate))); else : echo '';
                                                        endif;
                                                        ?>', '<?php
                                                        if ($wphrm_leaveapplications->work_date != "0000-00-00") : echo esc_js(date('d-m-Y', strtotime($wphrm_leaveapplications->work_date))); else : echo '';
                                                        endif;
                                                        ?>', '<?php
                                                        if ($wphrm_leaveapplications->outstation_date != "0000-00-00") : echo esc_js(date('d-m-Y', strtotime($wphrm_leaveapplications->outstation_date))); else : echo '';
                                                        endif;
                                                        ?>', '<?php
                                                        if (isset($wphrm_leaveapplications->assign_country)) : echo esc_js($wphrm_leaveapplications->assign_country);
                                                        endif;
                                                        ?>', '<?php
                                                        if (isset($wphrm_leaveapplications->examination_name)) : echo esc_js($wphrm_leaveapplications->examination_name);
                                                        endif;
                                                        ?>', '<?php
                                                        if ($wphrm_leaveapplications->examination_date != "0000-00-00") : echo esc_js(date('d-m-Y', strtotime($wphrm_leaveapplications->examination_date))); else : echo '';
                                                        endif;
                                                        ?>', '<?php
                                                        if (isset($wphrm_leaveapplications->examination_time)) : echo esc_js($wphrm_leaveapplications->examination_time);
                                                        endif;
                                                        ?>', '<?php
                                                        if (isset($wphrm_leaveapplications->id)) : echo esc_js(get_application_file_url($wphrm_leaveapplications->id));
                                                        endif;
                                                        ?>')" style="text-decoration:underline">
                                                            <?php
                                                            if (isset($wphrmEmployeeInfo['wphrm_employee_fname'])) : echo esc_html($wphrmEmployeeInfo['wphrm_employee_fname']);
                                                            endif;
                                                            if (isset($wphrmEmployeeInfo['wphrm_employee_lname'])) : echo ' ' . esc_html($wphrmEmployeeInfo['wphrm_employee_lname']);
                                                            endif;
                                                            ?>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if (isset($wphrm_leaveapplications->leaveType)) : echo esc_html($this->get_leave_info($wphrm_leaveapplications->leaveType, 'leaveType'));
                                                        endif;
                                                        ?>
                                                    </td>
                                                    <td class="default-width">
                                                        <?php
                                                        if (isset($wphrm_leaveapplications->reason)) : echo esc_html(esc_html($wphrm_leaveapplications->reason));
                                                        endif;
                                                        ?>
                                                    </td>
                                                    <td><?php
                                                        if (isset($wphrm_leaveapplications->date) && isset($wphrm_leaveapplications->toDate) && (strtotime($wphrm_leaveapplications->date)) == (strtotime($wphrm_leaveapplications->toDate))) {
                                                            if (isset($wphrm_leaveapplications->date) && $wphrm_leaveapplications->date != '0000-00-00') : echo esc_html(date('d-M-Y', strtotime($wphrm_leaveapplications->date)));
                                                            endif;
                                                        }else {
                                                            if (isset($wphrm_leaveapplications->date) && $wphrm_leaveapplications->date != '0000-00-00') : echo esc_html(date('d-M-Y', strtotime($wphrm_leaveapplications->date)));
                                                            endif;
                                                            ?>
                                                            <?php
                                                            if (isset($wphrm_leaveapplications->toDate) && $wphrm_leaveapplications->toDate != '0000-00-00') :
                                                            echo ' To  ' . esc_html(date('d-M-Y', strtotime($wphrm_leaveapplications->toDate)));
                                                            endif;
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if (isset($wphrm_leaveapplications->halfDayType)){
                                                            $halfday_types = explode(',', $wphrm_leaveapplications->halfDayType);
                                                            if( ($wphrm_leaveapplications->date == $wphrm_leaveapplications->toDate || $wphrm_leaveapplications->toDate == '0000-00-00' ) && ($halfday_types[0] == 1 || $halfday_types[1] == 1) ){
                                                                echo 'Half Day';
                                                            }elseif( ($wphrm_leaveapplications->date == $wphrm_leaveapplications->toDate || $wphrm_leaveapplications->toDate == '0000-00-00' ) && ($halfday_types[0] == 0 || $halfday_types[1] == 0) ){
                                                                echo 'Full Day';
                                                            }else{
                                                                if($halfday_types[0] == 0 || $halfday_types[1] == 0) { 
                                                                    echo 'Full Day';
                                                                } else {
                                                                    echo 'Half Day';
                                                                }
                                                                /*if($halfday_types[0] == 0){
                                                                    echo '<div ><span title="First Day" ></span>First Day: Full<div>';
                                                                }else{
                                                                    echo '<div ><span title="First Day" ></span>First Day: Half<div>';
                                                                }
                                                                if($halfday_types[1] == 0){
                                                                    echo '<div ><span title="Last Day" ></span>Last Day: Full<div>';
                                                                }else{
                                                                    echo '<div ><span title="Last Day" ></span>Last Day: Half<div>';
                                                                }*/
                                                            }
                                                        }

                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if (isset($wphrm_leaveapplications->appliedOn)) : echo esc_html(date('d-M-Y', strtotime($wphrm_leaveapplications->appliedOn)));
                                                        endif;
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php if (isset($wphrm_leaveapplications->applicationStatus) && $wphrm_leaveapplications->applicationStatus == 'pending') { ?> <span class="leave-status label label-warning"><?php _e('Pending', 'wphrm'); ?></span> <?php } else if (isset($wphrm_leaveapplications->applicationStatus) && $wphrm_leaveapplications->applicationStatus == 'approved') { ?>
                                                            <span class="leave-status label label-success"><?php _e('Approved', 'wphrm'); ?></span>
                                                        <?php } else if (isset($wphrm_leaveapplications->applicationStatus) && $wphrm_leaveapplications->applicationStatus == 'rejected') { ?>
                                                            <span class="leave-status label label-danger"><?php _e('Rejected', 'wphrm'); ?></span>
                                                        <?php } ?>
                                                    </td>
                                                    <!--<td class="default-width">
                                                        <a class="btn purple" data-toggle="modal" href="#edit_static" onclick="applicationEdit(<?php ?><?php
                                                        if (isset($wphrm_leaveapplications->id)) : echo esc_js($wphrm_leaveapplications->id);
                                                        endif;
                                                        ?>, '<?php
                                                        if (isset($wphrm_leaveapplications->employeeID)) : echo esc_js($wphrm_leaveapplications->employeeID);
                                                        endif;
                                                        ?>', '<?php
                                                        if (isset($wphrmEmployeeInfo['wphrm_employee_fname'])) : echo esc_js($wphrmEmployeeInfo['wphrm_employee_fname']);
                                                        endif;
                                                        if (isset($wphrmEmployeeInfo['wphrm_employee_lname'])) : echo ' ' . esc_js($wphrmEmployeeInfo['wphrm_employee_lname']);
                                                        endif;
                                                        ?>', '<?php
                                                        if (isset($wphrm_leaveapplications->date)) : echo esc_js(date('d-m-Y', strtotime($wphrm_leaveapplications->date)));
                                                        endif;
                                                        ?>', '<?php
                                                        if (isset($wphrm_leaveapplications->toDate) && $wphrm_leaveapplications->toDate != '0000-00-00') : echo esc_js(date('d-m-Y', strtotime($wphrm_leaveapplications->toDate)));
                                                        endif;
                                                        ?>', '<?php
                                                        if (isset($wphrm_leaveapplications->leaveType)) :
                                                echo $wphrm_leaveapplications->leaveType.':'.$this->get_leave_info($wphrm_leaveapplications->leaveType, 'leaveType');
                                                        endif;
                                                        ?>', '<?php
                                                        if (isset($wphrm_leaveapplications->halfDayType)) : echo esc_js($wphrm_leaveapplications->halfDayType);
                                                        endif;
                                                        ?>', '<?php
                                                        if (isset($wphrm_leaveapplications->reason)) : echo esc_js($wphrm_leaveapplications->reason);
                                                        endif;
                                                        ?>', '<?php
                                                        if (isset($wphrm_leaveapplications->appliedOn)) : echo esc_js(date('d-m-Y', strtotime($wphrm_leaveapplications->appliedOn)));
                                                        endif;
                                                        ?>', '<?php
                                                        if (isset($wphrm_leaveapplications->applicationStatus)) : echo esc_js($wphrm_leaveapplications->applicationStatus);
                                                        endif;
                                                        ?>', '<?php
                                                        if (isset($wphrm_leaveapplications->id)) : echo esc_js(get_application_file_url($wphrm_leaveapplications->id));
                                                        endif;
                                                        ?>')"><i class="fa fa-edit"></i><?php _e('View/Edit', 'wphrm'); ?></a>
                                                        <a class="btn red" href="javascript:;" onclick="WPHRMCustomDelete(<?php
                                                        if (isset($wphrm_leaveapplications->id)) : echo esc_js($wphrm_leaveapplications->id);
                                                        endif;
                                                        ?>, '<?php echo esc_js($this->WphrmLeaveApplicationTable) ?>', 'id')"><i class="fa fa-trash"></i><?php _e('Delete', 'wphrm'); ?> </a>
                                                    </td>-->
                                                    <td class="default-width">
                                                        <?php
                                                        if (isset($wphrm_leaveapplications->adminComments) && $wphrm_leaveapplications->adminComments != '') : echo esc_html(esc_html($wphrm_leaveapplications->adminComments));
                                                        else :
                                                            echo '-';
                                                        endif;
                                                        ?>
                                                    </td>
                                                </tr>
                                                <?php
                                                $i++;
                                            }
                                        else :
                                            ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                        </div>
                    </div>
                    <!-- protlet end -->
                </div>
                <div id="annual_leave_management" class="tab-pane fade in <?php echo $annual?>">
                    <div class="portlet box blue calendar">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-list"></i><?php _e('List of Annual Leave Entitlement', 'wphrm'); ?>
                            </div>
                        </div>
                        <div class="portlet-body">

                            <?php if(in_array('manageOptionsNotice', $wphrmGetPagePermissions)) { ?>

                                <table class="table table-striped table-bordered table-hover" id="wphrmDataTable">
                                    <thead>
                                        <tr> <th><?php _e('Years of Service', 'wphrm'); ?></th>
                                            <th><?php _e('NE-III (Staff)', 'wphrm'); ?></th>
                                            <th><?php _e('NE-II to E-I (Supervisor)', 'wphrm'); ?></th>
                                            <th><?php _e('M-III to M-I (Manager)', 'wphrm'); ?></th>
                                            <th><?php _e('SM-III to SM-I (Senior Manager)', 'wphrm'); ?></th>
                                            <th><?php _e('Actions', 'wphrm'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $wphrmLE = $wpdb->get_results("SELECT * FROM  $this->WphrmEmployeeLevelTable ORDER BY years_of_service ASC");
                                        if(!empty($wphrmLE)) {
                                            foreach ($wphrmLE as $key => $le) { ?>
                                                <tr>
                                                    <td>
                                                        <?php 
                                                           if( $le->years_of_service == 1 ) {
                                                               echo $le->years_of_service.'st Year';
                                                           } elseif( $le->years_of_service == 2 ) {
                                                               echo $le->years_of_service.'nd Year';
                                                           } elseif( $le->years_of_service == 3 ) {
                                                               echo $le->years_of_service.'rd Year';
                                                           } else {
                                                               echo $le->years_of_service.'th Year';
                                                           }
                                                        ?>
                                                    </td>
                                                    <td><?php echo $le->staff; ?></td>
                                                    <td><?php echo $le->supervisor; ?></td>
                                                    <td><?php echo $le->manager; ?></td>
                                                    <td><?php echo $le->senior_manager; ?></td>
                                                    <td>
                                                        <a class="btn purple" data-toggle="modal" title="Edit" href="#edit_leave_entitlement" onclick="leaveentitlementEdit(<?php if (isset($le->id)): echo esc_js($le->id); endif; ?>, '<?php echo $le->years_of_service ?>', '<?php echo $le->staff ?>', '<?php echo $le->supervisor ?>', '<?php echo $le->manager ?>', '<?php echo $le->senior_manager ?>')"> <i class="fa fa-edit"></i></a>
                                                        <a class="btn red" href="javascript:;" title="Delete item" onclick="WPHRMCustomDelete(<?php if (isset($le->id)): echo esc_js($le->id); endif; ?>, '<?php echo esc_js($this->WphrmEmployeeLevelTable) ?>', 'id')">
                                                       <i class="fa fa-trash"></i></a>
                                                    </td>
                                                </tr>
                                            <?php $i++; }
                                            } ?>
                                    </tbody>
                                </table>
                            <?php } ?>
                        </div>
                    </div>
                </div><!--end of tab content of employee level-->
            </div>  
            <!-- tab body end -->
            <?php endif; ?>
            <!--end Admin-->


            <?php  if(in_array('manageOptionsLeaveApplicationsView', $wphrmGetPagePermissions)): ?>
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#leave_management">Leave Requests</a></li>
                <li><a href="<?php echo site_url("/wp-admin/admin.php?page=wphrm-view-attendance") ?>">Attendance Management</a></li>
            </ul>
            
            <div class="tab-content">
                <div id="leave_management" class="tab-pane fade in active">
                    
                    <div class="portlet box blue calendar">
                        <div class="portlet-title">
                            <div class="caption"> <i class="fa fa-list"></i><?php _e('Your Leave Applications ', 'wphrm'); ?></div>
                        </div>
                        <div class="portlet-body">
                                <table class="table table-striped table-bordered table-hover wphrmDataTableClass" id="wphrmDataTablex">
                                    <thead>
                                        <tr>
                                            <th><?php _e('Name', 'wphrm'); ?></th>
                                            <th><?php _e('Date', 'wphrm'); ?></th>
                                            <th><?php _e('Availment', 'wphrm'); ?></th>
                                            <th><?php _e('Leave Type', 'wphrm'); ?></th>
                                            <th><?php _e('Reason', 'wphrm'); ?></th>
                                            <th><?php _e('Applied on', 'wphrm'); ?></th>
                                            <th><?php _e('Status', 'wphrm'); ?></th>
                                            <th><?php _e('Comment', 'wphrm'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $wphrm_leaveapplication = $wpdb->get_results("SELECT * FROM $this->WphrmLeaveApplicationTable WHERE `applicationStatus`!= '' AND `employeeID` = '$wphrmCurrentuserId'  AND `date` != '0000-00-00' ORDER BY id DESC");
                                        if (!empty($wphrm_leaveapplication)) :
                                            foreach ($wphrm_leaveapplication as $key => $wphrm_leaveapplications) {
                                                $wphrmEmployeeInfo_load = get_user_meta($wphrmCurrentuserId, 'wphrmEmployeeInfo', true);
                                                $wphrmEmployeeInfo = unserialize(base64_decode($wphrmEmployeeInfo_load));
                                                ?>
                                                <tr>
                                                    <td><?php
                                                        if (isset($wphrmEmployeeInfo['wphrm_employee_fname'])) : echo esc_html($wphrmEmployeeInfo['wphrm_employee_fname']);
                                                        endif;
                                                        if (isset($wphrmEmployeeInfo['wphrm_employee_lname'])) : echo ' ' . esc_html($wphrmEmployeeInfo['wphrm_employee_lname']);
                                                        endif;
                                                        ?>
                                                    </td>
                                                    <td><?php
                                                        if (isset($wphrm_leaveapplications->date) && isset($wphrm_leaveapplications->toDate) && (strtotime($wphrm_leaveapplications->date)) == (strtotime($wphrm_leaveapplications->toDate))) {
                                                            if (isset($wphrm_leaveapplications->date) && $wphrm_leaveapplications->date != '0000-00-00') :
                                                            echo esc_html(date('d-M-Y', strtotime($wphrm_leaveapplications->date)));
                                                            endif;
                                                        }elseif (isset($wphrm_leaveapplications->date) && isset($wphrm_leaveapplications->toDate) && (strtotime($wphrm_leaveapplications->date)) == (strtotime($wphrm_leaveapplications->toDate))) {
                                                            if (isset($wphrm_leaveapplications->date) && $wphrm_leaveapplications->date == '0000-00-00') :
                                                            echo esc_html(date('d-M-Y', strtotime($wphrm_leaveapplications->date)));
                                                            endif;
                                                        }else {
                                                            if (isset($wphrm_leaveapplications->date) && $wphrm_leaveapplications->date != '0000-00-00' ) :
                                                            echo esc_html(date('d-M-Y', strtotime($wphrm_leaveapplications->date)));
                                                            endif;
                                                            ?>
                                                            <?php
                                                            if (isset($wphrm_leaveapplications->toDate) && $wphrm_leaveapplications->toDate != '0000-00-00') :
                                                            echo ' To  ' . esc_html(date('d-M-Y', strtotime($wphrm_leaveapplications->toDate)));
                                                            endif;
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if (isset($wphrm_leaveapplications->halfDayType)){
                                                            $halfday_types = explode(',', $wphrm_leaveapplications->halfDayType);
                                                            if( ($wphrm_leaveapplications->date == $wphrm_leaveapplications->toDate || $wphrm_leaveapplications->toDate == '0000-00-00' ) && ($halfday_types[0] == 1 || $halfday_types[1] == 1) ){
                                                                echo 'Half';
                                                            }elseif( ($wphrm_leaveapplications->date == $wphrm_leaveapplications->toDate || $wphrm_leaveapplications->toDate == '0000-00-00' ) && ($halfday_types[0] == 0 || $halfday_types[1] == 0) ){
                                                                echo 'Full';
                                                            }else{
                                                                echo 'Full';
                                                               /* if($halfday_types[0] == 0){
                                                                    echo '<div ><span title="First Day" ><b>?</b></span>FD: Full<div>';
                                                                }else{
                                                                    echo '<div ><span title="First Day" ><b>?</b></span>FD: Half<div>';
                                                                }
                                                                if($halfday_types[1] == 0){
                                                                    echo '<div ><span title="Last Day" ><b>?</b></span>LD: Full<div>';
                                                                }else{
                                                                    echo '<div ><span title="Last Day" ><b>?</b></span>LD: Half<div>';
                                                                }*/
                                                            }
                                                        }

                                                        ?>
                                                    </td>
                                                    <td><?php
                                                        if (isset($wphrm_leaveapplications->leaveType)) : echo esc_html($this->get_leave_info($wphrm_leaveapplications->leaveType, 'leaveType'));
                                                        endif;
                                                        ?>
                                                    </td>
                                                    <td class="default-width"><?php
                                                        if (isset($wphrm_leaveapplications->reason)) : echo esc_html($wphrm_leaveapplications->reason);
                                                        endif;
                                                        ?></td>
                                                    <td ><?php
                                                        if (isset($wphrm_leaveapplications->appliedOn)) : echo esc_html(date('d-M-Y', strtotime($wphrm_leaveapplications->appliedOn)));
                                                        endif;
                                                        ?>
                                                    </td>
                                                    <td><?php if (isset($wphrm_leaveapplications->applicationStatus) && $wphrm_leaveapplications->applicationStatus == 'pending') { ?>
                                                            <span data-id="<?php echo $wphrm_leaveapplications->id; ?>" class="leave-status label label-warning"><?php _e('Pending', 'wphrm'); ?></span> <?php } else if (isset($wphrm_leaveapplications->applicationStatus) && $wphrm_leaveapplications->applicationStatus == 'approved') { ?>
                                                            <span data-id="<?php echo $wphrm_leaveapplications->id; ?>" class="leave-status label label-success"><?php _e('Approved', 'wphrm'); ?></span>
                                                        <?php } else if (isset($wphrm_leaveapplications->applicationStatus) && $wphrm_leaveapplications->applicationStatus == 'rejected') { ?>
                                                            <span data-id="<?php echo $wphrm_leaveapplications->id; ?>" class="leave-status label label-danger"><?php _e('Rejected', 'wphrm'); ?></span>
                                                        <?php } ?>
                                                    </td>
                                                    <!--<td class="default-width">
                                                        <?php
                                                        if (isset($wphrm_leaveapplications->applicationStatus) && ($wphrm_leaveapplications->applicationStatus == 'approved' or $wphrm_leaveapplications->applicationStatus == 'rejected')) {

                                                        } else {
                                                            ?>
                                                            <a class="btn purple" data-toggle="modal" href="#add_static" onclick="user_staticEdit(<?php
                                                            if (isset($wphrm_leaveapplications->id)) : echo esc_js($wphrm_leaveapplications->id);
                                                            endif;
                                                            ?>, '<?php
                                                            if (isset($wphrm_leaveapplications->date)) : echo esc_js(date('d-m-Y', strtotime($wphrm_leaveapplications->date)));
                                                            endif;
                                                            ?>', '<?php
                                                            if (isset($wphrm_leaveapplications->toDate) && $wphrm_leaveapplications->toDate != '0000-00-00') : echo esc_js(date('d-m-Y', strtotime($wphrm_leaveapplications->toDate)));
                                                            endif;
                                                            ?>', '<?php
                                                            if (isset($wphrm_leaveapplications->leaveType)) : echo esc_js($wphrm_leaveapplications->leaveType);
                                                            endif;
                                                            ?>', '<?php
                                                            if (isset($wphrm_leaveapplications->halfDayType)) : echo esc_js($wphrm_leaveapplications->halfDayType);
                                                            endif;
                                                            ?>', '<?php
                                                            if (isset($wphrm_leaveapplications->reason)) : echo esc_js($wphrm_leaveapplications->reason);
                                                            endif;
                                                            ?>', '<?php
                                                            if (isset($wphrm_leaveapplications->id)) : echo esc_js(get_application_file_name($wphrm_leaveapplications->id));
                                                            endif;
                                                            ?>')">
                                                                <i class="fa fa-edit"></i><?php _e('View/Edit', 'wphrm'); ?>
                                                            </a>
                                                            <a class="btn red" href="javascript:;" onclick="WPHRMCustomDelete(<?php
                                                            if (isset($wphrm_leaveapplications->id)) : echo esc_js($wphrm_leaveapplications->id);
                                                            endif;
                                                            ?>, '<?php echo esc_js($this->WphrmLeaveApplicationTable) ?>', 'id')"><i class="fa fa-trash"></i><?php _e('Delete', 'wphrm'); ?> </a>
                                                           <?php } ?>
                                                    </td>-->

                                                    <td  class="default-width">
                                                        <?php
                                                        if (isset($wphrm_leaveapplications->adminComments)) : echo esc_html(esc_html($wphrm_leaveapplications->adminComments));
                                                        endif;
                                                        ?>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        else :
                                            ?>

                                        <?php endif; ?>
                                    </tbody>
                                </table>
                        </div>
                    </div>
                    <!-- protlet end -->
                </div>
                
                <div class="leave_applications">
                    <div class="portlet box blue calendar col-md-4 col-sm-6 col-xs-12">
                        <div class="portlet-title">
                            <div class="caption"> <i class="fa fa-list"></i><?php _e('Leave Balance', 'wphrm'); ?></div>
                        </div>
                        <div class="portlet-body">
                               <?php
                                $employee_id = get_current_user_id();
                                $attendance_date = esc_sql(date('Y-m-d')); // esc
                                $todaydate = esc_sql('0'); // esc
                                $employeeAttendanceCount = esc_sql('0'); // esc
                                $currentMonth = date('m'); // esc

                                $wphrmEmployeeInfo = $this->WPHRMGetUserDatas($employee_id, 'wphrmEmployeeInfo');

                                if (isset($wphrmEmployeeInfo['wphrm_employee_status']) && $wphrmEmployeeInfo['wphrm_employee_status'] == 'Active') {
                                    $getAttendancebyId = $wpdb->get_row("select * from $this->WphrmAttendanceTable where `date` = '" . $attendance_date . "' and `employeeID` ='" . $employee_id . "'");
                                    $lastAbsent = $wpdb->get_results("select * from $this->WphrmAttendanceTable where  `employeeID` ='" . $employee_id . "' and `status` = 'absent' and `date` <= '$attendance_date' order by id desc");
                                    if (!empty($lastAbsent)) {
                                        if ($lastAbsent[0]->date == $attendance_date) {
                                            $todaydate = 'today';
                                        } else {
                                            $now = time(); // or your date as well
                                            $yourDate = strtotime($lastAbsent[0]->date);
                                            $datediff = $now - $yourDate;
                                            $beforeday = floor($datediff / (60 * 60 * 24));
                                            $todaydate = 'Before : ' . $beforeday . ' Day';
                                        }
                                    } else {
                                        $todaydate = '0';
                                    }
                                    ?>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="alert alert-info">

                                                <?php
                                                if (isset($wphrmEmployeeInfo['wphrm_employee_joining_date'])) {
                                                    $wphrmEmployeeJoiningDate = $wphrmEmployeeInfo['wphrm_employee_joining_date'];
                                                }
                                                $wphrmEmployeeJoiningDate = new DateTime($wphrmEmployeeJoiningDate);
                                                $today = new DateTime();
                                                $interval = $today->diff($wphrmEmployeeJoiningDate);
                                                $wphrmEmployeeJoiningToCurrentTotalYear = ((int) $interval->format('%y years') + 1);
                                                $curQuarter = ceil($currentMonth / 3);
                                                $leavesTypes = $wpdb->get_results("SELECT * FROM $this->WphrmLeaveTypeTable ORDER BY leaveType ASC");
                                                foreach ($leavesTypes as $leavesType) {
                                                    if( $this->employee_allowed_leaves($employee_id) ) {
                                                        if(!in_array($leavesType->id, $this->employee_allowed_leaves($employee_id))) continue;
                                                    } else {
                                                        continue;
                                                    }

                                                    $totalNoOfLeave = 0;
                                                    if ($leavesType->period == 'Monthly') {
                                                        $totalNoOfLeave = intval($leavesType->numberOfLeave * $currentMonth);
                                                    } else if ($leavesType->period == 'Quarterly') {
                                                        $totalNoOfLeave = intval($leavesType->numberOfLeave * $curQuarter);
                                                    } else if ($leavesType->period == 'Yearly') {
                                                        $totalNoOfLeave = intval($leavesType->numberOfLeave * $wphrmEmployeeJoiningToCurrentTotalYear);
                                                    }

                                                    //if( in_array($leavesType->leave_rules, array_keys($this->get_leave_rules()) ) ){
                                                        $totalNoOfLeave = $this->get_employee_max_leave($employee_id, $leavesType->leave_rules, $leavesType);
                                                    //}

                                                    $employeeLeaves = $wpdb->get_row("SELECT COUNT(id) AS leaveCounter FROM $this->WphrmAttendanceTable WHERE `status`='absent' AND `employeeID` ='" . $employee_id . "' AND `leaveType`='$leavesType->leaveType' AND `date` <= '$attendance_date' AND `applicationStatus`='approved'");
                                                    $employeeLeavesHalfday = $wpdb->get_row("SELECT COUNT(id) AS halfdayCounter FROM $this->WphrmAttendanceTable WHERE `halfDayType`='halfday' AND `employeeID` ='" . $employee_id . "' AND `leaveType`='$leavesType->leaveType' AND `date` <= '$attendance_date' AND `applicationStatus`='approved'");

                                                    $halfdayCounter = ($employeeLeavesHalfday->halfdayCounter / 2);
                                                    $leaveTotal = $employeeLeaves->leaveCounter + $halfdayCounter;

                                                    $leaveTotal = $this->get_used_leave( $employee_id, $leavesType->id );
                                                    //$leaveTotal = apply_filters('employee_attendance_after_calculate_leave_total', $leaveTotal, $leavesType, $employee_id);

                                                    $leaveRemaining = ($totalNoOfLeave - $leaveTotal);
                                                    if($leaveRemaining >0){
                                                        $leaveRemaining = $leaveRemaining;
                                                    }else{
                                                        $leaveRemaining=0;
                                                    }
                                                    if($leavesType->id == 35 || $leavesType->leaveType == 'Annual Leave' || $leavesType->leaveType == 'annual leave' || $leavesType->leaveType == 'Annual leave') { 

                                                        /*$emp = $this->get_user_complete_info( get_current_user_id() );
                                                        $leveltype = $emp->basic_info->wphrm_employee_level;
                                                        $leave_entitlement_count = $wpdb->get_var("SELECT COUNT(*) FROM  $this->WphrmEmployeeLevelTable WHERE years_of_service = $wphrmEmployeeJoiningToCurrentTotalYear");
                                                        if( $leave_entitlement_count > 0 ) {
                                                            $leave_entitlement = $wpdb->get_row("SELECT * FROM  $this->WphrmEmployeeLevelTable WHERE years_of_service = $wphrmEmployeeJoiningToCurrentTotalYear");
                                                        } else {
                                                            $leave_entitlement = $wpdb->get_row("SELECT * FROM  $this->WphrmEmployeeLevelTable ORDER BY id DESC LIMIT 1");
                                                        }
                                                        if( $leveltype == 'senior_manager' ) {
                                                            $max_leave = $leave_entitlement->senior_manager;
                                                        } elseif( $leveltype == 'manager' ) {
                                                            $max_leave = $leave_entitlement->manager;
                                                        } elseif( $leveltype == 'supervisor' ) {
                                                            $max_leave = $leave_entitlement->supervisor;
                                                        } elseif( $leveltype == 'staff' ) {
                                                            $max_leave = $leave_entitlement->staff;
                                                        }*/
                                                        
                                                        $year = date('Y');
                                                        $annual_leave_history = isset($wphrmEmployeeInfo['wphrm_employee_leave_carried']) ? $wphrmEmployeeInfo['wphrm_employee_leave_carried'] : false;
                                                        if(
                                                            isset($annual_leave_history[$year-1]) && isset($annual_leave_history[$year-1]['can_carry_over'])
                                                            && filter_var($annual_leave_history[$year-1]['can_carry_over'], FILTER_VALIDATE_BOOLEAN)
                                                            && !empty($annual_leave_history[$year-1]['count']) && $annual_leave_history[$year-1]['expiry'] > date('m/d/Y')
                                                        ){
                                                            $carried_leave = $annual_leave_history[$year-1]['count'];
                                                        }
                                                        $max_leave = (int)$this->get_employee_total_leave( $employee_id, $leavesType->id);
                                                        $remaining = (int)$max_leave - (int)$leaveTotal;
                                                        echo esc_html($leavesType->leaveType) . ' :';
                                                        echo '<div class="leave-details-holder">';
                                                            echo '<span>Total: <strong>'.esc_html($max_leave).'</strong> day(s)</span>';
                                                            echo '<span>Used: <strong>'.esc_html($leaveTotal).'</strong> (MTD - Month to Date)</span>';
                                                            echo '<span>Balance: <strong>'.esc_html($remaining).'</strong> ('.($max_leave - $carried_leave).' - '.$year.')</span>';
                                                            
                                                            if( isset($annual_leave_history[$year-1]) && isset($annual_leave_history[$year-1]['can_carry_over']) && filter_var($annual_leave_history[$year-1]['can_carry_over'], FILTER_VALIDATE_BOOLEAN) && !empty($annual_leave_history[$year-1]['count']) && $annual_leave_history[$year-1]['expiry'] > date('m/d/Y') ) {
                                                                    echo '<span>Bring forward <strong>'.esc_html($annual_leave_history[$year-1]['count']).'</strong> day(s) from ('.($year-1).'), expiry '.date( 'dS M Y', strtotime($annual_leave_history[$year-1]['expiry']) ).'.</span>';
                                                                }
                                                            
                                                        echo '</div>';
                                                } elseif( $leavesType->id == 45 || $leavesType->id == 48 || $leavesType->id == 49 || $leavesType->id == 50 ) {
                                                        echo esc_html($leavesType->leaveType) . ' :';
                                                        global $wphrm;
                                                        $ytoday = date('Y') - 1;
                                                        $wphrmEmployeeInfo = $wphrm->WPHRMGetUserDatas($employee_id, 'wphrmEmployeeInfo');
                                                        $wphrm_employee_maternity_carried = isset($wphrmEmployeeInfo['wphrm_employee_maternity_carried']) ? $wphrmEmployeeInfo['wphrm_employee_maternity_carried'] : array();
                                                        $expiry = strtotime( $wphrm_employee_maternity_carried[$ytoday]['expiry'] );
                                                        $date_today = strtotime( date( 'm/d/Y' ) ); 

                                                        if( $expiry < $date_today ) {
                                                            $max_leave = $wphrm_employee_maternity_carried[$ytoday]['count'] + $totalNoOfLeave;
                                                        } else {
                                                            $max_leave = $totalNoOfLeave;
                                                        }
                                                        $m_remaining = (int)$max_leave - (int)$leaveTotal;
                                                ?>
                                                        <strong style="cursor: pointer;" title="Total : <?php echo esc_attr($max_leave).' , '; ?> Taken : <?php echo esc_attr($leaveTotal).' , '; ?> Remaining : <?php echo esc_attr($m_remaining); ?>"><?php echo esc_html($leaveTotal) . '/' . $max_leave . '<br>'; ?></strong>
                                                    <?php
                                                    } elseif( $leavesType->id == 39 || $leavesType->id == 41 || $leavesType->id == 42 || $leavesType->id == 43 ) {
                                                        echo esc_html($leavesType->leaveType) . ' :';
                                                        if( $leavesType->id == 39 ) {
                                                            $leaveTotal = $this->get_used_leave($employee_id, 39) + $this->get_used_leave($employee_id, 37) + $this->get_used_leave($employee_id, 40);
                                                        } elseif( $leavesType->id == 41 ) {
                                                            $leaveTotal = $this->get_used_leave($employee_id, 41) + $this->get_used_leave($employee_id, 63) + $this->get_used_leave($employee_id, 40);
                                                        } elseif( $leavesType->id == 42 ) {
                                                            $leaveTotal = $this->get_used_leave($employee_id, 42) + $this->get_used_leave($employee_id, 38) + $this->get_used_leave($employee_id, 40);
                                                        } elseif( $leavesType->id == 43 ) {
                                                            $leaveTotal = $this->get_used_leave($employee_id, 43) + $this->get_used_leave($employee_id, 64) + $this->get_used_leave($employee_id, 40);
                                                        } else {
                                                            $leaveTotal = $this->get_used_leave($employee_id, $leavesType->id);
                                                        }
                                                        
                                                        $m_remaining = (int)$totalNoOfLeave - (int)$leaveTotal;
                                                ?>
                                                        <strong style="cursor: pointer;" title="Total : <?php echo esc_attr($totalNoOfLeave).' , '; ?> Taken : <?php echo esc_attr($leaveTotal).' , '; ?> Remaining : <?php echo esc_attr($m_remaining); ?>"><?php echo esc_html($leaveTotal) . '/' . $totalNoOfLeave . '<br>'; ?></strong>
                                                    <?php
                                                    } else {
                                                        echo esc_html($leavesType->leaveType) . ' :';
                                                         if ($totalNoOfLeave >= $leaveTotal) { ?>
                                                <strong style="cursor: pointer;" title="Total : <?php echo esc_attr($totalNoOfLeave).' , '; ?> Taken : <?php echo esc_attr($leaveTotal).' , '; ?> Remaining : <?php echo esc_attr($leaveRemaining); ?>"><?php echo esc_html($leaveTotal) . '/' . $totalNoOfLeave . '<br>'; ?></strong>
                                                            <?php } else { ?>
                                                               <strong style="cursor: pointer;" title="Total : <?php echo esc_attr($totalNoOfLeave).' , '; ?> Taken : <?php echo esc_attr($leaveTotal).' , '; ?> Remaining : <?php echo esc_attr($leaveRemaining); ?>"><?php echo esc_html($leaveTotal) . '/' . $totalNoOfLeave . '<br>'; ?></strong>
                                                            <?php } ?>
                                                        </tr>
                                                    <?php }
                                                } ?>

                                                <div class="medical-balance-holder">
                                                    <?php 
                                                        global $wphrm;
                                                        $employee_allowed_leaves = $this->employee_allowed_leaves($employee_id);
                                                        $wphrm_leavetypes = $wpdb->get_results("SELECT * FROM  $this->WphrmLeaveTypeTable ORDER BY leaveType ASC");
                                                        foreach ($wphrm_leavetypes as $key => $wphrm_leavetype) {
                                                            if( !in_array( $wphrm_leavetype->id, $employee_allowed_leaves ) ) continue;
                                                            if( $wphrm_leavetype->id == 37 || $wphrm_leavetype->id == 38 || $wphrm_leavetype->id == 39 || $wphrm_leavetype->id == 41 || $wphrm_leavetype->id == 42 || $wphrm_leavetype->id == 43 || $wphrm_leavetype->id == 63 || $wphrm_leavetype->id == 64 ) {
                                                                $leaveRules =  $wpdb->get_row("SELECT * FROM $this->WphrmLeaveRulesTable WHERE id = $wphrm_leavetype->leave_rules ");
                                                                if( $leaveRules ) {
                                                                    $reimbursement = $leaveRules->medical_claim_limit;
                                                                }
                                                            }
                                                            if( $wphrm_leavetype->id == 40 || $wphrm_leavetype->id == 71 ) {
                                                                $leaveRules =  $wpdb->get_row("SELECT * FROM $this->WphrmLeaveRulesTable WHERE id = $wphrm_leavetype->leave_rules ");
                                                                if( $leaveRules ) {
                                                                    $elderly = $leaveRules->elderly_screening_limit;
                                                                }
                                                            }
                                                        }

                                                        $wphrmEmpInfo = $wphrm->WPHRMGetUserDatas($employee_id, 'wphrmEmployeeInfo');
                                                        $wphrm_claimed_medical = isset($wphrmEmpInfo['wphrm_employee_medical_reimbursement']) ? $wphrmEmpInfo['wphrm_employee_medical_reimbursement'] : array();
                                                        $yearToday = date('Y');

                                                        $claimed = 0;
                                                        $year_claimed = $wpdb->get_results( "SELECT * FROM $this->WphrmLeaveApplicationTable WHERE employeeID = $employee_id AND leaveType = 37 OR  employeeID = $employee_id AND leaveType = 38 OR  employeeID = $employee_id AND leaveType = 39 OR  employeeID = $employee_id AND leaveType = 41 OR  employeeID = $employee_id AND leaveType = 42 OR  employeeID = $employee_id AND leaveType = 43 OR  employeeID = $employee_id AND leaveType = 63 OR  employeeID = $employee_id AND leaveType = 64" );
                                                        foreach( $year_claimed as $yclaimed ) {
                                                            $claimed = $claimed + $yclaimed->medical_claim_amount;
                                                        }
                                                        $total_claimed = ( $claimed + $wphrm_claimed_medical[$yearToday]['amount'] );
                                                        $total_reimbursement = ((int)$reimbursement + (int)$elderly);
                                                        echo '<br><span>Medical Claim: <strong>$'.$reimbursement.'</strong></span>';
                                                        if( $elderly > 0 ) {
                                                            echo '<br><span>Annual Checkup: <strong>$'.$elderly.'</strong></span>';
                                                        }
                                                        echo '<br><span>Medical Balance: <strong>$'.($total_reimbursement - $total_claimed).'</strong></span>';
                                                        echo '<br><span>Used: <strong>$'.$total_claimed.'</strong></span>';
                                                        echo '<br><span>Total: <strong>$'.$total_reimbursement.'</strong></span>';
                                                    ?>
                                                </div>


                                            </div> </div></div>
                                    <?php
                                }
                                ?>
                        </div>
                    </div>
                    <!-- protlet end -->
                </div>
            </div>  
            <!-- tab body end -->
            <?php endif; ?>


        </div>
    </div>
</div>
<script>
    /*jQuery(function (argument) {
        jQuery('[type="checkbox"]').bootstrapSwitch();
    });*/
</script>
<!-- END PAGE CONTENT-->

