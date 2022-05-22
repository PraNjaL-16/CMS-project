<?php  include "includes/header.php"; ?>

<?php
    echo loggedInUserId();

    if(userLikedThisPost(2)) {
        echo 'USER LIKED IT';
    }
    else {
        echo "NOT LIKED";
    }
?>