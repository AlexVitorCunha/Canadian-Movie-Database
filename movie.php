<?php
//connect to db
require_once 'database.php';
$conn = db_connect();
?>

<?php
    include_once './shared/top.php';
?>

<h1 class="text-center mt-5">Add New Movie <i class="bi bi-camera-reels-fill"></i></h1>

<div class="row mt-5 justify-content-center">
    <form action="save-movie.php" class="col-9 mb-5" method="POST">
        <div class="row mb-4">
            <label for="title" class="col-lg-2 col-0 col-form-label fs-5 text-lg-end text-center">Movie Title</label>
            <div class="col-lg-10 col-12">
                <input required type="text" name="title" class="form-control form-control-lg">
            </div>
        </div>

        <div class="row mb-4">
            <label for="date" class="col-lg-2 col-0 col-form-label fs-5 text-lg-end text-center">Release Date</label>
            <div class="col-lg-10 col-12">
                <input type="date" name="date" class="form-control form-control-lg">
            </div>
        </div>

        <div class="row mb-4">
            <label for="genre" class="col-lg-2 col-0 col-form-label fs-5 text-lg-end text-center">Genre</label>
            <div class="col-lg-10 col-12">
                <input type="text" name="genre" class="form-control form-control-lg">
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
                <input required pattern="https:\/\/www.imdb.com\/title\/tt[0-9]{7}"  type="text" name="imdb" class="form-control form-control-lg">
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