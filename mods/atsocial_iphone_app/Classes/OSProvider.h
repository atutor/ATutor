//
//  OSProvider.h
//  ATutor
//
//  Created by Quang Anh Do on 29/05/2010.
//  Copyright 2010 Quang Anh Do. All rights reserved.
//

#import <Foundation/Foundation.h>


@interface OSProvider : NSObject {
	NSString *requestUrl;
	NSString *authorizeUrl;
	NSString *accessUrl;
	NSString *endpointUrl;
	
	NSArray *extraRequestUrlParams;
	BOOL isOpenSocial;
	
	NSString *consumerKey;
	NSString *consumerSecret;
	
	NSString *name; 
}

@property(retain) NSString *requestUrl;
@property(retain) NSString *authorizeUrl;
@property(retain) NSString *accessUrl;
@property(retain) NSString *endpointUrl;

@property(retain) NSArray *extraRequestUrlParams;
@property BOOL isOpenSocial;

@property(retain) NSString *consumerKey;
@property(retain) NSString *consumerSecret;

@property(retain) NSString *name;

+ (OSProvider *)getATutorProviderWithKey:(NSString *)key withSecret:(NSString *)secret;

@end
