<?php
  session_start();
  if(!isset($_SESSION['username'])){
    echo "You haven't login, <a href='../index.html'>please login</a>";
  }else{
    $user_path='../upload/'.$_SESSION['username'];
    if(!file_exists($user_path)){
      echo "You don't have available files.</br>";
      echo "<a href='./upload.html'>Go to upload page</a>";
      exit();
    }
    $dir=opendir($user_path);
    while(($filename=readdir($dir)) !== false){
      if($filename!="." && $filename!="..") {
        echo "<a href='../api/download?filename=$filename'>$filename</a><br>";
      }
    }
    closedir($dir);
    echo "<a href='./homepage.html'>Return to home page</a>";
  }
?>
