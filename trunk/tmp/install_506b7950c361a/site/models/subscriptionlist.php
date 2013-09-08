<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require(JPATH_COMPONENT . DS . 'models' . DS . 'baselist.php');

class FleetMatrixModelSubscriptionList extends FleetMatrixModelBaseList
{
    var $fields = "h.*, a.name as entity, a.id as entity_id, b.name as driver, b.id as driver_id, c.id as weight_id, CONCAT(c.min, '-', c.max) as weight";
    var $table_name = "#__fleet_subscription";
	protected function getListQuery()
	{
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true)
            ->select($this->fields)
            ->from($this->table_name.' as h')
         	->leftJoin('#__fleet_entity as a ON h.entity_id=a.id')
			->leftJoin('#__fleet_driver as b ON h.driver_id=b.id')
			->leftJoin('#__fleet_weight as c ON h.weight_id=c.id');

		return $query;
	}

}