<?php
/**
 * Created by PhpStorm.
 * User: amalyuchik
 * Date: 7/25/2017
 * Time: 2:32 PM
 */
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/globals.php");
$connection = null;
if ($connection == null)
{
    $connection = new connections();
}
$post_classroom_qty = stripslashes($_POST['post_classroom_qty']);
$post_refill_qty = stripslashes($_POST['post_refill_qty']);
$post_refill_qty_detail = stripslashes($_POST['post_refill_qty_detail']);
$post_participant_qty = stripslashes($_POST['post_participant_qty']);
$post_presenter_qty = stripslashes($_POST['post_presenter_qty']);
$post_retail_qty = stripslashes($_POST['post_retail_qty']);
$post_retail_qty_detail = stripslashes($_POST['post_retail_qty_detail']);
$post_record_id = stripslashes($_POST['post_lab_product_line_id']);

$f_v_array = array("lab_product_line_id"=>$post_record_id,"lab_product_line_classroom_qty"=>$post_classroom_qty,"lab_product_line_refill_qty"=>$post_refill_qty,"lab_product_line_refill_qty_detail"=>$post_refill_qty_detail,"lab_product_line_participant_qty"=>$post_participant_qty,"lab_product_line_presenter_qty"=>$post_presenter_qty,"lab_product_line_retail_qty"=>$post_retail_qty,"lab_product_line_retail_qty_detail"=>$post_retail_qty_detail);
$table_array = array('lab_product_line');
$query = $connection->query_construct('update',$table_array,$f_v_array);
//echo $query."\r\n<br />";
$execute_sql = $connection->runconn_sql_execute($connection_array,$query);


