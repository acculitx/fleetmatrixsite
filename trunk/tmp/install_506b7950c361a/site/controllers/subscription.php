<?php

// No direct access.
defined('_JEXEC') or die;

require(JPATH_COMPONENT . DS . 'controllers' . DS . 'base.php');

class FleetMatrixControllerSubscription extends FleetMatrixControllerBase
{
    var $model_key = 'Subscription';
}