<?php
session_start();

require_once 'validations.php';

require_login();

//connect to db
require_once 'database.php';
$conn = db_connect();

    $title_tag = 'Edit movie';
    include_once './shared/top.php';
    if($_SERVER['REQUEST_METHOD'] == 'GET'){
        $id = filter_var($_GET['movie_id'], FILTER_SANITIZE_NUMBER_INT);

        $sql = "SELECT * FROM movies WHERE movie_id=" . $id;
        $movie = db_queryOne($sql, $conn);
    }
    else if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        $title = trim(filter_var($_POST['movie_title'],FILTER_SANITIZE_STRING));
        $date = trim(filter_var($_POST['release_date'],FILTER_SANITIZE_STRING));
        $genre = trim(filter_var($_POST['genre'],FILTER_SANITIZE_STRING));
        $language = $_POST['language'];
        $imdb = trim(filter_var($_POST['imdb'],FILTER_SANITIZE_STRING));
        $id = trim(filter_var($_POST['movie_id'], FILTER_SANITIZE_NUMBER_INT));
        $poster = trim(filter_var($_POST['poster'], FILTER_SANITIZE_STRING));

        //create an associative array on the user input
        $movie = [];
        $movie['movie_title'] = $title;
        $movie['release_date'] = $date;
        $movie['genre'] = $genre;
        $movie['language'] = $language;
        $movie['url'] = $imdb;

        // get poster input if it exists
        if($_FILES['pic']['size'] > 0){
            $name = $_FILES['pic']['name'];
            $tmp_name = $_FILES['pic']['tmp_name'];
            $type = mime_content_type($tmp_name);
            $size = $_FILES['pic']['size'];
            $movie['name'] = substr(session_id(), 0, 5) . $name;
            $movie['tmp_name'] = $tmp_name;
            $movie['type'] = $type;
            $movie['size'] = $size;    
         }else{
             $movie['name'] = $poster; 
         }
        
        //validate the inputs
        $errors = validate_movie($movie);

        //if there are no errors, update db
        if(empty($errors)){
            try{
                //set up the SQL INSERT command
                $sql = "UPDATE movies SET movie_title=:title, ";
                $sql .= "release_date=:date, genre=:genre, lang=:language, url=:imdb, poster=:poster ";
                $sql .= "WHERE movie_id=:id";

                //create a command object and fill the parameters with the form values
                $cmd = $conn->prepare($sql);
                $cmd -> bindParam(':title', $title, PDO::PARAM_STR, 50);
                $cmd -> bindParam(':date', $date, PDO::PARAM_STR, 10);
                $cmd -> bindParam(':genre', $genre, PDO::PARAM_STR, 32);
                $cmd -> bindParam(':language', $language, PDO::PARAM_STR, 7);
                $cmd -> bindParam(':imdb', $imdb, PDO::PARAM_STR, 100);
                $cmd -> bindParam(':id', $id, PDO::PARAM_INT);
                $cmd -> bindParam(':poster', $movie['name'], PDO::PARAM_STR, 100); 

                move_uploaded_file($tmp_name, "./uploads/" . $movie['name']);
                //delete previous image from the upload photo if new photo is added
                if($movie['name'] != $poster){
                    unlink("./uploads/" . $poster);
                }

                //execute the command
                $cmd -> execute();
                header("Location: movies.php?t=2&msg=" . $title);
                exit;
            } catch (Exception $e) {
                header("Location: error.php");
                exit;
            }
            
        }
    }
?>

<h1 class="text-center mt-5">Edit Movie <i class="bi bi-camera-reels-fill"></i></h1>

<div class="row mt-5 ms-1">
    <form class="row justify-content-center mb-5" method="POST" enctype="multipart/form-data">
        <div class="col-12 col-md-6">
            <div class="row mb-4">
                <label for="title" class="col-lg-2 col-0 col-form-label fs-5 text-lg-end text-center">Movie Title</label>
                <div class="col-lg-10 col-12">
                    <input required type="text" name="movie_title" class="<?= (isset($errors['title']) ? 'is-invalid ' : ''); ?>form-control form-control-lg" value="<?= $movie['movie_title'] ?>">
                    <p class="text-danger"><?= $errors['title'] ?? ''; ?></p>
                </div>
            </div>

            <div class="row mb-4">
                <label for="date" class="col-lg-2 col-0 col-form-label fs-5 text-lg-end text-center">Release Date</label>
                <div class="col-lg-10 col-12">
                    <input type="date" name="release_date" class="<?= (isset($errors['date']) ? 'is-invalid ' : ''); ?>form-control form-control-lg" value="<?= $movie['release_date'] ?>">
                    <p class="text-danger"><?= $errors['date'] ?? ''; ?></p>
                </div>
            </div>

            <div class="row mb-4">
                <label for="genre" class="col-lg-2 col-0 col-form-label fs-5 text-lg-end text-center">Genre</label>
                <div class="col-lg-10 col-12">
                    <input type="text" name="genre" class="<?= (isset($errors['genre']) ? 'is-invalid ' : ''); ?>form-control form-control-lg" value="<?php echo ucfirst($movie['genre']) ?>">
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

                            foreach ($langs as $eachlang){
                                echo "<option value=" . $eachlang["lang"] . ((strtolower($movie['lang']) == $eachlang['lang']) ? " selected" : " ") . ">" . ucfirst($eachlang["lang"]) . "</option>";
                            }
                        ?>
                    </select>
                </div>
            </div>

            <div class="row mb-4">
                <label for="imdb" class="col-lg-2 col-0 col-form-label fs-5 text-lg-end text-center">iMDb link</label>
                <div class="col-lg-10 col-12">
                    <input required pattern="https:\/\/www.imdb.com\/title\/tt[0-9]{7}"  type="text" name="imdb" class="<?= (isset($errors['imdb']) ? 'is-invalid ' : ''); ?>form-control form-control-lg" value="<?php echo $movie['url'] ?>">
                    <p class="text-danger"><?= $errors['imdb'] ?? ''; ?></p>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-3 mb-5">
            <div class="card">
                <img id="cover" src="<?= (isset($movie['poster'])) ? ("./uploads/" . $movie['poster']) : "https://dummyimage.com/320x480" ?>" class="card-img-top" alt="game cover">
                <div class="card-body">
                    <input id="choosefile" type="file" name="pic" class="form-control">
                </div>
                <p class="px-3 pb-2 text-danger"><?= $errors['pic'] ?? ''; ?></p>
            </div>
        </div>                    
        <div class="row justify-content-center col-12 col-md-9">
            <input readonly class="form-control form-control-lg" type="hidden" name="poster" value=<?= $movie['poster']?>>
            <input readonly class="form-control form-control-lg" type="hidden" name="movie_id" value=<?php echo  $id?>>
            <button class="btn btn-outline-success btn-lg">Update Movie</button>
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