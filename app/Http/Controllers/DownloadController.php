<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Models\Visit;
use File;
use ZipArchive;

class DownloadController extends Controller
{
    /**
     * Index method to show the main page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('index');
        Visit::create([
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'visited_at' => Carbon::now()
        ]);

    }

    /**
     * Download a YouTube video.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function download(Request $request)
    {
        set_time_limit(0);

        $request->validate([
            'playlist_url' => 'required|url',
        ]);

        $url = $request->input('playlist_url');
        $format = $request->input('format', 'mp3');
        $quality = $request->input('quality', 'high');

        if (!preg_match('/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.?be)\/.+/', $url)) {
            return response()->json(['error' => 'Please enter a valid YouTube URL.'], 400);
        }

        $session_id = Session::getId();
        $temp_dir = storage_path("app/public/temp_$session_id");

        if (!file_exists($temp_dir)) {
            mkdir($temp_dir, 0777, true);
        }

        if (strpos($url, 'list=') !== false) {
            // It's a playlist
            $fetch_command = sprintf('yt-dlp --flat-playlist --dump-single-json %s', escapeshellarg($url));
            exec($fetch_command . ' 2>&1', $output, $return_var);

            $json_lines = array_filter($output, function ($line) {
                return preg_match('/^{.*}$/', $line);
            });

            $output_string = implode("\n", $json_lines);
            Log::info('Output String', ['output_string' => $output_string]);

            $playlist_data = json_decode($output_string, true);
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
        } else {
            // It's a single video
            $fetch_command = sprintf('yt-dlp --dump-single-json %s', escapeshellarg($url));
            exec($fetch_command . ' 2>&1', $output, $return_var);

            $json_lines = array_filter($output, function ($line) {
                return preg_match('/^{.*}$/', $line);
            });
            $output_string = implode("\n", $json_lines);
            Log::info('Output String', ['output_string' => $output_string]);

            $video_data = json_decode($output_string, true);
            Log::info('Parsed Video Data', ['video_data' => $video_data]);

            if ($return_var !== 0 || empty($video_data)) {
                Log::error('Failed to fetch video details', ['output' => $output]);
                return response()->json(['error' => 'Failed to fetch video details.'], 500);
            }

            $video_details = [
                [
                    'id' => $video_data['id'],
                    'title' => $video_data['title'],
                    'duration' => isset($video_data['duration']) ? gmdate("H:i:s", $video_data['duration']) : 'Unknown',
                    'thumbnail' => $video_data['thumbnails'][0]['url'] ?? null
                ]
            ];

            return response()->json(['video_details' => $video_details, 'session_id' => $session_id]);
        }
    }

    public function downloadVideo(Request $request)
    {
        $request->validate([
            'video_id' => 'required|string',
            'format' => 'required|string',
            'quality' => 'required|string',
        
        ]);


        $video_id = $request->input('video_id');
        $format = $request->input('format');
        $quality = $request->input('quality');
        $session_id = Session::getId();
        $temp_dir = storage_path("app/public/temp_$session_id");
        $video_url = 'https://www.youtube.com/watch?v=' . $video_id;

        $output_template = $temp_dir . '/%(title)s.%(ext)s';
        $format_flag = $format === 'mp3' ? 'bestaudio' : 'bestvideo';
        $audio_quality_flag = $format === 'mp3' ? '--audio-quality ' . escapeshellarg($quality) : '';
        $extract_audio_flag = $format === 'mp3' ? '--extract-audio --audio-format mp3' : '';
        $download_command = sprintf(
            'yt-dlp -f %s %s %s --output "%s" %s',
            escapeshellarg($format_flag),
            $audio_quality_flag,
            $extract_audio_flag,
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

    /**
     * Run a shell command.
     *
     * @param string $command
     * @throws ProcessFailedException
     */
  
    /**
     * Recursively delete a directory and its contents.
     *
     * @param string $dir
     * @return bool
     */
    
}
