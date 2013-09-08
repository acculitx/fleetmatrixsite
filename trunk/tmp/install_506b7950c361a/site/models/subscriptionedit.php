<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require(JPATH_COMPONENT . DS . 'models' . DS . 'baseedit.php');

class FleetMatrixModelSubscriptionEdit extends FleetMatrixModelBaseEdit
{
    var $model_key = 'Subscription';
    protected $table_name = '#__fleet_subscription';

    function makeDataConversions($data) {
        $data = parent::makeDataConversions($data);
        $data['visible'] = $data['visible'] == 'checked' ? 1 : 0;
        return $data;
    }

	public function updItem($data)
	{
        if (!parent::updItem($data)) {
            return false;
        }
        if (!$data['driver_id']) {
            return true;
        }

        // Assign all of the trips not assigned already to this driver.
        $db =& JFactory::getDBO();
   		$query = $db->getQuery(true);
        $query = $query->select('serial')
            ->from('#__fleet_subscription')
            ->where('id='.$data['id'])
            ;
        $db->setQuery((string)$query);
        $db->query();
        $serial = $db->loadResult();

        $query->clear();
        $query = $query->select('id')
            ->from('fleet_trip as h')
            ->join('left outer', '#__fleet_trip_driver as b on b.trip_id = h.id')
            ->where('subscriber_id = "'.$serial.'"')
            ->where('driver_id is NULL')
            ;
        $db->setQuery((string)$query);
        $db->query();
        $unassigned = $query->loadResultArray();
        if (!$unassigned) {
            $unassigned = array();
        }

        foreach($unassigned as $row) {
            $query->clear();
            $query = $query->insert('#__fleet_trip_driver')
                ->set('trip_id='.$row)
                ->set('driver_id='.$data['driver_id'])
                ;
            $db->setQuery((string)$query);
            $db->query();
        }
        return true;
    }
}