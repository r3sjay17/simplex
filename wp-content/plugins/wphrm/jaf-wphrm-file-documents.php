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
    <h3 class="page-title"><?php _e('Files & Documents', 'wphrm'); ?></h3>
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li><i class="fa fa-home"></i><?php _e('Home', 'wphrm'); ?><i class="fa fa-angle-right"></i></li>
            <li><?php _e('File and Documents', 'wphrm'); ?></li>
        </ul>
    </div>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-12">

            <div class="portlet box blue calendar">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-list"></i><?php _e('File & Folders', 'wphrm'); ?>
                    </div>
                </div>
                <div class="portlet-body">
                    <?php if (in_array('manageOptionsFileDocuments', $wphrmGetPagePermissions) || current_user_can('administrator')): ?>
                    <!--uploader-->

                    <div id="file-uploader-wrap" class="" >
                        <div id="progress" class="progress">
                            <div class="progress-bar progress-bar-success"></div>
                        </div>
                        <span class="btn btn-success fileinput-button">
                            <i class="glyphicon glyphicon-plus"></i>
                            <span>Select files...</span>
                            <!-- The file input field used as target for the file upload widget -->
                            <input id="fileupload" type="file" name="files[]" multiple>
                        </span>

                        <a class="btn btn-info" href="#" data-toggle="modal" data-target="#new-folder"><i class="fa fa-plus"></i><?php _e('New Folder', 'wphrm'); ?></a>
                    </div>
                    <script>
                    </script>

                    <?php endif; ?>
                    <div id="file-manager-wrapper" >
                        <div class="page-bar">
                            <ul class="page-breadcrumb folder-breadcrumb">
                                <li><i class="fa fa-folder-open"></i><?php _e('Root', 'wphrm'); ?></li>
                            </ul>
                        </div>
                        <div id="folder-file-container" >

                        </div>
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

                    <!--modal for deleting file-->
                    <div id="delete-file-folder" class="modal fade" role="dialog" >
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Are you sure you want to delete file?</h4>
                                </div>
                                <div class="modal-body">
                                    <p>Deleting the item will remove it permanently on the system. Folder with items will be move to its parent or root.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-danger delete-file" data-id="" data-type="" >DELETE</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--Modal for sharing file-->
                    <div id="share-file-modal" class="modal fade" role="dialog" >
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Please select to which Users/Department/Manager share this file.</h4>
                                </div>
                                <form id="form-share-file" accept-charset="UTF-8" enctype="multipart/form-data" >
                                    <input type="hidden" name="action" value="share_file" >
                                    <input type="hidden" name="file_id" value="" >
                                    <input type="hidden" name="file_type" value="" >
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="form-group">
                                                <label class="control-label col-md-3"><?php _e('User(s):', 'wphrm'); ?>  </label>
                                                <div class="col-md-9">
                                                    <p>
                                                        <select class="form-control select2me" name="share_to_user" id="share_to_user" multiple="yes" >
                                                            <?php $employee_managers =  get_users();?>
                                                            <?php foreach ($employee_managers as $key => $userdata): ?>
                                                            <?php $wphrmEmployeeFirstName = get_user_meta($userdata->data->ID, 'first_name', true); ?>
                                                            <?php $wphrmEmployeeLastName = get_user_meta($userdata->data->ID, 'last_name', true); ?>
                                                            <option value="<?php echo $userdata->ID; ?>"  > <?php (!empty($wphrmEmployeeFirstName) || !empty($wphrmEmployeeLastName)) ? _e($wphrmEmployeeFirstName. ' '.$wphrmEmployeeLastName, 'wphrm') : $userdata->display_name; ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </p>
                                                    <p class="description" >Please select one or more. CTRL + Click to deselect.</p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-3"><?php _e('Department(s):', 'wphrm'); ?>  </label>
                                                <div class="col-md-9">
                                                    <p>
                                                        <select class="form-control make-sumoselect2" name="share_to_department" id="share_to_department" multiple="yes" >
                                                            <?php
                                                            $wphrmDepartments = $wpdb->get_results("SELECT * FROM  $this->WphrmDepartmentTable");
                                                            foreach ($wphrmDepartments as $key => $wphrmDepartment):
                                                            $wphrmDepartmentInfo = unserialize(base64_decode($wphrmDepartment->departmentName));
                                                            ?>
                                                            <option value="<?php if (isset($wphrmDepartment->departmentID)) : echo esc_attr($wphrmDepartment->departmentID); endif; ?> " >
                                                                <?php if (isset($wphrmDepartmentInfo['departmentName'])) : echo esc_html($wphrmDepartmentInfo['departmentName']); endif; ?>
                                                            </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </p>
                                                    <p class="description" >Please select one or more. CTRL + Click to deselect.</p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-3"><?php _e('Share to all Super/Admin/Hr Managers:', 'wphrm'); ?>  </label>
                                                <div class="col-md-9">
                                                    <p>
                                                        <input type="checkbox" value="1" name="share_to_manager">
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-success share-file"  >SHARE</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(function($){
        var Jaf_WP_File_Manager = {};
        Jaf_WP_File_Manager.main_container = $('#file-manager-wrapper');
        Jaf_WP_File_Manager.folder_file_container = $('#folder-file-container');
        Jaf_WP_File_Manager.modal_delete = $('#delete-file-folder');
        Jaf_WP_File_Manager.modal_share = $('#share-file-modal');
        Jaf_WP_File_Manager.modal_new_folder = $('#new-folder');
        Jaf_WP_File_Manager.current_folder = 0;

        Jaf_WP_File_Manager.load_files = function(folder_term){
            var _this = this;

            $.post( ajaxurl, { action: 'jaf_load_files', folder_term : folder_term }, function(data){
                console.log('File list to be refresh:', data);
                if(data.status){
                    _this.folder_file_container.html(data.html);
                    _this.main_container.find('.folder-breadcrumb').html(data.ancestor);
                    _this.current_folder = folder_term;
                }else{
                    alert( data.message );
                }
            }, 'json' );
        };

        Jaf_WP_File_Manager.init = function(){
            var _this = this;
            _this.load_files(0);

            /*Load file handler*/
            this.main_container.on('click', '.file-element', function(){

                var file_type = $(this).data('type');
                var file_id = $(this).data('id');
                var file_url = $(this).data('url');

                //check if the file type is folder, then open it
                if(file_type == 'dir'){
                    _this.load_files(file_id);
                }else{ //downlad it
                    console.log(file_url);
                    window.open(file_url);
                }

            });

            /*Delete handler*/
            this.main_container.on('click', '.file-delete', function(){

                var file_type = $(this).attr('data-type');
                var file_id = $(this).attr('data-id');

                //set modal values
                _this.modal_delete.find('.delete-file').attr('data-type', file_type).attr('data-id', file_id);

            });
            _this.modal_delete.on('click', '.delete-file', function(){
                var _this_button = $(this);
                $.post(ajaxurl, {action: 'delete_file', file_type: $(this).attr('data-type'), file_id: $(this).attr('data-id') }, function(data){
                    if(data.success){
                        _this.load_files(_this.current_folder);
                    }
                    _this_button.attr('data-type', '').attr('data-id', '');
                    _this.modal_delete.modal('hide');
                }, 'json' );

            });

            /*Form share handler*/
            this.main_container.on('click', '.file-share', function(){

                var file_type = $(this).data('type');
                var file_id = $(this).data('id');
                var file_user = $(this).data('shared_user');
                var file_department = $(this).data('shared_department');
                var file_manager = $(this).data('shared_manager');

                //set modal values
                _this.modal_share.find('input[name="file_type"]').val(file_type);
                _this.modal_share.find('input[name="file_id"]').val(file_id);
                //set selected
                _this.modal_share.find('select[name="share_to_user"]').val(file_user.split(","));
                _this.modal_share.find('select[name="share_to_department"]').val(file_department.split(","));
                //_this.modal_share.find('select[name="share_to_manager"]').val(file_manager.split(","));
                _this.modal_share.find('input[name="share_to_manager"]').prop('checked', file_manager == '1' ? true : false);

            });
            _this.modal_share.on('submit', 'form', function(){
                var _this_form = $(this);
                /*$.post(ajaxurl, _this_form.serializeArray(), function(data){
                    if(data.success){
                        _this.load_files(_this.current_folder);
                    }
                    _this.modal_share.modal('hide');
                    _this.modal_share.find('input[name="file_type"]').val('');
                    _this.modal_share.find('input[name="file_id"]').val('');
                    _this.modal_share.find('select[name="share_to_user"]').val('');
                    _this.modal_share.find('select[name="share_to_department"]').val('');
                    _this.modal_share.find('select[name="share_to_manager"]').val('');
                }, 'json' );*/

                $(_this_form).ajaxSubmit({
                    url: ajaxurl,
                    type: 'POST',
                    dataType: 'json',
                    success: function(data){
                        if(data.success){
                            _this.load_files(_this.current_folder);
                        }
                        _this.modal_share.modal('hide');
                        _this.modal_share.find('input[name="file_type"]').val('');
                        _this.modal_share.find('input[name="file_id"]').val('');
                        _this.modal_share.find('select[name="share_to_user"]').val('');
                        _this.modal_share.find('select[name="share_to_department"]').val('');
                        _this.modal_share.find('select[name="share_to_manager"]').val('');
                    },
                });

                return false;
            });

            _this.modal_new_folder.on('submit', 'form', function(){
                $(this).find('[name="current_folder"]').val(Jaf_WP_File_Manager.current_folder);
                $.post(ajaxurl, $(this).serializeArray(), function(data){
                    if(data.status){
                        _this.modal_new_folder.modal('hide');
                        _this.load_files(get_current_folder());
                    }else{
                        alert(data.message);
                    }
                }, 'json' );

                return false;
            });

        };

        Jaf_WP_File_Manager.init();

        /*File uploader handler*/
        function get_current_folder(){
            return (Jaf_WP_File_Manager != undefined && Jaf_WP_File_Manager.current_folder != undefined) ? Jaf_WP_File_Manager.current_folder : 0;
        }
        var url = '<?php echo admin_url('admin-ajax.php'); ?>';
        $('#fileupload').fileupload({
            url: url,
            dataType: 'json',
            submit: function(e, data){
                $('#progress .progress-bar').css( 'width', '0%' );
                data.formData = {action: 'wphrm_upload_file', current_folder: get_current_folder() };
            },
            done: function (e, data) {
                /*$.each(data.result.files, function (index, file) {
                    $('<p/>').text(file.name).appendTo('#files');
                });*/
            },
            always: function(){ Jaf_WP_File_Manager.load_files(get_current_folder()); },
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('#progress .progress-bar').css(
                    'width',
                    progress + '%'
                );
            }
        }).prop('disabled', !$.support.fileInput)
            .parent().addClass($.support.fileInput ? undefined : 'disabled');

        /*New Folder ajax Handler*/
        /*$('#form-new-folder').submit(function(){
            $(this).find('[name="current_folder"]').val(Jaf_WP_File_Manager.current_folder);
            $.post(ajaxurl, $(this).serializeArray(), function(data){
                if(data.status){
                    Jaf_WP_File_Manager.modal_new_folder.modal('hide');
                    Jaf_WP_File_Manager.load_files(get_current_folder());
                }else{
                    alert(data.message);
                }
            }, 'json' );

            return false;
        });*/

    });
</script>
<!-- END PAGE CONTENT-->
