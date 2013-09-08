<?php

// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla table library
jimport('joomla.database.table');

class FleetMatrixTableUser extends JTable
{
    var $user_id = 0;
    var $entity_id = 0;
    var $phone = null;
    var $fax = null;
    var $entity_type = 0;

	function __construct(&$db)
	{
		parent::__construct('#__fleet_user', 'user_id', $db);
	}

}
