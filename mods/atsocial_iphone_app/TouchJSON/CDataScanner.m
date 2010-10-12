//
//  CDataScanner.m
//  TouchJSON
//
//  Created by Jonathan Wight on 04/16/08.
//  Copyright 2008 toxicsoftware.com. All rights reserved.
//

#import "CDataScanner.h"

#import "CDataScanner_Extensions.h"

@interface CDataScanner ()
@property (readwrite, retain) NSCharacterSet *doubleCharacters;
@end

#pragma mark -

inline static unichar CharacterAtPointer(void *start, void *end)
{

	const u_int8_t theByte = *(u_int8_t *)start;
if (theByte & 0x80)
	{
	// TODO -- UNICODE!!!!
	}
const unichar theCharacter = theByte;
return(theCharacter);
}

@implementation CDataScanner

@dynamic data;
@dynamic scanLocation;
@dynamic isAtEnd;
@synthesize doubleCharacters;

+ (id)scannerWithData:(NSData *)inData
{
CDataScanner *theScanner = [[[self alloc] init] autorelease];
theScanner.data = inData;
return(theScanner);
}

- (id)init
{
if ((self = [super init]) != nil)
	{
	self.doubleCharacters = [NSCharacterSet characterSetWithCharactersInString:@"0123456789eE-."];
	}
return(self);
}

- (void)dealloc
{
self.data = NULL;
self.doubleCharacters = NULL;
//
[super dealloc];
}

- (NSInteger)scanLocation
{
return current - start;
}

- (NSData *)data
{
return(data); 
}

- (void)setData:(NSData *)inData
{
if (data != inData)
	{
	if (data)
		{
		[data autorelease];
		data = NULL;
		}
	
	if (inData)
		{
		data = [inData retain];
		//
		start = (u_int8_t *)data.bytes;
		end = start + data.length;
		current = start;
		length = data.length;
		}
    }
}

- (void)setScanLocation:(NSInteger)inScanLocation
{
current = start + inScanLocation;
}

- (BOOL)isAtEnd
{
return(self.scanLocation >= length);
}

- (unichar)currentCharacter
{
return(CharacterAtPointer(current, end));
}

- (unichar)scanCharacter
{
const unichar theCharacter = CharacterAtPointer(current++, end);
return(theCharacter);
}

- (BOOL)scanCharacter:(unichar)inCharacter
{
unichar theCharacter = CharacterAtPointer(current, end);
if (theCharacter == inCharacter)
	{
	++current;
	return(YES);
	}
else
	return(NO);
}

- (BOOL)scanUTF8String:(const char *)inString intoString:(NSString **)outValue;
{
const size_t theLength = strlen(inString);
if (end - current < theLength)
	return(NO);
if (strncmp((char *)current, inString, theLength) == 0)
	{
	current += theLength;
	if (outValue)
		*outValue = [NSString stringWithUTF8String:inString];
	return(YES);
	}
return(NO);
}

- (BOOL)scanString:(NSString *)inString intoString:(NSString **)outValue
{
if (end - current < inString.length)
	return(NO);
if (strncmp((char *)current, [inString UTF8String], inString.length) == 0)
	{
	current += inString.length;
	if (outValue)
		*outValue = inString;
	return(YES);
	}
return(NO);
}

- (BOOL)scanCharactersFromSet:(NSCharacterSet *)inSet intoString:(NSString **)outValue
{
u_int8_t *P;
for (P = current; P < end && [inSet characterIsMember:*P] == YES; ++P)
	;

if (P == current)
	{
	return(NO);
	}

if (outValue)
	{
	*outValue = [[[NSString alloc] initWithBytes:current length:P - current encoding:NSUTF8StringEncoding] autorelease];
	}
	
current = P;

return(YES);
}

- (BOOL)scanUpToString:(NSString *)inString intoString:(NSString **)outValue
{
const char *theToken = [inString UTF8String];
const char *theResult = strnstr((char *)current, theToken, end - current);
if (theResult == NULL)
	{
	return(NO);
	}

if (outValue)
	{
	*outValue = [[[NSString alloc] initWithBytes:current length:theResult - (char *)current encoding:NSUTF8StringEncoding] autorelease];
	}

current = (u_int8_t *)theResult;

return(YES);
}

- (BOOL)scanUpToCharactersFromSet:(NSCharacterSet *)inSet intoString:(NSString **)outValue
{
u_int8_t *P;
for (P = current; P < end && [inSet characterIsMember:*P] == NO; ++P)
	;

if (P == current)
	{
	return(NO);
	}

if (outValue)
	{
	*outValue = [[[NSString alloc] initWithBytes:current length:P - current encoding:NSUTF8StringEncoding] autorelease];
	}
	
current = P;

return(YES);
}

- (BOOL)scanNumber:(NSNumber **)outValue
{
// Replace all of this with a strtod call
NSString *theString = NULL;
if ([self scanCharactersFromSet:doubleCharacters intoString:&theString])
	{
	if (outValue)
		*outValue = [NSNumber numberWithDouble:[theString doubleValue]]; // TODO dont use doubleValue
	return(YES);
	}
return(NO);
}

@end
