//
//  ATutorAppDelegate.h
//  ATutor
//
//  Created by Quang Anh Do on 25/05/2010.
//  Copyright Quang Anh Do 2010. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "LauncherViewController.h"
#import "QAWebController.h"
#import "ATutorHelper.h"
#import "IASKAppSettingsViewController.h"

@class OSConsumer;

@interface ATutorAppDelegate : NSObject <UIApplicationDelegate, ATutorHelperDelegate, IASKSettingsDelegate> {
    UIWindow *window;
	OSConsumer *consumer;
	LauncherViewController *launcher;
	QAWebController *webController;
	ATutorHelper *helper;
	IASKAppSettingsViewController *settingsViewController;
}

@property (nonatomic, retain) IBOutlet UIWindow *window;
@property (nonatomic, retain) OSConsumer *consumer;
@property (nonatomic, retain) LauncherViewController *launcher;
@property (nonatomic, retain) TTWebController *webController;
@property (nonatomic, retain) ATutorHelper *helper;
@property (nonatomic, retain) IASKAppSettingsViewController *settingsViewController;

@end

