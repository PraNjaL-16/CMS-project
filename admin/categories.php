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
                        Welcome to admin
                        <small>Author</small>
                    </h1>
                </div>
            </div>
            <!-- /.row -->

            <!-- form -->
            <div class="col-xs-6">

                <?php
                    // create operation - CRUD
                    insert_categories();
                ?>

                <!-- form for creating new tag -->
                <form action="" method="post">
                    <div class="form-group">
                        <label for="cat-title">Add Category</label>
                        <input type="text" class="form-control" name="cat_title"></input>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" name="submit"></input>
                    </div>
                </form>
                <!-- end -->

                <?php
                    // update operation - CRUD
                    if(isset($_GET['edit'])) {
                        $cat_id = $_GET['edit'];

                        include "includes/update_categories.php";
                    }
                ?>
            </div>
            <!-- form end -->

            <!-- to display categories table -->
            <div class="col-xs-6">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Category Title</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            // read operation - CRUD
                            findAllCategories();
                        ?>
                    </tbody>
                </table>
            </div>
            <!-- table end -->

            <?php 
                // delete operation - CRUD
                deleteCategories();
            ?>

        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->

<?php include "includes/admin_footer.php"; ?>