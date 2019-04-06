<!--
Responsible for uploading images. The system includes password authentication
using bcrypt.
-->

<?php
require(dirname(__FILE__).'/includes/functions.php');
ini_set('file_uploads', 1); // allow file uploads

if ( isset($_POST["submit"]) ) {
    $last_id = $db->select("posts", "id", [
        "ORDER" => [
            "id" => 'DESC'
        ],
        "LIMIT" => 1
    ]);

    if ($last_id == []) {
        $new_id = 1;
    } else {
        $new_id = $last_id[0]["id"] + 1;
    };

    $filetype = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION)); // returns 'jpg' or 'png' or whatever

    $new_name = $new_id . '.' . $filetype; // returns '1.jpg' or '2.png' or whatever

    if ($_FILES["image"]["size"] == 0 || !(getimagesize($_FILES["image"]["tmp_name"]))) { // if no file is uploaded or it is a fake file
        $success = 0;
        $err_message = "Please choose an image (png, jpg, jpeg, or gif).";
    } elseif (!(password_verify($_POST["secret"], substr(file_get_contents(dirname(__FILE__)."/private/hash"), 0, -1)))) { // if the password is wrong
        $success = 0;
        $err_message = "Wrong password.";
    } elseif ($filetype !== "jpg" && $filetype !== "png" && $filetype !== "jpeg" && $filetype !== "gif") { // if the file isn't the right type
        $success = 0;
        $err_message = "Wrong file type.";
    } else { // if it passes all those tests
        $success = 1;
    };

    if ($success == 1 && move_uploaded_file($_FILES["image"]["tmp_name"], "tmp/" . $new_name)) {
        $db->insert("posts", [
            "id" => $new_id,
            "caption" => $_POST["caption"],
            "date" => date('F j, Y'),
            "filetype" => $filetype
        ]);
        header('Location: https://photos.jacklgreenberg.com');
    } else {
       echo "<div class=\"error-container\">" . $err_message . "</div>";
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
