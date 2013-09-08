<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require(JPATH_COMPONENT . DS . 'models' . DS . 'baselist.php');

class FleetMatrixModelUserList extends FleetMatrixModelBaseList
{
    var $fields = "a.id, a.name, c.title, d.name as entity_name, h.*";
    var $table_name = "#__fleet_user";

	protected function getListQuery()
	{
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true)
            ->select($this->fields)
            ->from($this->table_name.' as h')
            ->leftJoin('#__users as a on a.id = h.id')
            ->leftJoin('#__user_usergroup_map as b on b.user_id = a.id')
            ->leftJoin('#__usergroups as c on c.id = b.group_id')
            ->leftJoin('#__fleet_entity as d ON h.entity_id=d.id');
		return $query;
	}
}
