<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelitem library
jimport('joomla.application.component.modelitem');

class FleetMatrixModelWeight extends JModelItem
{
	/**
	 * @var object item
	 */
	protected $item;

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
        echo "in populate state";

		$app = JFactory::getApplication();
		$id = JRequest::getInt('id');
		$this->setState('weight.id', $id);

        $cmd = JRequest::getCmd('cmd', '');
        $this->setState('driver.cmd', $cmd);

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
	public function getTable($type = 'Weight', $prefix = 'FleetMatrixTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	/**
	 * Get the message
	 * @return object The message to be displayed to the user
	 */
	public function getItem()
	{
        $cmd = $this->getState('driver.cmd');
        if ($cmd == 'add') {
            $this->item = 'add';
        }

		if (!isset($this->item))
		{
			$id = $this->getState('weight.id');

			$this->_db->setQuery($this->_db->getQuery(true)
				->from('#__fleet_weight as h')
				->select('h.*')
				->where('h.id=' . (int)$id));
			if (!$this->item = $this->_db->loadObject())
			{
				$this->setError($this->_db->getErrorMsg());
			}
			else
			{
                // success
			}
		}
		return $this->item;
	}
}
