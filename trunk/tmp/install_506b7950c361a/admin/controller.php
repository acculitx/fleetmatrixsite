<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * General Controller of FleetMatrix Component
 */
class FleetMatrixController extends JController
{
	/**
	 * display task
	 *
	 * @return void
	 */
	function display($cachable = false)
	{
		// call parent behavior
		parent::display($cachable);
	}
}
