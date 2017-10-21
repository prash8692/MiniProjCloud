
<?php

require 'vendor/autoload.php';
use Aws\Sqs\SqsClient;
use Aws\Sns\SnsClient;
use Aws\Rds\RdsClient;
// make sure you have php-gd installed and you may need to reload the webserver (apache2)
//This should be done in install app

// get SQS queue name
$sqsclient = SqsClient::factory(array(
       'version' => 'latest',
      'region'  => 'us-west-2'
));

$rdsclient = RdsClient::factory(array(
'version' => 'latest',
'region'  => 'us-west-2'
));

$s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'us-west-2'
]);

$snsclient = new Aws\Sns\SnsClient([
    'version' => 'latest',
    'region'  => 'us-west-2'
]);


$queueUrl = $sqsclient->listQueues(array(
 'QueueNamePrefix' => 'kro',
));

$rdsresult = $rdsclient->describeDBInstances(array(
    'DBInstanceIdentifier' => 'itmo544-krose1-mysqldb-readonly',
));

$sqsresult = $sqsclient->receiveMessage(array(
    // QueueUrl is required
    'QueueUrl' => $queueUrl['QueueUrls'][0],
    'VisibilityTimeout' => 300,//Hiding the message for 300 seconds
  'MaxNumberOfMessages' => 1,

));

//echo $sqsresult;


// if(!empty($sqsresult)){

//echo "Inside if loop" . "\n";

//Extrating the body from the result obtained and then passing it to the variable
$messagebodyfromsqs=$sqsresult['Messages'][0]['Body'];

$receipttodelete=$sqsresult['Messages'][0]['ReceiptHandle'];

$arnsns = $snsclient->createTopic(array(
    // Name is required
    'Name' => 'krose-topic',
));

if (!empty($messagebodyfromsqs)){
echo "Inside If loop" . "\n";
$dburl="";

foreach ($rdsresult['DBInstances'] as $ep)
{
   // echo $ep['DBInstanceIdentifier'] . "<br>";

    foreach($ep['Endpoint'] as $endpointurl)
        {
        $dburl=$endpointurl;
                break;
        }
}

//retriving the url for the db instance and then establishing connection using mysqli
//$dburl=$rdsresult['DBIntances'][0]['Endpoint']['Address'];

$conn = mysqli_connect($dburl,"controller","controllerpass","school","3306") or die("Error " . mysqli_error($link));

$sqlselect = "SELECT s3_raw_url,s3_finished_url FROM records where receipt='$messagebodyfromsqs'";
$resultforselect = $conn->query($sqlselect);

$rawurl="";

while($row = $resultforselect->fetch_assoc()){

                $rawurl=$row["s3_raw_url"];
                echo $rawurl . "\n";

}
$conn->close();

// load the "stamp" and photo to apply the water mark to
$stamp = imagecreatefrompng('https://s3-us-west-2.amazonaws.com/raw-kro/IIT-logo.png');
$im = '';  // replace this path with $rawurl

$checkimgformat=substr($rawurl, -3);

if($checkimgformat == 'png' || $checkimgformat == 'PNG'){

$im=imagecreatefrompng($rawurl);}
else{
$im = imagecreatefromjpeg($rawurl);
}

$lstoccuranceofslash=strripos($rawurl,"/") + 1;

echo "finding the last position of the slash symbol:     " . $lstoccuranceofslash . "\n";

$imagename=substr($rawurl,$lstoccuranceofslash, strlen($rawurl));

echo $imagename . "\n";

$marge_right=10;
$marge_bottom=10;
$sx = imagesx($stamp);
$sy = imagesy($stamp);
echo $sy . "\n";
imagecopy($im,$stamp,imagesx($im) - $sx -$marge_right, imagesy($im) - $sy -$marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));

$tmp="/tmp/$imagename";
echo "the tmp directory" . $tmp . "\n";
//output and free memory
//header('Content-type: image/png');
imagepng($im,$tmp);
imagedestroy($im);
echo shell_exec('ls -ltr /tmp') . "\n";

// $tmp="/tmp/$imagename";

$resultput = $s3->putObject(array(
             'Bucket'=>'finish-kro',
             'Key' =>  $imagename,
             'SourceFile' => $tmp,
             'region' => 'us-west-2',
              'ACL'    => 'public-read'
));

$finishedimageurl=$resultput['ObjectURL'];

echo "Finished URL: " . $finishedimageurl ."\n";
// retreive the Object URL
$rdsresultupdate = $rdsclient->describeDBInstances(array(
    'DBInstanceIdentifier' => 'itmo544-krose1-mysqldb',
));

$urlupdate="";

foreach ($rdsresultupdate['DBInstances'] as $ep)
{
   // echo $ep['DBInstanceIdentifier'] . "<br>";

    foreach($ep['Endpoint'] as $endpointurl)
        {
        $urlupdate=$endpointurl;
                break;
        }
}

$connupdate = mysqli_connect($urlupdate,"controller","controllerpass","school","3306") or die("Error " . mysqli_error($link));
$sqlselect = "UPDATE records SET s3_finished_url='$finishedimageurl',status=1 WHERE receipt='$messagebodyfromsqs'";
$resultforselect = $connupdate->query($sqlselect);
$connupdate->close();

$sendmessage = $snsclient->publish(array(
    'TopicArn' => $arnsns['TopicArn'],

    // Message is required
    'Message' => 'Image Processed Please check your gallery Page',
    'Subject' => 'Check -01',

));


$resultfordelete = $sqsclient->deleteMessage(array(
    // QueueUrl is required
    'QueueUrl' => $queueUrl['QueueUrls'][0],
    // ReceiptHandle is required
    'ReceiptHandle' => $receipttodelete,
));



if($resultfordelete)
{
echo "Message Deleted" ."\n";

}

}
else
{
$sendmessage = $snsclient->publish(array(
    'TopicArn' => $arnsns['TopicArn'],

    // Message is required
    'Message' => 'Quee is empty',
    'Subject' => 'Check -01',

));


echo "the Quee is busy or empty" . "\n";
}
?>


