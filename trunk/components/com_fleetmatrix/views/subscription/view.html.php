<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

require(JPATH_COMPONENT . DS . 'baseview.php');

class FleetMatrixViewSubscription extends FleetMatrixBaseView
{
    var $model_key = 'subscription';
    
    // Overwriting JView display method
    function display($tpl = null) {
        
        $cmd = "";
        if (isset($_GET['cmd'])) {
            $cmd = $_GET['cmd'];
        }
        elseif (isset($_GET['view'])) {
            $cmd = $_GET['view'];
            JRequest::setVar('cmd', $cmd);
        }
        
        return parent::display($cmd);
    }

    function getRoute() {
        if ($this->item) {
            return 'index.php?option=com_fleetmatrix&view='.strtolower($this->model_key).
                '&cmd='.strtolower($this->item->getTemplate());
        } else {
            return 'index.php?option=com_fleetmatrix&view='.strtolower($this->model_key);
        }
    }
}
