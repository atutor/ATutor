#!/bin/sh
# This shell script is used to create ATutor subsites. Enter any sub site ID 
# when prompted. This will create a directory using that ID, copy 
# over the install directory, update the installer's common.inc.php file to 
# indicate the site is a subsite, create the subsite's content directory and 
# config.inc.php file and make them writeable. 
#
# Run the file (./make_multi.sh) from within the ATutor root directory being 
# used as the shared source code for ATutor multisite.
#
# Also see the readme.multisite file for details on setting up apache to allow
# multisite installations
###########

thispwd=`pwd -P`
base_dir=`dirname $thispwd`
base_http=`hostname -s`.`hostname -d`

echo 'Type an ID number or string for the directory where multisite should create a new subsite:'
echo $base_http
read var1
sub_dir=$var1
domain_dir=$base_dir/$sub_dir.$base_http
#echo 'Type the alias name for the directory'
#read var1
#sub_dir_alias=$var1
#alias_dir=$base_dir/$sub_dir_alias.$base_http
pwd=`pwd -P`
echo 'Creating ' $domain_dir
mkdir $domain_dir
chmod a+rwx $domain_dir
mkdir $domain_dir/content
chmod a+rwx $domain_dir/content
cp -r install $domain_dir/install
cp $pwd/include/config.inc.php $domain_dir/config_tmp.inc.php
sed -i "s#//\$AT_SUBSITE='';#\$AT_SUBSITE='$sub_dir';#g" $domain_dir/install/include/common.inc.php 
touch $domain_dir/config.inc.php   
mkdir $domain_dir/themes
mkdir $domain_dir/mods
chmod a+rwx $domain_dir/config.inc.php
#ln -s $domain_dir $alias_dir