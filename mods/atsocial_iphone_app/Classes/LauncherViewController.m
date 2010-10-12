//
//  LauncherViewController.m
//  ATutor
//
//  Created by Quang Anh Do on 25/05/2010.
//  Copyright 2010 Quang Anh Do. All rights reserved.
//

#import "LauncherViewController.h"
#import "ATutorAppDelegate.h"
#import "CommonFunctions.h"

@interface LauncherViewController (Private)

- (BOOL)isLoggedIn;
- (void)logout;

@end

@implementation LauncherViewController

@synthesize launcherView;
@synthesize logoutButton;
@synthesize consumer;

- (id)init {
	if (self = [super init]) {
		self.consumer = [(ATutorAppDelegate *)[[UIApplication sharedApplication] delegate] consumer];
	}
	
	return self;
}

- (void)dealloc {
	[launcherView release];
	[logoutButton release];
	[consumer release];
	
    [super dealloc];
}

- (void)viewDidLoad {
	[super viewDidLoad];
	
	logoutButton = [[UIBarButtonItem alloc] initWithTitle:TTLocalizedString(@"Logout", @"") 
													style:UIBarButtonItemStyleBordered 
												   target:self action:@selector(logout)];
	
	self.title = TTLocalizedString(@"ATutor Social", @"");
}

- (void)viewWillAppear:(BOOL)animated {
	[super viewWillAppear:animated];
	self.navigationItem.rightBarButtonItem = [self isLoggedIn] ? logoutButton : nil;
}

- (void)didReceiveMemoryWarning {
    // Releases the view if it doesn't have a superview.
    [super didReceiveMemoryWarning];
    
    // Release any cached data, images, etc that aren't in use.
}

- (void)viewDidUnload {
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}

- (void)loadView {
	[super loadView];
	
	launcherView = [[TTLauncherView alloc] initWithFrame:self.view.bounds];
	
	launcherView.delegate = self;
	launcherView.backgroundColor = [UIColor colorWithRed:0.875 green:0.871 blue:0.925 alpha:1.000];
	launcherView.columnCount = 2;
	
	// Attempt to restore data if exists
	[self restorePages];
	
	[self.view addSubview:launcherView];
}

#pragma mark -
#pragma mark TTLauncherViewDelegate

- (void)launcherViewDidBeginEditing:(TTLauncherView*)launcher {
	[self.navigationItem setRightBarButtonItem:[[[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemDone
																							  target:launcherView
																							  action:@selector(endEditing)] autorelease] 
									  animated:YES];
}

- (void)launcherViewDidEndEditing:(TTLauncherView*)launcher {
	[self.navigationItem setRightBarButtonItem:logoutButton animated:YES];

	// Persist data the ugly way
	NSData *pages = [NSKeyedArchiver archivedDataWithRootObject:launcherView.pages];
	[[NSUserDefaults standardUserDefaults] setObject:pages forKey:@"launcher.pages"];
}

- (void)launcherView:(TTLauncherView*)launcher didSelectItem:(TTLauncherItem*)item {
	if ([item.title isEqualToString:TTLocalizedString(@"Activities", @"")]) {
		[[TTNavigator navigator] openURLAction:[[TTURLAction actionWithURLPath:@"atutor://activities"] applyAnimated:YES]];
	} else if ([item.title isEqualToString:TTLocalizedString(@"Contacts", @"")]) {
		[[TTNavigator navigator] openURLAction:[[TTURLAction actionWithURLPath:@"atutor://contacts"] applyAnimated:YES]];
	} else if ([item.title isEqualToString:TTLocalizedString(@"Gadgets", @"")]) {
		[[TTNavigator navigator] openURLs:
		 [NSString stringWithFormat:@"%@/mods/_standard/social/applications.php", kATutorURL], nil];
	} else if ([item.title isEqualToString:TTLocalizedString(@"Groups", @"")]) {
		[[TTNavigator navigator] openURLAction:[[TTURLAction actionWithURLPath:@"atutor://groups"] applyAnimated:YES]];
	}
}

#pragma mark -
#pragma mark QAWebControllerDelegate

- (void)didFinishAuthorizationInWebViewController:(QAWebController *)webViewController {
	[consumer finishAuthProcess];
	
	[[(ATutorAppDelegate *)[[UIApplication sharedApplication] delegate] helper] fetchContactList];
}

#pragma mark -
#pragma mark Misc

- (void)restorePages {
	NSData *pages = [[NSUserDefaults standardUserDefaults] objectForKey:@"launcher.pages"];
	if (pages != nil) {
		launcherView.pages = [NSKeyedUnarchiver unarchiveObjectWithData:pages];
	} else {
		for (NSString *module in [NSArray arrayWithObjects:@"Activities", @"Contacts", @"Gadgets", @"Groups", nil]) {
			[launcherView addItem:[[[TTLauncherItem alloc] initWithTitle:TTLocalizedString(module, @"") 
																   image:[NSString stringWithFormat:@"bundle://%@.png", module]
																	 URL:[NSString stringWithFormat:@"atutor://modules/%@", module] 
															   canDelete:NO] autorelease] 
						 animated:NO];
		}
	}
}

- (BOOL)isLoggedIn {
	return consumer.accessToken != nil;
}

- (void)logout {
	[consumer clearAuthentication];
	
	[self.navigationItem setRightBarButtonItem:nil animated:YES];
	
	alertMessage(@"", @"You have been logged out");
}

@end
