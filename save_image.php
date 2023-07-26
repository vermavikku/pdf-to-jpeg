<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $imageData = $_POST["imageData"];
  $filename = $_POST["filename"];

  $folderPath = "upload/";
  if(!file_exists( $folderPath)){
    mkdir( $folderPath , 0777, true);
    $filePath = $folderPath . $filename;

    $decodedImageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData));
  
    if (file_put_contents($filePath, $decodedImageData)) {
      echo "Image data saved successfully!";
    } else {
      http_response_code(500);
      echo "Error saving image data.";
    }
  }else{
    $filePath = $folderPath . $filename;

    $decodedImageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData));
  
    if (file_put_contents($filePath, $decodedImageData)) {
      echo "Image data saved successfully!";
    } else {
      http_response_code(500);
      echo "Error saving image data.";
    }
  }

}
?>
