<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class DownloadController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function download(Request $request)
    {
        // Increase the maximum execution time to handle long-running processes
        set_time_limit(0);  // 0 means no limit

        $request->validate([
            'playlist_url' => 'required|url',
        ]);

        $playlist_url = $request->input('playlist_url');

        // Validate that the URL is a YouTube URL
        if (!preg_match('/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.?be)\/.+/', $playlist_url)) {
            return response()->json(['error' => 'Please enter a valid YouTube URL.'], 400);
        }

        // Use the session ID as the unique identifier
        $session_id = Session::getId();
        $temp_dir = storage_path("app/public/temp_$session_id");

        if (!file_exists($temp_dir)) {
            mkdir($temp_dir, 0777, true);
        }

        // Fetch video details
        $fetch_command = sprintf(
            'yt-dlp --flat-playlist --dump-single-json %s',
            escapeshellarg($playlist_url)
        );
        exec($fetch_command . ' 2>&1', $output, $return_var);

        $json_lines = array_filter($output, function ($line) {
            return preg_match('/^{.*}$/', $line);
        });

        // Combine the JSON lines into a single string
        $output_string = implode("\n", $json_lines);
        Log::info('Output String', ['output_string' => $output_string]);

        $playlist_data = json_decode($output_string, true);

        // Log the parsed JSON for debugging
        Log::info('Parsed Playlist Data', ['playlist_data' => $playlist_data]);

        if ($return_var !== 0 || empty($playlist_data['entries'])) {
            Log::error('Failed to fetch video details', ['output' => $output]);
            return response()->json(['error' => 'Failed to fetch video details.'], 500);
        }

        $video_details = array_map(function ($entry) {
            return [
                'id' => $entry['id'],
                'title' => $entry['title'],
                'duration' => isset($entry['duration']) ? gmdate("H:i:s", $entry['duration']) : 'Unknown',
                'thumbnail' => $entry['thumbnails'][0]['url'] ?? null
            ];
        }, $playlist_data['entries']);

        return response()->json(['video_details' => $video_details, 'session_id' => $session_id]);
    }

    public function downloadVideo(Request $request)
    {
        $request->validate([
            'video_id' => 'required|string',
        ]);

        $video_id = $request->input('video_id');
        // Infer the temp_dir based on the session ID
        $session_id = Session::getId();
        $temp_dir = storage_path("app/public/temp_$session_id");
        $video_url = 'https://www.youtube.com/watch?v=' . $video_id;

        $output_template = $temp_dir . '/%(title)s.%(ext)s';
        $download_command = sprintf(
            'yt-dlp -f bestaudio --extract-audio --audio-format mp3 --audio-quality 0 -o "%s" %s',
            $output_template,
            escapeshellarg($video_url)
        );

        // Execute the download command
        $process = proc_open($download_command, [
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w']
        ], $pipes);

        if (is_resource($process)) {
            while (!feof($pipes[1])) {
                $output = fgets($pipes[1], 1024);
                Log::info($output);
            }
            fclose($pipes[1]);
            fclose($pipes[2]);

            $return_var = proc_close($process);

            if ($return_var !== 0) {
                return response()->json(['error' => 'Download failed for video: ' . $video_id], 500);
            }

            // Find the downloaded file
            $files = glob("$temp_dir/*.mp3");
            if (count($files) > 0) {
                $file_path = $files[0];
                $file_name = basename($file_path);
                return response()->json(['file_url' => route('download.file', ['file_name' => $file_name])]);
            }
        }

        return response()->json(['error' => 'Download failed.'], 500);
    }

    public function getFile($file_name)
    {
        $session_id = Session::getId();
        $file_path = storage_path("app/public/temp_$session_id/$file_name");

        if (file_exists($file_path)) {
            return response()->download($file_path)->deleteFileAfterSend(true);
        }

        return abort(404);
    }
}
    