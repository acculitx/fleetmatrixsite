<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require(JPATH_COMPONENT . DS . 'models' . DS . 'base.php');

class FleetMatrixModelMap extends FleetMatrixModelBase
{
    var $model_key = 'Map';

	public function getItem()
	{
        parent::getItem();

		if (!isset($this->item) || FALSE===$this->item)
		{
			$id = $this->getState('map.id');
			$this->_db->setQuery($this->_db->getQuery(true)
				->from('fleet_trip as h')
				->select('h.*')
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
