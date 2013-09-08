<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require(JPATH_COMPONENT . DS . 'models' . DS . 'baseedit.php');
require(JPATH_COMPONENT . DS . 'models' . DS . 'user_create.php');

class FleetMatrixModelUserEdit extends FleetMatrixModelBaseEdit
{
    var $model_key = 'User';
    protected $table_name = '#__fleet_user';

	public function updItem($data)
	{
        $id = NULL;
        if (array_key_exists($this->pk_name, $data)) {
            $id = $data[$this->pk_name];
        }

        $postfields = array('username', 'password', 'confirmpassword', 'name', 'email');

        // set the data into a query to update the record
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
        $query->clear();
        $query2 = $db->getQuery(true);
        $query->clear();

        if ($id) {
    		$query->update($this->table_name);
    		$query->where($this->pk_name.' = ' . (int) $id );
    		$query2->update('#__users');
    		$query2->where('id = ' . (int) $id );
        } else {
            $data['id'] = createJoomlaUser($data);
    		$query->insert($this->table_name);
        }

        $data = $this->makeDataConversions($data);

        foreach ($data as $k => $v) {
            if (in_array($k, $postfields)) {
                if ($k == 'confirmpassword') {
                    continue;
                }
                if ($id) {
                    if ($k == 'password') {
                        if (!$v) {
                            continue;
                        }
                        $v = getCryptedPassword($v);
                    }
                    $query2->set($db->NameQuote($k) . ' = ' . $db->Quote($v) );
                }
                continue;
            }
            $query->set($db->NameQuote($k) . ' = ' . $db->Quote($v) );
        }

		$db->setQuery((string)$query);

        if (!$db->query()) {
            JError::raiseError(500, $db->getErrorMsg());
        	return false;
        }
        if ($id) {
            $db->setQuery((string)$query2);
            if (!$db->query()) {
                JError::raiseError(500, $db->getErrorMsg());
            	return false;
            }
		}
     	return true;
	}
}
