<?php

// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla table library
jimport('joomla.database.table');

class FleetMatrixTableWeight extends JTable
{
    var $id = 0;
    var $min = 0;
    var $max = null;
    var $compensation_table_number = null;

	function __construct(&$db)
	{
		parent::__construct('#__fleet_weight', 'id', $db);
	}

}
