<?php

// No direct access
defined('_JEXEC') or die('Restricted access');

// import Joomla table library
jimport('joomla.database.table');

class FleetMatrixTableEntity extends JTable
{
    var $id = 0;
    var $entity_type = 0;
    var $parent_entity_id = 0;
    var $name = null;
    var $contact_name = null;
    var $street_address = null;
    var $city = null;
    var $state = null;
    var $zip = null;
    var $phone = null;
    var $email = null;

	function __construct(&$db)
	{
		parent::__construct('#__fleet_entity', 'id', $db);
	}

}
