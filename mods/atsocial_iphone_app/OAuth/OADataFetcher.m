//
//  OADataFetcher.m
//  OAuthConsumer
//
//  Created by Jon Crosby on 11/5/07.
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


#import "OADataFetcher.h"


@implementation OADataFetcher

+ (void)fetchDataWithRequest:(OAMutableURLRequest *)request 
                    delegate:(id)delegate 
           didFinishSelector:(SEL)didFinishSelector {
  NSURLResponse *response;
  NSError *error = nil;
  
  [request prepare];
  // TODO: use the asynch fetcher from Google's open source library. 
  // Never do sync fetches in a client app on the main thread. 
  NSData *responseData = [NSURLConnection sendSynchronousRequest:request
                                               returningResponse:&response
                                                           error:&error];
  
  NSString *responseBody = [[[NSString alloc] initWithData:responseData encoding:NSUTF8StringEncoding] autorelease]; 
  NSLog(@"Request url: %@", [[request URL] absoluteString]);
  NSLog(@"Response Body: %@", responseBody);
  
  if (response == nil || responseData == nil || error != nil) {
    OAServiceTicket *ticket = [[[OAServiceTicket alloc] initWithRequest:request response:response didSucceed:NO] autorelease];
    [delegate performSelector:didFinishSelector withObject:ticket withObject:error];
    
  } else {
    NSLog(@"response had status code %i", [(NSHTTPURLResponse *)response statusCode]);
    OAServiceTicket *ticket = [[[OAServiceTicket alloc] initWithRequest:request
                                                               response:response
                                                             didSucceed:[(NSHTTPURLResponse *)response statusCode] < 400] autorelease];
    
    [delegate performSelector:didFinishSelector withObject:ticket withObject:responseBody];
  }   
}

@end
