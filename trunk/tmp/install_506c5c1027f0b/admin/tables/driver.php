<?php

// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla table library
jimport('joomla.database.table');

class FleetMatrixTableDriver extends JTable
{
    var $id = 0;
    var $visible = 0;
    var $entity_id = 0;
    var $name = null;

	function __construct(&$db)
	{
		parent::__construct('#__fleet_driver', 'id', $db);
	}

}
