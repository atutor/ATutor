#! /bin/csh -f
#########################################################################
# ATutor bundle script                                                  #
# ./bundle [VERSION] to specify an optional version number              #
# Author: Joel Kronenberg - ATRC, Oct 2003                              #
#########################################################################

set db_name = "dev_atutor_langs"
set db_user = "dev_atutor_langs"
set db_pass = "devlangs99"

set now = `date +"%Y_%m_%d"`
set atutor_dir = "ATutor_$now"
set bundle = "ATutor"

echo "\033[1mATutor Bundle Script [for CVS 1.3.1+] \033[0m"
echo "--------------------"

if ($#argv > 0) then
	set extension = "-$argv[1]"
else 
	echo "\nNo argument given. Run \033[1m./bundle.sh [VERSION]\033[0m to specify bundle version."
	set extension = ""
endif

echo "\nUsing $atutor_dir as temp bundle directory."
echo "Using $bundle$extension.tar.gz as bundle name."
sleep 1
if (-e $atutor_dir) then
	echo -n "\nDir $atutor_dir exists. Overwrite? (y/q) "

	set ans = $<
	switch ($ans)
	    case q: 
		echo "\n$atutor_dir not touched. Exiting.\n"
	       exit
	    case y:
		echo "\nRemoving old $atutor_dir"
		rm -r $atutor_dir
	endsw
endif
sleep 1

echo "\nCopying docs/ to $atutor_dir"
mkdir $atutor_dir
cp -R docs $atutor_dir/ATutor
sleep 1

echo "\nDumping $db_name.lang_base"
rm $atutor_dir/ATutor/install/db/atutor_lang_base.sql
echo "DROP TABLE lang_base;" > $atutor_dir/ATutor/install/db/atutor_lang_base.sql
mysqldump $db_name lang_base -u $db_user --password=$db_pass --allow-keywords --quote-names --quick >> $atutor_dir/ATutor/install/db/atutor_lang_base.sql
sleep 1

echo "\nRemoving $atutor_dir/ATutor/include/config.inc.php"
rm $atutor_dir/ATutor/include/config.inc.php
echo "<?php /* This file is a placeholder. Do not delete. Use the automated installer. */ ?>" > $atutor_dir/ATutor/include/config.inc.php
sleep 1

rm -r $atutor_dir/ATutor/users/admin

echo "\nRemoving $atutor_dir/ATutor/include/cvs_development.inc.php"
rm $atutor_dir/ATutor/include/cvs_development.inc.php
sleep 1

echo "\nDisabling DEBUG if enabled."
sed "s/define('AT_DEVEL', 1);/define('AT_DEVEL', 0);/" $atutor_dir/ATutor/include/vitals.inc.php > $atutor_dir/vitals.inc.php
mv $atutor_dir/vitals.inc.php $atutor_dir/ATutor/include/
sleep 1

echo "\nRemoving $atutor_dir/ATutor/content/"
rm -r $atutor_dir/ATutor/content/
sleep 1

echo "\nCreating $atutor_dir/ATutor/content"
mkdir $atutor_dir/ATutor/content
touch $atutor_dir/ATutor/content/index.html
sleep 1

echo "\nCreating $atutor_dir/ATutor/content/import"
mkdir $atutor_dir/ATutor/content/import
touch $atutor_dir/ATutor/content/import/index.html
sleep 1

echo "\nCreating $atutor_dir/ATutor/content/chat"
mkdir $atutor_dir/ATutor/content/chat
touch $atutor_dir/ATutor/content/chat/index.html
sleep 1

echo "\nTargz'ing $bundle${extension}.tar.gz $atutor_dir/ATutor/"
sleep 1

if (-f "$bundle${extension}.tar.gz") then
	echo -n "\nBundle $bundle$extension.tar.gz exists. Overwrite? (y/n/q) "

	set ans = $<

	switch ($ans)
	    case q:
		echo "\n$bundle$extension.tar.gz not touched."
		exit
	    case y:
		echo "\nRemoving old $bundle$extension.tar.gz"
		set final_name = "$bundle$extension.tar.gz"
		rm -r "$bundle$extension.tar.gz"
		breaksw
	    case n: 
		set time = `date +"%k_%M_%S"`
		set extension = "${extension}-${time}"
		echo "\nSaving as $bundle$extension.tar.gz instead.\n"
		set final_name = "$bundle$extension.tar.gz"
		breaksw
	endsw
else
	set final_name = "$bundle$extension.tar.gz"
endif	

echo "Creating \033[1m$final_name\033[0m"
cd $atutor_dir
tar -zcf $final_name ATutor/
mv $final_name ..
cd ..
sleep 1

echo -n "\nRemove temp $atutor_dir directory? (y/n) "
set ans = $<

if ($ans == "y") then
	echo "\nRemoving temp $atutor_dir directory"
	rm -r $atutor_dir
endif

echo "\n\033[1mBundle complete. Enjoy.\n\nExiting.\033[0m"

exit 1
