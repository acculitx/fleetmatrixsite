<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

class FleetMatrixBaseView extends JView
{
    protected $edit_model = null;
    protected $list_model = null;
    var $model_key;
    var $pk_name;
    var $pk_val;

	// Overwriting JView display method
	function display($tpl = null)
	{
		// Assign data to the view
		$this->item = $this->get('Item');

        if ($this->item && !is_a($this->item, 'JModel')) {
            $tpl = 'edit';
            $this->setModel($this->edit_model, true);
            $this->edit_model->setItem($this->item);
    		$this->form	= $this->get('Form');
            if (!is_string($this->item)) {
                $pk_name = $this->edit_model->pk_name;
                $this->pk_name = $pk_name;
                if (property_exists($this->item, $pk_name)) {
                    $this->pk_val = $this->item->$pk_name;
                }
            }
        } else {
            $this->setModel($this->list_model, true);
       		$items = $this->get('Items');
    		$pagination = $this->get('Pagination');

    		$this->items = $items;
            $this->pagination = $pagination;
        }

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
            echo "<font color='red'>".implode('<br />', $errors).'</font>';
			/*JError::raiseError(500, implode('<br />', $errors));*/
			return false;
		}

        return parent::display($tpl);

	}

    function getRoute() {
        return 'index.php?option=com_fleetmatrix&view='.strtolower($this->model_key);
    }

    function setEditModel($edit_model) {
        $this->edit_model = $edit_model;
    }

    function setListModel($list_model) {
        $this->list_model = $list_model;
    }
}
