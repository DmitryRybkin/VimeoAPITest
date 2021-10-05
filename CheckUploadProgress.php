<?php

$vimeoClient = new Vimeo($clientId, $clientSecret, $accessToken);
$data = [
    'name' => 'Vimeo API SDK test upload',
    'description' => "This video was uploaded through the Vimeo API's PHP SDK."
];
$uri = '/me/videos?fields=uri,upload';
$attempt = $vimeoClient->request($uri, $data, 'POST', true, []);

$uploadLink = 'https://europe-files.tus.vimeo.com/files/vimeo-prod-src-tus-eu/10cdd25b42393b55bf933af502c14304';
$curl_opts = array(
    CURLOPT_CUSTOMREQUEST => 'HEAD',
    CURLOPT_HTTPHEADER => array(
        'Accept: application/vnd.vimeo.*+json;version=3.4',
        'Tus-Resumable: 1.0.0',
    )
);

$curl = curl_init($uploadLink);
curl_setopt_array($curl, $curl_opts);
$response = curl_exec($curl);
$curl_info = curl_getinfo($curl);

var_dump($response);
