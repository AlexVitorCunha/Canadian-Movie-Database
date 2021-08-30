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
    $new_movie['movie_title'] = $title;
    $new_movie['release_date'] = $date;
    $new_movie['genre'] = $genre;
    $new_movie['language'] = $language;
    $new_movie['url'] = $imdb;

    
    // get poster input if it exists
    if($_FILES['pic']['size'] > 0){
        $name = $_FILES['pic']['name'];
        $tmp_name = $_FILES['pic']['tmp_name'];
        $type = mime_content_type($tmp_name);
        $size = $_FILES['pic']['size'];
        $new_movie['name'] = substr(session_id(), 0, 5) . $name;
        $new_movie['tmp_name'] = $tmp_name;
        $new_movie['type'] = $type;
        $new_movie['size'] = $size;    
     }else{
         $new_movie['name'] = NULL; 
     }

    //validate the inputs
    $errors = validate_movie($new_movie);

    //if there are no errors, insert into db
    if(empty($errors)){
        try{
            //set up the SQL INSERT command
            $sql = "INSERT INTO movies (movie_title, release_date, genre, lang, url, poster) VALUES (:title, :date, :genre, :language, :imdb, :poster)";
    
            //create a command object and fill the parameters with the form values
            $cmd = $conn->prepare($sql);
            $cmd -> bindParam(':title', $title, PDO::PARAM_STR, 50);
            $cmd -> bindParam(':date', $date, PDO::PARAM_STR, 10);
            $cmd -> bindParam(':genre', $genre, PDO::PARAM_STR, 32);
            $cmd -> bindParam(':language', $language, PDO::PARAM_STR, 7);
            $cmd -> bindParam(':imdb', $imdb, PDO::PARAM_STR, 100);
            $cmd -> bindParam(':poster', $new_movie['name'], PDO::PARAM_STR, 100);

            move_uploaded_file($tmp_name, "./uploads/" . $new_movie['name']);
            //execute the command
            $cmd -> execute();
    
            header("Location: movies.php?t=3&msg=" . $title);
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


<div class="row mt-5 ms-1">
    <form class="row justify-content-center mb-5" method="POST" enctype="multipart/form-data">
        <div class="col-12 col-md-6">
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
        </div>
        <div class="col-12 col-sm-3 mb-5">
                <div class="card">
                    <img id="cover" src="https://dummyimage.com/320x480" class="card-img-top" alt="game cover">
                    <div class="card-body">
                        <input id="choosefile" type="file" name="pic" class="form-control">
                    </div>
                    <p class="px-3 pb-2 text-danger"><?= $errors['pic'] ?? ''; ?></p>
                </div>
            </div>
        <div class="row justify-content-center col-12 col-md-9">
            <button class="btn btn-outline-success btn-lg">Submit</button>
        </div>
    </form>
</div>

<script>
function handleFileSelect(evt){
    const reader = new FileReader();

    reader.addEventListener('load', (e) => {
        cover.src = e.target.result;
    })

    reader.readAsDataURL(evt.target.files[0]);
}
choosefile.addEventListener('change', handleFileSelect);
</script>

<?php

include_once './shared/footer.php';

?>