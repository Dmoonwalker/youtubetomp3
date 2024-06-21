<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Video Vibe</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" />
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
</head>
<body>
    <header class="app-header">
        <div class="container-fluid">
            <div class="header-links text-center">
                <a href="#about">About Us</a>
                <a href="#faq">FAQ</a>
                <a href="#tandc">T&C</a>
            </div>
            <div class="logo-title-section text-center mt-4">
                <img src="{{ asset('assets/img/icon.png') }}" alt="Logo" class="logo">
                <h1 class="app-title mt-2">Video Vibe</h1>
            </div>
        </div>
    </header>

    <div class="container main-container">
        <div class="card emphasized-card mt-5 mx-auto">
            <div class="card-header text-center">
                <h3>Download Your favorite Playlist to MP3 or MP4</h3>
            </div>
            <div class="card-body">
                <form id="downloadForm">
                    @csrf
                    <div id="inputSection">
                        <div class="form-group">
                            <label for="playlist_url">Enter YouTube Playlist URL:</label>
                            <input type="url" class="form-control" id="playlist_url" name="playlist_url" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="format">Format:</label>
                                <select class="form-control" id="format" name="format">
                                    <option value="mp3">MP3</option>
                                    <option value="mp4">MP4</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="quality">Quality:</label>
                                <select class="form-control" id="quality" name="quality">
                                    <option value="high">High</option>
                                    <option value="medium">Medium</option>
                                    <option value="low">Low</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Fetch Playlist</button>
                    </div>
                </form>
                <div class="progress mt-3" style="display:none; background-color: white;">
                    <div class="progress-bar progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    <div id="progressText" class="animated-text mt-2"></div>
                </div>
                <button id="cancelButton" class="btn btn-danger btn-block mt-3" style="display:none;">Cancel</button>
                <button id="convertNextButton" class="btn btn-secondary btn-block mt-3" style="display:none;">Convert Next</button>
                <div class="btn-container mt-3">
                    <button id="downloadSelectedButton" class="btn btn-primary" style="display:none;">Download Selected</button>
                    <button id="downloadZipButton" class="btn btn-primary" style="display:none;">Download All as ZIP</button>
                </div>
            </div>
        </div>
        <div id="video-list" class="mt-4">
            <table class="table table-bordered table-striped">
                <thead style="display: none;" id="table-headers">
                    <tr>
                        <th scope="col">Select</th>
                        <th scope="col">Thumbnail</th>
                        <th scope="col">Title</th>
                        <th scope="col">Duration</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody id="video-table-body">
                </tbody>
            </table>
        </div>
        <div class="comment-section mt-4">
            <h4>Comments</h4>
            <div class="login-buttons">
                <a href="{{ url('auth/google') }}" class="btn btn-danger mb-2">Login</a>
                <a href="{{ url('auth/facebook') }}" class="btn btn-primary mb-2">Sign Up</a>
            </div>
            <div id="commentsList" class="mt-4">
                <div class="card comment-card">
                    <div class="card-body">
                        <div class="comment-header">
                            <img src="{{ asset('assets/img/profile.png') }}" alt="Avatar" class="comment-avatar">
                            <div class="comment-author">
                                <h5 class="comment-author-name">John Doe</h5>
                                <p class="comment-date">1 month ago</p>
                            </div>
                        </div>
                        <p class="comment-text">Impressive! Though it seems the drag feature could be improved. But overall it looks incredible. You've nailed the design and the responsiveness at various breakpoints works really well.</p>
                    </div>
                </div>
                <!-- More comments will be dynamically loaded here -->
            </div>
            <form id="commentForm" class="mt-4">
                @csrf
                <div class="form-group">
                    <textarea class="form-control" id="commentText" name="comment" rows="3" placeholder="Write a comment..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Post Comment</button>
            </form>
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
    <script src="{{ asset('assets/js/api.js') }}"></script>
    <script src="{{ asset('assets/js/ui.js') }}"></script>
    <script src="{{ asset('assets/js/events.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
</body>
</html>
