// api.js

var format = 'mp3'; // Default format, update based on user selection
var quality = 'high'; // Default quality, update based on user selection

function fetchPlaylist(formData, successCallback, errorCallback) {
    $.ajax({
        url: '/download',
        method: 'POST',
        data: formData,
        success: successCallback,
        error: errorCallback
    });
}

function downloadVideo(videoId, successCallback, errorCallback) {
    $.ajax({
        url: '/downloadVideo',
        method: 'POST',
        data: {
            video_id: videoId,
            format: format, // Use the global format variable
            quality: quality // Use the global quality variable
        },
        success: function(response) {
            console.log('downloadVideo response:', response); // Log the response
            successCallback(response);
        },
        error: errorCallback
    });
}

function processVideoDownloads(videoIds) {
    if (videoIds.length > 0) {
        let videoId = videoIds.shift();
        let index = videoDetails.findIndex(video => video.id === videoId);

        updateButtonStatus(index, true);

        downloadVideo(videoId, function(response) {
            console.log('processVideoDownloads response:', response); // Log the response
            var link = document.createElement('a');
            link.href = response.file_url;
            link.download = response.file_url.split('/').pop();
            link.click();
            updateButtonStatus(index, false, true);
            processVideoDownloads(videoIds);
        }, function(response) {
            showProgress(response.responseJSON.error || 'Download Failed', false);
            toggleInputSection(true);
            hideProgress();
        });
    } else {
        showProgress('All downloads completed', false);
        toggleButtons(false);
    }

}
