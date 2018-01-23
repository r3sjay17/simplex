<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $current_user, $wpdb;
$wphrmUserRole = implode(',', $current_user->roles);
$readonly_class = '';
$readonly = '';
$edit_mode = false;
$wphrmMessagesNoticeBordDelete = $this->WPHRMGetMessage(13);
$wphrmGetPagePermissions = $this->WPHRMGetPagePermissions();

/*J@F*/
wp_enqueue_style('wphrm-fullcalendar-css');
wp_enqueue_script('wphrm-bic-calendar-js');
wp_enqueue_script('wphrm-fullcalender-js');
wp_enqueue_script('jaf-wphrm-js');
/*J@F*/
?>
<!-- BEGIN PAGE HEADER-->
<div class="preloader">
<span class="preloader-custom-gif"></span>
</div>
<div id="deleteModal" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="alert alert-success display-hide" id="WPHRMCustomDelete_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrmMessagesUpdateDeparment->messagesDesc); ?>
                <button class="close" data-close="alert"></button>
            </div>
            <div class="alert alert-danger display-hide" id="WPHRMCustomDelete_error">
                <button class="close" data-close="alert"></button>
            </div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
                <h4 class="modal-title"><?php _e('Confirmation', 'wphrm'); ?></h4>
            </div>
            <div class="modal-body" id="info"><p><?php _e('Are you sure you want to delete', 'wphrm'); ?>?</p></div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn red" id="delete"><i class="fa fa-trash"></i><?php _e('Delete', 'wphrm'); ?></button>
                <button type="button" data-dismiss="modal" aria-hidden="true" class="btn default"><i class="fa fa-times"></i><?php _e('Cancel', 'wphrm'); ?></button>
            </div>
        </div>
    </div>
</div>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div style="padding-left: 0px; padding-right:20px; padding-top:0px;" class="col-md-12">

    <!-- BEGIN PAGE HEADER-->
    <h3 class="page-title"><?php _e('Notices', 'wphrm'); ?></h3>
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li><i class="fa fa-home"></i><?php _e('Home', 'wphrm'); ?><i class="fa fa-angle-right"></i></li>
            <li><?php _e('Notices', 'wphrm'); ?></li>
        </ul>
    </div>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-12">
            <?php if (in_array('manageOptionsNotice', $wphrmGetPagePermissions)) { ?>
                <a class="btn green " href="?page=wphrm-add-notice" data-toggle="modal"><i class="fa fa-plus"></i><?php _e('Add New Notice', 'wphrm'); ?></a>
            <?php } ?>

            <!--J@F Modified-->
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#notice-list">Notice List</a></li>
                <li><a data-toggle="tab" href="#company-notice">Company Calendar</a></li>
                <li><a data-toggle="tab" href="#department-notice">Deparment Calendar</a></li>
            </ul>

            <div class="tab-content">
                <div id="notice-list" class="tab-pane fade in active">
                    <div class="portlet box blue calendar">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-list"></i><?php _e('List of Notices', 'wphrm'); ?>
                            </div>
                        </div>
                        <div class="portlet-body">

                            <?php if(in_array('manageOptionsNotice', $wphrmGetPagePermissions)): ?>

                            <table class="table table-striped table-bordered table-hover" id="wphrmDataTable">
                                <thead>
                                    <tr> <th><?php _e('Date', 'wphrm'); ?></th>
                                        <th><?php _e('Notice Title', 'wphrm'); ?></th>
                                        <th><?php _e('Department', 'wphrm'); ?></th>
                                        <th><?php _e('Type', 'wphrm'); ?></th>
                                        <th><?php _e('Actions', 'wphrm'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i=1;
                                    $wphrmNotices = $wpdb->get_results("SELECT * FROM  $this->WphrmNoticeTable ORDER BY wphrmdate DESC");
                                    $wphrmNotices = apply_filters('wphrm_notice_list', $wphrmNotices);
                                    if(!empty($wphrmNotices)) :
                                        foreach ($wphrmNotices as $key => $wphrmNotice) { ?>

                                            <?php
                                                //filter the notices
                                                if(!$wphrmUserRole =='administrator') {

                                                }
                                            ?>
                                            <tr>
                                                 <td><?php echo $wphrmNotice->wphrmdate ? date('Y-m-d', strtotime($wphrmNotice->wphrmdate)) : '-'; ?></td>
                                                <td>
                                                    <?php if (isset($wphrmNotice->wphrmtitle)) : echo esc_html($wphrmNotice->wphrmtitle); endif; ?>
                                                    <?php //if (isset($wphrmNotice->wphrminvitationnotice) && $wphrmNotice->wphrminvitationnotice == 1) : echo '<sup><b>*invitation</b></sup>'; endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (isset($wphrmNotice->wphrmdepartment)): $department = $this->get_department_info($wphrmNotice->wphrmdepartment); ?>
                                                    <?php echo empty($department['departmentName']) ? 'Company' : esc_html($department['departmentName']); endif; ?>
                                                </td>
                                                <td>
                                                    <?php echo (isset($wphrmNotice->wphrminvitationnotice) && $wphrmNotice->wphrminvitationnotice == 1) ? 'Invitation' : 'Notice'; ?>
                                                </td>

                                                <td>
                                                    <?php if (isset($wphrmNotice->wphrminvitationnotice) && $wphrmNotice->wphrminvitationnotice == 1 && $current_user->ID == $wphrmNotice->wphrminvitationsender) : ?>
                                                    <a class="btn blue" href="<?php echo admin_url('admin.php?page=wphrm-invitation&id='.$wphrmNotice->id); ?>" onClick="viewInvitationAttendee('<?php echo $wphrmNotice->id; ?>')"  >
                                                            <i class="fa fa-eye"></i><?php _e('Attendees', 'wphrm'); ?>
                                                        </a>
                                                    <?php endif; ?>

                                                    <?php if (!in_array('manageOptionsNotice', $wphrmGetPagePermissions)) { ?>
                                                    <a class="btn purple"  href="?page=wphrm-view-notice&notice_id=<?php if (isset($wphrmNotice->id)) : echo esc_attr($wphrmNotice->id);
                                                        endif; ?>"> <i class="fa fa-edit"></i><?php _e('View', 'wphrm'); ?> </a>
                                                    <?php } else { ?>
                                                        <a class="btn purple"  href="?page=wphrm-add-notice&notice_id=<?php if (isset($wphrmNotice->id)) : echo esc_attr($wphrmNotice->id); endif; ?>">
                                                            <i class="fa fa-edit"></i><?php _e('Edit', 'wphrm'); ?>
                                                        </a>
                                                        <a class="btn red" href="javascript:;" onclick="WPHRMCustomDelete(<?php if (isset($wphrmNotice->id)) : echo esc_js($wphrmNotice->id); endif; ?>, '<?php echo trim(esc_js($this->WphrmNoticeTable)); ?>', 'id')">
                                                            <i class="fa fa-trash"></i><?php _e('Delete', 'wphrm'); ?>
                                                        </a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php $i++; }
                                    else : ?>

                                    <?php endif; ?>
                                </tbody>
                            </table>

                            <?php else: ?>

                            <table class="table table-striped table-bordered table-hover" id="wphrmDataTable">
                                <thead>
                                    <tr> <th><?php _e('Date', 'wphrm'); ?></th>
                                        <th><?php _e('Notice Title', 'wphrm'); ?></th>
                                        <th><?php _e('Department', 'wphrm'); ?></th>
                                        <th><?php _e('Type', 'wphrm'); ?></th>
                                        <th><?php _e('Status', 'wphrm'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i=1;
                                    $wphrmNotices = $wpdb->get_results("SELECT * FROM  $this->WphrmNoticeTable INNER JOIN $this->WphrmInvitationAttendeeTable ON $this->WphrmNoticeTable.id = $this->WphrmInvitationAttendeeTable.notice_id ORDER BY wphrmdate DESC");
                                    $wphrmNotices = apply_filters('wphrm_notice_list', $wphrmNotices);
                                    if(!empty($wphrmNotices)) :
                                        foreach ($wphrmNotices as $key => $wphrmNotice) { ?>

                                            <?php
                                                //filter the notices
                                                if(!$wphrmUserRole =='administrator') {

                                                }
                                            ?>
                                            <tr>
                                                 <td><?php echo $wphrmNotice->wphrmdate ? date('Y-m-d', strtotime($wphrmNotice->wphrmdate)) : '-'; ?></td>
                                                <td>
                                                    <?php

                                                        if (isset($wphrmNotice->wphrmtitle)){

                                                            echo '<a href="?page=wphrm-view-notice&notice_id='.$wphrmNotice->id.'">'.esc_html($wphrmNotice->wphrmtitle).' </a>';

                                                        }

                                                    ?>
                                                    <?php //if (isset($wphrmNotice->wphrminvitationnotice) && $wphrmNotice->wphrminvitationnotice == 1) : echo '<sup><b>*invitation</b></sup>'; endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (isset($wphrmNotice->wphrmdepartment)): $department = $this->get_department_info($wphrmNotice->wphrmdepartment); ?>
                                                    <?php echo empty($department['departmentName']) ? 'Company' : esc_html($department['departmentName']); endif; ?>
                                                </td>
                                                <td>
                                                    <?php echo (isset($wphrmNotice->wphrminvitationnotice) && $wphrmNotice->wphrminvitationnotice == 1) ? 'Invitation' : 'Notice'; ?>
                                                </td>
                                                <td style="text-transform: capitalize">
                                                    <?php echo (isset($wphrmNotice->wphrminvitationnotice) && $wphrmNotice->wphrminvitationnotice == 1) ? $wphrmNotice->status : 'N/A'; ?>
                                                </td>

                                            </tr>
                                        <?php $i++; }
                                    else : ?>

                                    <?php endif; ?>
                                </tbody>
                            </table>

                            <?php endif; ?>

                        </div>
                    </div>
                </div>

                <div id="company-notice" class="tab-pane fade in">
                    <div class="portlet box blue calendar col-md-8">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-list"></i><?php _e('Company Notice Calendar', 'wphrm'); ?>
                            </div>
                        </div>
                        <div class="portlet-body">
                             <div id="Company_Notice_Calendar" class="has-toolbar"></div>
                            <script>
                                jQuery(document).ready(function ($) {
                                    Company_Notice_Calendar.init();
                                    $('.nav-tabs a').on('shown.bs.tab', function(event){
                                        $($(this).attr('href')).find('.fc-today-button').trigger('click');
                                    });
                                });
                            </script>
                        </div>
                    </div>
                </div>

                <div id="department-notice" class="tab-pane fade in">
                    <div class="portlet box blue calendar col-md-8">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-list"></i><?php _e('Department Notice Calendar', 'wphrm'); ?>
                            </div>
                        </div>
                        <div class="portlet-body">

                            <div class="row">
                                <!--<div class="col-md-2">
                                <label><h2>Selected Department:</h2></label>
                                </div>-->
                                <div class="col-md-12">
                                    <div class="form-group">

                                        <?php if(isset($wphrmUserRole) && $wphrmUserRole =='administrator'): ?>

                                        <?php $selected_department = isset($wphrmNotices->wphrmtitle) ?  esc_attr($wphrmNotices->wphrmtitle) : ''; ?>
                                        <?php $wphrmDepartments = $wpdb->get_results("SELECT * FROM  $this->WphrmDepartmentTable"); ?>
                                        <select class="bs-select form-control" data-show-subtext="true" name="wphrm_notice_calendar_department_selected" id="wphrm_notice_calendar_department_selected">
                                            <?php if(!empty($wphrmDepartments)): ?>

                                            <?php foreach ($wphrmDepartments as $key => $wphrmDepartment): ?>
                                            <?php $dep = unserialize(base64_decode($wphrmDepartment->departmentName)); ?>
                                                <option value="<?php echo $wphrmDepartment->departmentID; ?>" <?php selected($wphrmDepartment->departmentID, $wphrmNotices->wphrmdepartment ) ?> > <?php echo $dep['departmentName']; ?></option>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>

                                        <?php else: ?>
                                        <?php $wphrmEmployeeInfo = $this->WPHRMGetUserDatas(get_current_user_id(), 'wphrmEmployeeInfo'); ?>
                                        <?php $department_info = $this->get_department_info($wphrmEmployeeInfo['wphrm_employee_department']); ?>
                                        <h1><?php echo $department_info['departmentName']; ?></h1>
                                        <input type="hidden" name="wphrm_notice_calendar_department_selected" id="wphrm_notice_calendar_department_selected" value="<?php echo $wphrmEmployeeInfo['wphrm_employee_department']; ?>" >
                                        <?php endif; ?>

                                    </div>
                                </div>
                            </div>

                             <div id="Department_Notice_Calendar" class="has-toolbar"></div>
                            <script>
                                jQuery(document).ready(function ($) {
                                    Department_Notice_Calendar.init();
                                    $('.nav-tabs a').on('shown.bs.tab', function(event){
                                        $($(this).attr('href')).find('.fc-today-button').trigger('click');
                                    });
                                    jQuery('#wphrm_notice_calendar_department_selected').on('change', function(){
                                        Department_Notice_Calendar.init();
                                    });
                                });
                            </script>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- END PAGE CONTENT-->
