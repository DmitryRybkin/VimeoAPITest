<?php

use Vimeo\Vimeo;

/**
 * Get file
 */
$file = $_FILES['video_file_path'];
$fileName = $file['tmp_name'];

/**
 * Init Vimeo PHP SDK with the user credentials
 */
$vimeoClient = new Vimeo($clientId, $clientSecret, $accessToken);

/**
 * Init request data
 */
$data = [
    'name' => 'Vimeo API SDK test upload',
    'description' => "This video was uploaded through the Vimeo API's PHP SDK."
];
$uri = '/me/videos?fields=uri,upload';
$fileSize = filesize($fileName);
$data['upload']['approach'] = 'tus';
$data['upload']['size'] = $fileSize;

/**
 * Make request and get the upload.link value for checking upload process
 * This step works fine and I get response with all correct data
 */
$attempt = $vimeoClient->request($uri, $data, 'POST', true, []);
$uploadLink = $attempt['body']['upload']['upload_link'];
var_dump($uploadLink);

/**
 * Response:
 *
 * $uploadLink - 'https://europe-files.tus.vimeo.com/files/vimeo-prod-src-tus-eu/98218864e893d8efc60151b6bd05ed89'
 *
 * $attempt -
 *
 * 'body' =>
 *
 * 'uri' => '/videos/623456268',
 * 'upload' =>
 *
 * 'status' => 'in_progress',
 * 'upload_link' => 'https://europe-files.tus.vimeo.com/files/vimeo-prod-src-tus-eu/56abc18bb09ea9abeda4d8e72274f0e8',
 * 'form' => NULL,
 * 'complete_uri' => NULL,
 * 'approach' => 'tus',
 * 'size' => 6391193,
 * 'redirect_url' => NULL,
 * 'link' => NULL,
 *
 * 'status' => 200,
 * 'headers' =>
 *
 * 'Connection' => 'keep-alive',
 * 'Content-Length' => '269',
 * 'Server' => 'nginx',
 * 'Content-Type' => 'application/vnd.vimeo.video+json',
 * 'Cache-Control' => 'private, no-store, no-cache',
 * 'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains; preload',
 * 'X-RateLimit-Limit' => '500',
 * 'X-RateLimit-Remaining' => '499',
 * 'X-RateLimit-Reset' => '2021-10-05T13:47:34+00:00',
 * 'Request-Hash' => '517d563b',
 * 'X-BApp-Server' => 'api-v15686-jqrsh',
 * 'X-Vimeo-DC' => 'ge',
 * 'Accept-Ranges' => 'bytes',
 * 'Via' => '1.1 varnish, 1.1 varnish',
 * 'Date' => 'Tue, 05 Oct 2021 13:46:36 GMT',
 * 'X-Served-By' => 'cache-bwi5128-BWI, cache-vie6378-VIE',
 * 'X-Cache' => 'MISS, MISS',
 * 'X-Cache-Hits' => '0, 0',
 * 'X-Timer' => 'S1633441595.897439,VS0,VE1962',
 * 'Vary' => 'Accept,Vimeo-Client-Id',
 *
 */

/**
 * Prepare data and make additional request via curl PHP for getting the Upload-Offset value
 */
$curlOpts = [
    CURLOPT_CUSTOMREQUEST => 'HEAD',
    CURLOPT_HTTPHEADER => [
        'Accept: application/vnd.vimeo.*+json;version=3.4',
        'Tus-Resumable: 1.0.0',
    ]
];

$curl = curl_init($uploadLink);
curl_setopt_array($curl, $curlOpts);
$response = curl_exec($curl);
$curlInfo = curl_getinfo($curl);

/**
 * $response - false (Actually it takes about 2 minutes to get the response)
 *
 * $curlInfo -
 *
 * 'url' => 'https://europe-files.tus.vimeo.com/files/vimeo-prod-src-tus-eu/98218864e893d8efc60151b6bd05ed89',
 * 'content_type' => NULL,
 * 'http_code' => 200,
 * 'header_size' => 570,
 * 'request_size' => 185,
 * 'filetime' => -1,
 * 'ssl_verify_result' => 0,
 * 'redirect_count' => 0,
 * 'total_time' => 360.44831,
 * 'namelookup_time' => 0.124819,
 * 'connect_time' => 0.184231,
 * 'pretransfer_time' => 0.222609,
 * 'size_upload' => 0.0,
 * 'size_download' => 0.0,
 * 'speed_download' => 0.0,
 * 'speed_upload' => 0.0,
 * 'download_content_length' => -1.0,
 * 'upload_content_length' => -1.0,
 * 'starttransfer_time' => 0.617318,
 * 'redirect_time' => 0.0,
 * 'redirect_url' => '',
 * 'primary_ip' => '107.178.240.106',
 *
 * 'certinfo' =>
 *
 * 'primary_port' => 443,
 * 'local_ip' => '192.168.31.176',
 * 'local_port' => 36932,
 * 'http_version' => 2,
 * 'protocol' => 2,
 * 'ssl_verifyresult' => 0,
 * 'scheme' => 'HTTPS',
 *
 */

/**
 * Hope it will be helpful
 */
