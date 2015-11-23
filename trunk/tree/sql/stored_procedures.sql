drop procedure if exists get_tags;
commit;

DELIMITER //

CREATE PROCEDURE get_tags()
BEGIN
select id, name, id_parent, child_num
    from tag;
END //

DELIMITER ;

drop procedure if exists get_scenarios;
commit;

DELIMITER //

CREATE PROCEDURE get_scenarios(IN tags VARCHAR(255))
BEGIN
   SET @query = CONCAT ('SELECT DISTINCT id, name, description FROM giqwm_fleet_subscription, scenario_tag WHERE scenario.id = scenario_tag.id_scenario AND id_tag IN (', tags ,')');
    PREPARE stmt FROM @query;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END //

DELIMITER ;
