<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require(JPATH_COMPONENT . DS . 'models' . DS . 'base.php');

class FleetMatrixModelManageControllers extends FleetMatrixModelBase
{
    var $model_key = 'ManageControllers';

	public function getItem()
	{
        // We always go to the add screen
        return 'add';
	}
}
