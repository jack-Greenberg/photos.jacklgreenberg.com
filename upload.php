<!--
Responsible for uploading images. The system includes password authentication
using bcrypt.
-->

<?php
require(dirname(__FILE__).'/includes/functions.php');
ini_set('file_uploads', 1); // allow file uploads

$directory = array_diff(scandir("images/", SCANDIR_SORT_DESCENDING), array('..', '.'));

$last_file_number = (intval(explode(".", $directory[0], 2)[0]));

if ( isset($_POST["submit"]) ) {
    /*
    This script checks whether or not to allow file uploads. First, we check that an image was selected and that it isn't fake, then we check the password against the stored hash, then we check that the file isn't too big, then we check that it is an acceptable file type, and if that all works, and the upload is successful, we redirect to the home page, or else echo the error message.
    */

    $directory = array_diff(scandir("images/"), array('..', '.'));

    $image_type = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION)); // returns 'jpg' or 'png' or whatever
    $new_name = ($last_file_number + 1) . '.' . $image_type; // returns '1.jpg' or '2.png' or whatever

    if ($_FILES["image"]["size"] == 0 || !(getimagesize($_FILES["image"]["tmp_name"]))) { // if no file is uploaded or it is a fake file
        $success = 0;
        $err_message = "Please choose an image (png, jpg, jpeg, or gif).";
    } elseif (!(password_verify($_POST["secret"], substr(file_get_contents(dirname(__FILE__)."/private/hash"), 0, -2)))) { // if the password is wrong
        $success = 0;
        $err_message = "Wrong password.";
    } elseif ($image_type !== "jpg" && $image_type !== "png" && $image_type !== "jpeg" && $image_type !== "gif") { // if the file isn't the right type
        $success = 0;
        $err_message = "Wrong file type.";
    } else { // if it passes all those tests
        $success = 1;
    };

    if ($success == 1 && move_uploaded_file($_FILES["image"]["tmp_name"], "images/" . $new_name)) {
        $db->insert("caption_map", [
            "id" => ($last_file_number + 1),
            "image" => $new_name,
            "caption" => $_POST["caption"],
            "date" => date('F j, Y')
        ]);
        header('Location: https://blog.jacklgreenberg.com');
    } else {
       echo $err_message;
    };
};
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Uploads</title>
    <link rel="stylesheet" href="/build/bundle.css">
</head>
<body>
    <h1 class="upload-title">Post a photo</h1>

    <form action="" method="post" autocomplete="off" enctype="multipart/form-data">
        <input type="file" name="image" id="image"><br>
        <input type="text" name="caption" id="caption" placeholder="Caption"><br>
        <input type="password" name="secret" id="secret" placeholder="Secret"><br>
        <input type="submit" name="submit" value="Submit">
    </form>
</body>
</html>
