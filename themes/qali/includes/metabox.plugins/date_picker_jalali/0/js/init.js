var DateTimePickerPersian = function () {

    return {

        init: function () {
            this.date_time_picker_init($('.datetimepicker_persian'));

        },
        after_clone: function (selector) {
            this.date_time_picker_init(selector);
        },
        jalaliToGregorian: function (j_y, j_m, j_d, type) {

            Dates_Number = {
                g_days_in_month: [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31],
                j_days_in_month: [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29]
            };

            j_days_in_month : [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];
            j_y = parseInt(j_y);
            j_m = parseInt(j_m);
            j_d = parseInt(j_d);
            var jy = j_y - 979;
            var jm = j_m - 1;
            var jd = j_d;
            var j_day_no = 365 * jy + parseInt(jy / 33) * 8 + parseInt((jy % 33 + 3) / 4);
            for (var i = 0; i < jm; ++i)
                j_day_no += Dates_Number.j_days_in_month[i];
            j_day_no += jd;
            var g_day_no = j_day_no + 79;
            var gy = 1600 + 400 * parseInt(g_day_no / 146097);
            g_day_no = g_day_no % 146097;

            var leap = true;
            if (g_day_no >= 36525) {
                g_day_no--;
                gy += 100 * parseInt(g_day_no / 36524);
                g_day_no = g_day_no % 36524;

                if (g_day_no >= 365)
                    g_day_no++;
                else
                    leap = false;
            }

            gy += 4 * parseInt(g_day_no / 1461);
            g_day_no %= 1461;

            if (g_day_no >= 366) {
                leap = false;

                g_day_no--;
                gy += parseInt(g_day_no / 365);
                g_day_no = g_day_no % 365;
            }

            for (var i = 0; g_day_no >= Dates_Number.g_days_in_month[i] + (i == 1 && leap); i++)
                g_day_no -= Dates_Number.g_days_in_month[i] + (i == 1 && leap);
            var gm = i + 1;
            var gd = g_day_no + 1;

            if (type == '1')
                return gy + '-' + gm + '-' + gd;
            else
                return [gy, gm, gd];

        },
        date_t_timestamp: function (myDate, date_only) {

            if (date_only == true) {
                myDate = myDate + ' 00:00';
            }

            myDate = myDate + ':00';
            //.log('myDate: ' + myDate);
            var result = myDate.split('-');
            //console.log('asdasd' + result[2].split(' ')[0]);
            Gregorian = this.jalaliToGregorian(result[0], result[1], result[2].split(' ')[0], 1);
            Gregorian = Gregorian.split('-');
            jQuery.each(Gregorian, function (index, value) {
                if (index > 0)
                    Gregorian[index] = ('0' + value).slice(-2);
            });
            Gregorian = Gregorian.join('-');
            Gregorian = Gregorian + ' ' + result[2].split(' ')[1];
            var timestamp = new Date(Gregorian).getTime();
            return timestamp.toString().slice(0, -3);

        },
        selector_correct_val: function (selector, date, date_only) {
            $(selector).val(this.date_t_timestamp(date, date_only))
        },
        date_time_picker_init: function (selector) {

            selector.each(function (index, value) {
                var This = $(value);
                if (This.attr('data-dateonly') == 'true') {
                    This.datepicker({
                        dateFormat: "yy-mm-dd",
                        isRTL: true,
                        changeMonth: true,
                        changeYear: true,
                        onSelect: function (dateText) {
                            if (This.attr('data-aimtime')) {
                                DateTimePickerPersian.selector_correct_val(This.attr('data-aimtime'), this.value, true);
                                //console.log(This.attr('data-aimtime'));
                            }
                        }
                    });
                } else {
                    This.datetimepicker({
                        timeFormat: "HH:mm",
                        dateFormat: "yy-mm-dd",
                        stepHour: 1,
                        isRTL: true,
                        stepMinute: 10,
                        stepSecond: 10,
                        changeMonth: true,
                        changeYear: true,
                        onSelect: function (dateText) {
                            if (This.attr('data-aimtime')) {
                                DateTimePickerPersian.selector_correct_val(This.attr('data-aimtime'), this.value, false);
                                //console.log(This.attr('data-aimtime'));
                            }
                        }
                    });
                }
            });

        },
    }
}();

$(document).ready(function ($) {
    DateTimePickerPersian.init();
    /*$('#wpbody').on('clone', function (event) {
        //console.log(event);
        $('#wpbody').find('.datetimepicker_persian').each(function () {
            DateTimePickerPersian.after_clone($(this));
        })

    });*/
});


