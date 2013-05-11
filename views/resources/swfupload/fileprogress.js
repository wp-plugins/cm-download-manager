/*
	A simple class for displaying file information and progress
	Note: This is a demonstration only and not part of SWFUpload.
	Note: Some have had problems adapting this class in IE7. It may not be suitable for your application.
*/

// Constructor
// file is a SWFUpload file object
// targetID is the HTML element id attribute that the FileProgress HTML structure will be added to.
// Instantiating a new FileProgress object with an existing file will reuse/update the existing DOM elements
var submitValue = '';
function FileProgress(file, targetID) {
    this.fileProgressID = file.id;

    this.opacity = 100;
    this.height = 0;


    this.fileProgressWrapper = document.getElementById(this.fileProgressID);
    if (!this.fileProgressWrapper) {
        this.fileProgressWrapper = document.createElement("div");
        this.fileProgressWrapper.className = "progressWrapper";
        this.fileProgressWrapper.id = this.fileProgressID;

        this.fileProgressElement = document.createElement("div");
        this.fileProgressElement.className = "progressContainer";

        var progressCancel = document.createElement("a");
        progressCancel.className = "progressCancel";
        progressCancel.href = "#";
        progressCancel.style.visibility = 'hidden';
        progressCancel.appendChild(document.createTextNode("x"));

        var progressText = document.createElement("div");
        progressText.className = "progressName";
        progressText.appendChild(document.createTextNode(file.name));

        var progressImg = document.createElement("div");
        progressImg.className = "progressImg";
        progressImg.appendChild(progressCancel);
        var progressBar = document.createElement("div");
        progressBar.className = "progressBarInProgress";

        var progressStatus = document.createElement("div");
        progressStatus.className = "progressBarStatus";
        progressStatus.innerHTML = "&nbsp;";
//        this.fileProgressElement.appendChild(progressCancel);
        this.fileProgressElement.appendChild(progressText);
        this.fileProgressElement.appendChild(progressImg);
        this.fileProgressElement.appendChild(progressStatus);
        this.fileProgressElement.appendChild(progressBar);

        this.fileProgressWrapper.appendChild(this.fileProgressElement);

        document.getElementById(targetID).appendChild(this.fileProgressWrapper);
    } else {
        this.fileProgressElement = this.fileProgressWrapper.firstChild;
        this.reset();
    }

    this.height = this.fileProgressWrapper.offsetHeight;
    this.setTimer(null);


}
FileProgress.prototype.setTimer = function (timer) {
    this.fileProgressElement["FP_TIMER"] = timer;
};
FileProgress.prototype.getTimer = function (timer) {
    return this.fileProgressElement["FP_TIMER"] || null;
};

FileProgress.prototype.reset = function () {
    this.fileProgressElement.className = "progressContainer";
    jQuery(this.fileProgressElement).find('.progressBarStatus').removeClass('progressBarError progressBarComplete');
    jQuery(this.fileProgressElement).find('.progressBarInProgress').css('width', 0);
	
    //this.appear();	
};

FileProgress.prototype.setProgress = function (percentage) {
    jQuery(this.fileProgressElement).find('.progressBarInProgress').css('width', percentage+'%');

    this.appear();	
};
FileProgress.prototype.setComplete = function (deleteCallback) {
    jQuery(this.fileProgressElement).find('.progressBarStatus').addClass('progressBarComplete');
    jQuery(this.fileProgressElement).find('.progressBarInProgress').css('width', 0);
    jQuery(this.fileProgressElement).find('.progressCancel').css('visibility', 'visible').click(deleteCallback);
    jQuery(this.fileProgressElement).find('.progressName').hide();
    jQuery(this.fileProgressElement).find('.progressImg').show();
    var oSelf = this;
    this.setTimer(setTimeout(function () {
        oSelf.disappear();
    }, 5000));
};
FileProgress.prototype.setError = function () {
    jQuery(this.fileProgressElement).find('.progressBarStatus').addClass('progressBarError');
    jQuery(this.fileProgressElement).find('.progressBarInProgress').css('width', 0);

    var oSelf = this;
    this.setTimer(setTimeout(function () {
        oSelf.disappearAll();
    }, 5000));
};
FileProgress.prototype.setCancelled = function () {
    jQuery(this.fileProgressElement).find('.progressBarStatus').addClass("progressBarError");
    jQuery(this.fileProgressElement).find('.progressBarInProgress').css('width', 0);

    var oSelf = this;
    this.setTimer(setTimeout(function () {
        oSelf.disappear();
    }, 2000));
};
FileProgress.prototype.setStatus = function (status) {
    jQuery(this.fileProgressElement).find('.progressBarStatus').text(status);
};
FileProgress.prototype.setName = function (name, path) {
    jQuery(this.fileProgressElement).find('.progressCancel').data('name', name);
    var thumb = document.createElement("img");
    thumb.height="60";
    thumb.src = path+name;
    jQuery(this.fileProgressElement).find('.progressImg').append(thumb);
};

// Show/Hide the cancel button
FileProgress.prototype.toggleCancel = function (show, swfUploadInstance) {
    if (swfUploadInstance) {
        var fileID = this.fileProgressID;
    //		this.fileProgressElement.childNodes[0].onclick = function () {
    //			swfUploadInstance.cancelUpload(fileID);
    //			return false;
    //		};
    }
};

FileProgress.prototype.appear = function () {
    jQuery(this.fileProgressWrapper).show();
};
FileProgress.prototype.disappearAll = function () {
    var wrapper = this.fileProgressWrapper;
    jQuery(wrapper).fadeOut('slow');
}
// Fades out and clips away the FileProgress box.
FileProgress.prototype.disappear = function () {

	
		
    jQuery(this.fileProgressElement).find('.progressBarStatus, .progressBarInProgress').fadeOut('slow');


};
FileProgress.prototype.disableSubmit = function() {
    var input = jQuery(this.fileProgressWrapper).parents('form').find('input[type=submit]');
    if (submitValue=="") submitValue = input.val();
    input.attr('disabled', 'disabled').val('Uploading...');
}
FileProgress.prototype.enableSubmit = function() {
    jQuery(this.fileProgressWrapper).parents('form').find('input[type=submit]').removeAttr('disabled').val(submitValue);
}