<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require(JPATH_COMPONENT . DS . 'models' . DS . 'baseedit.php');

class FleetMatrixModelReportsEdit extends FleetMatrixModelBaseEdit
{
	protected function populateState()
	{
        $cmd = JRequest::getCmd('cmd', '');
        $this->setState(strtolower($this->model_key).'.cmd', $cmd);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
		parent::populateState();
	}

	/**
	 * Get the data for a new qualification
	 */
	public function getForm($data = array(), $loadData = true)
	{
        return NULL;
        /*
        // Get the form.
		$form = $this->loadForm('com_fleetmatrix.'.strtolower($this->model_key),
                                strtolower($this->model_key),
                                array('control' => 'jform', 'load_data' => true)
        );
		if (empty($form)) {
			return false;
		}
		return $form;
        */
	}

	function &getItem()
	{
		return $this->_item;
	}

    public function setItem(&$item) {
        $this->_item = $item;
    }

    public function loadFormData() {
        return NULL;
        /*
        if (!is_string($this->_item)) {
            $ret = get_object_vars($this->_item);
        } else {
            $ret = $this->_item;
        }
        return $ret;
        */
    }

	public function updItem($data)
	{
     	return true;
	}

}
