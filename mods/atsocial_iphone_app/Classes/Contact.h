//
//  Contact.h
//  ATutor
//
//  Created by Quang Anh Do on 03/07/2010.
//  Copyright 2010 Quang Anh Do. All rights reserved.
//

#import <Foundation/Foundation.h>


@interface Contact : NSObject {
	int identifier;
	NSString *displayName;
}

@property int identifier;
@property (nonatomic, retain) NSString *displayName;

+ (Contact *)contactWithDictionary:(NSDictionary *)dictionary;

@end
