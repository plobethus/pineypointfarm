<?php
// camera.php

// Never timeout while streaming
set_time_limit(0);
ignore_user_abort(true);

// Tell the browser it’s getting an MJPEG stream
header('Content-Type: multipart/x-mixed-replace; boundary=frame');

// Open a cURL handle to your Pi’s feed
$ch = curl_init('http://10.0.0.74:5000/video_feed');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

// As data comes in, push it straight out to the client
curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($ch, $data) {
    echo $data;
    // flush both PHP and web-server buffers
    if (ob_get_level()) { ob_flush(); }
    flush();
    return strlen($data);
});

// Start streaming
curl_exec($ch);
curl_close($ch);
