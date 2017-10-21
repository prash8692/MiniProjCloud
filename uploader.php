<?php 
session_start();

require 'vendor/autoload.php';

use Aws\Sqs\SqsClient;

use Aws\Rds\RdsClient;

include 'checkuploadenabled.php';

$variable=returnenabledstatus();

$sqsclient = SqsClient::factory(array(
       'version' => 'latest',
      'region'  => 'us-west-2'
));

$client = RdsClient::factory(array(
'version' => 'latest',
'region'  => 'us-west-2'
));

$s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'us-west-2'
]);

$result = $client->describeDBInstances(array(
    'DBInstanceIdentifier' => 'itmo544-krose1-mysqldb',
));
$endpoint = "";
$url="";
foreach ($result['DBInstances'] as $ep)
{
   // echo $ep['DBInstanceIdentifier'] . "<br>";
    foreach($ep['Endpoint'] as $endpointurl)
        {

        $url=$endpointurl;
        break;
        }
}
$conn = mysqli_connect($url,"controller","controllerpass","school","3306") or die("Error " . mysqli_error($link));

$name=$_FILES["fileToUpload"]["name"];

$tmp=$_FILES['fileToUpload']['tmp_name'];

$resultput = $s3->putObject(array(
             'Bucket'=>'raw-kro',
             'Key' =>  $name,
             'SourceFile' => $tmp,
             'region' => 'us-west-2',
              'ACL'    => 'public-read'
        ));
        
$imageurl=$resultput['ObjectURL'];

$_SESSION['s3-raw']=$imageurl;
        

if (!($stmt2 = $conn->prepare("INSERT INTO records (id,email,phone,s3_raw_url,s3_finished_url,status,receipt) VALUES (NULL,?, ?, ?, ?, ?, ?)"))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}

$stmt = $conn->prepare("INSERT INTO records (email,phone,s3_raw_url,s3_finished_url,status,receipt) VALUES (?, ?, ?, ?, ?, ?)");
$statusnumber=0;
$stmt->bind_param("ssssss", $email, $phone, $s3_raw_url,$s3_finished_url,$status,$receipt);
$email=$_SESSION['emailid'];
$phone="6036744303";
$s3_raw_url=$imageurl;
$s3_finished_url="YET_TO_UPLOAD";
$status=$statusnumber;
$receipt=md5($imageurl);
$stmt->execute();
$stmt->close();
$conn->close();

$_SESSION['receipt']=$receipt;
  
$queueUrl = $sqsclient->listQueues(array(
    // QueueName is required just using the prefix to find out the queue URL
'QueueNamePrefix' => 'kro',
));

$sqsclient->sendMessage(array(
    'QueueUrl'    => $queueUrl['QueueUrls'][0],
    'MessageBody' => $_SESSION['receipt'],
));

?>

<html>
<head>
<title>Uploaded Image</title>
<style>
body {
    margin: 0;
}
ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
    width: 25%;
    background-color: #f1f1f1;
    position: fixed;
    height: 100%;
    overflow: auto;
}
li a {
    display: block;
    color: #000;
    padding: 8px 16px;
    text-decoration: none;
    border-bottom: 1px solid #555;
}
li a.active {
    background-color: #4CAF50;
    color: white;
}
li a:hover:not(.active) {
    background-color: #555;
    color: white;
}
.button {
    background-color: #4CAF50;
    border: none;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
}
.buttonreturn{
    background-color: #4CAF50;
    color: white;
    padding: 14px 25px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
}

</style>
</head>
<body>

<ul>
  <li><a href="/welcome.php">Home</a></li>
  <li><a href="/gallery.php">Gallery</a></li>
<?php
if($variable == 'on'){
  echo"<li><a href=\"/upload.php\">Upload</a></li>";
}
if($_SESSION['username']=="controller"){
echo "<li><a href=\"/admin.php\">Admin</a></li>";
}
?>
<li><a href="/logout.php">Log Out</a></li>
</ul>

<div style="margin-left:25%;padding:1px 16px;height:1000px;">
<h4 style="float:right" >welcome: <?php echo $_SESSION['username']; ?></h4>
<form action="" method='post' enctype="multipart/form-data">
<h1>Success!</h1>
<br>
<br>
<h3>Name of the image: <?php echo $name; ?><h3>
<img src="<?php echo $imageurl; ?>" height="200" width="200">
<br>
<br>
<a class="buttonreturn"href="/upload.php">Go To Upload</a>
</form>
</div>
</body>
</html>
