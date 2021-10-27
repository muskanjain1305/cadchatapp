<?php
require '../vendor/autoload.php';
use Aws\S3\S3Client;
include_once "config.php";
  
$bucket = 'chatappimg';
  
$s3Client = new S3Client([
    'version' => 'latest',
    'region' => 'ap-south-1',
    'credentials' => [
        'key'    => $access_key_id,
        'secret' => $secret_access_key
    ]
]);
  
try {
    $result = $s3Client->createBucket([
        'Bucket' => $bucket, // REQUIRED
    ]);
    echo "Bucket created successfully.";
} catch (Aws\S3\Exception\S3Exception $e) {
    // output error message if fails
    echo $e->getMessage();
}