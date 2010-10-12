//
//  OAMutableURLRequest.m
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


#import "OAMutableURLRequest.h"
#import "OAConsumer.h"
#import "OAToken.h"
#import "OAHMAC_SHA1SignatureProvider.h"
#import "OARequestParameter.h"

@interface OAMutableURLRequest ()
@property (nonatomic, retain) id<OASignatureProviding> signatureProvider;
- (NSString *)_generateTimestamp;
- (NSString *)_generateNonce;
- (NSString *)_signatureBaseString;
- (void)_putParametersInRequest;
- (NSString *)_getNormalizedRequestParameters;
@end

@implementation OAMutableURLRequest
@synthesize signatureProvider;
@synthesize signature, nonce, parameters;

#pragma mark init

- (id)initWithURL:(NSURL *)aUrl consumer:(OAConsumer *)aConsumer token:(OAToken *)aToken {     
  return [self initWithURL:aUrl parameters:nil consumer:aConsumer token:aToken];
}

- (id)initWithURL:(NSURL *)aUrl parameters:(NSArray *)extraParameters
         consumer:(OAConsumer *)aConsumer token:(OAToken *)aToken {     
  return [self initWithURL:aUrl parameters:extraParameters consumer:aConsumer token:aToken realm:nil 
         signatureProvider:nil nonce:[self _generateNonce] timestamp:[self _generateTimestamp]];
}

- (id)initWithURL:(NSURL *)aUrl consumer:(OAConsumer *)aConsumer token:(OAToken *)aToken
            realm:(NSString *)aRealm signatureProvider:(id<OASignatureProviding, NSObject>)aProvider {     
  return [self initWithURL:aUrl parameters:nil consumer:aConsumer token:aToken realm:aRealm 
         signatureProvider:aProvider nonce:[self _generateNonce] timestamp:[self _generateTimestamp]];
}

// Setting a timestamp and nonce to known
// values can be helpful for testing
- (id)initWithURL:(NSURL *)aUrl
       parameters:(NSArray *)extraParameters
         consumer:(OAConsumer *)aConsumer
            token:(OAToken *)aToken
            realm:(NSString *)aRealm
signatureProvider:(id<OASignatureProviding, NSObject>)aProvider
            nonce:(NSString *)aNonce
        timestamp:(NSString *)aTimestamp {
  if (self = [super initWithURL:aUrl
                    cachePolicy:NSURLRequestReloadIgnoringCacheData
                timeoutInterval:20.0]) {    
    consumer = [aConsumer retain];
    
    // empty token for Unauthorized Request Token transaction
    if (aToken == nil) {
      token = nil;
    } else {
      token = [aToken retain];
    }
    
    if (aRealm == nil) {
      realm = @"";
    } else {
      realm = [aRealm retain];
    }
    
    // default to HMAC-SHA1
    if (aProvider == nil) {
      self.signatureProvider = [[[OAHMAC_SHA1SignatureProvider alloc] init] autorelease];
    } else { 
      self.signatureProvider = aProvider;
    }
    
    timestamp = [aTimestamp retain];
    nonce = [aNonce retain];
    
    if (extraParameters) {
      self.parameters = [NSMutableArray arrayWithArray:extraParameters];
    } else {
      self.parameters = [NSMutableArray arrayWithCapacity:7];
    }
  }
  return self;
}

- (void)dealloc {
  [consumer release];
  [token release];
  [realm release];
  [signatureProvider release];
  [timestamp release];
  [nonce release];
  [parameters release];
  [super dealloc];
}

#pragma mark -
#pragma mark Public

- (void)prepare {
  // Add in all of the oauth parameters
  [parameters addObject:[OARequestParameter requestParameterWithName:@"oauth_consumer_key" value:consumer.key]];
  [parameters addObject:[OARequestParameter requestParameterWithName:@"oauth_signature_method" value:[signatureProvider name]]];
  [parameters addObject:[OARequestParameter requestParameterWithName:@"oauth_timestamp" value:timestamp]];
  [parameters addObject:[OARequestParameter requestParameterWithName:@"oauth_nonce" value:nonce]];
  [parameters addObject:[OARequestParameter requestParameterWithName:@"oauth_version" value:@"1.0"]];
  
  if (token) {
    [parameters addObject:[OARequestParameter requestParameterWithName:@"oauth_token" value:token.key]];
  }
  
  // TODO: if later RSA-SHA1 support is added then a little code redesign is needed
  NSString *baseString = [self _signatureBaseString];
  NSString *secret = [NSString stringWithFormat:@"%@&%@",
                      [consumer.secret URLEncodedString],
                      token ? [token.secret URLEncodedString] : @""];
  
  NSLog(@"Base string: %@ and secret: %@", baseString, secret);
  signature = [signatureProvider signClearText:baseString
                                    withSecret:secret];
  
  [parameters addObject:[OARequestParameter requestParameterWithName:@"oauth_signature" value:signature]];
  [self _putParametersInRequest];
}

#pragma mark -
#pragma mark Private

- (NSString *)_generateTimestamp {
  return [NSString stringWithFormat:@"%d", time(NULL)];
}

- (NSString *)_generateNonce {
  CFUUIDRef theUUID = CFUUIDCreate(NULL);
  CFStringRef string = CFUUIDCreateString(NULL, theUUID);
  NSMakeCollectable(theUUID);
  return [(NSString *)string autorelease];
}

- (NSString *)_signatureBaseString {
  return [NSString stringWithFormat:@"%@&%@&%@",
          [self HTTPMethod],
          [[[self URL] absoluteString] URLEncodedString],
          [[self _getNormalizedRequestParameters] URLEncodedString]];
}

- (void)_putParametersInRequest {  
  NSString *normalizedRequestParameters = [self _getNormalizedRequestParameters];
  
  if ([[self HTTPMethod] isEqualToString:@"GET"] || [[self HTTPMethod] isEqualToString:@"DELETE"]) {
    [self setURL:[NSURL URLWithString:[NSString stringWithFormat:@"%@?%@", [[self URL] absoluteString], normalizedRequestParameters]]];
    
  } else {
    // POST, PUT
    NSData *postData = [normalizedRequestParameters dataUsingEncoding:NSASCIIStringEncoding allowLossyConversion:YES];
    [self setHTTPBody:postData];
    [self setValue:[NSString stringWithFormat:@"%d", [postData length]] forHTTPHeaderField:@"Content-Length"];
    [self setValue:@"application/x-www-form-urlencoded" forHTTPHeaderField:@"Content-Type"];
    
    NSString *oauthToken = @"";
    if (token) {
      oauthToken = [NSString stringWithFormat:@"oauth_token=\"%@\", ", [token.key URLEncodedString]];
    }
    
    NSString *oauthHeader = [NSString stringWithFormat:@"OAuth realm=\"%@\" oauth_consumer_key=\"%@\", %@oauth_signature_method=\"%@\", oauth_signature=\"%@\", oauth_timestamp=\"%@\", oauth_nonce=\"%@\", oauth_version=\"1.0\"",
                             [realm URLEncodedString],
                             [consumer.key URLEncodedString],
                             oauthToken,
                             [[signatureProvider name] URLEncodedString],
                             [signature URLEncodedString],
                             timestamp,
                             nonce];
    
    [self setValue:oauthHeader forHTTPHeaderField:@"Authorization"];
    [oauthToken release];
    [oauthHeader release];
  }
}

- (NSString *)_getNormalizedRequestParameters {
  NSMutableArray *parameterPairs = [NSMutableArray arrayWithCapacity:([parameters count])];
  
  for (OARequestParameter *param in parameters) {
    [parameterPairs addObject:[param URLEncodedNameValuePair]];
  }
  
  NSArray* sortedPairs = [parameterPairs sortedArrayUsingSelector:@selector(compare:)];
  return [sortedPairs componentsJoinedByString:@"&"];
}


@end
