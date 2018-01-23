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
/*wp_enqueue_style('wphrm-fullcalendar-css');
wp_enqueue_script('wphrm-bic-calendar-js');
wp_enqueue_script('wphrm-fullcalender-js');*/
wp_enqueue_script('jaf-wphrm-js');
/*J@F*/

$notice_id = empty($_REQUEST['id']) ? false : $_REQUEST['id'];

//get notice invitation created by the current user
$invitations = $this->get_user_invitations($current_user->ID);
?>
<!-- BEGIN PAGE HEADER-->
<div class="preloader">
    <span class="preloader-custom-gif"></span>
</div>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div style="padding-left: 0px; padding-right:20px; padding-top:0px;" class="col-md-12">

    <!-- BEGIN PAGE HEADER-->
    <h3 class="page-title"><?php _e('Files & Documents', 'wphrm'); ?></h3>
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li><i class="fa fa-home"></i><?php _e('Home', 'wphrm'); ?><i class="fa fa-angle-right"></i></li>
            <li><?php _e('Invitations', 'wphrm'); ?></li>
        </ul>
    </div>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-12">
            <a class="btn green " href="?page=wphrm-notice"><i class="fa fa-arrow-left"></i><?php _e('Back', 'wphrm'); ?></a>
            <?php if(isset($_REQUEST['show_all']) && filter_var($_REQUEST['show_all'], FILTER_VALIDATE_BOOLEAN)): ?>
            <a class="btn green " href="?page=wphrm-invitation&id=<?php echo $notice_id; ?>"><?php _e('Attending only', 'wphrm'); ?></a>
            <?php else: ?>
            <a class="btn green " href="?page=wphrm-invitation&id=<?php echo $notice_id; ?>&show_all=yes"><?php _e('Show All', 'wphrm'); ?></a>
            <?php endif; ?>

            <?php if($invitations): ?>
            <div class="invitation-select">
                    <form action="">
                        <input type="hidden" name="page" value="wphrm-invitation">
                        <input type="hidden" name="show_all" value="<?php echo isset($_REQUEST['show_all']) ? $_REQUEST['show_all'] : 'no'; ?>" >
                        <select name="id" >
                            <?php if(!$notice_id): ?>
                            <option value=""> - Please Select - </option>
                            <?php endif; ?>
                            <?php foreach($invitations as $invitation): ?>
                            <option value="<?php echo $invitation->id; ?>" <?php selected($invitation->id, $notice_id); ?> ><?php echo $invitation->wphrmtitle; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn blue">Change</button>
                    </form>
            </div>
            <p></p>
            <?php endif; ?>

            <div class="portlet box blue calendar">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-list"></i><?php _e('Attendees', 'wphrm'); ?>
                    </div>
                </div>
                <div class="portlet-body">
                    <?php if (in_array('manageOptionsNotice', $wphrmGetPagePermissions) || current_user_can('administrator')): ?>
                    <!--uploader-->

                    <?php endif; ?>
                    <div  >
                        <table class="table table-striped table-bordered table-hover" id="wphrmDataTable">
                            <thead>
                                <tr>
                                    <th><?php _e('#', 'wphrm'); ?></th>
                                    <th><?php _e('Name', 'wphrm'); ?></th>
                                    <th><?php _e('Department', 'wphrm'); ?></th>
                                    <th><?php _e('Status', 'wphrm'); ?></th>
                                    <th><?php _e('Date', 'wphrm'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i=1;
                                $attendees = $wpdb->get_results("SELECT * FROM  $this->WphrmInvitationAttendeeTable WHERE notice_id = '$notice_id' ".( !empty($_REQUEST['show_all']) && filter_var($_REQUEST['show_all'], FILTER_VALIDATE_BOOLEAN) ? "" : "AND status = 'attending'" )." ORDER BY id ASC");
                                $attendees = apply_filters('wphrm_notice_list', $attendees);
                                if(!empty($attendees)) :
                                foreach ($attendees as $key => $attendee) { ?>

                                <?php
                                    //filter the notices
                                    if(!$wphrmUserRole =='administrator') {

                                    }
                                ?>
                                <tr>
                                    <td><?php echo esc_html($i); ?></td>
                                    <td>
                                        <?php
                                        $user_info = $this->get_user_info($attendee->user_id);
                                        echo $user_info->wphrm_employee_fname. ' ' .$user_info->wphrm_employee_lname;
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $user_dept = $this->get_department_info($user_info->wphrm_employee_department);
                                        echo $user_dept['departmentName'];
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo ucfirst($attendee->status);
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo $attendee->date_created;
                                        ?>
                                    </td>
                                </tr>
                                <?php $i++; }
                                else : ?>
                                <tr>
                                    <td colspan="4"><?php _e('No notices found in the database.', 'wphrm'); ?>
                                    </td><td class="collapse"></td><td class="collapse"></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!--modal for adding folder-->
                    <div id="new-folder" class="modal fade" role="dialog" >
                        <form id="form-new-folder">
                            <input type="hidden" name="action" value="add_new_folder" >
                            <input type="hidden" name="current_folder" value="" >
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">New Folder</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="form-group">
                                                <label class="control-label col-md-3"><?php _e('Folder Name:', 'wphrm'); ?>  </label>
                                                <div class="col-md-9">
                                                    <input type="text" class="form-control" name="folder-name" value="" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-success" data-id="" data-type="" >SAVE</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<!-- END PAGE CONTENT-->
