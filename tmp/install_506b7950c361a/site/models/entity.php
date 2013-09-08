<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require(JPATH_COMPONENT . DS . 'models' . DS . 'base.php');

class FleetMatrixModelEntity extends FleetMatrixModelBase
{
    var $model_key = 'Entity';

	public function getItem()
	{
        parent::getItem();

		if (!isset($this->item) || FALSE===$this->item)
		{
			$id = $this->getState('entity.id');
			$this->_db->setQuery($this->_db->getQuery(true)
				->from('#__fleet_entity as h')
				->leftJoin('#__fleet_entity as c ON h.parent_entity_id=c.id')
				->select('h.*, c.name as parent_entity')
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
