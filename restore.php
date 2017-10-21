<?php
session_start();
require 'vendor/autoload.php';
use Aws\Rds\RdsClient;
include 'checkuploadenabled.php';
$variable=returnenabledstatus();
$client = RdsClient::factory(array(
'version' => 'latest',
'region'  => 'us-west-2'
));
$result = $client->describeDBInstances(array(
    'DBInstanceIdentifier' => 'itmo544-krose1-mysqldb',
));
$endpoint = "";
$url="";
foreach ($result['DBInstances'] as $ep)
{
    foreach($ep['Endpoint'] as $endpointurl)
    {
        $url=$endpointurl;
        break;
    }
}

exec('aws s3 mv s3://databasebackup-kro/db-backup.sql /home/ubuntu/');
exec('aws s3 cp /home/ubuntu/db-backup.sql s3://databasebackup-kro/ --grants read=uri=http://acs.amazonaws.com/groups/global/AllUsers');

$conn = new mysqli($url, 'controller', 'controllerpass', 'school');
$filename = '/home/ubuntu/db-backup.sql';
$op_data = '';
$lines = file($filename);
foreach ($lines as $line)
{
    if (substr($line, 0, 2) == '--' || $line == '')//This IF Remove Comment Inside SQL FILE
    {
        continue;
    }
    $op_data .= $line;
    if (substr(trim($line), -1, 1) == ';')//Breack Line Upto ';' NEW QUERY
    {
        $conn->query($op_data);
        $op_data = '';
    }
}
//echo "Restore succesfull " . $database;

?>

<html>
<head>
<meta charset=utf-8 />
<title>ADMIN</title>
<!-- <link rel="stylesheet" href="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.css" />
<script src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
<script src="http://code.jquery.com/mobile/1.2.0/jquery.mobile-1.2.0.min.js"></script> -->
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
<form action="admin.php" method="post">
<h1>Restore Success!</h1>
<input type="submit" value="back to admin page" />
</form>
</div>
</body>
</html>
