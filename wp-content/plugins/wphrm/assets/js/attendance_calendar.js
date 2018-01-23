var Attendance_Calendar = function () {
    return {
        //main function to initiate the module
        init: function () {
            Attendance_Calendar.initAttendance_Calendar();
        },
        initAttendance_Calendar: function () {
           
            var date = new Date();
            var d = date.getDate();
            var m = date.getMonth();
            var y = date.getFullYear();
            var h = {};
            changeMonthYear();
            if (jQuery('#Attendance_Calendar').parents(".portlet").width() <= 720) {
                jQuery('#Attendance_Calendar').addClass("mobile");
                h = {
                    right: 'title, prev, next',
                    center: '',
                    left: 'month, Current'
                };
            } else {
                jQuery('#Attendance_Calendar').removeClass("mobile");
                h = {
                    right: 'title',
                    center: '',
                    left: 'month, Current, prev,next'
                };
            }
            if (jQuery('#Attendance_Calendar').parents(".portlet").width() <= 720) {
                jQuery('#Attendance_Calendar').addClass("mobile");
                h = {
                    left: 'title, prev, next',
                    center: '',
                    right: 'Current,month'
                };
            } else {
                jQuery('#Attendance_Calendar').removeClass("mobile");
                h = {
                    left: 'title',
                    center: '',
                    right: 'prev,next,Current,month'
                };
            }
            var ajax_url = ajaxurl;
            var employee_id = jQuery('#employee_id').val();
          
            wphrm_attendance_data = {
                'action': 'WPHRMEmployeeAttendanceData',
                'employee_id': employee_id,
            }
            jQuery.post(ajax_url, wphrm_attendance_data, function (data){
               var response = JSON.parse(data);
            jQuery('#Attendance_Calendar').fullCalendar('destroy'); // destroy the calendar
            jQuery('#Attendance_Calendar').fullCalendar({//re-initialize the calendar
               
                events: response
            });
            
        });
        }
    };
}();
