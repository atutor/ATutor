//
//  QAWebController.h
//  ATutor
//
//  Created by Quang Anh Do on 30/05/2010.
//  Copyright 2010 Quang Anh Do. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <Three20/Three20.h>

@protocol QAWebControllerDelegate;

@interface QAWebController : TTWebController {
	id oAuthDelegate;
}

@property (nonatomic, assign) id<QAWebControllerDelegate> oAuthDelegate;

@end

@protocol QAWebControllerDelegate

- (void)didFinishAuthorizationInWebViewController:(QAWebController *)webViewController;

@end

