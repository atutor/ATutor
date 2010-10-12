//
//  ATutorAppDelegate.m
//  ATutor
//
//  Created by Quang Anh Do on 25/05/2010.
//  Copyright Quang Anh Do 2010. All rights reserved.
//

#import "ATutorAppDelegate.h"
#import "StyleSheet.h"
#import "OSConsumer.h"
#import "OSProvider.h"

#import "ActivitiesViewController.h"
#import "ContactsViewController.h"
#import "ContactViewController.h"
#import "GroupsViewController.h"
#import "CommonFunctions.h"

@interface ATutorAppDelegate (Private) 

- (void)wireUpNavigator;

@end


@implementation ATutorAppDelegate

@synthesize window;
@synthesize consumer;
@synthesize launcher;
@synthesize webController;
@synthesize helper;
@synthesize settingsViewController;

- (BOOL)application:(UIApplication *)application didFinishLaunchingWithOptions:(NSDictionary *)launchOptions {    
	// Set service consumer
	consumer = [[OSConsumer alloc] init];
	
	// Set global stylesheet
	[TTDefaultStyleSheet setGlobalStyleSheet:[[[StyleSheet alloc] init] autorelease]];	
	
	// Set web controller handler
	launcher = [[LauncherViewController alloc] init];
	
	webController = [[QAWebController alloc] init];
	webController.oAuthDelegate = launcher;
	
	// Set up settings VC
	settingsViewController = [[IASKAppSettingsViewController alloc] initWithNibName:@"IASKAppSettingsView" 
																			 bundle:nil];
	[settingsViewController setDelegate:self];
	
	// Wire things up
	[self wireUpNavigator];
	
	// Home screen
	[[TTNavigator navigator] openURLAction:[TTURLAction actionWithURLPath:@"atutor://launcher"]];
	
	// Prepare helper
	helper = [[ATutorHelper alloc] init];
	[helper setDelegate:self];
	
	if (!kATutorURL || !kShindigURL) {
		NSLog(@"Settings required");
		[[TTNavigator navigator] openURLAction:[[TTURLAction actionWithURLPath:@"atutor://settings"] applyAnimated:YES]];
	} else {
		[helper fetchContactList];
	}
	
	return YES;
}

- (void)dealloc {
    [window release];
	[consumer release];
	[launcher release];
	[webController release];
	[helper release];
	[settingsViewController release];
	
    [super dealloc];
}

#pragma mark -
#pragma mark Misc

- (void)wireUpNavigator {
	TTNavigator *navigator = [TTNavigator navigator];
	navigator.window = window;
	navigator.persistenceMode = TTNavigatorPersistenceModeNone;
	
	TTURLMap *map = navigator.URLMap;
	[map from:@"*" toViewController:webController];
	[map from:@"atutor://launcher" toViewController:launcher];
	[map from:@"atutor://activities" toViewController:[ActivitiesViewController class]];
	[map from:@"atutor://contacts" toViewController:[ContactsViewController class]];
	[map from:@"atutor://contact/(initWithId:)" toViewController:[ContactViewController class]];
	[map from:@"atutor://contact/(initWithId:)/(name:)" toViewController:[ContactViewController class]];
	[map from:@"atutor://groups" toViewController:[GroupsViewController class]];
	[map from:@"atutor://settings" toViewController:settingsViewController];
}

#pragma mark -
#pragma mark Helper delegate

- (void)doneFetchingContactList {
	[[TTNavigator navigator] openURLAction:[TTURLAction actionWithURLPath:@"atutor://launcher"]];
}

#pragma mark -
#pragma mark IASK delegate

- (void)settingsViewControllerDidEnd:(IASKAppSettingsViewController *)sender {
	NSLog(@"Settings updated, refresh consumer");
	consumer.currentProvider = [OSProvider getATutorProviderWithKey:kConsumerKey withSecret:kConsumerSecret];
}

@end
