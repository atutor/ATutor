    //
//  QAWebController.m
//  ATutor
//
//  Created by Quang Anh Do on 30/05/2010.
//  Copyright 2010 Quang Anh Do. All rights reserved.
//

#import "QAWebController.h"


@implementation QAWebController

@synthesize oAuthDelegate;

- (void)dealloc {
	[oAuthDelegate release];
	
	[super dealloc];
}

- (BOOL)webView:(UIWebView*)webView shouldStartLoadWithRequest:(NSURLRequest*)request navigationType:(UIWebViewNavigationType)navigationType {
	// Ignore normal URLs
	if (![[[request URL] scheme] isEqualToString:@"internal"]) {
		return [super webView:webView shouldStartLoadWithRequest:request navigationType:navigationType];
	}	
	
	// OAuth
	if ([[[request URL] host] isEqualToString:@"finish-auth"]) {
		if (oAuthDelegate && [oAuthDelegate respondsToSelector:@selector(didFinishAuthorizationInWebViewController:)]) {
			[oAuthDelegate performSelector:@selector(didFinishAuthorizationInWebViewController:) withObject:self];
		}
	}
	
	return YES;
}

@end
