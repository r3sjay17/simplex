<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/** WP_HRM ajax data featch with HTML
 *   
 */

class WPHRMAjaxDatas {
    // global protected variable for the using database tables
    protected $wphrmMainClass, $departmentTable, $salaryTable, $designationTable, $settingsTable, $holidayTable;
    public function __construct($wphrmMainClass) {
        // user wphrm main object only & set protected global in table name config file in wphrm mail class
        $this->wphrmMainClass = $wphrmMainClass;
        $this->departmentTable = $this->wphrmMainClass->WphrmDepartmentTable;
        $this->salaryTable = $this->wphrmMainClass->WphrmSalaryTable;
        $this->designationTable = $this->wphrmMainClass->WphrmDesignationTable;
        $this->settingsTable = $this->wphrmMainClass->WphrmSettingsTable;
        $this->financialsTable = $this->wphrmMainClass->WphrmFinancialsTable;
        $this->holidayTable = $this->wphrmMainClass->WphrmHolidaysTable;
        $this->WeeklySalaryTable = $this->wphrmMainClass->WphrmWeeklySalaryTable;
    }

    public function WPHRMGetFinancialGraph($wphrmYear) {
        ob_end_clean();
        if (ob_get_level() == 0){ ob_start(); }
        global $wpdb;
        $wphrmMonths = $this->wphrmMainClass->WPHRMGetMonths();
        $wphrmProfilossReport = $this->wphrmMainClass->WPHRMGetFinancialsReport($wphrmYear);
        if (!empty($wphrmProfilossReport)) :
            foreach ($wphrmProfilossReport as $monthkey => $mothReport) :
                if ($mothReport >= 0) :
                    ?>
                    <li><div data-amount="<?php echo esc_attr($mothReport); ?>" class="bar"></div><span><?php echo esc_html($wphrmMonths[$monthkey]); ?></span></li>
                <?php else : ?>
                    <li><div data-amount="<?php echo abs($mothReport); ?>" class="bar_lose"></div><span><?php echo esc_html($wphrmMonths[$monthkey]); ?></span></li>
                <?php
                endif;
            endforeach;
            ?>
            <script>
                /**  chart js  **/
                jQuery("#bars li .bar").each(function (key, bar) {
                    var percentage = jQuery(this).data('amount');
                    var amounts = jQuery('.wphrm_level').val();
                    var final = (percentage * 100) / amounts; 
                    if(final > 99){
                      var datacheck = 99;
                       }else{
                         var datacheck = final;
                          }
       
                     jQuery(this).animate({
                        'height': datacheck + '%'
                       }, 1000);
                })
                
                jQuery("#bars li .bar_lose").each(function (key, bar_lose) {
                    var percentage = jQuery(this).data('amount');
                    var amounts = jQuery('.wphrm_level').val();
                    var final = (percentage * 100) / amounts;
                    if(final > 99){
                      var datacheck = 99;
                       }else{
                         var datacheck = final;
                          }
       
                     jQuery(this).animate({
                        'height': datacheck + '%'
                       }, 1000);
                })
            </script>
        <?php else : ?>
            <?php foreach ($wphrmMonths as $wphrmMonth) : ?>
                <li><div data-amount="" class="bar"></div><span><?php echo esc_html($wphrmMonth); ?></span></li>
            <?php endforeach; ?>
        <?php
        endif;
    }

    /** WP-HRM Ajax Data For the Holiday List * */
    public function WPHRMGetHolidayMonth($wphrmYear, $wphrmMonth) {
        ob_end_clean();
        if (ob_get_level() == 0){ ob_start(); }
        global $current_user, $wpdb;
        $wphrmUserRole = implode(',', $current_user->roles);
        $wphrmGetPagePermissions = $this->wphrmMainClass->WPHRMGetPagePermissions();
        ?>
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-calendar"></i><?php esc_html_e($wphrmMonth, 'wphrm'); ?>
            </div>
            <div class="action">
                <a href="#" class="btn-delete-selected-holiday per_month btn btn-xs red"><i class="fa fa-trash"></i> DELETE</a>
            </div>
        </div>
        <div class="portlet-body">
            <div class="table-scrollable">
                <form method="POST" action="#" class="wphrm_delete_selected_holiday_per_month">
                    <table class="table table-hover">
                        <thead>
                            <tr> 
                                <th></th>
                                <th><?php _e('Date', 'wphrm'); ?></th>
                                <th><?php _e('Occasion', 'wphrm'); ?> </th>
                                <th><?php _e('Day', 'wphrm'); ?> </th>
                                <?php if (in_array('manageOptionsHolidays', $wphrmGetPagePermissions)) { ?>
                                    <th><?php _e('Actions', 'wphrm'); ?> </th>
                                <?php } ?>                
                            </tr>
                        </thead>
                        <tbody>
                           
                            <?php
                            $current_year = esc_sql($wphrmYear); // esc
                            $holiday_month = esc_sql(date("m", strtotime($wphrmMonth))); // esc
                            $wphrm_holidays = $wpdb->get_results("SELECT * FROM  $this->holidayTable where  wphrmDate BETWEEN '$current_year-$holiday_month-01' AND '$current_year-$holiday_month-31'");
                            if (!empty($wphrm_holidays)) :
                                foreach ($wphrm_holidays as $key => $wphrm_holidays_between) {
                                    ?>
                                    <tr id="row102">
                                        <td><input type="checkbox" name="to_delete_holiday_per_month[]" class="to_delete_holiday_per_month" value="<?php echo esc_js($wphrm_holidays_between->id); ?>"></td>
                                        <td> <?php echo esc_html(date('d F Y', strtotime($wphrm_holidays_between->wphrmDate))); ?> </td>
                                        <td> <?php echo esc_html($wphrm_holidays_between->wphrmOccassion); ?> </td>
                                        <td><?php
                                            $originalDate = $wphrm_holidays_between->wphrmDate;
                                            echo esc_html($newDate = date("D", strtotime($originalDate)));
                                            ?> </td>
                                        <td>
                                            <?php if (in_array('manageOptionsHolidays', $wphrmGetPagePermissions)) { ?>
                                                <button type="button" onclick="WPHRMCustomDelete(<?php echo esc_js($wphrm_holidays_between->id); ?>, '<?php echo esc_js($this->holidayTable); ?>', 'id')" href="#" class="btn btn-xs red">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            <?php } ?>
                                        </td> 
                                    </tr>
                                <?php } ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="4" style="text-align:center"><?php _e('No holidays found.', 'wphrm'); ?></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </form>
            </div>
        </div><?php
    }

    /** WP-HRM Ajax Data For the Holiday List 
     *   @argument Year & Month
     *   @return Moth list for the Selected Year
     * */
    public function WPHRMGetHolidayYear($wphrmYear) {
        ob_end_clean();
        if (ob_get_level() == 0){ ob_start(); }
        global $current_user, $wpdb;
        $wphrmGetPagePermissions = $this->wphrmMainClass->WPHRMGetPagePermissions();
        $wphrmUserRole = implode(',', $current_user->roles);
        $holiday_month = esc_sql(date("m", strtotime('January'))); // esc
        $current_year = esc_sql($wphrmYear); // esc
        $wphrm_holidays = $wpdb->get_results("SELECT * FROM  $this->holidayTable where  wphrmDate BETWEEN '$current_year-$holiday_month-01' AND '$current_year-$holiday_month-31'");
        ?>
                
          
        <div class="col-md-3">
            <ul class="ver-inline-menu tabbable margin-bottom-10" >
                <?php
                for ($m = 1; $m <= 12; $m++) {
                    $month[] = date('F', mktime(0, 0, 0, $m, 1, date('Y')));
                } $months = $month;
                $activeMonth = 'January';
                foreach ($months as $key => $month) {
                    ?>
                    <li   <?php if ($month == $activeMonth) { ?> class="active" <?php } ?> >
                        <a  data-toggle="tab" class="datasa" onclick="wphrm_month('<?php echo esc_js($month); ?>',<?php echo esc_js($key); ?>);" href="#<?php echo esc_attr($month); ?>"><i class="fa fa-calendar"></i> <?php esc_html_e($month, 'wphrm'); ?></a>
                        <span class="after"></span>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <div class="col-md-9">
            <div class="tab-content">
                <?php foreach ($months as $month) { ?>
                    <div id="<?php esc_html_e($month, 'wphrm'); ?>" class="tab-pane <?php
                    if ($month == $activeMonth) {
                        echo 'active';
                    }
                    ?>">
                        <div class="portlet box blue month_holidays" >
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-calendar"></i><?php esc_html_e($month, 'wphrm'); ?>
                                </div>
                                <div class="action">
                                    <a href="#" class="btn-delete-selected-holiday btn btn-xs red"><i class="fa fa-trash"></i> DELETE</a>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="table-scrollable">
                                    <form method="POST" action="#" class="wphrm_delete_selected_holiday_per_year">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th> <?php _e('Date', 'wphrm'); ?> </th>
                                                    <th> <?php _e('Occasion', 'wphrm'); ?> </th>
                                                    <th> <?php _e('Day', 'wphrm'); ?> </th>
                                                    <?php if (in_array('manageOptionsHolidays', $wphrmGetPagePermissions)) { ?>
                                                        <th><?php _e('Actions', 'wphrm'); ?></th>
                                                    <?php } ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                               
                                                <?php
                                                if (!empty($wphrm_holidays)) :
                                                    foreach ($wphrm_holidays as $key => $wphrm_holidays_between) {
                                                        ?>
                                                        <tr id="row102">
                                                            <td><input type="checkbox" name="to_delete_holiday_per_year[]" class="to_delete_holiday_per_year" value="<?php echo esc_js($wphrm_holidays_between->id); ?>"></td>
                                                            <td><?php
                                                                $whrmholidate = date('d F Y', strtotime($wphrm_holidays_between->wphrmDate));
                                                                echo esc_html($whrmholidate);
                                                                ?> </td>
                                                            <td><?php echo esc_html($wphrm_holidays_between->wphrmOccassion); ?> </td>
                                                            <td><?php
                                                                $originalDate = $wphrm_holidays_between->wphrmDate;
                                                                echo esc_html($newDate = date("D", strtotime($originalDate)));
                                                                ?></td>
                                                            <td>
                                                                <?php if (in_array('manageOptionsHolidays', $wphrmGetPagePermissions)) { ?>  <button type="button" onclick="WPHRMCustomDelete(<?php echo esc_js($wphrm_holidays_between->id); ?>, '<?php echo esc_js($this->holidayTable); ?>', 'id')" href="#" class="btn btn-xs red">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button> <?php } ?>
                                                            </td> 
                                                        </tr>
                                                        <?php
                                                    }
                                                else :
                                                    ?>
                                                    <tr>
                                                        <td colspan="4" style="text-align:center"><?php _e('No holiday here.', 'wphrm'); ?></td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div><?php
    }

   
    
     /** WP-HRM Ajax Data For the Salary
     *   @argument Employee ID & Year
     *   @return Salary Data HTML
     * */
    public function WPHRMGetSalaryData($wphrmEmployeeID, $wphrmYear) {
        ob_end_clean();
        if (ob_get_level() == 0) {
            ob_start();
        }
        global $current_user, $wpdb;
        $wphrmUserRole = implode(',', $current_user->roles);
        $wphrmGetPagePermissions = $this->wphrmMainClass->WPHRMGetPagePermissions();
        $wphrmInfo = $this->wphrmMainClass->WPHRMGetUserDatas($wphrmEmployeeID, 'wphrmEmployeeInfo');
        if (empty($wphrmInfo)) {
            echo esc_html('invalid');
            exit;
        }
        $wphrmEmployeeJoiningYear = date('Y');
        $wphrmEmployeeJoiningMonth = date('m');
        if (isset($wphrmInfo['wphrm_employee_joining_date'])) :
            $wphrmEmployeeJoiningYear = date('Y', strtotime($wphrmInfo['wphrm_employee_joining_date']));
            $wphrmEmployeeJoiningMonth = date('m', strtotime($wphrmInfo['wphrm_employee_joining_date']));
        endif;
        ?>
        <div class="portlet-body">
            <div class="table-scrollable">

                <table class="table table-striped table-bordered table-hover" >
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><?php _e('Months', 'wphrm'); ?></th>
                            <th><?php _e('Action', 'wphrm'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $months = $this->wphrmMainClass->WPHRMGetMonths();
                        $currentMonth = esc_sql(date('F')); // esc
                        $current_month = esc_sql(date('m')); // esc
                        $currentYear = esc_sql(date('Y')); // esc
                        $i = 1;
                        $wphrm_generated = array();
                        $wphrm_generatedreturn = array();
                        $wphrm_generated_salary = $wpdb->get_results("SELECT * FROM $this->salaryTable WHERE `employeeID`=$wphrmEmployeeID AND year(`date`)='$wphrmYear'");
                        $wphrmGenerate = $wpdb->get_results("SELECT * FROM $this->salaryTable WHERE `employeeID` = $wphrmEmployeeID");
                        foreach ($wphrm_generated_salary as $G => $generated_salary) {
                            $wphrm_generated[] = date('F', strtotime($generated_salary->date));
                        }


                        $wphrm_generatedreturn = $this->wphrmMainClass->wphrmCurrentMonth($wphrm_generated, $months);
                        foreach ($months as $k => $month) {
                            if (($wphrmEmployeeJoiningYear <= $currentYear && $wphrmEmployeeJoiningMonth <= $k) || ($currentYear == $wphrmYear) && $k <= $current_month) {
                                if (!empty($wphrm_generated)) {
                                    $wphrm_created = false;
                                    ?>
                                    <tr id="row102">
                                        <td> <?php echo esc_html($i); ?> </td>
                                        <td><?php esc_html_e($month, 'wphrm'); ?></td>
                                        <?php if (isset($wphrm_generatedreturn[$k]) == $month) { ?>
                                            <td> 
                                                <?php if (in_array('manageOptionsSalary', $wphrmGetPagePermissions)) { ?>
                                                    <button type="button" onclick="createSalary('<?php echo esc_js($month) ?>')" href="#" class="btn btn-xs blue">
                                                        <i class="fa fa-pencil"></i><?php _e('Edit', 'wphrm'); ?>
                                                    </button>
                                                <?php } ?>
                                                <button type="button" onclick="wphrm_pdf_generator('wphrm-salary-slip-pdf', <?php echo esc_js($wphrmEmployeeID); ?>, '<?php echo esc_js($month) ?>')" href="#" class="btn btn-xs purple">
                                                    <i class="fa fa-download"></i><?php _e('Download', 'wphrm'); ?>
                                                </button>
                                            </td>
                                            <?php
                                            $wphrm_created = true;
                                        }
                                        ?>
                                        <?php
                                        if ($current_month < $k) {
                                            if ($wphrm_created != true):
                                                ?>
                                                <td>
                                                    <?php if (in_array('manageOptionsSalary', $wphrmGetPagePermissions)) { ?>
                                                        <?php
                                                        if ($wphrmYear != $currentYear) :
                                                            if (!empty($wphrmGenerate)) :
                                                                ?>
                                                                <button type="button" href="#duplicateModal" onclick="wphrmDuplicate('<?php echo esc_js($month) ?>')" data-toggle="modal" class="btn btn-xs blue">
                                                                    <i class="fa fa-plus"></i><?php _e('Create', 'wphrm'); ?>
                                                                </button>
                                                            <?php else : ?>
                                                                <button type="button" onclick="createSalary('<?php echo esc_js($month) ?>')" href="#" class="btn btn-xs blue">
                                                                    <i class="fa fa-plus"></i><?php _e('Create', 'wphrm'); ?>
                                                                </button><?php
                                                            endif;
                                                        endif;
                                                        ?>
                                                    <?php } else { ?>
                                                        <?php
                                                        if ($wphrmYear != $currentYear) :
                                                            $montharr = $this->wphrmMainClass->WPHRMRequestButtonDisable($wphrmYear);
                                                            if ((isset($montharr['month']) && $montharr['month'] != '') && (isset($montharr['year']) && $montharr['year'] != '')) {
                                                                if (in_array($month, $montharr['month'])) {
                                                                    ?>
                                                                    <button type="button" disabled  href="#" class="btn btn-xs blue">
                                                                        <i class="fa fa-file-text-o"></i><?php _e('Request', 'wphrm'); ?>
                                                                    </button>
                                                            <?php } else { ?>
                                                                    <button type="button" onclick="salary_request(<?php echo esc_js($wphrmEmployeeID); ?>, '<?php echo esc_js($month) ?>')" href="#" class="btn btn-xs blue">
                                                                        <i class="fa fa-file-text-o"></i><?php _e('Request', 'wphrm'); ?>
                                                                    </button>
                                                                <?php
                                                            }
                                                        } else {
                                                            ?> 
                                                                <button type="button" onclick="salary_request(<?php echo esc_js($wphrmEmployeeID); ?>, '<?php echo esc_js($month) ?>')" href="#" class="btn btn-xs blue">
                                                                    <i class="fa fa-file-text-o"></i><?php _e('Request', 'wphrm'); ?>
                                                                </button>
                                                            <?php
                                                        }
                                                    endif;
                                                    ?>
                                                <?php } ?>
                                                </td>
                                                <?php
                                            endif;
                                        } else {
                                            ?>
                                            <?php
                                            if (in_array('manageOptionsSalary', $wphrmGetPagePermissions)) {
                                                if ($wphrm_created != true):
                                                    ?>
                                                    <td>
                                                        <?php if (!empty($wphrmGenerate)) : ?>
                                                            <button type="button" href="#duplicateModal" onclick="wphrmDuplicate('<?php echo esc_js($month) ?>')" data-toggle="modal" class="btn btn-xs blue">
                                                                <i class="fa fa-plus"></i><?php _e('Create', 'wphrm'); ?>
                                                            </button>
                                                        <?php else : ?>
                                                        
                                                            <button type="button" onclick="createSalary('<?php echo esc_js($month) ?>')" href="#" class="btn btn-xs blue">
                                                                <i class="fa fa-plus"></i><?php _e('Create', 'wphrm'); ?>
                                                            </button>
                                                        <?php endif; ?>
                                                    </td>                                            
                                                    <?php
                                                endif;
                                            } else {
                                                if ($wphrm_created != true):$montharr = $this->wphrmMainClass->WPHRMRequestButtonDisable($wphrmYear);
                                                    if ((isset($montharr['month']) && $montharr['month'] != '') && (isset($montharr['year']) && $montharr['year'] != '')) {
                                                        if (in_array($month, $montharr['month'])) {
                                                            ?>
                                                            <td>
                                                                <button type="button" disabled  href="#" class="btn btn-xs blue">
                                                                    <i class="fa fa-file-text-o"></i><?php _e('Request', 'wphrm'); ?>
                                                                </button>
                                                            </td> 
                                                        <?php } else { ?>
                                                            <td>
                                                                <button type="button" onclick="salary_request(<?php echo esc_js($wphrmEmployeeID); ?>, '<?php echo esc_js($month) ?>')" href="#" class="btn btn-xs blue">
                                                                    <i class="fa fa-file-text-o"></i><?php _e('Request', 'wphrm'); ?>
                                                                </button>
                                                            </td>    
                                                            <?php
                                                        }
                                                    } else {
                                                        ?> 

                                                        <td>
                                                            <button type="button" onclick="salary_request(<?php echo esc_js($wphrmEmployeeID); ?>, '<?php echo esc_js($month) ?>')" href="#" class="btn btn-xs blue">
                                                                <i class="fa fa-file-text-o"></i><?php _e('Request', 'wphrm'); ?>
                                                            </button>
                                                        </td> 

                                                        <?php
                                                    } endif;
                                            }
                                        }
                                        ?>
                                    </tr>
                                <?php } else { ?>
                                    <tr id="row102">
                                        <td> <?php echo esc_html($i); ?> </td>
                                        <td><?php esc_html_e($month, 'wphrm'); ?></td>
                                        <?php if (in_array('manageOptionsSalary', $wphrmGetPagePermissions)) { ?>
                                            <td>
                                                <?php if ($current_month >= $k || $wphrmYear != $currentYear) : ?>
                                                    <?php if (!empty($wphrmGenerate)) : ?>
                                                        <button type="button" href="#duplicateModal" onclick="wphrmDuplicate('<?php echo esc_js($month) ?>')" data-toggle="modal" class="btn btn-xs blue">
                                                            <i class="fa fa-plus"></i><?php _e('Create', 'wphrm'); ?>
                                                        </button>
                                                    <?php else : ?>
                                                        <button type="button" onclick="createSalary('<?php echo esc_js($month) ?>')" href="#" class="btn btn-xs blue">
                                                            <i class="fa fa-plus"></i><?php _e('Create', 'wphrm'); ?>
                                                        </button>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                            <?php
                                        } else {
                                            $montharr = $this->wphrmMainClass->WPHRMRequestButtonDisable($wphrmYear);
                                            if ((isset($montharr['month']) && $montharr['month'] != '') && (isset($montharr['year']) && $montharr['year'] != '')) {
                                                if (in_array($month, $montharr['month'])) {
                                                    ?>
                                                    <td>
                                                        <button type="button" disabled  href="#" class="btn btn-xs blue">
                                                            <i class="fa fa-file-text-o"></i><?php _e('Request', 'wphrm'); ?>
                                                        </button>
                                                    </td> 
                                                    <?php
                                                } else {
                                                    if ($wphrmEmployeeJoiningYear != $wphrmYear) {
                                                        if ($currentYear == $wphrmYear && $k <= $current_month) {
                                                            ?>
                                                            <td>
                                                                <button type="button" onclick="salary_request(<?php echo esc_js($wphrmEmployeeID); ?>, '<?php echo esc_js($month) ?>')" href="#" class="btn btn-xs blue">
                                                                    <i class="fa fa-file-text-o"></i><?php _e('Request', 'wphrm'); ?>
                                                                </button>
                                                            </td>    
                                                            <?php
                                                        } else {
                                                            if ($currentYear != $wphrmYear) {
                                                                ?>
                                                                <td>
                                                                    <button type="button" onclick="salary_request(<?php echo esc_js($wphrmEmployeeID); ?>, '<?php echo esc_js($month) ?>')" href="#" class="btn btn-xs blue">
                                                                        <i class="fa fa-file-text-o"></i><?php _e('Request', 'wphrm'); ?>
                                                                    </button>
                                                                </td>  <?php } else { ?>
                                                                <td></td>     
                                                                <?php
                                                            }
                                                        }
                                                    } else {
                                                        ?>
                                                        <td>
                                                            <button type="button" onclick="salary_request(<?php echo esc_js($wphrmEmployeeID); ?>, '<?php echo esc_js($month) ?>')" href="#" class="btn btn-xs blue">
                                                                <i class="fa fa-file-text-o"></i><?php _e('Request', 'wphrm'); ?>
                                                            </button>
                                                        </td>
                                                        <?php
                                                    }
                                                }
                                            } else {
                                                ?> 
                                                <td>
                                                    <button type="button" onclick="salary_request(<?php echo esc_js($wphrmEmployeeID); ?>, '<?php echo esc_js($month) ?>')" href="#" class="btn btn-xs blue">
                                                        <i class="fa fa-file-text-o"></i><?php _e('Request', 'wphrm'); ?>
                                                    </button>
                                                </td> 

                                                <?php
                                            }
                                        }
                                        ?>
                                    </tr>
                                    <?php
                                }
                            } else {
                                if ($wphrmEmployeeJoiningYear == $wphrmYear) {
                                    ?>
                                    <tr id="row102">
                                        <td><?php echo esc_html($i); ?> </td>
                                        <td><?php esc_html_e($month, 'wphrm'); ?></td>
                                        <td></td>
                                    </tr>
                                    <?php
                                } else {
                                    if ($currentYear == $wphrmYear) {
                                        ?>
                                        <tr id="row102">
                                            <td><?php echo esc_html($i); ?> </td>
                                            <td><?php esc_html_e($month, 'wphrm'); ?></td>
                                            <td></td>
                                        </tr>
                                        <?php
                                    } else {
                                        if (in_array('manageOptionsSalary', $wphrmGetPagePermissions)) {
                                            ?>
                                            <tr id="row102">
                                                <td><?php echo esc_html($i); ?> </td>
                                                <td><?php esc_html_e($month, 'wphrm'); ?></td>
                                                <td> <?php if (!empty($wphrmGenerate)) : ?>
                                                        <button type="button" href="#duplicateModal" onclick="wphrmDuplicate('<?php echo esc_js($month) ?>')" data-toggle="modal" class="btn btn-xs blue">
                                                            <i class="fa fa-plus"></i><?php _e('Create', 'wphrm'); ?>
                                                        </button>
                                                    <?php else : ?>
                                                        <button type="button" onclick="createSalary('<?php echo esc_js($month) ?>')" href="#" class="btn btn-xs blue">
                                                            <i class="fa fa-plus"></i><?php _e('Create', 'wphrm'); ?>
                                                        </button>
                                                    <?php endif; ?></td>
                                            </tr>
                                        <?php } else { ?>
                                            <tr id="row102">
                                                <td><?php echo esc_html($i); ?> </td>
                                                <td><?php esc_html_e($month, 'wphrm'); ?></td>
                                                <?php
                                                $montharr = $this->wphrmMainClass->WPHRMRequestButtonDisable($wphrmYear);
                                                if ((isset($montharr['month']) && $montharr['month'] != '') && (isset($montharr['year']) && $montharr['year'] != '')) {
                                                    if (in_array($month, $montharr['month'])) {
                                                        ?>
                                                        <td>
                                                            <button type="button" disabled  href="#" class="btn btn-xs blue">
                                                                <i class="fa fa-file-text-o"></i><?php _e('Request', 'wphrm'); ?>
                                                            </button>
                                                        </td> 
                                                    <?php } else { ?>
                                                        <td>
                                                            <button type="button" onclick="salary_request(<?php echo esc_js($wphrmEmployeeID); ?>, '<?php echo esc_js($month) ?>')" href="#" class="btn btn-xs blue">
                                                                <i class="fa fa-file-text-o"></i><?php _e('Request', 'wphrm'); ?>
                                                            </button>
                                                        </td> </tr>   
                                                        <?php
                                                    }
                                                } else {
                                                    ?> 
                                                    <td>
                                                        <button type="button" onclick="salary_request(<?php echo esc_js($wphrmEmployeeID); ?>, '<?php echo esc_js($month) ?>')" href="#" class="btn btn-xs blue">
                                                            <i class="fa fa-file-text-o"></i><?php _e('Request', 'wphrm'); ?>
                                                        </button>
                                                    </td> 
                                                </tr> 
                                                <?php
                                            }
                                        }
                                    }
                                }
                            }
                            $i++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div><?php
    }
    
    /** WP-HRM Ajax Data For the Salary
     *   @argument Employee ID & Year
     *   @return Salary Data HTML
     * */
    public function WPHRMGetSalaryWeekData($wphrmEmployeeID, $wphrmYear) {
        
       ob_end_clean();
        if (ob_get_level() == 0) {
            ob_start();
        }
        global $current_user, $wpdb;
        $wphrmUserRole = implode(',', $current_user->roles);
        $wphrmGetPagePermissions = $this->wphrmMainClass->WPHRMGetPagePermissions();
        $wphrmInfo = $this->wphrmMainClass->WPHRMGetUserDatas($wphrmEmployeeID, 'wphrmEmployeeInfo');
        if (empty($wphrmInfo)) {
            echo esc_html('invalid');
            exit;
        }
        $wphrmEmployeeJoiningYear = date('Y');
        $wphrmEmployeeJoiningMonth = date('m');
        if (isset($wphrmInfo['wphrm_employee_joining_date'])) :
            $wphrmEmployeeJoiningYear = date('Y', strtotime($wphrmInfo['wphrm_employee_joining_date']));
            $wphrmEmployeeJoiningMonth = date('m', strtotime($wphrmInfo['wphrm_employee_joining_date']));
        endif;
        ?>
                <style> .btn-green {
    background: #45B6AF;
    color: #fff; padding: 1px 1px;} 
                .btn-blue {
    background: #0B92C8;
    color: #fff; padding: 1px 1px; }.fa.fa-pencil {
                                    margin-left: 3px;
                                    }
                                 .fa.fa-download {
                                    position: relative;
                                     left: -1px;
                                    }</style> 
        <div class="portlet-body">
            <div class="table-scrollable">

                <table class="table table-striped table-bordered table-hover" >
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><?php _e('Months', 'wphrm'); ?></th>
                            <th><?php _e('Action', 'wphrm'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $months = $this->wphrmMainClass->WPHRMGetMonths();
                        $currentMonth = esc_sql(date('F')); // esc
                        $current_month = esc_sql(date('m')); // esc
                        $currentYear = esc_sql(date('Y')); // esc
                        $i = 1;
                        $wphrm_generated = array();
                        $wphrm_generatedreturn = array();
                        $wphrm_generated_salary = $wpdb->get_results("SELECT * FROM $this->WeeklySalaryTable WHERE `employeeID`=$wphrmEmployeeID AND year(`date`)='$wphrmYear'");
                        $wphrmGenerate = $wpdb->get_results("SELECT * FROM $this->WeeklySalaryTable WHERE `employeeID` = $wphrmEmployeeID");
                        foreach ($wphrm_generated_salary as $G => $generated_salary) {
                            $wphrm_generated[] = date('F', strtotime($generated_salary->date));
                        }


                        $wphrm_generatedreturn = $this->wphrmMainClass->wphrmCurrentMonth($wphrm_generated, $months);
                        foreach ($months as $k => $month) {
                            if (($wphrmEmployeeJoiningYear <= $currentYear && $wphrmEmployeeJoiningMonth <= $k) || ($currentYear == $wphrmYear) && $k <= $current_month) {
                                if (!empty($wphrm_generated)) {
                                    $wphrm_created = false;
                                    ?>
                                    <tr id="row102">
                                        <td> <?php echo esc_html($i); ?> </td>
                                        <td><?php esc_html_e($month, 'wphrm'); ?></td>
                                        <?php if (isset($wphrm_generatedreturn[$k]) == $month) { ?>
                                            <td> <table>
                                                <?php if (in_array('manageOptionsSalary', $wphrmGetPagePermissions)) { ?>
                                                                        
                                                                       <?php  $monthInWeek = $this->wphrmMainClass->WPHRMWeeksInMonth($wphrmYear, $k, '6');
                                                                        $monthInSalaryGenerated = $this->wphrmMainClass->WPHRMMonthInSalaryGenerated($wphrmEmployeeID, $k, $wphrmYear);
                                                                        $count = 1;
                                                                        for ($count; $count <= $monthInWeek;) {
                                                                            if (in_array($count, $monthInSalaryGenerated)) {
                                                                                ?>
                                                                                <td>
                                                                                    <table style="margin-right: 13px;"><tr>
                                                                                           
                                                                                            <td><button type="button" onclick="createWeekSalary('<?php echo $month; ?>','<?php echo $count; ?>')" href="#" class="btn btn-xs btn-green">
                                                                                                    &nbsp;<?php $currentCount = $this->wphrmMainClass->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-pencil"></i>
                                                                                                </button></td>

                                                                                            <td><button type="button" style="position: relative; left: -1px;" onclick="wphrm_pdf_week_generator('wphrm-salary-slip-week-pdf', <?php echo $wphrmEmployeeID; ?>, '<?php echo $month ?>', '<?php echo $count ?>')" href="#" class="btn btn-xs btn-green">
                                                                                                    <i class="fa fa-download"></i>
                                                                                                </button></td></tr>
                                                                                    </table>
                                                                                </td>   
                                                                            <?php } else { ?>
                                                                                <td><table style="margin-right: 13px;"><tr>
                                                                                            <?php if(!empty($wphrmGenerate)) { ?>
                                                                                            <td><button type="button" onclick="wphrmDuplicateWeek('<?php echo $month; ?>','<?php echo $count; ?>')" href="#duplicateModal" data-toggle="modal" class="btn btn-xs btn-blue">
                                                                                                    &nbsp;<?php $currentCount = $this->wphrmMainClass->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-plus"></i>
                                                                                                </button></td>
                                                                                            <?php }else{ ?>
                                                                                              <td><button type="button" onclick="createWeekSalary('<?php echo $month; ?>','<?php echo $count; ?>')" href="#" class="btn btn-xs btn-blue">
                                                                                                    &nbsp;<?php $currentCount = $this->wphrmMainClass->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-plus"></i>
                                                                                                </button></td>  
                                                                                            <?php } ?></tr></table>   
                                                                                </td>
                                                                                <?php
                                                                            }
                                                                            $count++;
                                                                            } $wphrm_created = true;
                                                           } else {   $monthInWeek = $this->wphrmMainClass->WPHRMWeeksInMonth($wphrmYear, $k, '6');
                                                                        $monthInSalaryGenerated = $this->wphrmMainClass->WPHRMMonthInSalaryGenerated($wphrmEmployeeID, $k, $wphrmYear);
                                                                        $count = 1;
                                                                        for ($count; $count <= $monthInWeek;) {
                                                                            if (in_array($count, $monthInSalaryGenerated)) {
                                                                                ?>
                                                                                <td>
                                                                                    <table style="margin-right: 13px;"><tr>
                                                                                            <td><button type="button" onclick="wphrm_pdf_week_generator('wphrm-salary-slip-week-pdf', <?php echo $wphrmEmployeeID; ?>, '<?php echo $month ?>', '<?php echo $count ?>')" href="#" class="btn btn-xs btn-green">
                                                                                                   &nbsp;<?php $currentCount = $this->wphrmMainClass->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-download"></i>
                                                                                                </button></td>
                                                                                            </tr>
                                                                                    </table>
                                                                                </td>   
                                                                            <?php } else { ?>
                                                                                <td><table style="margin-right: 13px;"><tr>
                                                                                            <td>
                                                                                                <button type="button" onclick="salary_Week_request(<?php echo esc_js($wphrmEmployeeID); ?>, '<?php echo esc_js($month) ?>','<?php echo esc_js($count) ?>')" href="#" class="btn btn-xs btn-blue">
                                                                                    &nbsp;<?php $currentCount = $this->wphrmMainClass->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-file-text-o"></i>
                                                                                </button></td></tr></table>   
                                                                                </td>
                                                                                <?php
                                                                            }
                                                                            $count++;
                                                                        } ?>
                                            </td>
                                            <?php
                                            $wphrm_created = true;
                                            } ?> </table> <?php }
                                        ?>
                                        <?php
                                        if ($current_month < $k) {
                                            if ($wphrm_created != true):
                                                ?>
                                                <td>
                                                    <?php if (in_array('manageOptionsSalary', $wphrmGetPagePermissions)) { ?>
                                                    <table>
                                                        <?php
                                                        if ($wphrmYear != $currentYear) :
                                                            $monthInWeek = $this->wphrmMainClass->WPHRMWeeksInMonth($wphrmYear, $k, '6');
                                                                        $monthInSalaryGenerated = $this->wphrmMainClass->WPHRMMonthInSalaryGenerated($wphrmEmployeeID, $k, $wphrmYear);
                                                                        $count = 1;
                                                                        for ($count; $count <= $monthInWeek;) {
                                                                            if (in_array($count, $monthInSalaryGenerated)) {
                                                                                ?>
                                                                                <td>
                                                                                    <table style="margin-right: 13px;"><tr>
                                                                                           
                                                                                            <td><button type="button" onclick="createWeekSalary('<?php echo $month; ?>','<?php echo $count; ?>')" href="#" class="btn btn-xs btn-green">
                                                                                                    &nbsp;<?php $currentCount = $this->wphrmMainClass->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-pencil"></i>
                                                                                                </button></td>

                                                                                            <td><button type="button" style="position: relative; left: -1px;" onclick="wphrm_pdf_week_generator('wphrm-salary-slip-week-pdf', <?php echo $wphrmEmployeeID; ?>, '<?php echo $month ?>', '<?php echo $count ?>')" href="#" class="btn btn-xs btn-green">
                                                                                                    <i class="fa fa-download"></i>
                                                                                                </button></td></tr>
                                                                                    </table>
                                                                                </td>   
                                                                            <?php } else { ?>
                                                                                <td><table style="margin-right: 13px;"><tr>
                                                                                            <?php if(!empty($wphrmGenerate)) { ?>
                                                                                            <td><button type="button" onclick="wphrmDuplicateWeek('<?php echo $month; ?>','<?php echo $count; ?>')" href="#duplicateModal" data-toggle="modal" class="btn btn-xs btn-blue">
                                                                                                    &nbsp;<?php $currentCount = $this->wphrmMainClass->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-plus"></i>
                                                                                                </button></td>
                                                                                            <?php }else{ ?>
                                                                                              <td><button type="button" onclick="createWeekSalary('<?php echo $month; ?>','<?php echo $count; ?>')" href="#" class="btn btn-xs btn-blue">
                                                                                                    &nbsp;<?php $currentCount = $this->wphrmMainClass->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-plus"></i>
                                                                                                </button></td>  
                                                                                            <?php } ?></tr></table>   
                                                                                </td>
                                                                                <?php
                                                                            }
                                                                            $count++;
                                                                        }
                                                        endif;
                                                        ?>
                                                    </table>
                                                    <?php } else { ?>
                                                        <?php
                                                        if ($wphrmYear != $currentYear) { ?>
                                                            <table>
                                                                    <?php
                                                                        $monthInWeek = $this->wphrmMainClass->WPHRMWeeksInMonth($wphrmYear, $k, '6');
                                                                        $monthInSalaryGenerated = $this->wphrmMainClass->WPHRMMonthInSalaryGenerated($wphrmEmployeeID, $k, $wphrmYear);
                                                                        $count = 1;
                                                                        for ($count; $count <= $monthInWeek;) {
                                                                            if (in_array($count, $monthInSalaryGenerated)) {
                                                                                ?>
                                                                                <td>
                                                                                    <table style="margin-right: 13px;"><tr>
                                                                                             <td><button type="button" onclick="wphrm_pdf_week_generator('wphrm-salary-slip-week-pdf', <?php echo $wphrmEmployeeID; ?>, '<?php echo $month ?>', '<?php echo $count ?>')" href="#" class="btn btn-xs btn-green">
                                                                                                    &nbsp;<?php $currentCount = $this->wphrmMainClass->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-download"></i>
                                                                                                </button></td></tr>
                                                                                    </table>
                                                                                </td>   
                                                                            <?php } else { ?>
                                                                                <td><table style="margin-right: 13px;"><tr>
                                                                                     <td>
                                                                                        <button type="button" onclick="salary_Week_request(<?php echo esc_js($wphrmEmployeeID); ?>, '<?php echo esc_js($month) ?>','<?php echo esc_js($count) ?>')" href="#" class="btn btn-xs btn-blue">
                                                                                    &nbsp;<?php $currentCount = $this->wphrmMainClass->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-file-text-o"></i>
                                                                                </button></td></tr></table>   
                                                                                </td>
                                                                                <?php
                                                                            }
                                                                            $count++;
                                                                        }
                                                                   ?>
                                                                </table>  
                                                  <?php  }
                                                    ?>
                                                <?php } ?>
                                                </td>
                                                <?php
                                            endif;
                                        } else {
                                            ?>
                                            <?php
                                            if (in_array('manageOptionsSalary', $wphrmGetPagePermissions)) {
                                              if ($wphrm_created != true){
                                                    ?>
                                                    <td><table>
                                                                    <?php
                                                                        $monthInWeek = $this->wphrmMainClass->WPHRMWeeksInMonth($wphrmYear, $k, '6');
                                                                        $monthInSalaryGenerated = $this->wphrmMainClass->WPHRMMonthInSalaryGenerated($wphrmEmployeeID, $k, $wphrmYear);
                                                                        $count = 1;
                                                                        for ($count; $count <= $monthInWeek;) {
                                                                            if (in_array($count, $monthInSalaryGenerated)) {
                                                                                ?>
                                                                                <td>
                                                                                    <table style="margin-right: 13px;"><tr>
                                                                                            <td><button type="button" onclick="createWeekSalary('<?php echo $month; ?>','<?php echo $count; ?>')" href="#" class="btn btn-xs btn-green">
                                                                                                    &nbsp;<?php $currentCount = $this->wphrmMainClass->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-pencil"></i>
                                                                                                </button></td>

                                                                                            <td><button type="button" style="position: relative; left: -1px;" onclick="wphrm_pdf_week_generator('wphrm-salary-slip-week-pdf', <?php echo $wphrmEmployeeID; ?>, '<?php echo $month ?>', '<?php echo $count ?>')" href="#" class="btn btn-xs btn-green">
                                                                                                    <i class="fa fa-download"></i>
                                                                                                </button></td></tr>
                                                                                    </table>
                                                                                </td>   
                                                                            <?php } else { ?>
                                                                                <td><table style="margin-right: 13px;"><tr>
                                                                                    <?php if(!empty($wphrmGenerate)) { ?>
                                                                                            <td><button type="button" onclick="wphrmDuplicateWeek('<?php echo $month; ?>','<?php echo $count; ?>')" href="#duplicateModal" data-toggle="modal" class="btn btn-xs btn-blue">
                                                                                                    &nbsp;<?php $currentCount = $this->wphrmMainClass->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-plus"></i>
                                                                                                </button></td>
                                                                                            <?php }else{ ?>
                                                                                              <td><button type="button" onclick="createWeekSalary('<?php echo $month; ?>','<?php echo $count; ?>')" href="#" class="btn btn-xs btn-blue">
                                                                                                    &nbsp;<?php $currentCount = $this->wphrmMainClass->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-plus"></i>
                                                                                                </button></td>  
                                                                                            <?php } ?></tr></table>   
                                                                                </td>
                                                                                <?php
                                                                            }
                                                                            $count++;
                                                                        }
                                                                   ?>
                                                        </table> </td>                                            
                                                    <?php
                                                }
                                            } else {
                                                if ($wphrm_created != true){ ?>
                                                    <td><table>
                                                                    <?php
                                                                        $monthInWeek = $this->wphrmMainClass->WPHRMWeeksInMonth($wphrmYear, $k, '6');
                                                                        $monthInSalaryGenerated = $this->wphrmMainClass->WPHRMMonthInSalaryGenerated($wphrmEmployeeID, $k, $wphrmYear);
                                                                        $count = 1;
                                                                        for ($count; $count <= $monthInWeek;) {
                                                                            if (in_array($count, $monthInSalaryGenerated)) {
                                                                                ?>
                                                                                <td>
                                                                                    <table style="margin-right: 13px;"><tr>
                                                                                             <td><button type="button" onclick="wphrm_pdf_week_generator('wphrm-salary-slip-week-pdf', <?php echo $wphrmEmployeeID; ?>, '<?php echo $month ?>', '<?php echo $count ?>')" href="#" class="btn btn-xs btn-green" >
                                                                                                    &nbsp;<?php $currentCount = $this->wphrmMainClass->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-download"></i>
                                                                                                </button></td></tr>
                                                                                    </table>
                                                                                </td>   
                                                                            <?php } else { ?>
                                                                                <td><table style="margin-right: 13px;"><tr>
                                                                                     <td>
                                                                                        <button type="button" onclick="salary_Week_request(<?php echo esc_js($wphrmEmployeeID); ?>, '<?php echo esc_js($month) ?>','<?php echo esc_js($count) ?>')" href="#" class="btn btn-xs btn-blue">
                                                                                    &nbsp;<?php $currentCount = $this->wphrmMainClass->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-file-text-o"></i>
                                                                                </button></td></tr></table>   
                                                                                </td>
                                                                                <?php
                                                                            }
                                                                            $count++;
                                                                        }
                                                                   ?>
                                                        </table> </td>
                                                   <?php }
                                            }
                                        }
                                        ?>
                                    </tr>
                                <?php } else { ?>
                                    <tr id="row102">
                                        <td> <?php echo esc_html($i); ?> </td>
                                        <td><?php esc_html_e($month, 'wphrm'); ?></td>
                                        <td>  <table>
                                        <?php if (in_array('manageOptionsSalary', $wphrmGetPagePermissions)) { ?>
                                            
                                                <?php if ($current_month >= $k || $wphrmYear != $currentYear) { ?>
                                                
                                                                    <?php
                                                                        $monthInWeek = $this->wphrmMainClass->WPHRMWeeksInMonth($wphrmYear, $k, '6');
                                                                        $monthInSalaryGenerated = $this->wphrmMainClass->WPHRMMonthInSalaryGenerated($wphrmEmployeeID, $k, $wphrmYear);
                                                                        $count = 1;
                                                                        for ($count; $count <= $monthInWeek;) {
                                                                            if (in_array($count, $monthInSalaryGenerated)) {
                                                                                ?>
                                                                                <td>
                                                                                    <table style="margin-right: 13px;"><tr>
                                                                                           
                                                                                            <td><button type="button" onclick="createWeekSalary('<?php echo $month; ?>','<?php echo $count; ?>')" href="#" class="btn btn-xs btn-green">
                                                                                                    &nbsp;<?php $currentCount = $this->wphrmMainClass->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-pencil"></i>
                                                                                                </button></td>

                                                                                            <td><button type="button" style="position: relative; left: -1px;" onclick="wphrm_pdf_week_generator('wphrm-salary-slip-week-pdf', <?php echo $wphrmEmployeeID; ?>, '<?php echo $month ?>', '<?php echo $count ?>')" href="#" class="btn btn-xs btn-green">
                                                                                                    <i class="fa fa-download"></i>
                                                                                                </button></td></tr>
                                                                                    </table>
                                                                                </td>   
                                                                            <?php } else { ?>
                                                                                <td><table style="margin-right: 13px;"><tr>
                                                                                            <?php if(!empty($wphrmGenerate)) { ?>
                                                                                            <td><button type="button" onclick="wphrmDuplicateWeek('<?php echo $month; ?>','<?php echo $count; ?>')" href="#duplicateModal" data-toggle="modal" class="btn btn-xs btn-blue">
                                                                                                    &nbsp;<?php $currentCount = $this->wphrmMainClass->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-plus"></i>
                                                                                                </button></td>
                                                                                            <?php }else{ ?>
                                                                                              <td><button type="button" onclick="createWeekSalary('<?php echo $month; ?>','<?php echo $count; ?>')" href="#" class="btn btn-xs btn-blue">
                                                                                                    &nbsp;<?php $currentCount = $this->wphrmMainClass->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-plus"></i>
                                                                                                </button></td>  
                                                                                            <?php } ?></tr></table>   
                                                                                </td>
                                                                                <?php
                                                                            }
                                                                            $count++;
                                                                        }
                                                                   ?>

                                                                 
                                                
                                                <?php } ?>
                                           
                                            <?php
                                        } else { if ($current_month >= $k || $wphrmYear != $currentYear) { ?>
                                             
                                                                    <?php
                                                                        $monthInWeek = $this->wphrmMainClass->WPHRMWeeksInMonth($wphrmYear, $k, '6');
                                                                        $monthInSalaryGenerated = $this->wphrmMainClass->WPHRMMonthInSalaryGenerated($wphrmEmployeeID, $k, $wphrmYear);
                                                                        $count = 1;
                                                                        for ($count; $count <= $monthInWeek;) {
                                                                            if (in_array($count, $monthInSalaryGenerated)) {
                                                                                ?>
                                                                                <td>
                                                                                    <table style="margin-right: 13px;"><tr>
                                                                                             <td><button type="button" onclick="wphrm_pdf_week_generator('wphrm-salary-slip-week-pdf', <?php echo $wphrmEmployeeID; ?>, '<?php echo $month ?>', '<?php echo $count ?>')" href="#" class="btn btn-xs btn-green">
                                                                                                   &nbsp;<?php $currentCount = $this->wphrmMainClass->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-download"></i>
                                                                                                </button></td></tr>
                                                                                    </table>
                                                                                </td>   
                                                                            <?php } else { ?>
                                                                                <td><table style="margin-right: 13px;"><tr>
                                                                                     <td>
                                                                                        <button type="button" onclick="salary_Week_request(<?php echo esc_js($wphrmEmployeeID); ?>, '<?php echo esc_js($month) ?>','<?php echo esc_js($count) ?>')" href="#" class="btn btn-xs btn-blue">
                                                                                    &nbsp;<?php $currentCount = $this->wphrmMainClass->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-file-text-o"></i>
                                                                                </button></td></tr></table>   
                                                                                </td>
                                                                                <?php
                                                                            }
                                                                            $count++;
                                                                        }
                                                                   ?>
                                                               
                                        <?php } }
                                        ?></table></td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                if ($wphrmEmployeeJoiningYear == $wphrmYear) {
                                    ?>
                                    <tr id="row102">
                                        <td><?php echo esc_html($i); ?> </td>
                                        <td><?php esc_html_e($month, 'wphrm'); ?></td>
                                        <td></td>
                                    </tr>
                                    <?php
                                } else {
                                    if ($currentYear == $wphrmYear) {
                                        ?>
                                        <tr id="row102">
                                            <td><?php echo esc_html($i); ?> </td>
                                            <td><?php esc_html_e($month, 'wphrm'); ?></td>
                                            <td></td>
                                        </tr>
                                        <?php
                                    } else {
                                        if (in_array('manageOptionsSalary', $wphrmGetPagePermissions)) {
                                            ?>
                                            <tr id="row102">
                                                <td><?php echo esc_html($i); ?> </td>
                                                <td><?php esc_html_e($month, 'wphrm'); ?></td>
                                                <td> <table>
                                                                    <?php
                                                                        $monthInWeek = $this->wphrmMainClass->WPHRMWeeksInMonth($wphrmYear, $k, '6');
                                                                        $monthInSalaryGenerated = $this->wphrmMainClass->WPHRMMonthInSalaryGenerated($wphrmEmployeeID, $k, $wphrmYear);
                                                                        $count = 1;
                                                                        for ($count; $count <= $monthInWeek;) {
                                                                            if (in_array($count, $monthInSalaryGenerated)) {
                                                                                ?>
                                                                                <td>
                                                                                    <table style="margin-right: 13px;"><tr>
                                                                                           
                                                                                            <td><button type="button" onclick="createWeekSalary('<?php echo $month; ?>','<?php echo $count; ?>')" href="#" class="btn btn-xs btn-green">
                                                                                                   &nbsp;<?php $currentCount = $this->wphrmMainClass->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-pencil"></i>
                                                                                                </button></td>

                                                                                            <td><button type="button" style="position: relative; left: -1px;" onclick="wphrm_pdf_week_generator('wphrm-salary-slip-week-pdf', <?php echo $wphrmEmployeeID; ?>, '<?php echo $month ?>', '<?php echo $count ?>')" href="#" class="btn btn-xs btn-green">
                                                                                                    <i class="fa fa-download"></i>
                                                                                                </button></td></tr>
                                                                                    </table>
                                                                                </td>   
                                                                            <?php } else { ?>
                                                                                <td><table style="margin-right: 13px;"><tr>
                                                                                            <?php if(!empty($wphrmGenerate)) { ?>
                                                                                            <td><button type="button" onclick="wphrmDuplicateWeek('<?php echo $month; ?>','<?php echo $count; ?>')" href="#duplicateModal" data-toggle="modal" class="btn btn-xs btn-blue">
                                                                                                   &nbsp;<?php $currentCount = $this->wphrmMainClass->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-plus"></i>
                                                                                                </button></td>
                                                                                            <?php }else{ ?>
                                                                                              <td><button type="button" onclick="createWeekSalary('<?php echo $month; ?>','<?php echo $count; ?>')" href="#" class="btn btn-xs btn-blue">
                                                                                                    &nbsp;<?php $currentCount = $this->wphrmMainClass->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-plus"></i>
                                                                                                </button></td>  
                                                                                            <?php } ?></tr></table>   
                                                                                </td>
                                                                                <?php
                                                                            }
                                                                            $count++;
                                                                        }
                                                                   ?>

                                                                </table> </td>
                                            </tr>
                                        <?php } else {
                                            ?>
                                            <tr id="row102">
                                                <td><?php echo esc_html($i); ?> </td>
                                                <td><?php esc_html_e($month, 'wphrm'); ?></td>
                                               
                                               <td>
                                                  <table>
                                                                    <?php
                                                                        $monthInWeek = $this->wphrmMainClass->WPHRMWeeksInMonth($wphrmYear, $k, '6');
                                                                        $monthInSalaryGenerated = $this->wphrmMainClass->WPHRMMonthInSalaryGenerated($wphrmEmployeeID, $k, $wphrmYear);
                                                                        $count = 1;
                                                                        for ($count; $count <= $monthInWeek;) {
                                                                            if (in_array($count, $monthInSalaryGenerated)) {
                                                                                ?>
                                                                                <td>
                                                                                    <table style="margin-right: 13px;"><tr>
                                                                                             <td><button type="button" onclick="wphrm_pdf_week_generator('wphrm-salary-slip-week-pdf', <?php echo $wphrmEmployeeID; ?>, '<?php echo $month ?>', '<?php echo $count ?>')" href="#" class="btn btn-xs btn-green">
                                                                                                   &nbsp;<?php $currentCount = $this->wphrmMainClass->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-download"></i>
                                                                                                </button></td></tr>
                                                                                    </table>
                                                                                </td>   
                                                                            <?php } else { ?>
                                                                                <td><table style="margin-right: 13px;"><tr>
                                                                                     <td>
                                                                                        <button type="button" onclick="salary_Week_request(<?php echo esc_js($wphrmEmployeeID); ?>, '<?php echo esc_js($month) ?>','<?php echo esc_js($count) ?>')" href="#" class="btn btn-xs btn-blue">
                                                                                   &nbsp;<?php $currentCount = $this->wphrmMainClass->WPHRMWeekCount($count); echo $currentCount; ?><i class="fa fa-file-text-o"></i>
                                                                                </button></td></tr></table>   
                                                                                </td>
                                                                                <?php
                                                                            }
                                                                            $count++;
                                                                        }
                                                                   ?>
                                                                </table>   
                                               </td>     
                                       <?php }
                                    }
                                }
                            }
                            $i++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div><?php
    
    }
    
 
}
?>