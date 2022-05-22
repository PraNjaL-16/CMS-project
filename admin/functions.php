<?php

    function query($query) {
        global $connection;
        $result = mysqli_query($connection, $query);
        confirmQuery($result);
        return $result;
    }

    function escape($string) {
        global $connection;

        return mysqli_real_escape_string($connection, trim($string));
    }

    function fetchRecords($result) {
        return mysqli_fetch_array($result);
    }

    function currentUser() {
        global $connection;

        if(isset($_SESSION['username'])) {
            return $_SESSION['username'];
        }

        return false;
    }

    // to get online users count using javascript AJAX call 
    // don't have to refresh the page with javascript 
    function usersOnline() {
        if(isset($_GET['onlineusers'])) {

            global $connection;

            if(!$connection) {
                // we don't have access of session & database in this function 
                // as we are directly coming to this function from javascript
                session_start();
                include "../includes/db.php";
            
                // catching current session id
                $session = session_id();
                $time = time();
                $time_out_in_seconds = 05;
                $time_out = $time - $time_out_in_seconds;

                $query = "SELECT * FROM users_online WHERE session = '$session'";
                $send_query = mysqli_query($connection, $query);
                $count = mysqli_num_rows($send_query);

                if($count == NULL) {
                    mysqli_query($connection, "INSERT INTO users_online (session, time) VALUES ('$session', '$time')");
                }
                else {
                    mysqli_query($connection, "UPDATE users_online SET time = '$time' WHERE session = '$session'");
                }

                $users_online_query = mysqli_query($connection, "SELECT * FROM users_online WHERE time > '$time_out'");
                echo $count_user = mysqli_num_rows($users_online_query);
            }   
        }
    }
    usersOnline();

    // to check if a sql query passed or failed
    function confirmQuery($result) {
        global $connection;

        if(!$result) {
            die('Query failded. ' . mysqli_error($connection));
        }
    }

    // create operation - CRUD
    function insert_categories() {
        global $connection;
        
        if(isset($_POST['submit'])) {
            $cat_title = $_POST['cat_title'];
            $user_id = loggedInUserId();

            if($cat_title == "" || empty($cat_title)) {
                echo 'This field should not be empty';
            }
            else {
                $stmt = mysqli_prepare($connection, "INSERT INTO categories (user_id, cat_title) VALUES (?, ?)");
                mysqli_stmt_bind_param($stmt, 'is', $user_id, $cat_title);
                mysqli_stmt_execute($stmt);
                confirmQuery($stmt);

                mysqli_stmt_close($stmt);
            }
        }
    }

    // read operation - CRUD
    function findAllCategories() {
        global $connection;
        
        $query = "SELECT * FROM categories";
        $select_categories = mysqli_query($connection, $query); 

        while($row = mysqli_fetch_assoc($select_categories)) {
            $cat_id = $row['cat_id'];
            $cat_title = $row['cat_title'];

            echo "<tr>";
            echo "<td>{$cat_id}</td>";
            echo "<td>{$cat_title}</td>";
            echo "<td><a href='categories.php?delete={$cat_id}'>Delete</a></td>";
            echo "<td><a href='categories.php?edit={$cat_id}'>Edit</a></td>";
            echo "</tr>";
        }
    }

    // delete operation - CRUD
    function deleteCategories() {
        global $connection;
        
        if(isset($_GET['delete'])) {
            $the_cat_id = $_GET['delete'];

            $query = "DELETE FROM categories WHERE cat_id = $the_cat_id ";
            $delete_query = mysqli_query($connection, $query);

            // to reload the page
            header("Location: categories.php");
        }  
    }

    function loggedInUserId() {
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

    function countRecords($result) {
        return mysqli_num_rows($result);
    }

    function getAllUsersPosts() {
        return query("SELECT * FROM posts WHERE user_id=".loggedInUserId()."");
    }

    function getAllPostsUsersComments() {
        return query("SELECT * FROM posts INNER JOIN comments ON posts.post_id=comments.comment_post_id WHERE posts.user_id=".loggedInUserId()."");
    }

    function getAllUsersCategories() {
        return query("SELECT * FROM categories WHERE user_id=".loggedInUserId()."");
    }

    function getAllUsersPublishedPosts() {
        return query("SELECT * FROM posts WHERE user_id=".loggedInUserId()." AND post_status='published'");
    }

    function getAllUsersDraftPosts() {
        return query("SELECT * FROM posts WHERE user_id=".loggedInUserId()." AND post_status='draft'");
    }

    function getAllUserApprovedComments() {
        return query("SELECT * FROM posts INNER JOIN comments ON posts.post_id=comments.comment_post_id WHERE posts.user_id=".loggedInUserId()." AND comments.comment_status='approve'");
    }

    function getAllUserUnapprovedComments() {
        return query("SELECT * FROM posts INNER JOIN comments ON posts.post_id=comments.comment_post_id WHERE posts.user_id=".loggedInUserId()." AND comments.comment_status='unapprove'");
    }

    function recordCount($table) {
        global $connection;

        $query = "SELECT * FROM " . $table;
        $select_all_post = mysqli_query($connection, $query);
        $result = mysqli_num_rows($select_all_post);

        confirmQuery($select_all_post);

        return $result;
    }

    function checkStatus($table, $column, $status) {
        global $connection;

        $query = "SELECT * FROM $table WHERE $column = '$status' ";
        $result = mysqli_query($connection, $query);
        confirmQuery($result);

        return mysqli_num_rows($result);
    }

    function checkUserRole($table, $column, $role) {
        global $connection;

        $query = "SELECT * FROM $table WHERE $column = '$role' ";
        $result = mysqli_query($connection, $query);
        confirmQuery($result);

        return mysqli_num_rows($result);
    }

    function isLoggedIn() {
        if(isset($_SESSION['user_role'])) {
            return true;
        }

        return false;
    }

    function get_user_name() {
        return isset($_SESSION['username']) ? $_SESSION['username'] : null;
    }
    
    function is_Admin() {
        if(isLoggedIn()) {
            $result = query("SELECT user_role FROM users WHERE user_id = ".$_SESSION['user_id']."");
            $row = fetchRecords($result);
            if($row['user_role'] === 'admin') {
                return true;
            }
            else {
                return false;
            }
        }

        return false;
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

    function redirect($location) {
        header("Location: " . $location);
        exit;
    }
?>