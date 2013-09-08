<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelitem library
jimport('joomla.application.component.modelitem');

class FleetMatrixModelBase extends JModelItem
{
	/**
	 * @var object item
	 */
	protected $item;
    var $model_key;
    var $pk_name = "id";

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication();
		$id = JRequest::getInt($this->pk_name);
		$this->setState(strtolower($this->model_key).'.'.$this->pk_name, $id);

        $cmd = JRequest::getCmd('cmd', '');
        $this->setState(strtolower($this->model_key).'.cmd', $cmd);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
		parent::populateState();
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = '', $prefix = 'FleetMatrixTable', $config = array())
	{
        if ($type == '') {
            $type = $this->model_key;
        }
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getItem()
	{
        $cmd = $this->getState(strtolower($this->model_key).'.cmd');
        if ($cmd == 'add') {
            $this->item = 'add';
        }
        return $this->item;
	}
}
