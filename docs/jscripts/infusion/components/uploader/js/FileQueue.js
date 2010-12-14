/*
Copyright 2008-2009 University of Toronto
Copyright 2008-2009 University of California, Berkeley
Copyright 2010 OCAD University

Licensed under the Educational Community License (ECL), Version 2.0 or the New
BSD license. You may not use this file except in compliance with one these
Licenses.

You may obtain a copy of the ECL 2.0 License and BSD License at
https://source.fluidproject.org/svn/LICENSE.txt
*/

/*global SWFUpload, jQuery, fluid_1_3:true*/

fluid_1_3 = fluid_1_3 || {};

(function ($, fluid) {
    
    fluid.uploader = fluid.uploader || {};
    
    var filterFiles = function (files, filterFn) {
        var filteredFiles = [];
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            if (filterFn(file) === true) {
                filteredFiles.push(file);
            }
        }
        
        return filteredFiles;
    };
     
    fluid.uploader.fileQueue = function () {
        var that = {};
        that.files = [];
        that.isUploading = false;
        
        /********************
         * Queue Operations *
         ********************/
         
        that.start = function () {
            that.setupCurrentBatch();
            that.isUploading = true;
            that.shouldStop = false;
        };
        
        that.startFile = function () {
            that.currentBatch.fileIdx++;
            that.currentBatch.bytesUploadedForFile = 0;
            that.currentBatch.previousBytesUploadedForFile = 0; 
        };
                
        that.finishFile = function (file) {
            that.currentBatch.numFilesCompleted++;
        };
        
        that.shouldUploadNextFile = function () {
            return !that.shouldStop && 
                   that.isUploading && 
                   that.currentBatch.numFilesCompleted < that.currentBatch.files.length;
        };
        
        /*****************************
         * File manipulation methods *
         *****************************/
         
        that.addFile = function (file) {
            that.files.push(file);    
        };
        
        that.removeFile = function (file) {
            var idx = $.inArray(file, that.files);
            that.files.splice(idx, 1);        
        };
        
        /**********************
         * Queue Info Methods *
         **********************/
         
        that.totalBytes = function () {
            return fluid.uploader.fileQueue.sizeOfFiles(that.files);
        };

        that.getReadyFiles = function () {
            return filterFiles(that.files, function (file) {
                return (file.filestatus === fluid.uploader.fileStatusConstants.QUEUED || file.filestatus === fluid.uploader.fileStatusConstants.CANCELLED);
            });        
        };
        
        that.getErroredFiles = function () {
            return filterFiles(that.files, function (file) {
                return (file.filestatus === fluid.uploader.fileStatusConstants.ERROR);
            });        
        };
        
        that.sizeOfReadyFiles = function () {
            return fluid.uploader.fileQueue.sizeOfFiles(that.getReadyFiles());
        };
        
        that.getUploadedFiles = function () {
            return filterFiles(that.files, function (file) {
                return (file.filestatus === fluid.uploader.fileStatusConstants.COMPLETE);
            });        
        };

        that.sizeOfUploadedFiles = function () {
            return fluid.uploader.fileQueue.sizeOfFiles(that.getUploadedFiles());
        };

        /*****************
         * Batch Methods *
         *****************/
         
        that.setupCurrentBatch = function () {
            that.clearCurrentBatch();
            that.updateCurrentBatch();
        };
        
        that.clearCurrentBatch = function () {
            that.currentBatch = {
                fileIdx: -1,
                files: [],
                totalBytes: 0,
                numFilesCompleted: 0,
                numFilesErrored: 0,
                bytesUploadedForFile: 0,
                previousBytesUploadedForFile: 0,
                totalBytesUploaded: 0
            };
        };
        
        that.updateCurrentBatch = function () {
            var readyFiles = that.getReadyFiles();
            that.currentBatch.files = readyFiles;
            that.currentBatch.totalBytes = fluid.uploader.fileQueue.sizeOfFiles(readyFiles);
        };
        
        that.updateBatchStatus = function (currentBytes) {
            var byteIncrement = currentBytes - that.currentBatch.previousBytesUploadedForFile;
            that.currentBatch.totalBytesUploaded += byteIncrement;
            that.currentBatch.bytesUploadedForFile += byteIncrement;
            that.currentBatch.previousBytesUploadedForFile = currentBytes;
        };
                
        return that;
    };
    
    fluid.uploader.fileQueue.sizeOfFiles = function (files) {
        var totalBytes = 0;
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            totalBytes += file.size;
        }        
        return totalBytes;
    };
          
})(jQuery, fluid_1_3);
