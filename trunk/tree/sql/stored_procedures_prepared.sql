drop procedure if exists get_containers;
commit;

DELIMITER //

CREATE PROCEDURE get_containers()
BEGIN
  SELECT id, name,
  (case parent_entity_id WHEN 0 THEN null ELSE parent_entity_id END) as id_parent,
  0 as child_num
  FROM giqwm_fleet_entity;
END //

DELIMITER ;

drop procedure if exists get_items;
commit;

DELIMITER //

CREATE PROCEDURE get_items(IN containers VARCHAR(255))
BEGIN
   SET @query = "SELECT DISTINCT id, name, driver_id, serial
FROM giqwm_fleet_subscription sub, item_container
WHERE sub.id = item_container.id_item
AND FIND_IN_SET (id_container, ?);";
    set @_containers := REPLACE(containers,'\'','');
    PREPARE stmt FROM @query;
    EXECUTE stmt USING @_containers;
    DEALLOCATE PREPARE stmt;
   select @query, @_containers;
END //

DELIMITER ;

drop procedure if exists get_drivers;
commit;

DELIMITER //

CREATE PROCEDURE get_drivers(IN containers VARCHAR(255))
BEGIN
   SET @query = "SELECT DISTINCT id, name
FROM giqwm_fleet_driver driver, driver_container
WHERE driver.id = driver_container.id_driver
AND FIND_IN_SET (id_container, ?);";
    set @_containers := REPLACE(containers,'\'','');
    PREPARE stmt FROM @query;
    EXECUTE stmt USING @_containers;
    DEALLOCATE PREPARE stmt;
   select @query, @_containers;
END //

DELIMITER ;
