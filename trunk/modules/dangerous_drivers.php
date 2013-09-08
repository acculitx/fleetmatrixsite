<?php
defined('_JEXEC') or die;

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

function sort_items($a, $b) {
    if ($a->overall_score > $b->overall_score) {
        return -1;
    } else
    if ($b->overall_score > $a->overall_score) {
        return 1;
    }
    return 0;
}

require(JPATH_BASE.DS."components".DS."com_fleetmatrix".DS."models".DS."calculator.php");
$calculator = new ScoreCalculator();

        $user =& JFactory::getUser();
        $db =& JFactory::getDBo();
        if ($user) {
            $query = $db->getQuery(true)
                ->select('entity_type, entity_id')
                ->from('#__fleet_user')
                ->where('id = "'.$user->id.'"')
                ;
            $db->setQuery($query);
            $row = $db->loadObject();
            if ($row) {
            $query = $db->getQuery(true)
                ->select('id')
                ->from("#__fleet_entity")
                ->where('parent_entity_id = '.$row->entity_id)
                ;
            $db->setQuery($query);
            $results = $db->loadResultArray();
            switch ($row->entity_type) {
                case 1: // reseller
                    foreach($results as $id) {
                        $GLOBALS['user_companies'][] = $id;
                        $query = $db->getQuery(true)
                            ->select('id')
                            ->from("#__fleet_entity")
                            ->where('parent_entity_id = '.$id)
                            ;
                        $db->setQuery($query);
                        $groups = $db->loadResultArray();
                        foreach($groups as $gid) {
                            $GLOBALS['user_groups'][] = $gid;
                        }
                    }
                    break;
                case 2: // company
                    $GLOBALS['user_companies'][] = $row->entity_id;
                    foreach($results as $id) {
                        $GLOBALS['user_groups'][] = $id;
                    }
                    break;
                case 3: // group
                    $GLOBALS['user_groups'][] = $row->entity_id;
                    break;
                default: // ?? shouldn't happen
                    break;
            }
            }
        }

$db =& JFactory::getDBO();
$query = $db->getQuery(true);

$clause = "distinct b.name as driver_name, ".
        "a.name as group_name, s.id as vehicle_id, ".
        "a.id as group_id, a.parent_entity_id as company_id, ".
        "d.driver_id, ".
        "COUNT(h.subscriber_id) as trip_count, ".
        "'N/A' as mpg, ".
        "'N/A' as overall, ".
        "'N/A' as accel_score, ".
        "'N/A' as decel_score, ".
        "'N/A' as hard_turns, ".
        "'N/A' as speed_score, ".
        "SUM(h.odo_end - h.odo_start) as miles "
        ;
$query = $query->select($clause)
    ->from('fleet_trip as h')
    ->leftJoin('#__fleet_trip_subscription as e on e.trip_id = h.id')
    ->join('left outer', '#__fleet_subscription as s on e.subscription_id = s.id')
    ->leftJoin('#__fleet_trip_driver as d on h.id = d.trip_id')
    ->leftJoin('#__fleet_driver as b on d.driver_id = b.id')
    ->leftJoin('#__fleet_entity as a on b.entity_id = a.id')
    ->group('b.id')
    #->where('c.visible')
    ->where('b.visible')
    ;

            if (array_key_exists('user_companies', $GLOBALS) && $GLOBALS['user_companies']) {
            $query = $query->where(
                "a.parent_entity_id in (".implode(',', $GLOBALS['user_companies']).")"
            );
            }
            if (array_key_exists('user_groups', $GLOBALS) && $GLOBALS['user_groups']) {
            $query = $query->where(
                "a.id in (".implode(',', $GLOBALS['user_groups']).")"
            );
            }
$db->setQuery($query);
$rows = $db->loadObjectList();

$items = array();
foreach ($rows as $item) {
    $item->accel_score = $calculator->getDriverAccelScore($item, 7, 'accel');
    $item->decel_score = $calculator->getDriverAccelScore($item, 7, 'decel');
    $item->hard_turns = $calculator->getDriverAccelScore($item, 7, 'hard_turns');
    $item->speed_score = $calculator->getDriverSpeedScore($item, 7);
    $item->overall_score = $calculator->getOverall($item);
    $items[] = $item;
}
usort($items, "sort_items");
?><table width="100%"><?php
for ($x=0; $x<min(sizeof($items), 5); $x++) {
    printf('<tr><td width="50">%5.2f</td><td>%s</td></tr>',
           $items[$x]->overall_score, $items[$x]->driver_name);
}

?>
</table>
