#!/bin/bash
# This shell script is used to create ATutor subsites. Enter a unique sub site 
# ID number as the first parameter, and a unique single word name for the site 
# as the second paramater( e.g. ./make_multi.sh 234 mysite). This will create a 
# directory using that ID, create a symbolic link named equivalent then copy 
# over the install directory, update the installer's common.inc.php file to 
# indicate the site is a subsite, create the subsite's content directory and 
# config.inc.php file and make them writeable, and create symbolic links to the
# theme avilable in the base ATutor installation. 
#
# Run the file (./make_multi.sh) from within the base ATutor root directory 
# being used as the shared source code for ATutor multisite.
#
# Also see the readme.multisite file for details on setting up apache to allow
# multisite installations
###########

######
# Make sure there's a subsite ID and Alias passwed to this script
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
####
# Determine where the make_multi.sh script is being run from
# Sub sites will be created next to the base ATutor installation
#thispwd=`pwd -P`
#base_dir=`dirname $thispwd`
thispwd=$(readlink -f "$0")
thispwd=`dirname $thispwd`
base_dir=`dirname "$thispwd"`

# Detect the hostname, to be used in creating subsite directories
base_http=`hostname -s`.`hostname -d`

# Gather the first command link paramater (ID) and assign it to the
# sub_dir variable
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
sub_dir=$1

# Gather the second command line parameter (alias) and assign it
# to the domain_dir_alias variable
domain_dir_alias=$2


# Determine if the path of the sub site exists yet
domain_dir=$base_dir/$sub_dir.$base_http
if [ -d $domain_dir ]
	then 	
	echo "The site http://$domain_dir_alias.$base_http already exists"
	exit 1
fi 
# Determine if the alias of the sub site exists yet
subsite_alias=$base_dir/$domain_dir_alias.$base_http
if [ -h $subsite_alias ]
	then
	echo "Alias already exists. Try another"
	exit 1
fi

# Create the sub site's directory and make it writable
#echo 'Creating ' $domain_dir
mkdir $domain_dir
#if [ `mkdir $domain_dir` ]
#then
#echo "created $domain_dir"
#else
#echo "did not create $domain_dir"
#fi
chmod a+rwx $domain_dir

# Create the sub site's content directory and make it writable
mkdir $domain_dir/content
chmod a+rwx $domain_dir/content

# Copy the ATutor installation script from the base site to the sub site
cp -r $thispwd/install $domain_dir/install

# Copy the config.inc.php file from the base site to the sub site
# to be used to automate setup of the database
cp $thispwd/include/config.inc.php $domain_dir/config_tmp.inc.php

# Write the sub site's ID to the $AT_SUBSITE variable in the installer's
# common.inc.php file to identify the installation as a sub site
sed -i "s#//\$AT_SUBSITE='';#\$AT_SUBSITE='$sub_dir';#g" $domain_dir/install/include/common.inc.php 

# Create an empty config.inc.php file for the sub site and make it writable
touch $domain_dir/config.inc.php 
chmod a+rwx $domain_dir/config.inc.php

# Create a themes directory for the sub site, make it writable, and link
# from within it to the themes in the base site
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
ln -s $thispwd/themes/mobile $domain_dir/themes/mobile
ln -s $thispwd/themes/tablet1 $domain_dir/themes/tablet1
ln -s $thispwd/themes/simplified-desktop $domain_dir/themes/simplified-desktop

# Create the mods directory for the sub site and make it wrtable
mkdir $domain_dir/mods
chmod a+w $domain_dir/mods

# Create a symbolic link between the ID based directory for the sub site, and 
# and the alias directory
# echo 'Creating link ' $base_dir.$domain_dir_alias.$base_http
# echo 'Creating ' $subsite_alias
echo "Creating http://$2.$base_http<br />"
ln -s $domain_dir $subsite_alias

echo "DONE, Your ATutor installation is ready for setup at <a href=\"http://$2.$base_http\">http://$2.$base_http</a>"

