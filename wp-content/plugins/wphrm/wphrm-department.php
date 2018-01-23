<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb;
$readonly_class = ''; $readonly = '';
$edit_mode = false;
$wphrmMessagesDeparment = $this->WPHRMGetMessage(8);
$wphrmMessagesUpdateDeparment = $this->WPHRMGetMessage(9);
$wphrmMessagesDeleteDeparment = $this->WPHRMGetMessage(13);
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
                <h4 class="modal-title"><strong><i class="fa fa-plus"></i><?php _e('Add Department', 'wphrm'); ?></strong></h4>
            </div>
            <div class="modal-body">
                <div class="portlet-body form">
                    <div class="alert alert-success display-hide" id="wphrmDepartmentInfo_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrmMessagesDeparment); ?>
                        <button class="close" data-close="alert"></button>
                    </div>
                    <div class="alert alert-danger display-hide" id="wphrmDepartmentInfo_error">
                        <button class="close" data-close="alert"></button>
                    </div>
                    <!-- BEGIN FORM-->
                    <form method="POST"  accept-charset="UTF-8" class="form-horizontal department_frm" id="edit_form">
                        <div class="form-body">
                            <div class="form-group">
                                <div class="col-md-10">
                                    <input class="form-control form-control-inline " name="departmentName[]" id="department_name" type="text" value="" placeholder="<?php _e('Department Name', 'wphrm'); ?>" />
                                </div>
                            </div>
                        </div>
                        <div id="insertBeforeDepartment"></div>
                            <div class="form-group">
                                  <div class="col-md-10">
                                  <button type="button" id="plusButtonDepartment" class="btn btn-sm green form-control-inline">
                                <i class="fa fa-plus"></i><?php _e('Add More', 'wphrm'); ?>
                            </button>
                                  </div></div>
                        <div class="form-group">
                                <div class="col-md-10">          
                                <button type="submit"  class="btn blue"><i class="fa fa-plus"></i><?php _e('Add Department', 'wphrm'); ?></button>
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
                <h4 class="modal-title"><strong><i class="fa fa-edit"></i><?php _e('Rename Department', 'wphrm'); ?></strong></h4>
            </div>
            <div class="modal-body">
                <div class="portlet-body form">
                    <div class="alert alert-success display-hide" id="wphrm_Edepartment_info_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrmMessagesUpdateDeparment); ?>
                        <button class="close" data-close="alert"></button>
                    </div>
                    <div class="alert alert-danger display-hide" id="wphrm_Edepartment_info_error">
                        <button class="close" data-close="alert"></button>
                    </div>
                    <!-- BEGIN FORM-->
                    <form method="POST"  accept-charset="UTF-8" class="form-horizontal wphrm_edit_department" id="edit_form">
                        <div class="form-body">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <input class="form-control form-control-inline " name="editdepartment_name" id="editdepartment_name" type="text" value="" placeholder="<?php _e('Department Name', 'wphrm'); ?>" />
                                </div>
                            </div>
                        </div>
                          <div class="form-group">
                                <div class="col-md-12">                                    
                                    <button type="submit"  class="btn blue"><i class="fa fa-edit"></i><?php _e('Rename', 'wphrm'); ?><?php _e('Department', 'wphrm'); ?></button>
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
            <div class="alert alert-success display-hide" id="WPHRMCustomDelete_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrmMessagesUpdateDeparment); ?>
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
<div style="padding-left: 0px; padding-right:20px; padding-top:0px;"  class="col-md-12">
    <!-- BEGIN PAGE HEADER-->
    <h3 class="page-title"><?php _e('Departments', 'wphrm'); ?></h3>
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li><i class="fa fa-home"></i><?php _e('Home', 'wphrm'); ?><i class="fa fa-angle-right"></i></li>
            <li><?php _e('Departments', 'wphrm'); ?></li>
        </ul>
    </div>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-12">
            <a class="btn green " href="#add_static" data-toggle="modal"><i class="fa fa-plus"></i><?php _e('Add New Department', 'wphrm'); ?></a>
            <div class="portlet box blue calendar">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-list"></i><?php _e('List of Departments', 'wphrm'); ?>
                    </div>
                </div>
                <div class="portlet-body department-table">
                    <table class="wphrmtable table table-striped table-bordered table-hover" id="wphrmDataTable">
                        <thead>
                            <tr>
                                <th><?php _e('S.No', 'wphrm'); ?></th>
                                <th><?php _e('Department Name', 'wphrm'); ?></th>
                                <th ><?php _e('Designations', 'wphrm'); ?></th>
                                <th><?php _e('Actions', 'wphrm'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i=1;
                            $wphrmDepartments = $wpdb->get_results("SELECT * FROM $this->WphrmDepartmentTable ORDER BY departmentName ASC");
                            if(!empty($wphrmDepartments)) :
                                foreach ($wphrmDepartments as $key => $wphrmDepartment) {
                                    $wphrmDepartmentInfo = unserialize(base64_decode($wphrmDepartment->departmentName));
                                     $wphrmDepartmentID = esc_sql($wphrmDepartment->departmentID); // esc
                                    $wphrmDesignations = $wpdb->get_results("SELECT * FROM $this->WphrmDesignationTable WHERE departmentID =$wphrmDepartmentID "); ?>
                                    <tr>
                                        <td><?php echo esc_html($i); ?></td>
                                        <td><?php if (isset($wphrmDepartmentInfo['departmentName'])) : echo esc_html($wphrmDepartmentInfo['departmentName']); endif; ?> </td>
                                        <td>
                                            <table class="leaves">
                                            <?php $wphrm_designationNo=1;
                                            foreach($wphrmDesignations as $wphrmDesignation) {
                                                $designation_info = unserialize(base64_decode($wphrmDesignation->designationName));
                                                ?><tr>
                                                    <td ><?php echo esc_html($wphrm_designationNo).') '; ?></td>
                                                    <td style="float: left;"><?php echo  esc_html($designation_info['designationName']); ?></td>
                                                </tr> <?php $wphrm_designationNo++;
                                            } ?>
                                            </table>
                                        </td>
                                        <td>
                                            <a class="btn blue" href="?page=wphrm-add-designation&departmentID=<?php if (isset($wphrmDepartmentID)) : echo esc_attr($wphrmDepartmentID); endif; ?>&department_name=<?php if (isset($wphrmDepartmentInfo['departmentName'])) : echo esc_attr($wphrmDepartmentInfo['departmentName']); endif; ?>">
                                                <i class="fa fa-list"></i><?php _e('Designations', 'wphrm'); ?>
                                            </a>
                                            <a class="btn purple" data-toggle="modal" href="#edit_static" onclick="departmentEdit(<?php if (isset($wphrmDepartmentID)) : echo esc_js($wphrmDepartment->departmentID); endif; ?>,'<?php if (isset($wphrmDepartmentInfo['departmentName'])) : echo esc_js($wphrmDepartmentInfo['departmentName']); endif; ?>')">
                                                <i class="fa fa-edit"></i><?php _e('Rename', 'wphrm'); ?>
                                            </a>
                                            <a class="btn red" href="javascript:;" onclick="WPHRMCustomDelete(<?php if (isset($wphrmDepartmentID)) : echo esc_js($wphrmDepartmentID); endif; ?>, '<?php echo  trim(esc_js($this->WphrmDepartmentTable)); ?>', 'departmentID')">
                                                <i class="fa fa-trash"></i><?php _e('Delete', 'wphrm'); ?>
                                            </a>
                                        </td>
                                    </tr>
                            <?php $i++; }
                            else : ?>
                                <tr>
                                    <td colspan="4"><?php _e('No departments found in database.', 'wphrm'); ?>
                                    </td><td class="collapse"></td><td class="collapse"></td><td class="collapse"></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END PAGE CONTENT-->