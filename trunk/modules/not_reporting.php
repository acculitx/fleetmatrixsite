<?php
defined('_JEXEC') or die;

$db =& JFactory::getDBO();
$query = $db->getQuery(true);

$query = $query->select('distinct b.id')
    ->from('#__fleet_subscription as b')
    ->leftJoin('#__fleet_entity as a on a.id = b.entity_id')
    ->leftJoin('#__fleet_trip_subscription as e on e.subscription_id = b.id')
    ->leftJoin('fleet_trip as h on h.id = e.trip_id')
    ->group('b.id')
    ->where('b.visible')
    //->where('h.end_date > DATE_SUB(NOW(), INTERVAL 3 HOUR')
    ->where('h.end_date > DATE_SUB(NOW(), INTERVAL 7 DAY)')
    ;

$db->setQuery($query);
$ids = $db->loadResultArray();

$query->clear();
$query = $query->select('distinct b.name')
    ->from('#__fleet_subscription as b')
    ->where('b.visible')
    ;
if ($ids) {
    $query = $query->where('id not in '.implode(',', $ids));
}
$db->setQuery($query);
$names = $db->loadResultArray();

if (sizeof($names)) {
    echo implode("<br>", $names);
} else {
    echo "All Vehicles Reporting";
}
?>
