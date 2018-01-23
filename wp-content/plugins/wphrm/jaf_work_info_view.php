<?php
global $wphrm;
$employee_managers =  get_users();
//$employee_id = (isset($wphrmEmployeeEditId)) ? esc_attr($wphrmEmployeeEditId) : '';
$employee_id = (isset($_GET['employee_id'])) ? esc_attr($_GET['employee_id']) : '';
$wphrmEmployeeInfo = $wphrm->WPHRMGetUserDatas($employee_id, 'wphrmEmployeeInfo');
?>

<div class="form-group">
    <?php    
    $countries = $this->get_country_list( array( 'country' => array('SG', 'MY', 'CH', 'VN', 'MM', 'KH', 'MO', 'HK', 'BR') ) );
    $employment_coutry = empty($wphrmEmployeeBasicInfo['wphrm_employee_employment_country']) ? '' : $wphrmEmployeeBasicInfo['wphrm_employee_employment_country'];
    ?>
    <label class="control-label col-md-4"><?php _e('Country of Employment', 'wphrm'); ?></label>
    <div class="col-md-8" >
        <input readonly value="<?php echo empty($employment_coutry) && isset($countries[$employment_coutry]) ? '' : $countries[$employment_coutry]->name ?>" class="form-control">
    </div>
</div>

<!-- <div class="form-group">
    <label class="control-label col-md-4"><?php _e('Employee Id', 'wphrm'); ?>
    </label>
    <div class="col-md-8">
        <input  required type="text" id="wphrm_employee_uniqueid"  readonly value="<?php if (isset($wphrmEmployeeBasicInfo['wphrm_employee_uniqueid'])) : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_uniqueid']); endif; ?>" class="form-control" />
    </div>
</div> -->

<div class="form-group">
    <label class="control-label col-md-4"><?php _e('Employee Status', 'wphrm'); ?>
    </label>
    <div class="col-md-8">
        <div class="radio-list" data-error-container="#form_2_membership_error">
            <input readonly class="form-control" value="<?php echo isset($wphrmEmployeeBasicInfo['wphrm_employee_status']) ? $wphrmEmployeeBasicInfo['wphrm_employee_status'] : 'Inactive'; ?>" >
        </div>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-4"><?php _e('Department', 'wphrm'); ?>  </label>
    <div class="col-md-8">
        <?php $current_department = isset($wphrmEmployeeBasicInfo['wphrm_employee_department']) ? $this->get_department_info($wphrmEmployeeBasicInfo['wphrm_employee_department']) : ''; ?>
        <input readonly value="<?php echo $current_department ? $current_department['departmentName'] : ''; ?>" class="form-control" >
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-4"><?php _e('Designation', 'wphrm'); ?>
    </label>
    <div class="col-md-8">
        <?php $current_designation = isset($wphrmEmployeeBasicInfo['wphrm_employee_designation']) ? $this->get_designation_info($wphrmEmployeeBasicInfo['wphrm_employee_designation']) : ''; ?>
        <input readonly id="wphrm_ajax_employee_designation" value="<?php echo $current_designation['designationName']; ?>" class="form-control" >
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-4"><?php _e('Employee Type', 'wphrm'); ?>  </label>
    <div class="col-md-8">
        <?php  $employee_levels = $this->get_employee_types(); ?>
        <?php $selected_employee_level = isset($wphrmEmployeeBasicInfo['wphrm_employee_level']) ? $wphrmEmployeeBasicInfo['wphrm_employee_level'] : ''; ?>
        <input value="<?php echo isset($employee_levels[$selected_employee_level]) ? $employee_levels[$selected_employee_level]['title'] : ''; ?>" readonly class="form-control" >
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-4"><?php _e('Reporting Manager(s)', 'wphrm'); ?>  </label>
    <div class="col-md-8">
        <?php $reporting_manager = isset($wphrmEmployeeBasicInfo['wphrm_employee_reporting_manager']) && is_array($wphrmEmployeeBasicInfo['wphrm_employee_reporting_manager']) ? $wphrmEmployeeBasicInfo['wphrm_employee_reporting_manager'] : array(); ?>

        <ol>
            <!-- <?php 
            if($reporting_manager && is_array($reporting_manager)):
            foreach ($employee_managers as $key => $userdata): ?>
            <?php if(!in_array( $userdata->ID, $reporting_manager) ) continue; ?>
            <?php $wphrmEmployeeFirstName = get_user_meta($userdata->data->ID, 'first_name', true); ?>
            <?php $wphrmEmployeeLastName = get_user_meta($userdata->data->ID, 'last_name', true); ?>
            <li><?php echo (!empty($wphrmEmployeeFirstName) || !empty($wphrmEmployeeLastName)) ? _e($wphrmEmployeeFirstName. ' '.$wphrmEmployeeLastName, 'wphrm') : $userdata->display_name; ?></li>
            <?php
            endforeach;
            endif;
            ?> -->
            <?php
            foreach( $reporting_manager as $to_report ) {
                $emp_info = $this->WPHRMGetUserDatas( $to_report, 'wphrmEmployeeInfo' );
                $user_info = get_userdata( $to_report );
                $wphrmEmployeeFirstName = get_user_meta( $to_report, 'first_name', true);
                $wphrmEmployeeLastName = get_user_meta( $to_report, 'last_name', true);
                echo '<li>';
                    if( $emp_info['wphrm_employee_department'] == 4 ) {
                        echo 'Administrator';
                    } elseif( $emp_info['wphrm_employee_department'] == 7 ) {
                        echo 'Human Resource';
                    } else {
                        echo (!empty($wphrmEmployeeFirstName) || !empty($wphrmEmployeeLastName)) ? _e($wphrmEmployeeFirstName. ' '.$wphrmEmployeeLastName, 'wphrm') : $user_info->display_name;
                    }
                echo '</li>';
            }
            ?>
        </ol>
    </div>
</div>

<?php
$userid = empty( $_GET['employee_id'] ) ? $current_user->ID : $_GET['employee_id'];
$is_regular = $this->is_employee_regular($userid);
$max_leave = 0;
if( $is_regular ) :
?>
<div class="form-group">
    <label class="col-md-4 control-label"><?php _e('Leaves', 'wphrm'); ?></label>
    <div class="col-md-8">
        <table class="table">
            <tr>
                <td>Leave Type</td>
                <td>Used/Limit</td>
            </tr>
            <?php if(!empty($wphrmEmployeeBasicInfo['wphrm_employee_entitled_leave'])): ?>            
            <?php foreach($wphrmEmployeeBasicInfo['wphrm_employee_entitled_leave'] as $entitled_leave): 
            $leave_title = $this->get_leave_info($entitled_leave, 'leaveType');
            if(empty($leave_title)) continue;
            
            if( $leave_title == 'Annual Leave' || $leave_title == 'annual leave' || $leave_title == 'Annual leave' ) {
                /*if (isset($wphrmEmployeeBasicInfo['wphrm_employee_joining_date'])) {
                    $wphrmEmployeeJoiningDate = $wphrmEmployeeBasicInfo['wphrm_employee_joining_date'];
                }
                $wphrmEmployeeJoiningDate = new DateTime($wphrmEmployeeJoiningDate);
                $today = new DateTime();
                $interval = $today->diff($wphrmEmployeeJoiningDate);
                $yos = ((int) $interval->format('%y years') + 1);*/
                
                //$max_leave = $this->get_employee_total_leave( $userid, $entitled_leave);
                
                $leave_info = $this->get_leave_info(35);
                $max_leave = $this->get_employee_max_leave( $userid, $leave_info->leave_rules, $leave_info);
                
                /*$emp = $this->get_user_complete_info( $userid );
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
                
                $year = date('Y');
                $annual_leave_history = isset($wphrmEmployeeBasicInfo['wphrm_employee_leave_carried']) ? $wphrmEmployeeBasicInfo['wphrm_employee_leave_carried'] : false;
                if(
                    isset($annual_leave_history[$year-1]) && isset($annual_leave_history[$year-1]['can_carry_over'])
                    && filter_var($annual_leave_history[$year-1]['can_carry_over'], FILTER_VALIDATE_BOOLEAN)
                    && !empty($annual_leave_history[$year-1]['count'])
                    && $annual_leave_history[$year-1]['expiry'] > date('m/d/Y')
                ){
                    $max_leave = $max_leave + $annual_leave_history[$year-1]['count'];
                }*/
            ?>
                <tr>
                    <td><?php echo $leave_title; ?></td>
                    <td><?php echo $this->get_used_leave($userid, $entitled_leave); ?>/<?php echo $max_leave; ?></td>
                </tr>
            <?php
                } elseif( $entitled_leave == 45 || $entitled_leave == 48 || $entitled_leave == 49 || $entitled_leave == 50 ) {
                    $ytoday = date('Y') - 1;
                    $wphrm_employee_maternity_carried = isset($wphrmEmployeeBasicInfo['wphrm_employee_maternity_carried']) ? $wphrmEmployeeBasicInfo['wphrm_employee_maternity_carried'] : array();
                    $expiry = strtotime( $wphrm_employee_maternity_carried[$ytoday]['expiry'] );
                    $date_today = strtotime( date( 'm/d/Y' ) );
                    if( $expiry > $date_today ) {
                        $max_leave = $wphrm_employee_maternity_carried[$ytoday]['count'] + $this->get_employee_total_leave($userid, $entitled_leave);
                    } else {
                        $max_leave = $this->get_employee_total_leave($userid, $entitled_leave);
                    }
                    
            ?>
                    <tr>
                        <td><?php echo $leave_title; ?></td>
                        <td><?php echo $this->get_used_leave($userid, $entitled_leave); ?>/<?php echo $max_leave; ?></td>
                    </tr>
            <?php
                } elseif( $entitled_leave == 39 || $entitled_leave == 41 || $entitled_leave == 42 || $entitled_leave == 43 ) {
                    if( $entitled_leave == 39 ) {
                        $used_leave = $this->get_used_leave($employee_id, 39) + $this->get_used_leave($employee_id, 37) + $this->get_used_leave($employee_id, 40);
                    } elseif( $entitled_leave == 41 ) {
                        $used_leave = $this->get_used_leave($employee_id, 41) + $this->get_used_leave($employee_id, 63) + $this->get_used_leave($employee_id, 40);
                    } elseif( $entitled_leave == 42 ) {
                        $used_leave = $this->get_used_leave($employee_id, 42) + $this->get_used_leave($employee_id, 38) + $this->get_used_leave($employee_id, 40);
                    } elseif( $entitled_leave == 43 ) {
                        $used_leave = $this->get_used_leave($employee_id, 43) + $this->get_used_leave($employee_id, 64) + $this->get_used_leave($employee_id, 40);
                    } else {
                        $used_leave = $this->get_used_leave($employee_id, $entitled_leave);
                    }
                    
                    $max_leave = $this->get_employee_total_leave($userid, $entitled_leave);
                    
            ?>
                    <tr>
                        <td><?php echo $leave_title; ?></td>
                        <td><?php echo $used_leave; ?>/<?php echo $max_leave; ?></td>
                    </tr>
            <?php
                } else {
            ?>
                    <tr>
                        <td><?php echo $leave_title; ?></td>
                        <td><?php echo $this->get_used_leave($userid, $entitled_leave); ?>/<?php echo $this->get_employee_total_leave($userid, $entitled_leave); ?></td>
                    </tr>
            <?php } ?>
            <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>
</div>
<?php endif; ?>

<!--J@F END-->


<div class="form-group">
    <label class="control-label col-md-4"><?php _e('Joining Date', 'wphrm'); ?></label>
    <div class="col-md-6">
        <div class="input-group input-medium"  data-date-format="dd-mm-yyyy" data-date-viewmode="years">
            <input readonly class="form-control" value="<?php if (isset($wphrmEmployeeBasicInfo['wphrm_employee_joining_date'])) : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_joining_date']); endif; ?>" >
        </div>
    </div>
</div>

<!--J@F-->
<div class="form-group">
    <label class="control-label col-md-4"><?php _e('Leaving Date', 'wphrm'); ?></label>
    <div class="col-md-6">
        <div class="input-group input-medium " >
            <input readonly class="form-control" value="<?php if (isset($wphrmEmployeeBasicInfo['wphrm_employee_leaving_date'])) : echo esc_attr($wphrmEmployeeBasicInfo['wphrm_employee_leaving_date']); endif; ?>" >
        </div>
    </div>
</div>
<div class="form-group">
    <label class="control-label col-md-4"><?php _e('Probation Period', 'wphrm'); ?></label>
    <div class="col-md-6">
            <?php $current_probatio_period = empty($wphrmEmployeeBasicInfo['wphrm_employee_probation_period']) ? 3 : $wphrmEmployeeBasicInfo['wphrm_employee_probation_period']; ?>
        <input readonly class="form-control" value="<?php echo $current_probatio_period.' Months'; ?>" >
    </div>
</div>


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
                    <?php for($i = date('Y')-1; $i >= $wphrmEmployeeJoiningDate->format('Y'); $i--): ?>
                    <tr>
                        <td><input type="checkbox" value="yes" <?php checked( $wphrm_employee_leave_carried[$i]['can_carry_over'], 'yes'); ?> readonly autocomplete="off" class="icheck" ></td>
                        <td><input type="text" value="<?php echo $i; ?>" readonly  autocomplete="off" ></td>
                        <td><input type="number" value="<?php echo isset($wphrm_employee_leave_carried[$i]['count']) ? $wphrm_employee_leave_carried[$i]['count'] : ''; ?>" min="0" max="5" readonly autocomplete="off" ></td>
                        <td><input type="text" class="date-pickers" data-format='m/d/y' value="<?php echo !empty($wphrm_employee_leave_carried[$i]['expiry']) ? $wphrm_employee_leave_carried[$i]['expiry'] : date('m/d/'.($i + 1)); ?>" readonly autocomplete="off" ></td>
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
                        <td><input type="number" value="<?php echo isset($wphrm_employee_medical_reimbursement[$i]['amount']) ? $wphrm_employee_medical_reimbursement[$i]['amount'] : ''; ?>" min="0" name="wphrm_employee_medical_reimbursement[<?php echo $i; ?>][amount]" readonly autocomplete="off" ></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
