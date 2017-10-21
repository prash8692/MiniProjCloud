<?php
session_start();
include 'checkuploadenabled.php';

$variable=returnenabledstatus();

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
<form action="uploader.php" method="post" enctype="multipart/form-data">
    <h3>Select image to upload:</h3>
    <br>
    <br>
    <input type="file" name="fileToUpload" id="fileToUpload">
    <br>
    <br>
    <input class="button" type="submit" value="Upload Image" name="submit">
</form>
</div>
</body>
</html>
