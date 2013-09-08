<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require(JPATH_COMPONENT . DS . 'models' . DS . 'baselist.php');

class FleetMatrixModelDriverList extends FleetMatrixModelBaseList
{
    var $fields = "h.*, b.name as company, c.name as driver_group";
    var $table_name = "#__fleet_driver";

	protected function getListQuery()
	{
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true)
            ->select($this->fields)
            ->from($this->table_name.' as h')
     		->leftJoin('#__fleet_entity as c ON entity_id=c.id')
     		->leftJoin('#__fleet_entity as b ON b.id=c.parent_entity_id');
		return $query;
	}
}
