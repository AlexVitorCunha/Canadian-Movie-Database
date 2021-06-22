<?php
//connect to db
require_once 'database.php';
$conn = db_connect();
?>

<?php
    include_once './shared/top.php';
    if($_SERVER['REQUEST_METHOD'] == 'GET'){
        $id = filter_var($_GET['movie_id'], FILTER_SANITIZE_NUMBER_INT);

        $sql = "SELECT * FROM movies WHERE movie_id=" . $id;
        $movie = db_queryOne($sql, $conn);
    }
    else if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        $title = trim(filter_var($_POST['title'],FILTER_SANITIZE_STRING));
        $date = trim(filter_var($_POST['date'],FILTER_SANITIZE_STRING));
        $genre = trim(filter_var($_POST['genre'],FILTER_SANITIZE_STRING));
        $language = $_POST['language'];
        $imdb = trim(filter_var($_POST['imdb'],FILTER_SANITIZE_STRING));
        $id = trim(filter_var($_POST['movie_id'], FILTER_SANITIZE_NUMBER_INT));

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
                $sql = "UPDATE movies SET movie_title=:title, ";
                $sql .= "release_date=:date, genre=:genre, lang=:language, url=:imdb ";
                $sql .= "WHERE movie_id=:id";

                //create a command object and fill the parameters with the form values
                $cmd = $conn->prepare($sql);
                $cmd -> bindParam(':title', $title, PDO::PARAM_STR, 50);
                $cmd -> bindParam(':date', $date, PDO::PARAM_STR, 10);
                $cmd -> bindParam(':genre', $genre, PDO::PARAM_STR, 32);
                $cmd -> bindParam(':language', $language, PDO::PARAM_STR, 7);
                $cmd -> bindParam(':imdb', $imdb, PDO::PARAM_STR, 100);
                $cmd -> bindParam(':id', $id, PDO::PARAM_INT);

                //execute the command
                $cmd -> execute();

                //disconnect from the db
                $conn = null;

                header("Location: movies.php");
            } catch (Exception $e) {
                header("Location: error.php");
            }
            
        }
    }
?>

<h1 class="text-center mt-5">Edit Movie <i class="bi bi-camera-reels-fill"></i></h1>

<div class="row mt-5 justify-content-center">
    <form class="col-9 mb-5" method="POST">
        <div class="row mb-4">
            <label for="title" class="col-lg-2 col-0 col-form-label fs-5 text-lg-end text-center">Movie Title</label>
            <div class="col-lg-10 col-12">
                <input required type="text" name="title" class="form-control form-control-lg" value="<?php echo $movie['movie_title'] ?>">
            </div>
        </div>

        <div class="row mb-4">
            <label for="date" class="col-lg-2 col-0 col-form-label fs-5 text-lg-end text-center">Release Date</label>
            <div class="col-lg-10 col-12">
                <input type="date" name="date" class="form-control form-control-lg" value="<?php echo $movie['release_date'] ?>">
            </div>
        </div>

        <div class="row mb-4">
            <label for="genre" class="col-lg-2 col-0 col-form-label fs-5 text-lg-end text-center">Genre</label>
            <div class="col-lg-10 col-12">
                <input type="text" name="genre" class="form-control form-control-lg" value="<?php echo ucfirst($movie['genre']) ?>">
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
                <input required pattern="https:\/\/www.imdb.com\/title\/tt[0-9]{7}"  type="text" name="imdb" class="form-control form-control-lg" value="<?php echo $movie['url'] ?>">
            </div>
        </div>

        <div class="d-grid">
            <input readonly class="form-control form-control-lg" type="hidden" name="movie_id" value=<?php echo  $id?>>
            <button class="btn btn-outline-success btn-lg">Update Game</button>
        </div>
    </form>
</div>

<?php

include_once './shared/footer.php';

?>