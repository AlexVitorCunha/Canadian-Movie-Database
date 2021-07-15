<?php
session_start();

require_once 'validations.php';

require_login();
//connect to db
require_once 'database.php';
$conn = db_connect();
?>
<?php

//save form inputs into variables
$title = trim(filter_var($_POST['title'],FILTER_SANITIZE_STRING));
$date = trim(filter_var($_POST['date'],FILTER_SANITIZE_STRING));
$genre = trim(filter_var($_POST['genre'],FILTER_SANITIZE_STRING));
$language = $_POST['language'];
$imdb = trim(filter_var($_POST['imdb'],FILTER_SANITIZE_STRING));

$is_form_valid = true;

//check if all inputs are valid
if(empty($title)){
    echo "Please enter a title";
    $is_form_valid = false;
}
if(empty($date)){
    echo "Please enter a date";
    $is_form_valid = false;
}

$imdb_regex = "/https:\/\/www.imdb.com\/title\/tt[0-9]{7}/";

if(empty($imdb) || !preg_match($imdb_regex, $imdb)){
    echo "Please enter a valid url";
    $is_form_valid = false;
}

if($is_form_valid){
    try{
        //set up the SQL INSERT command
        $sql = "INSERT INTO movies (movie_title, release_date, genre, lang, url) VALUES (:title, :date, :genre, :language, :imdb)";

        //create a command object and fill the parameters with the form values
        $cmd = $conn->prepare($sql);
        $cmd -> bindParam(':title', $title, PDO::PARAM_STR, 50);
        $cmd -> bindParam(':date', $date, PDO::PARAM_STR, 10);
        $cmd -> bindParam(':genre', $genre, PDO::PARAM_STR, 32);
        $cmd -> bindParam(':language', $language, PDO::PARAM_STR, 7);
        $cmd -> bindParam(':imdb', $imdb, PDO::PARAM_STR, 100);

        //execute the command
        $cmd -> execute();

        //disconnect from the db
        $conn = null;

        //show message
        echo "Movie Saved";
    }catch (Exception $e) {
        header("Location: error.php");
    }
    
}
?>