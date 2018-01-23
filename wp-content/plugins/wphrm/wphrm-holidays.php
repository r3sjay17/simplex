<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $current_user, $wpdb;
$wphrmUserRole = implode(',', $current_user->roles);
$readonly_class = '';
$readonly = '';
$edit_mode = false;
$wphrm_messages_holiday = $this->WPHRMGetMessage(14);
$wphrmGetPagePermissions = $this->WPHRMGetPagePermissions();
wp_enqueue_script('wphrm-holiday-year-js');
?>
<script>
jQuery(document).ready(function () {
    jQuery('#calendar_year').bic_calendar({ });
});
</script>
<div class="preloader">
<span class="preloader-custom-gif"></span>
</div>
<div id="static" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
                <h4 class="modal-title"><strong><?php _e('Holidays', 'wphrm'); ?></strong></h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-success display-hide" id="holiday_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrm_messages_holiday); ?>
                    <button class="close" data-close="alert"></button>
                </div>
                <div class="alert alert-danger display-hide" id="holiday_error">
                    <button class="close" data-close="alert"></button>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    <form action="#" method="post" id="wphrmAddHolidays_frm" class="form-horizontal">
                        <div class="form-body">
                            <div class="form-group">
                                <div class="col-md-5">
                                    <input class="form-control form-control-inline input-medium date-picker-default" id="holiday_date" data-date-format="dd-mm-yyyy" name="holiday_date[]"  type="text" value="" placeholder="Date"/>
                                </div>
                                <div class="col-md-5">
                                    <input class="form-control form-control-inline occasioncl"  type="text" id="occasion" name="occasion[]" placeholder="<?php _e('*Must enter the Occasion', 'wphrm'); ?>"/>
                                </div>
                            </div>
                            <div id="insertBefore"></div>
                            <div class="form-group">
                                  <div class="col-md-12">
                                  <button type="button" id="plusButton" class="btn btn-sm green form-control-inline">
                                <i class="fa fa-plus"></i><?php _e('Add More', 'wphrm'); ?>
                            </button>
                                  </div></div>
                    <p class="description">Any Weekends conflict with holidays, it will be overitten and make set as holiday.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="submit"  class=" btn blue"> <i class="fa fa-plus"></i><?php _e('Add Holidays', 'wphrm'); ?></button>
                            <button type="button" data-dismiss="modal" aria-hidden="true" class="btn red"><i class="fa fa-times"></i><?php _e('Cancel', 'wphrm'); ?></button>
                        </div>
                    </form>
                    <!-- END FORM-->
                </div>
            </div>
        </div></div></div>
<!--Create year in Weekend-->
<div id="wphrm-Add-weekand" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
                <h4 class="modal-title"><strong><i class="fa fa-plus"></i><?php _e('Add Weekends', 'wphrm'); ?></strong></h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-success display-hide" id="weekend_success">
                    <button class="close" data-close="alert"></button>
                </div>
                <div class="alert alert-danger display-hide" id="weekend_error">
                    <button class="close" data-close="alert"></button>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    <form action="#" method="post" id="add-year-in-weekendfrm" class="form-horizontal">
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Year', 'wphrm'); ?>  </label>
                                <div class="col-md-9">
                                    <select class ="form-control select2me"  data-live-search="true" id="wphrmyear" name="wphrmyear">
                                        <option value=""><?php _e('Select Year', 'wphrm'); ?></option>
                                        <?php
                                        $current_year = date("Y");
                                        $current_year = $current_year + 5;
                                        $years_array = range($current_year, 2010);
                                        foreach ($years_array as $years_key => $years_arrays) { ?>
                                            <option value="<?php echo esc_attr($years_arrays); ?>" <?php
                                            if ($current_year == $years_arrays) { echo esc_attr('selected ="selected"'); } ?>>
                                                <?php echo esc_html($years_arrays); ?>
                                            </option>
                                            <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="control-label col-md-3"><?php _e('Weekend', 'wphrm'); ?>  </label>
                                    <div class="col-md-9">
                                        <select class ="form-control select2me"  data-live-search="true" id="wphrmWeekend" name="wphrmWeekend">
                          <option value=""><?php _e('Select Weekend', 'wphrm'); ?></option>
                                            <option value="Monday"><?php _e('Monday', 'wphrm'); ?></option>
                                            <option value="Tuesday"><?php _e('Tuesday', 'wphrm'); ?></option>
                                            <option value="Wednesday"><?php _e('Wednesday', 'wphrm'); ?></option>
                                            <option value="Thursday"><?php _e('Thursday', 'wphrm'); ?></option>
                                            <option value="Friday"><?php _e('Friday', 'wphrm'); ?></option>
                                            <option value="Saturday"><?php _e('Saturday', 'wphrm'); ?></option>
                                            <option value="Sunday"><?php _e('Sunday', 'wphrm'); ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                         <div class="form-body">
                                <div class="form-group">
                                    <label class="control-label col-md-3"><?php _e('Choose Week', 'wphrm'); ?>  </label>
                                    <div class="col-md-9" style="margin-top: 6px;">
                                        <input checked="checked" value="0" type="checkbox"   name="wphrmType[]" style="margin: 0px 0px 0px !important;"> &nbsp;<?php _e('First Week', 'wphrm'); ?><br>
                                        <input checked="checked" value="1" type="checkbox" name="wphrmType[]" style="margin: 0px 0px 0px !important;"> &nbsp;<?php _e('Second Week', 'wphrm'); ?><br>
                                         <input checked="checked" value="2" type="checkbox"   name="wphrmType[]" style="margin: 0px 0px 0px !important;"> &nbsp;<?php _e('Third Week', 'wphrm'); ?><br>
                                       <input checked="checked" value="3" type="checkbox"   name="wphrmType[]" style="margin: 0px 0px 0px !important;"> &nbsp;<?php _e('Fourth Week', 'wphrm'); ?><br>
                                       <input checked="checked" value="4" type="checkbox"   name="wphrmType[]" style="margin: 0px 0px 0px !important;"> &nbsp;<?php _e('Fifth Week', 'wphrm'); ?><br>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit"  class=" btn blue"> <i class="fa fa-plus"></i><?php _e('Add weekends', 'wphrm'); ?></button>
                            <button type="button" data-dismiss="modal" aria-hidden="true" class="btn red"><i class="fa fa-times"></i><?php _e('Cancel', 'wphrm'); ?></button>
                        </div>
                    </form>
                    <!-- END FORM-->
                </div>
            </div>
        </div></div></div>
<!--Remove Weekends-->
<div id="wphrm-remove-weekend" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
                <h4 class="modal-title"><strong><i class="fa fa-plus"></i><?php _e('Remove Weekends', 'wphrm'); ?></strong></h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-success display-hide" id="weekend_success">
                    <button class="close" data-close="alert"></button>
                </div>
                <div class="alert alert-danger display-hide" id="weekend_error">
                    <button class="close" data-close="alert"></button>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    <form action="#" method="post" id="remove-weekend-month" class="form-horizontal">
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Year', 'wphrm'); ?>  </label>
                                <div class="col-md-9">
                                    <select class ="form-control select2me"  data-live-search="true" id="wphrmyear" name="wphrmyear">
                                        <option value=""><?php _e('Select Year', 'wphrm'); ?></option>
                                        <?php
                                        $current_year = date("Y");
                                        $current_year = $current_year + 5;
                                        $years_array = range($current_year, 2010);
                                        foreach ($years_array as $years_key => $years_arrays) { ?>
                                            <option value="<?php echo esc_attr($years_arrays); ?>" <?php
                                            if ($current_year == $years_arrays) { echo esc_attr('selected ="selected"'); } ?>>
                                                <?php echo esc_html($years_arrays); ?>
                                            </option>
                                            <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="control-label col-md-3"><?php _e('Month/s', 'wphrm'); ?>  </label>
                                    <div class="col-md-9">
                                        <select class ="form-control select2me"  data-live-search="true" id="wphrmmonth" name="wphrmmonth">
                                            <option value=""><?php _e('Show All', 'wphrm'); ?></option>
                                            <?php for($m = 1; $m <= 12; $m++): ?>
                                            <option value="<?php echo $m; ?>"><?php echo date('F', strtotime("2000-$m-01")); ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="control-label col-md-3"><?php _e('Weekend', 'wphrm'); ?>  </label>
                                    <div class="col-md-9">
                                        <select class ="form-control select2me"  data-live-search="true" id="wphrmWeekend" name="wphrmWeekend">
                          <option value=""><?php _e('Select Weekend', 'wphrm'); ?></option>
                                            <option value="Monday"><?php _e('Monday', 'wphrm'); ?></option>
                                            <option value="Tuesday"><?php _e('Tuesday', 'wphrm'); ?></option>
                                            <option value="Wednesday"><?php _e('Wednesday', 'wphrm'); ?></option>
                                            <option value="Thursday"><?php _e('Thursday', 'wphrm'); ?></option>
                                            <option value="Friday"><?php _e('Friday', 'wphrm'); ?></option>
                                            <option value="Saturday"><?php _e('Saturday', 'wphrm'); ?></option>
                                            <option value="Sunday"><?php _e('Sunday', 'wphrm'); ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit"  class=" btn red"> <i class="fa fa-minus"></i><?php _e('Remove weekends', 'wphrm'); ?></button>
                            <button type="button" data-dismiss="modal" aria-hidden="true" class="btn blue"><i class="fa fa-times"></i><?php _e('Cancel', 'wphrm'); ?></button>
                        </div>
                    </form>
                    <!-- END FORM-->
                </div>
            </div>
        </div></div></div>
<!-- BEGIN PAGE CONTENT-->
<div  class="col-md-12">
    <h3 class="page-title">
        <?php _e('Holidays', 'wphrm'); ?>
    </h3>
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <?php _e('Home', 'wphrm'); ?>
                <i class="fa fa-angle-right"></i>
            </li>
            <li>
                <?php _e('Holidays', 'wphrm'); ?>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php if (in_array('manageOptionsHolidays', $wphrmGetPagePermissions)) { ?>
            <a class="btn green " href="#static" data-toggle="modal" onclick="setDateActive()"><i class="fa fa-plus"></i><?php _e('Add Holidays', 'wphrm'); ?> </a>
            <a class="btn green " href="#wphrm-Add-weekand" onclick="wphrmAddweekand()" data-toggle="modal"><i class="fa fa-plus"></i><?php _e('Add Weekends', 'wphrm'); ?> </a>
            <a class="btn red " style="margin-bottom: 10px;" href="#wphrm-remove-weekend" onclick="wphrmremoveweekend()" data-toggle="modal"><i class="fa fa-minus"></i><?php _e('Remove Weekends', 'wphrm'); ?> </a>
            <?php } ?>
            <!--J@F Modified-->
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#holiday-per-year">Holidays per Year</a></li>
                <li><a data-toggle="tab" href="#holiday-per-month">Holidays per Month</a></li>
            </ul>

            <div class="tab-content">
                <div id="holiday-per-month" class="tab-pane fade in ">
                    <div class="portlet box blue calendar">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-calendar"></i><?php _e('List of Holidays', 'wphrm'); ?>
                            </div>
                        </div>
                        <input class="yeardata" type="hidden">
                         <input class="month-data" type="hidden">
                        <div class="portlet-body">
                            <div id="calendar_year"></div>

                            <input type="hidden" class="image_url" value="<?php echo esc_attr(plugins_url('assets/images/', __FILE__)) ?>">
                            <div class="row year_holidays" id="myid">
                                <div class="col-md-3">
                                    <ul class="ver-inline-menu tabbable margin-bottom-10">
                                        <?php
                                        for ($m = 1; $m <= 12; $m++) {
                                            $month[] = date('F', mktime(0, 0, 0, $m, 1, date('Y')));
                                        }
                                        $months = $month;
                                        $currentMonth = date('F');
                                        foreach ($months as $key =>  $month) {
                                            ?>
                                        <li data-id="<?php echo esc_attr($key); ?>" <?php if ($month == $currentMonth) { ?> class="active" <?php } ?> >
                                                <a data-toggle="tab"  onclick="wphrm_month('<?php echo esc_js($month); ?>');" href="#<?php echo esc_attr($month); ?>">
                                                    <i class="fa fa-calendar"></i> <?php esc_html_e($month, 'wphrm' ); ?> </a>
                                                <span class="after">
                                                </span>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                                <div class="col-md-9">
                                    <div class="tab-content">
                                        <?php foreach ($months as $month) { ?>
                                            <div id="<?php echo esc_attr($month); ?>" class="tab-pane <?php
                                            if ($month == $currentMonth) {
                                                echo esc_attr('active');
                                            }
                                            ?>">
                                                <div class="portlet box blue month_holidays" id="month_holidays">
                                                    <div class="portlet-title">
                                                        <div class="caption">
                                                            <i class="fa fa-calendar"></i><?php esc_html_e($month, 'wphrm' ); ?>
                                                        </div>
                                                        <?php if (in_array('manageOptionsLeaveApplications', $wphrmGetPagePermissions)) { ?>
                                                            <div class="action">
                                                                <a href="#" class="btn-delete-selected-holiday per_month btn btn-xs red"><i class="fa fa-trash"></i> DELETE</a>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="portlet-body">
                                                        <div class="table-scrollable">
                                                            <form method="POST" action="#" class="wphrm_delete_selected_holiday_per_month">
                                                                <table class="table table-hover">
                                                                    <thead>
                                                                        <tr> 
                                                                            <th></th>
                                                                            <th><?php _e('S.No', 'wphrm'); ?></th>
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
                                                                        $i=1;
                                                                        $current = esc_sql(date("Y-m")); // esc
                                                                        $wphrm_holidays = $wpdb->get_results("SELECT * FROM  $this->WphrmHolidaysTable where  wphrmDate BETWEEN '$current-01' AND '$current-31'");
                                                                        if(!empty($wphrm_holidays)) :
                                                                            foreach ($wphrm_holidays as $key => $wphrm_holidays_between) {
                                                                                ?>
                                                                                <tr id="row102">
                                                                                    <td><input type="checkbox" name="to_delete_holiday_per_month[]" class="to_delete_holiday_per_month" value="<?php echo esc_js($wphrm_holidays_between->id); ?>"></td>
                                                                                    <td><?php echo esc_html($i); ?></td>
                                                                                    <td> <?php
                                                                                        $whrmholidate = date('d F Y', strtotime($wphrm_holidays_between->wphrmDate));
                                                                                        echo esc_html($whrmholidate);
                                                                                        ?> </td>
                                                                                    <td> <?php echo esc_html($wphrm_holidays_between->wphrmOccassion); ?> </td>
                                                                                    <td> <?php
                                                                                        $originalDate = $wphrm_holidays_between->wphrmDate;
                                                                                        echo esc_html($newDate = date("D", strtotime($originalDate)));
                                                                                        ?> </td>
                                                                                    <td>
                                                                                        <?php if (in_array('manageOptionsHolidays', $wphrmGetPagePermissions)) { ?>
                                                                                        <button type="button" onclick="WPHRMCustomDelete(<?php echo esc_js($wphrm_holidays_between->id); ?>, '<?php echo esc_attr($this->WphrmHolidaysTable); ?>', 'id')" href="#" class="btn btn-xs red">
                                                                                                <i class="fa fa-trash"></i>
                                                                                            </button>
                                                                                        <?php } ?>
                                                                                    </td>
                                                                                </tr>
                                                                            <?php $i++; }
                                                                        else : ?>
                                                                            <tr>
                                                                                <td colspan="6" style="text-align:center"><?php _e('No holidays found.', 'wphrm'); ?></td>
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
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="holiday-per-year" class="tab-pane fade in active">
                    <div class="portlet box blue calendar">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-calendar"></i><?php _e('List of Holidays', 'wphrm'); ?>
                            </div>
                        </div>
                        <input class="yeardata" type="hidden">
                         <input class="month-data" type="hidden">
                        <div class="portlet-body">
                            <div id="calendar_year"></div>

                            <input type="hidden" class="image_url" value="<?php echo esc_attr(plugins_url('assets/images/', __FILE__)) ?>">
                            <div class="row year_holidays" id="myid">
                                <div class="col-md-2">
                                    <ul class="ver-inline-menu tabbable margin-bottom-10">
                                        <?php
                                        $year = array();
                                        $start_year = date('Y', strtotime('-1 Year', current_time('timestamp')));
                                        $end_year = date('Y', strtotime('+1 Year', current_time('timestamp')));
                                        for ($y = $start_year; $y <= $end_year; $y++) {
                                            $year[] = $y;
                                        }
                                        $years = $year;
                                        $currentYear = date('Y');
                                        foreach ($years as $key =>  $year) {
                                            ?>
                                        <li data-id="<?php echo esc_attr($key); ?>" <?php if ($year == $currentYear) { ?> class="active" <?php } ?> >
                                                <a data-toggle="tab"  onclick="wphrm_month('<?php echo esc_js($year); ?>');" href="#<?php echo esc_attr($year); ?>">
                                                    <i class="fa fa-calendar"></i> <?php esc_html_e($year, 'wphrm' ); ?> </a>
                                                <span class="after">
                                                </span>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                                <div class="col-md-10">
                                    <div class="tab-content">
                                        <?php foreach ($years as $year) { ?>
                                            <div id="<?php echo esc_attr($year); ?>" class="tab-pane <?php
                                            if ($year == $currentYear) {
                                                echo esc_attr('active');
                                            }
                                            ?>">
                                                <div class="portlet box blue month_holidays" id="month_holidays">
                                                    <div class="portlet-title">
                                                        <div class="caption">
                                                            <i class="fa fa-calendar"></i><?php esc_html_e($year, 'wphrm' ); ?>
                                                        </div>
                                                        <?php if (in_array('manageOptionsLeaveApplications', $wphrmGetPagePermissions)) { ?>
                                                            <div class="action">
                                                                <a href="#" class="btn-delete-selected-holiday btn btn-xs red"><i class="fa fa-trash"></i> DELETE</a>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="portlet-body">
                                                        <div class="table-scrollable">
                                                            <form method="POST" action="#" class="wphrm_delete_selected_holiday_per_year">
                                                                <table class="table table-hover wphrmDataTableClass">
                                                                    <thead>
                                                                        <tr> 
                                                                            <th></th>
                                                                            <th><?php _e('S.No', 'wphrm'); ?></th>
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
                                                                        $i=1;
                                                                        $current = esc_sql(date("Y")); // esc
                                                                        $wphrm_holidays = $wpdb->get_results("SELECT * FROM  $this->WphrmHolidaysTable where  wphrmDate BETWEEN '$current-01-01' AND '$current-12-31'");
                                                                        if(!empty($wphrm_holidays)) :
                                                                            foreach ($wphrm_holidays as $key => $wphrm_holidays_between) {
                                                                                ?>
                                                                                <tr id="row102">
                                                                                    <td><input type="checkbox" name="to_delete_holiday_per_year[]" class="to_delete_holiday_per_year" value="<?php echo esc_js($wphrm_holidays_between->id); ?>"></td>
                                                                                    <td><?php echo esc_html($i); ?></td>
                                                                                    <td> <?php
                                                                                        $whrmholidate = date('d F Y', strtotime($wphrm_holidays_between->wphrmDate));
                                                                                        echo esc_html($whrmholidate);
                                                                                        ?> </td>
                                                                                    <td> <?php echo esc_html($wphrm_holidays_between->wphrmOccassion); ?> </td>
                                                                                    <td> <?php
                                                                                        $originalDate = $wphrm_holidays_between->wphrmDate;
                                                                                        echo esc_html($newDate = date("D", strtotime($originalDate)));
                                                                                        ?> </td>
                                                                                    <td>
                                                                                        <?php if (in_array('manageOptionsHolidays', $wphrmGetPagePermissions)) { ?>
                                                                                        <button type="button" onclick="WPHRMCustomDelete(<?php echo esc_js($wphrm_holidays_between->id); ?>, '<?php echo esc_attr($this->WphrmHolidaysTable); ?>', 'id')" href="#" class="btn btn-xs red">
                                                                                                <i class="fa fa-trash"></i>
                                                                                            </button>
                                                                                        <?php } ?>
                                                                                    </td>
                                                                                </tr>
                                                                            <?php $i++; }
                                                                        else : ?>
                                                                            <tr>
                                                                                <td colspan="6" style="text-align:center"><?php _e('No holidays found.', 'wphrm'); ?></td>
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
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!--J@F Modified end-->
        </div>
    </div>
</div>
<!-- END PAGE CONTENT-->
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
                <button type="button" data-dismiss="modal" class="btn red" id="delete"><i class="fa fa-trash"></i><?php _e('Delete', 'wphrm'); ?> </button>
                <button type="button" data-dismiss="modal" aria-hidden="true" class="btn default"><i class="fa fa-times"></i><?php _e('Cancel', 'wphrm'); ?></button>
            </div>
        </div>
    </div>
</div>
