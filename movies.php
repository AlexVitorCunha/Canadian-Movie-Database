<?php
//connect to database
require_once 'database.php';
$conn = db_connect();
?>
<?php
include_once './shared/top.php';
//build a sql query
$sql = "SELECT * FROM movies ORDER BY movie_title";
$movies = db_queryAll($sql, $conn);
?>

<div class="table-responsive mt-4 ms-4 me-4">
    <table class="table table-striped table-bordered fs-5">
        <thead>
            <tr>
                <th scope="col" class="text-center">Title</th>
                <th scope="col" class="text-center">Release Date</th>
                <th scope="col" class="text-center">Genre</th>
                <th scope="col" class="text-center">Language</th>
                <th scope="col" class="text-center col-2">iMDb url</th>
                <th scope="col" class="text-center col-1">Edit</th>
                <th scope="col" class="text-center col-2">Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($movies as $movie) { ?>
            <tr>
                <th scope="row" class="text-center"><?php echo $movie['movie_title'] ?></th>
                <td class="text-center"><?php format_date($movie['release_date']) ?></td>
                <td class="text-center"><?php echo ucfirst($movie['genre']) ?></td>
                <td class="text-center"><?php echo ucfirst($movie['lang']) ?></td>
                <td class="text-center"><a href="<?php echo $movie['url'] ?>" target="_blank"
                        class="btn btn-warning"><span class="d-none d-sm-inline">See more</span> <i
                            class="bi bi-box-arrow-up-right"></i></a></td>
                <td class="text-center"><a href="movie-edit.php?movie_id=<?php echo $movie['movie_id'];?>"
                        class="btn btn-secondary"><span class="d-none d-sm-inline">Edit</span> <i
                            class="bi bi-pencil-square"></i></a></td>
                <td class="text-center"><a href="movie-delete.php?movie_id=<?php echo $movie['movie_id'];?>"
                        class="btn btn-danger"><span class="d-none d-sm-inline">Delete</span> <i
                            class="bi bi-trash"></i></a></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php
include_once './shared/footer.php';
?>