<?php

    function query($query) {
        global $connection;

        return mysqli_query($connection, $query);
    }

    function imagePlaceholder($image=null) {
        if(!$image) {
            return './images/image_1.jpg';
        }
        else {
            return $image;
        }
    }

    function redirect($location) {
        header("Location: " . $location);
        exit();
    }

    // to check if a sql query passed or failed
    function confirmQuery($result) {
        global $connection;

        if(!$result) {
            die('Query failded. ' . mysqli_error($connection));
        }
    }

    function username_exists($username) {
        global $connection;

        $query = "SELECT username FROM users WHERE username = '$username'";
        $result = mysqli_query($connection, $query);
        confirmQuery($result);

        if(mysqli_num_rows($result) > 0) {
            return true;
        }
        else {
            return false;
        }
    }

    function email_exists($email) {
        global $connection;

        $query = "SELECT user_email FROM users WHERE user_email = '$email'";
        $result = mysqli_query($connection, $query);
        confirmQuery($result);

        if(mysqli_num_rows($result) > 0) {
            return true;
        }
        else {
            return false;
        }
    }

    function register_user($username, $email, $password) {
        global $connection;

        $username = mysqli_real_escape_string($connection , $username);
        $email = mysqli_real_escape_string($connection , $email);
        $password = mysqli_real_escape_string($connection , $password);

        // new password encryption
        $password = password_hash($password, PASSWORD_BCRYPT, array('cost' => 12));

        $query = "INSERT INTO users (username, user_email, user_password, user_role) ";
        $query .= "VALUES('{$username}', '{$email}', '{$password}', 'subscriber')";
        $register_user_query = mysqli_query($connection, $query);

        confirmQuery($register_user_query);
        
    }

    function login_user($username, $password) {

        global $connection;

        $username = trim($username);
        $password = trim($password);
       
        $username = mysqli_real_escape_string($connection, $username);
        $password = mysqli_real_escape_string($connection, $password);

        $query = "SELECT * FROM users WHERE username = '{$username}' ";
        $select_user_query = mysqli_query($connection, $query);

        confirmQuery($select_user_query);

        while($row = mysqli_fetch_assoc($select_user_query)) {
            $db_user_id = $row['user_id'];
            $db_username = $row['username'];
            $db_user_password = $row['user_password'];
            $db_user_firstname = $row['user_firstname'];
            $db_user_lastname = $row['user_lastname'];
            $db_user_role = $row['user_role'];
        }

        // password verification
        if(password_verify($password, $db_user_password)) {
            // storing value in session as key/value pair (i.e. as associative array)
            $_SESSION['user_id'] = $db_user_id;
            $_SESSION['username'] = $db_username;
            $_SESSION['firstname'] = $db_user_firstname;
            $_SESSION['lastname'] = $db_user_lastname;
            $_SESSION['user_role'] = $db_user_role;

            redirect("./admin/index.php");
        }
        else {
            redirect("./index.php"); 
        }
    }

    function is_Admin($username = "") {
        global $connection;

        $query = "SELECT user_role FROM users WHERE username = '$username'";
        $result = mysqli_query($connection, $query);
        confirmQuery($result);

        $row = mysqli_fetch_array($result);

        if($row['user_role'] === 'admin') {
            return true;
        }
        else {
            return false;
        }
    }

    function ifItIsMethod($method=null) {
        if($_SERVER['REQUEST_METHOD'] === strtoupper($method)) {
            return true;
        }

        return false;
    }

    function isLoggedIn() {
        if(isset($_SESSION['user_role'])) {
            return true;
        }

        return false;
    }

    function checkIfUserIsLoggedInAndRedirect($redirectLocation=null) {
        if(isLoggedIn()) {
            redirect($redirectLocation);
        }
    }

    function loggedInUserId() {
        global $connection;

        if(isLoggedIn()) {
            $result = query("SELECT * FROM users WHERE username='" . $_SESSION['username']. "'");
            confirmQuery($result);
            $user = mysqli_fetch_array($result);

            if(mysqli_num_rows($result) >= 1) {
                return $user['user_id'];
            }
        }

        return false;
    }

    function userLikedThisPost($post_id='') {
        if(loggedInUserId()) {
            $result = query("SELECT * FROM likes WHERE user_id=" . loggedInUserId() . " AND post_id={$post_id}");
            confirmQuery($result);
            return mysqli_num_rows($result) >=1 ? true : false;
        }

        return false;
    }

    function getPostLikes($post_id) {
        $result = query("SELECT * FROM likes where post_id=$post_id");
        confirmQuery($result);
        echo mysqli_num_rows($result);
    }
?>