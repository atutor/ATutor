//
//  LauncherViewController.h
//  ATutor
//
//  Created by Quang Anh Do on 25/05/2010.
//  Copyright 2010 Quang Anh Do. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <Three20/Three20.h>
#import "QAWebController.h"
#import "OSConsumer.h"

@interface LauncherViewController : TTViewController <TTLauncherViewDelegate, QAWebControllerDelegate> {
	TTLauncherView *launcherView;
	UIBarButtonItem *logoutButton;
	OSConsumer *consumer;
}

@property (nonatomic, retain) TTLauncherView *launcherView;
@property (nonatomic, retain) UIBarButtonItem *logoutButton;
@property (nonatomic, retain) OSConsumer *consumer;

- (void)restorePages;

@end
