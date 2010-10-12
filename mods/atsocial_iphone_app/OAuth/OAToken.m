//
//  OAToken.m
//  OAuthConsumer
//
//  Created by Jon Crosby on 10/19/07.
//  Copyright 2007 Kaboomerang LLC. All rights reserved.
//  Modified by Cassie Doll on 02/02/09
//
//  Permission is hereby granted, free of charge, to any person obtaining a copy
//  of this software and associated documentation files (the "Software"), to deal
//  in the Software without restriction, including without limitation the rights
//  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
//  copies of the Software, and to permit persons to whom the Software is
//  furnished to do so, subject to the following conditions:
//
//  The above copyright notice and this permission notice shall be included in
//  all copies or substantial portions of the Software.
//
//  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
//  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
//  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
//  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
//  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
//  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
//  THE SOFTWARE.


#import "OAToken.h"
#import "NSString+URLEncoding.h"

@implementation OAToken

@synthesize key, secret;

#pragma mark init

- (id)init 
{
  if (self = [super init])
  {
    self.key = @"";
    self.secret = @"";
  }
  return self;
}

- (id)initWithKey:(NSString *)aKey secret:(NSString *)aSecret 
{
  if (self = [super init])
  {
    self.key = aKey;
    self.secret = aSecret;
  }
  return self;
}

- (id)initWithHTTPResponseBody:(NSString *)body 
{
  if (self = [super init])
  {
    NSArray *pairs = [body componentsSeparatedByString:@"&"];
    
    for (NSString *pair in pairs) {
      NSArray *elements = [pair componentsSeparatedByString:@"="];
      if ([[elements objectAtIndex:0] isEqualToString:@"oauth_token"]) {
        self.key = [[elements objectAtIndex:1] URLDecodedString];
      } else if ([[elements objectAtIndex:0] isEqualToString:@"oauth_token_secret"]) {
        self.secret = [[elements objectAtIndex:1] URLDecodedString];
      }
    }
  }    
  return self;
}

- (void)dealloc
{
  [key release];
  [secret release];
  [super dealloc];
}

@end
