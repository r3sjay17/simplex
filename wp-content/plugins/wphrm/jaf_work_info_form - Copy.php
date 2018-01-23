<?php
$employee_managers =  get_users();
$department = '';
$employee_id = (isset($wphrmEmployeeEditId)) ? esc_attr($wphrmEmployeeEditId) : '';
?>

<?php if (in_array('manageOptionsEmployee', $wphrmGetPagePermissions)): ?>
<?php
$countries = $this->get_country_list( array( 'country' => $this->get_simplex_countries() ) );
$employment_coutry = empty($wphrmEmployeeBasicInfo['wphrm_employee_employment_country']) ? '' : $wphrmEmployeeBasicInfo['wphrm_employee_employment_country'];
?>

<div class="form-group">
    <label class="control-label col-md-4"><?php _e('Country of Employment', 'wphrm'); ?></label>
    <div class="col-md-8" >
        <select class="form-control " id="wphrm_employee_employment_country" name="wphrm_employee_employment_country" required >
            <option value="" >Select Country</option>
            <?php foreach($countries as $country): ?>
            <option value="<?php echo $country->code ?>" <?php selected($country->code, $employment_coutry) ?> ><?php echo $country->name; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<div class="form-group hide">
    <label class="control-label col-md-4"><?php _e('Employee Id', 'wphrm'); ?>
    </label>
    <div class="col-md-8">
        <input  name="wphrm_employee_uniqueid" required type="text" id="wphrm_employee_uniqueid"  value="<?php if (isset($wphrmEmployeeBasicInfo['wphrm_employee_uniqueid'])) : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_uniqueid']); endif; ?>" class="form-control" />
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-4"><?php _e('Employee Status', 'wphrm'); ?>
    </label>
    <div class="col-md-8">
        <div class="radio-list" data-error-container="#form_2_membership_error">
            <?php $wphrm_employee_status = empty($wphrmEmployeeBasicInfo['wphrm_employee_status']) ? 'Active' : $wphrmEmployeeBasicInfo['wphrm_employee_status']; ?>
            <input  name="wphrm_employee_status" required type="radio" <?php checked($wphrm_employee_status, 'Active') ?> id="wphrm_employee_status" value="Active" class="icheck" >&nbsp;Active &nbsp;&nbsp;
            <input  name="wphrm_employee_status" required type="radio" id="wphrm_employee_status" <?php checked($wphrm_employee_status, 'Inactive') ?> value="Inactive" class="icheck" >&nbsp;<?php _e('Inactive', 'wphrm'); ?>
        </div>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-4"><?php _e('Department', 'wphrm'); ?>  </label>
    <div class="col-md-8">
        <?php
            $selected = '';
            $wphrmDepartments = $wpdb->get_results("SELECT * FROM $this->WphrmDepartmentTable");
        ?>
        <select class="form-control select2me" name="wphrm_employee_department" id="wphrm_employee_department" required >
            <option value=""> <?php _e('Select Department', 'wphrm'); ?></option>
            <?php
            foreach ($wphrmDepartments as $key => $wphrmDepartment) {

                if (in_array($wphrmDepartment->departmentID, $wphrmDesignationarr)) {
                    $wphrmDepartmentInfo = unserialize(base64_decode($wphrmDepartment->departmentName));
                    if (intval($wphrmEmployeeBasicInfo['wphrm_employee_department']) == intval($wphrmDepartment->departmentID)) {
                        $selected = 'selected';
                        $department = $wphrmEmployeeBasicInfo['wphrm_employee_department'];
                    } else {
                        $selected = '';
                    }
            ?>
            <?php echo esc_attr($wphrmDepartment->departmentID); ?>
            <option value="<?php
                    if (isset($wphrmDepartment->departmentID)) : echo esc_attr($wphrmDepartment->departmentID);
                    endif;
                           ?> "<?php echo esc_attr($selected); ?>><?php
                    if (isset($wphrmDepartmentInfo['departmentName'])) : echo esc_html($wphrmDepartmentInfo['departmentName']);
                    endif;
                ?></option>
            <?php
                }
            }
            ?>
        </select>
    </div>
</div>

<div class="form-group">
    <?php if (isset($wphrmEmployeeBasicInfo['wphrm_employee_designation'])) : $designation = esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_designation']); endif; ?>
    <input type="hidden" name="wphrm_ajax_employee_designation" id="wphrm_ajax_employee_designation" value="<?php echo $designation ?>">
    <input type="hidden" name="wphrm_hidden_employee_designation" id="wphrm_hidden_employee_designation" value="<?php echo $designation ?>">
    <label class="control-label col-md-4"><?php _e('Designation', 'wphrm'); ?>
    </label>
    <div class="col-md-8">
        <select class="form-control select2me" name="wphrm_employee_designation" id="wphrm_select_employee_designation" >
            <option value=""> <?php _e('Select Designation', 'wphrm'); ?></option>
            <?php
            $wphrmDesignation = $wpdb->get_results( "SELECT * FROM $this->WphrmDesignationTable" );
            foreach( $wphrmDesignation as $designations ) {
                $designationName = unserialize(base64_decode($designations->designationName));
                $department = $wphrmEmployeeBasicInfo['wphrm_employee_department'];
                if( $department == $designations->departmentID ) {
                    $hide = '';
                } else {
                    $hide = 'hide';
                }
                echo '<option value="'.$designations->designationID.'" class="dep-'.$designations->departmentID.' '.$hide.'" '.selected( $designation, $designations->designationID ).' data-department="'.$designations->departmentID.'">'.$designationName['designationName'].'</option>';
            }
            ?>
        </select>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-4"><?php _e('Employee Type', 'wphrm'); ?>  </label>
    <div class="col-md-8">
        <?php  $employee_levels = $this->get_employee_types(); ?>
        <?php $selected_employee_level = isset($wphrmEmployeeBasicInfo['wphrm_employee_level']) ? $wphrmEmployeeBasicInfo['wphrm_employee_level'] : ''; ?>
        <select class="form-control select2me" name="wphrm_employee_level" id="wphrm_employee_level" required >
            <option value=""> <?php _e('-- not set --', 'wphrm'); ?></option>
            <?php foreach($employee_levels as $key => $employee_level): ?>
            <option value="<?php echo $key; ?>" <?php selected($key, $selected_employee_level); ?> ><?php echo $employee_level['title']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-4"><?php _e('Reporting Manager(s)', 'wphrm');?>  </label>
    <div class="col-md-8">        
        <?php $reporting_manager = isset($wphrmEmployeeBasicInfo['wphrm_employee_reporting_manager']) && is_array($wphrmEmployeeBasicInfo['wphrm_employee_reporting_manager']) ? $wphrmEmployeeBasicInfo['wphrm_employee_reporting_manager'] : array(); ?>
        <select class="form-control make-sumoselect" name="wphrm_employee_reporting_manager[]" id="wphrm_employee_reporting_manager" multiple=yes >
            <?php //foreach ($employee_managers as $key => $userdata):?>
            <?php //if(in_array( 'administrator', array_keys($userdata->caps)) || $employee_id == $userdata->data->ID) continue; ?>
            <?php //$wphrmEmployeeFirstName = get_user_meta($userdata->data->ID, 'first_name', true); ?>
            <?php //$wphrmEmployeeLastName = get_user_meta($userdata->data->ID, 'last_name', true); ?>
            <!--<option value="<?php //echo $userdata->ID; ?>" <?php //selected( in_array($userdata->ID, $reporting_manager), true); ?>  > <?php //(!empty($wphrmEmployeeFirstName) || !empty($wphrmEmployeeLastName)) ? _e($wphrmEmployeeFirstName. ' '.$wphrmEmployeeLastName, 'wphrm') : $userdata->display_name; ?></option>-->
            <?php //endforeach; ?>
        
        <?php
        
        if(!empty($employee_managers)) { 
            foreach ($employee_managers as $key => $userdata) {
                $wphrmEmployeeInfo = $this->WPHRMGetUserDatas($userdata->data->ID, 'wphrmEmployeeInfo');
                $wphrmEmployeePermit = $this->WPHRMGetUserDatas($userdata->ID, 'wphrmEmployeeWorkPermitInfo');
                
                //do some page filtering
                if( $userdata->ID != $employee_id ) { //echo '<option>'.intval($wphrmEmployeeInfo['wphrm_employee_department']).'</option>';
                    if( $wphrmEmployeeInfo['wphrm_employee_approving_officer'] == 'Yes' && $wphrmEmployeeInfo['wphrm_employee_status'] == 'Active' ) {
                        $wphrmEmployeeFirstName = get_user_meta($userdata->data->ID, 'first_name', true);
                        $wphrmEmployeeLastName = get_user_meta($userdata->data->ID, 'last_name', true);
                    ?>  

                        <option value="<?php echo $userdata->ID; ?>" <?php selected( in_array($userdata->ID, $reporting_manager), true); ?>  > <?php (!empty($wphrmEmployeeFirstName) || !empty($wphrmEmployeeLastName)) ? _e($wphrmEmployeeFirstName. ' '.$wphrmEmployeeLastName, 'wphrm') : $userdata->display_name; ?></option>

                    <?php
                    } else {
                    /*if( intval($wphrmEmployeeBasicInfo['wphrm_employee_department']) == intval($wphrmEmployeeInfo['wphrm_employee_department']) && $wphrmEmployeeInfo['wphrm_employee_status'] == 'Active' ) {
                        $wphrmEmployeeFirstName = get_user_meta($userdata->data->ID, 'first_name', true);
                        $wphrmEmployeeLastName = get_user_meta($userdata->data->ID, 'last_name', true);
                    ?>  

                        <option value="<?php echo $userdata->ID; ?>" <?php selected( in_array($userdata->ID, $reporting_manager), true); ?>  > <?php (!empty($wphrmEmployeeFirstName) || !empty($wphrmEmployeeLastName)) ? _e($wphrmEmployeeFirstName. ' '.$wphrmEmployeeLastName, 'wphrm') : $userdata->display_name; ?></option>

                    <?php
                    }*/
                        if( $wphrmEmployeeBasicInfo['wphrm_employee_level'] == 'senior_manager' ) {
                            if( $wphrmEmployeeInfo['wphrm_employee_department'] == 4 && $wphrmEmployeeInfo['wphrm_employee_status'] == 'Active' || $wphrmEmployeeInfo['wphrm_employee_designation'] == 14 && $wphrmEmployeeInfo['wphrm_employee_status'] == 'Active' || $wphrmEmployeeInfo['wphrm_employee_designation'] == 19 && $wphrmEmployeeInfo['wphrm_employee_status'] == 'Active' ) {
                                $wphrmEmployeeFirstName = get_user_meta($userdata->data->ID, 'first_name', true);
                                $wphrmEmployeeLastName = get_user_meta($userdata->data->ID, 'last_name', true);
                                    ?>

                                    <option value="<?php echo $userdata->ID; ?>" <?php selected( in_array($userdata->ID, $reporting_manager), true); ?>  > <?php if( $wphrmEmployeeInfo['wphrm_employee_department'] == 4 && $wphrmEmployeeInfo['wphrm_employee_status'] == 'Active' ) { echo 'Administrator'; } else { (!empty($wphrmEmployeeFirstName) || !empty($wphrmEmployeeLastName)) ? _e($wphrmEmployeeFirstName. ' '.$wphrmEmployeeLastName, 'wphrm') : $userdata->display_name; } ?></option>

                                    <?php 
                            }
                        } elseif( $wphrmEmployeeBasicInfo['wphrm_employee_level'] == 'manager' || $wphrmEmployeeBasicInfo['wphrm_employee_level'] == 'supervisor' ) {
                            if( $wphrmEmployeeInfo['wphrm_employee_department'] == 4 && $wphrmEmployeeInfo['wphrm_employee_status'] == 'Active' || $wphrmEmployeeInfo['wphrm_employee_level'] == 'senior_manager' && $wphrmEmployeeInfo['wphrm_employee_status'] == 'Active' ) {
                                $wphrmEmployeeFirstName = get_user_meta($userdata->data->ID, 'first_name', true);
                                $wphrmEmployeeLastName = get_user_meta($userdata->data->ID, 'last_name', true);
                            ?>

                                    <option value="<?php echo $userdata->ID; ?>" <?php selected( in_array($userdata->ID, $reporting_manager), true); ?>  > <?php if( $wphrmEmployeeInfo['wphrm_employee_department'] == 4 && $wphrmEmployeeInfo['wphrm_employee_status'] == 'Active' ) { echo 'Administrator'; } else { (!empty($wphrmEmployeeFirstName) || !empty($wphrmEmployeeLastName)) ? _e($wphrmEmployeeFirstName. ' '.$wphrmEmployeeLastName, 'wphrm') : $userdata->display_name; } ?></option>
                            <?php 
                            }
                        } else {
                            /*if( (!empty($_GET['filter_department']) && isset($wphrmEmployeeInfo['wphrm_employee_department']) && ($wphrmEmployeeInfo['wphrm_employee_department'] != $_GET['filter_department']) ) or (!empty($_GET['filter_department'])) && empty($wphrmEmployeeInfo['wphrm_employee_department'])) continue;

                            if( (!empty($_GET['filter_designation']) && isset($wphrmEmployeeInfo['wphrm_employee_designation']) && ($wphrmEmployeeInfo['wphrm_employee_designation'] != $_GET['filter_designation']) ) or (!empty($_GET['filter_designation'])) && empty($wphrmEmployeeInfo['wphrm_employee_designation'])) continue;

                            if( (!empty($_GET['filter_employee_type']) && isset($wphrmEmployeeInfo['wphrm_employee_level']) && ($wphrmEmployeeInfo['wphrm_employee_level'] != $_GET['filter_employee_type']) ) or (!empty($_GET['filter_employee_type'])) && empty($wphrmEmployeeInfo['wphrm_employee_level'])) continue;

                            if( (!empty($_GET['filter_nationality']) && isset($wphrmEmployeePermit['wphrm_employee_nationality']) && ($wphrmEmployeePermit['wphrm_employee_nationality'] != $_GET['filter_nationality']) ) or (!empty($_GET['filter_nationality'])) && empty($wphrmEmployeePermit['wphrm_employee_nationality'])) continue;*/

                            if (isset($wphrmEmployeeInfo['wphrm_employee_status']) && $wphrmEmployeeInfo['wphrm_employee_status'] == 'Active'){
                                if (isset($wphrmEmployeeInfo['wphrm_employee_department']) && $wphrmEmployeeInfo['wphrm_employee_department'] != '') {
                                    $employeeDepartmentsLoads = esc_sql($wphrmEmployeeInfo['wphrm_employee_department']); // esc
                                    $departments = $wpdb->get_row("SELECT * FROM  $this->WphrmDepartmentTable  where `departmentID` = '$department'");
                                    if ($departments != '') {
                                        $dep = unserialize(base64_decode($departments->departmentName));
                                        if( $wphrmEmployeeInfo['wphrm_employee_designation'] == 6 && $wphrmEmployeeInfo['wphrm_employee_status'] == 'Active' || $wphrmEmployeeInfo['wphrm_employee_department'] == $department && $wphrmEmployeeInfo['wphrm_employee_status'] == 'Active' || $wphrmEmployeeInfo['wphrm_employee_department'] == 4 && $wphrmEmployeeInfo['wphrm_employee_status'] == 'Active' ) {
                                            $wphrmEmployeeFirstName = get_user_meta($userdata->data->ID, 'first_name', true);
                                            $wphrmEmployeeLastName = get_user_meta($userdata->data->ID, 'last_name', true);
                            ?>

                                            <option value="<?php echo $userdata->ID; ?>" <?php selected( in_array($userdata->ID, $reporting_manager), true); ?>  > <?php (!empty($wphrmEmployeeFirstName) || !empty($wphrmEmployeeLastName)) ? _e($wphrmEmployeeFirstName. ' '.$wphrmEmployeeLastName, 'wphrm') : $userdata->display_name; ?></option>
                            <?php 
                                        }
                                    }
                                }
                            }
                        }// checking if manager
                    }//if approving officer
                }
            }
        }
        
        ?>
        </select>
    </div>
</div>
<!--J@F END-->


<div class="form-group">
    <label class="control-label col-md-4"><?php _e('Joining Date', 'wphrm'); ?></label>
    <div class="col-md-6">
        <div class="input-group input-medium date before-current-date"  data-date-format="dd-mm-yyyy" data-date-viewmode="years">
            <input class="form-control"  name="wphrm_employee_joining_date" type="text" id="wphrm_employee_joining_date" value="<?php if (isset($wphrmEmployeeBasicInfo['wphrm_employee_joining_date'])) : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_joining_date']); endif; ?>" autocapitalize="none"  />
            <span class="input-group-btn">
                <button class="btn default-date" type="button"><i class="fa fa-calendar" style="line-height: 1.9;"></i></button>
            </span>
        </div>
    </div>
</div>

<!--J@F-->
<div class="form-group">
    <label class="control-label col-md-4"><?php _e('Leaving Date', 'wphrm'); ?></label>
    <div class="col-md-6">
        <div class="input-group input-medium date after-current-date"  data-date-format="dd-mm-yyyy" data-date-viewmode="years">
            <input class="form-control date-pickers"  name="wphrm_employee_leaving_date" type="text" id="wphrm_employee_leaving_date" value="<?php if (isset($wphrmEmployeeBasicInfo['wphrm_employee_leaving_date'])) : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_leaving_date']); endif; ?>" autocapitalize="none"  />
            <span class="input-group-btn">
                <button class="btn default-date" type="button"><i class="fa fa-calendar" style="line-height: 1.9;"></i></button>
            </span>
        </div>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-md-4"><?php _e('Probation Period', 'wphrm'); ?></label>
    <div class="col-md-6">
        <select class="form-control select2me" name="wphrm_employee_probation_period" id="wphrm_employee_probation_period">
            <?php $current_probatio_period = empty($wphrmEmployeeBasicInfo['wphrm_employee_probation_period']) ? 3 : $wphrmEmployeeBasicInfo['wphrm_employee_probation_period']; ?>
            <?php foreach ($employee_probition_periods as $period): ?>
            <option value="<?php echo $period; ?>" <?php selected($period, $current_probatio_period ); ?>  > <?php echo $period; ?> Months</option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<?php if( $selected_employee_level == 'supervisor' || $selected_employee_level == 'staff' ) { ?>
<div class="form-group">
    <label class="control-label col-md-4">
        <p><?php _e('Carry Over Annual Leaves', 'wphrm'); ?></p>
    </label>
    <div class="col-md-12">
        <div class="table-responsive" style="max-height: 200px;" >
            <?php $wphrm_employee_leave_carried = isset($wphrmEmployeeBasicInfo['wphrm_employee_leave_carried']) ? $wphrmEmployeeBasicInfo['wphrm_employee_leave_carried'] : array(); ?>
            <table class="table table-condensed table-bordered">
                <thead>
                    <tr>
                        <th>Approved</th>
                        <th>Year Carried From</th>
                        <th>Count</th>
                        <th>Expiry</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>                    
                    <?php
                    $wphrmEmployeeJoiningDate = '';
                    if (isset($wphrmEmployeeBasicInfo['wphrm_employee_joining_date'])) {
                        $wphrmEmployeeJoiningDate = $wphrmEmployeeBasicInfo['wphrm_employee_joining_date'];
                    }
                    $wphrmEmployeeJoiningDate = new DateTime($wphrmEmployeeJoiningDate);
                    ?>
                    <?php if($wphrmEmployeeJoiningDate->format('Y') < date('Y')): ?>
                    <?php for($i = date('Y') - 1; $i >= $wphrmEmployeeJoiningDate->format('Y'); $i--): ?>
                    
                    <?php
                    foreach($wphrmEmployeeBasicInfo['wphrm_employee_entitled_leave'] as $entitled_leave): 
                    $leave_title = $this->get_leave_info($entitled_leave, 'leaveType');
                    if(empty($leave_title)) continue;

                    if( $leave_title == 'Annual Leave' || $leave_title == 'annual leave' || $leave_title == 'Annual leave' || $leave_title == 'ANNUAL LEAVE' ) {
                        if (isset($wphrmEmployeeBasicInfo['wphrm_employee_joining_date'])) {
                            $wphrmEmployeeJoiningDate = $wphrmEmployeeBasicInfo['wphrm_employee_joining_date'];
                        }
                        $wphrmEmployeeJoiningDate = new DateTime($wphrmEmployeeJoiningDate);
                        $today = new DateTime();
                        $interval = $today->diff($wphrmEmployeeJoiningDate);
                        $wphrmEmployeeJoiningToCurrentTotalYear = ((int) $interval->format('%y years') + 1);

                        $emp = $this->get_user_complete_info( $employee_id );
                        $leveltype = $emp->basic_info->wphrm_employee_level;
                        $leave_entitlement_count = $wpdb->get_var("SELECT COUNT(*) FROM  $this->WphrmEmployeeLevelTable WHERE years_of_service = $yos");
                        if( $leave_entitlement_count > 0 ) {
                            $leave_entitlement = $wpdb->get_row("SELECT * FROM  $this->WphrmEmployeeLevelTable WHERE years_of_service = $yos");
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
                        }
                        
                        $used_leave = $this->get_used_leave($employee_id, $entitled_leave, $i);
                        if( $max_leave > $used_leave ) {
                            $remaining_leave = $max_leave - $used_leave;
                            if( $remaining_leave > 5 ) {
                                $remaining_leave = 5;
                            } 
                        }
                    }
                    endforeach;
                    ?>
                    
                    <tr>
                        <td><input type="checkbox" value="yes" <?php checked( $wphrm_employee_leave_carried[$i]['can_carry_over'], 'yes'); ?> name="wphrm_employee_leave_carried[<?php echo $i; ?>][can_carry_over]" autocomplete="off" class="icheck" ></td>
                        <td><input type="text" value="<?php echo $i; ?>" name="wphrm_employee_leave_carried[<?php echo $i; ?>][year]" readonly  autocomplete="off" ></td>
                        <td><input type="number" value="<?php /*echo $remaining_leave;*/echo isset($wphrm_employee_leave_carried[$i]['count']) ? $wphrm_employee_leave_carried[$i]['count'] : ''; ?>" min="0" name="wphrm_employee_leave_carried[<?php echo $i; ?>][count]" autocomplete="off" ></td>
                        <td><input type="text" class="date-pickers" data-format='m/d/y' value="<?php echo !empty($wphrm_employee_leave_carried[$i]['expiry']) ? $wphrm_employee_leave_carried[$i]['expiry'] : date('m/d/'.($i + 1)); ?>" name="wphrm_employee_leave_carried[<?php echo $i; ?>][expiry]" autocomplete="off" ></td>
                        <td><a class="btn tiny calculate-unused-annual-leave" data-year="<?php echo $i; ?>" data-user_id="<?php echo $wphrmEmployeeEditId; ?>" title="Calculate this year unused leave(s)">?</a></td>
                    </tr>
                    <?php endfor; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan=5 ><p class="description">Just Started this year, cannot give a carry over leave.</p></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if( $wphrmEmployeeBasicInfo['wphrm_employee_gender'] == "Female" ) { ?>
<div class="form-group">
    <label class="control-label col-md-4">
        <p><?php _e('Carry Over Maternity Leaves', 'wphrm'); ?></p>
    </label>
    <div class="col-md-12">
        <div class="table-responsive" style="max-height: 200px;" >
            <?php $wphrm_employee_maternity_carried = isset($wphrmEmployeeBasicInfo['wphrm_employee_maternity_carried']) ? $wphrmEmployeeBasicInfo['wphrm_employee_maternity_carried'] : array(); ?>
            <table class="table table-condensed table-bordered">
                <thead>
                    <tr>
                        <!--<th>Approved</th>-->
                        <th>Year Carried From</th>
                        <th>Count</th>
                        <th>Expiry</th>
                        <!--<th></th>-->
                    </tr>
                </thead>
                <tbody>                    
                    <?php
                    $wphrmEmployeeJoiningDate = '';
                    if (isset($wphrmEmployeeBasicInfo['wphrm_employee_joining_date'])) {
                        $wphrmEmployeeJoiningDate = $wphrmEmployeeBasicInfo['wphrm_employee_joining_date'];
                    }
                    $wphrmEmployeeJoiningDate = new DateTime($wphrmEmployeeJoiningDate);
                    ?>
                    <?php if($wphrmEmployeeJoiningDate->format('Y') < date('Y')): ?>
                    <?php for($j = date('Y') - 1; $j >= $wphrmEmployeeJoiningDate->format('Y'); $j--): ?>
                    
                    <?php
                    $carried = false;
                    foreach($wphrmEmployeeBasicInfo['wphrm_employee_entitled_leave'] as $entitled_leave): 
                    $leave_title = $this->get_leave_info($entitled_leave, 'leaveType');
                    if(empty($leave_title)) continue;

                    if( $entitled_leave == 45 || $entitled_leave == 48 || $entitled_leave == 49 || $entitled_leave == 50 ) {
                        $year_start_date = date($j.'-01-01');
                        $year_end_date = date($j.'-12-31');
                        
                        $wphrm_leavetype = $wpdb->get_row("SELECT * FROM $this->WphrmLeaveTypeTable WHERE `id` = '$entitled_leave'");
                        $wphrm_leaveapply = $wpdb->get_row("SELECT * FROM $this->WphrmLeaveApplicationTable WHERE `date` BETWEEN '$year_start_date' AND '$year_end_date' AND `employeeID` = '$employee_id' AND `leaveType` = '$entitled_leave' AND `applicationStatus`='approved'");
                        
                        if( $wphrm_leaveapply->maternity_option == 'mutual' ) {
                            $carried = true;
                        } else {
                            $carried = false;
                        }
                        
                        $remaining_leave = $wphrm_leavetype->numberOfLeave / 2;
                        $expiry = explode( '-', $wphrm_leaveapply->date );
                        $expiry = $expiry[1].'/'.$expiry[2].'/'.($expiry[0] + 1); 
                    }
                    endforeach;
                    if( $carried ) {
                    ?>
                    
                    <tr>
                        <!--<td><input type="checkbox" value="yes" <?php checked( $wphrm_employee_maternity_carried[$j]['can_carry_over_maternity'], 'yes'); ?> name="wphrm_employee_maternity_carried[<?php echo $j; ?>][can_carry_over_maternity]" autocomplete="off" class="icheck" checked ></td>-->
                        <td><input type="text" value="<?php echo $j; ?>" name="wphrm_employee_maternity_carried[<?php echo $j; ?>][year]" readonly  autocomplete="off" ></td>
                        <td><input type="number" value="<?php echo $remaining_leave;//echo isset($wphrm_employee_maternity_carried[$j]['count']) ? $wphrm_employee_maternity_carried[$j]['count'] : ''; ?>" min="0" name="wphrm_employee_maternity_carried[<?php echo $j; ?>][count]" readonly autocomplete="off" ></td>
                        <td><input type="text" class="date-pickers" data-format='m/d/y' value="<?php echo $expiry; ?>" name="wphrm_employee_maternity_carried[<?php echo $j; ?>][expiry]" autocomplete="off" readonly ></td>
                        <!--<td><a class="btn tiny calculate-unused-maternity-leave" data-year="<?php echo $j; ?>" data-user_id="<?php echo $wphrmEmployeeEditId; ?>" title="Calculate this year unused leave(s)">?</a></td>-->
                    </tr>
                    <?php } //for carried maternity ?>
                    <?php endfor; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan=5 ><p class="description">Just Started this year, cannot give a carry over leave.</p></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php } //end checking if female?>

<?php } ?>

<div class="form-group">
    <label class="control-label col-md-4">
        <p><?php _e('Medical Reimbursements', 'wphrm'); ?></p>
    </label>
    <div class="col-md-12">
        <div class="table-responsive" style="max-height: 200px;" >
            <?php $wphrm_employee_medical_reimbursement = isset($wphrmEmployeeBasicInfo['wphrm_employee_medical_reimbursement']) ? $wphrmEmployeeBasicInfo['wphrm_employee_medical_reimbursement'] : array(); ?>
            <table class="table table-condensed table-bordered">
                <thead>
                    <tr>
                        <th>Year</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>                    
                    <?php
                    $wphrmEmployeeJoiningDate = '';
                    if (isset($wphrmEmployeeBasicInfo['wphrm_employee_joining_date'])) {
                        $wphrmEmployeeJoiningDate = $wphrmEmployeeBasicInfo['wphrm_employee_joining_date'];
                    }
                    $wphrmEmployeeJoiningDate = new DateTime($wphrmEmployeeJoiningDate);
                    for($i = date('Y'); $i >= $wphrmEmployeeJoiningDate->format('Y'); $i--): 
                    ?>
                    
                    <tr>
                        <td><input type="text" value="<?php echo $i; ?>" name="wphrm_employee_medical_reimbursement[<?php echo $i; ?>][year]" readonly  autocomplete="off" ></td>
                        <td><input type="number" value="<?php echo isset($wphrm_employee_medical_reimbursement[$i]['amount']) ? $wphrm_employee_medical_reimbursement[$i]['amount'] : ''; ?>" min="0" name="wphrm_employee_medical_reimbursement[<?php echo $i; ?>][amount]" autocomplete="off" ></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php endif; ?>