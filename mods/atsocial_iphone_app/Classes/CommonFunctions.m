//
//  CommonFunctions.m
//  ATutor
//
//  Created by Quang Anh Do on 30/05/2010.
//  Copyright 2010 Quang Anh Do. All rights reserved.
//

#import "CommonFunctions.h"

#import <SystemConfiguration/SystemConfiguration.h>
#import "SFHFKeychainUtils.h"

@implementation CommonFunctions

void alertMessage(NSString *title, NSString *message) {
	UIAlertView *alertView = [[UIAlertView alloc] initWithTitle:TTLocalizedString(title, @"")
														message:TTLocalizedString(message, @"")
													   delegate:nil cancelButtonTitle:TTLocalizedString(@"OK", @"") otherButtonTitles:nil];
	[alertView show];	
	[alertView release];
}

BOOL dataSourceAvailable() {
	Boolean success;    
	const char *host_name = "www.google.com";
	
	SCNetworkReachabilityRef reachability = SCNetworkReachabilityCreateWithName(NULL, host_name);
	SCNetworkReachabilityFlags flags;
	success = SCNetworkReachabilityGetFlags(reachability, &flags);
	BOOL _isDataSourceAvailable = success && (flags & kSCNetworkFlagsReachable) && !(flags & kSCNetworkFlagsConnectionRequired);
	CFRelease(reachability);
	
    return _isDataSourceAvailable;
} 

NSString *applicationDocumentsDirectory() {
	return [NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES) lastObject];
}

BOOL isLoggedIn() {
	return ![[SFHFKeychainUtils getPasswordForUsername:@"accessToken" andServiceName:kATutor error:nil] isEqualToString:@""] 
	&& ![[SFHFKeychainUtils getPasswordForUsername:@"requestToken" andServiceName:kATutor error:nil] isEqualToString:@""];
}

NSString *shortLinkToContact(int id) {
	return [NSString stringWithFormat:@"atutor://contact/%d", id];
}

NSString *linkToContact(int id, NSString *name) {
	return [NSString stringWithFormat:@"atutor://contact/%d/%@", id, [name stringByAddingPercentEscapesUsingEncoding:NSUTF8StringEncoding]];
}

NSString *rewriteURLStrings(NSString *content) {
	NSMutableString *retVal = [NSMutableString stringWithString:content];
	
	// Links to contacts
	[retVal replaceOccurrencesOfString:[NSString stringWithFormat:@"%@/mods/_standard/social/sprofile.php?id=", kATutorURL] 
							withString:@"atutor://contact/"
							   options:NSCaseInsensitiveSearch
								 range:NSMakeRange(0, [retVal length])];
	
	return retVal;
}

NSString *niceTimeString(NSString *timeString) {
	NSDateFormatter *dateFormatter = [[[NSDateFormatter alloc] init] autorelease];
	[dateFormatter setDateFormat:@"yyyy-MM-dd HH:mm:ss"];
	
	NSDate *date = [dateFormatter dateFromString:timeString];
	[dateFormatter setDateStyle:NSDateFormatterShortStyle];
	
	return [dateFormatter stringFromDate:date];
}

@end
