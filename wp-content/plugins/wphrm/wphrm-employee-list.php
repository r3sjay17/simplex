<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $current_user, $wpdb, $wp_query;
$wphrmCurrentuserId = $current_user->ID;
$wphrmUserRole = implode(',', $current_user->roles);
$wphrmUsers =  get_users();
$wphrmGetPagePermissions = $this->WPHRMGetPagePermissions();
$designations = $this->get_designation_list();

?>
<!-- BEGIN PAGE CONTENT-->
<div class="preloader">
    <span class="preloader-custom-gif"></span>
</div>
<div style="padding-left: 0px; padding-right:20px; padding-top:0px;" class="col-md-12">
    <!-- BEGIN PAGE HEADER-->
    <h3 class="page-title"><?php _e('Employees', 'wphrm'); ?></h3>
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li><i class="fa fa-home"></i><?php _e('Home', 'wphrm'); ?><i class="fa fa-angle-right"></i></li>
            <li><?php _e('Employees', 'wphrm'); ?></li>
        </ul>
    </div>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-12">
            <?php
            $employeePro = '';
            $employeeIcon = '';
            if (in_array('manageOptionsEmployee', $wphrmGetPagePermissions)) { 
            ?><br>
            <a class="btn green " href="?page=wphrm-employee-info" data-toggle="modal"><i class="fa fa-plus"></i><?php _e('Add New Employee', 'wphrm'); ?> </a>
            <?php if(!isset($_REQUEST['users'])){ ?>
            <a class="btn green " style="float: right;" href="?page=wphrm-employees&users=wphrmuser"><i class="fa fa-user"></i><?php _e('Show Inactive Users', 'wphrm'); ?> </a>
            <?php } else {?>
            <a class="btn green " style="float: right;" href="?page=wphrm-employees"><i class="fa fa-user"></i><?php _e('Hide Inactive Users', 'wphrm'); ?> </a>
            <?php } } else {
                $table = '';
                $employeePro = __('My Profile', 'wphrm');
                $employeeIcon = 'fa fa-edit';
            } ?>
                                
            <div class="portlet box blue calendar">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="<?php if (isset($employeeIcon) && $employeeIcon == '') : echo esc_attr('fa fa-list'); else : echo esc_attr($employeeIcon); endif; ?>"></i>
                        <?php if (isset($employeePro) && $employeePro == '') : _e('List of Employees', 'wphrm');
                        else : echo esc_html($employeePro); endif; ?>
                    </div>
                </div>
                <div class="portlet-body">

                    <?php  if (in_array('manageOptionsEmployee', $wphrmGetPagePermissions)): ?>
                    <div class="panel">
                        <form action="<?php echo admin_url('admin.php'); ?>" method="GET" class="form-inline" role="form">
                            <input type="hidden" name="page" value="wphrm-employees" >
                            <?php if(isset($_GET['users'])): ?>
                            <input type="hidden" name="users" value="<?php echo $_GET['users']; ?>" >
                            <?php endif; ?>
                            <div class="form-group">
                                <label class="sr-only" for="">Department</label>
                                <?php $emp_dep = array(); ?>
                                <?php $department_list = $this->get_department_list(); ?>
                                <?php $filtered_department = isset($_GET['filter_department']) ? $_GET['filter_department'] : ''; ?>
                                <select class="form-control" name="filter_department"  >
                                    <option value="" >- department</option>
                                    <?php 
                                    foreach($department_list as $key => $department):
                                        $deparment_info = $this->get_department_info($department->departmentID);
                                        $emp_dep[] = array( 'depID' => $department->departmentID, 'depName' => $deparment_info['departmentName'] );
                                    endforeach;

                                    usort($emp_dep, function ($a, $b) { return strnatcmp($a['depName'], $b['depName']); });
                                    foreach( $emp_dep as $sorted_dep ) {
                                    ?>

                                    <option value="<?php echo $sorted_dep['depID'] ?>" <?php selected( $sorted_dep['depID'], $filtered_department ); ?> ><?php echo $sorted_dep['depName']; ?></option>

                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="sr-only" for="">Designation</label>
                                <?php $emp_designation = array(); ?>
                                <?php $filtered_designation = isset($_GET['filter_designation']) ? $_GET['filter_designation'] : ''; ?>
                                <select name="filter_designation" >
                                    <option value="" >- designation</option>
                                    <?php foreach($designations as $designation): ?>
                                    <?php $designation_info = $this->get_designation_info($designation->designationID); ?>
                                    <?php $emp_designation[] = array( 'designationID' => $designation->designationID, 'designationName' => $designation_info['designationName'] ); ?>
                                    <?php endforeach; ?>

                                    <?php usort($emp_designation, function ($a, $b) { return strnatcmp($a['designationName'], $b['designationName']); }); ?>
                                    <?php foreach( $emp_designation as $sorted_des ) { ?>
                                    <option value="<?php echo $sorted_des['designationID']; ?>" <?php selected( $sorted_des['designationID'], $filtered_designation ); ?> ><?php echo $sorted_des['designationName']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="sr-only" for="">Employee Type</label>
                                <?php  $employee_levels = $this->get_employee_types(); ?>
                                <?php $selected_employee_level = isset($_GET['filter_employee_type']) ? $_GET['filter_employee_type'] : ''; ?>
                                <select class="form-control" name="filter_employee_type"  >
                                    <option value="" >- employee type</option>
                                    <?php foreach($employee_levels as $key => $employee_level): ?>
                                    <option value="<?php echo $key; ?>" <?php selected($key, $selected_employee_level); ?> ><?php echo $employee_level['title']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="sr-only" for="">Nationality</label>
                                <?php
                                $nationality_list = array();
                                foreach($wphrmUsers as $user){
                                    $info = $this->WPHRMGetUserDatas($user->ID, 'wphrmEmployeeWorkPermitInfo');
                                    if(!empty($info['wphrm_employee_nationality']) && !in_array($info['wphrm_employee_nationality'], $nationality_list))
                                        $nationality_list[] = $info['wphrm_employee_nationality'];
                                }
                                ?>
                                <?php $selected_nationality = isset($_GET['filter_nationality']) ? $_GET['filter_nationality'] : ''; ?>
                                <select class="form-control" name="filter_nationality"  >
                                    <option value="" >- nationality</option>
                                    <?php foreach($nationality_list as $key => $nationality): ?>
                                    <option value="<?php echo $nationality; ?>" <?php selected($nationality, $selected_nationality); ?> ><?php echo $nationality; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </form>
                    </div>
                    <?php endif; ?>

                    <table class="table table-striped table-bordered table-hover" id="wphrmDataTable">
                        <thead>
                            <tr>
                                <th><?php _e('S.No', 'wphrm'); ?></th>
                                <th><?php _e('Emp. ID', 'wphrm'); ?>  </th>
                                <th class="text-center"><?php _e('Name', 'wphrm'); ?>  </th>
                                <th><?php _e('Department', 'wphrm'); ?>  </th>
                                <th><?php _e('Designation', 'wphrm'); ?>  </th>
                                <th><?php _e('Email', 'wphrm'); ?>  </th>
                                <th><?php _e('Phone No.', 'wphrm'); ?> </th>
                                <th><?php _e('Date Joined', 'wphrm'); ?>  </th>
                                <th> <?php _e('Nationaltiy', 'wphrm'); ?></th>
                                <th> <?php _e('Status', 'wphrm'); ?></th>
                                <th><?php _e('Action', 'wphrm'); ?>  </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i=1;
                            $employeeDepartmentsLoads = '';
                            $wphrmDepartments = '';
                            $wphrmDepartmentInfo = '';


                            if(!empty($wphrmUsers)) {
                                if(!isset($_REQUEST['users'])){
                                    foreach ($wphrmUsers as $key => $userdata) {
                                        $wphrmEmployeeInfo = $this->WPHRMGetUserDatas($userdata->data->ID, 'wphrmEmployeeInfo');
                                        $wphrmEmployeePermit = $this->WPHRMGetUserDatas($userdata->ID, 'wphrmEmployeeWorkPermitInfo');

                                        $wphrmEmployeeFirstName = get_user_meta($userdata->data->ID, 'first_name', true);
                                        $wphrmEmployeeLastName = get_user_meta($userdata->data->ID, 'last_name', true);

                                        //do some page filtering
                                        if( (!empty($_GET['filter_department']) && isset($wphrmEmployeeInfo['wphrm_employee_department']) && ($wphrmEmployeeInfo['wphrm_employee_department'] != $_GET['filter_department']) ) or (!empty($_GET['filter_department'])) && empty($wphrmEmployeeInfo['wphrm_employee_department'])) continue;

                                        if( (!empty($_GET['filter_designation']) && isset($wphrmEmployeeInfo['wphrm_employee_designation']) && ($wphrmEmployeeInfo['wphrm_employee_designation'] != $_GET['filter_designation']) ) or (!empty($_GET['filter_designation'])) && empty($wphrmEmployeeInfo['wphrm_employee_designation'])) continue;

                                        if( (!empty($_GET['filter_employee_type']) && isset($wphrmEmployeeInfo['wphrm_employee_level']) && ($wphrmEmployeeInfo['wphrm_employee_level'] != $_GET['filter_employee_type']) ) or (!empty($_GET['filter_employee_type'])) && empty($wphrmEmployeeInfo['wphrm_employee_level'])) continue;

                                        if( (!empty($_GET['filter_nationality']) && isset($wphrmEmployeePermit['wphrm_employee_nationality']) && ($wphrmEmployeePermit['wphrm_employee_nationality'] != $_GET['filter_nationality']) ) or (!empty($_GET['filter_nationality'])) && empty($wphrmEmployeePermit['wphrm_employee_nationality'])) continue;

                                        if (isset($wphrmEmployeeInfo['wphrm_employee_status']) && $wphrmEmployeeInfo['wphrm_employee_status'] == 'Active'){
                                            if (isset($wphrmEmployeeInfo['wphrm_employee_department']) && $wphrmEmployeeInfo['wphrm_employee_department'] != '') {
                                                $employeeDepartmentsLoads = esc_sql($wphrmEmployeeInfo['wphrm_employee_department']); // esc
                                                $wphrmDepartments = $wpdb->get_row("SELECT * FROM  $this->WphrmDepartmentTable  where `departmentID` = '$employeeDepartmentsLoads'");
                                                if ($wphrmDepartments != '') {
                                                    $wphrmDepartmentInfo = unserialize(base64_decode($wphrmDepartments->departmentName));
                                                }
                                            }
                            ?>
                            <tr id="row">
                                <td><?php echo esc_html($i); ?></td>
                                <!--J@F-->
                                <td><?php echo empty($wphrmEmployeeInfo['wphrm_employee_userid']) ? '' : $wphrmEmployeeInfo['wphrm_employee_userid']; ?></td>
                                <!--J@F-->
                                <td class="text-center">
                                    <a href='?page=wphrm-employee-view-details&employee_id=<?php echo esc_attr($userdata->ID); ?>'>
                                        <?php
                                            if (isset($wphrmEmployeeFirstName)) : echo esc_html($wphrmEmployeeFirstName); endif; if (isset($wphrmEmployeeLastName)) : echo ' ' .esc_html( $wphrmEmployeeLastName); endif; ?>
                                    </a>
                                </td>
                                <td><?php
                                            if (isset($wphrmDepartmentInfo['departmentName'])) : echo esc_html($wphrmDepartmentInfo['departmentName']);
                                            endif; ?>
                                </td>
                                <td><?php
                                            if (isset($wphrmEmployeeInfo['wphrm_hidden_employee_designation'])) {
                                                $designation_info = $this->get_designation_info($wphrmEmployeeInfo['wphrm_hidden_employee_designation']);
                                                echo isset($designation_info['designationName']) ? $designation_info['designationName'] : '';
                                            }
                                    ?>
                                </td>
                                <td> <?php
                                            if (isset($wphrmEmployeeInfo['wphrm_employee_email'])) : echo esc_html($wphrmEmployeeInfo['wphrm_employee_email']);
                                            endif; ?>
                                </td>
                                <td><?php
                                            if (isset($wphrmEmployeeInfo['wphrm_employee_phone'])) : echo esc_html($wphrmEmployeeInfo['wphrm_employee_phone']);
                                            endif; ?>
                                </td>
                                <!--J@F-->
                                <td><?php echo empty($wphrmEmployeeInfo['wphrm_employee_joining_date']) ? '' : date('Y-m-d', strtotime($wphrmEmployeeInfo['wphrm_employee_joining_date'])); ?></td>
                                <td>
                                    <?php
                                        if(isset($wphrmEmployeePermit['wphrm_employee_nationality'])){
                                            echo $wphrmEmployeePermit['wphrm_employee_nationality'];
                                        }
                                    ?>
                                </td>
                                <!--J@F-->
                                <td><?php if (isset($wphrmEmployeeInfo['wphrm_employee_status']) && $wphrmEmployeeInfo['wphrm_employee_status'] == 'Active') { ?>
                                    <span class="label label-sm label-success"><?php _e('Active', 'wphrm'); ?></span>
                                    <?php } else { ?>
                                    <span class="label label-sm label-danger"><?php _e('Inactive', 'wphrm'); ?></span>
                                    <?php } ?>
                                </td>
                                <td class="">
                                    <a class="btn purple" href='?page=wphrm-employee-view-details&employee_id=<?php echo esc_attr($userdata->ID); ?>'>
                                        <i class="fa fa-eye"></i><?php _e('View', 'wphrm'); ?>
                                    </a>
                                    <a class="btn blue" href='?page=wphrm-employee-info&employee_id=<?php echo esc_attr($userdata->ID); ?>'>
                                        <i class="fa fa-edit"></i><?php _e('Edit', 'wphrm'); ?>
                                    </a>
                                </td>
                            </tr>
                            <?php $i++;
                                        }
                                    }
                                }else{
                                    foreach ($wphrmUsers as $key => $userdata) {
                                        $wphrmDepartmentInfo = '';
                                        $wphrmEmployeeInfo = $this->WPHRMGetUserDatas($userdata->data->ID, 'wphrmEmployeeInfo');
                                        $wphrmEmployeePermit = $this->WPHRMGetUserDatas($userdata->ID, 'wphrmEmployeeWorkPermitInfo');

                                        $wphrmEmployeeFirstName = get_user_meta($userdata->data->ID, 'first_name', true);
                                        $wphrmEmployeeLastName = get_user_meta($userdata->data->ID, 'last_name', true);

                                        if (isset($wphrmEmployeeInfo['wphrm_employee_department']) && $wphrmEmployeeInfo['wphrm_employee_department'] != '') {
                                            $employeeDepartmentsLoads = esc_sql($wphrmEmployeeInfo['wphrm_employee_department']); // esc
                                            $wphrmDepartments = $wpdb->get_row("SELECT * FROM  $this->WphrmDepartmentTable  where `departmentID` = '$employeeDepartmentsLoads'");
                                            if ($wphrmDepartments != '') {
                                                $wphrmDepartmentInfo = unserialize(base64_decode($wphrmDepartments->departmentName));
                                            }
                                        }

                                        //do some page filtering
                                        if( (!empty($_GET['filter_department']) && isset($wphrmEmployeeInfo['wphrm_employee_department']) && ($wphrmEmployeeInfo['wphrm_employee_department'] != $_GET['filter_department']) ) or (!empty($_GET['filter_department'])) && empty($wphrmEmployeeInfo['wphrm_employee_department'])) continue;

                                        if( (!empty($_GET['filter_designation']) && isset($wphrmEmployeeInfo['wphrm_employee_designation']) && ($wphrmEmployeeInfo['wphrm_employee_designation'] != $_GET['filter_designation']) ) or (!empty($_GET['filter_designation'])) && empty($wphrmEmployeeInfo['wphrm_employee_designation'])) continue;

                                        if( (!empty($_GET['filter_employee_type']) && isset($wphrmEmployeeInfo['wphrm_employee_level']) && ($wphrmEmployeeInfo['wphrm_employee_level'] != $_GET['filter_employee_type']) ) or (!empty($_GET['filter_employee_type'])) && empty($wphrmEmployeeInfo['wphrm_employee_level'])) continue;

                                        if( (!empty($_GET['filter_nationality']) && isset($wphrmEmployeePermit['wphrm_employee_nationality']) && ($wphrmEmployeePermit['wphrm_employee_nationality'] != $_GET['filter_nationality']) ) or (!empty($_GET['filter_nationality'])) && empty($wphrmEmployeePermit['wphrm_employee_nationality'])) continue;

                            ?>
                            <tr id="row">
                                <td><?php echo esc_html($i); ?></td>
                                <!--J@F-->
                                <td><?php echo empty($wphrmEmployeeInfo['wphrm_employee_userid']) ? '' : $wphrmEmployeeInfo['wphrm_employee_userid']; ?></td>
                                <!--J@F-->
                                <td class="text-center">
                                    <a href='?page=wphrm-employee-view-details&employee_id=<?php echo esc_attr($userdata->ID); ?>'>
                                        <?php
                                        if (isset($wphrmEmployeeFirstName) && $wphrmEmployeeFirstName !='') : echo esc_html($wphrmEmployeeFirstName);
                                        else : echo esc_html($userdata->data->user_nicename);
                                        endif;
                                        if (isset($wphrmEmployeeLastName) && $wphrmEmployeeLastName !='') : echo ' ' .esc_html($wphrmEmployeeLastName);
                                        else : echo '';
                                        endif; ?>
                                    </a>
                                </td>
                                <td><?php

                                        if (isset($wphrmDepartmentInfo['departmentName'])) : echo esc_html($wphrmDepartmentInfo['departmentName']);
                                        endif; ?>
                                </td>
                                <td><?php
                                            if (isset($wphrmEmployeeInfo['wphrm_hidden_employee_designation'])) {
                                                $designation_info = $this->get_designation_info($wphrmEmployeeInfo['wphrm_hidden_employee_designation']);
                                                echo isset($designation_info['designationName']) ? $designation_info['designationName'] : '';
                                            }
                                    ?>
                                </td>
                                <td> <?php
                                        if (isset($wphrmEmployeeInfo['wphrm_employee_email']) && $wphrmEmployeeInfo['wphrm_employee_email'] !='') : echo esc_html($wphrmEmployeeInfo['wphrm_employee_email']);
                                        else : echo esc_html($userdata->data->user_email);
                                        endif; ?>
                                </td>
                                <td><?php
                                        if (isset($wphrmEmployeeInfo['wphrm_employee_phone'])) : echo esc_html($wphrmEmployeeInfo['wphrm_employee_phone']);
                                        endif; ?>
                                </td>
                                <!--J@F-->
                                <td><?php echo empty($wphrmEmployeeInfo['wphrm_employee_joining_date']) ? '' : date('Y-m-d', strtotime($wphrmEmployeeInfo['wphrm_employee_joining_date'])); ?></td>
                                <td>
                                    <?php
                                        if(isset($wphrmEmployeePermit['wphrm_employee_nationality'])){
                                            echo $wphrmEmployeePermit['wphrm_employee_nationality'];
                                        }
                                    ?>
                                </td>
                                <!--J@F-->
                                <td><?php if (isset($wphrmEmployeeInfo['wphrm_employee_status']) && $wphrmEmployeeInfo['wphrm_employee_status'] == 'Active') { ?>
                                    <span class="label label-sm label-success"><?php _e('Active', 'wphrm'); ?></span>
                                    <?php } else { ?>
                                    <span class="label label-sm label-danger"><?php _e('Inactive', 'wphrm'); ?></span>
                                    <?php } ?>
                                </td>
                                <td class="">
                                    <a class="btn purple" href='?page=wphrm-employee-view-details&employee_id=<?php echo esc_attr($userdata->ID); ?>'>
                                        <i class="fa fa-eye"></i><?php _e('View', 'wphrm'); ?>
                                    </a>
                                    <a class="btn blue" href='?page=wphrm-employee-info&employee_id=<?php echo esc_attr($userdata->ID); ?>'>
                                        <i class="fa fa-edit"></i><?php _e('Edit', 'wphrm'); ?>
                                    </a>
                                </td>
                            </tr>
                            <?php $i++; }

                                }
                            }else { ?>
                            <tr>
                                <td colspan="7"><?php _e('No employees found in database.', 'wphrm'); ?>
                                </td><td class="collapse"></td><td class="collapse"></td><td class="collapse"></td><td class="collapse"></td><td class="collapse"></td><td class="collapse"></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
</div>
<!-- END PAGE CONTENT-->
