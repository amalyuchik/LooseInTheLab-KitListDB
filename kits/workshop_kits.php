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
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/classes/participant_kit_list_item.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/classes/presenter_kit_list_item.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/classes/retail_kit_list_item.php");
//require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/classes/book_names_left_menu.php");
$book_title = null;
$kit_master_array_participant = [];
$kit_master_array_presenter = [];
$kit_master_array_retail = [];


if(!$conn)
    $conn = new connections();
if (!$select_sql)
    $select_sql = new select_sql();
$book_title = new book_title($connection_array,'',$_GET['book_id'],$conn,$select_sql);
$query_select_participant = $select_sql->query("participant_kit_list", null, null, $_GET['book_id']);
$query_select_presenter = $select_sql->query("presenter_kit_list", null, null, $_GET['book_id']);
$query_select_retail = $select_sql->query("retail_kit_list", null, null, $_GET['book_id']);
$sql_execute_participant = $conn->runconn_sql_execute($connection_array, $query_select_participant);
$sql_execute_presenter = $conn->runconn_sql_execute($connection_array, $query_select_presenter);
$sql_execute_retail = $conn->runconn_sql_execute($connection_array, $query_select_retail);

//Participant
foreach ($sql_execute_participant as $current_record_participant)
{
    if(count($kit_master_array_participant) == 0)
    {
        array_push($kit_master_array_participant, new participant_kit_list_item($current_record_participant));
    }
    else
    {
        $last_item = $kit_master_array_participant[count($kit_master_array_participant)-1];
        if ( $last_item->product_id == $current_record_participant['product_id'] ) //if previous item is the same as the new product
        {
            if ($current_record_participant['reusable'] && $last_item->reusable_qty < $current_record_participant['lab_product_line_participant_qty'])
            {
                $last_item->reusable_qty = $current_record_participant['lab_product_line_participant_qty'];
                $last_item->product_reusable_price = $current_record_participant['product_price']*$current_record_participant['lab_product_line_participant_qty'];
            }
            elseif (!$current_record_participant['reusable'])
            {
                $last_item->consumable_qty += $current_record_participant['lab_product_line_participant_qty'];
                $last_item->product_consumable_price += $current_record_participant['product_price'] * $current_record_participant['lab_product_line_participant_qty'];
            }
            $kit_master_array_participant[count($kit_master_array_participant)-1] = $last_item;
        }
        elseif (count($kit_master_array_participant > 0) && $current_record_participant['product_id'] != $last_item->product_id)
        {
            array_push($kit_master_array_participant, new participant_kit_list_item($current_record_participant));
        }
    }
}

//Presenter
foreach ($sql_execute_presenter as $current_record_presenter)
{
    if(count($kit_master_array_presenter) == 0)
    {
        array_push($kit_master_array_presenter, new presenter_kit_list_item($current_record_presenter));
    }
    else
    {
        $last_item = $kit_master_array_presenter[count($kit_master_array_presenter)-1];
        if ( $last_item->product_id == $current_record_presenter['product_id'] ) //if previous item is the same as the new product
        {
            if ($current_record_presenter['reusable'] && $last_item->reusable_qty < $current_record_presenter['lab_product_line_presenter_qty'])
            {
                $last_item->reusable_qty = $current_record_presenter['lab_product_line_presenter_qty'];
                $last_item->product_reusable_price = $current_record_presenter['product_price']*$current_record_presenter['lab_product_line_presenter_qty'];
            }
            elseif (!$current_record_presenter['reusable'])
            {
                $last_item->consumable_qty += $current_record_presenter['lab_product_line_presenter_qty'];
                $last_item->product_consumable_price += $current_record_presenter['product_price'] * $current_record_presenter['lab_product_line_presenter_qty'];
            }
            $kit_master_array_presenter[count($kit_master_array_presenter)-1] = $last_item;
        }
        elseif (count($kit_master_array_presenter > 0) && $current_record_presenter['product_id'] != $last_item->product_id)
        {
            array_push($kit_master_array_presenter, new presenter_kit_list_item($current_record_presenter));
        }
    }
}
//Retail
foreach ($sql_execute_retail as $current_record_retail)
{
    if(count($kit_master_array_retail) == 0)
    {
        array_push($kit_master_array_retail, new retail_kit_list_item($current_record_retail));
    }
    else
    {
        $last_item = $kit_master_array_retail[count($kit_master_array_retail)-1];
        if ( $last_item->product_id == $current_record_retail['product_id'] ) //if previous item is the same as the new product
        {
            if ($current_record_retail['reusable'] && $last_item->reusable_qty < $current_record_retail['lab_product_line_retail_qty'])
            {
                $last_item->reusable_qty = $current_record_retail['lab_product_line_retail_qty'];
                $last_item->product_reusable_price = $current_record_retail['product_price']*$current_record_retail['lab_product_line_retail_qty'];
            }
            elseif (!$current_record_retail['reusable'])
            {
                $last_item->consumable_qty += $current_record_retail['lab_product_line_retail_qty'];
                $last_item->product_consumable_price += $current_record_retail['product_price'] * $current_record_retail['lab_product_line_retail_qty'];
            }
            $kit_master_array_retail[count($kit_master_array_retail)-1] = $last_item;
        }
        elseif (count($kit_master_array_retail > 0) && $current_record_retail['product_id'] != $last_item->product_id)
        {
            array_push($kit_master_array_retail, new retail_kit_list_item($current_record_retail));
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
    <title><?php echo $book_title; ?> Workshop Kit List</title>
</head>

<body>
<div class="container">

    <section>
        <h1><?php echo $book_title; ?> Workshop Kit Lists</h1>
        <div class="row">
<!--            <div class="col-lg-3 col-md-4 col-sm-5">-->

<!--            </div>-->
            <div class="col-lg-9 col-md-8 col-sm-7">

                <article>
                    <div id="book_detail_result">

                        <?php
                        echo "<h2>Participant Kit List</h2>";
                        $product_category_while_loop = '';
                        $total_kit_price = 0;
                        foreach ($kit_master_array_participant as $item_object)
                        {
                            if($product_category_while_loop !='' && $product_category_while_loop != $item_object->product_category)
                                echo "</table>";
                            if($product_category_while_loop != $item_object->product_category)
                            {
                                $product_category_while_loop = $item_object->product_category;
                                echo "<h3>".$product_category_while_loop."</h3><table width=\"680px\">";
                            }

                            echo "<tr>";
//                            echo "<td>";
//                            echo $item_object->product_id;
//                            echo "</td>";
                            if ($item_object->product_category_id != 6)
                            {
                                echo "<td>";
                                echo $item_object->product_sku;
                                echo "</td>";
                            }
                            echo "<td width=\"440px\">";
                            echo $item_object->product_name;
                            echo "</td>";

                            echo "<td>";
                            echo $item_object->consumable_qty + $item_object->reusable_qty;
                            echo "</td><td>&nbsp;&nbsp;&nbsp;</td>";

                            echo "<td>";
                            if ($item_object->product_category_id != 6) {
                                $total = $item_object->product_reusable_price + $item_object->product_consumable_price;
                                echo money_format('%(#10n', $total);
                            }

                            echo "</td>";
                            echo "</tr>";

                            if ($item_object->product_category_id != 6)
                                $total_kit_price += $item_object->product_reusable_price + $item_object->product_consumable_price;
                        }
                        echo "</table>";
                        echo "<strong>". $total_kit_price . "</strong><hr />";

                        //Presenter's kit
                        echo "<h2>Presenter Kit List</h2>";
                        $product_category_while_loop = '';
                        $total_kit_price = 0;
                        foreach ($kit_master_array_presenter as $item_object)
                        {
                            if($product_category_while_loop !='' && $product_category_while_loop != $item_object->product_category)
                                echo "</table>";
                            if($product_category_while_loop != $item_object->product_category)
                            {
                                $product_category_while_loop = $item_object->product_category;
                                echo "<h3>".$product_category_while_loop."</h3><table width=\"680px\">";
                            }

                            echo "<tr>";
//                            echo "<td>";
//                            echo $item_object->product_id;
//                            echo "</td>";
                            if ($item_object->product_category_id != 6)
                            {
                                echo "<td>";
                                echo $item_object->product_sku;
                                echo "</td>";
                            }
                            echo "<td width=\"440px\">";
                            echo $item_object->product_name;
                            echo "</td>";

                            echo "<td>";
                            echo $item_object->consumable_qty + $item_object->reusable_qty;
                            echo "</td><td>&nbsp;&nbsp;&nbsp;</td>";

                            echo "<td width=\"80px\">";
                            echo $item_object->product_qty_detail;
                            echo "</td>";

                            echo "<td>";
                            if ($item_object->product_category_id != 6) {
                                $total = $item_object->product_reusable_price + $item_object->product_consumable_price;
                                echo money_format('%(#10n', $total);
                            }

                            echo "</td>";
                            echo "</tr>";

                            if ($item_object->product_category_id != 6)
                                $total_kit_price += $item_object->product_reusable_price + $item_object->product_consumable_price;
                        }
                        echo "</table>";
                        echo "<strong>". $total_kit_price . "</strong><hr />";

                        //Retail Kit
                        echo "<h2>Retail Kit List</h2>";
                        $product_category_while_loop = '';
                        $total_kit_price = 0;
                        foreach ($kit_master_array_retail as $item_object)
                        {
                            if($product_category_while_loop !='' && $product_category_while_loop != $item_object->product_category)
                                echo "</table>";
                            if($product_category_while_loop != $item_object->product_category)
                            {
                                $product_category_while_loop = $item_object->product_category;
                                echo "<h3>".$product_category_while_loop."</h3><table width=\"680px\">";
                            }

                            echo "<tr>";
//                            echo "<td>";
//                            echo $item_object->product_id;
//                            echo "</td>";
                            if ($item_object->product_category_id != 6)
                            {
                                echo "<td>";
                                echo $item_object->product_sku;
                                echo "</td>";
                            }
                            echo "<td width=\"440px\">";
                            echo $item_object->product_name;
                            echo "</td>";

                            echo "<td>";
                            echo $item_object->consumable_qty + $item_object->reusable_qty;
                            echo "</td><td>&nbsp;&nbsp;&nbsp;</td>";

                            echo "<td width=\"80px\">";
                            echo $item_object->product_qty_detail;
                            echo "</td>";

                            echo "<td>";
                            if ($item_object->product_category_id != 6) {
                                $total = $item_object->product_reusable_price + $item_object->product_consumable_price;
                                echo money_format('%(#10n', $total);
                            }

                            echo "</td>";
                            echo "</tr>";

                            if ($item_object->product_category_id != 6)
                                $total_kit_price += $item_object->product_reusable_price + $item_object->product_consumable_price;
                        }
                        echo "</table>";
                        echo "<strong>". $total_kit_price . "</strong>";
                        //var_dump($kit_master_array_retail);
                        ?>

                        <?php
                         ?>

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


