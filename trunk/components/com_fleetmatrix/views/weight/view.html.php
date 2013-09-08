<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

class FleetMatrixViewWeight extends JView
{
    var $edit_model = null;

	// Overwriting JView display method
	function display($tpl = null)
	{
		// Assign data to the view
		$this->item = $this->get('Item');

        if ($this->item) {
            $tpl = 'edit';
            $this->setModel($this->edit_model, true);
        }

		$this->form	= $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
            echo "<font color='red'>".implode('<br />', $errors).'</font>';
			/*JError::raiseError(500, implode('<br />', $errors));*/
			return false;
		}

		// Display the view
		parent::display($tpl);
	}

    function setEditModel($edit_model) {
        $this->edit_model = $edit_model;
    }
}
