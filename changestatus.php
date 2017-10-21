<?php

require 'vendor/autoload.php';

use Aws\Rds\RdsClient;


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


$link = mysqli_connect($url,"controller","controllerpass","school","3306") or die("Error " . mysqli_error($link));

$newValue=$_POST['flagstatus'];


if($newValue == "on"){

     $sqlupdate = "update credentials set status='on'";
        $resultforupdate= $link->query($sqlupdate);
        $link->close();

  } else {

     $sqlupdate = "update credentials set status='off'";
        $resultforupdate= $link->query($sqlupdate);
        $link->close();

  }

  header( "Location: admin.php" );

 ?>
