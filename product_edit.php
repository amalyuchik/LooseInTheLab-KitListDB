<?php
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/globals.php");
$field_value_arr = array(); //id field/id value first. Then all other values
$table = array(0=>'products');
$insert_error = '';
if(isset($_POST['product_sku']) || (isset($_GET['product_id']) && $_GET['product_id'] == 0))
{
    $field_value_arr = array(
        'product_id' => $_POST['product_id'],
        'product_sku' => $_POST['product_sku'],
        'product_name' => $_POST['product_name'],
        'product_description' => $_POST['product_description'],
        'product_category' => $_POST['product_category'],
        'product_category_id_fk' => $_POST['product_category_id_fk'],
        'product_cost' => $_POST['product_cost'],
        'product_price' => $_POST['product_price']
    );
    $product_conn = new connections();
    if ($_GET['product_id'] != 0)
    {
        $product_edit_query = $product_conn->query_construct('update', $table, $field_value_arr);
        $product_edit_result = $product_conn->runconn_sql_execute($connection_array, $product_edit_query);
    }
    elseif ($_GET['product_id'] == 0 && $_POST['product_name'] !='')
    {
        $product_add_query = $product_conn->query_construct('create', $table, $field_value_arr);
        $product_add_result = $product_conn->runconn_sql_execute($connection_array, $product_add_query);
        $insert_error = $product_add_result;
    }
    $product_conn = null;
}

$product_id = $_GET['product_id'];
if($conn == null)
    $conn = new connections();
if($select_sql == null)
    $select_sql = new select_sql();

if(isset($product_id) && $product_id !== 0)
{
    $query_product = $select_sql->edit_query("product", $product_id);
    $edit_product_result = $conn->runconn_sql_execute($connection_array, $query_product);

    $product_sku = trim($edit_product_result[0]['product_sku']);
    $product_name = trim($edit_product_result[0]['product_name']);
    $product_description = trim($edit_product_result[0]['product_description']);
    $product_category = $edit_product_result[0]['product_category'];
    $product_category_id_fk = $edit_product_result[0]['product_category_id_fk'];
    $product_cost = $edit_product_result[0]['product_cost'];
    $product_price = $edit_product_result[0]['product_price'];
}


?>
<!DOCTYPE html>
<html>
<head>
    <META NAME="ROBOTS" CONTENT="NONE">
    <?php echo $bootstrapLink; ?>
    <link rel="stylesheet" href="css/nav_style.css" />
<style>
    td{
        padding-left: 10px;
    }th{
         padding-left: 10px;
     }
</style>
<title><?php
    if ($_GET['product_id'] != 0)
        echo $edit_product_result[0]['product_name']." | Edit";
    else
        echo "New Product Add";
    ?> </title>

</head>
<body>
<div class="container">
    <section>
<?php


?>
    <?php
        if ($insert_error == '')
            echo $product_id == 0 ? "<h1>Add New Product</h1>" : "<h1>".$edit_product_result[0]['product_name']. "</h1>";
        else
            echo "<h1>Previous product failed to insert</h1><p style='color: red;'>Try to insert the product again and make sure all fields are filled in correctly. If so, contact Andrei at andrei.malyuchik@gmail.com and give him the following error.</p><p>".$insert_error."</p>"
        ?>
<form action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
    <label>SKU: &nbsp;</label>
    <input type="text" size="60" name="product_sku" value="<?php echo $product_sku; ?>"><br /><br />
    <?php echo "<input type=\"hidden\" name=\"product_id\" value=".$edit_product_result[0]['product_id'].">"; ?>
    <label>Name: &nbsp;</label>
    <input type="text" size="60" name="product_name" value="<?php echo $product_name; ?>"><br /><br />
    <label>Description: &nbsp;</label>
    <textarea rows="4" cols="59" name="product_description"><?php echo $product_description; ?></textarea><br /><br />
    <?php $select_field_categories = new select_input($global_product_categories_list,'Category','product_category_id_fk',$product_category_id_fk);
    echo $select_field_categories->create_select_field(false);?>&nbsp;
    <br /><br /><label>Cost: &nbsp;</label>
    <input type="text" size="60" name="product_cost" value="<?php echo $product_cost; ?>"><br /><br />
    <label>Price: &nbsp;</label>
    <input type="text" size="60" name="product_price" value="<?php echo $product_price; ?>"><br /><br />
    <input type="submit" value="Add Product">
</form>

    </section>
<?php echo $navigation = new site_nav(); ?>

<footer>
    <p>Copyright 2017 ASM</p>
</footer>
</div>
</body>
</html>