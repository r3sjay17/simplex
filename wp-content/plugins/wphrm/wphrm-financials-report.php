<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $current_user, $wpdb, $wp_query;
$wphrmCurrentuserId = $current_user->ID;
$wphrmUserRole = implode(',', $current_user->roles);
$wphrmFinancialAdd = $this->WPHRMGetMessage(36);
$wphrmFinancialUpdate = $this->WPHRMGetMessage(37);
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
                <h4 class="modal-title"><strong><i class="fa fa-plus"></i><?php _e('Add Transaction', 'wphrm'); ?></strong></h4>
            </div>	
            <div class="modal-body">
                <div class="portlet-body form">
                    <div class="alert alert-success display-hide" id="wphrm_financials_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrmFinancialAdd); ?>
                        <button class="close" data-close="alert"></button>
                    </div>
                    <div class="alert alert-danger display-hide" id="wphrm_financials_error">
                        <button class="close" data-close="alert"></button>
                    </div>
                    <!-- BEGIN FORM-->
                    <form method="POST"  accept-charset="UTF-8" class="form-horizontal" id="wphrm_financials_frm">
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Item', 'wphrm'); ?> </label>
                                <div class="col-md-8">
                                    <input class="form-control"   type="text" name="wphrm-item" id="wphrm_item" placeholder="Item" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Amount', 'wphrm'); ?> </label>
                                <div class="col-md-8">
                                    <input class="form-control"   type="text" name="wphrm-amount" id="wphrm_amount" placeholder="Amount" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Type', 'wphrm'); ?>  </label>
                                <div class="col-md-8">
                                    <select class="form-control" name="wphrm-status" id="wphrm_status">
                                        <option value=""><?php _e('Select Type', 'wphrm'); ?> </option>
                                        <option value="Profit"><?php _e('Cash In', 'wphrm'); ?></option>
                                        <option value="Loss"><?php _e('Cash Out', 'wphrm'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Date', 'wphrm'); ?></label>
                                <div class="col-md-8">
                                    <input class="form-control form-control-inline input-medium before-current-date" data-date-format="dd-mm-yyyy"  type="text" name="wphrm-financials-date" id="wphrm_financials_date" placeholder=" Date" />
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-offset-3 col-md-9">                                    
                                    <button type="submit"  class="demo-loading-btn btn blue"><i class="fa fa-plus"></i><?php _e('Add', 'wphrm'); ?></button>
                                    <button type="button" data-dismiss="modal" aria-hidden="true" class="btn red"><i class="fa fa-times"></i><?php _e('Cancel', 'wphrm'); ?></button>
                                </div>
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
                <h4 class="modal-title"><strong><i class="fa fa-edit"></i><?php _e(' Edit Transaction', 'wphrm'); ?></strong></h4>
            </div>
            <div class="modal-body">
                <div class="portlet-body form">
                    <div class="alert alert-success display-hide" id="wphrm_edit_financials_success"><i class='fa fa-check-square' aria-hidden='true'></i> <?php echo esc_html($wphrmFinancialUpdate); ?>
                        <button class="close" data-close="alert"></button>
                    </div>
                    <div class="alert alert-danger display-hide" id="wphrm_edit_financials_error">
                        <button class="close" data-close="alert"></button>
                    </div>
                    <!-- BEGIN FORM-->
                    <form method="POST"  accept-charset="UTF-8" class="form-horizontal" id="wphrm_edit_financials_frm">
                        <input type="hidden" name="finacials_id" id="finacials_id" />
                        <div class="form-body">
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Item', 'wphrm'); ?> </label>
                                <div class="col-md-8">
                                    <input class="form-control"   type="text" name="wphrm-item" id="wphrm_eitem" placeholder="Item" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Amount', 'wphrm'); ?> </label>
                                <div class="col-md-8">
                                    <input class="form-control"   type="text" name="wphrm-amount" id="wphrm_eamount" placeholder="Amount" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Type', 'wphrm'); ?>  </label>
                                <div class="col-md-8">
                                    <select class="form-control" name="wphrm-status" id="wphrm_estatus">
                                        <option value=""><?php _e('Select Type ', 'wphrm'); ?></option>
                                        <option value="Profit"><?php _e('Cash In', 'wphrm'); ?></option>
                                        <option value="Loss"><?php _e('Cash Out', 'wphrm'); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php _e('Date', 'wphrm'); ?>  </label>
                                <div class="col-md-8">
                                    <input class="form-control form-control-inline input-medium before-current-date" data-date-format="dd-mm-yyyy"  type="text" name="wphrm-financials-date" id="wphrm_efinancials_date" placeholder=" Date" />
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-offset-3 col-md-9">
                                    <button type="submit" data-loading-text="Updating..." class="demo-loading-btn btn blue"><i class="fa fa-edit"></i><?php _e('Update', 'wphrm'); ?> </button>
                                    <button type="button" data-dismiss="modal" aria-hidden="true" class="btn red"><i class="fa fa-times"></i><?php _e('Cancel', 'wphrm'); ?></button>
                                </div>
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
<div style="padding-left: 0px; padding-right:20px; padding-top:0px;" class="col-md-12">
<h3 class="page-title">
    <?php _e('Finance Management', 'wphrm'); ?>
</h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <i class="fa fa-home"></i>
           <?php _e('Home', 'wphrm'); ?>
            <i class="fa fa-angle-right"></i>
        </li>
        <li>
            <?php _e('Finance Management', 'wphrm'); ?>
        </li>
    </ul>
</div>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
    <div class="row">
        <div class="col-md-12">
            <a class="btn green " href="#add_static" data-toggle="modal"><i class="fa fa-plus"></i><?php _e('Add Transaction', 'wphrm'); ?></a>
            <div class="portlet box blue calendar">
                <div class="portlet-title">
                    <div class="caption"> <i class="fa fa-list"></i><?php _e('List of Transactions', 'wphrm'); ?>  </div>
                    
                  <div class="actions">
                        <a href="javascript:;"  onclick="wphrmProfitLossReport('wphrm-pdf-reports','finacial-report')" data-loading-text="Updating..."  class="demo-loading-btn btn btn-sm red">
                            <i class="fa fa-download" ></i><?php _e('PDF', 'wphrm'); ?>
                        </a>
                        <a href="javascript:;"  onclick="wphrmProfitLossReport('wphrm-excel-reports','finacial-report')" data-loading-text="Updating..."  class="demo-loading-btn btn btn-sm yellow">
                            <i class="fa fa-download" ></i><?php _e('Excel', 'wphrm'); ?>
                        </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <form name="wphrm-profit-loss" id="wphrm-profit-loss" action="?page=wphrm-profit-loss-reports" method="post">
                        <div class="col-md-12 col-sm-12 finacial-report-msg"><?php _e('Please Select Date Range.', 'wphrm'); ?> </div>
                        <div class="col-md-12 col-sm-12" style="text-align: center;">                          
                            <label>
                                <?php _e('From Date', 'wphrm'); ?> : <input  placeholder="<?php _e('From Date', 'wphrm'); ?> " data-date-format="dd-mm-yyyy" id="from-date" name="from-date" class="date-picker form-control input-small input-inline">
                            </label>
                            <label>&nbsp;&nbsp;
                                <?php _e('To Date', 'wphrm'); ?> : <input placeholder="<?php _e('To Date', 'wphrm'); ?>" data-date-format="dd-mm-yyyy" name="to-date" id="to-date" class="date-picker form-control input-small input-inline">
                            </label>                                
                            <label>&nbsp;&nbsp; <?php _e('Type', 'wphrm'); ?> :
                                <select class="form-control input-small input-inline" name="mainsearch" id="mainsearch">
                                    <option value=""><?php _e('Both', 'wphrm'); ?></option>
                                    <option value="cash-in"><?php _e('Cash In', 'wphrm'); ?></option>
                                    <option value="cash-out"><?php _e('Cash Out', 'wphrm'); ?></option>                                    
                                </select>
                            </label>
                            <input type="hidden" name="wphrm-report-type" value="" id="wphrm-report-type" class="wphrm-report-type" />
                            <input type="hidden" name="wphrm-report-action" value="" id="wphrm-report-action" class="wphrm-report-action" />
                        </div>
                    </form>
                    <table class="table table-striped table-bordered table-hover" id="wphrmDataTable">
                        <thead>
                            <tr> <th><?php _e('S.No', 'wphrm'); ?></th>
                                <th><?php _e('Item', 'wphrm'); ?></th>
                                <th><?php _e('Amount', 'wphrm'); ?></th>
                                <th><?php _e('Date', 'wphrm'); ?></th>
                                <th><?php _e('Financial Type', 'wphrm'); ?></th>
                                <th><?php _e('Actions', 'wphrm'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i=1;
                            $wphrm_finacials = $wpdb->get_results("SELECT * FROM  $this->WphrmFinancialsTable ORDER BY id DESC");
                            if(!empty($wphrm_finacials)) :
                                foreach ($wphrm_finacials as $key => $wphrm_finacial) { ?>
                                    <tr> 
                                         <td><?php echo esc_html($i); ?></td>
                                        <td>
                                            <?php if (isset($wphrm_finacial->wphrmItem)) : echo esc_html($wphrm_finacial->wphrmItem); endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($wphrm_finacial->wphrmAmounts)) : echo esc_html($wphrm_finacial->wphrmAmounts); endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($wphrm_finacial->wphrmDate)) : echo esc_html(date('d-m-Y', strtotime($wphrm_finacial->wphrmDate))); endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($wphrm_finacial->wphrmStatus) && $wphrm_finacial->wphrmStatus == 'Profit') { ?>
                                                <span class="label label-success"><?php _e('Cash In', 'wphrm'); ?></span>
                                            <?php } else { ?>
                                                <span class="label label-danger"><?php _e('Cash Out', 'wphrm'); ?></span>
                                            <?php } ?>
                                        </td>
                                        <td>  <a class="btn purple" data-toggle="modal" href="#edit_static" onclick="finacialsEdit(<?php
                                            if (isset($wphrm_finacial->id)) : echo esc_js($wphrm_finacial->id);
                                            endif; ?>
                                                , '<?php
                                            if (isset($wphrm_finacial->wphrmItem)) : echo esc_js($wphrm_finacial->wphrmItem);
                                            endif;
                                            ?>', '<?php
                                            if (isset($wphrm_finacial->wphrmAmounts)) : echo esc_js($wphrm_finacial->wphrmAmounts);
                                            endif;
                                            ?>', '<?php
                                            if (isset($wphrm_finacial->wphrmDate)) : echo esc_js(date('d-m-Y', strtotime($wphrm_finacial->wphrmDate)));
                                            endif;
                                            ?>', '<?php
                                            if (isset($wphrm_finacial->wphrmStatus)) : echo esc_js($wphrm_finacial->wphrmStatus);
                                            endif;
                                            ?>')">
                                                <i class="fa fa-edit"></i><?php _e('View/Edit', 'wphrm'); ?> 
                                            </a>
                                            <a class="btn red" href="javascript:;" onclick="WPHRMCustomDelete(<?php if (isset($wphrm_finacial->id)) : echo esc_js($wphrm_finacial->id); endif; ?>, '<?php echo esc_js($this->WphrmFinancialsTable) ?>', 'id')"><i class="fa fa-trash"></i><?php _e('Delete', 'wphrm'); ?> </a> </td>
                                    </tr>
                                    <?php
                               $i++; }
                            else : ?>
                                <tr>
                                    <td colspan="6"><?php _e('No financial data found in the database.', 'wphrm'); ?>
                                    </td><td class="collapse"></td><td class="collapse"></td><td class="collapse"></td><td class="collapse"></td><td class="collapse"></td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="financialModal" class="modal fade" tabindex="-1" data-backdrop="static" data-keyboard="false">
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
                <h4 class="modal-title"><?php _e('Financials', 'wphrm'); ?></h4>
            </div>
            <div class="modal-body" id="info"><?php _e('Please select date range.', 'wphrm'); ?></div>
            <div class="modal-footer">
               
                <button type="button" data-dismiss="modal" aria-hidden="true" class="btn default"><i class="fa fa-times"></i><?php _e('Cancel', 'wphrm'); ?></button>
            </div>
        </div>
    </div>
</div>
