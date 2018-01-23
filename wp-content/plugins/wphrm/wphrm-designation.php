<?php
global $wpdb;
$readonly_class = '';
$readonly = 'readonly';
$edit_mode = false;
$wphrmMessagesDesignation = $this->WPHRMGetMessage(10);
$wphrmMessagesUpdateDesignation = $this->WPHRMGetMessage(11);
$wphrmMessagesDeleteDesignation = $this->WPHRMGetMessage(12);
if (isset($_REQUEST['departmentID']) && $_REQUEST['departmentID'] == '' && isset($_REQUEST['department_name']) && $_REQUEST['department_name'] == '' ) :
    wp_redirect(admin_url('admin.php?page=wphrm-departments'), 301);
else :
     $departmentId = esc_sql($_REQUEST['departmentID']); // esc
     $departmentName = esc_sql($_REQUEST['department_name']); // esc
endif;
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
                <h4 class="modal-title"><strong><i class="fa fa-plus"></i><?php _e('Add Designation', 'wphrm'); ?></strong></h4>
            </div>
            <div class="modal-body">
                <div class="portlet-body form">
                    <div class="alert alert-success display-hide" id="wphrmDesignationInfo_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrmMessagesDesignation); ?>
                        <button class="close" data-close="alert"></button>
                    </div>
                    <div class="alert alert-danger display-hide" id="wphrmDesignationInfo_error">
                        <button class="close" data-close="alert"></button>
                    </div>
                    <!-- BEGIN FORM-->
                    <form method="POST"  accept-charset="UTF-8" class="form-horizontal designation_frm" id="edit_form">
                        <div class="form-body">
                            <div class="form-group">
                                <div class="col-md-10">
                                    <input  name="departmentID" id="departmentID" type="hidden" value="<?php if(isset($departmentId)) :echo esc_html($departmentId);  endif; ?>"/>
                                    <input class="form-control form-control-inline " name="designation_name[]" id="designation_name" type="text" value="" placeholder="<?php _e('Designation Name', 'wphrm'); ?>" />
                                </div>
                            </div>
                        </div>
                        <div id="insertBeforeDesignation"></div>
                            <div class="form-group">
                                  <div class="col-md-10">
                                  <button type="button" id="plusButtonDesignation" class="btn btn-sm green form-control-inline">
                                <i class="fa fa-plus"></i><?php _e('Add More', 'wphrm'); ?>
                            </button>
                                  </div></div>
                        <div class="form-group">
                                <div class="col-md-12">                                       
                                    <button type="submit"  class="btn blue"><i class="fa fa-plus"></i><?php _e('Add Designation', 'wphrm'); ?></button>
                                    <button type="button" data-dismiss="modal" aria-hidden="true" class="btn red"><i class="fa fa-times"></i><?php _e('Cancel', 'wphrm'); ?></button>
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
                <h4 class="modal-title"><strong><i class="fa fa-edit"></i><?php _e('Edit Designation', 'wphrm'); ?></strong></h4>
            </div>
            <div class="modal-body">
                <div class="portlet-body form">
                    <div class="alert alert-success display-hide" id="wphrm_Edesignation_info_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrmMessagesUpdateDesignation); ?>
                        <button class="close" data-close="alert"></button>
                    </div>
                    <div class="alert alert-danger display-hide" id="wphrm_Edesignation_info_error">
                        <button class="close" data-close="alert"></button>
                    </div>
                    <!-- BEGIN FORM-->
                    <form method="POST" accept-charset="UTF-8" class="form-horizontal wphrm_edit_designation" id="edit_form">
                        <div class="form-body">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <input class="form-control form-control-inline " name="editdesignation" id="editdesignation" type="text" value="" placeholder="<?php _e('Designation Name', 'wphrm'); ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                                <div class="col-md-12">                                      
                                    <button type="submit"  class="btn blue"><i class="fa fa-edit"></i><?php _e('Edit Designation', 'wphrm'); ?></button>
                                    <button type="button" data-dismiss="modal" aria-hidden="true" class="btn red"><i class="fa fa-times"></i><?php _e('Cancel', 'wphrm'); ?></button>
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
            <div class="alert alert-success display-hide" id="WPHRMCustomDelete_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrmMessagesDeleteDesignation); ?>
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
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div style="padding-left: 0px; padding-right:20px; padding-top:0px;" class="col-md-12">
<h3 class="page-title"> <?php _e('Designations', 'wphrm'); ?> </h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li> <i class="fa fa-home"></i><?php _e('Home', 'wphrm'); ?><i class="fa fa-angle-right"></i> </li>
        <li> <?php _e('Designations', 'wphrm'); ?> </li>
    </ul>
</div>
    <div class="row">
        <div class="col-md-12">
            <a class="btn green " href="#add_static" data-toggle="modal"><i class="fa fa-plus"></i><?php _e('Add New Designation', 'wphrm'); ?></a>
            <a class="btn green " href="?page=wphrm-departments" ><i class="fa fa-arrow-left"></i><?php _e('Back', 'wphrm'); ?> </a>
            <!--<a href="#" class="btn purple btn-edit-designation"><i class="fa fa-edit"></i><?php //_e('Edit', 'wphrm'); ?></a>-->
            <a href="#" class="btn red btn-delete-selected-designation"><i class="fa fa-trash"></i><?php _e('Delete Selected', 'wphrm'); ?></a>
            <div class="portlet box blue calendar">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-list"></i><?php  _e('List of ', 'wphrm'); ?><?php echo esc_html($departmentName).' '; _e('Designations', 'wphrm'); ?>
                    </div>
                </div>
                <div class="portlet-body designation-table">
                    <form id="wphrm_delete_selected_designations" method="post" action="#">
                        <table class="table table-striped table-bordered table-hover" id="wphrmDataTable">
                            <thead>
                                <tr>  
                                    <th></th>
                                    <th><?php _e('S.No', 'wphrm'); ?></th>
                                    <th><?php _e('Department Name', 'wphrm'); ?></th>
                                    <th><?php _e('Designation Name', 'wphrm'); ?></th>
                                    <th><?php _e('Actions', 'wphrm'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i=1;
                                $wphrmDesignations = $wpdb->get_results("SELECT * FROM  $this->WphrmDesignationTable where departmentID =$departmentId ");
                                if(!empty($wphrmDesignations)) :
                                    foreach ($wphrmDesignations as $key => $wphrmDesignation) {                                
                                        $wphrmDepartment = $wpdb->get_row("SELECT * FROM  $this->WphrmDepartmentTable where departmentID = $departmentId");
                                        $wphrmDepartmentInfo = unserialize(base64_decode($wphrmDepartment->departmentName));
                                        $designationInfo = unserialize(base64_decode($wphrmDesignation->designationName)); ?>
                                        <tr>  
                                            <td>
                                                <input type="checkbox" name="bulk_delete_designation[]" class="bulk_delete_designation" value="<?php echo $wphrmDesignation->designationID ?>" class="form-control icheck">
                                            </td>
                                             <td><?php echo esc_html($i); ?></td>
                                             <td > <?php echo esc_html($wphrmDepartmentInfo['departmentName']); ?> </td>
                                            <td style="text-align: left;"> <?php echo esc_html($designationInfo['designationName']); ?> </td>
                                            <td>
                                                <a class="btn purple" data-toggle="modal" href="#edit_static" onclick="designationEdit(<?php echo esc_js($wphrmDesignation->designationID); ?>, '<?php echo esc_js($designationInfo['designationName']); ?>')"> <i class="fa fa-edit"></i><?php _e('View/Edit', 'wphrm'); ?>  </a>
                                                <!--<a class="btn red" href="javascript:;" onclick="WPHRMCustomDelete(<?php //echo esc_js($wphrmDesignation->designationID); ?>, '<?php //echo esc_js($this->WphrmDesignationTable) ?>', 'designationID')"><i class="fa fa-trash"></i><?php //_e('Delete', 'wphrm'); ?> </a>-->
                                            </td>
                                        </tr>
                                    <?php $i++; }
                                else : ?>
                                    <tr>
                                        <td colspan="4"><?php _e('No designation found in database.', 'wphrm'); ?>
                                        </td><td class="collapse"></td><td class="collapse"></td><td class="collapse"></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END PAGE CONTENT-->