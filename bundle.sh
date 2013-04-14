#! /bin/csh -f
#########################################################################
# ATutor bundle script                                                  #
# ./bundle [VERSION] to specify an optional version number              #
# Author: Joel Kronenberg - IDRC, Oct 2003                              #
#########################################################################
# Updated Sept 2011 for GitHub Greg Gay
# Run this script on a server that has git and wget installed
# Issue the command './bundle.sh [VERSION]' to generate an ATutor distribution bundle
# In addition to creating a clone of the atutor/ATutor source code from GitHub
# This script retreives a copy of the latest English language from the atutor.ca language database via wget.
#



set now = `date +"%Y_%m_%d"`
set atutor_dir = "ATutor_$now"
set bundle = "ATutor"

set gitdir = "git://github.com/atutor/ATutor.git"
set gitexec = "git"

echo "ATutor Bundle Script for GitHub"
echo "--------------------"

if ($#argv > 0) then
	set extension = "-$argv[1]"
else 
	echo "No argument given. Run./bundle.sh [VERSION] to specify bundle version."
	set extension = ""
endif

if ($#argv == "2") then
	set ignore_mode = true
else
	set ignore_mode = false
endif

echo "Using $atutor_dir as temp bundle directory."
echo "Using $bundle$extension.tar.gz as bundle name."
sleep 1
if (-e $atutor_dir) then
	echo -n "Dir $atutor_dir exists. Overwrite? (y/q) "

	set ans = $<
	switch ($ans)
	    case q: 
		echo "$atutor_dir not touched. Exiting.\n"
	       exit
	    case y:
		echo "Removing old $atutor_dir"
		rm -r $atutor_dir
	endsw
endif
sleep 1

echo "Cloning from GitHub to $atutor_dir"
echo "Leave Password empty if prompted."
mkdir $atutor_dir
#cp -R docs $atutor_dir/ATutor
#$svnexec --force export $gitdir
$gitexec clone $gitdir

mv 'ATutor' $atutor_dir/ATutor
sleep 1

echo "Dumping language_text"
rm $atutor_dir/ATutor/include/install/db/atutor_language_text.sql
echo 'DROP TABLE `language_text`;' > $atutor_dir/ATutor/include/install/db/atutor_language_text.sql
wget --output-document=- http://atutor.ca/atutor/translate/dump_lang.php >> $atutor_dir/ATutor/include/install/db/atutor_language_text.sql

sleep 1

echo "Removing $atutor_dir/ATutor/include/config.inc.php"
rm -f $atutor_dir/ATutor/include/config.inc.php
echo -n "<?php /* This file is a placeholder. Do not delete. Use the automated installer. */ ?>" > $atutor_dir/ATutor/include/config.inc.php
sleep 1

echo "Removing $atutor_dir/ATutor/include/install/db/atutor_upgrade sql < 1.4"
rm -r $atutor_dir/ATutor/include/install/db/atutor_upgrade_1.0_to_1.1.sql
rm -r $atutor_dir/ATutor/include/install/db/atutor_upgrade_1.1_to_1.2.sql
rm -r $atutor_dir/ATutor/include/install/db/atutor_upgrade_1.2_to_1.3.sql
rm -r $atutor_dir/ATutor/include/install/db/atutor_upgrade_1.3_to_1.3.2.sql
rm -r $atutor_dir/ATutor/include/install/db/atutor_upgrade_1.3.2_to_1.4.sql
sleep 1


echo "Disabling AT_DEVEL if enabled."
sed "s/define('AT_DEVEL', 1);/define('AT_DEVEL', 0);/" $atutor_dir/ATutor/include/vitals.inc.php > $atutor_dir/vitals.inc.php
rm $atutor_dir/ATutor/include/vitals.inc.php
echo "Disabling AT_DEVEL_TRANSLATE if enabled."
sed "s/define('AT_DEVEL_TRANSLATE', 1);/define('AT_DEVEL_TRANSLATE', 0);/" $atutor_dir/vitals.inc.php > $atutor_dir/ATutor/include/vitals.inc.php
sleep 1
set date = `date`
echo -n "<?php "'$svn_data = '"'" >> $atutor_dir/ATutor/svn.php
echo $date >> $atutor_dir/ATutor/svn.php
#echo "Bundled" `date` >> $atutor_dir/ATutor/svn.php
echo "';?>" >> $atutor_dir/ATutor/svn.php
rm -Rf $atutor_dir/ATutor/.git
echo "Targz'ing $bundle${extension}.tar.gz $atutor_dir/ATutor/"
sleep 1

if (-f "$bundle${extension}.tar.gz") then
	echo -n "Bundle $bundle$extension.tar.gz exists. Overwrite? (y/n/q) "

	set ans = $<

	switch ($ans)
	    case q:
		echo "$bundle$extension.tar.gz not touched."
		exit
	    case y:
		echo "Removing old $bundle$extension.tar.gz"
		set final_name = "$bundle$extension.tar.gz"
		rm -r "$bundle$extension.tar.gz"
		breaksw
	    case n: 
		set time = `date +"%k_%M_%S"`
		set extension = "${extension}-${time}"
		echo "Saving as $bundle$extension.tar.gz instead.\n"
		set final_name = "$bundle$extension.tar.gz"
		breaksw
	endsw
else
	set final_name = "$bundle$extension.tar.gz"
endif	

echo "Creating $final_name"
cd $atutor_dir
tar -zcf $final_name ATutor/
mv $final_name ..
cd ..
sleep 1

if ($ignore_mode == true) then
	set ans = "y"
else 
	echo -n "Remove temp $atutor_dir directory? (y/n) "
	set ans = $<
endif

if ($ans == "y") then
	echo "Removing temp $atutor_dir directory"
	rm -r $atutor_dir	
	#remove the Git cloned directory
	#rm -rf "ATutor"
endif

echo " >> Did you update check_atutor_version.php ?? <<"

echo "Bundle complete. Enjoy.Exiting."


exit 1
