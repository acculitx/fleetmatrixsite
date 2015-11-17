<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require(JPATH_COMPONENT . DS . 'models' . DS . 'base.php');

class FleetMatrixModelDriver extends FleetMatrixModelBase
{
    var $model_key = 'Driver';

    public function getItem() {
        parent::getItem();

        if (!isset($this->item)) {
            $id = $this->getState('driver.id');

            $query = $this->_db->getQuery(true)
                    ->from('#__fleet_driver as h')
                    ->leftJoin('#__fleet_entity as c ON h.entity_id=c.id')
                    ->select('h.*, c.name as entity')
                    ->where('h.id=' . (int)$id);

            $this->_db->setQuery($query);

            if (!$this->item = $this->_db->loadObject()) {
                $this->setError($this->_db->getErrorMsg());
            }
        }

        return $this->item;
    }

}
