drop view if exists trips;
create view trips 
as
SELECT distinct s.name as vehicle_name, 
  DATE_ADD(h.start_date, 
  INTERVAL h.time_zone HOUR) as trip_start, 
  h.odo_end - h.odo_start as miles, 
  b.name as assigned_driver,
  a.name as group_name, 
  aa.name as company_name, 
  redflag.hard_turns_hard_count as turns_hard,
  redflag.hard_turns_severe_count as turns_severe,
  redflag.accel_hard_count as accel_hard,
  redflag.accel_severe_count as accel_severe,
  redflag.decel_hard_count as decel_hard,
  redflag.decel_severe_count as decel_severe,
  sScore.hard_count as speed_hard,
  sScore.severe_count as speed_severe,
  d.driver_id, 
  h.id as trip_id,  
  idle.idle_time
FROM fleet_trip as h
LEFT JOIN giqwm_fleet_trip_subscription as e on e.trip_id = h.id
LEFT OUTER JOIN giqwm_fleet_subscription as s on e.subscription_id = s.id
LEFT JOIN giqwm_fleet_trip_driver as d on h.id = d.trip_id
LEFT JOIN giqwm_fleet_driver as b on d.driver_id = b.id
LEFT JOIN giqwm_fleet_entity as a on b.entity_id = a.id
LEFT JOIN fleet_idletime as idle ON h.id = idle.trip_id
LEFT JOIN giqwm_fleet_entity as aa on a.parent_entity_id = aa.id
LEFT JOIN fleet_redflag_report as redflag ON h.id = redflag.tripid
LEFT JOIN fleet_redflag_speed_report as sScore ON h.id = sScore.tripid
WHERE b.visible AND UNIX_TIMESTAMP(h.end_date)-UNIX_TIMESTAMP(h.start_date)>60 
  AND h.end_date > DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY h.id
ORDER BY h.end_date DESC;
