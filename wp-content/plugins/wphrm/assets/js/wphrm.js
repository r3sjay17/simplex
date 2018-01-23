/*
 * WP-HRM
 * Copyright 2014-2016 IndigoThemes
 */
jQuery(window).load(function () {
    jQuery('.preloader-custom-gif').hide();
    jQuery('.preloader ').hide();
});
jQuery(document).ready(function () {

    window.setInterval(function(){
        jQuery('.alert-success').addClass('display-hide');
    }, 8000);


    calculateEarningSum();
    calculateDeductionSum();
    calculateNetTotal();

    jQuery('.holidayDivHide').hide();
    jQuery('.bank-account-hide-div').show();
    jQuery('.other-details-div').show();
    jQuery('.documents-hide-div').show();
    jQuery('.salary-details-div').show();



    jQuery('.edit-role').hide();
    jQuery('.add-role').hide();
    jQuery('.role-permission-show').hide();


    // role wise permission
    jQuery("#rolenameget").change(function () {
        var rolenameget = jQuery("#rolenameget").val();
        wphrm_data = {
            'action': 'wphrmRoleWiseCapabilityGet',
            'rolenameget': rolenameget,
        }
        jQuery('.preloader-custom-gif').show();
        jQuery('.preloader ').show();
        jQuery.post(ajaxurl, wphrm_data, function (response) {
            var data = JSON.parse(response);
            if (data.success) {
                jQuery(".role-permission-show").show();
                jQuery('.preloader-custom-gif').hide();
                jQuery('.preloader ').hide();
                jQuery('#role-permission-show').html(data.success);
            } else {
                jQuery('.preloader-custom-gif').hide();
                jQuery('.preloader ').hide();
            }
        });
    });


    var decimalpointvalue = jQuery('#decimalpointvalue').val();
    var documentsHideId = jQuery('.documents-hide-id').val();
    var bankAccountHideId = jQuery('.bank-account-hide-id').val();
    var otherDetailsId = jQuery('.other-details-id').val();
    var salaryDetailsId = jQuery('.salary-details-id').val();

    if (documentsHideId == '0') {
        jQuery('.documents-hide-div').hide();
    }
    if (bankAccountHideId == '0') {
        jQuery('.bank-account-hide-div').hide();

    }
    if (otherDetailsId == '0') {
        jQuery('.other-details-div').hide();
    }
    if (salaryDetailsId == '0') {
        jQuery('.salary-details-div').hide();
    }

    var TableManaged = function () {
        var initTable2 = function () {
            // DataTable
            var table = jQuery('#wphrmDataTable, .wphrmDataTableClass');
            table.DataTable({
                dom: '<"row" <"col-md-6 col-sm-12" l <"btn-group"B> > <"col-md-6 col-sm-12" f>  > t<"row" <"col-md-7 col-sm-12" i> <"col-md-5 col-sm-12" p> >',
                // Internationalisation.
                "language": {
                    "aria": {
                        "sortAscending": ": activate to sort column ascending",
                        "sortDescending": ": activate to sort column descending"
                    },
                    "emptyTable": "No data available",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "infoEmpty": "No entries found",
                    "infoFiltered": "(filtered1 from _MAX_ total entries)",
                    "lengthMenu": "Show _MENU_ entries",
                    "search": "Search:",
                    "zeroRecords": "No matching records found"
                },
                "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
                "lengthMenu": [
                    [5, 15, 20, -1],
                    [5, 15, 20, "All"] // change per page values here
                ],
                "pageLength": 20,
                "language": {
                    "lengthMenu": WPHRMCustomJS.records + " _MENU_ ",
                    "oPaginate": {
                        "sPrevious": "Prev",
                        "sNext": "Next"
                    }
                },
                "columnDefs": [{// set default column settings
                    'orderable': true,
                    'targets': [0]
                }, {
                    "searchable": true,
                    "targets": [0]
                }],
                buttons: [
                    {
                        extend: 'copyHtml5',
                        className: 'btn btn-default',
                        exportOptions: {
                            columns: ':not(:last-child)',
                            //columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ]
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        className: 'btn btn-default',
                        exportOptions: {
                            columns: ':not(:last-child)',
                            //columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ]
                        },
                    },
                    {
                        extend: 'csvHtml5',
                        className: 'btn btn-default',
                        exportOptions: {
                            columns: ':not(:last-child)',
                            //columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ]
                        },
                    },
                    {
                        extend: 'pdfHtml5',
                        className: 'btn btn-default',
                        exportOptions: {
                            columns: ':not(:last-child)',
                            //columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ]
                        },
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-default',
                        exportOptions: {
                            columns: ':not(:last-child)',
                            //columns: "visible"
                        },
                    },
                    {
                        extend: 'colvis',
                        className: 'btn btn-default',
                    },
                ],
            });
        }
        return {
            //main function to initiate the module
            init: function () {
                if (!jQuery().dataTable) {
                    return;
                }
                initTable2();
            }
        };
    }();
    var date = new Date();
    var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());
    var yesterday = new Date(date.getFullYear(), date.getMonth(), date.getDate() - 1);
    jQuery('.before-current-date').datepicker({
        format: "dd-mm-yyyy",
        startDate: '01-01-1901',
        setDate: today,
        endDate: today,
        autoclose: true,
        todayBtn: "linked",
        todayHighlight: true,
    });

    jQuery(".month-year").datepicker({
        format: "MM yyyy",
        viewMode: "months",
        minViewMode: "months",
        autoclose: true
    })

    jQuery('.after-current-date').datepicker({
        format: "dd-mm-yyyy",
        startDate: '1d',
        autoclose: true,
        Default: false,
        todayHighlight: true,
    });

    jQuery('#from-date').datepicker({
        format: "dd-mm-yyyy",
        startDate: '01-01-1901',
        setDate: yesterday,
        endDate: yesterday,
        todayHighlight: true,
        autoclose: true
    });
    jQuery('#to-date').datepicker({
        format: "dd-mm-yyyy",
        startDate: '01-01-1901',
        setDate: today,
        endDate: today,
        todayHighlight: true,
        autoclose: true
    });


    jQuery('.picker-date').datepicker({
        format: " g:i a",
        todayBtn: "linked",
        todayHighlight: true,
    });






    (function (jQuery) {
        jQuery.fn.strongPassword = function () {
            var password = [];
            var len = 32;
            var symbols = "";
            var digits = "";
            var similar = "";
            for (i = 0; i < len; i++) {
                var num = randomNumber();
                num = checkChar(num, symbols, digits);
                password.push(String.fromCharCode(num));
            }
            jQuery(this).val(password.join(''));
        }
        randomNumber = function () {
            return Math.floor(Math.random() * (127 - 33 + 1)) + 33; // 33 to 127
        }
        checkChar = function (num, symbols, digits, similar) {
            if (!symbols) {
                while (hasSymbols(num)) {
                    num = randomNumber();
                }
            }
            if (!digits) {
                while (hasDigits(num)) {
                    num = randomNumber();
                }
            }
            if (!similar) {
                while (hasSimilarChars(num)) {
                    num = randomNumber();
                }
            }
            return num;
        }
        hasDigits = function (num) {
            if (num >= 48 && num <= 57) {
                return true;
            }
            return false;
        }
        hasSymbols = function (num) {
            if ((num >= 33 && num <= 47) || (num >= 58 && num <= 64) || (num >= 91 && num <= 96) || (num >= 123 && num <= 126)) {
                return true;
            }
            return false;
        }
        hasSimilarChars = function (num) {
            if (num == 48 || num == 49 || num == 73 || num == 76 || num == 79 || num == 105 || num == 108 || num == 111) {
                return true;
            }
            return false;
        }
    })(jQuery);
    (function (jQuery) {
        jQuery.toggleShowPassword = function (options) {
            var settings = jQuery.extend({
                field: "#wphrm_employee_password",
                control: "#toggle_show_password",
            }, options);
            var control = jQuery(settings.control);
            var field = jQuery(settings.field)
            control.bind('click', function () {
                if (control.is(':checked')) {
                    field.attr('type', 'text');
                } else {
                    field.attr('type', 'password');
                }
            })
        };
    }(jQuery));
    jQuery.toggleShowPassword({
        field: '#wphrm_employee_password',
        control: '#methods'
    });
    jQuery("#generatePassword").on('click', function () {
        jQuery('#wphrm_employee_password').strongPassword();
    });
    /** Tabs **/
    jQuery('ul.tabs li').click(function () {
        var tab_id = jQuery(this).attr('data-tab');
        jQuery('ul.tabs li').removeClass('current');
        jQuery('.tab-content').removeClass('current');
        jQuery(this).addClass('current');
        jQuery("#" + tab_id).addClass('current');
    });
    // onready class used for attendance mark
    jQuery('.leaveType').hide();
    jQuery('.reason').hide();
    jQuery('.leaveOn').hide();
    //jQuery('.leaveOnHaflDay').hide();
    jQuery('.checkbox').hide();
    /** chart js **/
    jQuery("#bars li .bar").each(function (key, bar) {
        var percentage = jQuery(this).data('amount');
        var amounts = jQuery('.wphrm_level').val();
        var final = (percentage * 100) / amounts;
        if (final > 99) {
            var datacheck = 99;
        } else {
            var datacheck = final;
        }

        jQuery(this).animate({
            'height': datacheck + '%'
        }, 1000);
    });


    jQuery("#edit-currency-sign").hide();
    jQuery("#edit-currency-name").hide();
    jQuery("#edit-currency-desc").hide();
    jQuery("#edit-loadaction").hide();

    jQuery("#currencyloaddata").change(function () {
        var id = jQuery("#currencyloaddata").val();
        wphrm_data = {
            'action': 'WPHRMLoadCurrencyData',
            'id': id,
        }
        jQuery('.preloader-custom-gif').show();
        jQuery('.preloader ').show();
        jQuery.post(ajaxurl, wphrm_data, function (response) {

            var data = JSON.parse(response);
            if (data.success != '') {
                jQuery("#edit-currency-sign").val(data.success.currencySign);
                jQuery("#edit-currency-name").val(data.success.currencyName);
                jQuery("#edit-currency-desc").val(data.success.currencyDesc);
                jQuery("#edit-currency-sign").show();
                jQuery("#edit-currency-name").show();
                jQuery("#edit-currency-desc").show();
                jQuery("#edit-loadaction").show();
                jQuery('.preloader-custom-gif').hide();
                jQuery('.preloader ').hide();

            }
        });


    });



    /** Upload Profile images Validation **/
    jQuery("#employee_profile").change(function () {
        var fileExtension = ['jpeg', 'jpg', 'png', 'gif'];
        if (jQuery.inArray(jQuery(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            jQuery('#personal_details_error').html("<i class='fa fa-close' aria-hidden='true'></i> Only \'" + fileExtension.join('\', \'') + "\' filetypes are allowed.");
            jQuery('#personal_details_error').removeClass('display-hide');
            return false;
        } else {
            jQuery('#personal_details_error').addClass('display-hide');
        }
    });
    /** Upload Documents Validation **/
    jQuery(".documents-Upload").change(function () {
        var fileExtension = ['gif', 'GIF', 'png', 'PNG', 'jpg', 'DOC', 'doc', 'DOCX', 'docx', 'txt', 'TXT', 'JPEG', 'JPG'];
        if (jQuery.inArray(jQuery(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            jQuery('#Documents_error').html("<i class='fa fa-close' aria-hidden='true'></i> Only \'" + fileExtension.join('\', \'') + "\' filetypes are allowed.");
            jQuery('#Documents_error').removeClass('display-hide');
        } else {
            jQuery('#Documents_error').addClass('display-hide');
        }
    });
    jQuery("#bars li .bar_lose").each(function (key, bar_lose) {
        var percentage = jQuery(this).data('amount');
        var amounts = jQuery('.wphrm_level').val();
        var final = (percentage * 100) / amounts;
        if (final > 99) {
            var datacheck = 99;
        } else {
            var datacheck = final;
        }
        jQuery(this).animate({
            'height': datacheck + '%'
        }, 1000);
    })

    jQuery("#wphrmyearchoose").hide();
    // this is for employee  details store in database ajax
    var form_employee_form = jQuery("#employee_form");
    form_employee_form.validate({
        rules: {
            wphrm_employee_fname: "required",
            wphrm_employee_lname: "required",
            wphrm_employee_uniqueid: "required",
            wphrm_employee_email: {
                required: true,
                email: true
            },
            wphrm_employee_userid: "required",
            wphrm_employee_password: "required",
            wphrm_employee_department: "required",
            wphrm_employee_designation: "required",
            wphrm_employee_joining_date: "required",
        },
        submitHandler: function (form_employee_form) {
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var formData = new FormData(jQuery(form_employee_form)['0']);
            formData.append('action', 'WPHRMEmployeeDetails');
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
                        var employee_id = data.success;
                        if (employee_id == 0) {
                            jQuery('.preloader-custom-gif').hide();
                            jQuery('.preloader ').hide();
                            jQuery('#success').removeClass('display-hide');
                            jQuery("html, body").animate({scrollTop: 0}, "slow");
                        } else {
                            var employee_id = data.success;
                            jQuery('.preloader-custom-gif').hide();
                            jQuery('.preloader ').hide();
                            jQuery('#success').removeClass('display-hide');
                            jQuery('#success').removeClass('display-hide');
                            window.location.href = '?page=wphrm-employee-info&employee_id=' + employee_id;

                        }
                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#error').html("<i class='fa fa-close' aria-hidden='true'></i>" + data.errors);
                        jQuery('#error').removeClass('display-hide');
                        jQuery("#error").css('color', 'red');

                    }
                }
            });
        }
    });
    // this is for employee Documents details store in database ajax
    var wphrm_employee_basic_info_form = jQuery("#wphrm_employee_basic_info_form");
    wphrm_employee_basic_info_form.validate({
        rules: {
            wphrm_employee_fname: "required",
            wphrm_employee_lname: "required",
            wphrm_employee_uniqueid: "required",
            wphrm_employee_email: {
                required: true,
                email: true
            },
            wphrm_employee_userid: "required",
            wphrm_employee_password: "required",
            wphrm_employee_department: "required",
            wphrm_employee_designation: "required",
            wphrm_employee_joining_date: "required",
            wphrm_employee_role: "required",
        },
        submitHandler: function (wphrm_employee_basic_info_form) {
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var formData = new FormData(jQuery(wphrm_employee_basic_info_form)['0']);
            formData.append('action', 'WPHRMEmployeeBasicInfo');
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
                        var employee_id = data.success;
                        var role = data.currentrole;
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#personal_details_success').removeClass('display-hide');
                        jQuery("html, body").animate({scrollTop: 0}, "slow");
                        if (role) {
                            if (employee_id != true) {
                                window.setTimeout(function () {
                                    window.location.href = '?page=wphrm-employee-info&employee_id=' + employee_id;
                                }, 1500);
                            }
                        }
                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#personal_details_error').html(data.error);
                        jQuery('#personal_details_error').removeClass('display-hide');
                        jQuery("#personal_details_error").css('color', 'red');
                        jQuery("html, body").animate({scrollTop: 0}, "slow");
                    }
                }
            });
        }
    });
    // this is for employee Documents details store in database ajax
    var wphrmEmployeeDocumentInfo_form = jQuery("#wphrmEmployeeDocumentInfo_form");
    wphrmEmployeeDocumentInfo_form.validate({
        rules: {
        },
        submitHandler: function (wphrmEmployeeDocumentInfo_form) {
            var documentsfieldslebal = [];
            jQuery("input[name='documentsfieldslebal[]']").each(function () {
                documentsfieldslebal.push(jQuery(this).val());
            });
            var documentValues = [];
            jQuery("input[name='documentValues[]']").each(function () {
                documentValues.push(jQuery(this).val());
            });
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var formData = new FormData(jQuery(wphrmEmployeeDocumentInfo_form)['0']);
            formData.append('action', 'WPHRMEmployeeDocumentInfo');
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
                        jQuery('#Documents_success').removeClass('display-hide');
                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#Documents_error').html(data.error);
                        jQuery('#Documents_error').removeClass('display-hide');
                        jQuery("#Documents_error").css('color', 'red');
                    }
                }
            });
        }
    });

    // this is for employee basic details store in database ajax
    var wphrmEmployeeSalaryInfo_form = jQuery("#wphrmEmployeeSalaryInfo_form");
    wphrmEmployeeSalaryInfo_form.validate({
        rules: {
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
        },
        submitHandler: function (wphrmEmployeeSalaryInfo_form) {
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var wphrmsalaryfieldslebal = [];
            jQuery("input[name='salary-fields-lebal[]']").each(function () {
                wphrmsalaryfieldslebal.push(jQuery(this).val());
            });
            var wphrmsalaryfieldsvalue = [];
            jQuery("input[name='salary-fields-value[]']").each(function () {
                wphrmsalaryfieldsvalue.push(jQuery(this).val());
            });
            var formData = new FormData(jQuery(wphrmEmployeeSalaryInfo_form)['0']);
            formData.append('action', 'WPHRMEmployeeSalaryInfo');
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
                        jQuery('#salary_details_success').removeClass('display-hide');
                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#salary_details_success_error').html(data.errors);
                        jQuery('#salary_details_success_error').removeClass('display-hide');
                        jQuery("#salary_details_success_error").css('color', 'red');
                    }
                }
            });
        }
    });

    // this is for employee bank details store in database ajax
    var wphrmhideAutomaticAttendanceForm = jQuery("#wphrmhide-automatic-attendance-form");
    wphrmhideAutomaticAttendanceForm.validate({
        submitHandler: function (wphrmhideAutomaticAttendanceForm) {
            var bankChecked = jQuery(".automatic-attendance-checked").is(":checked");
            if (bankChecked == true) {
                var status = 'on';
            } else {
                var status = 'off';
            }
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var formData = new FormData(jQuery(wphrmhideAutomaticAttendanceForm)['0']);
            formData.append('automatic-attendance', status);
            formData.append('action', 'WPHRMAutomaticAttendanceInfo');
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
                        jQuery('#automatic_attendance_success').removeClass('display-hide');
                        jQuery('#automatic_attendance_success').html("<i class='fa fa-check-square' aria-hidden='true'></i> " + data.success);
                        jQuery("#automatic_attendance_success").css('color', '#3c763d');
                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#automatic_attendance_error').html(data.errors);
                        jQuery('#automatic_attendance_error').removeClass('display-hide');
                        jQuery("#automatic_attendance_error").css('color', 'red');
                    }
                }
            });
        }
    });


    // this is for employee bank details store in database ajax
    var wphrmEmployeeBankInfo_form = jQuery("#wphrmEmployeeBankInfo_form");
    wphrmEmployeeBankInfo_form.validate({
        rules: {
            wphrm_employee_bank_account_name: "required",
            wphrm_employee_bank_name: "required",
            wphrm_employee_bank_account_no: {
                required: true,
            },
            wphrm_Confirm_mployee_bank_account_no: {
                equalTo: "#wphrm_employee_bank_account_no"
            },
        },
        messages: {
            wphrm_Confirm_mployee_bank_account_no: {
                equalTo: "Account number don't match",
            },
        },
        submitHandler: function (wphrmEmployeeBankInfo_form) {
            var wphrmbankfieldslebal = [];
            jQuery("input[name='bank-fields-lebal[]']").each(function () {
                wphrmbankfieldslebal.push(jQuery(this).val());
            });
            var wphrmbankfieldsvalue = [];
            jQuery("input[name='bank-fields-value[]']").each(function () {
                wphrmbankfieldsvalue.push(jQuery(this).val());
            });
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var formData = new FormData(jQuery(wphrmEmployeeBankInfo_form)['0']);
            formData.append('action', 'WPHRMEmployeeBankInfo');
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
                        jQuery('#wphrm_bank_details').removeClass('display-hide');
                        jQuery("html, body").animate({scrollTop: 0}, "slow");
                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#wphrm_bank_details_error').html(data.errors);
                        jQuery('#wphrm_bank_details_error').removeClass('display-hide');
                        jQuery("#wphrm_bank_details_error").css('color', 'red');
                        jQuery("html, body").animate({scrollTop: 0}, "slow");
                    }
                }
            });
        }
    });

    // this is for employee other details store in database ajax
    var wphrmEmployeeOtherInfo_form = jQuery("#wphrmEmployeeOtherInfo_form");
    wphrmEmployeeOtherInfo_form.validate({
        rules: {
        },
        submitHandler: function (wphrmEmployeeOtherInfo_form) {
            var wphrmotherfieldslebal = [];
            jQuery("input[name='other-fields-lebal[]']").each(function () {
                wphrmotherfieldslebal.push(jQuery(this).val());
            });
            var wphrmotherfieldsvalue = [];
            jQuery("input[name='other-fields-value[]']").each(function () {
                wphrmotherfieldsvalue.push(jQuery(this).val());
            });

            var formData = new FormData(jQuery(wphrmEmployeeOtherInfo_form)['0']);
            formData.append('action', 'WPHRMEmployeeOtherInfo');
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
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
                        jQuery('#other_details_success').removeClass('display-hide');
                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#other_details_success_error').html(data.errors);
                        jQuery('#other_details_success_error').removeClass('display-hide');
                        jQuery("#other_details_success_error").css('color', 'red');
                    }
                }
            });
        }
    });
    var wphrmEmployeeOtherInfo_form_emp = jQuery(".wphrmEmployeeOtherInfo_form");
    wphrmEmployeeOtherInfo_form_emp.validate({
        rules: {
        },
        submitHandler: function (wphrmEmployeeOtherInfo_form_emp) {
            var wphrmotherfieldslebal = [];
            jQuery("input[name='other-fields-lebal[]']").each(function () {
                wphrmotherfieldslebal.push(jQuery(this).val());
            });
            var wphrmotherfieldsvalue = [];
            jQuery("input[name='other-fields-value[]']").each(function () {
                wphrmotherfieldsvalue.push(jQuery(this).val());
            });

            var formData = new FormData(jQuery(wphrmEmployeeOtherInfo_form_emp)['0']);
            formData.append('action', 'WPHRMEmployeeOtherInfo');
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
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
                        jQuery('#other_details_success').removeClass('display-hide');
                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#other_details_success_error').html(data.errors);
                        jQuery('#other_details_success_error').removeClass('display-hide');
                        jQuery("#other_details_success_error").css('color', 'red');
                    }
                }
            });
        }
    });
    var wphrmEmployeeFamilyInfo_form_emp = jQuery(".wphrmEmployeeFamilyInfo_form");
    wphrmEmployeeFamilyInfo_form_emp.validate({
        submitHandler: function (wphrmEmployeeFamilyInfo_form_emp) {
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var formData = new FormData(jQuery(wphrmEmployeeFamilyInfo_form_emp)['0']);
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


    // this is for employee other details store in database ajax
    var wphrmEmployeeOtherInfo_form = jQuery("#wphrmEmployeeOtherInfo_form");
    wphrmEmployeeOtherInfo_form.validate({
        rules: {
        },
        submitHandler: function (wphrmEmployeeOtherInfo_form) {
            var wphrmotherfieldslebal = [];
            jQuery("input[name='other-fields-lebal[]']").each(function () {
                wphrmotherfieldslebal.push(jQuery(this).val());
            });
            var wphrmotherfieldsvalue = [];
            jQuery("input[name='other-fields-value[]']").each(function () {
                wphrmotherfieldsvalue.push(jQuery(this).val());
            });

            var formData = new FormData(jQuery(wphrmEmployeeOtherInfo_form)['0']);
            formData.append('action', 'WPHRMEmployeeOtherInfo');
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
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
                        jQuery('#other_details_success').removeClass('display-hide');
                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#other_details_success_error').html(data.errors);
                        jQuery('#other_details_success_error').removeClass('display-hide');
                        jQuery("#other_details_success_error").css('color', 'red');
                    }
                }
            });
        }
    });

    // this is for Department store in database ajax
    var department_frm = jQuery(".department_frm");
    department_frm.validate({
        rules: {
            'departmentName[]': {
                required: true,
            },
        },
        submitHandler: function (department_frm) {
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var departmentName = [];
            jQuery("input[name='departmentName[]']").each(function () {
                departmentName.push(jQuery(this).val());
            });
            var formData = new FormData(jQuery(department_frm)['0']);
            formData.append('action', 'WPHRMDepartmentInfo');
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
                        jQuery('#wphrmDepartmentInfo_success').removeClass('display-hide');
                        window.setTimeout(function () {
                            window.location.reload();
                        }, 500);

                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#wphrmDepartmentInfo_error').html(data.errors);
                        jQuery('#wphrmDepartmentInfo_error').removeClass('display-hide');
                        jQuery("#wphrmDepartmentInfo_error").css('color', 'red');
                    }
                }
            });
        }
    });
    // this is for employee  details store in database ajax
    var wphrmEmployeeAttendanceMark_frm = jQuery("#wphrmEmployeeAttendanceMark_frm");
    wphrmEmployeeAttendanceMark_frm.validate({
        rules: {
        },
        submitHandler: function (wphrmEmployeeAttendanceMark_frm) {
            var formData = new FormData(jQuery(wphrmEmployeeAttendanceMark_frm)['0']);
            formData.append('action', 'WPHRMEmployeeAttendanceMark');
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
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
                        jQuery("html, body").animate({scrollTop: 0}, "slow");
                        jQuery('#employee_attendance_mark_success').removeClass('display-hide');
                    }
                }
            });
        }
    });


    /* this is for Designation store in database ajax */
    var designation_frm = jQuery(".designation_frm");
    designation_frm.validate({
        rules: {
            'designation_name[]': {
                required: true,
            },
        },
        submitHandler: function (designation_frm) {
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var designation_name = [];
            jQuery("input[name='designation_name[]']").each(function () {
                designation_name.push(jQuery(this).val());
            });
            var formData = new FormData(jQuery(designation_frm)['0']);
            formData.append('action', 'WPHRMDesignationInfo');
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
                        jQuery('#wphrmDesignationInfo_success').removeClass('display-hide');
                        jQuery("#designation_name").val("");
                        window.location.reload();
                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#wphrmDesignationInfo_error').html(data.errors);
                        jQuery('#wphrmDesignationInfo_error').removeClass('display-hide');
                        jQuery("#wphrmDesignationInfo_error").css('color', 'red');
                    }
                }
            });
        }
    });




    /** Add multiple Weekends  */
    var wphrmAddyearInWeekendfrm = jQuery("#add-year-in-weekendfrm");
    wphrmAddyearInWeekendfrm.validate({
        rules: {
            wphrmyear: "required",
            wphrmWeekend: "required",
        },
        submitHandler: function (wphrmAddyearInWeekendfrm) {

            var formData = new FormData(jQuery(wphrmAddyearInWeekendfrm)['0']);
            formData.append('action', 'WPHRMwphrmAddyearInWeekendInfo');
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
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
                        jQuery('#weekend_success').removeClass('display-hide');
                        jQuery('#weekend_success').html("<i class='fa fa-check-square' aria-hidden='true'></i> " + data.success);
                        jQuery("#wphrmyear").val("");
                        jQuery("#wphrmWeekend").val("");
                        window.setTimeout(function () {
                            window.location.reload();
                        }, 500);
                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#weekend_error').html(data.error);
                        jQuery('#weekend_error').removeClass('display-hide');
                        jQuery("#weekend_error").css('color', 'red');
                    }
                }
            });
        }
    });

    /* this is for hodiday add in database  */
    jQuery(function () {
        var wphrmAddHolidays_frm = jQuery("#wphrmAddHolidays_frm");
        wphrmAddHolidays_frm.validate({
            submitHandler: function (wphrmAddHolidays_frm) {
                jQuery('.preloader-custom-gif').show();
                jQuery('.preloader ').show();
                var formData = new FormData(jQuery(wphrmAddHolidays_frm)['0']);
                formData.append('action', 'WPHRMAddHolidays');
                jQuery.ajax({
                    method: "POST",
                    url: ajaxurl,
                    contentType: false,
                    cache: false,
                    processData: false,
                    data: formData,
                    success: function (output) {
                        var data = JSON.parse(output);
                        if (data.success == true) {
                            jQuery('.preloader-custom-gif').hide();
                            jQuery('.preloader ').hide();
                            jQuery('#holiday_success').removeClass('display-hide');
                            jQuery("#holiday_date").val("");
                            jQuery("#occasion").val("");
                            window.setTimeout(function () {
                                window.location.reload();
                            }, 500);

                        } else {
                            jQuery('.preloader-custom-gif').hide();
                            jQuery('.preloader ').hide();
                            jQuery('#holiday_error').html(data.error);
                            jQuery('#holiday_error').removeClass('display-hide');
                            jQuery("#holiday_error").css('color', 'red');
                        }
                    }
                });
            }
        });

    });

    /* Add leave type
    J@F modified 2017-05-08 */
    var wphrm_add_leavetype_form = jQuery("#wphrm_add_leavetype_form");
    wphrm_add_leavetype_form.validate({
        rules: {
            leaveType: "required",
            numberOfLeave: {
                min: 1,
                required: true
            },
            wphrm_period: "required",
        },
        submitHandler: function (wphrm_add_leavetype_form) {
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var formData = new FormData(jQuery(wphrm_add_leavetype_form)['0']);
            formData.append('action', 'WPHRMLeavetypeInfo');
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
                        jQuery('#wphrm_add_leavetype_success').removeClass('display-hide');
                        jQuery("#add_leaveType").val("");
                        jQuery("#wphrm_period").val("");
                        jQuery("#add_num_of_leave").val("");
                        jQuery("#add_leave_file_attachment, #add_leave_medical_claim").prop('checked', false);
                        window.setTimeout(function () {
                            window.location.reload();
                        }, 500);

                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#wphrm_add_leavetype_error').html(data.errors);
                        jQuery('#wphrm_add_leavetype_error').removeClass('display-hide');
                        jQuery("#wphrm_add_leavetype_error").css('color', 'red');
                    }
                }
            });
        }
    });

    /* Selected Department wise get designation */
    var id = jQuery('#wphrm_employee_department').val();
    if (id != '') {
        var designationid = jQuery('#wphrm_ajax_employee_designation').val();
        getDesignation(id, designationid);
    }

    /* add employee department  */
    jQuery('#wphrm_employee_department').change(function () {
        var deprtid = jQuery('#wphrm_employee_department').val();
        var desigID = jQuery('#wphrm_ajax_employee_designation').val();
        getDesignation(deprtid, desigID);
    });

    /* Year to Change Month ajax  */
    var yearload = jQuery('#wphrm-duplicate-to-salary-year').val();
    var employeeID = jQuery('form#wphrm-duplicate-salary input[name="wphrm-employee-id"]').val();
    if (yearload != '' && employeeID != '' && employeeID != 0) {
        GetSalaryMonthsForDuplicate(yearload, employeeID);
    }

    jQuery('#wphrm-duplicate-to-salary-year').change(function () {
        var year = jQuery('#wphrm-duplicate-to-salary-year').val();
        var employeeID = jQuery('form#wphrm-duplicate-salary input[name="wphrm-employee-id"]').val();
        GetSalaryMonthsForDuplicate(year, employeeID);
    });

    function GetSalaryMonthsForDuplicate(year, employeeID) {
        jQuery('#wphrm-duplicate-to-salary-month option:not(:first)').remove();
        jQuery.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                'year': year, 'employeeID': employeeID, 'action': 'WPHRMSalaryMonthAjax'
            },
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    jQuery(data.details).each(function (key, item) {
                        jQuery('#wphrm-duplicate-to-salary-month').html(jQuery('<option>', {
                            value: item.wphrmMonths,
                            text: item.wphrmMonths,
                        }));
                    });
                } else {
                    jQuery('#wphrm-duplicate-to-salary-month').html('');
                }
            }
        });
    }

    /* Year to Change Month for week ajax  */
    var yearweekload = jQuery('#wphrm-duplicate-to-salary-week-year').val();
    var employeeweekID = jQuery('form#wphrm-duplicate-week-salary input[name="wphrm-employee-id"]').val();
    if (yearweekload != '' && employeeweekID != '' && employeeweekID != 0) {
        GetSalaryMonthsForDuplicateWeek(yearweekload, employeeweekID);
    }

    jQuery('#wphrm-duplicate-to-salary-week-year').change(function () {
        var yearweekload = jQuery('#wphrm-duplicate-to-salary-week-year').val();
        var employeeweekID = jQuery('form#wphrm-duplicate-week-salary input[name="wphrm-employee-id"]').val();
        GetSalaryMonthsForDuplicateWeek(yearweekload, employeeweekID);
    });

    function GetSalaryMonthsForDuplicateWeek(year, employeeID) {

        jQuery('#wphrm-duplicate-to-salary-week-month').empty();
        jQuery.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                'year': year, 'employeeID': employeeID, 'action': 'WPHRMSalaryWeekMonthAjax'
            },
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    jQuery('#wphrm-duplicate-to-salary-week-month').append(jQuery('<option>Select Month</option>'));
                    jQuery(data.details).each(function (key, item) {
                        jQuery('#wphrm-duplicate-to-salary-week-month').append(jQuery('<option>', {
                            value: item.wphrmMonths,
                            text: item.wphrmMonths,
                        }));
                    });
                } else {
                    jQuery('#wphrm-duplicate-to-salary-week-month').html('');
                }
            }
        });
    }


    /* Year to Change Month for week ajax  */
    var yearweekload1 = jQuery('#wphrm-duplicate-to-salary-week-year').val();
    var Monthweekload1 = jQuery('#wphrm-duplicate-to-salary-week-month').val();
    var employeeweekID1 = jQuery('form#wphrm-duplicate-week-salary input[name="wphrm-employee-id"]').val();
    if (yearweekload1 != '' && employeeweekID1 != '' && employeeweekID1 != 0) {
        GetSalaryMonthsForDuplicateWeekGet(Monthweekload1,yearweekload1, employeeweekID1);
    }

    jQuery('#wphrm-duplicate-to-salary-week-month').change(function () {
        var yearweekload = jQuery('#wphrm-duplicate-to-salary-week-year').val();
        var Monthweekload = jQuery('#wphrm-duplicate-to-salary-week-month').val();
        var employeeweekID = jQuery('form#wphrm-duplicate-week-salary input[name="wphrm-employee-id"]').val();
        GetSalaryMonthsForDuplicateWeekGet(Monthweekload,yearweekload, employeeweekID);
    });

    function GetSalaryMonthsForDuplicateWeekGet(Monthweekload,year, employeeID) {
        jQuery('#wphrm-duplicate-to-salary-week-no').empty();
        jQuery.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                'year': year,'month': Monthweekload, 'employeeID': employeeID, 'action': 'WPHRMSalaryWeekMonthGetAjax'
            },
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    jQuery(data.details).each(function (key, item) {
                        jQuery('#wphrm-duplicate-to-salary-week-no').append(jQuery('<option>', {
                            value: item.wphrmMonths,
                            text: 'Week '+item.wphrmMonths,
                        }));
                    });
                } else {
                    jQuery('#wphrm-duplicate-to-salary-week-no').html('');
                }
            }
        });
    }



    // General Settings
    var wphrmGeneralSettingsInfo_form = jQuery("#wphrmGeneralSettingsInfo_form");
    wphrmGeneralSettingsInfo_form.validate({
        rules: {
            wphrm_company_email: {
                email: true,
            },
        },
        submitHandler: function (wphrmGeneralSettingsInfo_form) {
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var formData = new FormData(jQuery(wphrmGeneralSettingsInfo_form)['0']);
            formData.append('action', 'WPHRMGeneralSettingsInfo');
            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                contentType: false,
                cache: false,
                processData: false,
                data: formData,
                success: function (output) {
                    var data = JSON.parse(output);
                    if (data.success == true) {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#general_settings_success').removeClass('display-hide');
                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#general_settings_error').html(data.error);
                        jQuery('#general_settings_error').removeClass('display-hide');
                        jQuery("#general_settings_error").css('color', 'red');
                    }
                }
            });
        }
    });

    // Add roles
    var wphrmAddRolesInfo_form = jQuery("#wphrmRolePermissionForm");
    wphrmAddRolesInfo_form.validate({
        rules: {
            wphrm_rolename: {
                required: true,
            },
            wphrm_user_permission: {
                required: true,
            }
        },
        submitHandler: function (wphrmAddRolesInfo_form) {
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var formData = new FormData(jQuery(wphrmAddRolesInfo_form)['0']);
            formData.append('action', 'WPHRMAddRoles');
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
                        jQuery("#wphrm_rolename").val('');
                        jQuery('.role-permission-show').hide();
                        jQuery(".add-role").hide();
                        jQuery(".edit-role").hide();
                        jQuery("#rolenameget").val(jQuery("#rolenameget option:first").val());
                        jQuery('#addroles_settings_success').removeClass('display-hide');
                        jQuery('#addroles_settings_success').html("<i class='fa fa-check-square' aria-hidden='true'></i> " + data.success);
                        jQuery("#addroles_settings_success").css('color', '#3c763d');

                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('.role-permission-show').hide();
                        jQuery(".add-role").hide();
                        jQuery(".edit-role").hide();
                        jQuery("#rolenameget").val(jQuery("#rolenameget option:first").val());
                        jQuery('#addroles_settings_error').html(data.error);
                        jQuery('#addroles_settings_error').removeClass('display-hide');
                        jQuery("#addroles_settings_error").css('color', 'red');
                    }
                }
            });
        }
    });

    // Change Password Settings
    var wphrmChangePasswordInfo_form = jQuery("#wphrmChangePasswordInfo_form");
    wphrmChangePasswordInfo_form.validate({
        rules: {
            wphrm_current_password: {
                required: true,
            },
            wphrm_new_password: {
                required: true,
            },
            wphrm_conform_password: {
                equalTo: "#wphrm_new_password"
            }
        },
        submitHandler: function (wphrmChangePasswordInfo_form) {
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var formData = new FormData(jQuery(wphrmChangePasswordInfo_form)['0']);
            formData.append('action', 'WPHRMChangePasswordInfo');
            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                contentType: false,
                cache: false,
                processData: false,
                data: formData,
                success: function (output) {
                    var data = JSON.parse(output);
                    if (data.success == true) {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#wphrmChangePasswordInfo_success').removeClass('display-hide');
                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery("#wphrmChangePasswordInfo_success").hide();
                        jQuery('#wphrmChangePasswordInfo_error').html(data.error);
                        jQuery('#wphrmChangePasswordInfo_error').removeClass('display-hide');
                        jQuery("#wphrmChangePasswordInfo_error").css('color', 'red');
                    }
                }
            });
        }
    });

    // salary-slip-settings
    var wphrmSalarySlipInfo_form = jQuery("#wphrmSalarySlipInfo_form");
    wphrmSalarySlipInfo_form.validate({
        rules: {
        },
        submitHandler: function (wphrmSalarySlipInfo_form) {
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var formData = new FormData(jQuery(wphrmSalarySlipInfo_form)['0']);
            formData.append('action', 'WPHRMSalarySlipInfo');
            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                contentType: false,
                cache: false,
                processData: false,
                data: formData,
                success: function (output) {
                    var data = JSON.parse(output);
                    if (data.success == true) {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#wphrmSalarySlipInfo_success').removeClass('display-hide');
                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery("#wphrmSalarySlipInfo_success").hide();
                        jQuery('#wphrmSalarySlipInfo_error').html(data.error);
                        jQuery('#wphrmSalarySlipInfo_error').removeClass('display-hide');
                        jQuery("#wphrmSalarySlipInfo_error").css('color', 'red');
                    }
                }
            });
        }
    });

    // For page permissions
    jQuery("#wphrmSalarySlipFieldsInfoForm").submit(function (e) {
        e.preventDefault();
        var wphrmearninglebal = [];
        jQuery("input[name='earninglebal[]']").each(function () {
            wphrmearninglebal.push(jQuery(this).val());
        });
        var wphrmearningtype = [];
        jQuery("input[name='earningtype[]']").each(function () {
            wphrmearningtype.push(jQuery(this).val());
        });

        var wphrmearningamount = [];
        jQuery("input[name='earningamount[]']").each(function () {
            wphrmearningamount.push(jQuery(this).val());
        });

        var wphrmdeductionlebal = [];
        jQuery("input[name='deductionlebal[]']").each(function () {
            wphrmdeductionlebal.push(jQuery(this).val());
        });

        var wphrmdeductiontype = [];
        jQuery("input[name='deductiontype[]']").each(function () {
            wphrmdeductiontype.push(jQuery(this).val());
        });

        var wphrmdeductionamount = [];
        jQuery("input[name='deductionamount[]']").each(function () {
            wphrmdeductionamount.push(jQuery(this).val());
        });
        wphrm_data = {
            'action': 'wphrmAddEarningLabelInfo',
            'wphrmearninglebal': wphrmearninglebal,
            'wphrmearningtype': wphrmearningtype,
            'wphrmearningamount': wphrmearningamount,
            'wphrmdeductionlebal': wphrmdeductionlebal,
            'wphrmdeductiontype': wphrmdeductiontype,
            'wphrmdeductionamount': wphrmdeductionamount,
        }
        jQuery('.preloader-custom-gif').show();
        jQuery('.preloader ').show();
        jQuery.post(ajaxurl, wphrm_data, function (response) {
            var data = JSON.parse(response);
            if (data.success == true) {
                jQuery('.preloader-custom-gif').hide();
                jQuery('.preloader ').hide();
                jQuery('#wphrmsalaryslipfield_success').removeClass('display-hide');
            } else {
                jQuery('.preloader-custom-gif').hide();
                jQuery('.preloader ').hide();
                jQuery('#wphrmsalaryslipfield_error').html(data.error);
                jQuery('#wphrmsalaryslipfield_error').removeClass('display-hide');
                jQuery("#wphrmsalaryslipfield_error").css('color', 'red');
            }
        });
    });

    // Bank detail fields info settings
    jQuery("#wphrmBankDetailsFieldsInfoForm").submit(function (e) {
        e.preventDefault();
        var wphrmBankfieldsLebal = [];
        jQuery("input[name='bank-fields-lebal[]']").each(function () {
            wphrmBankfieldsLebal.push(jQuery(this).val());
        });
        wphrm_data = {
            'action': 'wphrmAddBnakDetailsLabelInfo',
            'wphrmBankfieldsLebal': wphrmBankfieldsLebal,
        }
        jQuery('.preloader-custom-gif').show();
        jQuery('.preloader ').show();
        jQuery.post(ajaxurl, wphrm_data, function (response) {
            var data = JSON.parse(response);
            if (data.success == true) {
                jQuery('.preloader-custom-gif').hide();
                jQuery('.preloader ').hide();
                jQuery('#wphrmBankfield_success').removeClass('display-hide');
            } else {
                jQuery('.preloader-custom-gif').hide();
                jQuery('.preloader ').hide();
                jQuery('#wphrmBankfield_error').html(data.error);
                jQuery('#wphrmBankfield_error').removeClass('display-hide');
                jQuery("#wphrmBankfield_error").css('color', 'red');
            }
        });
    });

    // Other detail fields info settings
    jQuery("#wphrmotherDetailsFieldsInfoForm").submit(function (e) {
        e.preventDefault();
        var wphrmOtherfieldsLebal = [];
        jQuery("input[name='other-fields-lebal[]']").each(function () {
            wphrmOtherfieldsLebal.push(jQuery(this).val());
        });
        wphrm_data = {
            'action': 'wphrmAddOtherDetailsLabelInfo',
            'wphrmOtherfieldsLebal': wphrmOtherfieldsLebal,
        }
        jQuery('.preloader-custom-gif').show();
        jQuery('.preloader ').show();
        jQuery.post(ajaxurl, wphrm_data, function (response) {
            var data = JSON.parse(response);
            if (data.success == true) {
                jQuery('.preloader-custom-gif').hide();
                jQuery('.preloader ').hide();
                jQuery('#wphrmotherfield_success').removeClass('display-hide');
            } else {
                jQuery('.preloader-custom-gif').hide();
                jQuery('.preloader ').hide();
                jQuery('#wphrmotherfield_error').html(data.error);
                jQuery('#wphrmotherfield_error').removeClass('display-hide');
                jQuery("#wphrmotherfield_error").css('color', 'red');
            }
        });
    });

    // upload Document fields info settings
    jQuery("#wphrmEmployeeDocumentsForm").submit(function (e) {
        e.preventDefault();
        var documentsfieldsLebal = [];
        jQuery("input[name='documentsfieldslebal[]']").each(function () {
            documentsfieldsLebal.push(jQuery(this).val());
        });
        wphrm_data = {
            'action': 'wphrmAddDocumentsfieldslebalInfo',
            'documentsfieldsLebal': documentsfieldsLebal,
        }
        jQuery('.preloader-custom-gif').show();
        jQuery('.preloader ').show();
        jQuery.post(ajaxurl, wphrm_data, function (response) {
            var data = JSON.parse(response);
            if (data.success == true) {
                jQuery('.preloader-custom-gif').hide();
                jQuery('.preloader ').hide();
                jQuery('#wphrmotherfield_success').removeClass('display-hide');
            } else {
                jQuery('.preloader-custom-gif').hide();
                jQuery('.preloader ').hide();
                jQuery('#wphrmotherfield_error').html(data.error);
                jQuery('#wphrmotherfield_error').removeClass('display-hide');
                jQuery("#wphrmotherfield_error").css('color', 'red');
            }
        });
    });

    // Salary detail fields info settings
    jQuery("#wphrmSalaryDetailsFieldsSettingsForm").submit(function (e) {
        e.preventDefault();
        var salaryFieldsLebal = [];
        jQuery("input[name='salary-fields-lebal[]']").each(function () {
            salaryFieldsLebal.push(jQuery(this).val());
        });
        wphrm_data = {
            'action': 'wphrmSalaryDetailsFieldsSettingsinfo',
            'salaryFieldsLebal': salaryFieldsLebal,
        }
        jQuery('.preloader-custom-gif').show();
        jQuery('.preloader ').show();
        jQuery.post(ajaxurl, wphrm_data, function (response) {
            var data = JSON.parse(response);
            if (data.success == true) {
                jQuery('.preloader-custom-gif').hide();
                jQuery('.preloader ').hide();
                jQuery('#wphrmsalaryfield_success').removeClass('display-hide');
            } else {
                jQuery('.preloader-custom-gif').hide();
                jQuery('.preloader ').hide();
                jQuery('#wphrmsalaryfield_error').html(data.error);
                jQuery('#wphrmsalaryfield_error').removeClass('display-hide');
                jQuery("#wphrmsalaryfield_error").css('color', 'red');
            }
        });
    });

    // notifications settings
    var wphrmNotificationsSettingsInfo_form = jQuery("#wphrmNotificationsSettingsInfo_form");
    wphrmNotificationsSettingsInfo_form.validate({
        rules: {},
        submitHandler: function (wphrmNotificationsSettingsInfo_form) {
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var formData = new FormData(jQuery(wphrmNotificationsSettingsInfo_form)['0']);
            formData.append('action', 'WPHRMNotificationsSettingsInfo');
            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                contentType: false,
                cache: false,
                processData: false,
                data: formData,
                success: function (output) {
                    var data = JSON.parse(output);
                    if (data.success == true) {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#wphrm_notifications_settings_success').removeClass('display-hide');
                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery("#wphrm_notifications_settings_success").hide();
                        jQuery('#wphrm_notifications_settings_error').html(data.error);
                        jQuery('#wphrm_notifications_settings_error').removeClass('display-hide');
                        jQuery("#wphrm_notifications_settings_error").css('color', 'red');
                    }
                }
            });
        }
    });

    // Hide/Show Employee Information Sections settings
    var wphrmhide_show_employee_section_form = jQuery("#wphrmhide-show-employee-section-form");
    wphrmhide_show_employee_section_form.validate({
        rules: {},
        submitHandler: function (wphrmhide_show_employee_section_form) {
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();

            var bankChecked = jQuery(".bank-checked").is(":checked");
            var salaryChecked = jQuery(".salary-checked").is(":checked");
            var documentChecked = jQuery(".document-checked").is(":checked");
            var otherChecked = jQuery(".other-checked").is(":checked");

            if (bankChecked == true) {
                var bankChecked1 = '1';
            } else {
                var bankChecked1 = '0';
            }
            if (salaryChecked == true) {
                var salaryChecked1 = '1';
            } else {
                var salaryChecked1 = '0';
            }
            if (documentChecked == true) {
                var documentChecked1 = '1';
            } else {
                var documentChecked1 = '0';
            }
            if (otherChecked == true) {
                var otherChecked1 = '1';
            } else {
                var otherChecked1 = '0';
            }
            jQuery(".bank-checked-yes").val(bankChecked1);
            jQuery(".salary-checked-yes").val(salaryChecked1);
            jQuery(".documents-checked-yes").val(documentChecked1);
            jQuery(".other-checked-yes").val(otherChecked1);


            var formData = new FormData(jQuery(wphrmhide_show_employee_section_form)['0']);
            formData.append('action', 'WPHRMHideShowEmployeeSectionInfo');
            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                contentType: false,
                cache: false,
                processData: false,
                data: formData,
                success: function (output) {
                    var data = JSON.parse(output);
                    if (data.success != '') {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#wphrm_hide_show_section_success').removeClass('display-hide');
                        jQuery('#wphrm_hide_show_section_success').html("<i class='fa fa-check-square' aria-hidden='true'></i> " + data.success);

                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#wphrm_hide_show_section_error').html(data.error);
                        jQuery('#wphrm_hide_show_section_error').removeClass('display-hide');
                        jQuery("#wphrm_hide_show_section_error").css('color', 'red');
                    }
                }
            });
        }
    });

    // For notice
    jQuery("#wphrmNoticeInfo_frm").click(function (e) {
        e.preventDefault();
        var wphrm_notice_id = jQuery("#wphrm_notice_id").val();
        var wphrm_notice_title = jQuery("#wphrm_notice_title").val();
        var wphrm_notice_desc = tinymce.get('wphrm_notice_desc').getContent();
        /*J@F*/
        var wphrm_notice_date = jQuery('#wphrm_notice_date').val();
        var wphrm_notice_department = jQuery('#wphrm_notice_department').val();
        var wphrm_invitation_notice = jQuery('#wphrm_invitation_notice').is(':checked') ? 1 : 0;

        var wphrm_invitation_recipient = [];
        jQuery('input[name="wphrm_invitation_recipient[]"]').each(function(e){
            if(jQuery(this).is(':checked')){
                wphrm_invitation_recipient.push(jQuery(this).val());
            }
        });
        if(wphrm_notice_title == ''){
            alert('Please provide a Notice Title.');
            return false;
        }
        /*J@F*/
        wphrm_data = {
            'action': 'WPHRMNoticeInfo',
            'wphrm_notice_title': wphrm_notice_title,
            'wphrm_notice_desc': wphrm_notice_desc,
            'wphrm_notice_id': wphrm_notice_id,
            'wphrm_notice_date': wphrm_notice_date,
            'wphrm_notice_department': wphrm_notice_department,
            'wphrm_invitation_notice': wphrm_invitation_notice,
            'wphrm_invitation_recipient': wphrm_invitation_recipient,
        }
        jQuery('.preloader-custom-gif').show();
        jQuery('.preloader ').show();
        jQuery.post(ajaxurl, wphrm_data, function (response) {
            //console.log('response:', response, 'data_sent: ', wphrm_data);alert();
            var data = JSON.parse(response);
            if (data.success == true) {
                jQuery('.preloader-custom-gif').hide();
                jQuery('.preloader ').hide();
                jQuery('#wphrmNoticeInfo_success').removeClass('display-hide');
                jQuery("html, body").animate({scrollTop: 0}, "slow");
            } else {
                jQuery('.preloader-custom-gif').hide();
                jQuery('.preloader ').hide();
                jQuery("#wphrmNoticeInfo_success").hide();
                jQuery('#wphrmNoticeInfo_error').html(data.error);
                jQuery('#wphrmNoticeInfo_error').removeClass('display-hide');
                jQuery("#wphrmNoticeInfo_error").css('color', 'red');
                jQuery("html, body").animate({scrollTop: 0}, "slow");
            }
        });
    });

    // For user permissions
    var wphrmUserPermissionInfo_frm = jQuery("#wphrmUserPermissionInfo_frm");
    wphrmUserPermissionInfo_frm.validate({
        rules: {},
        submitHandler: function (wphrmUserPermissionInfo_frm) {
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var formData = new FormData(jQuery(wphrmUserPermissionInfo_frm)['0']);
            formData.append('action', 'WPHRMUserPermissionInfo');
            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                contentType: false,
                cache: false,
                processData: false,
                data: formData,
                success: function (output) {
                    var data = JSON.parse(output);
                    if (data.success == true) {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#wphrm_user_permission_success').removeClass('display-hide');

                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery("#wphrm_user_permission_success").hide();
                        jQuery('#wphrm_user_permission_error').html(data.error);
                        jQuery('#wphrm_user_permission_error').removeClass('display-hide');
                        jQuery("#wphrm_user_permission_error").css('color', 'red');
                    }
                }
            });
        }
    });

    // For user permissions
    var wphrsalaryDayOrHourlyForm = jQuery("#wphrsalaryDayOrHourlyForm");
    wphrsalaryDayOrHourlyForm.validate({
        rules: {
            'wphrm-according': 'required',
        },
        submitHandler: function (wphrsalaryDayOrHourlyForm) {
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var formData = new FormData(jQuery(wphrsalaryDayOrHourlyForm)['0']);
            formData.append('action', 'wphrsalaryDayOrHourlyInfo');
            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                contentType: false,
                cache: false,
                processData: false,
                data: formData,
                success: function (output) {
                    var data = JSON.parse(output);
                    if (data.success == true) {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#wphrsalaryDayOrHourlySuccess').removeClass('display-hide');
                        window.setTimeout(function () {
                            window.location.reload();
                        }, 1000);
                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#wphrsalaryDayOrHourlyError').html(data.error);
                        jQuery('#wphrsalaryDayOrHourlyError').removeClass('display-hide');
                        jQuery("#wphrsalaryDayOrHourlyError").css('color', 'red');
                    }
                }
            });
        }
    });

    // For user permissions
    var wphrSalaryGenerationForm = jQuery("#wphrSalaryGenerationForm");
    wphrSalaryGenerationForm.validate({
        rules: {
            'monthdate': 'required',
        },
        submitHandler: function (wphrSalaryGenerationForm) {
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var formData = new FormData(jQuery(wphrSalaryGenerationForm)['0']);
            formData.append('action', 'WPHRMSalaryGenerationSettings');
            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                contentType: false,
                cache: false,
                processData: false,
                data: formData,
                success: function (output) {
                    var data = JSON.parse(output);
                    if (data.success == true) {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#wphrSalaryGenerationSuccess').removeClass('display-hide');

                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#wphrSalaryGenerationError').html(data.error);
                        jQuery('#wphrSalaryGenerationError').removeClass('display-hide');
                        jQuery("#wphrSalaryGenerationError").css('color', 'red');
                    }
                }
            });
        }
    });


    // For add and update currency
    var markAttendanceBulk = jQuery("#mark-attendance-bulk");
    markAttendanceBulk.validate({
        rules: {
            'employee-id': 'required',
            'from-date': 'required',
            'to-date': 'required',
            'attendance-mark': 'required'

        },
        submitHandler: function (markAttendanceBulk) {
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var formData = new FormData(jQuery(markAttendanceBulk)['0']);
            formData.append('action', 'WPHRMMarkAttendanceBulk');
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
                        jQuery('#markAttendanceBulkSuccess').removeClass('display-hide');
                        jQuery('#markAttendanceBulkSuccess').html(data.success);
                        jQuery("#my-select").val("");
                        jQuery("#set-from-date").val("");
                        jQuery("#set-to-date").val("");
                        jQuery("#attendance-mark").val("");
                        window.setTimeout(function () {
                            window.location.reload();
                        }, 500);
                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#markAttendanceBulkError').html(data.error);
                        jQuery('#markAttendanceBulkError').removeClass('display-hide');
                        jQuery("#markAttendanceBulkError").css('color', 'red');
                        jQuery("#my-select").val("");
                        jQuery("#set-from-date").val("");
                        jQuery("#set-to-date").val("");
                        jQuery("#attendance-mark").val("");

                    }
                }
            });
        }
    });

    // For add and update currency
    var wphrcurrencyForm = jQuery(".wphrcurrencyForm");
    wphrcurrencyForm.validate({
        rules: {
            'currency-sign': 'required',
            'currency-name': 'required'

        },
        submitHandler: function (wphrcurrencyForm) {
            var id = jQuery('#currencyloaddata').val();
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var formData = new FormData(jQuery(wphrcurrencyForm)['0']);
            formData.append('action', 'wphrmaddcurrencysettingsinfo');
            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                contentType: false,
                cache: false,
                processData: false,
                data: formData,
                success: function (output) {
                    var data = JSON.parse(output);
                    if (data.success == true) {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#wphrcurrencySuccess').removeClass('display-hide');
                        jQuery("#currency-sign").val("");
                        jQuery("#currency-name").val("");
                        jQuery("#currency-desc").val("");
                        jQuery("#wphrm_currency").val("");
                        location.reload();

                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#wphrcurrencyError').html(data.error);
                        jQuery('#wphrcurrencyError').removeClass('display-hide');
                        jQuery("#wphrcurrencyError").css('color', 'red');
                    }
                }
            });
        }
    });

    // For expense report amount
    var wphrmExpenseReportInfo_frm = jQuery("#wphrmExpenseReportInfo_frm");
    wphrmExpenseReportInfo_frm.validate({
        rules: {},
        submitHandler: function (wphrmExpenseReportInfo_frm) {
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var formData = new FormData(jQuery(wphrmExpenseReportInfo_frm)['0']);
            formData.append('action', 'WPHRMExpenseReportInfo');
            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                contentType: false,
                cache: false,
                processData: false,
                data: formData,
                success: function (output) {
                    var data = JSON.parse(output);
                    if (data.success == true) {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#wphrm_expense_report_success').removeClass('display-hide');

                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery("#wphrm_expense_report_error").hide();
                        jQuery('#wphrm_expense_report_error').html(data.error);
                        jQuery('#wphrm_expense_report_error').removeClass('display-hide');
                        jQuery("#wphrm_expense_report_error").css('color', 'red');
                    }
                }
            });
        }
    });
    // For expense report amount
    var wphrmimportxmlInfo_frm = jQuery("#wphrmimportxmlInfo_frm");
    wphrmimportxmlInfo_frm.validate({
        submitHandler: function (wphrmimportxmlInfo_frm) {
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var formData = new FormData(jQuery(wphrmimportxmlInfo_frm)['0']);
            var fileCheck = jQuery('#wphrm_import ').val();
            if (fileCheck != '') {
                formData.append('action', 'WPHRMImportXml');
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
                            jQuery('#import_error').hide();
                            jQuery('#import_success').removeClass('display-hide');
                            jQuery('#import_success').html("<i class='fa fa-check-square' aria-hidden='true'></i> " + data.success);
                            jQuery('#checkdata').click();
                        } else {
                            jQuery('.preloader ').hide();
                            jQuery('.preloader-custom-gif').hide();
                            jQuery('.import_success').hide();
                            jQuery('#import_error').html("<i class='fa fa-close' aria-hidden='true'></i> " + data.error);
                            jQuery('#import_error').removeClass('display-hide');
                            jQuery('#checkdata').click();
                        }
                    }
                });
            } else {
                jQuery('.preloader-custom-gif').hide();
                jQuery('.preloader ').hide();
                jQuery('#checkdata').click();
                jQuery('.import_success').hide();
                jQuery('#import_error').removeClass('display-hide');
                jQuery('#import_error').html("<i class='fa fa-close' aria-hidden='true'></i> " + WPHRMCustomJS.fileError);
            }
        }
    });

    // For page permissions
    jQuery("#wphrmPagePermissionInfo_frm").click(function (e) {
        e.preventDefault();
        var wphrm_page_permissions = jQuery('#wphrm_page_permissions').val();
        var wphrm_employee = [];
        jQuery("input[name='wphrm_employee[]']").each(function () {
            if (jQuery(this).is(':checked')) {
                wphrm_employee.push(jQuery(this).val());
            } else {
                wphrm_employee.push('off');
            }
        });
        var wphrmDepartments = [];
        jQuery("input[name='wphrmDepartments[]']").each(function () {
            if (jQuery(this).is(':checked')) {
                wphrmDepartments.push(jQuery(this).val());
            } else {
                wphrmDepartments.push('off');
            }
        });
        var wphrm_holidays = [];
        jQuery("input[name='wphrm_holidays[]']").each(function () {
            if (jQuery(this).is(':checked')) {
                wphrm_holidays.push(jQuery(this).val());
            } else {
                wphrm_holidays.push('off');
            }
        });
        var wphrm_attendances = [];
        jQuery("input[name='wphrm_attendances[]']").each(function () {
            if (jQuery(this).is(':checked')) {
                wphrm_attendances.push(jQuery(this).val());
            } else {
                wphrm_attendances.push('off');
            }
        });
        var wphrm_leave_applications = [];
        jQuery("input[name='wphrm_leave_applications[]']").each(function () {
            if (jQuery(this).is(':checked')) {
                wphrm_leave_applications.push(jQuery(this).val());
            } else {
                wphrm_leave_applications.push('off');
            }
        });
        var wphrm_financials = [];
        jQuery("input[name='wphrm_financials[]']").each(function () {
            if (jQuery(this).is(':checked')) {
                wphrm_financials.push(jQuery(this).val());
            } else {
                wphrm_financials.push('off');
            }
        });
        var wphrm_notice = [];
        jQuery("input[name='wphrm_notice[]']").each(function () {
            if (jQuery(this).is(':checked')) {
                wphrm_notice.push(jQuery(this).val());
            } else {
                wphrm_notice.push('off');
            }
        });
        var wphrm_settings = [];
        jQuery("input[name='wphrm_settings[]']").each(function () {
            if (jQuery(this).is(':checked')) {
                wphrm_settings.push(jQuery(this).val());
            } else {
                wphrm_settings.push('off');
            }
        });
        wphrm_data = {
            'action': 'WPHRMPagePermissionInfo',
            'wphrm_page_permissions': wphrm_page_permissions,
            'wphrm_employee': wphrm_employee,
            'wphrmDepartments': wphrmDepartments,
            'wphrm_holidays': wphrm_holidays,
            'wphrm_attendances': wphrm_attendances,
            'wphrm_leave_applications': wphrm_leave_applications,
            'wphrm_financials': wphrm_financials,
            'wphrm_notice': wphrm_notice,
            'wphrm_settings': wphrm_settings,
        }
        jQuery('.preloader-custom-gif').show();
        jQuery('.preloader ').show();
        jQuery.post(ajaxurl, wphrm_data, function (response) {
            var data = JSON.parse(response);
            if (data.success == true) {
                jQuery('.preloader-custom-gif').hide();
                jQuery('.preloader ').hide();
                jQuery('#wphrm_page_permission_success').removeClass('display-hide');

            } else {
                jQuery('.preloader-custom-gif').hide();
                jQuery('.preloader ').hide();
                jQuery('#wphrm_page_permission_error').html(data.error);
                jQuery('#wphrm_page_permission_error').removeClass('display-hide');
                jQuery("#wphrm_page_permission_error").css('color', 'red');
            }
        });
    });

    /** Input box not copy and paste validation**/
    jQuery("#wphrm_employee_bank_account_no").bind('copy paste', function (e) {
        e.preventDefault();
        jQuery("#wphrm_bank_details_error").html("Copy paste doesn't work for security reasons.");
        jQuery('.alert').addClass('display-hide');
        jQuery('#wphrm_bank_details_error').removeClass('display-hide');
    });

    jQuery('#wphrm_employee_bank_account_no').bind('paste', function (e) {
        e.preventDefault();
        jQuery("#wphrm_bank_details_error").html("Copy paste doesn't work for security reasons.");
        jQuery('.alert').addClass('display-hide');
        jQuery('#wphrm_bank_details_error').removeClass('display-hide');
    });

    var month = new Date();
    var monthGet = month.getMonth();
    jQuery('.month-data').val(monthGet);


    jQuery("#myid li").click(function () {
        var dataId = jQuery(this).attr("data-id");
        jQuery('.month-data').val(dataId);
    });


    var setYear = jQuery('.yeardata').val();
    /* For add multiple input text in holiday  */
    var $insertBefore = jQuery('#insertBefore');
    var $i = 0;


    jQuery('#plusButton').click(function () {
        var monthselect = jQuery('.month-data').val();
        var setYear = jQuery('.yeardata').val();
        $i = $i + 1;
        jQuery(' <div class="form-group addHoliday' + $i + '"> ' +
               '<div class="col-md-5" ><input class="form-control form-control-inline input-medium date-picker-default' + $i + '" name="holiday_date[' + $i + ']" type="text" value="" placeholder="Date"/></div>' +
               '<div class="col-md-5"><input class="form-control form-control-inline occasioncl" name="occasion[' + $i + ']" type="text" value="" placeholder="' + WPHRMCustomJS.occasion + '"/></div>' +
               '<div class="col-md-2"><a id="remScnt" class="btn red" onclick="addHoliday(' + $i + ');"><i class="fa fa-trash" aria-hidden="true"></i></a></div>' +
               '</div>').insertBefore($insertBefore);
        jQuery.fn.datepicker.defaults.format = "dd-mm-yyyy";
        jQuery.fn.datepicker.defaults.autoclose = true;
        jQuery('.date-picker-default' + $i).datepicker("setDate", new Date(setYear, monthselect));
    });

    /* For add multiple input text in Earning  */
    var $EarninginsertBefore = jQuery('#earninginsertBefore');
    var $i = 0;
    jQuery('#add-more-earning').click(function () {

        $i = $i + 1;
        jQuery('<tr id="earning_scents' + $i + '"><td><input type="text"  class="earningDeduction"  name="wphrm-earning-lebal[]"></td>' +
               '<td  style="padding-right: 0px; float:right;">' +
               '<input type="text" class="earningcal earningDeduction validationonnumber two-digits" value="' + decimalpointvalue + '" onkeyup="calculateEarningSum()" name="wphrm-earning-value[]" >' +
               '</td><td style="text-align: center;"><a id="remScnt" class="btn red" onclick="removeearning(' + $i + ');"><i class="fa fa-trash" aria-hidden="true"></i></a></td>' +
               '</tr>').insertBefore($EarninginsertBefore);
    });

    /* For add multiple input text in Deduction  */
    var $deductioninsertBefore = jQuery('#deductioninsertBefore');
    var $i = 0;
    jQuery('#add-more-Deduction').click(function () {
        $i = $i + 1;
        jQuery('<tr id="deduction_scents' + $i + '"><td><input type="text"  class="earningDeduction"   name="wphrm-deduction-lebal[]"></td>' +
               '<td  style="padding-right: 0px; float:right;">' +
               '<input type="text" class="earningDeduction deductioncal validationonnumber two-digits" value="' + decimalpointvalue + '" onkeyup="calculateDeductionSum()" name="wphrm-deduction-value[]" >' +
               '</td><td style="text-align: center;"><a  id="remScnt" class="btn red" onclick="removedeductions(' + $i + ');"><i class="fa fa-trash" aria-hidden="true"></i></a></td>' +
               '</tr>').insertBefore($deductioninsertBefore);
    });

    /* For add multiple input text in Deduction  */
    var $bankfieldslebalBefore = jQuery('#bank-fields-lebal-Before');
    var $i = 0;
    jQuery('#add-bank-fields-lebal').click(function () {
        $i = $i + 1;
        jQuery('<div class="form-group" id="Bankfieldlabel_scents' + $i + '"><div class="col-md-8">' +
               '<input class="form-control form-control-inline" name="bank-fields-lebal[]" placeholder="' + WPHRMCustomJS.bankfieldlabel + '"></div>' +
               '<div class="col-md-2"><a  id="remScnt" class="btn red" onclick="Bankfieldlabel(' + $i + ');"><i class="fa fa-trash" aria-hidden="true"></i></a></div>' +
               '</div></div>').insertBefore($bankfieldslebalBefore);
    });

    /* For add multiple input text in Deduction  */
    var $otherfieldslebalBefore = jQuery('#other-fields-lebal-Before');
    var $i = 0;
    jQuery('#add-other-fields-lebal').click(function () {
        $i = $i + 1;
        jQuery('<div class="form-group" id="Otherfieldlabel_scents' + $i + '"><div class="col-md-8">' +
               '<input class="form-control form-control-inline" name="other-fields-lebal[]" placeholder="' + WPHRMCustomJS.otherfieldlabel + '"></div>' +
               '<div class="col-md-2"><a id="remScnt" class="btn red" onclick="Otherfieldlabel(' + $i + ');"><i class="fa fa-trash" aria-hidden="true"></i></a></div>' +
               '</div></div>').insertBefore($otherfieldslebalBefore);
    });

    //documentfieldlabel documentfieldlabel_scents
    /* For add multiple input for document upload  */
    var $documentsfieldslebalBefore = jQuery('#documentsfieldslebalBefore');
    var $i = 0;
    jQuery('#documentsfieldslebaladd').click(function () {
        $i = $i + 1;
        jQuery('<div class="form-group" id="documentfieldlabel_scents' + $i + '"><div class="col-md-8">' +
               '<input class="form-control form-control-inline" name="documentsfieldslebal[]" placeholder="' + documentfieldlabel + '"></div>' +
               '<div class="col-md-2"><a id="remScnt" class="btn red" onclick="documentfieldlabel(' + $i + ');"><i class="fa fa-trash" aria-hidden="true"></i></a></div>' +
               '</div></div>').insertBefore($documentsfieldslebalBefore);
    });

    /* For add multiple input text in Deduction  */
    var $salaryfieldslebalBefore = jQuery('#salary-fields-lebal-Before');
    var $i = 0;
    jQuery('#add-salary-fields-lebal').click(function () {
        $i = $i + 1;
        jQuery('<div class="form-group" id="salaryfieldlabel_scents' + $i + '"><div class="col-md-8">' +
               '<input class="form-control form-control-inline" name="salary-fields-lebal[]" placeholder="' + WPHRMCustomJS.salaryfieldlabel + '"></div>' +
               '<div class="col-md-2"><a  id="remScnt" class="btn red" onclick="Salaryfieldlabel(' + $i + ');"><i class="fa fa-trash" aria-hidden="true"></i></a></div>' +
               '</div></div>').insertBefore($salaryfieldslebalBefore);
    });

    /* For add multiple input text in Deduction  */
    var $earninglebalinsertBefore = jQuery('#earninglebalinsertBefore');
    var $i = 0;
    jQuery('#addearninglebal').click(function () {
        $i = $i + 1;
        jQuery('<div class="form-group" id="earning_scents' + $i + '"><div class="col-md-4">' +
               '<input class="form-control form-control-inline" name="earninglebal[]" placeholder="' + WPHRMCustomJS.earninglabel + '"></div>' +
               '<div class="col-md-4"><input disabled class="form-control form-control-inline" value="% of Current Salary"/><input type="hidden" name="earningtype[]" id="earningtype" value="pamount"/></div>' +
               '<div class="col-md-2">' +
               '<input class="form-control form-control-inline wphrm-salary-persanage validationonnumber" name="earningamount[]" id="earningamount"/></div>' +
               '<div class="col-md-2"><a  id="remScnt" class="btn red" onclick="removeearning(' + $i + ');"><i class="fa fa-trash" aria-hidden="true"></i></a></div>' +
               '</div></div>').insertBefore($earninglebalinsertBefore);
    });
    var $deductionlebalinsertBefore = jQuery('#deductionlebalinsertBefore');
    var $i = 0;
    jQuery('#adddeductionlebal').click(function () {
        $i = $i + 1;
        jQuery('<div class="form-group" id="deduction_scents' + $i + '"><div class="col-md-4">' +
               '<input class="form-control form-control-inline" name="deductionlebal[]" placeholder="' + WPHRMCustomJS.deductionlabel + '"></div>' +
               '<div class="col-md-4"><input disabled class="form-control form-control-inline" value="% of Current Salary"/><input type="hidden" name="deductiontype[]" id="deductiontype" value="pamount"/></div>' +
               '<div class="col-md-2">' +
               '<input class="form-control form-control-inline wphrm-salary-persanage validationonnumber" name="deductionamount[]" id="deductionamount"/></div>' +
               '<div class="col-md-2"><a  id="remScnt" class="btn red" onclick="removedeductions(' + $i + ');"><i class="fa fa-trash" aria-hidden="true"></i></a></div>' +
               '</div></div>').insertBefore($deductionlebalinsertBefore);
    });

    /* For add multiple input text in departments  */
    var $insertBeforeDepartment = jQuery('#insertBeforeDepartment');
    var $i = 0;
    jQuery('#plusButtonDepartment').click(function () {
        $i = $i + 1;
        departmentName = (typeof departmentName == 'undefined') ? 'Department Name' : departmentName;
        jQuery('<div class="form-group" id="departmentID' + $i + '"><div class="col-md-10">' +
               '<input class="form-control form-control-inline " name="departmentName[]" id="department_name" type="text"  value="" placeholder="' + departmentName + '" /></div>' +
               '<div class="col-md-2"><a  id="remScnt" class="btn red" onclick="departmentAdd(' + $i + ');"><i class="fa fa-trash" aria-hidden="true"></i></a></div>' +
               '</div>').insertBefore($insertBeforeDepartment);
    });

    /* For add multiple input text in designation  */
    var $insertBeforeDesignation = jQuery('#insertBeforeDesignation');
    var $i = 0;
    jQuery('#plusButtonDesignation').click(function () {
        $i = $i + 1;
        designationName = (typeof designationName == 'undefined') ? 'Designation Name' : designationName;
        jQuery('<div class="form-group" id="designationID' + $i + '"><div class="col-md-10">' +
               '<input class="form-control form-control-inline " name="designation_name[]" id="designation_name" type="text" value="" placeholder="' + designationName + '" /></div>' +
               '<div class="col-md-2"><a  id="remScnt" class="btn red" onclick="designationAdd(' + $i + ');"><i class="fa fa-trash" aria-hidden="true"></i></a></div>' +
               '</div>').insertBefore($insertBeforeDesignation);
    });


    /*for Add leave Applications  modul*/
    var wphrm_user_leave_applications_frm = jQuery("#wphrm_user_leave_applications_frm");
    wphrm_user_leave_applications_frm.validate({
        rules: {
            wphrm_leavetype: {
                required: true,
            },
            wphrm_leavedate: {
                required: true,
            },
            wphrm_leavedate_to: {
                required: true,
            },
            wphrm_reason: {
                required: true,
            }
        },
        submitHandler: function (wphrm_user_leave_applications_frm) {
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var fromleavehalf = jQuery(".wphrm-from-leve-day-type").is(":checked");
            var toleavehalf = jQuery(".wphrm-to-leve-day-type").is(":checked");
            if (fromleavehalf == true) {
                var fromleavehalf1 = '0';
            } else {
                var fromleavehalf1 = '1';
            }
            if (toleavehalf == true) {
                var toleavehalf1 = '0';
            } else {
                var toleavehalf1 = '1';
            }

            jQuery("#wphrm-from-leve-day").val(fromleavehalf1);
            jQuery("#wphrm-to-leve-day").val(toleavehalf1);
            var formData = new FormData(jQuery(wphrm_user_leave_applications_frm)['0']);


            var EnteredDate = jQuery("#wphrm_leavedate").val(); // For JQuery

            var wphrmEmployeeID = jQuery("#wphrm_employeeID").val(); // For JQuery
            var dateAr = EnteredDate.split('-');
            var newDate = dateAr[2] + '-' + dateAr[1] + '-' + dateAr[0];
            var myDates = new Date(newDate);
            var myDate = myDates.setDate(myDates.getDate() + 2);

            var EnteredToDate = jQuery("#wphrm_leavedate_to").val(); // For JQuery

            var d = new Date();
            var curr_date = d.getDate();
            var curr_month = d.getMonth() + 1;
            curr_month = curr_month < 10 ? '0' + curr_month : curr_month;
            curr_date = curr_date < 10 ? '0' + curr_date : curr_date;
            var curr_year = d.getFullYear();
            var newtoday = curr_year + "-" + curr_month + "-" + curr_date;
            var todaydate = new Date(newtoday);
            var todaydates = todaydate.setDate(todaydate.getDate() + 2);

            if (EnteredToDate != '') {
                var dateArr = EnteredToDate.split('-');
                var newDateTo = dateArr[2] + '-' + dateArr[1] + '-' + dateArr[0];
                var myDatesTo = new Date(newDateTo);
                var myDatesTo = myDatesTo.setDate(myDatesTo.getDate() + 2);
            } else {
                var myDatesTo = todaydates;
            }

            if (wphrmEmployeeID == '') {
                if (myDate >= todaydates && myDatesTo >= myDate) {
                    formData.append('action', 'WPHRMUserLeaveApplicationsInfo');
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
                                jQuery('#wphrm_add_leave_applications_success').removeClass('display-hide');
                                jQuery('#wphrm_leavetype').val("");
                                jQuery('#wphrm_leavedate').val("");
                                jQuery('#wphrm_reason').val("");
                                window.setTimeout(function () {
                                    window.location.reload();
                                }, 1500);
                            } else {
                                jQuery('.preloader-custom-gif').hide();
                                jQuery('.preloader ').hide();
                                jQuery('#wphrm_add_leave_applications_error').html(data.error);
                                jQuery('#wphrm_add_leave_applications_error').removeClass('display-hide');
                                jQuery("#wphrm_add_leave_applications_error").css('color', 'red');
                            }
                        }
                    });
                } else {
                    jQuery('.preloader-custom-gif').hide();
                    jQuery('.preloader ').hide();
                    jQuery('#wphrm_add_leave_applications_error').html('Entered date is less than today date');
                    jQuery('#wphrm_add_leave_applications_error').removeClass('display-hide');
                    return false;
                }
            } else {

                formData.append('action', 'WPHRMUserLeaveApplicationsInfo');
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
                            jQuery('#wphrm_add_leave_applications_success').removeClass('display-hide');
                            jQuery('#wphrm_leavetype').val("");
                            jQuery('#wphrm_leavedate').val("");
                            jQuery('#wphrm_reason').val("");
                            window.setTimeout(function () {
                                window.location.reload();
                            }, 1500);
                        } else {
                            jQuery('.preloader-custom-gif').hide();
                            jQuery('.preloader ').hide();
                            jQuery('#wphrm_add_leave_applications_error').html(data.error);
                            jQuery('#wphrm_add_leave_applications_error').removeClass('display-hide');
                            jQuery("#wphrm_add_leave_applications_error").css('color', 'red');
                        }
                    }
                });
            }
        }
    });

    /*finacial add modul*/
    var wphrm_financials_frm = jQuery("#wphrm_financials_frm");
    wphrm_financials_frm.validate({
        rules: {
            'wphrm-item': {
                required: true,
            }
            , 'wphrm-amount': {
                required: true,
                number: true,
            },
            'wphrm-status': {
                required: true,
            },
            'wphrm-financials-date': {
                required: true,
            }
        }, messages: {
            'wphrm-amount': {
                required: "Please enter amount.",
                number: "Please enter a valid amount."
            },
        },
        submitHandler: function (wphrm_financials_frm) {
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var formData = new FormData(jQuery(wphrm_financials_frm)['0']);
            formData.append('action', 'WPHRMFinancialsInfo');
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
                        jQuery('#wphrm_financials_success').removeClass('display-hide');
                        jQuery("#wphrm_item").val("");
                        jQuery("#wphrm_amount").val("");
                        jQuery("#wphrm_status").val("");
                        jQuery("#wphrm_financials_date").val("");
                        window.setTimeout(function () {
                            window.location.reload();
                        }, 1500);
                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#wphrm_financials_error').html(data.errors);
                        jQuery('#wphrm_financials_error').removeClass('display-hide');
                        jQuery("#wphrm_financials_error").css('color', 'red');
                    }
                }
            });
        }
    });

    /** Reset salary slip settings **/
    jQuery("#salary_slip_settings_reset").submit(function (e) {
        e.preventDefault();
        var wphrm_font_color = jQuery("#font_color").val();
        var wphrm_border_color = jQuery("#border_color").val();
        var wphrm_h1_color = jQuery("#h1_color").val();
        var wphrm_logo_align = jQuery("#logo_align").val();
        var wphrm_footer_align = jQuery("#footer_align").val();
        wphrm_data = {
            'action': 'WPHRMSalarySlipInfo',
            'wphrm_font_color': wphrm_font_color,
            'wphrm_border_color': wphrm_border_color,
            'wphrm_background_color': wphrm_h1_color,
            'wphrm_logo_align': wphrm_logo_align,
            'wphrm_footer_content_align': wphrm_footer_align,
        }
        jQuery('.preloader-custom-gif').show();
        jQuery('.preloader ').show();
        jQuery.post(ajaxurl, wphrm_data, function (response) {
            var data = JSON.parse(response);
            if (data.success) {
                jQuery('.preloader-custom-gif').hide();
                jQuery('.preloader ').hide();
                jQuery('#wphrmSalarySlipInfo_success').removeClass('display-hide');
                jQuery("#wphrmSalarySlipInfo_success").html("<i class='fa fa-check-square' aria-hidden='true'></i> Salary Slip Details Successfully Reset");
                jQuery("#wphrmSalarySlipInfo_success").css('color', 'green');
                jQuery("#wphrm_currency_decimal").val(2);
                jQuery("html, body").animate({scrollTop: 0}, "slow");
                window.setTimeout(function () {
                    window.location.reload();
                }, 1500);
            } else {
                jQuery('.preloader-custom-gif').hide();
                jQuery('.preloader ').hide();
                jQuery('#wphrmSalarySlipInfo_error').html(data.errors);
                jQuery("#wphrmSalarySlipInfo_error").css('color', 'red');
            }
        });
    });

    /* Delete  Salary generated slips    */
    jQuery("#frm_salary_delete").submit(function (e) {
        e.preventDefault();
        var wphrm_employeeOther_id = jQuery("#employeedelete_id").val();
        var wphrm_generate_month = jQuery("#generate_month_delete").val();
        var wphrm_generate_year = jQuery("#generate_year_delete").val();
        wphrm_data = {
            'action': 'WPHRMRemoveSalarySlip',
            'wphrm_employeeOther_id': wphrm_employeeOther_id,
            'wphrm_generate_year': wphrm_generate_year,
            'wphrm_generate_month': wphrm_generate_month,
        }
        jQuery.post(ajaxurl, wphrm_data, function (response) {
            var data = JSON.parse(response);
            if (data.success) {
                location.href = '?page=wphrm-select-financials-month&employee_id=' + wphrm_employeeOther_id;
            }
        });
    });

    /* Delete  Salary generated slips    */
    jQuery("#frm_salary_week_delete").submit(function (e) {
        e.preventDefault();
        var wphrm_employeeOther_id = jQuery("#employeedelete_id").val();
        var wphrm_generate_month = jQuery("#generate_month_delete").val();
        var wphrm_generate_year = jQuery("#generate_year_delete").val();
        var wphrmCreateWeekSalaryWeekNo = jQuery("#wphrm-create-week-salary-week-no").val();
        wphrm_data = {
            'action': 'WPHRMRemoveSalaryWeekSlip',
            'wphrm_employeeOther_id': wphrm_employeeOther_id,
            'wphrm_generate_year': wphrm_generate_year,
            'wphrm_generate_month': wphrm_generate_month,
            'wphrmCreateWeekSalaryWeekNo': wphrmCreateWeekSalaryWeekNo,
        }
        jQuery.post(ajaxurl, wphrm_data, function (response) {
            var data = JSON.parse(response);
            if (data.success) {
                location.href = '?page=wphrm-select-financials-week&employee_id=' + wphrm_employeeOther_id;
            }
        });
    });

    /* For Salary generate */
    jQuery("#frm_salary_generate").submit(function (e) {
        e.preventDefault();
        var wphrm_information = jQuery("#information").val();
        var wphrm_emp_generate_id = jQuery("#emp_generate_id").val();
        var wphrm_generate_month = jQuery("#generate_month").val();
        var wphrm_generate_year = jQuery("#generate_year").val();
        var wphrm_dayofworking = jQuery("#dayofworking").val();
        var wphrm_workleave = jQuery("#workleave").val();
        var wphrm_dayofworked = jQuery("#dayofworked").val();
        var wphrm_Hours = jQuery("#wphrm_Hours").val();
        var wphrm_account_no = jQuery("#wphrm_account_no").val();
        var wphrmEarningLebal = [];
        jQuery("input[name='wphrm-earning-lebal[]']").each(function () {
            wphrmEarningLebal.push(jQuery(this).val());
        });
        var wphrmEarningValue = [];
        jQuery("input[name='wphrm-earning-value[]']").each(function () {
            wphrmEarningValue.push(jQuery(this).val());
        });
        var wphrmDeductionLebal = [];
        jQuery("input[name='wphrm-deduction-lebal[]']").each(function () {
            wphrmDeductionLebal.push(jQuery(this).val());
        });
        var wphrmDeductionValue = [];
        jQuery("input[name='wphrm-deduction-value[]']").each(function () {
            wphrmDeductionValue.push(jQuery(this).val());
        });
        wphrm_data = {
            'action': 'WPHRMGenerateSalary',
            'wphrm_information': wphrm_information,
            'wphrm_emp_generate_id': wphrm_emp_generate_id,
            'wphrm_generate_year': wphrm_generate_year,
            'wphrm_generate_month': wphrm_generate_month,
            'wphrm_dayofworking': wphrm_dayofworking,
            'wphrm_workleave': wphrm_workleave,
            'wphrm_dayofworked': wphrm_dayofworked,
            'wphrm_Hours': wphrm_Hours,
            'wphrm_account_no': wphrm_account_no,
            'wphrmEarningLebal': wphrmEarningLebal,
            'wphrmEarningValue': wphrmEarningValue,
            'wphrmDeductionLebal': wphrmDeductionLebal,
            'wphrmDeductionValue': wphrmDeductionValue,
        }
        jQuery('.preloader-custom-gif').show();
        jQuery('.preloader ').show();
        jQuery.post(ajaxurl, wphrm_data, function (response) {
            var data = JSON.parse(response);
            if (data.success) {
                jQuery('.preloader-custom-gif').hide();
                jQuery('.preloader ').hide();
                jQuery('#wphrmGenerateSalary_success').removeClass('display-hide');
                jQuery("html, body").animate({scrollTop: 0}, "slow");
                window.setTimeout(function () {
                    var ajax_url = ajaxurl + '?page=wphrm-select-financials-month&employee_id=' + wphrm_emp_generate_id;
                    var url = ajax_url.replace('/admin-ajax', '/admin');
                    window.location.href = url;

                }, 500);
            }
        });
    });

    /* For Salary for week generate */
    jQuery("#frm_salary_week_generate").submit(function (e) {
        e.preventDefault();
        var wphrm_information = jQuery("#information").val();
        var wphrm_emp_generate_id = jQuery("#emp_generate_id").val();
        var wphrm_generate_week_no = jQuery("#wphrm-create-week-salary-week-no").val();
        var wphrm_generate_month = jQuery("#generate_month").val();
        var wphrm_generate_year = jQuery("#generate_year").val();
        var wphrm_dayofworking = jQuery("#dayofworking").val();
        var wphrm_workleave = jQuery("#workleave").val();
        var wphrm_dayofworked = jQuery("#dayofworked").val();
        var wphrm_Hours = jQuery("#wphrm_Hours").val();
        var wphrm_account_no = jQuery("#wphrm_account_no").val();
        var wphrmEarningLebal = [];
        jQuery("input[name='wphrm-earning-lebal[]']").each(function () {
            wphrmEarningLebal.push(jQuery(this).val());
        });
        var wphrmEarningValue = [];
        jQuery("input[name='wphrm-earning-value[]']").each(function () {
            wphrmEarningValue.push(jQuery(this).val());
        });
        var wphrmDeductionLebal = [];
        jQuery("input[name='wphrm-deduction-lebal[]']").each(function () {
            wphrmDeductionLebal.push(jQuery(this).val());
        });
        var wphrmDeductionValue = [];
        jQuery("input[name='wphrm-deduction-value[]']").each(function () {
            wphrmDeductionValue.push(jQuery(this).val());
        });
        wphrm_data = {
            'action': 'WPHRMGenerateWeekSalary',
            'wphrm_information': wphrm_information,
            'wphrm_emp_generate_id': wphrm_emp_generate_id,
            'wphrm_generate_week_no': wphrm_generate_week_no,
            'wphrm_generate_year': wphrm_generate_year,
            'wphrm_generate_month': wphrm_generate_month,
            'wphrm_dayofworking': wphrm_dayofworking,
            'wphrm_workleave': wphrm_workleave,
            'wphrm_dayofworked': wphrm_dayofworked,
            'wphrm_Hours': wphrm_Hours,
            'wphrm_account_no': wphrm_account_no,
            'wphrmEarningLebal': wphrmEarningLebal,
            'wphrmEarningValue': wphrmEarningValue,
            'wphrmDeductionLebal': wphrmDeductionLebal,
            'wphrmDeductionValue': wphrmDeductionValue,
        }
        jQuery('.preloader-custom-gif').show();
        jQuery('.preloader ').show();
        jQuery.post(ajaxurl, wphrm_data, function (response) {
            var data = JSON.parse(response);
            if (data.success) {
                jQuery('.preloader-custom-gif').hide();
                jQuery('.preloader ').hide();
                jQuery('#wphrmGenerateSalary_success').removeClass('display-hide');
                jQuery("html, body").animate({scrollTop: 0}, "slow");
                window.setTimeout(function () {
                    var ajax_url = ajaxurl + '?page=wphrm-select-financials-week&employee_id=' + wphrm_emp_generate_id;
                    var url = ajax_url.replace('/admin-ajax', '/admin');
                    window.location.href = url;

                }, 500);
            }
        });
    });

    /* For search  by datepicker */
    jQuery(document).on("input", ".validationonnumber", function () {
        this.value = this.value.replace(/[^0-9\.]/g, '');

    });

    /* For search  by datepicker */
    /* For date picker */
    jQuery('.date-picker').datepicker({
        dateFormat: 'yyyy-mm-dd',
        todayHighlight: true,
        autoclose: true
    });


    /* For vehicle Checked */
    if (jQuery('#wphrm_employee_vehicle').is(":checked")) {
        jQuery("#wphrm_vehicle_details").toggle('open');
    }
    jQuery("#wphrm_employee_vehicle").click(function () {
        if (jQuery(this).is(":checked")) {
            jQuery("#wphrm_vehicle_details").toggle('open');
        } else {
            jQuery("#wphrm_vehicle_details").toggle('hide');
        }
    });
    jQuery("#wphrm_employee_vehicle").on('ifChanged', function(event){
        if (jQuery(this).is(":checked")) {
            jQuery("#wphrm_vehicle_details").toggle('open');
        } else {
            jQuery("#wphrm_vehicle_details").toggle('hide');
        }
    });

    /** T-Shirt Size **/
    jQuery("#tshirt_info_toggle").hover(function () {
        jQuery("#wphrm_tshirt_size_info_popup").fadeToggle();
    }, function () {
        jQuery("#wphrm_tshirt_size_info_popup").fadeToggle();
    });
    jQuery('#employee_pofile_img').on('click', function () {
        jQuery('#employee_profile').click();
    });

    jQuery("#wphrm_leavedate").change(function () {
        jQuery("#wphrm_leavedate_to").val(jQuery("#wphrm_leavedate").val());
    });

    jQuery("#mark-attendance-date").change(function () {
        var attendanceDate = jQuery('#mark-attendance-date').val();
        var ajax_url = ajaxurl + '?page=wphrm-mark-attendance&status=edit&attendancedate=' + attendanceDate;
        var url = ajax_url.replace('/admin-ajax', '/admin');
        window.location.href = url;
    });

    TableManaged.init();
});





// Get Designation
function getDesignation(departmentID, designationID) {
    jQuery('#wphrm_employee_designation option:not(:first)').remove();
    if (departmentID != '') {
        jQuery.ajax({
            method: "POST",
            url: ajaxurl,
            data: {
                'id': departmentID, 'action': 'WPHRMDesignationAjax'
            },
            dataType: 'json',
            success: function (data) {
                jQuery(data.details).each(function (key, item) {
                    var selectval = '';
                    if (item.id == designationID) {
                        selectval = "selected='selected'";
                        jQuery('#wphrm_employee_designation').append(jQuery('<option>', {
                            value: item.id,
                            text: item.name,
                            selected: selectval
                        }));
                    } else {
                        jQuery('#wphrm_employee_designation').append(jQuery('<option>', {
                            value: item.id,
                            text: item.name,
                        }));
                    }
                });
            }
        });
    }
}

/*for edit department modul*/
function departmentEdit(id, department_name) {
    jQuery("#editdepartment_name").val(department_name);
    var wphrm_edit_department = jQuery(".wphrm_edit_department");
    wphrm_edit_department.validate({
        rules: {
            editdepartment_name: "required",
        },
        submitHandler: function (wphrm_edit_department) {
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var formData = new FormData(jQuery(wphrm_edit_department)['0']);
            formData.append('action', 'WPHRMDepartmentInfo');
            formData.append('wphrm_department_id', id);
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
                        jQuery('.preloader-custom-gif').show();
                        jQuery('.preloader ').show();
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#wphrm_Edepartment_info_success').removeClass('display-hide');
                        window.setTimeout(function () {
                            window.location.reload();
                        }, 500);
                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#wphrm_Edepartment_info_error').html(data.errors);
                        jQuery('#wphrm_Edepartment_info_error').removeClass('display-hide');
                        jQuery("#wphrm_Edepartment_info_error").css('color', 'red');
                    }
                }
            });
        }
    });
}

function CloseSalarySlip() {
    jQuery("#wphrmyearchoose").hide();
}
function wphrmyesDuplicate() {
    jQuery("#wphrmyearchoose").show();
}

/*for edit designation modul*/
function designationEdit(id, designation_name) {
    jQuery("#editdesignation").val(designation_name);
    var wphrm_edit_designation = jQuery(".wphrm_edit_designation");
    wphrm_edit_designation.validate({
        rules: {
            editdepartment_name: "required",
        },
        submitHandler: function (wphrm_edit_designation) {
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var formData = new FormData(jQuery(wphrm_edit_designation)['0']);
            formData.append('action', 'WPHRMDesignationInfo');
            formData.append('wphrm_designation_id', id);
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
                        jQuery('#wphrm_Edesignation_info_success').removeClass('display-hide');
                        window.setTimeout(function () {
                            window.location.reload();
                        }, 500);
                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#wphrm_Edesignation_info_error').html(data.errors);
                        jQuery('#wphrm_Edesignation_info_error').removeClass('display-hide');
                        jQuery("#wphrm_Edesignation_info_error").css('color', 'red');
                    }
                }
            });
        }
    });
}

/* for edit leave type modul */
function leavetypeEdit(id, leavetype, period, no_of_leave, require_attachment, notice_period, leave_rule, leave_desc) {
    jQuery("#edit_leaveType").val(leavetype);
    jQuery("#edit_num_of_leave").val(no_of_leave);
    jQuery("#edit_wphrm_period").val(period);
    jQuery("#edit_leave_file_attachment").prop('checked', require_attachment == 1 ? true : false);
    jQuery("#edit_notice_period").val(notice_period);
    jQuery("#edit_leave_description").val(leave_desc);
    if( leave_rule == 0 ) {
        jQuery("#edit_leave_rules").val('');
    } else {
        jQuery("#edit_leave_rules").val(leave_rule);
    }
    
    if( leavetype == 'Annual Leave' || leavetype == 'Annual leave' || leavetype == 'annual leave' || leavetype == 'Annual' || leavetype == 'annual' ) {
        jQuery('#wphrm_edit_leavetype_form #edit_num_of_leave').parent().parent().hide();
    } else {
        jQuery('#wphrm_edit_leavetype_form #edit_num_of_leave').parent().parent().show();
    }
    
    var wphrm_edit_leavetype_form = jQuery("#wphrm_edit_leavetype_form");
    wphrm_edit_leavetype_form.validate({
        rules: {
            leaveType: "required",
            numberOfLeave: {
                min: 1,
                required: true
            },
        },
        submitHandler: function (wphrm_edit_leavetype_form) {
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var formData = new FormData(jQuery(wphrm_edit_leavetype_form)['0']);
            formData.append('action', 'WPHRMLeavetypeInfo');
            formData.append('wphrm_leavetype_id', id);
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
                        jQuery('#wphrm_edit_leavetype_success').removeClass('display-hide');

                        window.setTimeout(function () {
                            window.location.reload();
                        }, 500);
                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#wphrm_edit_leavetype_error').html(data.errors);
                        jQuery('#wphrm_edit_leavetype_error').removeClass('display-hide');
                        jQuery("#wphrm_edit_leavetype_error").css('color', 'red');
                    }
                }
            });
        }
    });
}

/** for Finacials Edit modul **/
function finacialsEdit(id, item, amount, date, status) {
    jQuery("#finacials_id").val(id);
    jQuery("#wphrm_eitem").val(item);
    jQuery("#wphrm_eamount").val(amount);
    jQuery("#wphrm_efinancials_date").val(date);
    jQuery("#wphrm_estatus").val(status);
    var wphrm_edit_financials_frm = jQuery("#wphrm_edit_financials_frm");
    wphrm_edit_financials_frm.validate({
        rules: {
            'wphrm-item': {
                required: true,
            }
            , 'wphrm-amount': {
                required: true,
                number: true,
            },
            'wphrm-status': {
                required: true,
            },
            'wphrm-financials-date': {
                required: true,
            }
        }, messages: {
            'wphrm-amount': {
                required: "Please enter amount.",
                number: "Please enter a valid amount."
            },
        },
        submitHandler: function (wphrm_edit_financials_frm) {
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var formData = new FormData(jQuery(wphrm_edit_financials_frm)['0']);
            formData.append('action', 'WPHRMFinancialsInfo');
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
                        jQuery('#wphrm_edit_financials_success').removeClass('display-hide');
                        window.setTimeout(function () {
                            window.location.reload();
                        }, 500);
                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#wphrm_edit_financials_error').html(data.errors);
                        jQuery('#wphrm_edit_financials_error').removeClass('display-hide');
                        jQuery("#wphrm_edit_financials_error").css('color', 'red');
                    }
                }
            });
        }
    });
}



/** for edit leave Applications  modul **/
function applicationEdit(id, employeeID, name, date, dateto, leave_type, halfday, reason, appliedon, applicationStatus, remaining_leave, reimbursement, due_date, maternity_option, child_bdate, work_date, outstation_date, assign_country, examination_name, examination_date, examination_time, attendanceFileUrl) {
    var myarray = halfday.split(",");
    jQuery("#application_name").val(name);
    jQuery(".application_leavedate").val(date);
    jQuery(".application_leavedate_to").val(dateto);
    jQuery(".wphrm-from-leve-day-type").val(myarray[0]);
    jQuery(".wphrm-to-leve-day-type").val(myarray[1]);
    jQuery(".wphrm-to-leve-day-type").val(myarray[1]);
    jQuery("#wphrm_child_bdate_admin").val(child_bdate);
    jQuery("#wphrm_work_date_admin").val(work_date);
    jQuery("#wphrm_outstation_date_admin").val(outstation_date);
    jQuery("#wphrm_assign_country_admin").val(assign_country);
    jQuery("#wphrm_examination_name_admin").val(examination_name);
    jQuery("#wphrm_examination_date_admin").val(examination_date);
    jQuery("#wphrm_examination_time_admin").val(examination_time);
    jQuery("#wphrm_leave_balance").val(remaining_leave);
    jQuery("#wphrm_reimbursement_amount").val(reimbursement);
    
    if( due_date == '' ) {
        jQuery('#wphrm_leave_applications_frm .maternity-holder').addClass('hide');
    } else {
        jQuery('#wphrm_leave_applications_frm .maternity-holder').removeClass('hide');
        
        var due = due_date.split('-');
        var startdate = new Date(
        parseInt(
            due[2], 10),
            parseInt(due[1], 10) - 1,
            parseInt(due[0], 10)
        );
        startdate.setDate(startdate.getDate() - 28);
        var new_date = ("0" + startdate.getDate()).slice(-2) + "-" + ("0" + (startdate.getMonth() + 1)).slice(-2) + "-" + startdate.getFullYear();
        var day = ((parseInt(due[0]) + 3) > 10) ? (parseInt(due[0]) + 3) : '0' + (parseInt(due[0]) + 3);
        var ndate = new_date.split('-');
        
        //for default option
        var leave_limit = 0;
        if( maternity_option == 'mutual' ) {
            leave_limit = 48;
        } else {
            leave_limit = 96;
        }
        var dmy = due_date.split("-");
        var joindate = new Date(
        parseInt(
            dmy[2], 10),
            parseInt(dmy[1], 10) - 1,
            parseInt(dmy[0], 10)
        );
        joindate.setDate(joindate.getDate() + parseInt(leave_limit) + 6);
        var ending_date = ("0" + joindate.getDate()).slice(-2) + "-" + ("0" + (joindate.getMonth() + 1)).slice(-2) + "-" + joindate.getFullYear();
        var end_date = ending_date.split('-');
        
        //for mutual option
        var mutualdate = new Date(
        parseInt(
            dmy[2], 10),
            parseInt(dmy[1], 10) - 1,
            parseInt(dmy[0], 10)
        );
        mutualdate.setDate(mutualdate.getDate() + 54);
        var mutual_date = ("0" + mutualdate.getDate()).slice(-2) + "-" + ("0" + (mutualdate.getMonth() + 1)).slice(-2) + "-" + mutualdate.getFullYear();
        var mutual = mutual_date.split('-');
        
        var month = {
            '1' : 'January',
            '2' : 'February',
            '3' : 'March',
            '4' : 'April',
            '5' : 'May',
            '6' : 'June',
            '7' : 'July',
            '8' : 'August',
            '9' : 'September',
            '10' : 'October',
            '11' : 'November',
            '12' : 'December'
        };
        
        var default_option = 'Starting from ' + ndate[0] + ' ' + month[parseInt(ndate[1])] + ' ' + ndate[2] + ' to ' + end_date[0] + ' ' + month[parseInt(end_date[1])] + ' ' + end_date[2];
        var mutual_option = 'First 8 weeks from ' + ndate[0] + ' ' + month[parseInt(ndate[1])] + ' ' + ndate[2] + ' to ' + mutual[0] + ' ' + month[parseInt(mutual[1])] + ' ' + mutual[2];
        
        if( maternity_option == 'mutual' ) {
            jQuery('.wphrm_maternity_option_admin').html(mutual_option);
        } else if( maternity_option == 'default' ) {
            jQuery('.wphrm_maternity_option_admin').html(default_option);
        } else {
            jQuery('.wphrm_maternity_option_admin').html('');
        }
        
        var due = due_date.split('-');
        var dueDate = month[parseInt(due[1])] + ' ' + due[0] + ', ' + due[2];
        jQuery("#wphrm_due_date_admin").val(dueDate);
    }
    if( reimbursement == 0 ) {
        jQuery('#wphrm_leave_applications_frm .medical-holder').addClass('hide');
    } else {
        jQuery('#wphrm_leave_applications_frm .medical-holder').removeClass('hide');
    }
    if( child_bdate == '' ) {
        jQuery('#wphrm_leave_applications_frm .paternity-holder').addClass('hide');
    } else {
        jQuery('#wphrm_leave_applications_frm .paternity-holder').removeClass('hide');
    }
    if( work_date == '' ) {
        jQuery('#wphrm_leave_applications_frm .lieu-holder').addClass('hide');
    } else {
        jQuery('#wphrm_leave_applications_frm .lieu-holder').removeClass('hide');
    }
    if( outstation_date == '' ) {
        jQuery('#wphrm_leave_applications_frm .outstation-holder').addClass('hide');
    } else {
        jQuery('#wphrm_leave_applications_frm .outstation-holder').removeClass('hide');
    }
    if( examination_date == '' ) {
        jQuery('#wphrm_leave_applications_frm .examination-holder').addClass('hide');
    } else {
        jQuery('#wphrm_leave_applications_frm .examination-holder').removeClass('hide');
    }
    
    if (myarray[0] == '1') {
        jQuery(".wphrm-from").val('Half-day');
    }else{
        jQuery(".wphrm-from").val('Full-day');
    }
    if (myarray[1] == '1') {
        jQuery(".wphrm-to").val('Half-day');
    }else{
        jQuery(".wphrm-to").val('Full-day');
    }

    var formatted_leave_type = leave_type.split(':');
    jQuery(".application_leavetype.leave-label").val(formatted_leave_type[1]);
    jQuery(".application_leavetype.leave-value").val(formatted_leave_type[0]);
    jQuery(".application_reason").val(reason);
    jQuery(".application_appliedon").val(appliedon);
    jQuery("#applicationStatus").val(applicationStatus);

    if(date == dateto){
        jQuery('#wphrm_leave_applications_frm .application_leavedate_to').closest('.form-group').addClass('hide');
    }else{
        jQuery('#wphrm_leave_applications_frm .application_leavedate_to').closest('.form-group').removeClass('hide');
    }

    /*J@F*/
    if(attendanceFileUrl){
        var file_name = attendanceFileUrl.split('/').pop();
        $anchor_tag = jQuery('<a></a>').attr('target', '_blank').attr('href', attendanceFileUrl).html(file_name);
        $li_tag = jQuery('<li></li>').append($anchor_tag);
        jQuery("#leave_application_attachment").html($li_tag);
    }else{
        $li_tag = jQuery('<li></li>').append('No Attachment');
        jQuery("#leave_application_attachment").html($li_tag);
    }
    /*J@F END*/

    var wphrm_leave_applications_frm = jQuery("#wphrm_leave_applications_frm");
    wphrm_leave_applications_frm.validate({
        rules: {},
        submitHandler: function (wphrm_leave_applications_frm) {
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            /*var formData = new FormData(jQuery(wphrm_leave_applications_frm)['0']);
            formData.append('action', 'WPHRMLeaveApplicationsInfo');
            formData.append('wphrm_leave_application_id', id);
            formData.append('wphrm_employeeID', employeeID);
            formData.append('edit_type', 'approval');
            jQuery.ajax({
                method: "POST",
                url: ajaxurl,
                contentType: false,
                cache: false,
                processData: false,
                data: formData,
                success: function (output) {
                    alert(output);
                    var data = JSON.parse(output);
                    if (data.success) {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#wphrm_edit_application_success').removeClass('display-hide');
                        window.setTimeout(function () {
                            window.location.reload();
                        }, 500);
                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#wphrm_edit_application_error').html(data.errors);
                        jQuery('#wphrm_edit_application_error').removeClass('display-hide');
                        jQuery("#wphrm_edit_application_error").css('color', 'red');
                    }
                }
            });*/

            jQuery(wphrm_leave_applications_frm).ajaxSubmit({
                url: ajaxurl,
                dataType: 'html',
                type: 'POST',
                data: {
                    action: 'WPHRMLeaveApplicationsInfo',
                    wphrm_leave_application_id: id,
                    wphrm_employeeID: employeeID,
                    edit_type: 'approval'
                },
                success: function(output){
                    var data = JSON.parse(output);
                    if (data.success) {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#wphrm_edit_application_success').removeClass('display-hide');
                        window.setTimeout(function () {
                            window.location.reload();
                        }, 500);
                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#wphrm_edit_application_error').html(data.errors);
                        jQuery('#wphrm_edit_application_error').removeClass('display-hide');
                        jQuery("#wphrm_edit_application_error").css('color', 'red');
                    }
                }
            });
            return false;
        }
    });
}

/** for edit leave Applications  modul **/
function user_staticEdit(id, date, todate, leave_type, leavedaytype, reason, attachment_file ) {
    var halfday_type = leavedaytype.split(",");
    console.log(halfday_type);

    jQuery(".fromDateBetweentype").bootstrapSwitch('state', halfday_type[0] == '1' ? false : true );
    jQuery(".toDateBetweentype").bootstrapSwitch('state', halfday_type[1] == '1' ? false : true );

    jQuery("#wphrm_leavedate").val(date);
    jQuery("#wphrm_leavedate_to").val( todate );
    jQuery(".wphrm_leavetype").val(leave_type);
    jQuery("#wphrm_reason").val(reason);
    jQuery("#wphrm_attendanceID").val(id);
    jQuery("#attendance-document").closest('.fileinput').find('.fileinput-filename').text(attachment_file);
    var wphrm_user_leave_applications_frm = jQuery("#wphrm_user_leave_applications_frm");
    wphrm_user_leave_applications_frm.validate({
        wphrm_leavetype: {
            required: true,
        }
        , wphrm_leavedate: {
            required: true,
        },
        wphrm_leavedate_to: {
            required: true,
        },
        wphrm_reason: {
            required: true,
        },
        submitHandler: function (wphrm_user_leave_applications_frm) {
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var formData = new FormData(jQuery(wphrm_user_leave_applications_frm)['0']);
            formData.append('action', 'WPHRMUserLeaveApplicationsInfo');
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
                        jQuery('#wphrm_add_leave_applications_success').removeClass('display-hide');
                        window.setTimeout(function () {
                            window.location.reload();
                        }, 500);
                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#wphrm_add_leave_applications_error').html(data.errors);
                        jQuery('#wphrm_add_leave_applications_error').removeClass('display-hide');
                        jQuery("#wphrm_add_leave_applications_error").css('color', 'red');
                    }
                }
            });
        }
    });
}

/** for edit Massages  modul **/
function edit_messages(id, title, desc) {
    jQuery("#wphrm_messages_id").val(id);
    jQuery("#wphrm_messages_title").val(title);
    jQuery("#wphrm_messages_desc").val(desc);
    var wphrmAllMessagesInfo_form = jQuery("#wphrmAllMessagesInfo_form");
    wphrmAllMessagesInfo_form.validate({
        wphrm_messages_title: {
            required: true,
        }
        , wphrm_messages_desc: {
            required: true,
        },
        submitHandler: function (wphrmAllMessagesInfo_form) {
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var formData = new FormData(jQuery(wphrmAllMessagesInfo_form)['0']);
            formData.append('action', 'WPHRMAllMessagesInfo');
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
                        jQuery('#wphrmAllMessagesInfo_success').removeClass('display-hide');
                        window.setTimeout(function () {
                            window.location.reload();
                        }, 500);
                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#wphrmAllMessagesInfo_error').removeClass('display-hide');
                        jQuery("#wphrmAllMessagesInfo_error").css('color', 'red');
                    }
                }
            });
        }
    });
}

/** wphrm Months **/
function wphrm_month(val, key) {
    var dataId = key;
    jQuery('.month-data').val(dataId);
    jQuery(".month_holidays").empty();
    jQuery('.preloader-custom-gif').show();
    jQuery('.preloader ').show();
    var wphrm_year = jQuery(".yeardata").val();
    wphrm_holiday_data = {
        'action': 'WPHRMHolidayMonthWise',
        'holiday_month': val,
        'wphrm_year': wphrm_year,
    }
    jQuery.post(ajaxurl, wphrm_holiday_data, function (response) {
        var data = JSON.parse(response);
        if (data != '') {
            jQuery('.preloader-custom-gif').hide();
            jQuery('.preloader ').hide();
            jQuery(".month_holidays").html(data.wphrmHolidayMonth);
        } else {
            jQuery('.preloader-custom-gif').hide();
            jQuery('.preloader ').hide();
            jQuery(".month_holidays").html("No data Found.");
        }
    });
}

/* for costom delete js */
function WPHRMCustomDelete(id, tablename, filed_name) {
    jQuery('#deleteModal').appendTo("body").modal('show');
    jQuery('#info').html(WPHRMCustomJS.Deletemsg);
    jQuery("#delete").click(function () {
        jQuery('.preloader-custom-gif').show();
        jQuery('.preloader ').show();
        wphrm_holiday_data = {
            'action': 'WPHRMCustomDelete',
            'WPHRMCustomDelete_id': id,
            'table_name': '' + tablename + '',
            'filed_name': '' + filed_name + ''
        }
        jQuery.post(ajaxurl, wphrm_holiday_data, function (response) {
            var data = JSON.parse(response);
            if (data.success) {
                jQuery('.preloader-custom-gif').hide();
                jQuery('.preloader ').hide();
                jQuery('#WPHRMCustomDelete_success').removeClass('display-hide');
                window.location.reload();
            } else {
                jQuery('#WPHRMCustomDelete_error').html(data.errors);
                jQuery('#WPHRMCustomDelete_error').removeClass('display-hide');
                jQuery('.preloader-custom-gif').hide();
                jQuery('.preloader ').hide();
            }
        });
    })
}

/* for change month */
function changeMonthYear() {
    var month = jQuery("#monthSelect").val();
    var year = jQuery("#yearSelect").val();
    jQuery('#Attendance_Calendar').fullCalendar('gotoDate', year + '-' + month + '-01');
    showReport();
}

/* for attendance Reports */
function showReport() {
    var month = jQuery("#monthSelect").val();
    var year = jQuery("#yearSelect").val();
    var employeeID = jQuery("#employee_id").val();
    wphrm_attendancereport_data = {
        'action': 'WPHRMEmployeeAttendanceReports',
        'employee_id': employeeID,
        'month': month,
        'year': year,
    }
    jQuery.post(ajaxurl, wphrm_attendancereport_data, function (data) {
        console.log(data);
        var response = JSON.parse(data);
        if (response.success == "success") {
            jQuery('#attendanceReport').html(response.Working);
            jQuery('#attendancePerReport').html(response.PerReport);
            jQuery('#attendanceworkingday').html(response.workingDays);
            jQuery('#attendanceAbsent').html(response.absent);
            jQuery('#attendancePersent').html(response.present);
            jQuery('#attendanceLeave').html(response.leaves);
            // jQuery('#attendancehalfdays').html(response.halfdays);
        }
    });
}

/* for remove salary earning and deduction details  */
function earning_remove(data_label, data_value, data_id, g_month, g_year) {
    var wphrm_emp_label = data_label;
    var wphrm_emp_value = data_value;
    var wphrm_employeeOther_id = data_id;
    var wphrm_generate_month = g_month;
    var wphrm_generate_year = g_year;
    function wphrm_earning_remove_validate() {
        var valid = true;
        if (wphrm_emp_label == "") {
            valid = false;
        }
        if (wphrm_employeeOther_id == "") {
            valid = false;
        }
        if (wphrm_emp_value == "") {
            valid = false;
        }
        return valid;
    }
    wphrm_data = {
        'action': 'WPHRMRemoveEarning',
        'wphrm_employeeOther_id': wphrm_employeeOther_id,
        'wphrm_emp_label': wphrm_emp_label,
        'wphrm_emp_value': wphrm_emp_value,
        'wphrm_generate_month': wphrm_generate_month,
        'wphrm_generate_year': wphrm_generate_year,
    }
    if (wphrm_earning_remove_validate()) {
        jQuery.post(ajaxurl, wphrm_data, function (response) {
            var data = JSON.parse(response);
            if (data.success) {
                window.location.reload();
            }
        });
    }
}

/* for remove Satting earning and deduction details  */
function deleteEarningAndDedutions(datalabel) {
    jQuery('.removefiled' + datalabel).remove();
    calculateEarningSum();
    calculateDeductionSum();
    calculateNetTotal();
}

/* for redirect page*/
function redirect_to() {
    var employee = jQuery('#changeEmployee').val();
    var ajax_url = ajaxurl + '?page=wphrm-view-attendance&employee_id=' + employee;
    var url = ajax_url.replace('/admin-ajax', '/admin');
    window.location.href = url;
}
function createSalary(wphrmMonth) {
    jQuery('form#wphrm-create-salary > input[name=wphrm-create-salary-month]').val(wphrmMonth);
    jQuery('form[name=wphrm-create-salary]').submit();
}
function createWeekSalary(wphrmMonth,weekNo) {
    jQuery('form#wphrm-create-week-salary > input[name=wphrm-create-week-salary-month]').val(wphrmMonth);
    jQuery('form#wphrm-create-week-salary > input[name=wphrm-create-week-salary-week-no]').val(weekNo);
    jQuery('form[name=wphrm-create-week-salary]').submit();
}
function redirecttosalary() {
    jQuery("#wphrmyearchoose").hide();
    jQuery('form[name=wphrm-create-salary]').submit();
}
function redirecttoWeeksalary() {
    jQuery("#wphrmyearchoose").hide();
    jQuery('form[name=wphrm-create-week-salary]').submit();
}

function wphrmDuplicate(wphrmMonth) {
    jQuery('form#wphrm-create-salary > input[name=wphrm-create-salary-month]').val(wphrmMonth);
    jQuery("form#wphrm-duplicate-salary > input[name='wphrm-create-salary-month']").val(wphrmMonth);
}

function wphrmDuplicateWeek(wphrmMonth,weekNo) {
    jQuery('form#wphrm-create-week-salary > input[name=wphrm-create-week-salary-month]').val(wphrmMonth);
    jQuery('form#wphrm-create-week-salary > input[name=wphrm-create-week-salary-week-no]').val(weekNo);
    jQuery("form#wphrm-duplicate-week-salary > input[name='wphrm-create-week-salary-month']").val(wphrmMonth);
    jQuery("form#wphrm-duplicate-week-salary > input[name='wphrm-create-week-salary-week-no']").val(weekNo);
}

function CreateDuplicateWeekSalarySlip() {
    var wphrmDuplicateToYear = jQuery("#wphrm-duplicate-to-salary-year option:selected").val();
    var wphrmDuplicateToMonth = jQuery("#wphrm-duplicate-to-salary-month option:selected").val();
    var wphrmDuplicateToWeek = jQuery("#wphrm-duplicate-to-salary-week-no option:selected").val();
    if (wphrmDuplicateToYear != '' && wphrmDuplicateToYear != 'null') {
        jQuery("#wphrm-duplicate-to-salary-year").css('border', 'none');
        if (wphrmDuplicateToMonth != '' && wphrmDuplicateToMonth != 'null' && wphrmDuplicateToYear != '' && wphrmDuplicateToYear != 'null') {
            jQuery("#wphrm-duplicate-to-salary-month").css('border', 'none');
            jQuery("#wphrm-duplicate-to-salary-week-no").css('border', 'none');
            jQuery("form[name='wphrm-duplicate-week-salary']").submit();
        } else {
            jQuery("#wphrm-duplicate-to-salary-month").css('border', '1px solid red');
            jQuery("#wphrm-duplicate-to-salary-week-no").css('border', '1px solid red');
        }
    } else {
        jQuery("#wphrm-duplicate-to-salary-year").css('border', '1px solid red');
    }
}

function CreateDuplicateSalarySlip() {
    var wphrmDuplicateToYear = jQuery("#wphrm-duplicate-to-salary-week-year option:selected").val();
    var wphrmDuplicateToMonth = jQuery("#wphrm-duplicate-to-salary-week-month option:selected").val();
    if (wphrmDuplicateToYear != '' && wphrmDuplicateToYear != 'null') {
        jQuery("#wphrm-duplicate-to-salary-week-year").css('border', 'none');
        if (wphrmDuplicateToMonth != '' && wphrmDuplicateToMonth != 'null') {
            jQuery("#wphrm-duplicate-to-salary-week-month").css('border', 'none');
            jQuery("form[name='wphrm-duplicate-salary']").submit();
        } else {
            jQuery("#wphrm-duplicate-to-salary-week-month").css('border', '1px solid red');
        }
    } else {
        jQuery("#wphrm-duplicate-to-salary-week-year").css('border', '1px solid red');
    }
}
function wphrm_pdf_generator(page, employeeID, month) {
    var link = '?page=' + page + '&employee_id=' + employeeID + '&month=' + month + '&years=';
    var year = jQuery('.yeardata').val();
    location.href = link + year;
}

function wphrm_pdf_week_generator(page, employeeID, month,weekNo) {
    var link = '?page=' + page + '&week-no=' + weekNo + '&employee_id=' + employeeID + '&month=' + month + '&years=';
    var year = jQuery('.yeardata').val();
    location.href = link + year;
}



function wphrm_pdf_attenchment(employeeID, month) {
    var wphrmYear = jQuery('.yeardata').val();
    jQuery("form.wphrm-send-main-form > #wphrm-employee-id").val(employeeID);
    jQuery("form.wphrm-send-main-form > #wphrm-salary-month").val(month);
    jQuery("form.wphrm-send-main-form > #wphrm-salary-year").val(wphrmYear);
}
function wphrmProfitLossReport(type, action) {
    var fromdate = jQuery('#from-date').val();
    var todate = jQuery('#to-date').val();
    if (fromdate != '' && todate != '') {
        jQuery("input[name='wphrm-report-type']").val(type);
        jQuery("input[name='wphrm-report-action']").val(action);
        jQuery("form[name='wphrm-profit-loss']").submit();
    } else {
        jQuery('#financialModal').appendTo("body").modal('show');
    }
}
function wphrmTotalSalaryReports(type) {
    var fromdate = jQuery('#from-month-year').val();
    var todate = jQuery('#to-month-year').val();
    if (fromdate != '' && todate != '') {
        jQuery("input[name='wphrm-report-type']").val(type);
        jQuery("form[name='wphrm-total-salary-reports']").submit();
    } else {
        jQuery('#financialModal').appendTo("body").modal('show');
    }
}
function wphrmSalaryReport(type) {
    var fromdate = jQuery('#from-date').val();
    var todate = jQuery('#to-date').val();
    if (fromdate != '' && todate != '') {
        jQuery("input[name='wphrm-report-type']").val(type);
        jQuery("form[name='wphrm-salary-report']").submit();

    } else {
        jQuery('#salary-excel-Modal').appendTo("body").modal('show');
    }
}
function salary_request(employeeID, month) {
    var year = jQuery('.yeardata').val();
    jQuery('.preloader-custom-gif').show();
    jQuery('.preloader ').show();
    wphrm_salary_data = {
        'action': 'WPHRMSalaryRequest',
        'employee_id': employeeID,
        'month': month,
        'year': year,
    }
    jQuery.post(ajaxurl, wphrm_salary_data, function (response) {
        var data = JSON.parse(response);
        if (data) {
            jQuery('.preloader-custom-gif').hide();
            jQuery('.preloader ').hide();
            jQuery('#salary_info_success').removeClass('display-hide');
            jQuery('#salary_info_success').html("<i class='fa fa-check-square' aria-hidden='true'></i>" + data.success);
            jQuery("html, body").animate({scrollTop: 0}, "slow");

        } else {
            jQuery('.preloader-custom-gif').hide();
            jQuery('.preloader ').hide();
            jQuery('#salary_info_error').html("Something went wrong!");
            jQuery('#salary_info_error').removeClass('display-hide');
            jQuery("#salary_info_error").css('color', 'red');
            jQuery("html, body").animate({scrollTop: 0}, "slow");
        }
    });
}

function salary_Week_request(employeeID, month,weekNo) {
    var year = jQuery('.yeardata').val();
    jQuery('.preloader-custom-gif').show();
    jQuery('.preloader ').show();
    wphrm_salary_data = {
        'action': 'WPHRMSalaryWeekRequest',
        'employee_id': employeeID,
        'weekNo': weekNo,
        'month': month,
        'year': year,
    }
    jQuery.post(ajaxurl, wphrm_salary_data, function (response) {
        var data = JSON.parse(response);
        if (data) {
            jQuery('.preloader-custom-gif').hide();
            jQuery('.preloader ').hide();
            jQuery('#salary_info_success').removeClass('display-hide');
            jQuery('#salary_info_success').html("<i class='fa fa-check-square' aria-hidden='true'></i>" + data.success);
            jQuery("html, body").animate({scrollTop: 0}, "slow");

        } else {
            jQuery('.preloader-custom-gif').hide();
            jQuery('.preloader ').hide();
            jQuery('#salary_info_error').html("Something went wrong!");
            jQuery('#salary_info_error').removeClass('display-hide');
            jQuery("#salary_info_error").css('color', 'red');
            jQuery("html, body").animate({scrollTop: 0}, "slow");
        }
    });
}

/** Import And Export XML Function **/
function wphrmImportAndExport() {
    var link = '?page=database-generate-reports';
    location.href = link;
}



function showHide(id) {
    jQuery('#leaveTypeLabel').show(100);
    jQuery('#reasonLabel').show(100);


    if (jQuery('#checkbox' + id + ':checked').val() == 'on') {
        jQuery('#leaveType' + id + ' option:first').attr('selected', 'selected');
        jQuery('#reason' + id).val("");
        jQuery('#leaveOn' + id + ' option:first').attr('selected', 'selected');

        jQuery('#leaveType' + id).hide(1000);
        jQuery('#reason' + id).hide(1000);
        jQuery('#leaveOn' + id).hide(1000);

    } else {
        jQuery('#leaveOn' + id).show(100);
        jQuery('#leaveType' + id).show(300);
        jQuery('#reason' + id).show(500);

    }
}
function showHide_permissions(id) {
    if (jQuery('#checkbox' + id + ':checked').val() != 'on') {
        jQuery('.checkbox' + id).hide(1000);
    } else {
        jQuery('.checkbox' + id).show(800);
    }
}
function halfDayToggle(id, value) {
    if (value == 'half day') {
        jQuery('#halfDayLabel').show(100);
        jQuery('#halfLeaveType' + id).show(100);
    } else {
        jQuery('#halfLeaveType' + id).hide(100);
    }
}

function wphrmRoleAction(status) {
    if (status == 'add') {
        var actions = 'add';
    } else {
        var actions = 'edit';
    }
    if (actions == 'edit') {
        wphrmGetRoles()
        jQuery(".add-role").hide();
        jQuery(".edit-role").show();
        jQuery("#wphrm_rolename").val('');
        jQuery("#wphrm_displayname").val('');
        jQuery(".role-permission-show").empty();
    } else if (actions == 'add') {
        jQuery(".edit-role").hide();
        jQuery(".add-role").show();
        jQuery(".role-permission-show").empty();
        wphrmGetDefaultRolePages();
    } else {
        jQuery(".add-role").hide();
        jQuery(".edit-role").hide();
        jQuery(".role-permission-show").empty();
    }
}

function wphrmGetDefaultRolePages() {
    wphrm_data = {
        'action': 'wphrmGetDefaultRolePages'
    }
    jQuery('.preloader-custom-gif').show();
    jQuery('.preloader ').show();
    jQuery.post(ajaxurl, wphrm_data, function (response) {
        var data = JSON.parse(response);
        if (data.success) {
            jQuery(".role-permission-show").show();
            jQuery('.preloader-custom-gif').hide();
            jQuery('.preloader ').hide();
            jQuery('#role-permission-show').html(data.success);
        } else {
            jQuery('.preloader-custom-gif').hide();
            jQuery('.preloader ').hide();
        }
    });
}

function wphrmGetRoles() {
    wphrm_data = {
        'action': 'WPHRMGetRoles'
    }
    jQuery('.preloader-custom-gif').show();
    jQuery('.preloader ').show();
    jQuery.post(ajaxurl, wphrm_data, function (response) {
        var data = JSON.parse(response);
        if (data.success) {
            jQuery('.wphrm_user_permission').html(data.success);
            jQuery('.preloader-custom-gif').hide();
            jQuery('.preloader ').hide();

        } else {
            jQuery('.preloader-custom-gif').hide();
            jQuery('.preloader ').hide();
        }
    });
}

function wphrmChangeRole(actionName) {
    if (actionName == 'adddatabaserole') {
        var actions = 'adddatabaserole';
    } else {
        var actions = 'adddefaultrole';
    }

    wphrm_data = {
        'action': 'WPHRMChangeRoleWiseDisplay',
        'wphrm-actions': actions
    }
    jQuery('.preloader-custom-gif').show();
    jQuery('.preloader ').show();
    jQuery.post(ajaxurl, wphrm_data, function (response) {
        var data = JSON.parse(response);
        if (data.success) {
            jQuery('.preloader-custom-gif').hide();
            jQuery('.preloader ').hide();
            location.reload();
        } else {
            jQuery('.preloader-custom-gif').hide();
            jQuery('.preloader ').hide();
            location.reload();
        }
    });
}

jQuery(function () {
    var $dTable = jQuery("#wphrmDataTable").dataTable();
    $dateControls = jQuery("#baseDateControl").children("div").clone();
    jQuery("#feedbackTable_filter").prepend($dateControls);
    jQuery("#to-date").change(function () {
        jQuery.fn.dataTableExt.afnFiltering.push(
            function (oSettings, aData, iDataIndex) {
                var dateStart = parseDateValue(jQuery("#from-date").val());
                var dateEnd = parseDateValue(jQuery("#to-date").val());
                var evalDate = parseDateValue(aData[3]);
                if ((evalDate >= dateStart) && (evalDate <= dateEnd)) {
                    return true;
                } else {
                    return false;
                }
            }
        );
        function parseDateValue(rawDate) {
            var dateArray = rawDate.split("-");
            var parsedDate = dateArray[2] + dateArray[1] + dateArray[0];
            return parsedDate;
        }
        $dTable.fnDraw();
    });

    /*jQuery('#wphrmDataTable tfoot th').each( function () {
        var title = $(this).text();
        jQuery(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    } );*/

    // Apply the search
    /*$dTable.columns().every( function () {
        var that = this;
        $( 'input', this.footer() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );
    } );*/


    jQuery("#to-month-year").change(function () {
        jQuery.fn.dataTableExt.afnFiltering.push(
            function (oSettings, aData, iDataIndex) {
                var dateStart = parseDateValue(jQuery("#from-month-year").val());
                var dateEnd = parseDateValue(jQuery("#to-month-year").val());
                var evalDate = parseDateValue(aData[1]);
                if ((evalDate >= dateStart) && (evalDate <= dateEnd)) {
                    return true;
                } else {
                    return false;
                }
            }
        );
        function parseDateValue(rawDate) {
            var dateString = rawDate,
                dateParts = dateString.split(' '),
                date;
            var monthString = dateParts[0];
            var dates = new Date('1 ' + monthString + ' 1999');
            var getMonth = dates.getMonth();
            date = new Date(dateParts[1], getMonth);
            var parseDateValue = date.getTime();
            return parseDateValue;

        }
        $dTable.fnDraw();
    });


    jQuery("#mainsearch").change(function () {
        jQuery.fn.dataTableExt.afnFiltering.push(
            function (oSettings, aData, iDataIndex) {
                var mainsearch = jQuery('#mainsearch').val();
                if (mainsearch == '') {
                    return true;
                } else {
                    var evalDate = aData[3].toLowerCase().replace(/ /g, '-');
                    if (mainsearch == evalDate) {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        );
        $dTable.fnDraw();
    });
});
function setDateActive() {
    var monthselect = jQuery('.month-data').val();
    var setYear = jQuery('.yeardata').val();
    jQuery.fn.datepicker.defaults.autoclose = true;
    jQuery('.date-picker-default').datepicker("setDate", new Date(setYear, monthselect));

}

function wphrmAddweekand() {
    var setYear = jQuery('.yeardata').val();
    jQuery('#wphrmyear').val(setYear);

}

function WPHRMNotificationStatusChangeInfo(id, empid, month, year) {
    var ajax_url = ajaxurl + '?page=wphrm-salary-slip-details&wphrm-employee-id=' + empid + '&wphrm-create-salary-month=' + month + '&wphrm-create-salary-year=' + year + '';
    var url = ajax_url.replace('/admin-ajax', '/admin');
    window.location.href = url;
}
function WPHRMNotificationStatusWeekChangeInfo(id, empid) {
    var ajax_url = ajaxurl + '?page=wphrm-select-financials-week&employee_id=' + empid;
    var url = ajax_url.replace('/admin-ajax', '/admin');
    window.location.href = url;
}


function removedeductions(val) {
    jQuery('#deduction_scents' + val).remove();
}
function removeearning(val) {
    jQuery('#earning_scents' + val).remove();
}
function addHoliday(val) {
    jQuery('.addHoliday' + val).remove();
}
function  Bankfieldlabel(val) {
    jQuery('#Bankfieldlabel_scents' + val).remove();
}
function  Otherfieldlabel(val) {
    jQuery('#Otherfieldlabel_scents' + val).remove();
}
function  documentfieldlabel(val) {
    jQuery('#documentfieldlabel_scents' + val).remove();
}
function  Salaryfieldlabel(val) {
    jQuery('#salaryfieldlabel_scents' + val).remove();
}
function  designationAdd(val) {
    jQuery('#designationID' + val).remove();
}
function  departmentAdd(val) {
    jQuery('#departmentID' + val).remove();
}
function  copyLocalAddresss() {
    var localAddresss = jQuery('#wphrm_employee_local_address').val();
    jQuery('#wphrm_employee_permanant_address').val(localAddresss);
}


function calculateEarningSum() {
    var sum = 0;
    var decimal = jQuery(".wphrmdecimalInfo").val();
    jQuery(".earningcal").each(function () {
        if (!isNaN(this.value) && this.value.length != 0) {
            sum += parseFloat(this.value);

            if (jQuery(this).val().indexOf('.') != -1) {
                if (jQuery(this).val().split(".")[1].length > decimal) {
                    if (isNaN(parseFloat(this.value)))
                        return;
                    this.value = parseFloat(this.value).toFixed(decimal);
                }
            }
            return this; //for chaining
        }
    });
    jQuery("#earningcalsum").html(sum.toFixed(decimal));
    jQuery("#inputearningcalsum").val(sum.toFixed(decimal));
    calculateNetTotal();
}


function calculateDeductionSum() {
    var sum = 0;
    var decimal = jQuery("#wphrmdecimalInfo").val();
    jQuery(".deductioncal").each(function () {
        if (!isNaN(this.value) && this.value.length != 0) {
            sum += parseFloat(this.value);
            if (jQuery(this).val().indexOf('.') != -1) {
                if (jQuery(this).val().split(".")[1].length > decimal) {
                    if (isNaN(parseFloat(this.value)))
                        return;
                    this.value = parseFloat(this.value).toFixed(decimal);
                }
            }
            return this; //for chaining
        }
    });
    jQuery("#deductioncalsum").html(sum.toFixed(decimal));
    jQuery("#inputdeductioncalsum").val(sum.toFixed(decimal));
    calculateNetTotal();
}
function calculateNetTotal() {
    var decimal = jQuery("#wphrmdecimalInfo").val();
    var earningTotal = jQuery("#earningcalsum").html();
    var detuctionTotal = jQuery("#deductioncalsum").html();
    var netTotal = earningTotal - detuctionTotal;
    jQuery("#netTotal").html(netTotal.toFixed(decimal));
}

jQuery(function () {

    jQuery(".toDateBetweenDate").on('changeDate', function () {
        wphgrmCheckHolidayBetweenDate();
    });
    jQuery(".fromDateBetweenDate").on('changeDate', function () {
        wphgrmCheckHolidayBetweenDate();
    });

    jQuery(".make-switch .wphrm-to-leve-day-type").bootstrapSwitch('state', true);

    jQuery(".make-switch .wphrm-from-leve-day-type").bootstrapSwitch('state', true);
    jQuery('.wphrm-to-leve-day-type').on('switchChange.bootstrapSwitch', function (event, state) {
        wphgrmCheckHolidayBetweenDate();
    });

    jQuery('.wphrm-from-leve-day-type').on('switchChange.bootstrapSwitch', function (event, state) {
        wphgrmCheckHolidayBetweenDate();
    });
});


function wphgrmCheckHolidayBetweenDate() {
    var startDate = jQuery("#wphrm_leavedate").val();
    var endDate = jQuery("#wphrm_leavedate_to").val();
    var wphrm_leavetyped = jQuery("#wphrm_leavetyped").val();
    var fromtype = jQuery(".wphrm-from-leve-day-type").is(':checked');
    var totype = jQuery(".wphrm-to-leve-day-type").is(':checked');

    /*if(fromtype == totype && totype != ''){
        jQuery(".wphrm-to-leve-day-type").bootstrapSwitch('readonly', true);
    }else{
        jQuery(".wphrm-to-leve-day-type").bootstrapSwitch('readonly', false);
    }*/

    wphrm_data = {
        'action': 'WPHRMCheckHolidayBetweenDate',
        'startDate': startDate,
        'endDate': endDate,
        'wphrm_leavetyped': wphrm_leavetyped,
        'fromtype': fromtype,
        'totype': totype,
    }
    jQuery('.preloader-custom-gif').show();
    jQuery('.preloader').show();
    jQuery.post(ajaxurl, wphrm_data, function (response) {
        var data = JSON.parse(response);
        if (data.success != '') {
			jQuery('#wphrm_user_leave_applications_frm .wphrm_toleave').val( data.leave_count );
            jQuery("#holidayview").html(data.holiday);
            jQuery(".wphrm_leave_limit").val(data.max_leave);
            jQuery("#leaveview").attr('count', data.leave_count).html(data.leave);
            jQuery("#leavepaid").html(data.paid);
            jQuery("#leaveunpaid").html(data.unpaid);
            jQuery(".holidayDivHide").show();
            jQuery('.preloader-custom-gif').hide();
            jQuery('.preloader ').hide();
            jQuery("#leavelimit").html(data.max_leave_html);
            jQuery('#wphrm_user_leave_applications_frm .wphrm_toleave').val(data.leave_count);
            
            if( data.reimbursement != 0 ) {
                jQuery('#reimbursement').parent().parent().removeClass('hide');
                jQuery('#claimed_reimbursement').parent().parent().removeClass('hide');
                jQuery('#reimbursement').text('$' + data.reimbursement);
                jQuery('#claimed_reimbursement').text('$' + data.claimed_medical);
            } else {
                jQuery('#reimbursement').parent().parent().addClass('hide');
                jQuery('#claimed_reimbursement').parent().parent().addClass('hide');
            }
            if( data.elderly != 0 ) {
                jQuery('#elderlylimit').parent().parent().removeClass('hide');
                jQuery('#elderlylimit').text('$' + data.elderly);
            } else {
                jQuery('#elderlylimit').parent().parent().addClass('hide');
            }
            if( data.child_age_limit != 0 ) {
                jQuery('#child_age').parent().parent().removeClass('hide');
                jQuery('#child_age').text(data.child_age_limit + ' years old');
            } else {
                jQuery('#child_age').parent().parent().addClass('hide');
            }
            if( data.max_children_covered != 0 ) {
                jQuery('#max_children').parent().parent().removeClass('hide');
                jQuery('#max_children').text(data.child_age_limit);
            } else {
                jQuery('#max_children').parent().parent().addClass('hide');
            }

            if( data.medical_claim ) {
                jQuery('.leave-medical-claim').removeClass('hide');
            } else {
                jQuery('.leave-medical-claim').addClass('hide');
                jQuery('#reimbursement').parent().parent().addClass('hide');
                jQuery('#claimed_reimbursement').parent().parent().addClass('hide');
            }
            
        }
        if( data.leave_count > data.total_leave_left ) {
            jQuery('#wphrm_user_leave_applications_frm').attr('disabled', 'disabled').find('[type="submit"]').attr('disabled', 'disabled');
            jQuery('#alert.alert-holder').html("The number of leave taken exceeded your leave balance. You are only allowed to use your remaining " + data.total_leave_left + " days of leave");
            jQuery('#alert.alert-holder').slideDown(800);
            setTimeout(function() {
                jQuery('#alert.alert-holder').slideUp(800);
            }, 5000);
        } else {
            jQuery('#alert.alert-holder').hide(); 
            jQuery('#wphrm_user_leave_applications_frm').removeAttr('disabled').find('[type="submit"]').removeAttr('disabled');
        }
    });

    //get the limit for this leave
    /*wphrm_data = {
        'action': 'wphrm_get_leave_limit',
        'startDate': startDate,
        'endDate': endDate,
        'wphrm_leavetyped': wphrm_leavetyped,
        'fromtype': fromtype,
        'totype': totype,
    }
    jQuery.post(ajaxurl, wphrm_data, function (response) {
        var data = JSON.parse(response);*/
        
        /*if( data.toLeave > data.total_leave_left ) {
            jQuery('#wphrm_user_leave_applications_frm').attr('disabled', 'disabled').find('[type="submit"]').attr('disabled', 'disabled');
            jQuery('#alert.alert-holder').html("The number of leave taken exceeded your leave balance. You are only allowed to use your remaining " + data.total_leave_left + " days of leave");
            jQuery('#alert.alert-holder').slideDown(800);
            setTimeout(function() {
                jQuery('#alert.alert-holder').slideUp(800);
            }, 5000);
        } else {
            jQuery('#alert.alert-holder').hide();*/
            /*if (data.status) {
                jQuery("#leavelimit").html(data.max_leave_html);
            }
            console.log(data.used_leave, data.max_leave);*/
            //jQuery('#wphrm_user_leave_applications_frm .wphrm_leave_left').val( data.total_leave_left );
            //jQuery('#wphrm_user_leave_applications_frm').removeAttr('disabled').find('[type="submit"]').removeAttr('disabled');

            /*if(data.used_leave >= data.max_leave) {
                jQuery('#wphrm_user_leave_applications_frm').attr('disabled', 'disabled').find('[type="submit"]').attr('disabled', 'disabled');
                //alert('“the number of leave taken exceeded than your leave balance”. You are only allowed to use your remaining '+ leaveLeft +' days of leave”');
                //jQuery("#leavelimit").addClass('label-danger').removeClass('label-info');
            }else{
                jQuery('#wphrm_user_leave_applications_frm').removeAttr('disabled').find('[type="submit"]').removeAttr('disabled');
                //jQuery("#leavelimit").addClass('label-info').removeClass('label-danger');
            }*/
        //}
   // });
}

function wphrmOpenSettings(evt, ganeralSettings) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(ganeralSettings).style.display = "block";
    evt.currentTarget.className += " active";
}

/*
function WPHRMCustomDelete(id, tablename, filed_name) {
    jQuery('#deleteModal').appendTo("body").modal('show');
    jQuery('#info').html(WPHRMCustomJS.Deletemsg);
    jQuery("#delete").click(function () {
        jQuery('.preloader-custom-gif').show();
        jQuery('.preloader ').show();
        wphrm_holiday_data = {
            'action': 'WPHRMCustomDelete',
            'WPHRMCustomDelete_id': id,
            'table_name': '' + tablename + '',
            'filed_name': '' + filed_name + ''
        }
        jQuery.post(ajaxurl, wphrm_holiday_data, function (response) {
            var data = JSON.parse(response);
            if (data.success) {
                jQuery('.preloader-custom-gif').hide();
                jQuery('.preloader ').hide();
                jQuery('#WPHRMCustomDelete_success').removeClass('display-hide');
                window.location.reload();
            } else {
                jQuery('#WPHRMCustomDelete_error').html(data.errors);
                jQuery('#WPHRMCustomDelete_error').removeClass('display-hide');
                jQuery('.preloader-custom-gif').hide();
                jQuery('.preloader ').hide();
            }
        });
    })
}*/


// this is for employee Documents details store in database ajax
jQuery(function($){
    var wphrm_employee_work_info_form = jQuery("#wphrm_employee_work_info_form");
    var leave = [];
    var leaves = jQuery('.leave-entitlement-holder input[name="wphrm_employee_entitled_leave[]"]').each(function() {
        leave.push( jQuery(this).val() );
    });
    wphrm_employee_work_info_form.validate({
        rules: {
            /*wphrm_employee_employment_country: "required",*/
        },
        submitHandler: function (wphrm_employee_work_info_form) {
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var formData = new FormData(jQuery(wphrm_employee_work_info_form)['0']);
            formData.append('action', 'WPHRMEmployeeBasicInfo');
            formData.append('wphrm_employee_entitled_leave', leave);
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
                        var employee_id = data.success;
                        var role = data.currentrole;
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#work_details_success').removeClass('display-hide');
                        jQuery("html, body").animate({scrollTop: 0}, "slow");
                        if (role) {
                            if (employee_id != true) {
                                /*window.setTimeout(function () {
                                    window.location.href = '?page=wphrm-employee-info&employee_id=' + employee_id;
                                }, 1500);*/
                            }
                        }
                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#work_details_error').html(data.error);
                        jQuery('#work_details_error').removeClass('display-hide');
                        jQuery("#work_details_error").css('color', 'red');
                        jQuery("html, body").animate({scrollTop: 0}, "slow");
                    }
                }
            });
        }
    });
});

/* Added JS for Leave Rules */
function leaveruleEdit( id, leaveRule, description, employeeType, emp_status, years_in_service, gender, nationality, age, marital_status, max_children_covered, child_age_limit, child_nationality, medical_claim_limit, elderly_screening_limit, maternity, paternity, lieu, outstation, examination ) {
    jQuery('#wphrm_edit_leaverule_form #wphrm-leaverule-id').val(id);
    jQuery('#wphrm_edit_leaverule_form #wphrm-leaveRule-edit').val(leaveRule);
    jQuery('#wphrm_edit_leaverule_form #wphrm-leaveRule-description-edit').val(description);
    jQuery('#wphrm_edit_leaverule_form #wphrm-yis-edit').val(years_in_service);
    jQuery('#wphrm_edit_leaverule_form #wphrm-employment-status-edit').val(emp_status);
    jQuery('#wphrm_edit_leaverule_form #wphrm-gender-edit').val(gender);
    jQuery('#wphrm_edit_leaverule_form #wphrm-nationality-edit').val(nationality);
    jQuery('#wphrm_edit_leaverule_form #wphrm-age-edit').val(age);
    jQuery('#wphrm_edit_leaverule_form #wphrm-status-edit').val(marital_status);
    jQuery('#wphrm_edit_leaverule_form #wphrm-max-child-edit').val(max_children_covered);
    jQuery('#wphrm_edit_leaverule_form #wphrm-age-limit-edit').val(child_age_limit);
    jQuery('#wphrm_edit_leaverule_form #wphrm-child-nationality-edit').val(child_nationality);
    jQuery('#wphrm_edit_leaverule_form #wphrm-claim-limit-edit').val(medical_claim_limit);
    jQuery('#wphrm_edit_leaverule_form #wphrm-elderly-limit-edit').val(elderly_screening_limit);
    jQuery("input[name=wphrm-maternity-edit][value="+maternity+"]").attr('checked', true);
    jQuery("input[name=wphrm-paternity-edit][value="+paternity+"]").attr('checked', true);
    jQuery("input[name=wphrm-lieu-edit][value="+lieu+"]").attr('checked', true);
    jQuery("input[name=wphrm-outstation-edit][value="+outstation+"]").attr('checked', true);
    jQuery("input[name=wphrm-examination-edit][value="+examination+"]").attr('checked', true);
    
    
    if( employeeType == 'N/A' ) {
        jQuery('.wphrm-employee-type-ed-edit').bootstrapSwitch('state', false);
        jQuery('#wphrm_edit_leaverule_form #wphrm-employee-type-edit').prop('disabled', true);
        jQuery('#wphrm_edit_leaverule_form #wphrm-employee-type-edit').val('');
        jQuery('#wphrm_edit_leaverule_form #wphrm-employee-type-edit')[0].sumo.unSelectAll();
    } else {
        jQuery('.wphrm-employee-type-ed-edit').bootstrapSwitch('state', true);
        jQuery('#wphrm_edit_leaverule_form #wphrm-employee-type-edit').prop('disabled', false);
        
        var et = employeeType.split(',');
        var x = 0;
        for( x = 0; x < et.length; x++ ) {
           jQuery('#wphrm_edit_leaverule_form #wphrm-employee-type-edit')[0].sumo.selectItem(et[x]);
        }
    }
    if( emp_status == 0 ) {
        jQuery('.wphrm-es-ed-edit').bootstrapSwitch('state', false);
        jQuery('#wphrm_edit_leaverule_form #wphrm-employment-status-edit').prop('disabled', true);
        jQuery('#wphrm_edit_leaverule_form #wphrm-employment-status-edit').val('');
    } else {
        jQuery('.wphrm-es-ed-edit').bootstrapSwitch('state', true);
        jQuery('#wphrm_edit_leaverule_form #wphrm-employment-status-edit').prop('disabled', false);
        
        if( emp_status == 2 ) {
            jQuery('#wphrm_edit_leaverule_form .yis-holder-edit').removeClass('hide');
        }
    }
    if( years_in_service == 0 ) {
        jQuery('.wphrm-yis-ed-edit').bootstrapSwitch('state', false);
        jQuery('#wphrm_edit_leaverule_form #wphrm-yis-edit').prop('disabled', true);
        jQuery('#wphrm_edit_leaverule_form #wphrm-yis-edit').val('');
    } else {
        jQuery('.wphrm-yis-ed-edit').bootstrapSwitch('state', true);
        jQuery('#wphrm_edit_leaverule_form #wphrm-yis-edit').prop('disabled', false);
    }
    if( gender == 0 ) {
        jQuery('.wphrm-gender-ed-edit').bootstrapSwitch('state', false);
        jQuery('#wphrm_edit_leaverule_form #wphrm-gender-edit').prop('disabled', true);
        jQuery('#wphrm_edit_leaverule_form #wphrm-gender-edit').val('');
    } else {
        jQuery('.wphrm-gender-ed-edit').bootstrapSwitch('state', true);
        jQuery('#wphrm_edit_leaverule_form #wphrm-gender-edit').prop('disabled', false);
    }
    if( nationality == 0 ) {
        jQuery('.wphrm-nationality-ed-edit').bootstrapSwitch('state', false);
        jQuery('#wphrm_edit_leaverule_form #wphrm-nationality-edit').prop('disabled', true);
        jQuery('#wphrm_edit_leaverule_form #wphrm-nationality-edit').val('');
    } else {
        jQuery('.wphrm-nationality-ed-edit').bootstrapSwitch('state', true);
        jQuery('#wphrm_edit_leaverule_form #wphrm-nationality-edit').prop('disabled', false);
    }
    if( age == 0 ) {
        jQuery('.wphrm-age-ed-edit').bootstrapSwitch('state', false);
        jQuery('#wphrm_edit_leaverule_form #wphrm-age-edit').prop('disabled', true);
        jQuery('#wphrm_edit_leaverule_form #wphrm-age-edit').val('');
    } else {
        jQuery('.wphrm-age-ed-edit').bootstrapSwitch('state', true);
        jQuery('#wphrm_edit_leaverule_form #wphrm-age-edit').prop('disabled', false);
    }
    if( marital_status == 'N/A' ) {
        jQuery('.wphrm-status-ed-edit').bootstrapSwitch('state', false);
        jQuery('#wphrm_edit_leaverule_form #wphrm-status-edit').prop('disabled', true);
        jQuery('#wphrm_edit_leaverule_form #wphrm-status-edit').val('');
        jQuery('#wphrm_edit_leaverule_form #wphrm-status-edit')[0].sumo.unSelectAll();
    } else {
        jQuery('.wphrm-status-ed-edit').bootstrapSwitch('state', true);
        jQuery('#wphrm_edit_leaverule_form #wphrm-status-edit').prop('disabled', false);
        var ms = marital_status.split(',');
        var y = 0;
        for( y = 0; y < ms.length; y++ ) {
           jQuery('#wphrm_edit_leaverule_form #wphrm-status-edit')[0].sumo.selectItem(ms[y]);
        }
    }
    if( max_children_covered == 0 ) {
        jQuery('.wphrm-max-child-ed-edit').bootstrapSwitch('state', false);
        jQuery('#wphrm_edit_leaverule_form #wphrm-max-child-edit').prop('disabled', true);
        jQuery('#wphrm_edit_leaverule_form #wphrm-max-child-edit').val('');
    } else {
        jQuery('.wphrm-max-child-ed-edit').bootstrapSwitch('state', true);
        jQuery('#wphrm_edit_leaverule_form #wphrm-max-child-edit').prop('disabled', false);
    }
    if( child_age_limit == 0 ) {
        jQuery('.wphrm-age-limit-ed-edit').bootstrapSwitch('state', false);
        jQuery('#wphrm_edit_leaverule_form #wphrm-age-limit-edit').prop('disabled', true);
        jQuery('#wphrm_edit_leaverule_form #wphrm-age-limit-edit').val('');
    } else {
        jQuery('.wphrm-age-limit-ed-edit').bootstrapSwitch('state', true);
        jQuery('#wphrm_edit_leaverule_form #wphrm-age-limit-edit').prop('disabled', false);
    }
    if( child_nationality == 0 ) {
        jQuery('.wphrm-child-nationality-ed-edit').bootstrapSwitch('state', false);
        jQuery('#wphrm_edit_leaverule_form #wphrm-child-nationality-edit').prop('disabled', true);
        jQuery('#wphrm_edit_leaverule_form #wphrm-child-nationality-edit').val('');
    } else {
        jQuery('.wphrm-child-nationality-ed-edit').bootstrapSwitch('state', true);
        jQuery('#wphrm_edit_leaverule_form #wphrm-child-nationality-edit').prop('disabled', false);
    }
    if( medical_claim_limit == 0 ) {
        jQuery('.wphrm-claim-limit-ed-edit').bootstrapSwitch('state', false);
        jQuery('#wphrm_edit_leaverule_form #wphrm-claim-limit-edit').prop('disabled', true);
        jQuery('#wphrm_edit_leaverule_form #wphrm-claim-limit-edit').val('');
    } else {
        jQuery('.wphrm-claim-limit-ed-edit').bootstrapSwitch('state', true);
        jQuery('#wphrm_edit_leaverule_form #wphrm-claim-limit-edit').prop('disabled', false);
    }
    if( elderly_screening_limit == 0 ) {
        jQuery('.wphrm-elderly-limit-ed-edit').bootstrapSwitch('state', false);
        jQuery('#wphrm_edit_leaverule_form #wphrm-elderly-limit-edit').prop('disabled', true);
        jQuery('#wphrm_edit_leaverule_form #wphrm-elderly-limit-edit').val('');
    } else {
        jQuery('.wphrm-elderly-limit-ed-edit').bootstrapSwitch('state', true);
        jQuery('#wphrm_edit_leaverule_form #wphrm-elderly-limit-edit').prop('disabled', false);
    }
    
    var wphrm_edit_leaverule_form = jQuery("#wphrm_edit_leaverule_form");
    var ajax_url = wphrm_edit_leaverule_form.data('ajax_url');
    wphrm_edit_leaverule_form.validate({
        rules: {
            
        },
        submitHandler: function (wphrm_edit_leaverule_form) {
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var formData = new FormData(jQuery(wphrm_edit_leaverule_form)['0']);
            formData.append('action', 'WPHRMLeaveRule');
            formData.append('wphrm-leaverule-id', id);
            jQuery.ajax({
                method: "POST",
                url: ajax_url,
                contentType: false,
                cache: false,
                processData: false,
                data: formData,
                success: function (output) {
                    var data = JSON.parse(output);
                    if (data.status) {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#wphrm_edit_leaverule_success').removeClass('display-hide');

                        window.setTimeout(function () {
                            window.location.reload();
                        }, 500);
                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        //jQuery('#wphrm_edit_leaverule_error').html(data.errors);
                        jQuery('#wphrm_edit_leaverule_error').removeClass('display-hide');
                        jQuery("#wphrm_edit_leaverule_error").css('color', 'red');
                        window.setTimeout(function () {
                            window.location.reload();
                        }, 500);
                    }
                }
            });
        }
    });
}
jQuery(function($) {

    $('.before-current-date input, .after-current-date input ').datepicker({
        orientation: "top left"
    });

    var lr_form = $('#wphrm_add_leaverule_form');
    var elr_form = $('#wphrm_edit_leaverule_form');
    function to_disable( form, target, status ) {
        if( status == false ) {
            form.find(target).prop('disabled', true);
            form.find(target).removeAttr('required');
            form.find(target).val('');
        } else {
            form.find(target).prop('disabled', false);
            form.find(target).prop('required', true);
        }        
    }
    function save_leaveRule() {
        var ajax_url = lr_form.data('ajax_url');
        var formData = new FormData( $(lr_form)['0'] );
        formData.append( 'action', 'WPHRMLeaveRule' );
        $.ajax({
            method: "POST",
            url: ajax_url,
            contentType: false,
            cache: false,
            processData: false,
            data: formData,
            success: function( output ) {
                var data = JSON.parse( output );
                if( data.status ) {
                    $('#wphrm_add_leaverule_success').removeClass('display-hide');
                    window.setTimeout(function () {
                        window.location.href = '?page=wphrm-leave-rules';
                    }, 1500);
                } else {
                    $('#wphrm_add_leaverule_success').removeClass('display-hide');
                }
            }
        });
    }
    lr_form.find('.wphrm-employee-type-ed').on('switchChange.bootstrapSwitch', function (event, state) {
        var chck = $(this).bootstrapSwitch('state');
        to_disable(lr_form, '#wphrm-employee-type', chck);
    });
    lr_form.find('.wphrm-es-ed').on('switchChange.bootstrapSwitch', function (event, state) {
        var chck = $(this).bootstrapSwitch('state');
        to_disable(lr_form, '#wphrm-employment-status', chck);
        lr_form.find('#wphrm-yis').removeAttr('required');
    });
    lr_form.find('.wphrm-yis-ed').on('switchChange.bootstrapSwitch', function (event, state) {
        var chck = $(this).bootstrapSwitch('state');
        to_disable(lr_form, '#wphrm-yis', chck);
    });
    lr_form.find('.wphrm-gender-ed').on('switchChange.bootstrapSwitch', function (event, state) {
        var chck = $(this).bootstrapSwitch('state');
        to_disable(lr_form, '#wphrm-gender', chck);
    });
    lr_form.find('.wphrm-nationality-ed').on('switchChange.bootstrapSwitch', function (event, state) {
        var chck = $(this).bootstrapSwitch('state');
        to_disable(lr_form, '#wphrm-nationality', chck);
    });
    lr_form.find('.wphrm-age-ed').on('switchChange.bootstrapSwitch', function (event, state) {
        var chck = $(this).bootstrapSwitch('state');
        to_disable(lr_form, '#wphrm-age', chck);
    });
    lr_form.find('.wphrm-status-ed').on('switchChange.bootstrapSwitch', function (event, state) {
        var chck = $(this).bootstrapSwitch('state');
        to_disable(lr_form, '#wphrm-status', chck);
    });
    lr_form.find('.wphrm-max-child-ed').on('switchChange.bootstrapSwitch', function (event, state) {
        var chck = $(this).bootstrapSwitch('state');
        to_disable(lr_form, '#wphrm-max-child', chck);
    });
    lr_form.find('.wphrm-age-limit-ed').on('switchChange.bootstrapSwitch', function (event, state) {
        var chck = $(this).bootstrapSwitch('state');
        to_disable(lr_form, '#wphrm-age-limit', chck);
    });
    lr_form.find('.wphrm-child-nationality-ed').on('switchChange.bootstrapSwitch', function (event, state) {
        var chck = $(this).bootstrapSwitch('state');
        to_disable(lr_form, '#wphrm-child-nationality', chck);
    });
    lr_form.find('.wphrm-claim-limit-ed').on('switchChange.bootstrapSwitch', function (event, state) {
        var chck = $(this).bootstrapSwitch('state');
        to_disable(lr_form, '#wphrm-claim-limit', chck);
    });
    lr_form.find('.wphrm-elderly-limit-ed').on('switchChange.bootstrapSwitch', function (event, state) {
        var chck = $(this).bootstrapSwitch('state');
        to_disable(lr_form, '#wphrm-elderly-limit', chck);
    });
    lr_form.find('.wphrm-maternity-ed').on('switchChange.bootstrapSwitch', function (event, state) {
        var chck = $(this).bootstrapSwitch('state');
        to_disable(lr_form, '.wphrm-maternity', chck);
    });
    lr_form.find('.wphrm-paternity-ed').on('switchChange.bootstrapSwitch', function (event, state) {
        var chck = $(this).bootstrapSwitch('state');
        to_disable(lr_form, '.wphrm-paternity', chck);
    });
    lr_form.find('.wphrm-lieu-ed').on('switchChange.bootstrapSwitch', function (event, state) {
        var chck = $(this).bootstrapSwitch('state');
        to_disable(lr_form, '.wphrm-lieu', chck);
    });
    lr_form.find('.wphrm-outstation-ed').on('switchChange.bootstrapSwitch', function (event, state) {
        var chck = $(this).bootstrapSwitch('state');
        to_disable(lr_form, '.wphrm-outstation', chck);
    });
    lr_form.find('.wphrm-examination-ed').on('switchChange.bootstrapSwitch', function (event, state) {
        var chck = $(this).bootstrapSwitch('state');
        to_disable(lr_form, '.wphrm-examination', chck);
    });
    
    /* edit form */
    elr_form.find('.wphrm-employee-type-ed-edit').on('switchChange.bootstrapSwitch', function (event, state) {
        var chck = $(this).bootstrapSwitch('state');
        to_disable(elr_form, '#wphrm-employee-type-edit', chck);
    });
    elr_form.find('.wphrm-es-ed-edit').on('switchChange.bootstrapSwitch', function (event, state) {
        var chck = $(this).bootstrapSwitch('state');
        to_disable(elr_form, '#wphrm-employment-status-edit', chck);
    });
    elr_form.find('.wphrm-yis-ed-edit').on('switchChange.bootstrapSwitch', function (event, state) {
        var chck = $(this).bootstrapSwitch('state');
        to_disable(elr_form, '#wphrm-yis-edit', chck);
    });
    elr_form.find('.wphrm-gender-ed-edit').on('switchChange.bootstrapSwitch', function (event, state) {
        var chck = $(this).bootstrapSwitch('state');
        to_disable(elr_form, '#wphrm-gender-edit', chck);
    });
    elr_form.find('.wphrm-nationality-ed-edit').on('switchChange.bootstrapSwitch', function (event, state) {
        var chck = $(this).bootstrapSwitch('state');
        to_disable(elr_form, '#wphrm-nationality-edit', chck);
    });
    elr_form.find('.wphrm-age-ed-edit').on('switchChange.bootstrapSwitch', function (event, state) {
        var chck = $(this).bootstrapSwitch('state');
        to_disable(elr_form, '#wphrm-age-edit', chck);
    });
    elr_form.find('.wphrm-status-ed-edit').on('switchChange.bootstrapSwitch', function (event, state) {
        var chck = $(this).bootstrapSwitch('state');
        to_disable(elr_form, '#wphrm-status-edit', chck);
    });
    elr_form.find('.wphrm-max-child-ed-edit').on('switchChange.bootstrapSwitch', function (event, state) {
        var chck = $(this).bootstrapSwitch('state');
        to_disable(elr_form, '#wphrm-max-child-edit', chck);
    });
    elr_form.find('.wphrm-child-nationality-ed-edit').on('switchChange.bootstrapSwitch', function (event, state) {
        var chck = $(this).bootstrapSwitch('state');
        to_disable(elr_form, '#wphrm-child-nationality-edit', chck);
    });
    elr_form.find('.wphrm-age-limit-ed-edit').on('switchChange.bootstrapSwitch', function (event, state) {
        var chck = $(this).bootstrapSwitch('state');
        to_disable(elr_form, '#wphrm-age-limit-edit', chck);
    });
    elr_form.find('.wphrm-claim-limit-ed-edit').on('switchChange.bootstrapSwitch', function (event, state) {
        var chck = $(this).bootstrapSwitch('state');
        to_disable(elr_form, '#wphrm-claim-limit-edit', chck);
    });
    elr_form.find('.wphrm-elderly-limit-ed-edit').on('switchChange.bootstrapSwitch', function (event, state) {
        var chck = $(this).bootstrapSwitch('state');
        to_disable(elr_form, '#wphrm-elderly-limit-edit', chck);
    });
    
    lr_form.on('submit', function() {
        save_leaveRule();
        return false;
    });
    lr_form.find('#wphrm-employment-status').on('change', function() {
        var regular = $(this).val();
        if( regular == 2 ) {
            lr_form.find('.yis-holder').removeClass('hide');
        } else {
            lr_form.find('.yis-holder').addClass('hide');
            lr_form.find('#wphrm-yis').removeAttr('required');
        }
    });
    elr_form.find('#wphrm-employment-status-edit').on('change', function() {
        var regular = $(this).val();
        if( regular == 2 ) {
            elr_form.find('.yis-holder-edit').removeClass('hide');
        } else {
            elr_form.find('.yis-holder-edit').addClass('hide');
            elr_form.find('#wphrm-yis-edit').removeAttr('required');
        }
    });
    
    //save all employee info
    if( $('body').hasClass('admin_page_wphrm-employee-info') ) {
        $('#wphrm_employee_work_info_form').trigger('submit');
    }
    $('.btn-save-all-emp-info').on('click', function() {
        $('#wphrm_employee_work_info_form').trigger('submit');
        $('#wphrm_employee_work_permit_form').trigger('submit');
        $('#wphrmEmployeeOtherInfo_form').trigger('submit');
        $('#wphrmEmployeeFamilyInfo_form').trigger('submit');
        $('#wphrm_employee_basic_info_form').trigger('submit');
        
        window.setTimeout(function () {
            window.location.reload();
        }, 1500);
        return false;
    });
    $('.admin_page_wphrm-employee-view-details').on('click', '.btn-save-emp-info', function() {
        $('.wphrmEmployeeOtherInfo_form').trigger('submit');
        $('.wphrmEmployeeFamilyInfo_form').trigger('submit');
        $('.wphrm_employee_work_permit_form').trigger('submit');
        $('.admin_page_wphrm-employee-view-details #other_details_success, .admin_page_wphrm-employee-view-details #family_details_success, .admin_page_wphrm-employee-view-details #work_permit_details_success').removeClass('display-hide');
        $('.admin_page_wphrm-employee-view-details #other_details_success, .admin_page_wphrm-employee-view-details #family_details_success, .admin_page_wphrm-employee-view-details #work_permit_details_success').html('<i class="fa fa-check-square"></i> Data successfully updated!');
        
        window.setTimeout(function () {
            window.location.reload();
        }, 2000);
        return false;
    });
    $('.admin_page_wphrm-employee-view-details').on('click', '.btn-edit-emp-info', function(e) {
        e.preventDefault();
        if( $(this).hasClass('to-cancel') ) {
            $(this).removeClass('to-cancel');
            $(this).html('<i class="fa fa-edit"></i> Edit');
            $('.admin_page_wphrm-employee-view-details .btn-save-emp-info').addClass('display-hide');
            $('.admin_page_wphrm-employee-view-details .editing-row').addClass('display-hide');
            $('.admin_page_wphrm-employee-view-details .viewing-row').removeClass('display-hide');
        } else {
            $(this).addClass('to-cancel');
            $(this).html('<i class="fa fa-times"></i> Cancel');
            $('.admin_page_wphrm-employee-view-details .btn-save-emp-info').removeClass('display-hide');
            $('.admin_page_wphrm-employee-view-details .editing-row').removeClass('display-hide');
            $('.admin_page_wphrm-employee-view-details .viewing-row').addClass('display-hide');
        }
    });

    //Hide designation per department
    $('#wphrm_employee_work_info_form').on('change', '#wphrm_employee_department', function() {
        var department = $(this).val();
        $('#wphrm_employee_work_info_form #wphrm_select_employee_designation option').each(function() {
            $(this).addClass('hide');
            if( $(this).data( 'department' ) == department ) {
                $(this).removeClass('hide');
            }
        });
    });

    //has child
    $('#wphrm_employee_basic_info_form').on('click', '.wphrm_employee_has_child', function() {
        var has = $('input[name=wphrm_employee_has_child]:checked').val();
        if( has == 'Yes' ) {
            $('.admin_page_wphrm-employee-info').animate({ scrollTop: $('.wphrmEmployeeFamilyInfo-table-children').offset().top }, 'slow');
            $('.wphrmEmployeeFamilyInfo-table-children input.child-name').focus();
            $('#wphrmEmployeeFamilyInfo_form .form-body .children-info-wrapper #children-info-message').removeClass('display-hide');
            setTimeout(function(){
                $('#wphrmEmployeeFamilyInfo_form .form-body .children-info-wrapper #children-info-message').addClass('display-hide');
            }, 5000);
        }
    });
    
    //bulk delete
    $('.btn-delete-selected-notifications').on('click', function(e) {
        var atLeastOneIsChecked = $('input[name="bulk_delete_notifications[]"]:checked').length > 0;
        if( atLeastOneIsChecked ) {
            if( confirm( "Are you sure you want to delete" ) ) {
                var formData = new FormData(jQuery("#wphrm_delete_selected_notifications")['0']);
                formData.append('action', 'WPHRMBulkDelete');
                jQuery.ajax({
                    method: "POST",
                    url: ajaxurl,
                    contentType: false,
                    cache: false,
                    processData: false,
                    data: formData,
                    success: function (output) {
                        var data = JSON.parse(output);
                        if( data.success ) {
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        }
                    }
                });
                return false;
            } else {
                return false;
            }
        } else {
            alert("Please select atleast one item from the table");
            return false;
        }  
    });
    $('.btn-delete-selected-designation').on('click', function(e) {
        var atLeastOneIsChecked = $('input[name="bulk_delete_designation[]"]:checked').length > 0;
        if( atLeastOneIsChecked ) {
            if( confirm( "Are you sure you want to delete" ) ) {
                var formData = new FormData(jQuery("#wphrm_delete_selected_designations")['0']);
                formData.append('action', 'WPHRMBulkDelete2');
                jQuery.ajax({
                    method: "POST",
                    url: ajaxurl,
                    contentType: false,
                    cache: false,
                    processData: false,
                    data: formData,
                    success: function (output) {
                        var data = JSON.parse(output);
                        if( data.success ) {
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        }
                    }
                });
                return false;
            } else {
                return false;
            }
        } else {
            alert("Please select atleast one item from the table");
        }
    });
    $('.simplex-portal_page_wphrm-holidays').on('click', '.btn-delete-selected-holiday', function(e) {
        var selected = [];
        if( $(this).hasClass('per_month') ) {
            var checkboxes = $('.wphrm_delete_selected_holiday_per_month input[name="to_delete_holiday_per_month[]"]:checked');
        } else {
            var checkboxes = $('.wphrm_delete_selected_holiday_per_year input[name="to_delete_holiday_per_year[]"]:checked');
        }
        checkboxes.each(function() {
            selected.push( $(this).val() );
        });
        var atLeastOneIsChecked = checkboxes.length > 0;
        if( atLeastOneIsChecked ) {
            if( confirm( "Are you sure you want to delete" ) ) {
                $.get(
                    ajaxurl, {
                        action: 'WPHRMBulkDeleteHoliday',
                        to_delete_holiday_per_year: selected
                    }, function( res ) {
                        var data = JSON.parse(res);
                        if( data.success ) {
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        }
                    }, 'html'
                );
                return false;
            } else {
                return false;
            }
        } else {
            alert("Please select atleast one item from the table");
        }
    });
    
    //check if employee has child
    $('.wphrm_employee_has_child').click(function() {
        var child = $("input[name='wphrm_employee_has_child']:checked").val();
        if( child == 'Yes' ) {
            $('.wphrmEmployeeFamilyInfo-table-children input[name="child-name[]"]').prop('required', true);
            $('.wphrmEmployeeFamilyInfo-table-children input[name="child-age[]"]').prop('required', true);
            $('.wphrmEmployeeFamilyInfo-table-children input[name="family-member-nationality[]"]').prop('required', true);
        } else {
            $('.wphrmEmployeeFamilyInfo-table-children input[name="child-name[]"]').removeAttr('required');
            $('.wphrmEmployeeFamilyInfo-table-children input[name="child-age[]"]').removeAttr('required');
            $('.wphrmEmployeeFamilyInfo-table-children input[name="family-member-nationality[]"]').removeAttr('required');
        }
    });
    
    //disable half day if medical leave
    $('#wphrm_leavetyped').on('change', function() {
        var leave = $(this).val();
        $('#wphrm_leavedate').closest('.col-md-5').find('.description').text('Set Full-day or Half-day on this date.');
        if( leave == 37 || leave == 38 || leave == 39 || leave == 41 || leave == 42 || leave == 43 || leave == 63 || leave == 64 ) {
            $('#wphrm_user_leave_applications_frm .bootstrap-switch').addClass('hide');
            $('.maternity-options-holder').addClass('hide');
            $('.maternity-wrapper').addClass('hide');
            $('#wphrm_leavedate_to').closest('.form-group').removeClass('hide');
        } else if( leave == 45 || leave == 48 || leave == 49 || leave == 50 ) {
            $('#wphrm_leavedate_to').closest('.form-group').addClass('hide');
            $('#wphrm_user_leave_applications_frm .bootstrap-switch').addClass('hide');
            $('.maternity-wrapper').removeClass('hide');
            $('.leave-medical-claim').addClass('hide');
        } else {
            $('#wphrm_user_leave_applications_frm .bootstrap-switch').removeClass('hide');
            $('#wphrm_leavedate_to').closest('.form-group').removeClass('hide');
            $('.maternity-options-holder').addClass('hide');
            $('.maternity-wrapper').addClass('hide');
            $('.leave-medical-claim').addClass('hide');
        }
    });
    
    //maternity leave validation
    $('#wphrm_due_date').on('change', function() {
        var ddate = $(this).val();
        var due = ddate.split('-');
        var startdate = new Date(
        parseInt(
            due[2], 10),
            parseInt(due[1], 10) - 1,
            parseInt(due[0], 10)
        );
        startdate.setDate(startdate.getDate() - 28);
        var new_date = ("0" + startdate.getDate()).slice(-2) + "-" + ("0" + (startdate.getMonth() + 1)).slice(-2) + "-" + startdate.getFullYear();
        var day = ((parseInt(due[0]) + 3) > 10) ? (parseInt(due[0]) + 3) : '0' + (parseInt(due[0]) + 3);
        var ndate = new_date.split('-');
        
        //for default option
        var leave_limit = $('#wphrm_user_leave_applications_frm .wphrm_leave_limit').val();
        var dmy = ddate.split("-");
        var joindate = new Date(
        parseInt(
            dmy[2], 10),
            parseInt(dmy[1], 10) - 1,
            parseInt(dmy[0], 10)
        );
        joindate.setDate(joindate.getDate() + parseInt(leave_limit) + 6);
        var ending_date = ("0" + joindate.getDate()).slice(-2) + "-" + ("0" + (joindate.getMonth() + 1)).slice(-2) + "-" + joindate.getFullYear();
        var end_date = ending_date.split('-');
        
        //for mutual option
        var mutualdate = new Date(
        parseInt(
            dmy[2], 10),
            parseInt(dmy[1], 10) - 1,
            parseInt(dmy[0], 10)
        );
        mutualdate.setDate(mutualdate.getDate() + 54);
        var mutual_date = ("0" + mutualdate.getDate()).slice(-2) + "-" + ("0" + (mutualdate.getMonth() + 1)).slice(-2) + "-" + mutualdate.getFullYear();
        var mutual = mutual_date.split('-');
        
        var month = {
            '1' : 'January',
            '2' : 'February',
            '3' : 'March',
            '4' : 'April',
            '5' : 'May',
            '6' : 'June',
            '7' : 'July',
            '8' : 'August',
            '9' : 'September',
            '10' : 'October',
            '11' : 'November',
            '12' : 'December'
        };
        
        var start = 'Your start date should be between ' + ndate[0] + ' ' + month[parseInt(ndate[1])] + ' ' + ndate[2] + ' and ' + due[0] + ' ' + month[parseInt(due[1])] + ' ' + due[2]; 
        $('#wphrm_leavedate').closest('.col-md-5').find('.description').text(start);
        $('#wphrm_leavedate').val(new_date);
        $('#wphrm_leavedate_to').val(ending_date);
        $('#wphrm_leavedate_to').trigger('change');
        
        $('.maternity-options .default-text').html('By default: Take ' + (leave_limit / 6) + ' weeks continuosly starting 4 weeks before delivery');
        
        var default_option = 'Starting from <strong>' + ndate[0] + ' ' + month[parseInt(ndate[1])] + ' ' + ndate[2] + '</strong> to <strong>' + end_date[0] + ' ' + month[parseInt(end_date[1])] + ' ' + end_date[2] + '<strong>';
        $('.maternity-options-holder').removeClass('hide');
        $('.maternity-options-holder .maternity-options .default-date').html(default_option);
        
        var mutual_option = '<ul><li>First 8 weeks from <strong>' + ndate[0] + ' ' + month[parseInt(ndate[1])] + ' ' + ndate[2] + '</strong> to <strong>' + mutual[0] + ' ' + month[parseInt(mutual[1])] + ' ' + mutual[2] + '</strong></li></ul>';
        $('.maternity-options-holder .maternity-options .mutual-date').html(mutual_option);
        $('#wphrm_user_leave_applications_frm .mo_default_date').val(ending_date);
        $('#wphrm_user_leave_applications_frm .mo_mutual_date').val(mutual_date);
    });
    // maternity option
    $('.maternity-options input[name="maternity-option"]').change(function() {
        var chck = $(this).val();
        var dd = $('#wphrm_user_leave_applications_frm .mo_default_date').val();
        var md = $('#wphrm_user_leave_applications_frm .mo_mutual_date').val();
        if( chck == 'mutual' ) {
            $('#wphrm_leavedate_to').val(md);
        } else {
            $('#wphrm_leavedate_to').val(dd);
        }
        $('#wphrm_leavedate_to').trigger('change');
    });
    
    Date.prototype.toInputFormat = function() {
       var yyyy = this.getFullYear().toString();
       var mm = (this.getMonth()+1).toString(); // getMonth() is zero-based
       var dd  = this.getDate().toString();
       return (dd[1]?dd:"0"+dd[0]) + "-" + (mm[1]?mm:"0"+mm[0]) + "-" + yyyy; // padding
    };

    /* for  employee work permit*/    
    var wphrm_employee_work_permit_form = $('.wphrm_employee_work_permit_form');
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

    /* Passport and FIN Expiration */
    var passport_expiration = $('#passport_expiration_remaining').val();
    var fin_expiration = $('#fin_expiration_remaining').val();
    if( passport_expiration < 7 && passport_expiration != 0 || fin_expiration < 7 && fin_expiration != 0 ) {
        $('#fin_passport_expiration').modal('show');
    }

    var emp_has_expiration = $('#emp_has_expiration').val();
    if( emp_has_expiration > 0 ) {
        $('#emp_fin_passport_expiration').modal('show');
    }

});


//add new leave entitlement
jQuery("#wphrm_add_leaveentitlement_form").on('submit', function() {
    jQuery('.preloader-custom-gif').show();
    jQuery('.preloader ').show();
    var ajax_url =  jQuery(this).data('ajax_url');
    var formData = new FormData(jQuery("#wphrm_add_leaveentitlement_form")['0']);
    formData.append('action', 'WPHRMLeaveEntitlement');
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
                jQuery('#wphrm_add_leaveentitlement_success').removeClass('display-hide');
                jQuery("#wphrm_yos").val("");
                jQuery("#wphrm_staff").val("");
                jQuery("#wphrm_supervisor").val("");
                jQuery("#wphrm_manager").val("");
                jQuery("#wphrm_senior_manager").val("");
                window.setTimeout(function () {
                    window.location.reload();
                }, 1500);
                jQuery('#employee_list').removeClass('active');
                jQuery('#employee_level').addClass('active');

            } else {alert(data.success)
                jQuery('.preloader-custom-gif').hide();
                jQuery('.preloader ').hide();
                jQuery('#wphrm_add_leaveentitlement_error').html(data.errors);
                jQuery('#wphrm_add_leaveentitlement_error').removeClass('display-hide');
                jQuery("#wphrm_add_leaveentitlement_error").css('color', 'red');
            }
        }
    });
    return false;
});
/* for edit leave entitlement modul */
function leaveentitlementEdit(id, yos, staff, supervisor, manager, senior_manager) {
    jQuery("#wphrm_yos_edit").val(yos);
    jQuery("#wphrm_staff_edit").val(staff);
    jQuery("#wphrm_supervisor_edit").val(supervisor);
    jQuery("#wphrm_manager_edit").val(manager);
    jQuery("#wphrm_senior_manager_edit").val(senior_manager);
    
    var wphrm_edit_leaveentitlement_form = jQuery("#wphrm_edit_leaveentitlement_form");
    wphrm_edit_leaveentitlement_form.validate({
        rules: {
            
        },
        submitHandler: function (wphrm_edit_leaveentitlement_form) {
            jQuery('.preloader-custom-gif').show();
            jQuery('.preloader ').show();
            var formData = new FormData(jQuery(wphrm_edit_leaveentitlement_form)['0']);
            formData.append('action', 'WPHRMLeaveEntitlement');
            formData.append('wphrm_leaveentitlement_id', id);
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
                        jQuery('#wphrm_edit_leaveentitlement_success').removeClass('display-hide');

                        window.setTimeout(function () {
                            window.location.reload();
                        }, 1500);
                    } else {
                        jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                        jQuery('#wphrm_edit_leaveentitlement_error').html(data.errors);
                        jQuery('#wphrm_edit_leaveentitlement_error').removeClass('display-hide');
                        jQuery("#wphrm_edit_leaveentitlement_error").css('color', 'red');
                    }
                }
            });
        }
    });
}