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
        if (array_key_exists('user_companies', $GLOBALS) && $GLOBALS['user_companies']) {
            $query = $query->where(
                "b.id in (".implode(',', $GLOBALS['user_companies']).")"
            );
        }
        if (array_key_exists('user_groups', $GLOBALS) && $GLOBALS['user_groups']) {
            $query = $query->where(
                "c.id in (".implode(',', $GLOBALS['user_groups']).")"
            );
        }
		return $query;
	}
}
