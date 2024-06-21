// events.js

function initEventHandlers() {
    $('.toggle-button').click(function() {
        $('.nav-links').toggleClass('active');
    });
   
$('#commentForm').on('submit', function(event) {
    event.preventDefault();
    let formData = $(this).serialize();
    $.ajax({
        url: '/comments',
        method: 'POST',
        data: formData,
        success: function(response) {
            $('#commentsList').prepend(renderComment(response.comment));
            $('#commentText').val('');
        },
        error: function(response) {
            alert('Failed to post comment. Please try again.');
        }
    });
});
// Handle download selected button click
$('#downloadSelectedButton').on('click', function() {
    let selectedVideos = getSelectedVideos();

    if (selectedVideos.length > 0) {
        showProgress('Downloading selected...', true);
        processVideoDownloads(selectedVideos);
    } else {
        showProgress('No videos selected');
    }
});
function getSelectedVideos() {
    let selectedVideos = [];
    $('.video-checkbox:checked').each(function() {
        let index = $(this).data('video-index');
        selectedVideos.push(videoDetails[index].id);
    });
    return selectedVideos;
}

    // Handle form submission to fetch playlist
    $('#downloadForm').on('submit', function(event) {
        event.preventDefault();
        var formData = $(this).serialize();
        toggleInputSection(false);
        showProgress('Fetching playlist...', true);

        fetchPlaylist(formData, function(response) {
            videoDetails = response.video_details;
            showProgress('Playlist fetched. Ready to download.', false);
            displayVideoDetails(videoDetails);
   
        }, function(response) {
            showProgress(response.responseJSON.error || 'Download Failed', false);
            toggleInputSection(true);
            hideProgress();
        });
    });

    // Handle individual video download button click
    $('#video-list').on('click', '.download-button', function() {
        let index = $(this).data('video-index');
        let videoId = videoDetails[index].id;
        updateButtonStatus(index, true);
        downloadVideo(videoId, function(response) {
            var link = document.createElement('a');
            link.href = response.file_url;
            link.download = response.file_url.split('/').pop();
            link.click();
            updateButtonStatus(index, false, true);
        }, function(response) {
            showProgress(response.responseJSON.error || 'Download Failed', false);
            toggleInputSection(true);
            hideProgress();
        });
    });

    
    // Handle convert next button click
    $('#convertNextButton').on('click', function() {
        location.reload();
    });
}
