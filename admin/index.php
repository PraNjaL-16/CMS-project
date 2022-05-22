<?php include "includes/admin_header.php"; ?>

<div id="wrapper">

    <!-- Navigation -->
    <?php include "includes/admin_navigation.php"; ?>

    <div id="page-wrapper">

        <div class="container-fluid">

            <!-- Page Heading -->
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">
                        <small>Role: <?php echo is_Admin($_SESSION['username']) ? 'Admin' : 'Subscriber'?></small>
                        Welcome to admin
                        <?php 
                            // retriving value from session using key
                            echo strtoupper(get_user_name());
                            ?>
                    </h1>
                </div>
            </div>
            <!-- /.row -->

            <!-- widgets -->
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-file-text fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <?php
                                        $post_count = countRecords(getAllUsersPosts());
                                        echo "<div class='huge'>{$post_count}</div>"
                                    ?>
                                    <div>Posts</div>
                                </div>
                            </div>
                        </div>
                        <a href="./posts.php">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-comments fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <?php
                                        $comment_count = countRecords(getAllPostsUsersComments());
                                        echo "<div class='huge'>{$comment_count}</div>"
                                    ?>
                                    <div>Comments</div>
                                </div>
                            </div>
                        </div>
                        <a href="./comments.php">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="panel panel-red">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-list fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <?php
                                        $category_count = countRecords(getAllUsersCategories());
                                        echo "<div class='huge'>{$category_count}</div>"
                                    ?>
                                    <div>Categories</div>
                                </div>
                            </div>
                        </div>
                        <a href="./categories.php">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <!-- widgets ends -->

            <?php
                $post_published_count = countRecords(getAllUsersPublishedPosts());

                $post_draft_count = countRecords(getAllUsersDraftPosts());

                $approved_comment_count = countRecords(getAllUserApprovedComments());

                $unapproved_comment_count = countRecords(getAllUserUnapprovedComments());
            ?>

            <!-- google charts -->
            <script type="text/javascript">
            google.charts.load('current', {
                'packages': ['bar']
            });
            google.charts.setOnLoadCallback(drawChart);

            function drawChart() {
                var data = google.visualization.arrayToDataTable([
                    ['Data', 'Count'],

                    <?php
                        $elements_text = ['All Posts','Active Posts', 'Draft Posts', 'Comments', 'Pending Comments', 'Approved Comments','Categories'];
                        $elements_count = [$post_count, $post_published_count, $post_draft_count, $comment_count, $unapproved_comment_count, $approved_comment_count, $category_count];

                        for($i = 0; $i < 7; $i++) {
                            echo "['{$elements_text[$i]}'" . "," . "{$elements_count[$i]}],";
                        }
                    ?>


                ]);

                var options = {
                    chart: {
                        title: '',
                        subtitle: '',
                    }
                };

                var chart = new google.charts.Bar(document.getElementById('columnchart_material'));

                chart.draw(data, google.charts.Bar.convertOptions(options));
            }
            </script>
            <div id="columnchart_material" style="width: 'auto'; height: 500px;"></div>
            <!-- google charts end -->

        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->

<?php include "includes/admin_footer.php"; ?>