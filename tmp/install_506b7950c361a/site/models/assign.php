<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require(JPATH_COMPONENT . DS . 'models' . DS . 'base.php');

class FleetMatrixModelAssign extends FleetMatrixModelBase
{
    var $model_key = 'Assign';

	public function getItem()
	{
        parent::getItem();
		return $this;
	}

    public function getTemplate($default=NULL) {
        $cmd = $this->getState(strtolower($this->model_key).'.cmd');
        if ($cmd) {
            return $cmd;
        }
        return $default;
    }
}
