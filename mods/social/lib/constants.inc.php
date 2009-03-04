<?php
//Constants Declaration
define(AT_SHINDIG_URL,						'http://localhost/shindig/php');
//define(AT_SHINDIG_URL,						'http://142.150.154.167/shindig/php');
//define(AT_SHINDIG_URL,						'http://social.atutor.ca/shindig/php');

//Privacy Control Constants, field indices
//Profile
define(AT_SOCIAL_PROFILE_BASIC,				0);	//Basic profile info
define(AT_SOCIAL_PROFILE_PROFILE,			1);	//All Profile info
define(AT_SOCIAL_PROFILE_STATUS_UPDATE,		2);
define(AT_SOCIAL_PROFILE_MEDIA,				3);
define(AT_SOCIAL_PROFILE_CONNECTION,		4);
define(AT_SOCIAL_PROFILE_EDUCATION,			5);
define(AT_SOCIAL_PROFILE_POSITION,			6);	//Job
//Search 		
define(AT_SOCIAL_SEARCH_VISIBILITY,			0);	//Who can find the user via search
define(AT_SOCIAL_SEARCH_PROFILE,			1);	//Search through profile?
define(AT_SOCIAL_SEARCH_CONNECTION,			2);	//Search through connection?
define(AT_SOCIAL_SEARCH_EDUCATION,			3);	//Search through education?
define(AT_SOCIAL_SEARCH_POSITION,			4);	//Search through job position?
//Actvity
define(AT_SOCIAL_ACTIVITIES_PROFILE,		0);	//Profile updates
define(AT_SOCIAL_ACTIVITIES_CONNECTION,		1);	//Adding a connection
define(AT_SOCIAL_ACTIVITIES_APPLICATION,	2);	//Adding an application
//Application 
//TODO: Application can record activities via ATutorAppDataService, restrict it.

define(AT_SOCIAL_EVERYONE_VISIBILITY,				0);	//Everyone
define(AT_SOCIAL_FRIENDS_VISIBILITY,				1);	//Friends
define(AT_SOCIAL_FRIENDS_OF_FRIENDS_VISIBILITY,		2);	//Friends of Friends
define(AT_SOCIAL_NETWORK_VISIBILITY,				3);	//Network
?>