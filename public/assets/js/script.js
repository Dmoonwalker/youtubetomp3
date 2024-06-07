$(document).ready(function() {
    let videoDetails = [];

    // Set CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#downloadForm').on('submit', function(event) {
        event.preventDefault();
        var formData = $(this).serialize();
        $('#inputSection').hide();
        $('.progress').show();
        $('#cancelButton').show();
        $('#progressText').text('Fetching playlist...');

        $.ajax({
            url: '/download',
            method: 'POST',
            data: formData,
            success: function(response) {
                videoDetails = response.video_details;
                $('#progressText').text('Playlist fetched. Ready to download.');
                displayVideoDetails();
            },
            error: function(response) {
                $('#progressText').text(response.responseJSON.error || 'Download Failed');
                $('#inputSection').show();
                $('.progress').hide();
                $('#cancelButton').hide();
            }
        });
    });

    function displayVideoDetails() {
        $('#video-list').empty();
        $('#video-list').append(`
            <div class="video-item">
                <input type="checkbox" id="select-all-checkbox">
                <label for="select-all-checkbox">Select All</label>
            </div>
        `);

        videoDetails.forEach(function(video, index) {
            $('#video-list').append(`
                <div class="video-item" id="video-${index}">
                    <input type="checkbox" class="video-checkbox" data-video-index="${index}">
                    <img src="${video.thumbnail}" alt="Thumbnail">
                    <h5>${video.title}</h5>
                    <p>Duration: ${video.duration}</p>
                    <button class="btn btn-primary float-right download-button" data-video-index="${index}">Download</button>
                </div>
            `);
        });

        $('#selectAllButton').show();
        $('#downloadSelectedButton').show();
        $('#cancelButton').hide();
        $('#convertNextButton').show();
    }

    $('#video-list').on('change', '#select-all-checkbox', function() {
        let isChecked = $(this).is(':checked');
        $('.video-checkbox').prop('checked', isChecked);
    });

    $('#downloadSelectedButton').on('click', function() {
        let selectedVideos = [];
        $('.video-checkbox:checked').each(function() {
            let index = $(this).data('video-index');
            selectedVideos.push(videoDetails[index].id);
        });

        if (selectedVideos.length > 0) {
            $('#progressText').text('Converting...');
            downloadVideos(selectedVideos);
        } else {
            $('#progressText').text('No videos selected');
        }
    });

    function downloadVideos(videoIds) {
        if (videoIds.length > 0) {
            let videoId = videoIds.shift();
            let index = videoDetails.findIndex(video => video.id === videoId);

            updateButtonStatus(index, true);

            $.ajax({
                url: '/downloadVideo',
                method: 'POST',
                data: {
                    video_id: videoId
                },
                success: function(response) {
                    var link = document.createElement('a');
                    link.href = response.file_url;
                    link.download = response.file_url.split('/').pop();
                    link.click();
                    updateButtonStatus(index, false, true);
                    downloadVideos(videoIds);
                },
                error: function(response) {
                    $('#progressText').text(response.responseJSON.error || 'Download Failed');
                    $('#inputSection').show();
                    $('.progress').hide();
                    $('#cancelButton').hide();
                }
            });
        } else {
            $('#progressText').text('All downloads completed');
            $('#convertNextButton').show();
            $('#selectAllButton').hide();
            $('#downloadSelectedButton').hide();
        }
    }

    function updateButtonStatus(index, isDownloading, isFinished = false) {
        let button = $(`#video-${index} .download-button`);
        if (isDownloading) {
            button.text('Converting...').addClass('btn-dark').removeClass('btn-primary').prop('disabled', true);
        } else if (isFinished) {
            button.text('Finished').addClass('btn-dark').removeClass('btn-primary').prop('disabled', true);
        }
    }

    $('#cancelButton').on('click', function() {
        $('#progressText').text('');
        $('.progress-bar').css('width', '0%').attr('aria-valuenow', 0);
        $('.progress').hide();
        $('#inputSection').show();
        $('#cancelButton').hide();
    });

    $('#convertNextButton').on('click', function() {
        $('#progressText').text('');
        $('.progress-bar').css('width', '0%').attr('aria-valuenow', 0);
        $('.progress').hide();
        $('#inputSection').show();
        $('#convertNextButton').hide();
        $('#video-list').empty();
        $('#selectAllButton').hide();
        $('#downloadSelectedButton').hide();
        videoDetails = [];
    });

    $('#video-list').on('click', '.download-button', function() {
        let index = $(this).data('video-index');
        let videoId = videoDetails[index].id;
        updateButtonStatus(index, true);

        $.ajax({
            url: '/downloadVideo',
            method: 'POST',
            data: {
                video_id: videoId
            },
            success: function(response) {
                var link = document.createElement('a');
                link.href = response.file_url;
                link.download = response.file_url.split('/').pop();
                link.click();
                updateButtonStatus(index, false, true);
            },
            error: function(response) {
                $('#progressText').text(response.responseJSON.error || 'Download Failed');
                $('#inputSection').show();
                $('.progress').hide();
                $('#cancelButton').hide();
            }
        });
    });
});
