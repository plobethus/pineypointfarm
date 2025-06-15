<?php
// camera.php
// Proxy the MJPEG stream so browsers see it as coming from your Apache server

set_time_limit(0);
$streamUrl = 'http://10.0.0.74:5000/video_feed';

// Open remote stream
$remote = @fopen($streamUrl, 'rb');
if (!$remote) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 502 Bad Gateway', true, 502);
    echo 'Unable to connect to camera stream.';
    exit;
}

// Tell the browser we're sending an MJPEG stream
header('Content-Type: multipart/x-mixed-replace; boundary=frame');

// Relay data in real time
while (!feof($remote)) {
    $buffer = fread($remote, 4096);
    if ($buffer === false) break;
    echo $buffer;
    @flush();
}

fclose($remote);
