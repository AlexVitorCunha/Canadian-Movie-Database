<?php

$keywords = "";
if(isset($_GET['k'])){
   $keywords = filter_var($_GET['k'], FILTER_SANITIZE_STRING); 
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title_tag ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="apple-touch-icon" sizes="180x180" href="./apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="./favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="./favicon-16x16.png">
    <link rel="manifest" href="./site.webmanifest">
</head>

<body>
    <div class="container-fullwidth">
        <header>
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <a class="navbar-brand" href="main.php">
                    <img  src="./img/logo.png" alt="logo" height=24px>
                    CMDb
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="main.php">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="movies.php">Movie List</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="getjson2.html">Gallery</a>
                            </li>
                            <?php if(is_logged_in()){ ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="movie.php">Add Movie</a>
                                </li>
                            <?php }?>
                            <li class="nav-item">
                                <a class="nav-link" href="store.php">Store</a>
                            </li>
                        </ul>
                        <form class="d-flex pe-5" action="movies.php" method="GET">
                            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="k" value="<?= $keywords ?>" >
                            <button class="btn btn-outline-success" type="submit">Search</button>
                        </form>
                        <ul class="navbar-nav d-flex">
                            <?php if(is_logged_in()){ ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-person-circle"></i> <?php echo $_SESSION['username']; ?> 
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                        <li><a class="dropdown-item" href="logout.php">Logout <i class="bi bi-box-arrow-right"></i></a></li>
                                    </ul>
                                </li>
                                <?php }else{?>
                                    <li class="nav-item">
                                        <a class="nav-link" href="login.php">Login</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="register.php">Register</a>
                                    </li>
                                <?php }?>     
                        </ul>
                    </div>
                </div>
            </nav>
        </header>