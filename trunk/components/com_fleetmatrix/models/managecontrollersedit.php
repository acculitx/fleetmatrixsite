<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require(JPATH_COMPONENT . DS . 'models' . DS . 'baseedit.php');

class FleetMatrixModelManagecontrollersEdit extends FleetMatrixModelBaseEdit
{
    var $model_key = 'ManageControllers';
    protected $table_name = '#__fleet_subscription';

	public function updItem($data)
	{
        var_dump($_REQUEST);
        die;

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
}