#! /bin/csh -f
#########################################################################
# ATutor bundle script                                                  #
# ./bundle [VERSION] to specify an optional version number              #
# Author: Joel Kronenberg - ATRC, Oct 2003                              #
#########################################################################

set now = `date +"%Y_%m_%d"`
set atutor_dir = "ATutor_$now"
set bundle = "ATutor"
set svndir = "http://atutorsvn.atrc.utoronto.ca/repos/atutor/trunk/docs/"

echo "\033[1mATutor Bundle Script [for CVS 1.3.1+] \033[0m"
echo "--------------------"

if ($#argv > 0) then
	set extension = "-$argv[1]"
else 
	echo "\nNo argument given. Run \033[1m./bundle.sh [VERSION]\033[0m to specify bundle version."
	set extension = ""
endif

if ($#argv == "2") then
	set ignore_mode = true
else
	set ignore_mode = false
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

echo "\nExporting from SVN/ to $atutor_dir"
mkdir $atutor_dir
#cp -R docs $atutor_dir/ATutor
/usr/bin/svn --force export $svndir
mv 'docs' $atutor_dir/ATutor
sleep 1

echo "\nDumping language_text"
rm $atutor_dir/ATutor/install/db/atutor_language_text.sql
echo "DROP TABLE language_text;" > $atutor_dir/ATutor/install/db/atutor_language_text.sql
wget --output-document=- http://atutor.ca/atutor/translate/dump_lang.php >> $atutor_dir/ATutor/install/db/atutor_language_text.sql

sleep 1

echo "\nRemoving $atutor_dir/ATutor/include/config.inc.php"
rm -f $atutor_dir/ATutor/include/config.inc.php
echo -n "<?php /* This file is a placeholder. Do not delete. Use the automated installer. */ ?>" > $atutor_dir/ATutor/include/config.inc.php
sleep 1

#echo "\nRemoving $atutor_dir/ATutor/themes/clean_blue"
#rm -r $atutor_dir/ATutor/themes/clean_blue
#sleep 1

#echo "\nRemoving $atutor_dir/ATutor/themes/open_book"
#rm -r $atutor_dir/ATutor/themes/open_book
#sleep 1

echo "\nRemoving $atutor_dir/ATutor/install/db/atutor_upgrade sql < 1.4"
rm -r $atutor_dir/ATutor/install/db/atutor_upgrade_1.0_to_1.1.sql
rm -r $atutor_dir/ATutor/install/db/atutor_upgrade_1.1_to_1.2.sql
rm -r $atutor_dir/ATutor/install/db/atutor_upgrade_1.2_to_1.3.sql
rm -r $atutor_dir/ATutor/install/db/atutor_upgrade_1.3_to_1.3.2.sql
rm -r $atutor_dir/ATutor/install/db/atutor_upgrade_1.3.2_to_1.4.sql
sleep 1


echo "\nDisabling AT_DEVEL if enabled."
sed "s/define('AT_DEVEL', 1);/define('AT_DEVEL', 0);/" $atutor_dir/ATutor/include/vitals.inc.php > $atutor_dir/vitals.inc.php
rm $atutor_dir/ATutor/include/vitals.inc.php
echo "\nDisabling AT_DEVEL_TRANSLATE if enabled."
sed "s/define('AT_DEVEL_TRANSLATE', 1);/define('AT_DEVEL_TRANSLATE', 0);/" $atutor_dir/vitals.inc.php > $atutor_dir/ATutor/include/vitals.inc.php
sleep 1

echo -n "<?php "'$svn_data = '"'" >> $atutor_dir/ATutor/svn.php
/usr/local/bin/svn log  -q -r HEAD http://atutorsvn.atrc.utoronto.ca/repos/atutor/trunk/  >> $atutor_dir/ATutor/svn.php
echo -n "';?>" >> $atutor_dir/ATutor/svn.php

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

if ($ignore_mode == true) then
	set ans = "y"
else 
	echo -n "\nRemove temp $atutor_dir directory? (y/n) "
	set ans = $<
endif

if ($ans == "y") then
	echo "\nRemoving temp $atutor_dir directory"
	rm -r $atutor_dir
endif

echo "\n\033[1m >> Did you update check_atutor_version.php ?? << \033[0m"

echo "\n\033[1mBundle complete. Enjoy.\n\nExiting.\033[0m"


exit 1