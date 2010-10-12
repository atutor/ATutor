//
//  CommonFunctions.h
//  ATutor
//
//  Created by Quang Anh Do on 30/05/2010.
//  Copyright 2010 Quang Anh Do. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <Three20/Three20.h>


@interface CommonFunctions : NSObject {

}

void alertMessage(NSString *title, NSString *message);
BOOL dataSourceAvailable();
NSString *applicationDocumentsDirectory();

BOOL isLoggedIn();

NSString *shortLinkToContact(int id);
NSString *linkToContact(int id, NSString *name);
NSString *rewriteURLStrings(NSString *content);
NSString *niceTimeString(NSString *timeString);

@end
