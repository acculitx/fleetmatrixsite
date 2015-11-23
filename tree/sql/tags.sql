insert into tag (id, name, id_parent, child_num) values (0, 'Security Control Validation', null, 0);
insert into tag (id, name, id_parent, child_num) values (1, 'NIST', 0, 0);
insert into tag (id, name, id_parent, child_num) values (2, 'PCI', 0, 1);
insert into tag (id, name, id_parent, child_num) values (3, 'Adversarial', null, 1);
insert into tag (id, name, id_parent, child_num) values (4, 'Groups', 3, 0);
insert into tag (id, name, id_parent, child_num) values (5, 'Techniques', 3, 1);
insert into tag (id, name, id_parent, child_num) values (6, 'Point of Infection', 5, 0);
insert into tag (id, name, id_parent, child_num) values (7, 'Escalation of Privilege ', 5, 1);
insert into tag (id, name, id_parent, child_num) values (8, 'C&C ', 5, 2);
insert into tag (id, name, id_parent, child_num) values (9, 'Asset Discovery ', 5, 3);
insert into tag (id, name, id_parent, child_num) values (10, 'Ex-filtration ', 5, 4);
insert into tag (id, name, id_parent, child_num) values (11, 'Events', 3, 2);
insert into tag (id, name, id_parent, child_num) values (12, 'Validation', null, 2);
insert into tag (id, name, id_parent, child_num) values (13, 'Operating Systems', 12, 0);
insert into tag (id, name, id_parent, child_num) values (14, 'Linux ', 13, 0);
insert into tag (id, name, id_parent, child_num) values (15, 'Hardening Scripts ', 14, 0);
insert into tag (id, name, id_parent, child_num) values (16, 'Windows', 13, 1);
insert into tag (id, name, id_parent, child_num) values (17, 'Networking', 12, 1);
insert into tag (id, name, id_parent, child_num) values (18, 'Technologies', 12, 2);
insert into tag (id, name, id_parent, child_num) values (19, 'Network Firewall', 18, 0);
insert into tag (id, name, id_parent, child_num) values (20, 'Host-based Firewall ', 18, 1);
insert into tag (id, name, id_parent, child_num) values (21, 'Web Filtering ', 18, 2);
insert into tag (id, name, id_parent, child_num) values (22, 'Anti-Virus ', 18, 3);
insert into tag (id, name, id_parent, child_num) values (23, 'DNS', 18, 4);