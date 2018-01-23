<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $current_user, $wpdb;
$wphrmCurrentuserId = esc_sql($current_user->ID); // esc
$wphrmUserRole = implode(',', $current_user->roles);
?>
<!-- BEGIN PAGE HEADER-->
<div class="preloader">
<span class="preloader-custom-gif"></span>
</div>
<div id="deleteModal" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="alert alert-success display-hide" id="WPHRMCustomDelete_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrm_messages_delete_designation->messagesDesc); ?>
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

                <button type="button" data-dismiss="modal" class="btn red" id="delete"><i class="fa fa-trash"></i><?php _e('Delete', 'wphrm'); ?></button>
               <button type="button" data-dismiss="modal" aria-hidden="true" class="btn default"><i class="fa fa-times"></i><?php _e('Cancel', 'wphrm'); ?></button>
            </div>
        </div>
    </div>
</div>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div style="padding-left: 0px; padding-right:20px; padding-top:0px;" class="col-md-12">
    <h3 class="page-title">
        <?php _e('Notifications', 'wphrm'); ?>
    </h3>
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li><i class="fa fa-home"></i> <?php _e('Home', 'wphrm'); ?><i class="fa fa-angle-right"></i></li>
            <li><?php _e('Notifications', 'wphrm'); ?></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box blue calendar">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-list"></i><?php _e('List of Notifications', 'wphrm'); ?>
                    </div>
                </div>
                <div class="portlet-body">
                <form id="wphrm_delete_selected_notifications" method="post" action="#">
                   <table class="wphrmtable table table-striped table-bordered table-hover" id="wphrmDataTable">
                        <thead>
                            <tr>
                            <th></th>
                            <th><?php _e('S.No', 'wphrm'); ?></th>
                            <th><?php _e('Message Type', 'wphrm'); ?></th>
                            <th><?php _e('Description', 'wphrm'); ?></th>
                            <th><?php _e('Date', 'wphrm'); ?></th>
                            <th><?php _e('Status', 'wphrm'); ?></th>
                            <!--<th><?php //_e('Action', 'wphrm'); ?></th>-->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                             $wphrmSalaryAccording = $this->WPHRMGetSettings('wphrsalaryDayOrHourlyInfo');
                            $wphrm_notifications = $wpdb->get_results("SELECT * FROM $this->WphrmNotificationsTable WHERE `wphrmUserID` = '$wphrmCurrentuserId' ORDER BY id DESC");

                            if(!empty($wphrm_notifications)) {
                                 $j = 1;
                                 $i = 1;
                                foreach ($wphrm_notifications as $key => $wphrm_notification) {
                                    $wphrmEmployeeInfo_load = get_user_meta($wphrm_notification->wphrmUserID, 'wphrmEmployeeInfo', true);
                                    $wphrmEmployeeInfo = unserialize(base64_decode($wphrmEmployeeInfo_load));
                                    ?>
                                    <tr>
                                        <td><input type="checkbox" name="bulk_delete_notifications[]" class="bulk_delete_notifications" value="<?php echo $wphrm_notification->id ?>" class="form-control icheck"></td>
                                        <td><?php echo esc_html($i); ?></td>
                                        <td>
                                            <?php if(isset($wphrm_notification->notificationType) && $wphrm_notification->notificationType == 'Leave Request'){ ?>
                                            <a  href="?page=wphrm-leaves-application"> <?php echo esc_html($wphrm_notification->notificationType) ?> </a>
                                            <?php }
                                            else if(isset($wphrm_notification->notificationType) && $wphrm_notification->notificationType == 'Salary Slip request'){
                                            $salarySliprequestinfomation = esc_sql('Salary Slip request'); // esc
                                            if(isset($wphrm_notification->wphrmFromId) && $wphrm_notification->wphrmFromId != '0'){
                                             $string = $wphrm_notification->wphrmDesc;
                                             $split = explode(" ", $string);
                                             $year = esc_sql($split[count($split) - 1]); // esc
                                             $month = esc_sql($split[count($split) - 2]); // esc
                                             $matchDate = date($year.'-'.$month.'-01');
                                             $wphrmCheckgenerated = $wpdb->get_row("SELECT * FROM  $this->WphrmSalaryTable  where `date` = '$matchDate' AND `employeeValue`= 'generated'");
                                            if(empty($wphrmCheckgenerated)){
                                               if (isset($wphrmSalaryAccording['wphrm-according']) && $wphrmSalaryAccording['wphrm-according'] == 'Week') {
                                                ?>
                                          <a class="text-decoration" onclick="WPHRMNotificationStatusWeekChangeInfo(<?php echo esc_js($wphrm_notification->id); ?>,'<?php echo esc_js($wphrm_notification->wphrmFromId); ?>')"><?php echo esc_html($wphrm_notification->notificationType) ?></a>
                                            <?php }else{
                                                ?>
                                          <a class="text-decoration" onclick="WPHRMNotificationStatusChangeInfo(<?php echo esc_js($wphrm_notification->id); ?>,'<?php echo esc_js($wphrm_notification->wphrmFromId); ?>','<?php echo esc_js($month); ?>','<?php echo esc_js($year); ?>')"><?php echo esc_html($wphrm_notification->notificationType) ?></a>
                                           <?php } } } else{
                                           if (isset($wphrmSalaryAccording['wphrm-according']) && $wphrmSalaryAccording['wphrm-according'] == 'Week') {      ?>
                                          <a class="text-decoration" href ="?page=wphrm-select-financials-week&employee_id=<?php echo $wphrm_notification->wphrmFromId; ?>"><?php echo esc_html($wphrm_notification->notificationType) ?></a>
                                           <?php }else{ ?>
                                              <a class="text-decoration" href ="?page=wphrm-select-financials-month&employee_id=<?php echo $wphrm_notification->wphrmFromId; ?>"><?php echo esc_html($wphrm_notification->notificationType) ?></a>
                                          <?php } } }
                                           else if(isset($wphrm_notification->notificationType) && $wphrm_notification->notificationType == 'Notice Board'){ ?>
                                          <a  href="?page=wphrm-notice"> <?php echo esc_html($wphrm_notification->notificationType) ?> </a>
                                          <?php }
                                          else if(isset($wphrm_notification->notificationType) && $wphrm_notification->notificationType == 'Salary Generated'){ ?>
                                            <a  href="?page=wphrm-select-financials-month&employee_id=<?php echo  $wphrm_notification->wphrmUserID; ?>"> <?php echo esc_html($wphrm_notification->notificationType) ?> </a>
                                         <?php }else{ ?>
                                            <a  href="?page=wphrm-leaves-application"> <?php echo esc_html($wphrm_notification->notificationType) ?> </a>
                                         <?php } ?>



                                        </td>
                                        <td><?php if(isset($wphrm_notification->wphrmDesc)) : echo esc_html($wphrm_notification->wphrmDesc); endif; ?></td>
                                        <td><?php if(isset($wphrm_notification->wphrmDate)) : echo esc_html(date('d-m-Y',strtotime($wphrm_notification->wphrmDate))); endif; ?></td>
                                        <td>
                                            <?php if(isset($wphrm_notification->wphrmStatus) && $wphrm_notification->wphrmStatus =='unseen') { ?>
                                                <span class="label label-sm label-success"><?php _e('Unread', 'wphrm'); ?></span>
                                            <?php } else { ?>
                                                <span class="label label-sm label-danger"><?php _e('Read', 'wphrm'); ?></span>
                                            <?php } ?>
                                        </td>
                                        <!--<td>
                                            <a class="btn red" href="javascript:;" onclick="WPHRMCustomDelete(<?php //if(isset($wphrm_notification->id)) : echo esc_js($wphrm_notification->id); endif; ?>, '<?php //echo esc_js($this->WphrmNotificationsTable) ?>', 'id')"><i class="fa fa-trash"></i><?php _e('Delete', 'wphrm'); ?></a>
                                        </td>-->
                                    </tr>
                                    <?php $j++;
                                            $i++; }
                               } else { ?>
                                            <!--<tr>
                                                <td colspan="7"><?php _e('No notifications found in the database.', 'wphrm'); ?>
                                                </td><td class="collapse"></td><td class="collapse"></td><td class="collapse"></td><td class="collapse"></td><td class="collapse"></td><td class="collapse"></td>
                                            </tr>-->
                               <?php } ?>
                                    </tbody>
                                </table>
                        <div>
                         <button type="submit" class="btn red btn-delete-selected-notifications" href="#" ><i class="fa fa-trash"></i><?php _e('Delete Selected', 'wphrm'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END PAGE CONTENT-->
