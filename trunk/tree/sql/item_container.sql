drop table if exists item_container;

create table item_container as
select id as id_item, entity_id as id_container from giqwm_fleet_subscription
;