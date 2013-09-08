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
		// Display the view
		parent::display($tpl);
	}
}

