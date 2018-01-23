var Dashboard_Calendar = function () {
    return {
//main function to initiate the module
        init: function () {
            Dashboard_Calendar.initAttendance_Calendar();
        },
        initAttendance_Calendar: function () {
            var date = new Date();
            var d = date.getDate();
            var m = date.getMonth();
            var y = date.getFullYear();
            var h = {};
            if (jQuery('#Dashboard_Calendar').parents(".portlet").width() <= 720) {
                jQuery('#Dashboard_Calendar').addClass("mobile");
                h = {
                    right: 'title, prev, next',
                    center: '',
                    left: 'month, today'
                };
            } else {
                jQuery('#Dashboard_Calendar').removeClass("mobile");
                h = {
                    right: 'title',
                    center: '',
                    left: 'month, today, prev,next'
                };
            }
            if (jQuery('#Dashboard_Calendar').parents(".portlet").width() <= 720) {
                jQuery('#Dashboard_Calendar').addClass("mobile");
                h = {
                    left: 'title, prev, next',
                    center: '',
                    right: 'today,month'
                };
            } else {
                jQuery('#Dashboard_Calendar').removeClass("mobile");
                h = {
                    left: 'title',
                    center: '',
                    right: 'prev,next,today,month'
                };
            }
            var ajax_url = ajaxurl;
            wphrm_dashboard_data = {
                'action': 'WPHRMEmployeeAttendanceData',
            }
            jQuery.post(ajax_url, wphrm_dashboard_data, function (data) {
              var response = JSON.parse(data);
               if(response != ''){
                jQuery('#Dashboard_Calendar').fullCalendar('destroy'); // destroy the calendar
                jQuery('#Dashboard_Calendar').fullCalendar({//re-initialize the calendar                    
                    events: response,
                    eventClick: function (event ,element) {  
                        if (event.url) {
                            window.href(event.url);
                            return false;
                        }
                    }
                });
            }else{
                jQuery('#Dashboard_Calendar').fullCalendar({//re-initialize the calendar                    
                    events: response,
                });
            }
            });
        },
    };
}();