<?php
require '../api/env.php';
createUserTable();
createNoteTable();
seedUserTable();

function getConnection(){
  $dbhost=getenv("MYSQL_HOST");
  $dbuser=getenv("MYSQL_USER");
  $dbpass=getenv("MYSQL_PASSWORD");
  $dbname=getenv("MYSQL_DATABASE");
  $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  return $dbh;
}

function createUserTable(){
  $sqlUser="create table user(id int(10) not null auto_increment,username varchar(30) NOT NULL UNIQUE,password varchar(40),primary key(id))";

  try {
    $db = getConnection();
    $stmt = $db->prepare($sqlUser);
    $stmt->execute();
    $db = null;
    echo "Table user created successfully\n";
  } catch (PDOException $e) {
    echo '"error":' . $e->getMessage() . "\n";
  }
}

function createNoteTable(){
  $sqlNote="create table note(id int(10) not null auto_increment,title varchar(100),content text(65535),owner varchar(30),primary key(id))";

  try {
    $db = getConnection();
    $stmt = $db->prepare($sqlNote);
    $stmt->execute();
    $db = null;
    echo "Table note created successfully\n";
  } catch (PDOException $e) {
    echo '"error":' . $e->getMessage() . "\n";
  }
}

function seedUserTable(){
  $sql="insert into user(username,password) values ('hxy','0000')";

  try {
    $db = getConnection();
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $db = null;
    echo "Insert data into user table successfully\n";
  } catch (PDOException $e) {
    echo '"error":' . $e->getMessage() . "\n";
  }
}
 ?>
