<?php
session_start();

require 'vendor/autoload.php';

use Aws\Rds\RdsClient;

$client = RdsClient::factory(array(
            'version' => 'latest',
            'region' => 'us-west-2'
        ));
$result = $client->describeDBInstances(array(
    'DBInstanceIdentifier' => 'itmo544-krose1-mysqldb-readonly',
        ));
$endpoint = "";
$url = "";
foreach ($result['DBInstances'] as $ep) {
    foreach ($ep['Endpoint'] as $endpointurl) {


        $url = $endpointurl;
        break;
    }
}
$link = mysqli_connect($url, "controller", "controllerpass", "school", "3306") or die("Error " . mysqli_error($link));

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // username and password sent from form 

    $myusername = $_POST['username'];
    $mypassword = $_POST['password'];
    // echo $myusername;

    $sql = "SELECT * FROM credentials WHERE userName = '$myusername' and userPass = '$mypassword'";
    $select_tbl = $link->query($sql);
    if ($select_tbl) {
$_SESSION['emailid']=$myusername;
//	$_SESSION['username']=$myusername;
$usernamefindposition=strpos($myusername,"@");
$usernameaftersubstaring=substr($myusername,0,$usernamefindposition);
$_SESSION['username']=$usernameaftersubstaring;
	header( "Location: welcome.php" );	
//        echo "success login" . "<br>";
    } else {
        echo "error!! user name or password is in correct" . "<br>";
    }
}
?>
<html>
    <head>
        <title>Cloud Computing week 12</title>
    </head>
    <body>

        <form id='login' action='' method='post' accept-charset='UTF-8'>
            <fieldset>
                <table>

                    <legend>Enter Your credentials</legend>
                    <br>
                    <br>

                    <tr>
                        <td>
                            <label for='username' >UserName </label>
                        </td>
                        <td>
                            <input type='text' name='username' id='username'  maxlength="50" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for='password' >Password </label>
                        </td>
                        <td>
                            <input type='password' name='password' id='password' maxlength="50" />
                        </td>
                    </tr>
                </table>
                <br>
                <br>
                <input type='submit' name='Submit' value='Submit' />
            </fieldset>

        </form>

    </body>

</html>


