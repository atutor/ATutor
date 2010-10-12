//
//  ContactsViewController.m
//  ATutor
//
//  Created by Quang Anh Do on 03/07/2010.
//  Copyright 2010 Quang Anh Do. All rights reserved.
//

#import "ContactsViewController.h"
#import "Contact.h"
#import "CommonFunctions.h"
#import "ContactsDataSource.h"

@implementation ContactsViewController

- (void)dealloc {
	[super dealloc];
}

- (id)init {
	if (self = [super init]) {
		self.title = TTLocalizedString(@"Contacts", @"");
		self.autoresizesForKeyboard = YES;
	}
	
	return self;
}

- (void)loadView {
	[super loadView];
	
	self.dataSource = [[ContactsDataSource alloc] init];
}

@end
