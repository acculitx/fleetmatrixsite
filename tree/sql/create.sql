drop table if exists tag;
create table tag (
  id int primary key,
  name varchar(50),
  id_parent int,
  child_num int
);

drop table if exists scenario;
create table scenario (
  id int primary key,
  name varchar(50),
  description varchar(1024)
);

drop table if exists scenario_tag;
create table scenario_tag (
  id_scenario int,
  id_tag int, 
  unique(id_scenario, id_tag)
);
