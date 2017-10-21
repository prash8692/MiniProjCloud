<?php
    // Include the SDK using the Composer autoloader
     require 'vendor/autoload.php';
     // use Aws\S3\S3Client;
     // use Aws\S3\Exception\S3Exception;
         $s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'us-west-2'
]);

include('image_validation.php'); // getExtension Method
$message='';
if($_SERVER['REQUEST_METHOD'] == "POST")
{
echo $name;
$name = $_FILES['file']['name'];
$size = $_FILES['file']['size'];
$tmp = $_FILES['file']['tmp_name'];
$ext = getExtension($name);

if(strlen($name) > 0)
{
// File format validation
        if(in_array($ext,$valid_formats))
        {
// File size validation
        if($size<(1024*1024))
        {
        //include('config_s3.php');
//Rename image name.
        $image_name_actual = time().".".$ext;

        try {
echo $ext;
echo $image_name_actual;
$resultput = $s3->putObject(array(
             'Bucket'=>'raw-kros',
             'Key' =>  $image_name_actual,
             'SourceFile' => $tmp,
             'region' => 'us-west-2',
              'ACL'    => 'public-read'
        ));
        $message = "S3 Upload Successful.";
        $imageurl=$resultput['ObjectURL'];
        $s3file='http://'.$bucket.'.s3.amazonaws.com/'.$actual_image_name;
        echo "<img src='$imageurl' height=\'500\' width=\'600\'/>";
        echo 'S3 File URL:'.$s3file;

    } catch (S3Exception $e) {
         // Catch an S3 specific exception.
        echo $e->getMessage();
    }
	//else
// $message = "Image size Max 1 MB";

}
//else
//$message = "Invalid file, please upload image file.";

}
//else
 //$message = "Please select image file.";

}}

?>

<html>
<head>
<title>Upload Image</title>
</head>
<body>
<form action="" method='post' enctype="multipart/form-data">
Upload image file here
<input type='file' name='file'/> <input type='submit' value='Upload Image'/>
<?php echo $msg; ?>
</form>
</body>
</html>
