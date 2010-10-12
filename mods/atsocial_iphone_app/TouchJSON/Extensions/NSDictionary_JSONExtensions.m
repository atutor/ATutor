//
//  NSDictionary_JSONExtensions.m
//  TouchJSON
//
//  Created by Jonathan Wight on 04/17/08.
//  Copyright 2008 toxicsoftware.com. All rights reserved.
//

#import "NSDictionary_JSONExtensions.h"

#import "CJSONDeserializer.h"

@implementation NSDictionary (NSDictionary_JSONExtensions)

+ (id)dictionaryWithJSONData:(NSData *)inData error:(NSError **)outError
{
return([[CJSONDeserializer deserializer] deserialize:inData error:outError]);
}

@end
