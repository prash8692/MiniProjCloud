<?php


require 'vendor/autoload.php';

use Aws\Rds\RdsClient;

function returnenabledstatus() {

$client = RdsClient::factory(array(
'version' => 'latest',
'region'  => 'us-west-2'
));


$result = $client->describeDBInstances(array(
    'DBInstanceIdentifier' => 'itmo544-krose1-mysqldb-readonly',
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


$link = mysqli_connect($url,"controller","controllerpass","school","3306") or die("Error " . mysqli_error($link));

$sqlselect = "SELECT status FROM credentials where userName='controller@iit.edu'";
$resultforselect = $link->query($sqlselect);
$value='';

if ($resultforselect->num_rows > 0) {
    while($row = $resultforselect->fetch_assoc()) {
        $value=$row["status"];
    }
} else {
    echo "0 results";
}

$link->close();


   return $value;
}

?>

