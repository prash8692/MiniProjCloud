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
    'DBInstanceIdentifier' => 'itmo544-krose1-mysqldb-readonly',
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


$link = mysqli_connect($url,"controller","controllerpass","school","3306") or die("Error " . mysqli_error($link));
$emailid=$_SESSION['emailid'];

$sqlselect = "SELECT s3_raw_url,s3_finished_url FROM records where status=1 AND email='$emailid'";
$resultforselect = $link->query($sqlselect);


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
#lightbox {
    position:fixed; /* keeps the lightbox window in the current viewport */
    top:0; 
    left:0; 
    width:100%; 
    height:100%; 
    background:url(overlay.png) repeat; 
    text-align:center;
}
#lightbox p {
    text-align:right; 
    color:#fff; 
    margin-right:20px; 
    font-size:12px; 
}
#lightbox img {
    box-shadow:0 0 25px #111;
    -webkit-box-shadow:0 0 25px #111;
    -moz-box-shadow:0 0 25px #111;
    max-width:940px;
}
</style>
</head>
<body>

<ul>
  <li><a href="/welcome.php">Home</a></li>
  <li><a class="active" href="/gallery.php">Gallery</a></li>
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
<br>
<br>
<br>
<?php
if ($resultforselect->num_rows > 0) {
    // output data of each row
    while($row = $resultforselect->fetch_assoc()) {
		$value=$row["s3_raw_url"];
        echo "<a href='$value' class=\"lightbox_trigger\">";

        echo "<img src='$value' height=\"200\" width=\"200\" style=\"margin:0px 20px\" />";

        $valuefinish=$row["s3_finished_url"];
        echo "<a href='$valuefinish' class=\"lightbox_trigger\">";

        echo "<img src='$valuefinish' height=\"200\" width=\"200\"/>";
        echo"<br>";
        echo"<hr>";
    }
} else {
    echo "0 results";
}
$link->close();
?>
</div>
<script src="https://code.jquery.com/jquery-1.6.2.min.js"></script>
<script>
jQuery(document).ready(function($) {
    
    $('.lightbox_trigger').click(function(e) {
        
        //prevent default action (hyperlink)
        e.preventDefault();
        
        //Get clicked link href
        var image_href = $(this).attr("href");
        
        /*  
        If the lightbox window HTML already exists in document, 
        change the img src to to match the href of whatever link was clicked
        
        If the lightbox window HTML doesn't exists, create it and insert it.
        (This will only happen the first time around)
        */
        
        if ($('#lightbox').length > 0) { // #lightbox exists
            
            //place href as img src value
            $('#content').html('<img src="' + image_href + '" height=\"400\" width=\"400\" />');
            
            //show lightbox window - you could use .show('fast') for a transition
            $('#lightbox').show();
        }
        
        else { //#lightbox does not exist - create and insert (runs 1st time only)
            
            //create HTML markup for lightbox window
            var lightbox = 
            '<div id="lightbox">' +
                '<p>Click to close</p>' +
                '<div id="content">' + //insert clicked link's href into img src
                    '<img src="' + image_href +'" height=\"400\" width=\"400\" />' +
                '</div>' +  
            '</div>';
                
            //insert lightbox HTML into page
            $('body').append(lightbox);
        }
        
    });
    
    //Click anywhere on the page to get rid of lightbox window
    $('#lightbox').live('click', function() { //must use live, as the lightbox element is inserted into the DOM
        $('#lightbox').hide();
    });

});
</script>
</body>
</html>

