<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
require '../vendor/autoload.php';
require './env.php';
session_start();
$app = new Slim\App();

//login: http://localhost:8081/api/login
$app->post('/login', 'login');
//upload file: http://localhost:8081/api/upload
$app->post('/upload', 'uploadFile');
//download file: http://localhost:8081/api/download?filename=test1.txt
$app->get('/download', 'downloadFile');
//add notes: http://localhost:8081/api/addnotes
$app->post('/addnotes','addNotes');
//get all notes: http://localhost:8081/api/getnotes
$app->get('/getnotes','getNotes');
//get notes by title: http://localhost:8081/api/getnotesbytitle?title=note1
$app->get('/getnotesbytitle','getNotesByTitle');
$app->run();

function getConnection(){
  $dbhost=getenv("MYSQL_HOST");
  $dbuser=getenv("MYSQL_USER");
  $dbpass=getenv("MYSQL_PASSWORD");
  $dbname=getenv("MYSQL_DATABASE");
  $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  return $dbh;
}

function login(Request $request,Response $response){
  $username=$_POST['username'];
  $password=$_POST['password'];
  $sql="select * from user where username = '$username' and password='$password'";

  try {
    $db = getConnection();
    $stmt = $db->query($sql);
    $user = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    if(!empty($user)){
      $_SESSION['username']=$username;
      echo "login successfully</br>";
      echo "<a href='../template/homepage.html'>Go to homepage</a>"; //this is for testing
    }else{
      echo "Unauthorized</br>";
      echo "<a href='../index.html'>Return to login page</a>"; //this is for testing
      return $response->withStatus(401);
    }
  } catch (PDOException $e) {
    echo '{"error":{"text":' . $e->getMessage() . '}}';
  }
}

function uploadFile(Request $request,Response $response){
  if(!isset($_SESSION['username'])){
    echo "Unauthorized";
    return $response->withStatus(401);
  }

  // check if the file is text
  $allow=array('txt');
  $suffix = pathinfo($_FILES['filename']['name'], PATHINFO_EXTENSION);
  if(!in_array($suffix, $allow)) {
    echo 'The file type is not allowed to upload';
    return $response->withStatus(400);
  }

  // check if to destination to store file is existed
  $userpath=$_SERVER['DOCUMENT_ROOT'].'/upload/'.$_SESSION['username'];
  if(!file_exists($userpath)){
    mkdir($userpath,0755,true);
  }

  $filepath=$userpath.'/'.$_FILES['filename']['name'];

  // move the file to the desired destination
  if(!move_uploaded_file($_FILES['filename']['tmp_name'], $filepath)){
    echo 'Failed to move file';
    return $response->withStatus(500);
  }
  echo 'file updated successfully</br>';
  echo "<a href='../template/homepage.html'>Return to homepage</a>"; //this is for testing
}

function downloadFile(Request $request,Response $response){
  if(!isset($_SESSION['username'])){
    echo "Unauthorized";
    return $response->withStatus(401);
  }
  $filepath=$_SERVER['DOCUMENT_ROOT'].'/upload/'.$_SESSION['username'].$_GET['filename'];

  $file=fopen($filepath,"r");
  $filesize=filesize($filepath);

  header("Content-type: application/octet-stream");
  header("Accept-Ranges: bytes");
  header("Accept-Length: $filesize");
  //prompt the download window
  header("Content-Disposition: attachment; filename=".$_GET['filename']);
  echo fread($file,$filesize);
  fclose($file);
}

function addNotes(Request $request,Response $response){
  if(!isset($_SESSION['username'])){
    echo "Unauthorized";
    return $response->withStatus(401);
  }

  $title=$_POST['title'];
  $content=$_POST['content'];
  $owner=$_SESSION['username'];

  $sql = "insert into note(title,content,owner) values ('$title','$content', '$owner')";
  try {
    $db = getConnection();
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $db = null;
    echo "Notes registered successfully";
  } catch (PDOException $e) {
    echo '{"error":{"text":' . $e->getMessage() . '}}';
  }
}

function getNotes(){
  $owner=$_SESSION['username'];
  $sql="select * from note where owner='$owner'";
  try {
    $db = getConnection();
    $stmt = $db->query($sql);
    $note = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    echo '{"notes": ' . json_encode($note) . '}';
  } catch (PDOException $e) {
    echo '{"error":{"text":' . $e->getMessage() . '}}';
  }
}

function getNotesByTitle(){
  $owner=$_SESSION['username'];
  $title=$_GET['title'];
  $sql="select * from note where owner='$owner' and title='$title'";
  try {
    $db = getConnection();
    $stmt = $db->query($sql);
    $note = $stmt->fetchObject();
    $db = null;
    echo '{"note": ' . json_encode($note) . '}';
  } catch (PDOException $e) {
    echo '{"error":{"text":' . $e->getMessage() . '}}';
  }
}
 ?>
