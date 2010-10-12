//
//  ATutorHelper.m
//  ATutor
//
//  Created by Quang Anh Do on 07/06/2010.
//  Copyright 2010 Quang Anh Do. All rights reserved.
//

#import "ATutorHelper.h"
#import "ATutorAppDelegate.h"
#import "OARequestParameter.h"
#import "CommonFunctions.h"
#import "OAServiceTicket.h"
#import "NSDictionary_JSONExtensions.h"
#import	"Contact.h"

@interface ATutorHelper (Private) 

- (void)peopleCallback:(OAServiceTicket *)ticket didFinishWithResponse:(id)response;
- (void)personCallback:(OAServiceTicket *)ticket didFinishWithResponse:(id)response;
- (NSDictionary *)matchDisplayNameWithId:(NSArray *)data;

@end


@implementation ATutorHelper

@synthesize consumer;
@synthesize numberOfContacts;
@synthesize contacts;
@synthesize contactMapping;
@synthesize delegate;

- (void)dealloc {
	[consumer dealloc];
	[contacts dealloc];
	[contactMapping dealloc];
	[delegate release];
	
	[super dealloc];
}

- (id)init {
	if (self = [super init]) {
		self.consumer = [(ATutorAppDelegate *)[[UIApplication sharedApplication] delegate] consumer];
		self.numberOfContacts = 0;
		self.contacts = [[NSMutableArray alloc] init];
		self.contactMapping = [[NSMutableArray alloc] init];
	}
	
	return self;
}

- (void)fetchContactList {
	NSLog(@"=-=-=-=-=-=-=-=-Fetching contact list-=-=-=-=-=-=-=-=");
	
	[consumer getDataForUrl:@"/people/@me/@contacts" 
			  andParameters:[NSArray arrayWithObjects:[OARequestParameter requestParameterWithName:@"count" value:@"100"], 
							 [OARequestParameter requestParameterWithName:@"startIndex" value:[NSString stringWithFormat:@"%d", numberOfContacts]], 
							 [OARequestParameter requestParameterWithName:@"sortBy" value:@"displayName"],
							 nil] 
				   delegate:self 
		  didFinishSelector:@selector(peopleCallback:didFinishWithResponse:)];	
}

- (void)fetchOwnProfile {
	NSLog(@"=-=-=-=-=-=-=-=-Fetching own profile-=-=-=-=-=-=-=-=");
	
	[consumer getDataForUrl:@"/people/@me/@self" 
			  andParameters:nil
				   delegate:self
		  didFinishSelector:@selector(personCallback:didFinishWithResponse:)];
}

- (void)peopleCallback:(OAServiceTicket *)ticket didFinishWithResponse:(id)response {
	if (ticket.didSucceed) {
		NSError *error = nil;
		NSDictionary *data = [NSDictionary dictionaryWithJSONData:[response dataUsingEncoding:NSUTF8StringEncoding] error:&error];
		NSArray *entries = [data objectForKey:@"entry"];
		
		// Mapping
		[contactMapping addObjectsFromArray:entries];
		numberOfContacts += [entries count];
		
		for (NSDictionary *entry in entries) {
			[contacts addObject:[Contact contactWithDictionary:entry]];
		}
		
		// Continue fetching or not?
		if (numberOfContacts < [[data objectForKey:@"totalResults"] intValue]) { // Fetch friend
			[self fetchContactList];
		} else { // Fetch own profile
			[self fetchOwnProfile];
		} 
	} else {
		alertMessage(@"Error", @"Unable to fetch your contact list");
	}
}

- (void)personCallback:(OAServiceTicket *)ticket didFinishWithResponse:(id)response {
	if (ticket.didSucceed) {
		NSError *error = nil;
		NSDictionary *data = [NSDictionary dictionaryWithJSONData:[response dataUsingEncoding:NSUTF8StringEncoding] error:&error];
		NSDictionary *entry = [data objectForKey:@"entry"];
		
		// Mapping
		[contactMapping addObject:entry];
		numberOfContacts++;
		
		[contacts addObject:[Contact contactWithDictionary:entry]];	
		
		// Wrap things up
		NSLog(@"Archiving contact list");
		
		[NSKeyedArchiver archiveRootObject:[self matchDisplayNameWithId:contactMapping]
									toFile:[applicationDocumentsDirectory() stringByAppendingPathComponent:@"contact_mapping.plist"]];
		
		[NSKeyedArchiver archiveRootObject:contacts 
									toFile:[applicationDocumentsDirectory() stringByAppendingPathComponent:@"contacts.plist"]];
			
		// Good to go
		if (delegate && [delegate respondsToSelector:@selector(doneFetchingContactList)]) {
			[delegate performSelector:@selector(doneFetchingContactList)];
		}
	} else {
		alertMessage(@"Error", @"Unable to fetch your profile");
	}	
}

- (NSDictionary *)matchDisplayNameWithId:(NSArray *)data {
	NSMutableDictionary *retVal = [[NSMutableDictionary alloc] init];
	
	for (NSDictionary *contact in data) {
		[retVal setObject:[contact objectForKey:@"displayName"] 
				   forKey:[contact objectForKey:@"id"]];
	}
	
	return [retVal autorelease];
}

@end
