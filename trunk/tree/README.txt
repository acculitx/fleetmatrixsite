AttackIQ, July 2015, John Dimm

Files to create and browse a tree of tags and associated scenarios.

Demo: 
 
  http://intel.attackiq.com/tree/

  - click a tag to display all scenarios with that tag or any of its children.
    Note:  the test data used here is very sparse.  Click on "Adversarial" or "Validation".

  - click the triangle next to a parent node to open or close it.

Files:

app.js
  Angular directive and controller.

doc
  A doc describing the data and how it is transformed.

index.html
  Entry point.

report_pdo.php
  A php interface to mysql that runs a stored procedure with optional parameters. Every database interaction
  at runtime uses this script.  The javascript code knows only how to call the stored procedures, and 
  has no knowledge of the underlying SQL tables.

sql
  Files to create and manage the database tables.

tree.html
  Recursive template to display the tag tree and scenarios.
