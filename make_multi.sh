#!/bin/bash

############
# This shell script is used to create ATutor subsites. Enter a unique subsite 
# ID number as the first parameter, and a unique single word name for the site 
# as the second paramater, and an optional third parameter the path to  where 
# the subsite will be created ( e.g. ./make_multi.sh 234 mysite 
# /var/www/subsites). If the subsite path is not defined it will default to the
# same parent directory as the base ATutor multisite installation.
#
# Running the script will create a  directory in the install path using that ID, 
# create a symbolic link named equivalent then copy 
# over the install directory, update the installer's common.inc.php file to 
# indicate the site is a subsite, create the subsite's content directory and 
# config.inc.php file and make them writeable, and create symbolic links to the
# themes available in the base ATutor multisite installation. 

##########
# A mysql account must be created that has grant privileges, used to 
# create accounts for each subsite. The user account and password for this 
# account must be added to the following variables below:

mysql_user="creator"
mysql_pass="create21users"

#############
# Run the file (./make_multi.sh) from within the base ATutor root directory 
# being used as the shared source code for ATutor multisite.
#
# Also see the readme.multisite file for details on setting up apache to allow
# multisite installations
###########

###########
# Make sure there's a subsite ID and Alias passed to this script
if [ -z $1 ]
	then 	
	echo "No ATutor site ID was provided"
	exit 1
fi


if [ -z $2 ]
	then 	
	echo "No ATutor site alias was provided"
	exit 1
fi

###########
# If sitepath is passed, use it as the basedir
# else use the default parent of the multisite code base
if [ -n "$3" ]
	then 	
	thispwd=$(readlink -f "$0")
	thispwd=`dirname $thispwd`
	base_dir=$3
else
	thispwd=$(readlink -f "$0")
	thispwd=`dirname $thispwd`
	base_dir=`dirname "$thispwd"`
fi

############
# Detect the hostname, to be used in creating subsite directories
#
base_http=`hostname -s`.`hostname -d`

############
# Gather the first command link paramater (ID) and assign it to the
# sub_dir variable
#
if [ -z $1 ]
	then 	
	echo "No ATutor site ID was provided"
	exit 1
fi


if [ -z $2 ]
	then 	
	echo "No ATutor site alias was provided"
	exit 1
fi
newuser=$2
sub_dir=$1

#############
# Gather the second command line parameter (alias) and assign it
# to the domain_dir_alias variable
#
domain_dir_alias=$2

#############
# Determine if the path of the sub site exists yet
#
domain_dir=$base_dir/$sub_dir.$base_http
if [ -d $domain_dir ]
	then 	
	echo "The site https://$domain_dir_alias.$base_http exists"
	exit 1
fi 

#############
# Determine if the alias of the sub site exists yet
#
subsite_alias=$base_dir/$domain_dir_alias.$base_http
if [ -h $subsite_alias ]
	then
	echo "Alias already exists. Try another"
	exit 1
fi

#############
# Create the sub site's directory and make it writable
#
mkdir $domain_dir
chmod a+rwx $domain_dir

#############
# Create the sub site's content directory and make it writable
#
mkdir $domain_dir/content
chmod a+rwx $domain_dir/content

#############
# Copy the create new mysql user template
#
cp $thispwd/newuser.sql $domain_dir/newuser.sql

#############
# Copy the ATutor installation script from the base site to the sub site
cp -r $thispwd/install $domain_dir/install

#############
# Copy the subsite config template file from the base site to the subsite
# to be used to automate setup of the database
cp $thispwd/include/config_subsite_tmpl.php $domain_dir/config_tmp.inc.php

###############
# Generate a random password for creating a mysql account
#
MAXSIZE=10
array1=(
q w e r t y u i o p a s d f g h j k l z x c v b n m Q W E R T Y U I O P A S D
F G H J K L Z X C V B N M 1 2 3 4 5 6 7 8 9 0 - _)
MODNUM=${#array1[*]}
pwd_len=0
while [ $pwd_len -lt $MAXSIZE ]
do
    index=$(($RANDOM%$MODNUM))
    newpass=$newpass"${array1[$index]}"
    ((pwd_len++))
done

############
# Update the temporary config file copied from the base multisite
# installation with the site alias as the login name, and the randomly
# generated password created above. Also write that mysql user informaiton
# to the newuser.sql file, which gets used below to create a new user
# for the subsite.

# replace tmp passwd
sed -i "s#NEWPWD_MYSQL#$newpass#g" $domain_dir/config_tmp.inc.php 
sed -i "s#NEWPWD_MYSQL#$newpass#g" $domain_dir/newuser.sql 
# replace tmp username
sed -i "s#NEWUSER_MYSQL#$newuser#g" $domain_dir/config_tmp.inc.php 
sed -i "s#NEWUSER_MYSQL#$newuser#g" $domain_dir/newuser.sql 

#############
# Generate a new mysql user for the subsite being created
# See note at the top of this file, about the mysql account
# needed here.

mysql -u $mysql_user -p$mysql_pass < $domain_dir/newuser.sql

############
# Write the sub site's ID to the $AT_SUBSITE variable in the installer's
# common.inc.php file to identify the installation as a sub site
#
sed -i "s#//\$AT_SUBSITE='';#\$AT_SUBSITE='$sub_dir';#g" $domain_dir/install/include/common.inc.php 

#############
# Create an empty config.inc.php file for the sub site and make it writable
#
touch $domain_dir/config.inc.php 
chmod a+rwx $domain_dir/config.inc.php

#############
# Create a themes directory for the sub site, make it writable, and link
# from within it to the themes in the base site
# 
mkdir $domain_dir/themes
chmod a+w $domain_dir/themes
ln -s $thispwd/themes/default $domain_dir/themes/default
ln -s $thispwd/themes/default15 $domain_dir/themes/default15
ln -s $thispwd/themes/default16 $domain_dir/themes/default16
ln -s $thispwd/themes/default_classic $domain_dir/themes/default_classic
ln -s $thispwd/themes/blumin $domain_dir/themes/blumin
ln -s $thispwd/themes/greenmin $domain_dir/themes/greenmin
ln -s $thispwd/themes/idi $domain_dir/themes/idi
ln -s $thispwd/themes/fluid $domain_dir/themes/fluid
ln -s $thispwd/themes/atspaces $domain_dir/themes/atspaces
ln -s $thispwd/themes/mobile $domain_dir/themes/mobile
ln -s $thispwd/themes/tablet1 $domain_dir/themes/tablet1
ln -s $thispwd/themes/simplified-desktop $domain_dir/themes/simplified-desktop

#############
# Create the mods directory for the subsite and make it wrtable
# 
mkdir $domain_dir/mods
chmod a+w $domain_dir/mods

#############
# Create a symbolic link between the ID based directory for the sub site, and 
# and the alias directory
# 
echo "Creating https://$2.$base_http<br />"
ln -s $domain_dir $subsite_alias

echo "DONE, Your ATutor installation is ready for setup at <a href=\"https://$2.$base_http\">https://$2.$base_http</a>"

