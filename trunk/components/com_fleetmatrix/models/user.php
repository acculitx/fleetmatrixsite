<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require(JPATH_COMPONENT . DS . 'models' . DS . 'base.php');

class FleetMatrixModelUser extends FleetMatrixModelBase
{
    var $model_key = 'User';

	public function getItem()
	{
        parent::getItem();

		if (!isset($this->item))
		{
			$id = $this->getState('user.id');
			$this->_db->setQuery($this->_db->getQuery(true)
				->from('#__fleet_user as h')
				->leftJoin('#__users as a ON h.id=a.id')
				->leftJoin('#__fleet_entity as c ON h.entity_id=c.id')
                ->select('h.*, a.username, a.name, a.email')
				->where('h.id=' . (int)$id));
			if (!$this->item = $this->_db->loadObject())
			{
				$this->setError($this->_db->getErrorMsg());
			}
			else
			{
                // success
			}
		}
		return $this->item;
	}
}
