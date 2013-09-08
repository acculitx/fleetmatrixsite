<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');

class FleetMatrixModelBaseList extends JModelList
{
    var $fields = 'id, name';
    var $table_name;
    var $model_key = 'model_list';

	protected function populateState()
	{
		$app = JFactory::getApplication();
        $cmd = JRequest::getCmd('cmd', '');
        $window = JRequest::getCmd('window', '7');
        $this->setState(strtolower($this->model_key).'.cmd', $cmd);
        $this->setState(strtolower($this->model_key).'.window', $window);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);
		parent::populateState();
	}

	protected function getListQuery()
	{
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true)
            ->select($this->fields)
            ->from($this->table_name.' as h');
		return $query;
	}
}