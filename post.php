<?php ob_start(); ?>

<!-- header -->
<?php include 'includes/header.php'?>

<!-- Navigation -->
<?php include 'includes/navigation.php'?>

<?php 
    // catching POST request created using JS's AJAX call at the bottom of file
    if(isset($_POST['liked'])) {
        // echo "it worked";

        // fetching data from AJAX call
        $post_id = $_POST['post_id'];
        $user_id = $_POST['user_id'];
        
        // 1. Fetching the right post
        $searchPostQuery = "SELECT * FROM posts WHERE post_id=$post_id";
        $postResult =  mysqli_query($connection, $searchPostQuery);
        $post = mysqli_fetch_array($postResult);
        $likes = $post['likes'];

        // 2. Update post with likes (i.e. increment likes)
        mysqli_query($connection, "UPDATE posts SET likes=$likes+1 WHERE post_id=$post_id");

        // 3. Create likes for post
        mysqli_query($connection, "INSERT INTO likes (user_id, post_id) VALUES ($user_id, $post_id)");
        
        exit();
    }

    // catching POST request created using JS's AJAX call at the bottom of file
    if(isset($_POST['unliked'])) {
        // echo "it worked";

        // fetching data from AJAX call
        $post_id = $_POST['post_id'];
        $user_id = $_POST['user_id'];
        
        // 1. Fetching the right post
        $searchPostQuery = "SELECT * FROM posts WHERE post_id=$post_id";
        $postResult =  mysqli_query($connection, $searchPostQuery);
        $post = mysqli_fetch_array($postResult);
        $likes = $post['likes'];

        // 2. Update post with likes (i.e. decrement likes)
        mysqli_query($connection, "UPDATE posts SET likes=$likes-1 WHERE post_id=$post_id");

        // 3. Delete likes for post
        mysqli_query($connection, "DELETE FROM likes WHERE post_id=$post_id AND user_id=$user_id");
        
        exit();
    }
?>

<!-- Page Content -->
<div class="container">

    <div class="row">

        <!-- Blog Entries Column -->
        <div class="col-md-8">

            <?php

                if(isset($_GET['p_id'])) {
                    $the_post_id = $_GET['p_id'];

                    // update view count only when we visit or refresh the page
                    if($_SERVER['REQUEST_METHOD'] !== 'POST') {
                        // updating post view count
                        $view_query = "UPDATE posts SET post_views_count = post_views_count + 1 WHERE post_id = {$the_post_id}";
                        $send_query = mysqli_query($connection, $view_query);

                        if(!$send_query) {
                            die("query failed");
                        }
                    }

                    if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
                        $query = "SELECT * FROM posts WHERE post_id = $the_post_id ";
                    }
                    else {
                        $query = "SELECT * FROM posts WHERE post_id = $the_post_id AND post_status='published'";
                    }
                
                    $select_all_posts_query = mysqli_query($connection, $query); 

                    if(mysqli_num_rows($select_all_posts_query) < 1) {
                        echo "<h1 class='text-center'>No posts available 😅</h1>";
                    }
                    else {

                    while($row = mysqli_fetch_assoc($select_all_posts_query)) {
                        $post_title = $row['post_title'];
                        $post_author = $row['post_author'];
                        $post_author = $row['post_user'];
                        $post_date = $row['post_date'];
                        $post_image = $row['post_image'];
                        $post_content = $row['post_content'];
                        
                        ?>

            <h1 class="page-header">
                Posts
            </h1>

            <!-- First Blog Post -->
            <h2>
                <a href="#"><?php echo $post_title; ?></a>
            </h2>
            <p class="lead">
                by <a href="index.php"><?php echo $post_author; ?></a>
            </p>
            <p><span class="glyphicon glyphicon-time"></span><?php echo ' ' . $post_date; ?></p>
            <hr>
            <img class="img-responsive" src="images/<?php echo imagePlaceholder($post_image); ?>" alt="">
            <hr>
            <p><?php echo $post_content; ?></p>
            <hr>

            <?php if(isLoggedIn()) { ?>
            <div class="row">
                <p class="pull-right"><a class="<?php echo userLikedThisPost($the_post_id) ? 'unlike' : 'like' ?>"
                        href=""><span class="glyphicon glyphicon-thumbs-up" data-toggle='tooltip' data-placement='top'
                            title="<?php echo userLikedThisPost($the_post_id) ? 'I liked this before' : 'Want to like it?' ?>"></span>
                        <?php echo userLikedThisPost($the_post_id) ? 'Unlike' : 'Like' ?></a></p>
            </div>
            <?php } else { ?>
            <div class="row">
                <p class="pull-right login-to-post">You need to <a href="./login.php">Login</a> to like</p>
            </div>
            <?php } ?>
            <div class="row">
                <p class="pull-right likes">Like: <?php getPostLikes($the_post_id); ?></p>
            </div>

            <div class="clear-fix"></div>

            <?php } ?>

        </div>

        <!-- Blog Sidebar Widgets Column -->
        <?php include 'includes/sidebar.php'?>

    </div>
    <!-- /.row -->

    <hr>

    <!-- Blog Comments -->

    <?php
         if($_SERVER['REQUEST_METHOD'] === 'POST') {
            if(isset($_POST['create_comment'])) {

                $the_post_id = $_GET['p_id'];
                $comment_author = $_POST['comment_author'];
                $comment_email = $_POST['comment_email'];
                $comment_content = $_POST['comment_content'];

                if(!empty($comment_author) && !empty($comment_email) && !empty($comment_content)) {
                    $query = "INSERT INTO comments (comment_post_id, comment_author, comment_email, comment_content, comment_status, comment_date) ";
                    $query .= "VALUES ({$the_post_id}, '{$comment_author}', '{$comment_email}', '{$comment_content}', 'unapproved', now())";
        
                    $create_comment_query = mysqli_query($connection, $query);
                    if(!$create_comment_query) {
                        die('Query failded. ' . mysqli_error($connection));
                    }
                }
                else {
                    // js in php
                    echo "<script>alert('fields cannot be empty')</script>";
                }
            }

            // solving comment issue on refresh
            header("Location: post.php?p_id={$the_post_id}");
         }
    ?>

    <!-- Comments Form -->
    <div class="well">
        <h4>Leave a Comment:</h4>
        <form action="" method="post" role="form">

            <input type="hidden" value="<?php isset($the_post_id) ? $the_post_id : null; ?>">

            <div class="form-group">
                <label for="author">Author</label>
                <input type="text" class="form-control" name="comment_author">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" name="comment_email">
            </div>
            <div class="form-group">
                <label for="comment">Your Comment</label>
                <textarea class="form-control" name="comment_content" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" name="create_comment">Submit</button>
        </form>
    </div>
    <!-- Comments Form ends -->

    <hr>

    <!-- Posted Comments -->

    <?php
        $query = "SELECT * FROM comments WHERE comment_post_id = {$the_post_id} ";
        $query .= "AND comment_status = 'approve' ";
        // latest comment will be displayed at the top
        $query .= "ORDER BY comment_id DESC ";
        $select_comment_query = mysqli_query($connection, $query);

        if(!$select_comment_query) {
            die('Query failed. ' . mysqli_error($connection));
        }

        while($row = mysqli_fetch_assoc($select_comment_query)) {
            $comment_date = $row['comment_date'];
            $comment_content = $row['comment_content'];
            $comment_author = $row['comment_author'];

            ?>

    <!-- Comment -->
    <div class="media">
        <a class="pull-left" href="#">
            <img class="media-object" src="http://placehold.it/64x64" alt="">
        </a>
        <div class="media-body">
            <h4 class="media-heading"><?php echo $comment_author; ?>
                <small><?php echo $comment_date; ?></small>
            </h4>
            <?php echo $comment_content; ?>
        </div>
    </div>

    <?php } } }
        else {
            header("Location: index.php");
        } 
    ?>

    <!-- footer -->
    <?php include 'includes/footer.php'?>

    <!-- javascirpt for like functionality -->
    <script>
    $(document).ready(function() {
        // for hovering
        $("[data-toggle='tooltip']").tooltip();

        var post_id = <?php echo $the_post_id; ?>;
        var user_id = <?php echo loggedInUserId(); ?>;

        $('.like').click(function() {
            // creating POST request
            $.ajax({
                url: "./post.php?p_id=<?php echo $the_post_id?>",
                type: 'post',
                data: {
                    'liked': 1,
                    'post_id': post_id,
                    'user_id': user_id,
                }
            })
        })

        $('.unlike').click(function() {
            // creating POST request
            $.ajax({
                url: "./post.php?p_id=<?php echo $the_post_id?>",
                type: 'post',
                data: {
                    'unliked': 1,
                    'post_id': post_id,
                    'user_id': user_id,
                }
            })
        })
    })
    </script>