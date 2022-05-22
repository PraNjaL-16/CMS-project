<?php
    if(isset($_POST['create_post'])) {
        $post_title = $_POST['title'];
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
        //***** ENDS *****//

        $post_tags = $_POST['post_tags'];
        $post_content = $_POST['post_content'];
        $post_date = date('d-m-y');

        $user_id = loggedInUserId();

        $query = "INSERT INTO posts (post_category_id, user_id, post_title, post_user, post_date, post_image, post_content, post_tags, post_status) ";
        $query .= "VALUES ({$post_category_id}, {$user_id},'{$post_title}', '{$post_user}', now(), '{$post_image}','{$post_content}', '{$post_tags}', '{$post_status}' )";

        $create_post_query = mysqli_query($connection, $query);

        // to check validation of the sql query 
        confirmQuery($create_post_query);

        // will fetch out id of last created post from posts table
        $the_post_id = mysqli_insert_id($connection);

        echo "<p class='bg-success'>Post Created. <a href='../post.php?p_id={$the_post_id}'>View Post</a> or <a href='posts.php'>Edit more posts</a></p>";
    }
?>


<!-- enctype="multipart/form-data" - important for sending file/image from a form using post method -->
<form action="" method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label for="title">Post Title</label>
        <input type="text" class="form-control" name="title">
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

                    echo "<option value='{$cat_id}'>{$cat_title}</option>";
                }
            ?>
        </select>
    </div>

    <!-- <div class="form-group">
        <label for="post_user">Post Author</label>
        <input type="text" class="form-control" name="author">
    </div> -->

    <div class="form-group">
        <label for="post_user">Users</label>
        <select name="post_user" id="">
            <?php
                // getting all the categories from DB dynamically
                $user_query = "SELECT * FROM users";
                $select_users = mysqli_query($connection, $user_query); 
                confirmQuery($select_users);
                
                while($row = mysqli_fetch_assoc($select_users)) {
                    $user_id = $row['user_id'];
                    $user_name = $row['username'];

                    echo "<option value='{$user_name}'>{$user_name}</option>";
                }
            ?>
        </select>
    </div>

    <!-- <div class="form-group">
        <label for="post_status">Post Status</label>
        <input type="text" class="form-control" name="post_status">
    </div> -->

    <div class="form-group">
        <select name="post_status" id="">
            <option value="draft">Post Status</Options>
            <option value="published">Published</Options>
            <option value="draft">Draft</Options>
        </select>
    </div>

    <div class="form-group">
        <label for="post_image">Post Image</label>
        <!-- type="file" - to send image -->
        <input type="file" class="form-control" name="image">
    </div>

    <div class="form-group">
        <label for="post_tags">Post Tags</label>
        <input type="text" class="form-control" name="post_tags">
    </div>

    <div class="form-group">
        <label for="summernote">Post Content</label>
        <textarea class="form-control" name="post_content" id="summernote" cols="30" _$_POSTs="10"></textarea>
    </div>

    <div class="form-group">
        <input class="btn btn-primary" type="submit" name="create_post" value="Publish Post">
    </div>
</form>