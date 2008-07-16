/* Fluid Multi-File Uploader Component
 * 
 * Built by The Fluid Project (http://www.fluidproject.org)
 * 
 * LEGAL
 * 
 * Copyright 2008 University of California, Berkeley
 * Copyright 2008 University of Toronto
 * 
 * Licensed under the Educational Community License (ECL), Version 2.0 or the New
 * BSD license. You may not use this file except in compliance with one these
 * Licenses.
 * 
 * You may obtain a copy of the ECL 2.0 License and BSD License at
 * https://source.fluidproject.org/svn/LICENSE.txt
 * 
 * DOCUMENTATION
 * Technical documentation is available at: http://wiki.fluidproject.org/x/d4ck
 * 
 */

/* TODO:
 * - handle duplicate file error
 * - make fields configurable
 *	   - strings (for i18n)
 * - refactor 'options' into more than one object as needed
 * - clean up debug code
 * - remove commented-out code
 * - use swfObj status to check states, etc. > drop our status obj
 */

/* ABOUT RUNNING IN LOCAL TEST MODE
 * To run locally using a fake upload, set uploadDefaults.uploadUrl to ''
 */

var fluid = fluid || {};

(function ($,fluid) {
	  
	/* these are the internal UI elements of the Uploader as defined in the 
	 * default HTML for the Fluid Uploader
	 */
	var defaultSelectors = {
		upload: ".fluid-uploader-upload",
		resume: ".fluid-uploader-resume",
		pause: ".fluid-uploader-pause",
		done: ".fluid-uploader-done",
		cancel: ".fluid-uploader-cancel",
		browse: ".fluid-uploader-browse",
		fluidUploader: ".fluid-uploader-queue-wrapper",
		fileQueue: ".fluid-uploader-queue",
		scrollingElement: ".fluid-scroller",
		emptyRow : ".fluid-uploader-row-placeholder",
		txtTotalFiles: ".fluid-uploader-totalFiles",
		txtTotalBytes: ".fluid-uploader-totalBytes",
		txtTotalFilesUploaded : ".fluid-uploader-num-uploaded",
		txtTotalBytesUploaded : ".fluid-uploader-bytes-uploaded",
		osModifierKey: ".fluid-uploader-modifierKey",
		txtFileStatus: ".removeFile",
		uploaderFooter : '.fluid-scroller-table-foot',
		qRowTemplate: '#queue-row-tmplt',
		qRowFileName: '.fileName',
		qRowFileSize: '.fileSize',
		qRowRemove: '.fileRemove',
		fileProgressor: '.file-progress',
		fileProgressText: ".file-progress-text",
		totalProgressor: '.total-progress',
		totalProgressText: ".fluid-scroller-table-foot .footer-total",
		debug: false
    };
	
    // Default configuration options.
	var uploadDefaults = {
		uploadUrl : "",
		flashUrl : "",
		fileSizeLimit : "20480",
		fileTypes : "*.*", 
		fileTypesText : "image files",
		fileUploadLimit : 0,
		fileQueueLimit : 0,
		elmUploaderControl: "",
		whenDone: "", // forces a refresh
		whenCancel: "", // forces a refresh
		whenFileUploaded: function(fileName, serverResponse) {},
		postParams: {},
		httpUploadElm: "",
		continueAfterUpload: true,
		continueDelay: 2000, //in milles
		queueListMaxHeight : 190,
        fragmentSelectors: defaultSelectors,
		// when to show the File browser
		// if false then the browser shows when the Browse button is clicked
		// if true
			// if using dialog then browser will show immediately
			// else browser will show as soon as dialog shows
		browseOnInit: false, 
		// dialog settings
		dialogDisplay: false,
		addFilesBtn: ".fluid-add-files-btn", // used in conjunction with dialog display to activate the Uploader
		debug: false
	};
	
	var dialog_settings = {
		title: "Upload Files", 
		width: 482,
		height: '', // left empty so that the dialog will auto-resize
		draggable: true, 
		modal: true, 
		resizable: false,
		autoOpen: false
	};
	
	var strings = {
		macControlKey: "Command",
		browseText: "Browse files",
		addMoreText: "Add more",
		fileUploaded: "File Uploaded",
		 	// tokens replaced by fluid.util.stringTemplate
		pausedLabel: "Paused at: %curFileN of %totalFilesN files (%currBytes of %totalBytes)",
		totalLabel: "Uploading: %curFileN of %totalFilesN files (%currBytes of %totalBytes)", 
		completedLabel: "Uploaded: %curFileN files (%totalCurrBytes)"
	};
		
	/* DOM Manipulation */
	
	/** 
	* adds a new file to the file queue in DOM
	* note: there are cases where a file will be added to the file queue but will not be in the actual queue 
	*/
	var addFileToQueue = function(uploaderContainer, file, fragmentSelectors, swfObj, status, maxHeight) {
		// make a new row
		var newQueueRow = $(fragmentSelectors.qRowTemplate).clone();
		// update the file name
		$(newQueueRow).children(fragmentSelectors.qRowFileName).text(file.name);
		// update the file size
		$(newQueueRow).children(fragmentSelectors.qRowFileSize).text(fluid.utils.filesizeStr(file.size));
		// update the file id and add the hover action
		newQueueRow.attr('id',file.id).css('display','none').addClass("ready row").hover(function(){
            if ($(this).hasClass('ready') && !$(this).hasClass('uploading')) {
                $(this).addClass('hover');
            }
        }, function(){
            if ($(this).hasClass('ready') && !$(this).hasClass('uploading')) {
                $(this).removeClass('hover');
            }
        });
        // insert the new row into the file queue
		$(fragmentSelectors.fileQueue, uploaderContainer).append(newQueueRow);
		
        // add remove action to the button
        $('#' + file.id, uploaderContainer).children(fragmentSelectors.qRowRemove).click(function(){
            removeRow(uploaderContainer, fragmentSelectors, $(this).parents('#' + file.id), swfObj, status, maxHeight);  
        });
        
        // display the new row
        $('#' + file.id, uploaderContainer).fadeIn('slow');
	};


	/** 
	* removes the defined row from the file queue 
	* @param {jQuery} 	uploaderContainer
	* @param {Object} 	fragmentSelectors	collection of Uploader DOM selectors 
	* @param {jQuery} 	row					a jQuery object for the row
	* @param {SWFUpload} swfObj				the SWF upload object
	* @param {Object} 	status				the status object to be updated
	* @return {jQuery}	returns row			the same jQuery object
	*/
	var removeRow = function(uploaderContainer, fragmentSelectors, row, swfObj, status, maxHeight) {
		row.fadeOut('fast', function (){
			var fileId = row.attr('id');
			var file = swfObj.getFile(fileId);
			queuedBytes (status, -file.size);
			swfObj.cancelUpload(fileId);
			row.remove();
			updateQueueHeight($(fragmentSelectors.scrollingElement, uploaderContainer), maxHeight);
			updateNumFiles(uploaderContainer, fragmentSelectors.txtTotalFiles, fragmentSelectors.fileQueue, fragmentSelectors.emptyRow);
			updateTotalBytes(uploaderContainer, fragmentSelectors.txtTotalBytes, status);
			updateStateByState(uploaderContainer,fragmentSelectors.fileQueue);
			updateBrowseBtnText(uploaderContainer, fragmentSelectors.fileQueue, fragmentSelectors.browse, status);
		});
		return row;
	};
	
	var updateQueueHeight = function(scrollingElm, maxHeight){
		var overMaxHeight = (scrollingElm.children().eq(0).height() > maxHeight);
		var setHeight = (overMaxHeight) ? maxHeight : '';
		scrollingElm.height( setHeight ) ;
		return overMaxHeight;
	};
	
	var scrollBottom = function(scrollingElm){
		// cast potentially a jQuery obj to a regular obj
		scrollingElm = $(scrollingElm)[0];
		// set the scrollTop to the scrollHeight
		scrollingElm.scrollTop = scrollingElm.scrollHeight;
	};
	
	var scrollTo = function(scrollingElm,row){
		if ($(row).prev().length) {
			var nextRow = $(row).next();
			row = (nextRow.length === 0) ? row : nextRow ;
		}
		
		var rowPosTop = $(row)[0].offsetTop;
		var rowHeight = $(row).height();
		var containerScrollTop = $(scrollingElm)[0].scrollTop;
		var containerHeight = $(scrollingElm).height();
		
		// if the top of the row is ABOVE the view port move the row into position
		if (rowPosTop < containerScrollTop) {
			$(scrollingElm)[0].scrollTop = rowPosTop;
		}
		
		// if the bottom of the row is BELOW the viewport then scroll it into position
		if ((rowPosTop + rowHeight) > (containerScrollTop + containerHeight)) {
			$(scrollingElm)[0].scrollTop = (rowPosTop - containerHeight + rowHeight);
		}
		//$(scrollingElm)[0].scrollTop = $(row)[0].offsetTop;
	};
	
	/**
	 * Updates the total number of rows in the queue in the UI
	 */
	var updateNumFiles = function(uploaderContainer, totalFilesSelector, fileQueueSelector) {
		$(totalFilesSelector, uploaderContainer).text(numberOfRows(uploaderContainer, fileQueueSelector));
	};
	
	/**
	 * Updates the total number of bytes in the UI
	 */
	var updateTotalBytes = function(uploaderContainer, totalBytesSelector, status) {
		$(totalBytesSelector, uploaderContainer).text(fluid.utils.filesizeStr(queuedBytes(status)));
	};
	 
    /*
     * Figures out the state of the uploader based on 
     * the number of files in the queue, and the number of files uploaded, 
     * or have errored, or are still to be uploaded
     * @param {String} uploaderContainer    the uploader container
     * @param {String} fileQueueSelector    the file queue used to test numbers.
     */
	var updateStateByState = function(uploaderContainer, fileQueueSelector) {
		var totalRows = numberOfRows(uploaderContainer, fileQueueSelector);
		var rowsUploaded = numFilesUploaded(uploaderContainer, fileQueueSelector);
		var rowsReady = numFilesToUpload(uploaderContainer, fileQueueSelector);
		
		fluid.utils.debug(
			"totalRows = " + totalRows + 
			"\nrowsUploaded = " + rowsUploaded + 
			"\nrowsReady = " + rowsReady
		);
		if (rowsUploaded > 0) { // we've already done some uploads
			if (rowsReady === 0) {
				updateState(uploaderContainer, 'empty');
			} else {
				updateState(uploaderContainer, 'reloaded');
			}
		} else if (totalRows === 0) {
			updateState(uploaderContainer, 'start');
		} else {
			updateState(uploaderContainer, 'loaded');
		}
	};
	
    /*
     * Sets the state (using a css class) for the top level element
     * @param {String} uploaderContainer    the uploader container
     * @param {String} stateClass    the file queue used to test numbers.
     */
	var updateState = function(uploaderContainer, stateClass) {
		$(uploaderContainer).children("div:first").attr('className',stateClass);
	};
	
	var updateBrowseBtnText = function(uploaderContainer, fileQueueSelector, browseButtonSelector, status) {
		if (numberOfRows(uploaderContainer, fileQueueSelector) > 0) {
			$(browseButtonSelector, uploaderContainer).text(strings.addMoreText);
		} else {
			$(browseButtonSelector, uploaderContainer).text(strings.browseText);
		}
	};
	
	var markRowComplete = function(row, fileStatusSelector, removeBtnSelector) {
		// update the status of the row to "uploaded"
		rowChangeState(row, removeBtnSelector, fileStatusSelector, 'uploaded', strings.fileUploaded);
	};
	
	var markRowError = function(row, fileStatusSelector, removeBtnSelector, scrollingElm, maxHeight, humanError) {
		// update the status of the row to "error"
		rowChangeState(row, removeBtnSelector, fileStatusSelector, 'error', 'File Upload Error');
		
		updateQueueHeight(scrollingElm, maxHeight);
		
		if (humanError !== '') {
            displayHumanReableError(row, humanError);
        }	
	};
	
	/* rows can only go from ready to error or uploaded */
	var rowChangeState = function(row, removeBtnSelector, fileStatusSelector, stateClass, stateMessage) {
		
		// remove the ready status and add the new status
		row.removeClass('ready').addClass(stateClass);
		
		// remove click event on Remove button
		$(row).find(removeBtnSelector).unbind('click');
		
		// add text status
		$(row).find(fileStatusSelector).attr('title',stateMessage);
	};
	
	var displayHumanReableError = function(row, humanError) {
		var newErrorRow = $('#queue-error-tmplt').clone();
		$(newErrorRow).find('.queue-error').html(humanError);
		$(newErrorRow).removeAttr('id').insertAfter(row);
	};
		
	// UTILITY SCRIPTS
	/**
	 * displays URL/URI or runs provided function
	 * does not validate action, unknown what it would do with other types of input
	 * @param {String, Function} action
	 */
	var variableAction = function(action) {
		if (action !== undefined) {
			if (typeof action === "function") {
				action();
			}
			else {
				location.href = action;
			}
		}
	};
	
	// SWF Upload Callback Handlers

    /*
     * @param {String} uploaderContainer    the uploader container
     * @param {int} maxHeight    maximum height in pixels for the file queue before scrolling
     * @param {Object} status    
     */
	var createFileQueuedHandler = function (uploaderContainer, fragmentSelectors, maxHeight, status) {
        return function(file){
            var swfObj = this;
            try {
				// what have we got?
                fluid.utils.debug(file.name + " file.size = " + file.size); // DEBUG
                
                // add the file to the queue
				addFileToQueue(uploaderContainer, file, fragmentSelectors, swfObj, status, maxHeight);
				
				updateStateByState(uploaderContainer, fragmentSelectors.fileQueue);

				var scrollingElm = $(fragmentSelectors.scrollingElement, uploaderContainer);
                
				// scroll to the bottom to reviel element
				if (updateQueueHeight(scrollingElm, maxHeight)) {
					scrollBottom(scrollingElm);
				}
				
                // add the size of the file to the variable maintaining the total size
                queuedBytes(status, file.size);
                // update the UI
				updateNumFiles(uploaderContainer, fragmentSelectors.txtTotalFiles, fragmentSelectors.fileQueue, fragmentSelectors.emptyRow);
                updateTotalBytes(uploaderContainer, fragmentSelectors.txtTotalBytes, status);
                
            } 
            catch (ex) {
                fluid.utils.debug(ex);
            }
        };
	};
		
	var createSWFReadyHandler = function (browseOnInit, allowMultipleFiles, useDialog) {
		return function(){
			if (browseOnInit && !useDialog) {
				browseForFiles(this,allowMultipleFiles);
			}
		};
	};
	
	function browseForFiles(swfObj,allowMultipleFiles) {
		if (allowMultipleFiles) {
			swfObj.selectFiles();
		}
		else {
			swfObj.selectFile();
		}
	}

	var createFileDialogStartHandler = function(uploaderContainer){
		return function(){
			try {
				$(uploaderContainer).children("div:first").addClass('browsing');
			} 
			catch (ex) {
				fluid.utils.debug(ex);
			}
		};
	};

	var createFileDialogCompleteHandler = function(uploaderContainer, fragmentSelectors, status) {
        return function(numSelected, numQueued){
            try {
                updateBrowseBtnText(uploaderContainer, fragmentSelectors.fileQueue, fragmentSelectors.browse, status);
				$(uploaderContainer).children("div:first").removeClass('browsing');
                debugStatus(status);
            } 
            catch (ex) {
                fluid.utils.debug(ex);
            }
        };
	};

	function fileQueueError(file, error_code, message) {
		// surface these errors in the queue
		try {
			var error_name = "";
			switch (error_code) {
			case SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED:
				error_name = "QUEUE LIMIT EXCEEDED";
				break;
			case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
				error_name = "FILE EXCEEDS SIZE LIMIT";
				break;
			case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
				error_name = "ZERO BYTE FILE";
				break;
			case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
				error_name = "INVALID FILE TYPE";
				break;
			default:
				error_name = "UNKNOWN";
				break;
			}
			var error_string = error_name + ":File ID: " + (typeof(file) === "object" && file !== null ? file.id : "na") + ":" + message;
			fluid.utils.debug ('error_string = ' + error_string);
		} catch (ex) {
			fluid.utils.debug (ex);
		}
	}	

    var createUploadStartHandler = function (uploaderContainer, fragmentSelectors, progressBar, status) {
        return function (file) {
            uploadStart (file, uploaderContainer, fragmentSelectors, progressBar, status);
        };
    };
    
	var uploadStart = function(file, uploaderContainer, fragmentSelectors, progressBar, status) {
		fluid.utils.debug("Upload Start Handler");
		updateState(uploaderContainer,'uploading');
		status.currError = ''; // zero out the error so we can check it later
		$("#"+file.id,uploaderContainer).addClass("uploading");
		progressBar.init('#'+file.id);
		scrollTo($(fragmentSelectors.scrollingElement, uploaderContainer),$("#"+file.id, uploaderContainer));
		uploadProgress(progressBar, uploaderContainer, file, 0, file.size, fragmentSelectors, status);
		fluid.utils.debug (
			"Starting Upload: " + (file.index + 1) + ' (' + file.id + ')' + ' [' + file.size + ']' + ' ' + file.name
		);
	};

	
	/* File and Queue Upload Progress */

    var createUploadProgressHandler = function (progressBar, uploaderContainer, fragmentSelectors, status) {
        return function(file, bytes, totalBytes) {
            uploadProgress (progressBar, uploaderContainer, file, bytes, totalBytes, fragmentSelectors, status);
        };
    };
    
	/* File Upload Error */
	var createUploadErrorHandler = function (uploaderContainer, progressBar, fragmentSelectors, maxHeight, status, options) {
        return function(file, error_code, message){
            uploadError(file, error_code, message,uploaderContainer, progressBar, fragmentSelectors, maxHeight, status, options);
        };
	};
	
	var uploadError = function (file, error_code, message, uploaderContainer, progressBar, fragmentSelectors, maxHeight, status, options) {
		fluid.utils.debug("Upload Error Handler");
		status.currError = '';
		status.continueOnError = false;
		var humanErrorMsg = '';
		var markError = true;
        try {
            switch (error_code) {
                case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
                    status.currError = "Error Code: HTTP Error, File name: " + file.name + ", Message: " + message;
					humanErrorMsg = 'An error occurred on the server during upload. It could be that the file already exists on the server.' + 
						formatErrorCode(message);
					status.continueOnError = true;
                    break;
                case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
                    status.currError = "Error Code: Upload Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message;
                    break;
                case SWFUpload.UPLOAD_ERROR.IO_ERROR:
                    status.currError = "Error Code: IO Error, File name: " + file.name + ", Message: " + message;
                    humanErrorMsg = 'An error occurred attempting to read the file from disk. The file was not uploaded.' + 
						formatErrorCode(message);
					status.continueOnError = true;
                    break;
                case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
                    status.currError = "Error Code: Security Error, File name: " + file.name + ", Message: " + message;
                    break;
                case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
                    status.currError = "Error Code: Upload Limit Exceeded, File name: " + file.name + ", File size: " + file.size + ", Message: " + message;
                    break;
                case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
                    status.currError = "Error Code: File Validation Failed, File name: " + file.name + ", File size: " + file.size + ", Message: " + message;
                    break;
                case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
                    status.currError = "File cancelled by user";
					status.continueOnError = true;
                    markError = false;
                    break;
                case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
                    status.currError = "Upload Stopped by user input";
					var pauseStrings = {
						curFileN: numFilesUploaded(uploaderContainer,fragmentSelectors.fileQueue), 
						totalFilesN: numberOfRows(uploaderContainer,fragmentSelectors.fileQueue), 
						currBytes: fluid.utils.filesizeStr(status.currBytes), 
						totalBytes: fluid.utils.filesizeStr(status.totalBytes)
					};
					var pausedString = fluid.utils.stringTemplate(strings.pausedLabel,pauseStrings);
					$(fragmentSelectors.totalProgressText, uploaderContainer).html(pausedString);

					updateState(uploaderContainer,'paused');
					
					markError = false;
                    break;
                default:
                    //				progress.SetStatus("Unhandled Error: " + error_code);
                    status.currError = "Error Code: " + error_code + ", File name: " + file.name + ", File size: " + file.size + ", Message: " + message;
                    break;
            }
							
			if (markError) {
                markRowError($('tr#' + file.id, uploaderContainer), fragmentSelectors.txtFileStatus, fragmentSelectors.qRowRemove, $(fragmentSelectors.scrollingElement, uploaderContainer), maxHeight, humanErrorMsg);
            }
            
			fluid.utils.debug(status.currError + '\n' + humanErrorMsg);
			
			// override continueAfterUpload
			options.continueAfterUpload = false;
        } 
        catch (ex) {
            fluid.utils.debug(ex);
        }		
	};
	
	var formatErrorCode = function(str) {
		return " (Error code: " + str + ")";
	};
	
	/* File Upload Success */
	
	var createUploadSuccessHandler =  function(uploaderContainer, progressBar, fragmentSelectors, whenFileUploaded, status){
		return function(file, server_data) {
			uploadSuccess(uploaderContainer, file, progressBar, fragmentSelectors, status, whenFileUploaded, server_data);
		};
	};	
	
	var uploadSuccess = function (uploaderContainer, file, progressBar, fragmentSelectors, status, whenFileUploaded, server_data){
		fluid.utils.debug("Upload Success Handler");
		
  		uploadProgress(progressBar, uploaderContainer, file, file.size, file.size, fragmentSelectors, status);
       	markRowComplete($('tr#' + file.id, uploaderContainer), fragmentSelectors.txtFileStatus, fragmentSelectors.qRowRemove);
		
 		try {
			whenFileUploaded(file.name, server_data);
		} 
		catch (ex) {
			 fluid.utils.debug(ex);
		}
	};
	
	
	/* File Upload Complete */
	
	var createUploadCompleteHandler = function (uploaderContainer, progressBar, fragmentSelectors, status, options, dialogObj) {
        return function(file){
			uploadComplete(this, file, uploaderContainer, progressBar, fragmentSelectors, status, options, dialogObj);
        };
	};
	
	var uploadComplete = function (swfObj, file, uploaderContainer, progressBar, fragmentSelectors, status, options, dialogObj) {
		fluid.utils.debug("Upload Complete Handler");
		
		$("#"+file.id,uploaderContainer).removeClass("uploading");
		
		var totalCount = numberOfRows(uploaderContainer, fragmentSelectors.fileQueue);
		
		var currStats = swfObj.getStats();
		
		fluid.utils.debug(
		"currStats.files_queued = " + currStats.files_queued + 
		"\ncurrStats.successful_uploads = " + currStats.successful_uploads + 
		"\ncurrStats.upload_errors = " + currStats.upload_errors
		);
				
    	if (currStats.files_queued === 0) {
            // we've completed all the files in this upload
            return fileQueueComplete(uploaderContainer, swfObj, options, progressBar, fragmentSelectors, dialogObj, status);
        }
       	else if (!status.currError || status.continueOnError) {
			// there was no error and there are still files to go
			uploadedBytes(status,file.size); // update the number of bytes that have actually be uploaded so far
            return swfObj.startUpload();  
        }
        else { 
			// there has been an error that we should stop on
        	// note: do not update the bytes because we didn't complete that last file
			return hideProgress(progressBar, true, $(fragmentSelectors.done, uploaderContainer));
        }
	};
	
	/* File Queue Complete */
	
	var fileQueueComplete = function(uploaderContainer, swfObj, options, progressBar, fragmentSelectors, dialogObj, status) {
		fluid.utils.debug("File Queue Complete Handler");
		
		updateState(uploaderContainer, 'done');
		var stats = swfObj.getStats();
		var newStrings = {
			curFileN: stats.successful_uploads,
			totalCurrBytes: fluid.utils.filesizeStr(status.totalBytes)
		};
		 
		$(fragmentSelectors.totalProgressText, uploaderContainer).html(fluid.utils.stringTemplate(strings.completedLabel,newStrings));
		hideProgress(progressBar, true, $(fragmentSelectors.done, uploaderContainer));
		options.continueDelay = (!options.continueDelay) ? 0 : options.continueDelay;
		if (options.continueAfterUpload) {
			setTimeout(function(){
				variableAction(options.whenDone);
			},options.continueDelay);
		}
	};
	
    /*
     * Return the queue size. If a number is passed in, increment the size first.
     */
	var queuedBytes = function (status, delta) {
		if (typeof delta === 'number') {
			status.totalBytes += delta;
		}
		return status.totalBytes;
	};
	
	var uploadedBytes = function (status, delta) {
		if (typeof delta === 'number') {
			status.currBytes += delta;
		}
		return status.currBytes;
	};
	
	function readyBytes(status) {
		return (status.totalBytes - status.currBytes);
	}

	
	function numberOfRows(uploaderContainer, fileQueueSelector) {
		return $(fileQueueSelector, uploaderContainer).find('.row').length ;
	}

	function numFilesToUpload(uploaderContainer, fileQueueSelector) {
		return $(fileQueueSelector, uploaderContainer).find('.ready').length ;
	}
	
	function numFilesUploaded(uploaderContainer, fileQueueSelector) {
		return $(fileQueueSelector, uploaderContainer).find('.uploaded').length;
	}
	
	/* PROGRESS
	 * 
	 */
	
	var uploadProgress = function(progressBar, uploaderContainer, file, fileBytes, totalFileBytes, fragmentSelectors, status) {
		fluid.utils.debug("Upload Progress Handler");
		
		fluid.utils.debug ('Upload Status : \n' + file.name + ' : ' + fileBytes + ' of ' + totalFileBytes + " bytes : \ntotal : " + (status.currBytes + fileBytes)  + ' of ' + queuedBytes(status) + " bytes");
		
		// update file progress
		var filePercent = fluid.utils.derivePercent(fileBytes,totalFileBytes);
		progressBar.updateProgress("file", filePercent, filePercent+"%");
		
		// update total 
		var totalQueueBytes = queuedBytes(status);
		var currQueueBytes = status.currBytes + fileBytes;
		var totalPercent = fluid.utils.derivePercent(currQueueBytes, totalQueueBytes);
		var fileIndex = file.index + 1;
		var numFilesInQueue = numberOfRows(uploaderContainer, fragmentSelectors.fileQueue);
		
		var totalHTML = totalStr(fileIndex,numFilesInQueue,currQueueBytes,totalQueueBytes);
		
		progressBar.updateProgress("total", totalPercent, totalHTML);		
	};
	
	function totalStr(fileIndex,numRows,bytes,totalBytes) {		
		var newStrings = {
			curFileN: fileIndex, 
			totalFilesN: numRows, 
			currBytes: fluid.utils.filesizeStr(bytes), 
			totalBytes: fluid.utils.filesizeStr(totalBytes)
		};
		
		return fluid.utils.stringTemplate(strings.totalLabel, newStrings);
	}
	
	var hideProgress = function(progressBar, dontPause, focusAfterHide) {
	 	progressBar.hide(dontPause);
        focusAfterHide.focus();
	};
	
	/* DIALOG
	 * 
	 */
	
 	var initDialog = function(uploaderContainer, addBtnSelector, browseOnInit, fileBrowseSelector) {
		dialogObj = uploaderContainer.dialog(dialog_settings).css('display','block');
		$(addBtnSelector).click(function(){
			$(dialogObj).dialog("open");
			if (browseOnInit) {
				$(fileBrowseSelector, uploaderContainer).click();
			}
		});

		return dialogObj;
	};
		
	var closeDialog = function(dialogObj) {
		$(dialogObj).dialog("close");
	};

	/* DEV CODE
	 * to be removed after beta or factored into unit tests
	 */
	
	function debugStatus(status) {
		fluid.utils.debug (
			"\n status.totalBytes = " + queuedBytes (status) + 
			"\n status.currCount = " + status.currCount + 
			"\n status.currBytes = " + status.currBytes + 
			"\n status.currError = " + status.currError +
			"\n status.continueOnError = " + status.continueOnError
			
		);
	}
	
	/* DEMO CODE
	 * this is code that fakes an upload with out a server
	 */

 
    // need to pass in current uploader
    
    var demoUpload = function (uploaderContainer, swfObj, progressBar, options, fragmentSelectors, status, dialogObj) {
		fluid.utils.debug("demoUpload Handler");
        var demoState = {};
		
		// used to break the demo upload into byte-sized chunks
		demoState.byteChunk = 200000; 
		
		// set up data
		demoState.row = $(fragmentSelectors.fileQueue + ' tbody tr:not(".fluid-uploader-placeholder"):not(".uploaded):not(".error)', uploaderContainer).eq(0);
		
		demoState.fileId = jQuery(demoState.row).attr('id');
		demoState.file = swfObj.getFile(demoState.fileId);
        
        fluid.utils.debug("num of ready files = " + numFilesToUpload(uploaderContainer, fragmentSelectors.fileQueue)); // check the current state 
        
		if (status.stop === true) { // we're pausing
			demoPause(swfObj, demoState.file, uploaderContainer, progressBar, fragmentSelectors, status, options, dialogObj, 0);
		} else if (numFilesToUpload(uploaderContainer, fragmentSelectors.fileQueue)) { // there are still files to upload
			status.stop = false;
			demoState.bytes = 0;
			demoState.totalBytes = demoState.file.size;
			demoState.numChunks = Math.ceil(demoState.totalBytes / demoState.byteChunk);
			fluid.utils.debug ('DEMO :: ' + demoState.fileId + ' :: totalBytes = ' 
                + demoState.totalBytes + ' numChunks = ' + demoState.numChunks);
			
			// start the demo upload
			uploadStart(demoState.file, uploaderContainer, fragmentSelectors, progressBar, status);
			
			// perform demo progress
			demoProgress(demoState, swfObj, uploaderContainer, progressBar, fragmentSelectors, status, options, dialogObj);
		} else { // no more files to upload close the display
			fileQueueComplete(uploaderContainer, swfObj, options, progressBar, fragmentSelectors, dialogObj, status);
		}

        function demoProgress(demoState, swfObj, uploaderContainer, progressBar, fragmentSelectors, status, options, dialogObj) {
			var timer;
			var delay = Math.floor(Math.random() * 1000 + 100);
   			if (status.stop === true) { // user paused the upload
   				// throw the pause error
 				demoPause(swfObj, demoState.file, uploaderContainer, progressBar, fragmentSelectors, status, options, dialogObj, delay);
    		} else {
				status.stop = false;
    			var tmpBytes = (demoState.bytes + demoState.byteChunk);
				
    			if (tmpBytes < demoState.totalBytes) { // we're still in the progress loop
    				fluid.utils.debug ('tmpBytes = ' + tmpBytes + ' totalBytes = ' + demoState.totalBytes);
    				uploadProgress(progressBar, uploaderContainer, demoState.file, tmpBytes, demoState.totalBytes, fragmentSelectors, status);
    				demoState.bytes = tmpBytes;
    				timer = setTimeout(function(){
						demoProgress(demoState, swfObj, uploaderContainer, progressBar, fragmentSelectors, status, options, dialogObj);
					}, delay);			
    			}
    			else { // progress is complete
    				// one last progress update just for nice
					uploadSuccess(uploaderContainer, demoState.file, progressBar, fragmentSelectors, status);
    				// change Stats here
    				timer = setTimeout(function(){
						uploadComplete(swfObj, demoState.file, uploaderContainer, progressBar, fragmentSelectors, status, options, dialogObj);
    				}, delay);
					// remove the file from the queue
					swfObj.cancelUpload(demoState.fileId);
				}
    		}  
			status.stop = false;
    	}
        
		function demoPause (swfObj, file, uploaderContainer, progressBar, fragmentSelectors, status, options, dialogObj, delay) {
			uploadError(file, -290, "", uploaderContainer, progressBar, fragmentSelectors, options.queueListMaxHeight, status, options);
    		uploadComplete(swfObj, file, uploaderContainer, progressBar, fragmentSelectors, status, options, dialogObj);
			status.stop = false;
		}
        
     };    

    function initSWFUpload(uploaderContainer, uploadURL, flashURL, progressBar, status, fragmentSelectors, options, allowMultipleFiles, dialogObj) {
		// Initialize the uploader SWF component
		// Check to see if SWFUpload is available
		if (typeof(SWFUpload) === "undefined") {
			return null;
		}
        
		var swf_settings = {
			// File Upload Settings
			upload_url: uploadURL,
			flash_url: flashURL,
            post_params: options.postParams,
			
			file_size_limit: options.fileSizeLimit,
			file_types: options.fileTypes,
			file_types_description: options.fileTypesDescription,
			file_upload_limit: options.fileUploadLimit,
			file_queue_limit: options.fileQueueLimit,
						
			// Event Handler Settings
			swfupload_loaded_handler : createSWFReadyHandler(options.browseOnInit, allowMultipleFiles, options.dialogDisplay),
			file_dialog_start_handler: createFileDialogStartHandler (uploaderContainer),
			file_queued_handler: createFileQueuedHandler (uploaderContainer, fragmentSelectors, options.queueListMaxHeight, status),
			file_queue_error_handler: fileQueueError,
			file_dialog_complete_handler: createFileDialogCompleteHandler (uploaderContainer, fragmentSelectors, status),
			upload_start_handler: createUploadStartHandler (uploaderContainer, fragmentSelectors, progressBar, status),
			upload_progress_handler: createUploadProgressHandler (progressBar, uploaderContainer, fragmentSelectors, status),
			upload_error_handler: createUploadErrorHandler (uploaderContainer, progressBar, fragmentSelectors, options.queueListMaxHeight, status, options),
			upload_success_handler: createUploadSuccessHandler (uploaderContainer, progressBar, fragmentSelectors, options.whenFileUploaded, status),
			upload_complete_handler: createUploadCompleteHandler (uploaderContainer, progressBar, fragmentSelectors, status, options, dialogObj),
			// debug_handler : debug_function, // a new event handler in swfUpload that we don't really know what to do with yet
			// Debug setting
			debug: options.debug
		}; 
		
        return new SWFUpload(swf_settings);
    }
    
    var whichOS = function () {
		if (navigator.appVersion.indexOf("Win") !== -1) {
            return "Windows";
        }
		if (navigator.appVersion.indexOf("Mac") !== -1) {
            return "MacOS";
        }
		if (navigator.appVersion.indexOf("X11") !== -1) {
            return "UNIX";
        }
		if (navigator.appVersion.indexOf("Linux") !== -1) {
            return "Linux";
        }
        else {
            return "unknown";
        }
	};
    
    var setKeyboardModifierString = function (uploaderContainer, modifierKeySelector) {
        // set the text difference for the instructions based on Mac or Windows
		if (whichOS() === 'MacOS') {
			$(modifierKeySelector, uploaderContainer).text(strings.macControlKey);
		}
    };
    
    var bindEvents = function (uploader, uploaderContainer, swfObj, allowMultipleFiles, whenDone, whenCancel) {

		// browse button
        var activateBrowse = function () {
            return (allowMultipleFiles) ? swfObj.selectFiles() : swfObj.selectFile();
		};
        
		$(uploader.fragmentSelectors.browse, uploaderContainer).click(activateBrowse).activatable(activateBrowse);
        
		// upload button
		$(uploader.fragmentSelectors.upload, uploaderContainer).click(function(){
			if ($(uploader.fragmentSelectors.upload, uploaderContainer).css('cursor') === 'pointer') {
				uploader.actions.beginUpload();
			}
		});
		
		// resume button
		$(uploader.fragmentSelectors.resume, uploaderContainer).click(function(){
			if ($(uploader.fragmentSelectors.resume, uploaderContainer).css('cursor') === 'pointer') {
				uploader.actions.beginUpload();
			}
		});
		
		// pause button
		$(uploader.fragmentSelectors.pause, uploaderContainer).click(function(){
			swfObj.stopUpload();
		});
		
		// done button
		$(uploader.fragmentSelectors.done, uploaderContainer).click(function(){
			variableAction(whenDone);
		});
		
		// cancel button
		$(uploader.fragmentSelectors.cancel, uploaderContainer).click(function(){
			variableAction(whenCancel);
		});
    };
    
    var enableDemoMode = function (uploaderContainer, swfObj, progressBar, options, fragmentSelectors, status, dialogObj) {
		// this is a local override to do a fake upload
		swfObj.startUpload = function(){
			demoUpload(uploaderContainer, swfObj, progressBar, options, fragmentSelectors, status, dialogObj);
		};
		swfObj.stopUpload = function(){
			status.stop = true;
		};
    };
    
	/* Public API */
	fluid.Uploader = function(uploaderContainerId, uploadURL, flashURL, settings){
        
        this.uploaderContainer = fluid.utils.jById(uploaderContainerId);
		
		// Mix user's settings in with our defaults.
        // temporarily public; to be made private after beta
		this.options = $.extend({}, uploadDefaults, settings);
        
        this.fragmentSelectors = this.options.fragmentSelectors;
        
        // Should the status object be more self-aware? Should various functions that operate on
        // it (and do little else) be encapsulated in it?
        this.status = {
    		totalBytes:0,
	    	currBytes:0,
		    currError:'',
			continueOnError: false,
		    stop: false
	    };
		
		var progressOptions = {
			progress: this.uploaderContainer,
			fileProgressor: this.fragmentSelectors.fileProgressor,
			fileText: this.fragmentSelectors.fileProgressText,
			totalProgressor: this.fragmentSelectors.totalProgressor,
			totalText: this.fragmentSelectors.totalProgressText,
			totalProgressContainer: this.fragmentSelectors.uploaderFooter
		};
		
		var progressBar = new fluid.Progress(progressOptions);
    				
		var allowMultipleFiles = (this.options.fileQueueLimit !== 1);

 		// displaying Uploader in a dialog
		if (this.options.dialogDisplay) {
			var dialogObj = initDialog(this.uploaderContainer, this.options.addFilesBtn, this.options.browseOnInit, this.fragmentSelectors.browse);
		}

        var swfObj = initSWFUpload(this.uploaderContainer, uploadURL, flashURL, progressBar, this.status, this.fragmentSelectors, this.options, allowMultipleFiles, dialogObj);
		
        this.actions = new fluid.SWFWrapper(swfObj);
        
        setKeyboardModifierString(this.uploaderContainer, this.fragmentSelectors.osModifierKey);
        
        // Bind all our event handlers.
        bindEvents(this, this.uploaderContainer, swfObj, allowMultipleFiles, this.options.whenDone, this.options.whenCancel);
		
        // If we've been given an empty URL, kick into demo mode.
        if (uploadURL === '') {
            enableDemoMode(this.uploaderContainer, swfObj, progressBar, this.options, this.fragmentSelectors, this.status, dialogObj);
        }
	};
	
    // temporary debuggin' code to be removed after beta
    // USE: call from the console to check the current state of the options and fragmentSelectors objects
    
	fluid.Uploader.prototype._test = function() {
		var str = "";
		for (key in options) {
            if (options.hasOwnProperty(key)) {
                str += key + ' = ' + options[key] + '\n';
            }
		}
		for (key in this.fragmentSelectors) {
           if (this.fragmentSelectors.hasOwnProperty(key)) {
               str += key + ' = ' + this.fragmentSelectors[key] + '\n';
           }
		}
		fluid.utils.debug (str);
	};
	
    fluid.SWFWrapper = function (swfObject) {
        this.swfObj = swfObject;
    };
	
    fluid.SWFWrapper.prototype.beginUpload = function() {
		this.swfObj.startUpload();
	};
    
})(jQuery,fluid);

/* PROGRESS
 *  
 */

(function ($) {
		 
	function animateToWidth(elm,width) {
		elm.animate({ 
			width: width,
			queue: false
		}, 200 );
	}
	
	var hideNow = function(which){
        $(which).fadeOut('slow');
    };      
    
	 /* Constructor */
	fluid.Progress = function (options) {
		this.minWidth = 5;
        this.progressContainer = options.progress;
  		this.fileProgressElm = $(options.fileProgressor, this.progressContainer);
		this.fileTextElm = $(options.fileText, this.progressContainer);
		this.totalProgressElm = $(options.totalProgressor, this.progressContainer);
		this.totalTextElm = $(options.totalText, this.progressContainer);
		this.totalProgressContainer = $(options.totalProgressContainer, this.progressContainer);
		
		this.totalProgressElm.width(this.minWidth);
		
		this.fileProgressElm.hide();
		this.totalProgressElm.hide();
	};
	
	fluid.Progress.prototype.init = function(fileRowSelector){
		
		this.currRowElm = $(fileRowSelector,this.progressContainer);
		
		// hide file progress in case it is showing
		this.fileProgressElm.width(this.minWidth);
		
		// set up the file row
		this.fileProgressElm.css('top',(this.currRowElm.position().top)).height(this.currRowElm.height()).width(this.minWidth);
		// here to make up for an IE6 bug
		if ($.browser.msie && $.browser.version < 7) {
			this.totalProgressElm.height(this.totalProgressElm.siblings().height());
		}	
		
		// show both
		this.totalProgressElm.show();
		this.fileProgressElm.show();
	};
	
	fluid.Progress.prototype.updateProgress = function(which, percent, text, dontAnimate) {
		if (which === 'file') {
			setProgress(percent, text, this.fileProgressElm, this.currRowElm, this.fileTextElm, dontAnimate);
		} else {
			setProgress(percent, text, this.totalProgressElm, this.totalProgressContainer, this.totalTextElm, dontAnimate);
		}
	};

    var setProgress = function(percent, text, progressElm, containerElm, textElm, dontAnimate) {
			
		var containerWidth = containerElm.width();	
		var currWidth = progressElm.width();
		var newWidth = ((percent * containerWidth)/100);
		
		// de-queue any left over animations
		progressElm.queue("fx", []); 
		
		textElm.html(text);
		
		if (percent === 0) {
			progressElm.width(this.minWidth);
		} else if (newWidth < currWidth || dontAnimate) {
			progressElm.width(newWidth);
		} else {
			animateToWidth(progressElm,newWidth);
		}
	};
        
    fluid.Progress.prototype.hide = function(dontPause) {
		var delay = 1600;
		if (dontPause) {
			hideNow(this.fileProgressElm);
			hideNow(this.totalProgressElm);
		} else {
			var timeOut = setTimeout(function(){
                hideNow(this.fileProgressElm);
				hideNow(this.totalProgressElm);
            }, delay);
		}
	};
	
    fluid.Progress.prototype.show = function() {
		this.progressContainer.fadeIn('slow');
	};
	
})(jQuery);




//fluid.Progress.update('.fluid-progress','.file-progress',40,"Label Change");


/* GRAVEYARD and SCRATCH
	
	// eventually used to create fileTypes sets.
	var fileTypes = {
		all: {
			ext: "*.*",
			desc: 'all files'
		},
		images: {
			ext: "*.gif;*.jpeg;*.jpg;*.png;*.tiff",
			desc: "image files"
		},
		text:"*.txt;*.text",
		Word:"*.doc;*.xdoc",
		Excel:"*.xls",
	}

	// for use in a better way of setting state to simplify structure
	states: "start uploading browse loaded reloaded paused empty done",

*/
