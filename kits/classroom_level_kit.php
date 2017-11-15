<?php
if(!isset($_COOKIE['ID_LooseInTheLab'])){
    header("Location: http://www.seriouslyfunnyscience.com/workshops/login.php");
}
/**
 * Created by IntelliJ IDEA.
 * User: amalyuchik
 * Date: 11/7/2017
 * Time: 4:42 PM
 */
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/globals.php");
//require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/data_processing/sql_functions.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/table_rows.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/classes/book_title.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/classes/classroom_kit_list_item.php");
//require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/classes/book_names_left_menu.php");
$book_title = null;
$kit_master_array = [];
$total_kit_price = 0;

if(!$conn)
    $conn = new connections();
if (!$select_sql)
    $select_sql = new select_sql();
$book_title = new book_title($connection_array,'',$_GET['book_id'],$conn,$select_sql);
$query_select = $select_sql->query("classroom_kit_list", null, null, $_GET['book_id']);
$sql_execute = $conn->runconn_sql_execute($connection_array, $query_select);
foreach ($sql_execute as $current_record)
{
    if(count($kit_master_array) == 0)
    {
        array_push($kit_master_array, new classroom_kit_list_item($current_record));
    }
    else
    {
        $last_item = $kit_master_array[count($kit_master_array)-1];
        if ( $last_item->product_id == $current_record['product_id'] ) //if previous item is the same as the new product
        {
            if ($current_record['reusable'] && $last_item->reusable_qty < $current_record['lab_product_line_classroom_qty'])
            {
                $last_item->reusable_qty = $current_record['lab_product_line_classroom_qty'];
                $last_item->product_reusable_price = $current_record['product_price']*$current_record['lab_product_line_classroom_qty'];
            }
            elseif (!$current_record['reusable'])
            {
                $last_item->consumable_qty += $current_record['lab_product_line_classroom_qty'];
                $last_item->product_consumable_price += $current_record['product_price'] * $current_record['lab_product_line_classroom_qty'];
            }
            $kit_master_array[count($kit_master_array)-1] = $last_item;
        }
        elseif (count($kit_master_array > 0) && $current_record['product_id'] != $last_item->product_id)
        {
            array_push($kit_master_array, new classroom_kit_list_item($current_record));
        }
    }
}


?>
<!DOCTYPE HTML>

<html>

<head>
	<META NAME="ROBOTS" CONTENT="NONE">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<?php echo $bootstrapLink;
			echo $jQueryLink;
            echo $fontAwesomeLink;?>
<link rel="stylesheet" href="../css/nav_style.css" />
<style>

</style>
<title><?php echo $book_title; ?> Classroom Kit List</title>
</head>

<body>
<div class="container">

    <section>
        <h1><?php echo $book_title; ?> Classroom Kit List</h1>
        <div class="row">
            <div class="col-lg-3 col-md-4 col-sm-5">


                <?php

                ?>

            </div>
            <div class="col-lg-9 col-md-8 col-sm-7">

                <article>
                    <div id="book_detail_result">
                        <table>
                        <?php
                        foreach ($kit_master_array as $item_object)
                        {
                            echo "<tr>";
//                            echo "<td>";
//                            echo $item_object->product_id;
//                            echo "</td>";
                            echo "<td>";
                            echo $item_object->product_sku;
                            echo "</td>";
                            echo "<td>";
                            echo $item_object->product_name;
                            echo "</td>";

                            echo "<td>";
                            echo $item_object->consumable_qty + $item_object->reusable_qty;
                            echo "</td><td>&nbsp;&nbsp;&nbsp;</td>";

                            echo "<td>";
                            if ($item_object->product_category_id != 6) {
                                $total = $item_object->product_reusable_price + $item_object->product_consumable_price;
                                echo money_format('%(#10n',$total);
                            }

                            echo "</td>";
                            echo "</tr>";
                            if ($item_object->product_category_id != 6)
                                $total_kit_price += $item_object->product_reusable_price + $item_object->product_consumable_price;
                        }
                        ?></table>

                        <?php
                        echo "<strong>". $total_kit_price . "</strong>"; ?>
                    </div> <!--Result of the Book_detail displays here-->

                </article>
            </div>
        </div>
    </section>
    <?php /** @var Site Navigation $navigation */
    //echo $navigation = new site_nav(); ?>

    <footer>
        <p><?php        echo $_SESSION['user']->user_first_name . " " . $_SESSION['user']->user_last_name . " is logged in as " . $_SESSION['user']->username . ".";    ?></p>    <p>Copyright 2017 Loose in the Lab</p>
        <div id="testing"></div>
    </footer>
</div>
</body>

</html>


