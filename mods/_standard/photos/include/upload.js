/*
Copyright 2008-2009 University of Cambridge
Copyright 2008-2009 University of Toronto
Copyright 2008-2009 University of California, Berkeley

Licensed under the Educational Community License (ECL), Version 2.0 or the New
BSD license. You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the ECL 2.0 License and BSD License at
https://source.fluidproject.org/svn/LICENSE.txt
*/

/*global jQuery*/
/*global fluid*/
/*global demo*/

var demo = demo || {};

(function ($, fluid) {
    demo.initUploader = function () {
        fluid.progressiveEnhanceableUploader(".flc-uploader", ".fl-progEnhance-basic", {
            demo: true,                
            uploadManager: {
                type: "fluid.swfUploadManager",        
                options: {
                   // Set the uploadURL to the URL for posting files to your server.
                   uploadURL: "http://localhost/atutor155/atutor_163/mods/photo_album/albums.php",
    
                   // This option points to the location of the SWFUpload Flash object that ships with Fluid Infusion.
                   flashURL: "../../jscript/infusion/lib/swfupload/flash/swfupload.swf"
                }
            },
            decorators: [{
                type: "fluid.swfUploadSetupDecorator",
                options: {
                    // This option points to the location of the Browse Files button used with Flash 10 clients.
                    flashButtonImageURL: "../../jscript/infusion/components/uploader/images/browse.png"
                }
            }]
        });    
    }
})(jQuery, fluid);


  