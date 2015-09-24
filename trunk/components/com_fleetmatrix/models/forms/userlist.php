<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require(JPATH_COMPONENT . DS . 'models' . DS . 'baselist.php');

class FleetMatrixModelUserList extends FleetMatrixModelBaseList
{
    var $fields = "h.id, h.name";
    var $table_name = "#__fleet_user";
}
