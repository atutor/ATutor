#! /bin/csh -f

set db_name = "atutor_v1_3"
set db_user = "webedit"
set db_pass = "C0ur8eM3re"

echo "\nDumping $db_name.lang_base"
mysqldump $db_name lang_base -u $db_user --password=$db_pass --allow-keywords --quote-names --quick --add-drop-table > atutor_lang_base.sql

mysqldump $db_name lang2 -u $db_user --password=$db_pass --allow-keywords --quote-names --quick --add-drop-table > atutor_lang2.sql

exit 1
