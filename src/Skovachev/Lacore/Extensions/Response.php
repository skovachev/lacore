<?php namespace Skovachev\Lacore\Extensions;

use File;

class Response extends \Illuminate\Support\Facades\Response
{
    /**
     * Create a response that will force a image to be displayed inline.
     *
     * @param string $path Path to the image
     * @param string $name Filename
     * @param int $lifetime Lifetime in browsers cache
     * @return Response
     */
    public static function inline($path, $name = null, $lifetime = 0)
    {
        if (is_null($name)) {
            $name = basename($path);
        }
 
        $filetime = filemtime($path);
        $etag = md5($filetime . $path);
        $time = gmdate('r', $filetime);
        $expires = gmdate('r', $filetime + $lifetime);
        $length = filesize($path);
 
        $headers = array(
            'Content-Disposition' => 'inline; filename="' . $name . '"',
            'Last-Modified' => $time,
            'Cache-Control' => 'must-revalidate',
            'Expires' => $expires,
            'Pragma' => 'public',
            'Etag' => $etag,
        );
 
        $headerTest1 = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $_SERVER['HTTP_IF_MODIFIED_SINCE'] == $time;
        $headerTest2 = isset($_SERVER['HTTP_IF_NONE_MATCH']) && str_replace('"', '', stripslashes($_SERVER['HTTP_IF_NONE_MATCH'])) == $etag;
        if ($headerTest1 || $headerTest2) { //image is cached by the browser, we dont need to send it again
            return static::make('', 304, $headers);
        }
 
        $mime_type = static::getFileMimeType($path);
        $headers = array_merge($headers, array(
            // 'Content-Type' => File::mime(File::extension($path)),
            'Content-Type' => $mime_type,
            'Content-Length' => $length,
                ));
 
        return static::make(File::get($path), 200, $headers);
 
    }

    private static function getFileMimeType($filepath)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $type = finfo_file($finfo, $filepath);
        finfo_close($finfo);
        return $type;
    }
}