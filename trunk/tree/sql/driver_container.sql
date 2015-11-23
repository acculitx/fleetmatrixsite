drop table if exists driver_container;

create table driver_container as
select id as id_driver, entity_id as id_container from giqwm_fleet_driver
;