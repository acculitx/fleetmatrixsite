<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require(JPATH_COMPONENT . DS . 'baseview.php');

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

class FleetMatrixViewAssign extends FleetMatrixBaseView
{
    var $model_key = 'assign';

	// Overwriting JView display method
	function display($tpl = null)
	{
		$this->item = $this->get('Item');
        #$tpl = $this->item->getTemplate($tpl);

        $this->company = JRequest::getInt('company', 0);
        $this->group = JRequest::getInt('group', 0);
        $this->vehicle = JRequest::getInt('vehicle', 0);
        $this->driver = JRequest::getInt('driver', 0);

        return parent::display($tpl);
	}

    function getRoute() {
        if ($this->item) {
            return 'index.php?option=com_fleetmatrix&view='.strtolower($this->model_key).
                '&cmd='.strtolower($this->item->getTemplate());
        } else {
            return 'index.php?option=com_fleetmatrix&view='.strtolower($this->model_key);
        }
    }

    function setEditModel($edit_model) {
        $this->company = JRequest::getInt('company', 0);
        $this->group = JRequest::getInt('group', 0);
        $this->vehicle = JRequest::getInt('vehicle', 0);
        $this->driver = JRequest::getInt('driver', 0);

        $this->edit_model = $edit_model;
    }

    function setListModel($list_model) {
        $this->list_model = $list_model;
    }
}
