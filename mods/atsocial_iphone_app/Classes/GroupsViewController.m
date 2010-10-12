//
//  GroupsViewController.m
//  ATutor
//
//  Created by Quang Anh Do on 08/08/2010.
//  Copyright 2010 Quang Anh Do. All rights reserved.
//

#import "GroupsViewController.h"
#import "CommonFunctions.h"
#import "OSConsumer.h"
#import "OAServiceTicket.h"
#import "NSDictionary_JSONExtensions.h"
#import "ATutorAppDelegate.h"

@interface GroupsViewController (Private)

- (void)groupsCallback:(OAServiceTicket *)ticket didFinishWithResponse:(id)response;

@end

@implementation GroupsViewController

- (id)init {
	if (self = [super init]) {
		self.title = TTLocalizedString(@"Groups", @"");
		self.autoresizesForKeyboard = YES;
		self.variableHeightRows = YES;
		
		OSConsumer *consumer = [(ATutorAppDelegate *)[[UIApplication sharedApplication] delegate] consumer];
		[consumer getDataForUrl:@"/groups/@me" 
				  andParameters:nil 
					   delegate:self 
			  didFinishSelector:@selector(groupsCallback:didFinishWithResponse:)];
	}
	
	return self;
}

- (void)loadView {
	[super loadView];
	
	self.tableView.allowsSelection = NO;
}

- (void)groupsCallback:(OAServiceTicket *)ticket didFinishWithResponse:(id)response {
	if (ticket.didSucceed) {
		
	} else {
		alertMessage(@"Error", @"The service groups is not implemented");
	}
}

@end
