AttackIQ July, 2015, John Dimm

Files to process and manage the data used by the homemade TreeController.

run.sh
  Does everything:  creates the tables, prepares the data, populates the tables.

tree.txt
  The primary input.  This file defines the tag hierarchy and lists associated scenarios.  It was typed
  from memory by Stephan in a few minutes, and is only a small subset of the real data. 

scenario_tag.sql
  Maps scenarios to tags.  Created manually.

scenarios.sql
  Lists a few scenarios.  Created manually.

tags.pl
  Perl script to read tree.txt and generate an adjacency table.  It deduces the structure from the
  indentation levels in tree.txt.

tags.sql
  Output of tags.pl

create.sql
  Creates the empty SQL tables for tags, scenarios, and scenario tags.

mysql.sh
  Runs mysql, either interactively or using stdin

stored_procedures.sql
  Stored procedures to get tags and scenarios.

