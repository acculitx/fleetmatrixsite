./tags.pl < tree.txt > tags.sql
./mysql.sh < create.sql
./mysql.sh < tags.sql
./mysql.sh < scenarios.sql
./mysql.sh < scenario_tag.sql
./mysql.sh < stored_procedures.sql
