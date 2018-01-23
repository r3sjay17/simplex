jQuery(function($){
    $('#wphrm_employee_userid').keyup(function(){
        $('#wphrm_employee_uniqueid').val( $(this).val() );
    });
});


jQuery(function($){

    $('.date-picker').on('changeDate', function() {
        $('.date-picker, .default-date').datepicker({
            format: 'dd-mm-yyyy',
            startDate: '01-01-1901',
            autoclose: true
        })
        var bdate = $(this).find('input').val().split('-');
        var currentTime = new Date();
        var year = currentTime.getFullYear();
        var age = year - bdate[2];
        $(this).closest('tr').find('.child-age').val( age );
    });
    
    function child_table_date_picker_reload(){
        $('.date-picker, .default-date').datepicker({
            format: 'dd-mm-yyyy',
            startDate: '01-01-1901',
            autoclose: true
        }).datepicker('remove').datepicker('update');
        /*$('.child-bod').datepicker({
        format: " g:i a",
    });*/

        $('.date-picker').on('changeDate', function() {
            $('.date-picker, .default-date').datepicker({
                format: 'dd-mm-yyyy',
                startDate: '01-01-1901',
                autoclose: true
            })
            var bdate = $(this).find('input').val().split('-');
            var currentTime = new Date();
            var year = currentTime.getFullYear();
            var age = year - bdate[2];
            $(this).closest('tr').find('.child-age').val( age );
        });
    }

    var wphrmEmployeeFamilyInfo_template = $('.wphrmEmployeeFamilyInfo-table').find('.blank-template').detach().removeClass('hide blank-template');
    $('.wphrmEmployeeFamilyInfo-table').on('click', '.add-new-row', function(){
        //remove class first
        $('.wphrmEmployeeFamilyInfo-table .table-action-button').removeClass('add-new-row').addClass('remove-item-row');
        $('.wphrmEmployeeFamilyInfo-table .table-action-button .fa').removeClass('fa-plus').addClass('fa-minus');
        $('.wphrmEmployeeFamilyInfo-table tbody').append(wphrmEmployeeFamilyInfo_template.clone());
    });
    $('.wphrmEmployeeFamilyInfo-table').on('click', '.remove-item-row', function(){
        //remove class first
        $(this).closest('tr').fadeOut().remove();
    });
    if($('.wphrmEmployeeFamilyInfo-table tbody tr').length <= 0){
        $('.wphrmEmployeeFamilyInfo-table tbody').append(wphrmEmployeeFamilyInfo_template.clone());
    }

    var wphrmEmployeeFamilyInfo_children_template = $('.wphrmEmployeeFamilyInfo-table-children').find('.blank-template').removeClass('hide blank-template').detach();
    $('.wphrmEmployeeFamilyInfo-table-children').on('click', '.add-new-row', function(){
        //remove class first
        $('.wphrmEmployeeFamilyInfo-table-children .table-action-button').removeClass('add-new-row').addClass('remove-item-row');
        $('.wphrmEmployeeFamilyInfo-table-children .table-action-button .fa').removeClass('fa-plus').addClass('fa-minus');
        $('.wphrmEmployeeFamilyInfo-table-children tbody').append(wphrmEmployeeFamilyInfo_children_template.clone());
        child_table_date_picker_reload();
    });
    $('.wphrmEmployeeFamilyInfo-table-children').on('click', '.remove-item-row', function(){
        //remove class first
        $(this).closest('tr').fadeOut().remove();
    });
    if($('.wphrmEmployeeFamilyInfo-table-children tbody tr').length <= 0){
        $('.wphrmEmployeeFamilyInfo-table-children tbody').append(wphrmEmployeeFamilyInfo_children_template.clone());
        child_table_date_picker_reload();
    }

    // this is for employee family details store in database ajax
    var wphrmEmployeeFamilyInfo_form = jQuery("#wphrmEmployeeFamilyInfo_form");
    wphrmEmployeeFamilyInfo_form.validate({
        /*rules: {
            wphrm_employee_join_salary:
                    {
                        number: true,
                        required: true
                    },
            wphrm_employee_current_salary:
                    {
                        number: true,
                        required: true
                    },
            wphrm_employee_basic_salary:
                    {
                        number: true,
                        required: true
                    },
            wphrm_employee_pf:
                    {
                        required: true
                    },
            'current-salary':
                    {
                        required: true
                    },
        },*/
        submitHandler: function (wphrmEmployeeFamilyInfo_form) {
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var formData = new FormData(jQuery(wphrmEmployeeFamilyInfo_form)['0']);
            formData.append('action', 'WPHRMEmployeeFamilyInfo');
            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                contentType: false,
                cache: false,
                processData: false,
                data: formData,
                success: function (output) {
                    var data = JSON.parse(output);
                    if (data.success) {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#family_details_success').removeClass('display-hide');
                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#family_details_success_error').html(data.errors);
                        jQuery('#family_details_success_error').removeClass('display-hide');
                        jQuery("#family_details_success_error").css('color', 'red');
                    }
                }
            });
        }
    });
});

jQuery(function($){
    var wphrm_employee_work_permit_form = $('#wphrm_employee_work_permit_form');
    wphrm_employee_work_permit_form.validate({
        rules: {},
        submitHandler: function(form){
            $(form).ajaxSubmit({
                type: 'POST',
                url: ajaxurl,
                dataType: 'json',
                data: { action: 'wphrm_employee_work_permit_form' },
                beforeSubmit: function(){

                },
                success: function(response, status){
                    if(response.status){
                        $('#work_permit_details_success').removeClass('display-hide');
                    }else{
                        //alert('no changes made');
                    }
                },
            });
        }
    });
});


jQuery(function($){
    if($().iCheck){
        $('input.icheck').iCheck({
            checkboxClass: 'icheckbox_flat-red',
            radioClass: 'iradio_flat-red'
        });
    }else{
        console.log('Icheck not loaded');
    }
});

jQuery(function($){
    if( $().SumoSelect ) {
        $('.make-sumoselect').SumoSelect();
        
        $('#wphrm_employee_reporting_manager').SumoSelect({
            triggerChangeCombined: true
        });
        var last_valid_selection = null;
        $('#wphrm_employee_reporting_manager').change(function(event) {
            if( $(this).val().length > 3 ) {
                //console.log('greater than 3');
                var $this = $(this);
                //$this[0].sumo.unSelectAll();
                $.each(last_valid_selection, function (i, e) {
                    //$this[0].sumo.selectItem( $this.find( 'option[value="' + e + '"]' ).index() );
                });
                $('#wphrm_employee_work_info_form .max-alert').slideDown(800);
            } else {
                last_valid_selection = $(this).val();
                $('#wphrm_employee_work_info_form .max-alert').slideUp(800);
            }
        });
    }
});

jQuery(function($){
    $('#wphrm_add_leavetype_form, #wphrm_edit_leavetype_form').on('change', '#leave_rules, #edit_leave_rules', function(){
        var selected_rule = $(this).val();
        $( '#wphrm_add_leavetype_form .leave_rule_descriptions li, #wphrm_edit_leavetype_form .leave_rule_descriptions li' ).addClass('hide');
        $( '#wphrm_add_leavetype_form .leave_rule_descriptions li.leave-' + selected_rule + ', #wphrm_edit_leavetype_form .leave_rule_descriptions li.leave-' + selected_rule ).removeClass('hide');
    });
});
jQuery(function($){
    jQuery(".wphrm-make-switch").bootstrapSwitch();
});

jQuery(function($){
    $('.calculate-unused-annual-leave').click(function(e){
        e.preventDefault();
        $.post(ajaxurl, {action: 'calculate_unused_annual_leave', year: $(this).attr('data-year'), user_id: $(this).attr('data-user_id')}, function(res){
            alert(res);
        });
    });
});

jQuery(function($){
    $('#wphrm_user_leave_applications_frm').on('change', '#wphrm_leavetyped', function(){
        var _form = $(this).closest('form');
        $.post(ajaxurl, {action: 'WPHRMGetLeaveTypeInfo', leave_id: $(this).val() }, function(data){
            
            if(data.maternity == 'Yes') {
                $('.maternity-holder').removeClass('hide');
            } else {
                $('.maternity-holder').addClass('hide');
            }
            if(data.paternity == 'Yes') {
                $('.paternity-holder').removeClass('hide');
            } else {
                $('.paternity-holder').addClass('hide');
            }
            if(data.lieu == 'Yes') {
                $('.lieu-holder').removeClass('hide');
            } else {
                $('.lieu-holder').addClass('hide');
            }
            if(data.outstation == 'Yes') {
                $('.outstation-holder').removeClass('hide');
            } else {
                $('.outstation-holder').addClass('hide');
            }
            if(data.examination == 'Yes') {
                $('.examination-holder').removeClass('hide');
            } else {
                $('.examination-holder').addClass('hide');
            }
            
            if(data.leave){
                //set the medical claim
                if(data.status && data.leave.leave_rules == 'medical_leave' && data.employee_claims.mc_eligible){
                    var max_mc = data.employee_claims.mc_total - data.employee_claims.mc_used;
                    _form.find('.leave-medical-claim')
                        .removeClass('hide required')
                        .find('[name="medical_claim"]')
                        .prop('required', true)
                        .attr('max', max_mc );
                    _form.find('.leave-medical-claim .description .amount').html('Max: ' + max_mc );
                }else{
                    _form.find('.leave-medical-claim').addClass('hide required').find('[name="medical_claim"]').prop('required', false);
                }
                //set the elderly screening
                if(data.status && data.leave.leave_rules == 'medical_leave' && data.employee_claims.es_eligible){
                    var max_es = data.employee_claims.es_total - data.employee_claims.es_used;
                    _form.find('.leave-elderly-screening')
                        .removeClass('hide required')
                        .find('[name="medical_claim"]')
                        .prop('required', true)
                        .attr('max', max_es );
                    _form.find('.leave-elderly-screening .description .amount').html('Max: ' + max_es );
                }else{
                    _form.find('.leave-elderly-screening').addClass('hide required').find('[name="elderly_screening"]').prop('required', false);
                }
                
                if(data.status && data.leave.require_file_attachment == '1'){
                    _form.find('#attendance-document').prop('required', true);
                    _form.find('.fileinput-new').show();
                    _form.find('.fileinput-new').parent().parent().show();
                }else{
                    _form.find('#attendance-document').prop('required', false);
                    _form.find('.fileinput-new').hide();
                    _form.find('.fileinput-new').parent().parent().hide();
                }

                if(data.leave.leave_description != undefined && data.leave.leave_description != ''){
                    _form.find('.leave-description').removeClass('hide').html(data.leave.leave_description);
                }else{
                    _form.find('.leave-description').addClass('hide').html('');
                }

                wphgrmCheckHolidayBetweenDate();
                //_form.find('.leave-date-picker').trigger('changeDate');
            }

        }, 'json');

        $.post(ajaxurl, {action: 'wphrm_disable_leave_dates', leave_id: $(this).val() }, function(data){
            if(data.status){
                console.log('Date Disabled: ', data.disable_dates);
                setTimeout(function(){
                    jQuery('.leave-date-picker').datepicker('setDatesDisabled', data.disable_dates );
                    /*jQuery('.leave-date-picker').datepicker({
                        beforeShowDay: function(date) {
                            var day = date.getDay();
                            var string = jQuery.datepicker.formatDate('mm/dd/yy', date);
                            var isDisabled = ($.inArray( string, data.disable_dates ) != -1);
                            return [day != 0 && !isDisabled];
                        }
                    });*/
                }, 1000);
            }
        }, 'json');
    });
    //$('#wphrm_leavetyped').trigger('change');
    
    jQuery('.leave-date-picker').datepicker({
        //format: "dd/mm/yyyy",
        autoclose: true
    });
    jQuery('.leave-date-picker').on('hide', function(){
        wphgrmCheckHolidayBetweenDate();
    });
});

jQuery(function($){
    $('form#remove-weekend-month').validate({
        rules:{},
        submitHandler: function(form){
            $(form).ajaxSubmit({
                type: 'POST',
                url: ajaxurl,
                dataType: 'json',
                data: { action: 'wphrm_remove_weekends' },
                beforeSubmit: function(){

                },
                success: function(response, status){

                },
            });
        }
    });
});


jQuery(function($){
    $('.select-all-checkbox').click(function(e){
        e.preventDefault();
        if($(this).attr('data-checked') != 'yes'){
            if($().iCheck){
                $(this).attr('data-checked', 'yes').closest('.form-group').find('input[type="checkbox"]').iCheck('check');
            }else{
                $(this).attr('data-checked', 'yes').closest('.form-group').find('input[type="checkbox"]').prop('checked', true);
            }
        }else{
            if($().iCheck){
                $(this).attr('data-checked', 'no').closest('.form-group').find('input[type="checkbox"]').iCheck('uncheck');
            }else{
                $(this).attr('data-checked', 'no').closest('.form-group').find('input[type="checkbox"]').prop('checked', false);
            }
        }
    });
});

jQuery(function($){
    $('#wphrm_email_notification_settings_form').on('change','#email-notification-type', function(){
        var _select = $(this);
        $('#wphrm_email_notification_settings_form [name="email_notification_subject"]').val('');
        $('#wphrm_email_notification_settings_form [name="email_notification_content"]').text('');
        //update_this_textarea();
        $.post(ajaxurl, {action: 'wphrm_get_email_notification_details', email_notification_key: _select.val()}, function(data){
            console.log(data);
            if(data.status){
                $('#wphrm_email_notification_settings_form #email_notification_variables').html(data.notification_variable_html);
                $('#wphrm_email_notification_settings_form [name="email_notification_subject"]').val(data.notification.subject);
                $('#wphrm_email_notification_settings_form [name="email_notification_content"]').text(data.notification.content);
                $('#wphrm_email_notification_settings_form [name="email_notification_content"]').val(data.notification.content);
                //update_this_textarea();
            }else{
                alert('problem getting email details.');
            }
        }, 'json');
    });

    function update_this_textarea(){
        // Check if TinyMCE is active
        if (typeof tinyMCE != "undefined") {
            if (tinyMCE.activeEditor != null){
                var editorContent = tinyMCE.activeEditor.getContent();
                $('#email_notification_content').val(editorContent).change();

                if ((editorContent === '' || editorContent === null)) {
                    $('#email_notification_content').val(editorContent).change();
                }
            }
        }

    }

    $('#wphrm_email_notification_settings_form').validate({
        rules: {},
        submitHandler: function(form){
            $(form).ajaxSubmit({
                type: 'POST',
                url: ajaxurl,
                dataType: 'json',
                data: {action: 'wphrm_update_email_notication'},
                success: function(data){
                    if(data.status){
                        alert('Update successfully');
                        //window.location.reload();
                    }else{
                        alert('Something went wrong');
                    }
                }
            });
        },
    });



    /* Added Scripts */
    $('#wphrm_employee_work_info_form').on('click', '.btn-promote-employee', function(e) {
        e.preventDefault();
        var emp_level = $(this).data('emp_level');
        $('#wphrm_emp_promotion_frm  .wphrm_employee_initial_level').val( emp_level );
        $('#add_promotion_modal').modal('show');
    });
    $('.wphrmEmployeePromotion-table').on('click', '.btn-remove-promotion', function(e) {
        e.preventDefault();
        //$(this).find('i.fa').removeClass('fa-minus').addClass('fa-refresh fa-spin');
        var key = $(this).attr('data-promotion-key');
        var masterkey = $(this).attr('data-master-key');
        var empID = $('#wphrm_emp_promotion_frm #wphrm_emp_promotion_id').val();
        $.get(
            ajaxurl, {
                action: 'WPHRMEmployeePromotion',
                promotion_key: key,
                master_key: masterkey,
                wphrm_emp_promotion_id: empID
            }, function( output ) {
                //$('.wphrmEmployeePromotion-table .btn-remove-promotion i.fa').addClass('fa-minus').removeClass('fa-refresh fa-spin');
                var data = JSON.parse( output );
                if( data.success ) {
                    location.reload();
                } else {
                    location.reload();
                }
            }
        );
    });

    var wphrmEmployeePromotionForm = $('#wphrm_emp_promotion_frm');
    wphrmEmployeePromotionForm.on('submit', function() {
        var formData = new FormData( $(wphrmEmployeePromotionForm)['0'] );
        formData.append( 'action', 'WPHRMEmployeePromotion' );
        $.ajax({
            method: "POST",
            url: ajaxurl,
            contentType: false,
            cache: false,
            processData: false,
            data: formData, 
            success: function( output ) {
                var data = JSON.parse( output );
                if( data.success ) {
                    $('#wphrm_emp_promotion_success').removeClass('display-hide');
                } else {
                    $('#wphrm_emp_promotion_success').removeClass('display-hide');
                }
                setTimeout(function() {
                    location.reload();
                }, 1500);
            }
        });
        return false;
    });
 
    //hiding ord info
    $('#wphrm_employee_basic_info_form').on('click', '.wphrm_employee_ord', function() {
        var t = $(this).val();
        if( t == 'Yes' ) {
            $('.ord-holder').removeClass('hide');
        } else {
            $('.ord-holder').addClass('hide');
        }
    });   

    //hiding rod info
    $('#wphrm_employee_basic_info_form').on('click', '.wphrm_employee_rod', function() {
        var t = $(this).val();
        if( t == 'Yes' ) {
            $('.rod-holder').removeClass('hide');
        } else {
            $('.rod-holder').addClass('hide');
        }
    });   

});