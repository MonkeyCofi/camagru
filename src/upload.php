<?php

function uuidv4(): string
{
  $data = random_bytes(16);

  $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
  $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
    
  return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function get_file_type(string $type): string {
    $file_types = [
        "image/jpeg" => ".jpeg",
        "image/jpg" => ".jpg",
        "image/png" => ".png"
    ];
    return $file_types[$type];
}

/**
 * Download the image to images/uploads, save the URL of the image (generate random UUID for filename), and add entry of 
 * post to the TABLE(posts)
 * schema:
 * INSERT INTO posts (Uri, UserID, PostID, PostURL)
 * @return string | array
 */
function upload(PDO $pdo) {
    $upload_dir = "public/assets/images/uploads";
    $upload_name = $upload_dir . "/" . uuidv4() . get_file_type($_FILES['upload']['type']);
    $max_file_size = 1024 * 1024 * 2;
    if ($_FILES['upload']['size'] > $max_file_size)
        return "File too large";
    $query = "INSERT INTO posts (UserID, PostURL) VALUES (?, ?)";
    try {
        $pdo->beginTransaction();
        $statement = $pdo->prepare($query);
        if (move_uploaded_file($_FILES['upload']['tmp_name'], $upload_name) == false)
            return "Failed to upload file";
        $statement->execute([$_SESSION['user_id'], $upload_name]);
        $pdo->commit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("
        <h1>
            Error
        </h1><br>
        <p>" . $e . "</p>");
    }
    return "<p>Uploaded file successfully</p>";
}

// this page should send a GET request for a javascript file
function upload_page() {
    // if there is no user session
    if (!isset($_SESSION['user_id']))
        return "<p>Must be logged in to upload photos</p>";
    // echo "<script src='./upload.js'></script>";
    return "
    <form method='POST' action='/upload' enctype='multipart/form-data'>
        <input type='file' name='upload' accept='image/jpeg image/jpg image/png'>
        <button type='submit'>Upload</button>
    </form>
    
    <video id='video' autoplay></video>
    <button id='captureButton'>Take Photo</button>
    <canvas id='upload-canvas' style='display:none;'></canvas>
    <img hidden id='photo' alt='Captured photo will appear here'>
    <form method='POST' enctype='multipart/form-data'>
        <input type='hidden' id='upload' accept='image/jpeg image/png image/jpg'>
        <button id='upload-capture-button' type='submit' hidden>Upload</button>
    </form>
    <script src='./upload.js'></script>
    ";
}