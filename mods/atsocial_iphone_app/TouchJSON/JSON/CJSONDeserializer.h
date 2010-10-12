//
//  CJSONDeserializer.h
//  TouchJSON
//
//  Created by Jonathan Wight on 12/15/2005.
//  Copyright 2005 Toxic Software. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface CJSONDeserializer : NSObject {

}

+ (id)deserializer;

- (id)deserialize:(NSData *)inData error:(NSError **)outError;

@end
