<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

require(JPATH_COMPONENT . DS . 'baseview.php');

class FleetMatrixViewEntity extends FleetMatrixBaseView
{
    var $model_key = 'entity';
}
