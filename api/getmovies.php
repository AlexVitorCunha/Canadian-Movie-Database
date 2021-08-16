<?php
header("Access-Control-Allow-Origin: *");
//connect to db
require_once '../database.php';
$conn = db_connect();

$sql = "SELECT * FROM movies ORDER BY movie_title";

$cmd = $conn -> prepare($sql);
$cmd -> execute();
$movies = $cmd -> fetchAll(PDO::FETCH_ASSOC);

function insert_img_urls($object){
    if(isset($object['poster'])){
        $object['poster'] = "https://lamp.computerstudi.es/~Alex200465920/COMP1006/project1/uploads/" . $object['poster'];
    } else{
        $object['poster'] = null;
    }
    return $object;
}

$movies2 = array_map('insert_img_urls',$movies);

echo json_encode($movies2);