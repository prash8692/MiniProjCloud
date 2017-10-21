<?php

echo "<h1 style=\"color:blue;\" align=\"center\">Data Base Test Page</h1>";

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
    echo $ep['DBInstanceIdentifier'] . "<br>";

    foreach($ep['Endpoint'] as $endpointurl)
	{
        echo "<h4>The url used to connect to the database</h4>";
        echo $endpointurl . "<br>";
	echo "<br>";
        $url=$endpointurl;
		break;
	}
}


$link = mysqli_connect($url,"controller","controllerpass","school","3306") or die("Error " . mysqli_error($link));

// echo "Here is the result: " . $link;

$drop_table = 'DROP TABLE IF EXISTS students';
$drop_tbl = $link->query($drop_table);
if ($drop_tbl) {
        echo "Table student has been deleted" . "<br>";
}
else {
        echo "error!!" . "<br>";

}


$create_table = 'CREATE TABLE IF NOT EXISTS students
(
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    age int(3) NOT NULL,
    PRIMARY KEY(id)
)';

$create_tbl = $link->query($create_table);
if ($create_tbl) {
        echo "Table is created or No error returned." . "<br>";
}
else {
        echo "error!!" . "<br>";
}

$sql1 = "INSERT INTO students (name,age) values ('Student-1',23)";

$sql2 = "INSERT INTO students (name,age) values ('Student-2',24)";

$sql3 = "INSERT INTO students (name,age) values ('Student-3',36)";

$sql4 = "INSERT INTO students (name,age) values ('Student-4',23)";

$sql5 = "INSERT INTO students (name,age) values ('Student-5',31)";


$sql_execute1 = $link->query($sql1);
$sql_execute2 = $link->query($sql2);
$sql_execute3 = $link->query($sql3);
$sql_execute4 = $link->query($sql4);
$sql_execute5 = $link->query($sql5);

if ($sql_execute1 && $sql_execute2 && $sql_execute3 && $sql_execute4 && $sql_execute5 ) {
    echo "New record created successfully" . "<br>";
} else {
    echo "Error: " . $sql . "<br>" . $link->error;
}

$sqlselect = "SELECT * FROM students";
$resultforselect = $link->query($sqlselect);

echo "<h3> Result of the select query <h3>";
echo "<hr>";

echo "<table border=\"1\">";
     echo "<tr>";
         echo "<td>ID</td>";
         echo "<td>Name</td>";
         echo "<td>Age</td>";
         echo "</tr>";


if ($resultforselect->num_rows > 0) {
    // output data of each row
    while($row = $resultforselect->fetch_assoc()) {

	echo "<tr>";
                echo "<td>" . $row["id"]. "</td>";
                echo "<td>" . $row["name"]. "</td>";
                echo "<td>" . $row["age"]. "</td>";
                echo "</tr>";
//        echo "id: " . $row["id"]. " - Name: " . $row["name"]. " " . $row["age"]. "<br>";
//		echo "-------------------------------" . "<br>";
    }
} else {
    echo "0 results";
}

echo "</table>";

$link->close();


?>



