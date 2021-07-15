<?php
session_start();

require_once 'validations.php';

require_login();

//connect to db
require_once 'database.php';
$conn = db_connect();


if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $id = filter_var($_GET['movie_id'], FILTER_SANITIZE_NUMBER_INT);

    $sql = "SELECT * FROM movies WHERE movie_id=" . $id;
    $movie = db_queryOne($sql, $conn);

    include_once './shared/top.php';
?>
<h1 class="text-center mt-5 display-1 text-danger"><i class="bi bi-x-octagon"></i></h1>
<h1 class="text-center mt-5">Are you sure you want to delete this?</h1>

<div class="row mt-5 justify-content-center">
    <form class="col-9 mb-5" method="POST">
        <div class="row mb-4">
            <label for="title" class="col-lg-2 col-0 col-form-label fs-5 text-lg-end text-center">Movie Title</label>
            <div class="col-lg-10 col-12">
                <input readonly type="text" name="title" class="form-control form-control-lg" value="<?php echo $movie['movie_title'] ?>">
            </div>
        </div>

        <div class="row mb-4">
            <label for="date" class="col-lg-2 col-0 col-form-label fs-5 text-lg-end text-center">Release Date</label>
            <div class="col-lg-10 col-12">
                <input readonly type="date" name="date" class="form-control form-control-lg" value=<?php echo $movie['release_date']?>>
            </div>
        </div>

        <div class="row mb-4">
            <label for="genre" class="col-lg-2 col-0 col-form-label fs-5 text-lg-end text-center">Genre</label>
            <div class="col-lg-10 col-12">
                <input readonly type="text" name="genre" class="form-control form-control-lg" value=<?php echo ucfirst($movie['genre'])?>>
            </div>
        </div>

        <div class="row mb-4">
            <label class="col-lg-2 col-0 col-form-label fs-5 text-lg-end text-center" for="language">Language</label>
            <div class="col-lg-10 col-12">
                <input readonly type="text" name="lang" class="form-control form-control-lg" value=<?php echo ucfirst($movie['lang'])?>>
            </div>
        </div>

        <div class="row mb-4">
            <label for="imdb" class="col-lg-2 col-0 col-form-label fs-5 text-lg-end text-center">iMDb link</label>
            <div class="col-lg-10 col-12">
                <input readonly type="text" name="imdb" class="form-control form-control-lg" value=<?php echo $movie['url']?>>
            </div>
        </div>

        <div class="d-grid">
        <input readonly type="hidden" name="movie_id" class="form-control form-control-lg" value=<?php echo $id?>>
            <button class="btn btn-outline-danger btn-lg">Delete forever</button>
        </div>
    </form>
</div>
<?php
}else if($_SERVER['REQUEST_METHOD'] == 'POST'){
    try{
        $id = filter_var($_POST['movie_id'], FILTER_SANITIZE_NUMBER_INT);

        $sql = "DELETE FROM movies WHERE movie_id=" . $id;

        $cmd = $conn->prepare($sql);
        $cmd -> execute();

        header("Location: movies.php");
    } catch (Exception $e) {
        header("Location: error.php");
    }

    
}

?>