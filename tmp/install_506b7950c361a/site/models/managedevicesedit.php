<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require(JPATH_COMPONENT . DS . 'models' . DS . 'baseedit.php');

class FleetMatrixModelManageDevicesEdit extends FleetMatrixModelBaseEdit
{
    var $model_key = 'ManageDevices';
    protected $table_name = '#__fleet_subscription';

    private function calcSubId($company, $last=0) {
		$db		= $this->getDbo();
        $query	= $db->getQuery(true);
        $query->clear();

        $query->select('parent_entity_id')
            ->from('#__fleet_entity as h')
            ->where('h.id = "' . $company . '"');
        $db->setQuery((string)$query);
        $parent_id = sprintf("%03d", $db->loadResult() + 99);

        if (!$last) {

            $query->clear();
            $query->select('count(h.id)')
                ->from('#__fleet_subscription as h')
                ->leftJoin('#__fleet_entity as b on h.entity_id = b.id')
                ->where('b.parent_entity_id = "' . $company . '"');

            $db->setQuery((string)$query);
            $last = $db->loadResult();
        }
        $last += 1;
        $sub_id = sprintf("%05d", $last);

        $ret = sprintf('%s-%s', $parent_id, $sub_id);
        return $ret;
    }

    protected function removeDevice($data, $cmd) {
        $db =& $this->getDbo();

        $query	= $db->getQuery(true);
        $query->clear();

        $serials = explode(',', $data['serialnumbers']);

        foreach ($serials as $serial) {
            $serial = trim($serial);

            $query = $query->update('#__fleet_subscription')
                ->set('serial = NULL')
                ->where('serial = "'.$serial.'"');
                ;

    		$db->setQuery((string)$query);

            if (!$db->query()) {
                JError::raiseError(500, $db->getErrorMsg());
            	return false;
            }

            if ($cmd == 'RemovefromGroup') {
                $query	= $db->getQuery(true);
                $query->clear();
                $query = $query->insert("#__fleet_device_blacklist")
                    ->set('serial="'.$serial.'"')
                    ;
                $db->setQuery((string)$query);
                $db->query();
            }
        }

        return true;

    }

	public function updItem($data, $cmd)
	{
        $cmd = JRequest::getCmd('submit', 'AddtoGroup');
        if ($cmd == 'RemovefromGroup' || $cmd == 'RetireDevice' ) {
            return $this->removeDevice($data);
        }

        $serials = explode(',', $data['serialnumbers']);
        $group = $data['selectgroup'];
        $company = $data['selectcompany'];

        echo "<br />";

        // set the data into a query to update the record
		$db		=& $this->getDbo();

        $counter = 0;

        $query	= $db->getQuery(true);
        $query->clear();
        $query = $query->from("#__fleet_device_blacklist")
                ->select('serial')
                ;
        $db->setQuery((string)$query);
        $blacklist = $db->loadResultArray();

        foreach($serials as $serial) {
            $serial = trim($serial);
            if (in_array($serial, $blacklist)) {
                continue;
            }

            $query	= $db->getQuery(true);
            $query->clear();

            $subid = $this->calcSubId($company, $counter);
            $counter ++;

    		$query->insert($this->table_name)
                ->set($db->nameQuote("entity_id") . ' = ' . $db->Quote($group))
                ->set($db->nameQuote("weight_id") . ' = ' . $db->Quote(0))
                ->set($db->nameQuote("driver_id") . ' = ' . $db->Quote(0))
                ->set($db->nameQuote("name") . ' = ' . $db->Quote(''))
                ->set($db->nameQuote("visible") . ' = ' . $db->Quote(1))
                ->set($db->nameQuote("subscription_id") . ' = ' . $db->Quote($subid))
                ->set($db->nameQuote("serial") . ' = ' . $db->Quote($serial));

    		$db->setQuery((string)$query);

            if (!$db->query()) {
                JError::raiseError(500, $db->getErrorMsg());
            	return false;
            }

            $query->clear();
            $query->select("h.id, b.subscription_id")
                ->from("fleet_trip as h")
                ->join("LEFT OUTER #__fleet_trip_subscription as b on b.subscription_id = h.id")
                ->where('b.subscription_id is NULL')
                ->where('h.subscriber_id = "'.$serial.'"')
                ;
            $db->setQuery((string)$query);

            foreach ($db->loadRowList() as $row) {
                $query = <<<QUERY
                INSERT INTO `giqwm_fleet_trip_subscription` (`trip_id`, `subscription_id`)
                VALUES ("__TRIPID__", "__SUBID__")
QUERY;
                $query = str_replace('__TRIPID__', $row[0], $query);
                $query = str_replace('__SUBID__', $row[1], $query);
                $db->setQuery((string)$query);
                $db->query();
            }

        }
       	return true;
	}
}