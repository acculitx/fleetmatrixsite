<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Include dependancy of the main model form
jimport('joomla.application.component.modelform');
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
// Include dependancy of the dispatcher
jimport('joomla.event.dispatcher');

class FleetMatrixModelBaseEdit extends JModelForm
{
	/**
	 * @var object item
	 */
	protected $item;
    protected $table_name;
    var $model_key;
    var $pk_name = 'id';

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
	 * Get the data for a new qualification
	 */
	public function getForm($data = array(), $loadData = true)
	{
        // Get the form.
		$form = $this->loadForm('com_fleetmatrix.'.strtolower($this->model_key),
                                strtolower($this->model_key),
                                array('control' => 'jform', 'load_data' => true)
        );
		if (empty($form)) {
			return false;
		}
		return $form;

	}

	function &getItem()
	{
		return $this->_item;
	}

    public function setItem(&$item) {
        $this->_item = $item;
    }

    public function loadFormData() {
        if (!is_string($this->_item)) {
            $ret = get_object_vars($this->_item);
        } else {
            $ret = $this->_item;
        }
        return $ret;
    }

	public function updItem($data)
	{
        // set the variables from the passed data
        $id = $data[$this->pk_name];

        echo "<br />";

        // set the data into a query to update the record
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
        $query->clear();

        if ($id) {
    		$query->update($this->table_name);
    		$query->where($this->pk_name.' = ' . (int) $id );
        } else {
    		$query->insert($this->table_name);
        }

        $data = $this->makeDataConversions($data);

        foreach ($data as $k => $v) {
            if (false !== strpos($k, '_nosave')) { continue; }
            $query->set($db->NameQuote($k) . ' = ' . $db->Quote($v) );
        }

		$db->setQuery((string)$query);

        if (!$db->query()) {
            JError::raiseError(500, $db->getErrorMsg());
        	return false;
        } else {
        	return true;
		}
	}

    function makeDataConversions($data) {
        return $data;
    }
}