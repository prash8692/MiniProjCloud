<?php

require 'vendor/autoload.php';

$s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'us-west-2'
]);

$result = $s3->listBuckets();

foreach ($result['Buckets'] as $bucket) {
    echo $bucket['Name'] . "\n";
}

// Convert the result object to a PHP array
$array = $result->toArray();

$bucket='raw-kro';
$filepath='/var/www/html/switchonarex.png';

$resultdelete = $s3->deleteObject(array(
    'Bucket' => $bucket,
    'Key'    => 'switchonrex.png'
));


$resultput = $s3->putObject(array(
    'Bucket' => $bucket,
    'Key' => 'switchonarex.png',
    'SourceFile' => $filepath,
    'region' => 'us-west-2',
    'ACL'    => 'public-read',
    'Body'   => 'Hello!'
));

// Access parts of the result object
// echo $resultput['Expiration'] . "\n";
// echo $resultput['ServerSideEncryption'] . "\n";
// echo $resultput['ETag'] . "\n";
// echo $resultput['VersionId'] . "\n";
// echo $resultput['RequestId'] . "\n";

$imageurl=$resultput['ObjectURL'];
// Get the URL the object can be downloaded from
// echo $resultput['ObjectURL'] . "\n";

?>

<html>
<body>
<br>
<h1><?php echo $imageurl; ?><h1>
<br>
<img src="<?php echo $imageurl; ?>" height="500" width="600">
<br>
<br>
<br>
</body>
</html>

