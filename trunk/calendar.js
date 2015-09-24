/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

months = {
    'Jan' : '1',
    'Feb' : '2',
    'Mar' : '3',
    'Apr' : '4',
    'May' : '5',
    'Jun' : '6',
    'Jul' : '7',
    'Aug' : '8',
    'Sep' : '9',
    'Oct' : '10',
    'Nov' : '11',
    'Dec' : '12'
};
var SCRIPT_REGEX = /<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi;

// D:\wamp21\www\FleetMatrix_site\components\com_fleetmatrix\models\calendar.php
// http://localhost/FleetMatrix_site/component/com_fleetmatrix/models/calendar.php
function handlePrev($) {
    var calendarTitle = jQuery('#calendar-title').text();
    // 2015 May, split this by spoace
    var year = parseInt(calendarTitle.substr(0, calendarTitle.indexOf(' ')), 10); 
    var monthName = calendarTitle.substr(calendarTitle.indexOf(' ') + 1); 
    // convert it to number
    var month = parseInt(months[monthName], 10);
    // compute the previous month and year
    if (month === 1) {
        month = 12;
        year = year - 1;
    } else {
        month = month - 1;
    }

    jQuery.ajax({
        url: '../../components/com_fleetmatrix/models/calendar.php',
        type: 'post',
        data: { "year": year, "month": month},
        success: function(response) {
            jQuery('#calendarContainer').html(response);
            jQuery('.dateMessage').hide();
            jQuery('.calendarDate').click(handleDate);
            jQuery('.prev').click(handlePrev);
            jQuery('.next').click(handleNext);
        }
    });
}

function handleNext($) {
    var calendarTitle = jQuery('#calendar-title').text();
    // 2015 May, split this by spoace
    var year = parseInt(calendarTitle.substr(0, calendarTitle.indexOf(' ')), 10); 
    var monthName = calendarTitle.substr(calendarTitle.indexOf(' ') + 1); 
    // convert it to number
    var month = parseInt(months[monthName], 10);
    // compute the previous month and year
    if (month === 12) {
        month = 1;
        year = year + 1;
    } else {
        month = month+ 1;
    }

    jQuery.ajax({
        url: '../../components/com_fleetmatrix/models/calendar.php',
        type: 'post',
        data: { "year": year, "month": month},
        success: function(response) {
            jQuery('#calendarContainer').html(response);
            jQuery('.dateMessage').hide();
            jQuery('.calendarDate').click(handleDate);
            jQuery('.prev').click(handlePrev);
            jQuery('.next').click(handleNext);
        }
    });
}

function handleIcon($) {
    jQuery('.dateMessage').hide();
    jQuery('#calendarContainer').slideToggle( "slow" );
}

function handleDate(event) {
    var selectedDateString = event.target.id.substring(3);
    var selectedDate = new Date(selectedDateString);
    var now = new Date();
    // check if the selected date is before current date
    if (now.getTime() - selectedDate.getTime() <= 0) {
        jQuery('#runningMessage').hide();
        jQuery('#dateErrorMessage').show();
        return;
    } else {
        jQuery('#dateErrorMessage').hide();
        jQuery('#runningMessage').show();
    }
    var timeDiff = Math.abs(now.getTime() - selectedDate.getTime());
    var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 
    document.location.href = '/component/fleetmatrix/?view=reports&cmd=totalscore&window=' + diffDays;
}

jQuery(document).ready(function() {
    jQuery('.calendarIcon').click(handleIcon);
    jQuery('.prev').click(handlePrev);
    jQuery('.next').click(handleNext);
    jQuery('.calendarDate').click(handleDate);
    jQuery('#calendarContainer').hide();
    jQuery('.dateMessage').hide();
});


