<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require(JPATH_COMPONENT . DS . 'models' . DS . 'base.php');

class FleetMatrixModelSubscription extends FleetMatrixModelBase
{
    var $model_key = 'Subscription';

	public function getItem()
	{
        parent::getItem();

		if (!isset($this->item))
		{
            $clause = "h.*, a.name as entity, a.id as entity_id, b.name as driver, b.id as driver_id,".
                         "c.id as weight_id, CONCAT(c.min, '-', c.max) as weight";
			$id = $this->getState('subscription.id');
			$query = $this->_db->getQuery(true)
				->from('#__fleet_subscription as h')
				->leftJoin('#__fleet_entity as a ON h.entity_id=a.id')
				->leftJoin('#__fleet_driver as b ON h.driver_id=b.id')
				->leftJoin('#__fleet_weight as c ON h.weight_id=c.id')
				->select($clause)
				->where('h.id=' . (int)$id);

            $this->_db->setQuery($query);

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
