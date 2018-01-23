/*Company Calendar of Notices*/
var Company_Notice_Calendar = function () {
    return {
//main function to initiate the module
        init: function () {
            Company_Notice_Calendar.initAttendance_Calendar();
        },
        initAttendance_Calendar: function () {
            var date = new Date();
            var d = date.getDate();
            var m = date.getMonth();
            var y = date.getFullYear();
            var h = {};
            if (jQuery('#Company_Notice_Calendar').parents(".portlet").width() <= 720) {
                jQuery('#Company_Notice_Calendar').addClass("mobile");
                h = {
                    right: 'title, prev, next',
                    center: '',
                    left: 'month, today'
                };
            } else {
                jQuery('#Company_Notice_Calendar').removeClass("mobile");
                h = {
                    right: 'title',
                    center: '',
                    left: 'month, today, prev,next'
                };
            }
            if (jQuery('#Company_Notice_Calendar').parents(".portlet").width() <= 720) {
                jQuery('#Company_Notice_Calendar').addClass("mobile");
                h = {
                    left: 'title, prev, next',
                    center: '',
                    right: 'today,month'
                };
            } else {
                jQuery('#Company_Notice_Calendar').removeClass("mobile");
                h = {
                    left: 'title',
                    center: '',
                    right: 'prev,next,today,month'
                };
            }
            var ajax_url = ajaxurl;
            wphrm_notice_data = {
                'action': 'WPHRMCompanyNoticeData',
            }
            jQuery.post(ajax_url, wphrm_notice_data, function (data) {
              var response = JSON.parse(data);
               if(response != ''){
                jQuery('#Company_Notice_Calendar').fullCalendar('destroy'); // destroy the calendar
                jQuery('#Company_Notice_Calendar').fullCalendar({//re-initialize the calendar
                    events: response,
                    eventClick: function (event ,element) {
                        if (event.url) {
                            window.href(event.url);
                            return false;
                        }
                    }
                });
            }else{
                jQuery('#Company_Notice_Calendar').fullCalendar({//re-initialize the calendar
                    events: response,
                });
            }
            });
        },
    };
}();


/*Department Calendar of Notices*/
var Department_Notice_Calendar = function () {
    return {
//main function to initiate the module
        init: function () {
            Department_Notice_Calendar.initAttendance_Calendar();
        },
        initAttendance_Calendar: function () {
            var date = new Date();
            var d = date.getDate();
            var m = date.getMonth();
            var y = date.getFullYear();
            var h = {};
            if (jQuery('#Department_Notice_Calendar').parents(".portlet").width() <= 720) {
                jQuery('#Department_Notice_Calendar').addClass("mobile");
                h = {
                    right: 'title, prev, next',
                    center: '',
                    left: 'month, today'
                };
            } else {
                jQuery('#Department_Notice_Calendar').removeClass("mobile");
                h = {
                    right: 'title',
                    center: '',
                    left: 'month, today, prev,next'
                };
            }
            if (jQuery('#Department_Notice_Calendar').parents(".portlet").width() <= 720) {
                jQuery('#Department_Notice_Calendar').addClass("mobile");
                h = {
                    left: 'title, prev, next',
                    center: '',
                    right: 'today,month'
                };
            } else {
                jQuery('#Department_Notice_Calendar').removeClass("mobile");
                h = {
                    left: 'title',
                    center: '',
                    right: 'prev,next,today,month'
                };
            }
            var ajax_url = ajaxurl;
            wphrm_notice_data = {
                'action': 'WPHRMDepartmentNoticeData',
                'department': jQuery('#wphrm_notice_calendar_department_selected').val(),
            }
            jQuery.post(ajax_url, wphrm_notice_data, function (data) {
              var response = JSON.parse(data);
               if(response != ''){
                jQuery('#Department_Notice_Calendar').fullCalendar('destroy'); // destroy the calendar
                jQuery('#Department_Notice_Calendar').fullCalendar({//re-initialize the calendar
                    events: response,
                    eventClick: function (event ,element) {
                        if (event.url) {
                            window.href(event.url);
                            return false;
                        }
                    }
                });
            }else{
                jQuery('#Department_Notice_Calendar').fullCalendar({//re-initialize the calendar
                    events: response,
                });
            }
            });
        },
    };
}();
