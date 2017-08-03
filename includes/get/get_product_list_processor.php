<?php
/**
 * Created by PhpStorm.
 * User: amalyuchik
 * Date: 8/3/2017
 * Time: 9:30 AM
 */
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/globals.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/classes/product_list_contents.php");
$product_cat_id = stripslashes($_GET['id']);
$product_cat_name = stripslashes($_GET['category_name']);
if ($conn == null)
{
    $conn = new connections();
}
//echo "Testing" . $_GET['id'];

$query = $conn->query_construct("read", array('products'),array("product_category_id_fk"=>$product_cat_id));
//$result = $conn->runconn_sql_execute($connection_array,$query);
$category_product_list = new product_list_contents($connection_array,$product_cat_id,$product_cat_name,$conn,$query,0,'');

//echo $category_product_list;

$conn = null;