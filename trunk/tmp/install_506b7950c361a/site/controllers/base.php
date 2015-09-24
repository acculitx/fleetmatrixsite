<?php

// No direct access.
defined('_JEXEC') or die;

// Include dependancy of the main controllerform class
jimport('joomla.application.component.controllerform');

class FleetMatrixControllerBase extends JControllerForm
{
    var $model_key;

	public function getModel($name = '', $prefix = '', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, array('ignore_request' => false));
	}

	public function submit()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$model = $this->getModel(strtolower($this->model_key).'edit');
        $view  = $this->getView(strtolower($this->model_key), 'html');

		// Get the data from the form POST
		$data = JRequest::getVar('jform', array(), 'post', 'array');

        // Now update the loaded data to the database via a function in the model
        $upditem	= $model->updItem($data);

    	// check if ok and display appropriate message.  This can also have a redirect if desired.
        if ($upditem) {
            $this->setRedirect($view->getRoute(), "<h2>".$this->model_key." has been saved</h2>");
        } else {
            echo "<h2>".$this->model_key." failed to be saved</h2>";
        }

		return true;
	}

}