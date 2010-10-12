//
//  ATutorHelper.h
//  ATutor
//
//  Created by Quang Anh Do on 07/06/2010.
//  Copyright 2010 Quang Anh Do. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "OSConsumer.h"

@protocol ATutorHelperDelegate;

@interface ATutorHelper : NSObject {
	OSConsumer *consumer;
	
	int numberOfContacts;
	NSMutableArray *contacts;
	NSMutableArray *contactMapping;
	
	id delegate;
}

@property (nonatomic, retain) OSConsumer *consumer;
@property int numberOfContacts;
@property (nonatomic, retain) NSMutableArray *contacts;
@property (nonatomic, retain) NSMutableArray *contactMapping;
@property (nonatomic, assign) id<ATutorHelperDelegate> delegate;

- (void)fetchContactList;
- (void)fetchOwnProfile;

@end

@protocol ATutorHelperDelegate

- (void)doneFetchingContactList;

@end