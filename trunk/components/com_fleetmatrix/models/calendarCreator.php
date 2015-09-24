<?php

include(JPATH_COMPONENT . DS . 'models' . DS . 'calendarJs.php');

// css file is set in, D:\wamp21\www\FleetMatrix_site\modules\mod_jt_menumatic\tmpl\default.php
// css file is located in, D:\wamp21\www\FleetMatrix_site\modules\mod_jt_menumatic\css\calendar.css
include(JPATH_COMPONENT . DS . 'models' . DS . 'calendar.php');

$dateToDisplayed = date('Y-m-d', strtotime("-1 days")); 
if (isset($_GET['diffDays'])) { // check if diffDays is specified within url
    $diffDaysMinusOneDay = (int) $_GET['diffDays'] + 1;
    $dateToDisplayed = date('Y-m-d', strtotime("-". $diffDaysMinusOneDay . " days")); 
} 
echo '<div id="calendarIconContainer" >'.
        '<div id="calendarIconTitle" >'.
            'Scores as of '. $dateToDisplayed. '. To change, press calendar'.
        '</div>'.
        '<div id="calendarIconImage" class="calendarIcon">'.
            '<img class="aCalendarIcon" src="images/Calendar_40.png" >'.
        '</div>'.
    '</div>';

$calendar = new Calendar();
echo '<div id="calendarContainer">'.    
    $calendar->show().
    '</div>';


