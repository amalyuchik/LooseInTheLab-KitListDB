<?php
/**
 * Created by PhpStorm.
 * User: amalyuchik
 * Date: 7/25/2017
 * Time: 2:32 PM
 */
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/globals.php");
$product_cat_id = stripslashes($_GET['post_record_id']);
if ($conn == null)
{
    $conn = new connections();
}
//echo "Testing" . $_GET['id'];

$query = $conn->query_construct("read", array('products'),array("product_category_id_fk"=>$_GET['id']));
$result = $conn->runconn_sql_execute($connection_array,$query);

echo var_export($result,true);

$conn = null;