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
    <div class="col-md-6">
        <?php  $employee_levels = $this->get_employee_types(); ?>
        <?php $selected_employee_level = isset($wphrmEmployeeBasicInfo['wphrm_employee_level']) ? $wphrmEmployeeBasicInfo['wphrm_employee_level'] : ''; ?>
        <select class="form-control select2me" name="wphrm_employee_level" id="wphrm_employee_level" required >
            <option value=""> <?php _e('-- not set --', 'wphrm'); ?></option>
            <?php foreach($employee_levels as $key => $employee_level): ?>
            <option value="<?php echo $key; ?>" <?php selected($key, $selected_employee_level); ?> ><?php echo $employee_level['title']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <a href="#" data-emp_level="<?php echo $selected_employee_level ?>" class="btn blue btn-promote-employee">Promote</a>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-4"><?php _e('Reporting Manager(s)', 'wphrm');?>  </label>
    <div class="col-md-3">      
        <?php $reporting_manager = isset($wphrmEmployeeBasicInfo['wphrm_employee_reporting_manager']) && is_array($wphrmEmployeeBasicInfo['wphrm_employee_reporting_manager']) ? $wphrmEmployeeBasicInfo['wphrm_employee_reporting_manager'] : array(); ?>
        <select class="form-control make-sumoselect" name="wphrm_employee_reporting_manager[]" id="wphrm_employee_reporting_manager" multiple=yes >
        
        <?php
        $to_report = array();
        if(!empty($employee_managers)) { 
            foreach ($employee_managers as $key => $userdata) {
                $wphrmEmployeeInfo = $this->WPHRMGetUserDatas($userdata->data->ID, 'wphrmEmployeeInfo');
                $wphrmEmployeePermit = $this->WPHRMGetUserDatas($userdata->ID, 'wphrmEmployeeWorkPermitInfo');
                
                //do some page filtering
                if( $userdata->ID != $employee_id ) { //echo '<option>'.intval($wphrmEmployeeInfo['wphrm_employee_department']).'</option>';
                    if( $wphrmEmployeeInfo['wphrm_employee_approving_officer'] == 'Yes' && $wphrmEmployeeInfo['wphrm_employee_status'] == 'Active' ) {
                        $wphrmEmployeeFirstName = get_user_meta($userdata->data->ID, 'first_name', true);
                        $wphrmEmployeeLastName = get_user_meta($userdata->data->ID, 'last_name', true);

                        if (isset($wphrmEmployeeInfo['wphrm_employee_department']) && $wphrmEmployeeInfo['wphrm_employee_department'] != '') {
                                    $employeeDepartmentsLoads = esc_sql($wphrmEmployeeInfo['wphrm_employee_department']); // esc
                                    $departments = $wpdb->get_row("SELECT * FROM  $this->WphrmDepartmentTable  where `departmentID` = '$department'");
                                    if ($departments != '') {
                                        $dep = unserialize(base64_decode($departments->departmentName));
                                        if( $wphrmEmployeeInfo['wphrm_employee_department'] == $department && $wphrmEmployeeInfo['wphrm_employee_status'] == 'Active' ) {
                                            $wphrmEmployeeFirstName = get_user_meta($userdata->data->ID, 'first_name', true);
                                            $wphrmEmployeeLastName = get_user_meta($userdata->data->ID, 'last_name', true);
                                            $emp_name = (!empty($wphrmEmployeeFirstName) || !empty($wphrmEmployeeLastName)) ? $wphrmEmployeeFirstName. ' '.$wphrmEmployeeLastName : $userdata->display_name;
                                            $to_report[] = array( 
                                                    'order' => 1,
                                                    'type' => 'Approving Officer',
                                                    'userid' => $userdata->ID,
                                                    'emp_name' => $emp_name,
                                                );
                                        }
                                    }
                                }
                    } 
                    //if( $wphrmEmployeeInfo['wphrm_employee_department'] == 4 && $wphrmEmployeeInfo['wphrm_employee_status'] == 'Active' ) { for admin
                    if( $wphrmEmployeeInfo['wphrm_employee_role'] == 'administrator' && $wphrmEmployeeInfo['wphrm_employee_status'] == 'Active' ) {
                            $wphrmEmployeeFirstName = get_user_meta($userdata->data->ID, 'first_name', true);
                            $wphrmEmployeeLastName = get_user_meta($userdata->data->ID, 'last_name', true);
                            $emp_name = (!empty($wphrmEmployeeFirstName) || !empty($wphrmEmployeeLastName)) ? $wphrmEmployeeFirstName. ' '.$wphrmEmployeeLastName : $userdata->display_name;
                            $to_report[] = array( 
                                    'order' => 2, 
                                    'type' => 'Administrator',
                                    'userid' => $userdata->ID,
                                    'emp_name' => $emp_name,
                                );
                    //if( $wphrmEmployeeInfo['wphrm_employee_department'] == 7 && $wphrmEmployeeInfo['wphrm_employee_status'] == 'Active' ) { for HR
                    } elseif( $wphrmEmployeeInfo['wphrm_employee_role'] == 'hr_manager' && $wphrmEmployeeInfo['wphrm_employee_status'] == 'Active' ) {
                        $wphrmEmployeeFirstName = get_user_meta($userdata->data->ID, 'first_name', true);
                        $wphrmEmployeeLastName = get_user_meta($userdata->data->ID, 'last_name', true);
                        $emp_name = (!empty($wphrmEmployeeFirstName) || !empty($wphrmEmployeeLastName)) ? $wphrmEmployeeFirstName. ' '.$wphrmEmployeeLastName : $userdata->display_name;
                        $to_report[] = array( 
                                'order' => 3, 
                                'type' => 'Human Resource',
                                'userid' => $userdata->ID,
                                'emp_name' => $emp_name,
                            );
                    }
                }
            }

            sort( $to_report );
            $rm_type = array( 'Approving Officer', 'Administrator', 'Human Resource' );
            foreach( $rm_type as $type ) {
                echo '<optgroup label="'.$type.'">';
                    foreach( $to_report as $report ) {
                        if( $type == $report['type'] ) {
                            echo '<option value="'.$report['userid'].'" '.selected( in_array( $report['userid'], $reporting_manager ), true ).'  > '.$report['emp_name'].'</option>';
                        }
                    }
                echo '</optgroup>';
            }
            /*foreach( $to_report as $reports ) {
                echo '<optgroup label="'.$reports['type'].'">';
                    foreach( $reports['content'] as $report ) {
                        echo '<option value="'.$report['userid'].'" '.selected( in_array( $report['userid'], $reporting_manager ), true ).'  > '.$report['emp_name'].'</option>';
                    }
                echo '</optgroup>';
            }*/
        }
        ?>
        </select>
    </div>
    <div class="col-md-5">
        <div class="alert-danger max-alert" style="padding: 1px 15px; text-align: center; display: none">
            <h6>You can only choose 3 Reporting Manager!</h6>
        </div>
    </div>
</div>
<!--J@F END-->

<div class="form-group">
    <label class="control-label col-md-4"><?php _e('Joining Date', 'wphrm'); ?></label>
    <div class="col-md-6">
        <div class="input-group input-medium date before-current-date"  data-date-format="dd-mm-yyyy" data-date-viewmode="years">
            <input class="form-control"  name="wphrm_employee_joining_date" type="text" id="wphrm_employee_joining_date" value="<?php if (isset($wphrmEmployeeBasicInfo['wphrm_employee_joining_date'])) : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_joining_date']); endif; ?>" autocapitalize="none"  />
            <span class="input-group-btn">
                <button class="btn default-date" type="button" disabled ><i class="fa fa-calendar" style="line-height: 1.9;"></i></button>
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
                <button class="btn default-date" type="button" disabled ><i class="fa fa-calendar" style="line-height: 1.9;"></i></button>
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

<?php
global $wphrm;
$wphrmEmployeeInfo = $wphrm->WPHRMGetUserDatas($employee_id, 'wphrmEmployeeInfo');
$emp = $this->get_user_complete_info( $employee_id );
$wphrmEmployeeJoiningDate = new DateTime($emp->basic_info->wphrm_employee_joining_date);
$today = new DateTime();
$interval = $today->diff($wphrmEmployeeJoiningDate);
$wphrmEmployeeJoiningToCurrentTotalYear = ((int) $interval->format('%y years') + 1);

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
}

$wphrmEmployeeInfo = $wphrm->WPHRMGetUserDatas($employee_id, 'wphrmEmployeeInfo');
$annual_leave_history = isset($wphrmEmployeeInfo['wphrm_employee_leave_carried']) ? $wphrmEmployeeInfo['wphrm_employee_leave_carried'] : false;
if(
    isset($annual_leave_history[$year-1]) && isset($annual_leave_history[$year-1]['can_carry_over'])
    && filter_var($annual_leave_history[$year-1]['can_carry_over'], FILTER_VALIDATE_BOOLEAN)
    && !empty($annual_leave_history[$year-1]['count'])
    && $annual_leave_history[$year-1]['expiry'] > date('m/d/Y')
){
    $max_leave = $max_leave + $annual_leave_history[$year-1]['count'];
}


$cp = count($wphrmEmployeeBasicInfo['wphrm_employee_promotion']);
$pdate = $wphrmEmployeeBasicInfo['wphrm_employee_promotion'][$cp-1];
if( !empty( $pdate ) ) {
    foreach( $pdate as $key => $pd ) {
        $promotion_date = explode( '-', $pd['wphrm_employee_promotion_date'] );
        if( $promotion_date[2] != date('Y') ) {
            $previous = ( $max_leave/12 ) * ($promotion_date[1] - 1);;
            $new_leave_entitlement = $wpdb->get_row("SELECT * FROM  $this->WphrmEmployeeLevelTable WHERE years_of_service = 1");
            $next = ($new_leave_entitlement->$pd['wphrm_employee_promotion_level']/12) * ( 12 - ($promotion_date[1] - 1));
            $new_leave = round( $previous + $next );
        } else {
            $wphrmEmployeeJoiningDate = new DateTime($pd['wphrm_employee_promotion_date']);
            $today = new DateTime();
            $interval = $today->diff($wphrmEmployeeJoiningDate);
            $wphrmEmployeeJoiningToCurrentTotalYear = ((int) $interval->format('%y years') + 1);
            $leveltype = $emp->basic_info->wphrm_employee_level;
            
            $leave_entitlement_count = $wpdb->get_var("SELECT COUNT(*) FROM  $this->WphrmEmployeeLevelTable WHERE years_of_service = $wphrmEmployeeJoiningToCurrentTotalYear");
            if( $leave_entitlement_count > 0 ) {
                $leave_entitlement = $wpdb->get_row("SELECT * FROM  $this->WphrmEmployeeLevelTable WHERE years_of_service = $wphrmEmployeeJoiningToCurrentTotalYear");
            } else {
                $leave_entitlement = $wpdb->get_row("SELECT * FROM  $this->WphrmEmployeeLevelTable ORDER BY id DESC LIMIT 1");
            }

            $max_leave = $leave_entitlement->$pd['wphrm_employee_promotion_level'];

            $wphrmEmployeeInfo = $wphrm->WPHRMGetUserDatas($user_id, 'wphrmEmployeeInfo');
            $annual_leave_history = isset($wphrmEmployeeInfo['wphrm_employee_leave_carried']) ? $wphrmEmployeeInfo['wphrm_employee_leave_carried'] : false;
            if(
                isset($annual_leave_history[$year-1]) && isset($annual_leave_history[$year-1]['can_carry_over'])
                && filter_var($annual_leave_history[$year-1]['can_carry_over'], FILTER_VALIDATE_BOOLEAN)
                && !empty($annual_leave_history[$year-1]['count'])
                && $annual_leave_history[$year-1]['expiry'] > date('m/d/Y')
            ){
                $max_leave = $max_leave + $annual_leave_history[$year-1]['count'];
            }
            $total = $max_leave;
        }
    } 
}
?>

<?php $wphrmPromotions = isset($wphrmEmployeeBasicInfo['wphrm_employee_promotion']) ? $wphrmEmployeeBasicInfo['wphrm_employee_promotion'] : ''; ?>
<?php $count_promotion = isset($wphrmEmployeeBasicInfo['wphrm_employee_promotion']) ? count($wphrmEmployeeBasicInfo['wphrm_employee_promotion'] ) : ''; ?>
<?php if( $count_promotion > 0 ) { ?>   
<div class="form-group">
    <label class="control-label col-md-4"><?php _e('Promotion History', 'wphrm'); ?></label>
    <div class="col-md-12">
        <div class="table-responsive" style="max-height: 200px;" >
            <table class="table table-condensed table-bordered wphrmEmployeePromotion-table">
                <thead>
                    <tr>
                        <th>Initial Level</th>
                        <th>Promotion Level</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>   
                    <?php if( !empty( $wphrmPromotions ) ) { ?>            
                        <?php foreach( $wphrmPromotions as $wphrmPromotionsKey => $promotions ) { ?>  
                        <?php if( !empty( $promotions ) ) { ?>             
                            <?php foreach( $promotions as $promotion ) { ?>            
                            <tr>
                                <td>
                                    <?php $wphrm_employee_initial_level = $promotion['wphrm_employee_initial_level']; ?>
                                    <select class="form-control select2me" disabled >
                                        <option value=""> <?php _e('-- not set --', 'wphrm'); ?></option>
                                        <?php foreach($employee_levels as $key => $employee_level): ?>
                                        <option value="<?php echo $key; ?>" <?php selected($key, $wphrm_employee_initial_level); ?> ><?php echo $employee_level['title']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <?php $wphrm_employee_promotion_level = $promotion['wphrm_employee_promotion_level'] ?>
                                    <select class="form-control select2me" disabled >
                                        <option value=""> <?php _e('-- not set --', 'wphrm'); ?></option>
                                        <?php foreach($employee_levels as $key => $employee_level): ?>
                                        <option value="<?php echo $key; ?>" <?php selected($key, $wphrm_employee_promotion_level); ?> ><?php echo $employee_level['title']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <?php $wphrm_employee_promotion_date = date( 'F j, Y', strtotime( $promotion['wphrm_employee_promotion_date'] ) ); ?>
                                    <div class="input-group date date-picker bdate-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                        <input class="form-control" value="<?php echo $wphrm_employee_promotion_date?>" autocapitalize="none" type="text" disabled>
                                        <span class="input-group-btn">
                                            <button class="btn default-date display-hide" type="button"><i class="fa fa-calendar" style="line-height: 1.9;"></i></button>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <a href="#" class="btn-remove-promotion" data-master-key="<?php echo $wphrmPromotionsKey ?>" data-promotion-key="<?php echo $promotion['wphrm_employee_promotion_date'] ?>"><i class="fa fa-minus"></i></a>
                                </td>
                            </tr>
                            <?php } ?>
                        <?php } ?>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php } ?>


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
                        $yos = ((int) $interval->format('%y years') + 1);

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
                        <td><input type="number" value="<?php /*echo $remaining_leave;*/echo isset($wphrm_employee_leave_carried[$i]['count']) ? $wphrm_employee_leave_carried[$i]['count'] : $remaining_leave; ?>" min="0" name="wphrm_employee_leave_carried[<?php echo $i; ?>][count]" autocomplete="off" ></td>
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