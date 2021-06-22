<?php
require_once 'db_cred.php';

function db_queryAll($sql, $conn){
    try{
       //run query and store results
        $cmd = $conn->prepare($sql);
        $cmd -> execute();
        return $cmd->fetchAll(); 
    } catch (Exception $e) {
        header("Location: error.php");
    }
    
}

function db_queryOne($sql, $conn){
    try{
       //run query and store  the results
        $cmd = $conn->prepare($sql);
        $cmd -> execute();
        return $cmd->fetch(); 
    } catch (Exception $e) {
        header("Location: error.php");
    }
    
}

function db_connect(){
    $conn = new PDO('mysql:host=' . DB_SERVER . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
    $conn -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conn;
}

function db_disconnect($conn){
    if (isset($conn)){
        $conn = null;
    }
}

function format_date($release_date){
    $date = new DateTime($release_date);
    echo $date->format('d/m/Y');
}

?>