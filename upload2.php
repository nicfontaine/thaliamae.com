<?php
//I removed the zip entry as you don't have any code to handle them here.
$valid_formats = array("jpg", "png", "gif");
//Edit: compress_image doesn't handle bmp files either, though it would
//easy enough to add with another elseif.
$max_file_size = 1500000; //300 kb
$path = "./img/upload/"; // Upload directory
$count = 0;

// Compress the image files
function compress_image($source_url, $destination_url, $quality) {
    $info = getimagesize($source_url);

    if ($info['mime'] == 'image/jpeg') $image = imagecreatefromjpeg($source_url);
    elseif ($info['mime'] == 'image/gif') $image = imagecreatefromgif($source_url);
    elseif ($info['mime'] == 'image/png') $image = imagecreatefrompng($source_url);

    // save file
    imagejpeg($image, $destination_url, $quality);

    // return destination file
    return $destination_url;
}

if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST"){
        // Loop $_FILES to execute all files
    foreach ($_FILES['file']['name'] as $f => $name) {     
        echo 'foreach';
        if ($_FILES['file']['error'][$f] != 0) {
            echo 'error found';
            continue; // Skip file if any error found
        }
        if ($_FILES['file']['error'][$f] == 0) {
            echo 'no error';
            if ($_FILES['file']['size'][$f] > $max_file_size ) {
                $message[] = "$name is too large!";
                echo 'too large';
                continue; // Skip large files.
            }
            elseif( ! in_array(pathinfo($name, PATHINFO_EXTENSION), $valid_formats) ){
                $message[] = "$name is not a valid format";
                echo 'not valid format';
                continue; // Skip invalid file formats
            }
            else{ // No error found! Move uploaded files 
                //All smaller files to be compressed.
                if(is_uploaded_file($_FILES["file"]["tmp_name"][$f])) {
                    //Add a '.jpg' to the name because I'm lazy.
                    compress_image($_FILES["file"]["tmp_name"][$f], $path.basename($name).'.jpg', 90);
                    echo 'compress';
                    $count ++; // Number of successfully uploaded files
                    // REDIRECT
                    header("HTTP/1.1 303 See Other");
                    header("Location: https://$_SERVER[HTTP_HOST]/");
                }
            }
        }
    }
}