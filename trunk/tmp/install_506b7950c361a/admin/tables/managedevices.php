<?php

// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla table library
jimport('joomla.database.table');

class FleetMatrixTableManageDevices extends JTable
{
    var $id = 0;
    var $entity_id = 0;
    var $weight_id = 0;
    var $driver_id = 0;
    var $vin = null;
    var $name = null;
    var $visible = 0;
    var $subscription_id = '';
    var $fuel_capacity = 0;
    var $serial = 0;

	function __construct(&$db)
	{
		parent::__construct('#__fleet_subscription', 'id', $db);
	}

}
