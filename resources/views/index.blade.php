<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>YouTube Playlist Downloader</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header text-center">
                <h3>YouTube Playlist to MP3</h3>
            </div>
            <div class="card-body">
                <form id="downloadForm">
                    @csrf
                    <div id="inputSection">
                        <div class="form-group">
                            <label for="playlist_url">Enter YouTube Playlist URL:</label>
                            <input type="url" class="form-control" id="playlist_url" name="playlist_url" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Fetch Playlist</button>
                    </div>
                </form>
                <div class="progress mt-3" style="display:none;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div id="progressText" class="mt-2"></div>
                <button id="cancelButton" class="btn btn-danger btn-block mt-3" style="display:none;">Cancel</button>
                <button id="convertNextButton" class="btn btn-secondary btn-block mt-3" style="display:none;">Convert Next</button>
            </div>
        </div>
        <div id="video-list" class="mt-4"></div>
        <div class="btn-container mt-3">
            <button id="selectAllButton" class="btn btn-primary mr-2" style="display:none;">Select All</button>
            <button id="downloadSelectedButton" class="btn btn-primary" style="display:none;">Download Selected</button>
        </div>
        <div class="info-section">
            <h4>How to Download</h4>
            <p>1. Copy the URL of the YouTube playlist you want to download.</p>
            <p>2. Paste the URL into the input box above.</p>
            <p>3. Click on the "Fetch Playlist" button to fetch the video details.</p>
            <p>4. Select the videos you want to download and click on the "Download Selected" button to start the conversion process.</p>
        </div>
        <div class="info-section">
            <h4>About Us</h4>
            <p>Welcome to our YouTube Playlist to MP3 downloader. Our tool allows you to easily convert and download YouTube playlists to MP3 format. We are dedicated to providing a simple and efficient service to help you enjoy your favorite music offline.</p>
            <p>If you have any questions or feedback, please feel free to contact us. We are always here to help and improve our service.</p>
        </div>
    </div>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
</body>
</html>
