<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require(JPATH_COMPONENT . DS . 'models' . DS . 'baselist.php');

class FleetMatrixModelEntityList extends FleetMatrixModelBaseList
{
    var $fields = "h.id, h.name, c.name as entity_type";
    var $table_name = "#__fleet_entity";

	protected function getListQuery()
	{
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true)
            ->select($this->fields)
            ->from($this->table_name.' as h')
     		->leftJoin('#__fleet_entity_type as c ON entity_type=c.id');
		return $query;
	}
}
