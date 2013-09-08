<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

class FleetMatrixViewFleetMatrix extends JView
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		// Assign data to the view
		$this->item = $this->get('Item');

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
}
