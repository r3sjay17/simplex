<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $current_user, $wpdb;
$wphrmMessages = $this->WPHRMGetMessage(26);
$wphrmUserRole = implode(',', $current_user->roles);
$readonly_class = '';
$readonly = '';
$edit_mode = false;
$wphrm_notice_desc = '';
$wphrmNotices ='';
if (isset($_REQUEST['notice_id']) && !empty($_REQUEST['notice_id'])) :
$noticeId = esc_sql($_REQUEST['notice_id']); // esc
$wphrmNotices = $wpdb->get_row("SELECT * FROM $this->WphrmNoticeTable WHERE id = '$noticeId'");
endif;
$wphrmGetPagePermissions = $this->WPHRMGetPagePermissions();
?>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="preloader">
    <span class="preloader-custom-gif"></span>
</div>
<?php if (!in_array('manageOptionsNotice', $wphrmGetPagePermissions)) { ?>
<div style="padding-left: 0px; padding-right:20px; padding-top:0px;" class="col-md-12">
    <h3 class="page-title"><?php _e('Notice', 'wphrm'); ?></h3>
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li><i class="fa fa-home"></i><?php _e('Home', 'wphrm'); ?><i class="fa fa-angle-right"></i></li>
            <li><?php _e('Notice Board', 'wphrm'); ?></li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <a class="btn green " href="?page=wphrm-notice"><i class="fa fa-arrow-left"></i><?php _e('Back', 'wphrm'); ?></a>
            <div class="portlet box blue calendar">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-exclamation-triangle"></i><?php _e('Notice Board', 'wphrm'); ?>
                        <span>
                            <?php if (isset($wphrmNotices->wphrminvitationnotice) && $wphrmNotices->wphrminvitationnotice == 1) : echo '( INVITATION )'; endif; ?>
                        </span>
                    </div>
                </div>
                <div class="portlet-body form" style="background: #FFFDE7;">

                    <div class="notice-title-2">
                        <h2><span><b>Title</b></span>: <span><?php if (isset($wphrmNotices->wphrmtitle)) : echo   stripslashes($wphrmNotices->wphrmtitle);  endif; ?></span></h2>
                    </div>

                    <div class="notice-date">
                        <p><h2><span><b>Date</b></span>: <span><?php if (isset($wphrmNotices->wphrmdate)) : echo date('M d, Y', strtotime(stripslashes($wphrmNotices->wphrmdate)));  endif; ?></span></h2></p>
                </div>

                <div class="panel notice-description">
                    <?php if (isset($wphrmNotices->wphrmdesc)) : echo   stripslashes($wphrmNotices->wphrmdesc);  endif; ?>
                </div>
                <!--J@F-->
                <?php
                                                                       //check if the user already respond to invitation
                                                                       $respond = $this->get_user_invitation_reponse($current_user->ID, $wphrmNotices->id);
                ?>
                <?php if(empty($respond) && $wphrmNotices->wphrminvitationnotice == '1'): ?>
                <div class="invitation-actions">
                    <a href="#" id="confirmNoticeInvitation" class="btn primary blue" data-notice_id="<?php echo $wphrmNotices->id; ?>"  ><i class="fa fa-check" ></i> I'M ATTENDING</a>
                    <a href="#" id="declineNoticeInvitation" class="btn primary red" data-notice_id="<?php echo $wphrmNotices->id; ?>"  ><i class="fa fa-times" ></i> DECLINE</a>
                    <p class="invitation-status"></p>
                    <p class="description">Please choose action below. If no selected, its you are NOT attending.</p>
                </div>
                <?php elseif(!empty($respond) && $wphrmNotices->wphrminvitationnotice == '1'): ?>
                <div class="invitation-actions">
                    <p class="description">You Already <strong><?php echo strtoupper($respond->status); ?></strong> to this invitation.</p>
                </div>
                <?php endif; ?>
                <!--J@F-->

            </div>
        </div>
    </div>
</div>
</div>

<script>
    jQuery(function($){
        $('#confirmNoticeInvitation').click(function(e){
            e.preventDefault();
            $.post(ajaxurl, {action: 'confirmNoticeInvitation', notice_id: $(this).attr('data-notice_id'), status: 'attending' }, function(res){
                if(res.status){
                    alert(res.message);
                }else{
                    alert(res.message);
                }
            }, 'json' );
        });
        $('#declineNoticeInvitation').click(function(e){
            e.preventDefault();
            $.post(ajaxurl, {action: 'confirmNoticeInvitation', notice_id: $(this).attr('data-notice_id'), status: 'declined' }, function(res){
                if(res.status){
                    alert(res.message);
                }else{
                    alert(res.message);
                }
            }, 'json' );
        });
    });
</script>
<?php } else { ?>
<!-- END PAGE CONTENT-->
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div style="padding-left: 0px; padding-right:20px; padding-top:0px;" class="col-md-12">
    <h3 class="page-title">
        <?php if (isset($noticeId) && $noticeId != '') { _e('Edit Notice', 'wphrm'); } else { _e('Add Notice', 'wphrm'); } ?>
    </h3>
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li><i class="fa fa-home"></i><?php _e('Home', 'wphrm'); ?><i class="fa fa-angle-right"></i></li>
            <li>
                <?php if (isset($noticeId) && $noticeId != '') { _e('Edit Notice', 'wphrm'); } else { ?>
                <?php _e('Add Notice', 'wphrm'); ?>
                <?php } ?>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <a class="btn green " href="?page=wphrm-notice"><i class="fa fa-arrow-left"></i><?php _e('Back', 'wphrm'); ?></a>
            <div class="portlet box blue calendar">
                <div class="portlet-title">
                    <div class="caption">
                        <?php if (isset($noticeId) && $noticeId != '') { _e('Edit Notice', 'wphrm'); } else { _e('Add Notice', 'wphrm'); } ?>
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    <form method="POST"  accept-charset="UTF-8" class="form-horizontal form-bordered">
                        <div class="form-body">
                            <div class="alert alert-success display-hide" id="wphrmNoticeInfo_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrmMessages); ?>
                                <button class="close" data-close="alert"></button>
                            </div>
                            <div class="alert alert-danger display-hide" id="wphrmNoticeInfo_error">
                                <button class="close" data-close="alert"></button>
                            </div>

                            <input type="hidden"  id="wphrm_notice_id" name="wphrm_notice_id" value="<?php if (isset($noticeId)) : echo esc_attr($noticeId); endif; ?>">
                            <div class="form-group">
                                <label class="col-md-3 control-label"><?php _e('Title', 'wphrm'); ?> :<?php echo isset($wphrmNotice->wphrmtitle) ?> <span class="required">*</span>
                                </label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="wphrm_notice_title" name="wphrm_notice_title" placeholder="<?php _e('Title', 'wphrm'); ?>" value="<?php
              if (isset($wphrmNotices->wphrmtitle)) : echo  esc_attr($wphrmNotices->wphrmtitle); endif; ?>">
                                </div>
                            </div>
                            <!--J@F-->
                            <div class="form-group">
                                <label class="col-md-3 control-label"><?php _e('Notice Schedule', 'wphrm'); ?> :<?php echo isset($wphrmNotice->wphrmtitle) ?> <span class="required">*</span>
                                </label>
                                <div class="col-md-8">
                                    <div class="input-group input-medium date after-current-date"  data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                        <input class="form-control date-pickers"  name="wphrm_notice_date" type="text" id="wphrm_notice_date" value="<?php
                  echo (isset($wphrmNotices->wphrm_notice_date)) ? esc_attr($wphrmNotices->wphrm_notice_date) : date('d-m-Y');
                                                                                                                                                     ?>" autocapitalize="none"  />
                                        <span class="input-group-btn">
                                            <button class="btn default-date" type="button"><i class="fa fa-calendar" style="line-height: 1.9;"></i></button>
                                        </span>
                                    </div>
                                    <p class="description">Set the date of the event here. This will be used to display on the calendar</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label"><?php _e('Choose Department', 'wphrm'); ?> :<?php echo isset($wphrmNotice->wphrmtitle) ?> <span class="required">*</span>
                                </label>
                                <div class="col-md-8">
                                    <?php $selected_department = isset($wphrmNotices->wphrmtitle) ?  esc_attr($wphrmNotices->wphrmtitle) : ''; ?>
                                    <?php $wphrmDepartments = $wpdb->get_results("SELECT * FROM  $this->WphrmDepartmentTable"); ?>
                                    <select class="bs-select form-control" data-show-subtext="true" <?php echo empty($_REQUEST['notice_id']) ? 'data-do="add"' : 'data-do="edit"'; ?>  name="wphrm_notice_department" id="wphrm_notice_department">
                                        <option value="" >Show to All</option>
                                        <?php if(!empty($wphrmDepartments)): ?>

                                        <?php foreach ($wphrmDepartments as $key => $wphrmDepartment): ?>
                                        <?php $dep = unserialize(base64_decode($wphrmDepartment->departmentName)); ?>
                                        <option value="<?php echo $wphrmDepartment->departmentID; ?>" <?php selected($wphrmDepartment->departmentID, $wphrmNotices->wphrmdepartment ) ?> > <?php echo $dep['departmentName']; ?></option>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <p class="description" >Set notification to specific department.</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label"><?php _e('Create Invitation?', 'wphrm'); ?> :<?php echo isset($wphrmNotice->wphrminvitationnotice) ?>
                                </label>
                                <div class="col-md-8">
                                    <?php $checked = isset($wphrmNotices->wphrminvitationnotice) ?  esc_attr($wphrmNotices->wphrminvitationnotice) : false; ?>
                                    <input type="checkbox" id="wphrm_invitation_notice" name="wphrm_invitation_notice" <?php checked($checked, true); ?> data-on-text="Yes" data-off-text="No" >
                                    <p class="description" >Send an invation through notification board. Employee can attend by clicking "Attend" button.</p>
                                </div>
                            </div>
                            <div class="form-group invitation_users hide">
                                <label class="col-md-3 control-label"><?php _e('Select Employee', 'wphrm'); ?> :
                                </label>
                                <div class="col-md-8">
                                    <p class="description" >To whom this notice be send?</p>
                                    <?php
              $user_selected = isset($wphrmNotices->wphrminvitationrecipient) ? explode(',', $wphrmNotices->wphrminvitationrecipient) : array();
              $wphrmUsers = get_users(array('role__in' => 'employee'));
                                    ?>
                                    <?php if($wphrmUsers): ?>
                                    <ul>
                                        <?php foreach($wphrmUsers as $user): ?>
                                        <?php $wphrmEmployeeBasicInfo = $this->WPHRMGetUserDatas($user->ID, 'wphrmEmployeeInfo'); ?>
                                        <li><label><input autocomplete="off" type="checkbox" <?php checked(in_array($user->ID ,$user_selected ), true ); ?> name="wphrm_invitation_recipient[]" data-department="<?php echo empty($wphrmEmployeeBasicInfo['wphrm_employee_department']) ? '' : $wphrmEmployeeBasicInfo['wphrm_employee_department']; ?>" value="<?php echo $user->ID; ?>" > <?php echo $wphrmEmployeeBasicInfo['wphrm_employee_fname'] .' '. $wphrmEmployeeBasicInfo['wphrm_employee_lname']; ?></label></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <div class=""><a href="#" class="user-select-all" >Select All</a> <a href="#" class="user-select-none" >Select None</a></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <!--J@F END-->
                            <div class="form-group">
                                <label class="col-md-3 control-label"><?php _e('Description', 'wphrm'); ?> :<span class="required">*</span>
                                </label>
                                <div class="col-md-8">
                                    <?php $desc ='';
              if (isset($wphrmNotices->wphrmdesc)) : $desc = $wphrmNotices->wphrmdesc; endif;
              wp_editor(stripslashes($desc), 'wphrm_notice_desc', array('media_buttons' => true, 'editor_height' => 200, 'editor_width' => 100)); ?>
                                </div>
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-offset-3 col-md-9">
                                        <?php if (isset($noticeId) && $noticeId != ''){ ?>
                                        <button type="button" id="wphrmNoticeInfo_frm" class="btn green"><i class="fa fa-edit"></i><?php _e('Edit Notice', 'wphrm'); ?></button>
                                        <?php } else { ?>
                                        <button type="button" id="wphrmNoticeInfo_frm"  class="btn green"><i class="fa fa-plus"></i><?php _e('Add Notice', 'wphrm'); ?></button>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <!-- END FORM-->
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(function($){
        function show_hide_user_select(state){
            if(state){
                $('.invitation_users').removeClass('hide');
            }else{
                $('.invitation_users').addClass('hide');
            }
        }
        function show_hide_user_department(_select){
            $('.invitation_users').find('input[type="checkbox"]').each(function(){
                if('' == _select.val()) {
                    if(_select.attr('data-do') != 'edit'){
                        $(this).prop('checked', true).removeAttr("disabled");
                    }else{
                        $(this).removeAttr("disabled");
                    }
                }else if($(this).data('department') == _select.val()) {
                    $(this).prop('checked', true).removeAttr("disabled");
                }else{
                    $(this).prop('checked', false).attr("disabled", true);
                }
            });
        }
        $('#wphrm_invitation_notice').bootstrapSwitch({
            onInit: function(e, state){
                show_hide_user_select(state);
            },
            onSwitchChange: function(e, state){
                show_hide_user_select(state);
            }
        });

        $('.user-select-all').click(function(e){
            e.preventDefault();
            $(this).closest('.invitation_users').find('input[type="checkbox"]').each(function(){
                if(!$(this).is(':disabled')) $(this).prop('checked', true);
            });
        });
        $('.user-select-none').click(function(e){
            e.preventDefault();
            $(this).closest('.invitation_users').find('input[type="checkbox"]').prop('checked', false);
        });

        $('#wphrm_notice_department').change(function(e){
            show_hide_user_department( $(this) );
        }).trigger('change');


    });
</script>
<!-- END PAGE CONTENT-->
<?php } ?>
