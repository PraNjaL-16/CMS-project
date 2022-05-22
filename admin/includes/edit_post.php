<?php

    if(isset($_GET['p_id'])) {
        $the_post_id = $_GET['p_id'];

        $query = "SELECT * FROM posts WHERE post_id=$the_post_id";
        $select_posts_by_id = mysqli_query($connection, $query); 

        while($row = mysqli_fetch_assoc($select_posts_by_id)) {
            $post_id = $row['post_id'];
            $post_user = $row['post_user'];
            $post_title = $row['post_title'];
            $post_category_id = $row['post_category_id'];
            $post_status = $row['post_status'];
            $post_image = $row['post_image'];
            $post_content = $row['post_content'];
            $post_tags = $row['post_tags'];
            $post_comment_count = $row['post_comment_count'];
            $post_date = $row['post_date'];
        }
    }

    if(isset($_POST['update_post'])) {
        $post_title = $_POST['post_title'];
        $post_user = $_POST['post_user'];
        $post_category_id = $_POST['post_category'];
        $post_status = $_POST['post_status'];

        //***** SPECIAL HADNDLING OF IMAGES *****//
        // $post_image - will be the real name of the image
        $post_image = $_FILES['image']['name'];
        // echo $post_image . '<br>';
        // $post_image_temp - is the temporary location on the server at wich the image get stored when we upload it before the final submission 
        $post_image_temp = $_FILES['image']['tmp_name'];
        // echo $post_image_temp;

        // will move image from temporary location to final location in our project directory
        move_uploaded_file($post_image_temp, "../images/$post_image");

        // if "$post_image" variable is empty
        if(empty($post_image)) {
            $query = "SELECT * FROM posts WHERE post_id=$the_post_id ";
            $select_image = mysqli_query($connection, $query);
            confirmQuery($select_image);

            while($row = mysqli_fetch_assoc($select_image)) {
                $post_image = $row['post_image'];
            }
        }
        //***** ENDS *****//

        $post_tags = $_POST['post_tags'];
        $post_content = $_POST['post_content'];

        $query = "UPDATE posts SET ";
        $query .= "post_title = '{$post_title}', ";
        $query .= "post_category_id = '{$post_category_id}', ";
        $query .= "post_date = now(), ";
        $query .= "post_user = '{$post_user}', ";
        $query .= "post_status = '{$post_status}', ";
        $query .= "post_tags = '{$post_tags}', ";
        $query .= "post_content = '{$post_content}', ";
        $query .= "post_image = '{$post_image}' ";
        $query .= "WHERE post_id = {$the_post_id} ";

        $update_post = mysqli_query($connection, $query);

        // to check validation of the sql query 
        confirmQuery($update_post);

        echo "<p class='bg-success'>Post Updated. <a href='../post.php?p_id={$the_post_id}'>View Post</a> or <a href='posts.php'>Edit more posts</a></p>";
    }
?>


<!-- enctype="multipart/form-data" - important for sending file/image from a form using post method -->
<form action="" method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label for="title">Post Title</label>
        <input value="<?php echo $post_title; ?>" type="text" class="form-control" name="post_title">
    </div>

    <div class="form-group">
        <label for="post_category">Category</label>
        <select name="post_category" id="">
            <?php
                // getting all the categories from DB dynamically
                $query = "SELECT * FROM categories";
                $select_categories = mysqli_query($connection, $query); 
                confirmQuery($select_categories);
                
                while($row = mysqli_fetch_assoc($select_categories)) {
                    $cat_id = $row['cat_id'];
                    $cat_title = $row['cat_title'];

                    if($cat_id == $post_category_id) {
                        echo "<option selected value='{$cat_id}'>{$cat_title}</option>";
                    }
                    else {
                        echo "<option value='{$cat_id}'>{$cat_title}</option>";
                    }
                }
            ?>
        </select>
    </div>

    <!-- <div class="form-group">
        <label for="post_user">Post Author</label>
        <input value="<?php // echo $post_user; ?>" type="text" class="form-control" name="post_user">
    </div> -->

    <div class="form-group">
        <label for="post_user">Users</label>
        <select name="post_user" id="">
            <?php
                // getting all the categories from DB dynamically
                $user_query = "SELECT * FROM users";
                $select_users = mysqli_query($connection, $user_query); 
                confirmQuery($select_users);

                echo "<option value='{$post_user}'>{$post_user}</option>";
                
                while($row = mysqli_fetch_assoc($select_users)) {
                    $user_id = $row['user_id'];
                    $user_name = $row['username'];

                    echo "<option value='{$user_name}'>{$user_name}</option>";
                }
            ?>
        </select>
    </div>

    <?php // echo($post_status); ?>
    <div class="form-group">
        <select name="post_status" id="">
            <option value="<?php echo $post_status ?>"><?php echo $post_status;?></option>

            <?php
            if($post_status == 'published') {
                echo "<option value='draft'>Draft</option>";
            }
            else {
                echo "<option value='published'>Published</option>";
            }
            ?>
        </select>
    </div>

    <div class="form-group">
        <label for="post_image">Post Image</label>
        <br>
        <!-- to display image by getting image name from the database-->
        <img width="100" src=' ../images/<?php echo $post_image; ?>' alt="image">
        <!-- type="file" - to send image -->
        <input type="file" class="form-control" name="image">
    </div>

    <div class="form-group">
        <label for="post_tags">Post Tags</label>
        <input value="<?php echo $post_tags; ?>" type="text" class="form-control" name="post_tags">
    </div>

    <div class="form-group">
        <label for="summernote">Post Content</label>
        <textarea class="form-control" name="post_content" id="summernote" cols="30" _$_POSTs="10"><?php echo str_replace('\r\n' ,'</br>', $post_content); ?>
        </textarea>
    </div>

    <div class="form-group">
        <input class="btn btn-primary" type="submit" name="update_post" value="Publish Post">
    </div>
</form>