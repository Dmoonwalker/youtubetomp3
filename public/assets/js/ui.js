// ui.js

function displayVideoDetails(videoDetails) {
    $('#table-headers').show();
    $('#video-table-body').show();
    videoDetails.forEach(function(video, index) {
        $('#video-table-body').append(`
            <tr id="video-${index}">
                <td>
                    <input type="checkbox" class="video-checkbox" data-video-index="${index}">
                </td>
                <td>
                    <img src="${video.thumbnail}" alt="Thumbnail" class="thumbnail-img">
                </td>
                <td>
                    <h5>${video.title}</h5>
                </td>
                <td>
                    <p>Duration</p>
                    <p>${video.duration}</p>
                </td>
                <td>
                    <button class="btn btn-primary download-button" data-video-index="${index}">Download</button>
                </td>
            </tr>
        `);
    });

    toggleButtons(true);
}

function toggleInputSection(show) {
    if (show) {
        $('#inputSection').show();
    } else {
        $('#inputSection').hide();
    }
}

function showProgress(message, animate = false) {
    $('.progress').show();
    $('#progressText').text(message);
    animateProgressText(animate);
}

function hideProgress(message = '') {
    $('.progress').hide();
    $('#progressText').text(message).removeClass('animated-text').css({
        'color': '',
        'font-weight': '',
        'font-size': ''
    });
    $('.progress-bar').css('width', '0%');
}

function updateButtonStatus(index, isDownloading, isFinished = false) {
    let button = $(`#video-${index} .download-button`);
    let checkbox = $(`#video-${index} .video-checkbox`);
    
    if (isDownloading) {
        button.text('Converting...').addClass('btn-dark').removeClass('btn-primary').prop('disabled', true);
    } else if (isFinished) {
        button.text('Finished').addClass('btn-dark').removeClass('btn-primary').prop('disabled', true);
        checkbox.prop('disabled', true); // Disable the checkbox
        $(`#video-${index}`).addClass('downloaded'); // Add a class to indicate the video is downloaded
    }
}
function toggleButtons(show) {
    if (show) {
        $('#selectAllButton').show();
        $('#downloadSelectedButton').show();
        $('#cancelButton').hide();
        $('#convertNextButton').show();
    } else {
        $('#selectAllButton').hide();
        $('#downloadSelectedButton').hide();
        $('#convertNextButton').show();
    }
}

function animateProgressText(animate) {
    if (animate) {
        $('#progressText').addClass('animated-text').css({
            'color': '#2a9d8f',
            'font-weight': 'bold',
            'font-size': '1.5rem',
            'animation': 'none'
        });
        startDotsAnimation();
    } else {
        $('#progressText').css({
            'color': '#2a9d8f',
            'font-weight': 'bold',
            'font-size': '1.5rem',
            'animation': 'none'
        });
        stopDotsAnimation();
    }
}

function startDotsAnimation() {
    let dots = 0;
    let interval = setInterval(() => {
        if ($('#progressText').hasClass('animated-text')) {
            dots = (dots + 1) % 4;
            let dotText = '.'.repeat(dots);
            $('#progressText').text($('#progressText').text().replace(/\.*$/, dotText));
        } else {
            clearInterval(interval);
        }
    }, 500);
}

function stopDotsAnimation() {
    $('#progressText').removeClass('animated-text');
    $('#progressText').text($('#progressText').text().replace(/\.*$/, ''));
}

$(document).ready(function() {
    $('head').append('<style>@keyframes blink { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }</style>');
});
