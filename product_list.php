<?php
/**
 * Created by IntelliJ IDEA.
 * User: Andrei
 * Date: 7/28/2017
 * Time: 11:39 PM
 */

require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/globals.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/sql_functions.php");
?>
<!DOCTYPE HTML>

<html>

<head>
	<META NAME="ROBOTS" CONTENT="NONE">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<?php echo $bootstrapLink;
			echo $jQueryLink;
            echo $fontAwesomeLink;?>
<link rel="stylesheet" href="css/nav_style.css" />
<style>

</style>
<title>Books</title>
<script>
</script>
</head>

<body>
<div class="container">
    <section>
        <div class="col-lg-3 col-md-4 col-sm-5">
            <header>
                <strong>Choose product </strong>
            </header>
        <?php $select_field_categories = new select_input($global_product_categories_list,'Category','product_category_id',$product_category_id);
        echo $select_field_categories->create_select_field(false);?>&nbsp;

        </div>
    </section>
    <?php $navigation = new site_nav(); ?>

    <footer>
        <p>Copyright 2017 ASM</p>
        <div id="testing"></div>
    </footer>
</div>