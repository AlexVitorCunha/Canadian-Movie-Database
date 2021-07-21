<?php
session_start();

require_once 'validations.php';

require_login();

//connect to db
require_once 'database.php';
$conn = db_connect();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    //save form inputs into variables
    $title = trim(filter_var($_POST['title'],FILTER_SANITIZE_STRING));
    $date = trim(filter_var($_POST['date'],FILTER_SANITIZE_STRING));
    $genre = trim(filter_var($_POST['genre'],FILTER_SANITIZE_STRING));
    $language = $_POST['language'];
    $imdb = trim(filter_var($_POST['imdb'],FILTER_SANITIZE_STRING));

    $new_movie = [];
    $new_movie['title'] = $title;
    $new_movie['date'] = $date;
    $new_movie['genre'] = $genre;
    $new_movie['language'] = $language;
    $new_movie['imdb'] = $imdb;

    //validate the inputs
    $errors = validate_movie($new_movie);

    //if there are no errors, insert into db
    if(empty($errors)){
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
    
            header("Location: movies.php");
            exit;
        }catch (Exception $e) {
            header("Location: error.php");
            exit;
        }
    }
}
?>

<?php
    $title_tag = 'Add Movie';
    include_once './shared/top.php';
?>

<h1 class="text-center mt-5">Add New Movie <i class="bi bi-camera-reels-fill"></i></h1>

<div class="row mt-5 justify-content-center">
    <form class="col-9 mb-5" method="POST">
        <div class="row mb-4">
            <label for="title" class="col-lg-2 col-0 col-form-label fs-5 text-lg-end text-center">Movie Title</label>
            <div class="col-lg-10 col-12">
                <input required type="text" name="title" class="<?= (isset($errors['title']) ? 'is-invalid ' : ''); ?>form-control form-control-lg">
                <p class="text-danger"><?= $errors['title'] ?? ''; ?></p>
            </div>
        </div>

        <div class="row mb-4">
            <label for="date" class="col-lg-2 col-0 col-form-label fs-5 text-lg-end text-center">Release Date</label>
            <div class="col-lg-10 col-12">
                <input type="date" name="date" class="<?= (isset($errors['date']) ? 'is-invalid ' : ''); ?>form-control form-control-lg">
                <p class="text-danger"><?= $errors['date'] ?? ''; ?></p>
            </div>
        </div>

        <div class="row mb-4">
            <label for="genre" class="col-lg-2 col-0 col-form-label fs-5 text-lg-end text-center">Genre</label>
            <div class="col-lg-10 col-12">
                <input type="text" name="genre" class="<?= (isset($errors['genre']) ? 'is-invalid ' : ''); ?>form-control form-control-lg">
                <p class="text-danger"><?= $errors['genre'] ?? ''; ?></p>
            </div>
        </div>

        <div class="row mb-4">
            <label class="col-lg-2 col-0 col-form-label fs-5 text-lg-end text-center" for="language">Language</label>
            <div class="col-lg-10 col-12">
                <select name="language" class="form-select form-select-lg">
                    <?php
                        $sql = "SELECT lang FROM langs ORDER BY lang";
                        $langs = db_queryAll($sql, $conn);

                        foreach ($langs as $lang){
                            echo "<option value=" . $lang["lang"] . ">" . ucfirst($lang["lang"]) . "</option>";
                        }
                    ?>
                </select>
            </div>
        </div>

        <div class="row mb-4">
            <label for="imdb" class="col-lg-2 col-0 col-form-label fs-5 text-lg-end text-center">iMDb link</label>
            <div class="col-lg-10 col-12">
                <input required pattern="https:\/\/www.imdb.com\/title\/tt[0-9]{7}"  type="text" name="imdb" class="<?= (isset($errors['imdb']) ? 'is-invalid ' : ''); ?>form-control form-control-lg">
                <p class="text-danger"><?= $errors['imdb'] ?? ''; ?></p>
            </div>
        </div>

        <div class="d-grid">
            <button class="btn btn-outline-success btn-lg">Submit</button>
        </div>
    </form>
</div>

<?php

include_once './shared/footer.php';

?>