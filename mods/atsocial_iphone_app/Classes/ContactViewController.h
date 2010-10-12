//
//  ContactViewController.h
//  ATutor
//
//  Created by Quang Anh Do on 06/07/2010.
//  Copyright 2010 Quang Anh Do. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <AddressBook/AddressBook.h>
#import <AddressBookUI/AddressBookUI.h>


@interface ContactViewController : UIViewController {
	ABAddressBookRef addressBook;
}

- (id)initWithId:(int)identifier;
- (id)initWithId:(int)identifier name:(NSString *)name;

@end
