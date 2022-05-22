<?php include "includes/admin_header.php"; ?>

<?php
    if(isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
        
        $query = "SELECT * FROM users WHERE username = '{$username}' ";
        $select_user_profile_query = mysqli_query($connection, $query);
        confirmQuery($select_user_profile_query);

        while($row = mysqli_fetch_assoc($select_user_profile_query)) {
            $username = $row['username'];
            $user_firstname = $row['user_firstname'];
            $user_lastname = $row['user_lastname'];
            $user_email = $row['user_email'];
        }
    }

   if(isset($_POST['edit_user'])) {
        $user_firstname = $_POST['user_firstname'];
        $user_lastname = $_POST['user_lastname'];
        $username = $_POST['username'];
        $user_email = $_POST['user_email'];
        $user_password = $_POST['user_password'];
        
        $query = "UPDATE users SET ";
        $query .= "user_firstname = '{$user_firstname}', ";
        $query .= "user_lastname = '{$user_lastname}', ";
        $query .= "username = '{$username}', ";
        $query .= "user_email = '{$user_email}', ";
        $query .= "user_password = '{$user_password}' ";
        $query .= "WHERE username = '{$username}' ";

        $edit_user_query = mysqli_query($connection, $query);

        // to check validation of the sql query 
        confirmQuery($edit_user_query);
    }
?>

<div id="wrapper">

    <!-- Navigation -->
    <?php include "includes/admin_navigation.php"; ?>

    <div id="page-wrapper">

        <div class="container-fluid">

            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">
                        Welcome to admin
                        <small>Author</small>
                    </h1>

                </div>

                <!-- form -->
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">Firstname</label>
                        <input type="text" class="form-control" name="user_firstname"
                            value="<?php echo $user_firstname; ?>">
                    </div>

                    <div class="form-group">
                        <label for="title">Lastname</label>
                        <input type="text" class="form-control" name="user_lastname"
                            value="<?php echo $user_lastname; ?>">
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
                        <input class="btn btn-primary" type="submit" name="edit_user" value="Update Profile">
                    </div>
                </form>
                <!-- form ends -->
            </div>

        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->

<?php include "includes/admin_footer.php"; ?>