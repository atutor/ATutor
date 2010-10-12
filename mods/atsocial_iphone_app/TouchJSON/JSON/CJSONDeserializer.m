//
//  CJSONDeserializer.m
//  TouchJSON
//
//  Created by Jonathan Wight on 12/15/2005.
//  Copyright 2005 Toxic Software. All rights reserved.
//

#import "CJSONDeserializer.h"

#import "CJSONScanner.h"
#import "CDataScanner.h"

@implementation CJSONDeserializer

+ (id)deserializer
{
return([[[self alloc] init] autorelease]);
}

- (id)deserialize:(NSData *)inData error:(NSError **)outError
{
CJSONScanner *theScanner = [CJSONScanner scannerWithData:inData];
id theObject = NULL;
if ([theScanner scanJSONObject:&theObject error:outError] == YES)
	return(theObject);
else
	return(NULL);
}

@end
