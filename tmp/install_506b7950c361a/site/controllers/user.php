<?php

// No direct access.
defined('_JEXEC') or die;

require(JPATH_COMPONENT . DS . 'controllers' . DS . 'base.php');

class FleetMatrixControllerUser extends FleetMatrixControllerBase
{
    var $model_key = 'User';
}