<?php

    function is_logged_in(){
        return isset($_SESSION['user_id']);
    }

    function require_login(){
        if(!is_logged_in()){
            header("Location:login.php");
            exit;
        }
    }

    function validate_movie($movie){
        $errors = [];
        if(empty($movie['movie_title'])){
            $errors['title'] = "Please enter a title";
        }
        if(empty($movie['genre'])){
            $errors['genre'] = "Please enter a genre";
        }

        if(empty($movie['release_date'])){
            $errors['date'] = "Please enter a date";
        }
        
        $imdb_regex = "/https:\/\/www.imdb.com\/title\/tt[0-9]{7}/";
        
        if(empty($movie['url']) || !preg_match($imdb_regex, $movie['url'])){
            $errors['imdb'] = "Please enter a valid url";   
        }

        if(isset($movie['size']) && $movie['size'] > 1000000){
            $errors['pic'] = "Image must be less than 1MB";
        }

        if(isset($movie['type']) && !($movie['type'] == 'image/jpeg' || $movie['type'] == 'image/png')){
            $errors['pic'] = "Image format must be .jpg or .png";
        }
        return $errors;
    }

    function validate_registration($user, $conn){
        $errors = [];
        $email_regex = "/.+\@.+\..+/";
        if(empty(trim($user['email'])))
        {
            $errors['email'] = "Email cannot be blank";
        }
        else if(!preg_match($email_regex, $user['email'])) {
            $errors['email'] = "Username must be a valid email address";
        }

        if(empty(trim($user['new-password'])))
        {
            $errors['password'] = "Password cannot be blank";
        }
        if(empty(trim($user['confirm-password'])))
        {
            $errors['confirm'] = "Confirmation password cannot be blank";
        }
        if(empty(trim($user['new-password'])) != empty(trim($user['confirm-password'])))
        {
            $errors['confirm'] = "Passwords must match";
        }

        $sql = "SELECT * FROM cmdb_users WHERE username='" . $user['email'] . "'";
        $cmd = $conn -> prepare($sql);
        $cmd -> execute();
        $found_username = $cmd -> fetch();

        if($found_username){
        $errors['email'] = "Username already taken"; 
        }

        return $errors;
    }

    function display_toast($t, $msg){
        if(!($t && $msg)) {
            return;
        }

        $msgs = [];
        $msgs['1'] = "Succcessfully Deleted";
        $msgs['2'] = "Successfully Edited";
        $msgs['3'] = "Successfully Added";

        echo <<<EOL
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="liveToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-dark text-light">
            <strong class="me-auto">$msgs[$t]</strong>
            <small>11 mins ago</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body bg-dark text-light">
            $msg
            </div>
        </div>
        </div>
        <script>
        window.addEventListener('DOMContentLoaded', () => {
           var toastElList = [].slice.call(document.querySelectorAll('.toast'))
            var toastList = toastElList.map(function (toastEl) {
            return new bootstrap.Toast(toastEl)
            });
            toastList.forEach(toast =>  toast.show()) 
        }); 
        </script>
        EOL;
    }

?>