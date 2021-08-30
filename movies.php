<?php
session_start();

require_once 'validations.php';

//connect to database
require_once 'database.php';
$conn = db_connect();
$title_tag = 'Movie List';
include_once './shared/top.php';
//build a sql query
$sql = "SELECT * FROM movies";
// will store the separate words that the user is searching
$word_list = [];

if(!empty($keywords)){
    $sql .= " WHERE "; 

    // split the multiple keywords into an array using php explode
    $word_list = explode(" ", $keywords);

    //loop through tje word list array, and add each word to the where
    foreach($word_list as $key => $word) {
        // but for the first word, omit the word OR
        if ($key == 0) {
            $sql .= "movie_title LIKE '%" . $word . "%'";
        }else{
            $sql .= " OR movie_title like '%" . $word . "%'";
        }
    }

}

$sql .= " ORDER by movie_title";

$movies = db_queryAll($sql, $conn);
?>

<div class="table-responsive mt-4 ms-4 me-4">
    <table class="sortable table table-striped table-bordered fs-5">
        <thead>
            <tr>
                <th scope="col" class="text-center">Title</th>
                <th scope="col" class="text-center">Release Date</th>
                <th scope="col" class="text-center">Genre</th>
                <th scope="col" class="text-center">Language</th>
                <th scope="col" class="text-center col-2 sorttable_nosort">iMDb url</th>
                <?php if(is_logged_in()){ ?>
                <th scope="col" class="text-center col-1 sorttable_nosort">Edit</th>
                <th scope="col" class="text-center col-2 sorttable_nosort">Delete</th>
                <?php }?>
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
                <?php if(is_logged_in()){ ?>
                <td class="text-center"><a href="movie-edit.php?movie_id=<?php echo $movie['movie_id'];?>"
                        class="btn btn-secondary"><span class="d-none d-sm-inline">Edit</span> <i
                            class="bi bi-pencil-square"></i></a></td>
                <td class="text-center"><a href="movie-delete.php?movie_id=<?php echo $movie['movie_id'];?>"
                        class="btn btn-danger"><span class="d-none d-sm-inline">Delete</span> <i
                            class="bi bi-trash"></i></a></td>
                <?php } ?>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php
$t = filter_var($_GET['t'] ?? '', FILTER_SANITIZE_STRING);
$msg = filter_var($_GET['msg'] ?? '', FILTER_SANITIZE_STRING);

display_toast($t, $msg);
include_once './shared/footer.php';
?>