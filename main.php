<?php
session_start();

require_once 'validations.php';

require_login();
//connect to database
require_once 'database.php';
$conn = db_connect();
?>
<?php
$title_tag = 'Home';
include_once './shared/top.php';
?>


<?php
include_once './shared/footer.php';
?>