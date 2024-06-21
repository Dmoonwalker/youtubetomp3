<?php
// Check if playlist URL is provided as a command line argument
if ($argc < 2) {
    echo "Usage: php fetch_playlist.php <YouTube Playlist URL>\n";
    exit(1);
}

$playlist_url = $argv[1];

// Escape the URL to prevent command injection
$safe_url = escapeshellarg($playlist_url);

// Build the command to fetch playlist details using yt-dlp
$fetch_command = sprintf('yt-dlp --flat-playlist --dump-single-json %s', $safe_url);

// Run the command and capture the output
$json_output = shell_exec($fetch_command);

// Check if the command executed successfully
if ($json_output === null) {
    echo "Failed to fetch playlist details.\n";
    exit(1);
}

// Decode the JSON output
$playlist_details = json_decode($json_output, true);

// Check if JSON decoding was successful
if ($playlist_details === null) {
    echo "Failed to decode JSON output.\n";
    exit(1);
}

// Display playlist details
echo "Playlist Title: " . $playlist_details['title'] . "\n";
echo "Total Videos: " . count($playlist_details['entries']) . "\n";
echo "\nVideo List:\n";

foreach ($playlist_details['entries'] as $index => $video) {
    echo sprintf("%d. %s (ID: %s)\n", $index + 1, $video['title'], $video['id']);
}
?>
