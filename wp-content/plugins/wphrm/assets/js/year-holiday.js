/*
 *
 * bic calendar
 * Autor: bichotll
 * Web-autor: bic.cat
 * Web script: http://bichotll.github.io/bic_calendar/
 * Llicència Apache
 *
 */
jQuery.fn.bic_calendar = function (options) {
    var opts = jQuery.extend({}, jQuery.fn.bic_calendar.defaults, options);
    var image_url = jQuery('.image_url').val();
    this.each(function () {
        /*** vars ***/
        //element called
        var elem = jQuery(this);
        var calendar;
        var layoutMonth;
        var daysMonthsLayer;
        var textMonthCurrentLayer = jQuery('<div class="visualmonth"></div>');
        var textYearCurrentLayer = jQuery('<div class="visualyear"></div>');
        var calendarId = "bic_calendar";
        var events = opts.events;
        var dayNames;
        if (typeof opts.dayNames != "undefined")
            dayNames = opts.dayNames;
        else
            //  dayNames = ["l", "m", "x", "j", "v", "s", "d"];
            var monthNames;
        if (typeof opts.monthNames != "undefined")
            monthNames = opts.monthNames;
        else
            monthNames =  '';
        var showDays;
        if (typeof opts.showDays != "undefined")
            showDays = opts.showDays;
        else
            showDays = true;
        var popoverOptions;
        if (typeof opts.popoverOptions != "undefined")
            popoverOptions = opts.popoverOptions;
        else
            popoverOptions = {placement: 'bottom', html: true, trigger: 'hover'};
        var tooltipOptions;
        if (typeof opts.tooltipOptions != "undefined")
            tooltipOptions = opts.tooltipOptions;
        else
            tooltipOptions = {placement: 'bottom', trigger: 'hover'};
        var reqAjax;
        if (typeof opts.reqAjax != "undefined")
            reqAjax = opts.reqAjax;
        else
            reqAjax = false;
        var enableSelect = false;
        if (typeof opts.enableSelect != 'undefined')
            enableSelect = opts.enableSelect;
        var multiSelect = false;
        if (typeof opts.multiSelect != 'undefined')
            multiSelect = opts.multiSelect;
        var firstDaySelected = '';
        var lastDaySelected = '';
        var daySelected = '';
        /*** --vars-- ***/
        function showCalendar() {
            //layer with the days of the month (literals)
            daysMonthsLayer = jQuery('<div id="monthsLayer" class="row"></div>');
            //Date obj to calc the day
            var objFecha = new Date();
            objFecha.setMonth(0);
            var year = objFecha.getFullYear();
            showMonths(year);
            var nextYearButton = jQuery('<td><a href="#"  class="button-year-next" id="loaddata"><span class="slip-icons"><img src="' + image_url + '/Arrow right.png"></span></a></td>');
            nextYearButton.click(function (e) {
                jQuery(".year_holidays").empty('');
                var loadyear = year + 1;
                var ajax_url = ajaxurl;
                wphrm_data = {
                    'action': 'WPHRMHolidayYearWise',
                    'wphrm_year': loadyear,
                }
               jQuery('.preloader-custom-gif').show();
                jQuery('.preloader ').show();
                jQuery.post(ajax_url, wphrm_data, function (response) {
                    jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                    var data = JSON.parse(response);
                      jQuery('.month-data').val(0);
                    jQuery(".year_holidays").html(data.wphrmHolidayYear);
                });
                e.preventDefault();
                year++;
                changeDate(year);
            })
            var previousYearButton = jQuery('<td><a href="#" class="button-year-previous"><span class="slip-icons"><img src="' + image_url + '/Arrow left.png"></span></a></td>');
            //event
            previousYearButton.click(function (e) {
                // alert(year);
                jQuery(".year_holidays").empty('');
                var loadyear = year - 1;
                var ajax_url = ajaxurl;
                jQuery('.preloader-custom-gif').show();
                jQuery('.preloader ').show();
                wphrm_data = {
                    'action': 'WPHRMHolidayYearWise',
                    'wphrm_year': loadyear,
                }
                jQuery.post(ajax_url, wphrm_data, function (response) {
                    jQuery('.preloader-custom-gif').hide();
                        jQuery('.preloader ').hide();
                    var data = JSON.parse(response);
                      jQuery('.month-data').val(0);
                    jQuery(".year_holidays").html(data.wphrmHolidayYear);
                });
                e.preventDefault();
                year--;
                changeDate(year);
            })
            //show the current year n current month text layer
            var headerLayer = jQuery('<table class="table header"></table>');
            var yearTextLayer = jQuery('<tr></tr>');
            var yearControlTextLayer = jQuery('<td colspan=5 class="monthAndYear span6"></td>');
            yearTextLayer.append(previousYearButton);
            yearTextLayer.append(yearControlTextLayer);
            yearTextLayer.append(nextYearButton);
            yearControlTextLayer.append(textYearCurrentLayer);
            headerLayer.append(yearTextLayer);
            calendar = jQuery('<div class="bic_calendar" id="' + calendarId + '" ></div>');
            calendar.prepend(headerLayer);
            calendar.append(daysMonthsLayer);
            elem.append(calendar);
        }
        /**
         * indeed, change month or year
         */
        function changeDate(year) {
            daysMonthsLayer.empty();
            showMonths(year);
        }
        function showMonths(year) {
            jQuery('.yeardata').val(year);
            for (i = 0; i != 12; i++) {
                if (i % 3 == false)
                    daysMonthsLayer.append(jQuery('</div><div class="row">'));
                showMonthDays(i, year);
            }
        }
        function showMonthDays(month, year) {
            layoutMonth = '';
            textMonthCurrentLayer.text(monthNames[month]);
            textYearCurrentLayer.text(year);
            var daysCounter = 1;
            var firstDay = calcNumberDayWeek(1, month, year);
            var lastDayMonth = lastDay(month, year);
            var nMonth = month + 1;
            var daysMonthLayerString = "";
        }
        /**
         * calc the number of the week day
         */
        function calcNumberDayWeek(day, month, year) {
            var objFecha = new Date(year, month, day);
            var numDia = objFecha.getDay();
            if (numDia == 0)
                numDia = 6;
            else
                numDia--;
            return numDia;
        }
        /**
         * check if a date is correct
         * 
         * @thanks http://kevin.vanzonneveld.net
         * @thanks http://www.desarrolloweb.com/manuales/manual-librerias-phpjs.html
         */
        function checkDate(m, d, y) {
            return m > 0 && m < 13 && y > 0 && y < 32768 && d > 0 && d <= (new Date(y, m, 0)).getDate();
        }
        /**
         * return last day of a date (month n year)
         */
        function lastDay(month, year) {
            var lastDayValue = 28;
            while (checkDate(month + 1, lastDayValue + 1, year)) {
                lastDayValue++;
            }
            return lastDayValue;
        }
        showCalendar();
    });
    return this;
};
