<?php
/**
 * Created by IntelliJ IDEA.
 * User: Andrei
 * Date: 7/28/2017
 * Time: 11:39 PM
 */

require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/globals.php");
//require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/sql_functions.php");
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
<title>Product List</title>
    <script>
        function selected()
        {
            var e = document.getElementById("product_category_id");
            var selectedProdCat = e.options[e.selectedIndex].value;
            var selectedProdName = e.options[e.selectedIndex].text;
            if(selectedProdCat !== '')
            {
                $.get("includes/validate.php?id=".concat(selectedProdCat, "&category_name=", selectedProdName), function (data) {
                    //alert(data);
                    $('#product_list_result').html(data);
                });
            }
        }
    </script>
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
            echo $select_field_categories->create_select_field(true);
            echo "<div class=\"col-lg-10\"><input style=\"display:none;\" id=\"submit\" type=\"submit\" value=\"Submit\"></div>";?>&nbsp;

        </div>
        <div class="col-lg-9 col-md-8 col-sm-7">
            <article>
                <div id="product_list_result"></div> <!--Result of the product_list displays here-->
                <?php
                //                if ($_GET['book_id'] !== null)
                //                {
                //                    $book_contents = new book_detail($connection_array, $_GET['Book Name'],$_GET['book_id'],$conn,$select_sql,'','',$global_lab_names_list);
                //                }
                ?>
            </article>
        </div>
    </section>
    <?php $navigation = new site_nav(); ?>

    <footer>
        <p>Copyright 2017 ASM</p>
        <div id="testing"></div>
    </footer>
</div>