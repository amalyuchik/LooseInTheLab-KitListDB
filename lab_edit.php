<?php
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/globals.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/add_lab_product.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/create_lab_product_line_field_values.php");

//$lab_id = $_GET['lab_id'];
$product_id=$_GET['product_id'];
/**
 * Created by PhpStorm.
 * User: sportypants
 * Date: 4/15/17
 * Time: 3:15 AM
 */


if(isset($_POST['product_id'])) //ASM This adds a new product to the lab
{
    $add_product_conn = new connections();
    $select_sql = new select_sql();
    $query_product_category = $select_sql->edit_query("product_category", $_POST['product_id']);
    //echo $query_product_category;
    $query_product_category_result = $add_product_conn->runconn_sql_execute($connection_array, $query_product_category);

    $create_field_values_arr = new create_lab_product_line_field_values($_POST['lab_id'], $query_product_category_result[0]['data'], $_POST['lab_product_line_product_category_id'], $_POST['product_id'], $_POST['classroom_qty_product_add'], $_POST['participant_qty_product_add'], $_POST['presenter_qty_product_add'], $_POST['refill_qty_product_add'], $_POST['refill_qty_detail_product_add'], $_POST['retail_qty_product_add'], $_POST['retail_qty_detail_product_add'], $_POST['product_reusable_product_add'], $current_date, $current_date);
    $add_product_query = $add_product_conn->query_construct('create',array('lab_product_line'),$create_field_values_arr);
    $add_product_query_execute = $add_product_conn->runconn_sql_execute($connection_array, $add_product_query);

    $add_product_conn = null;
    $select_sql = null;
}
if(isset($_GET['delete']))
{
    $delete_conn = new connections();
    $delete_query = $delete_conn->query_construct('delete',array('lab_product_line'),array('lab_product_line_id'=> $_GET['record_id']));
    $delete_query_execute = $delete_conn->runconn_sql_execute($connection_array,$delete_query);
}

if(isset($_GET['ed_re']))
{
    $update_conn = new connections();
    $edit_query = $update_conn->query_construct('update',array('lab_product_line'),array('lab_product_line_id'=> $_GET['record_id'],'lab_product_line_is_reusable' => !$_GET['ed_re']));
    $edit_query_execute = $update_conn->runconn_sql_execute($connection_array,$edit_query);
}

if($conn == null)
    $conn = new connections();

if($select_sql == null)
    $select_sql = new select_sql();
$product_ids='';
if(isset($lab_id))
{
    $query_lab = $select_sql->edit_query("lab", $lab_id);
    $edit_lab_result = $conn->runconn_sql_execute($connection_array, $query_lab);

    //Collect IDs of all the products associated with a current lab for an IN() clause
    $query_lab_line_ids = $select_sql->edit_query("lab_product_ids", $lab_id);
    $lab_line_ids_result = $conn->runconn_sql_execute($connection_array, $query_lab_line_ids);

    foreach($lab_line_ids_result as $arr)
    {
        $product_ids .= $arr['lab_product_line_product_id_fk'].',';
    }
    $product_ids = substr($product_ids,0,strlen($product_ids)-1);

    //Get all the products for the Edit screen
    if(strlen($product_ids) > 1)
    {
        $query_lab_products = $select_sql->edit_query("lab_products", $lab_id, $product_ids);

        $lab_line_products_result = $conn->runconn_sql_execute($connection_array, $query_lab_products);
    }
    //echo var_export($lab_line_products_result,true);
}
//echo var_export($edit_lab_result,true);
?>
<!DOCTYPE html>
<html>
<head>
    <META NAME="ROBOTS" CONTENT="NONE">
    <?php echo $bootstrapLink; echo "\r\n";?>
    <?php echo $fontAwesomeLink; echo "\r\n"; ?>
    <?php echo $jQueryLink; echo "\r\n"; ?>
    <link rel="stylesheet" href="css/nav_style.css" />
<style>
    td{
        padding-left: 10px;
    }th{
        padding-left: 10px;
    }
</style>
    <title><?php echo $edit_lab_result[0]['lab_name']; ?> | Edit</title>

</head>
<body>
<div class="container">
    <div class="row">
        <section>
<h1><?php echo $edit_lab_result[0]['lab_name']; ?></h1>
<form class="form-inline" action="<?php $thispage ?>" method="post">
    <label>Lab Name: &nbsp;</label>
    <input type="text" size="60" name="lab_name" value="<?php echo $edit_lab_result[0]['lab_name']; ?>"><br /><br />

    <?php
    $select_field_types = new select_input($global_lab_types_list,'Types','lab_type',$edit_lab_result[0]['lab_type_id_fk']);
    echo $select_field_types->create_select_field(false);
    $select_field_categories = new select_input($global_lab_categories_list,'Categories','lab_category',$edit_lab_result[0]['lab_category_id_fk']);
    echo $select_field_categories->create_select_field(false);
    ?>
<br /><br />
    <label>Video Link: &nbsp;</label>
    <input type="text" size="60" name="video_link" maxlength="255" value="<?php echo $edit_lab_result[0]['lab_video_link']; ?>">
    <input type="submit"  value="Save" >
</form><br /><br />
<form  action="<?php $thispage ?>" method="post" name="product">
<table style="table-layout: inherit; width: 100%;">


    <?php
    if(count($lab_line_products_result) > 0)
    {
        echo "<tr>
        <th>Product Name</th>
        <th>Category</th>
        <th>Class<br />Qty</th>
        <th>Grade<br />Refill<br />Qty</th>
        <th>Grade Qty Detail</th>
        <th>Participant<br />Qty</th>
        <th>Presenter<br />Qty</th>
        <th>Retail<br />Qty</th>
        <th>Retail Qty Detail</th>
        <th>Reusable</th>
        <th>Price</th>
        </tr>";

        foreach($lab_line_products_result as $k=>$v)
        {
            $recId = $v['lab_product_line_id'];
            echo "<tr id=\"row".$recId."\">";
            echo "<td><p style='width: 200px;'>";
            echo "<a href=\"".$base_url."kit_db/product_edit.php?product_id=";
            echo $v['lab_product_line_product_id_fk']."\">".$v['product_name']."</a>";
            echo "</p></td>";

            echo "<td><p style='width: 75px;'>";
            echo $v['lab_product_line_product_category'];
            echo "</p></td>";

            echo "<td><p>";
            echo "<input type=\"hidden\" id=\"product_line_id".$recId."\" name=\"product_line_id".$recId."\" value=".$v['lab_product_line_id']."><input type=\"hidden\" id=\"product_id".$recId."\" name=\"product_id".$recId."\" value=".$v['lab_product_line_product_id_fk']."><input type=\"hidden\" id=\"lab_id".$recId."\" name=\"lab_id".$recId."\" value=".$lab_id."><input type=\"text\" size=\"5\" onchange=\"onrowchange(classroom_qty".$recId.");\" id=\"classroom_qty".$recId."\" name=\"classroom_qty".$recId."\" maxlength=\"255\" value=". $v['lab_product_line_classroom_qty'] .">";
            echo "</p></td>";

            echo "<td><p>";
            echo "<input type=\"text\" size=\"5\" onchange=\"onrowchange(refill_qty".$recId.");\" id=\"refill_qty".$recId."\" name=\"refill_qty".$recId."\" maxlength=\"255\" value=". $v['lab_product_line_refill_qty'] .">";
            echo "</p></td>";

            echo "<td><p>";
            echo "<input type=\"text\" size=\"12\" onchange=\"onrowchange(refill_qty_detail".$recId.");\" id=\"refill_qty_detail".$recId."\" name=\"refill_qty_detail".$recId."\" maxlength=\"255\" value=". $v['lab_product_line_refill_qty_detail'] .">";
            echo "</p></td>";

            echo "<td><p>";
            echo "<input type=\"text\" size=\"5\" onchange=\"onrowchange(participant_qty".$recId.");\" id=\"participant_qty".$recId."\" name=\"participant_qty".$recId."\" maxlength=\"255\" value=". $v['lab_product_line_participant_qty'] .">";
            echo "</p></td>";

            echo "<td><p>";
            echo "<input type=\"text\" size=\"5\" onchange=\"onrowchange(presenter_qty".$recId.");\" id=\"presenter_qty".$recId."\" name=\"presenter_qty".$recId."\" maxlength=\"255\" value=". $v['lab_product_line_presenter_qty'] .">";
            echo "</p></td>";

            echo "<td><p>";
            echo "<input type=\"text\" size=\"5\" onchange=\"onrowchange(retail_qty".$recId.");\" id=\"retail_qty".$recId."\" name=\"retail_qty".$recId."\" maxlength=\"255\" value=". $v['lab_product_line_retail_qty'] .">";
            echo "</p></td>";

            echo "<td><p>";
            echo "<input type=\"text\" size=\"12\" onchange=\"onrowchange(retail_qty_detail".$recId.");\" id=\"retail_qty_detail".$recId."\" name=\"retail_qty_detail".$recId."\" maxlength=\"255\" value=". $v['lab_product_line_retail_qty_detail'] .">";
            echo "</p></td>";

            echo "<td><p>";
            if($v['lab_product_line_is_reusable'])
                echo "<a href=\"". $thispage . "?lab_id=".$lab_id."&record_id=".$v['lab_product_line_id']."&ed_re=".$v['lab_product_line_is_reusable']."\" ><img src=\"img/switch_true.png\" height=\"25\"></a>";//<i style=\"color: green;\" class=\"fa fa-check\" aria-hidden=\"true\"></i>
            else
                echo "<a href=\"". $thispage . "?lab_id=".$lab_id."&record_id=".$v['lab_product_line_id']."&ed_re=".$v['lab_product_line_is_reusable']."\" ><img src=\"img/switch_false.png\" height=\"25\"></a>";//<i style=\"color: red;\" class=\"fa fa-times\" aria-hidden=\"true\"></i>
            echo "</p></td>";

            echo "<td><p>$";
            echo $v['product_price'];
            echo "</p></td>";

            echo "<td><p style=\"width: 50px;padding-right:0px;\">";
            //echo "<input type=\"submit\"  value=\"Save\" >";
            //echo "<a href=\"". $thispage . "?lab_id=".$lab_id."&product_id=".$v['lab_product_line_product_id_fk']."\" ><i style=\"color: green;\" class=\"fa fa-floppy-o\" aria-hidden=\"true\"></i></a>";//
            echo "<a href=\"#\" onclick=\"save_product_edits(".$recId.");\" ><i id=\"save".$recId."\" style=\"color: green;\" class=\"fa fa-floppy-o\" aria-hidden=\"true\"></i></a>";//
            echo "&nbsp;&nbsp;&nbsp;<a href=\"". $thispage . "?lab_id=".$lab_id."&record_id=".$v['lab_product_line_id']."&delete=1\" ><i style=\"color: red;\" class=\"fa fa-trash-o\" aria-hidden=\"true\"></i></a>"; //
            echo "</p></td>";

            echo "</tr>";
        }
        if(isset($_GET['add_product']))
        {
            $add_row = new add_lab_product($global_product_names_list,$lab_id);
        }
        else
            $add_row = null;
    }
    else
        echo "<tr><td><strong>No Products in this lab.</strong></td></tr>";
    if($add_row == null)
        echo "<tr class='blank_white_row'><td><a style=\"color: red;font-weight: bold; font-size: 15px;\" href=\"$thispage?lab_id=$lab_id&add_product=1\">Add Product</a></td></tr>";


    ?>
</table>
    <div id="result"></div>
</form>
        </section>
    </div>

    <?php /** @var TYPE_NAME $navigation */
    echo $navigation = new site_nav(); ?>

    <footer>
        <p><?php        echo $_SESSION['user']->user_first_name . " " . $_SESSION['user']->user_last_name . " is logged in as " . $_SESSION['user']->username . ".";    ?></p>    <p>Copyright 2017 Loose in the Lab</p>
    </footer>
</div>
<script type="text/javascript">
//    if(strUser)
//    document.getElementById('testing').innerHTML = strUser;

    function save_product_edits(v)
    {
        var cq = '#'.concat('classroom_qty',v);
        var refq = '#'.concat('refill_qty',v);
        var refqd = '#'.concat('refill_qty_detail',v);
        var paq = '#'.concat('participant_qty',v);
        var prq = '#'.concat('presenter_qty',v);
        var retq = '#'.concat('retail_qty',v);
        var retqd = '#'.concat('retail_qty_detail',v);
        var rowID = '#'.concat('row',v);
        var className = ".editedElement";

        var classroom_qty = $(cq).val();
        var refill_qty = $(refq).val();
        var refill_qty_detail = $(refqd).val();
        var participant_qty = $(paq).val();
        var presenter_qty = $(prq).val();
        var retail_qty = $(retq).val();
        var retail_qty_detail = $(retqd).val();
        var activerow = $(rowID).val();

        //alert(classroom_qty);

        $.post('includes/post/post_save_lab_product_line.php', {post_lab_product_line_id:v,post_classroom_qty:classroom_qty,post_refill_qty:refill_qty,post_refill_qty_detail:refill_qty_detail,post_participant_qty:participant_qty,post_presenter_qty:presenter_qty,post_retail_qty:retail_qty,post_retail_qty_detail:retail_qty_detail},
        function(data)
        {
            $('#result').html(data);
            //alert(data);
        });

        var unchangeElementsofRow = $(rowID).find(className);
        var restoreElement = unchangeElementsofRow.removeClass("editedElement");
    }
    function onrowchange(v)
    {
        var elementID = '#'.concat(v.id);

        var $changedElement = $(elementID); // Save reference for better performance
        var backgroundInterval = $changedElement.addClass("editedElement");

        //var $showSaveButton =

    }
</script>
</body>
</html>