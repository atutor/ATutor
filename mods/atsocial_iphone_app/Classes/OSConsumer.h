//
//  OSConsumer.h
//  ATutor
//
//  Created by Quang Anh Do on 29/05/2010.
//  Copyright 2010 Quang Anh Do. All rights reserved.
//

#import <Foundation/Foundation.h>

@class OSProvider;
@class OAConsumer;
@class OAToken;

@interface OSConsumer : NSObject {
	OAConsumer *consumer;	
	OAToken *accessToken;
	OSProvider *currentProvider;
}

@property (nonatomic, retain) OAToken *accessToken;
@property (nonatomic, retain) OAConsumer *consumer;
@property (nonatomic, retain) OSProvider *currentProvider;

- (id)initWithProvider:(OSProvider *)provider;
- (void)getDataForUrl:(NSString *)relativeUrl 
        andParameters:(NSArray*)params 
			 delegate:(id)delegate 
	didFinishSelector:(SEL)didFinishSelector;
- (void)startAuthProcess;
- (void)finishAuthProcess;
- (void)clearAuthentication;

@end
