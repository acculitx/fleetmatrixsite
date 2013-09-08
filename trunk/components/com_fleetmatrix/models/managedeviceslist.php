<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require(JPATH_COMPONENT . DS . 'models' . DS . 'baselist.php');

class FleetMatrixModelManageDevicesList extends FleetMatrixModelBaseList
{
    #var $fields = "h.id, h.name, h.visible, h.subscription_id";
    var $table_name = "#__fleet_subscription";
}
