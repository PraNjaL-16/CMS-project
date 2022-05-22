<?php

    if(isset($_GET['edit_user'])) {
        $the_user_id = $_GET['edit_user'];

        $query = "SELECT * FROM users WHERE user_id=$the_user_id";
        $select_user_query = mysqli_query($connection, $query); 

        while($row = mysqli_fetch_assoc($select_user_query)) {
            $user_firstname = $row['user_firstname'];
            $user_lastname = $row['user_lastname'];
            $username = $row['username'];
            $user_role = $row['user_role'];
            $user_email = $row['user_email'];
        }

        if(isset($_POST['edit_user'])) {
            $user_firstname = $_POST['user_firstname'];
            $user_lastname = $_POST['user_lastname'];
            $username = $_POST['username'];
            $user_role = $_POST['user_role'];
            $user_email = $_POST['user_email'];
            $user_password = $_POST['user_password'];

            if(!empty($user_password)) {
                $hased_password = password_hash($user_password, PASSWORD_BCRYPT, array('cost' => 12));
            }
            
            $query = "UPDATE users SET ";
            $query .= "user_firstname = '{$user_firstname}', ";
            $query .= "user_lastname = '{$user_lastname}', ";
            $query .= "username = '{$username}', ";
            $query .= "user_role = '{$user_role}', ";
            $query .= "user_email = '{$user_email}', ";
            $query .= "user_password = '{$hased_password}' ";
            $query .= "WHERE user_id = {$the_user_id} ";

            $edit_user_query = mysqli_query($connection, $query);

            // to check validation of the sql query 
            confirmQuery($edit_user_query);
        }
    }
    else {
        header("Location: index.php");
    }
?>

<!-- enctype="multipart/form-data" - important for sending file/image from a form using post method -->
<form action="" method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label for="title">Firstname</label>
        <input type="text" class="form-control" name="user_firstname" value="<?php echo $user_firstname; ?>">
    </div>

    <div class="form-group">
        <label for="title">Lastname</label>
        <input type="text" class="form-control" name="user_lastname" value="<?php echo $user_lastname; ?>">
    </div>

    <div class="form-group">
        <select name="user_role" id="">
            <option value='<?php echo $user_role; ?>'><?php echo $user_role; ?></option>
            <?php
                if($user_role == 'admin') {
                    echo "<option value='subscriber'>subscriber</option>";
                }
                else {
                    echo "<option value='admin'>admin</option>";
                }
            ?>
        </select>
    </div>

    <div class="form-group">
        <label for="post_author">username</label>
        <input type="text" class="form-control" name="username" value="<?php echo $username; ?>">
    </div>

    <div class="form-group">
        <label for="post_status">Email</label>
        <input type="email" class="form-control" name="user_email" value="<?php echo $user_email; ?>">
    </div>

    <div class="form-group">
        <label for="post_tags">Password</label>
        <input autocomplete="off" type="password" class="form-control" name="user_password">
    </div>

    <div class="form-group">
        <input class="btn btn-primary" type="submit" name="edit_user" value="Update User">
    </div>
</form>