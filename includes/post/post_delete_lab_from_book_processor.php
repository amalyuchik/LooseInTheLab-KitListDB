<?php
/**
 * Created by PhpStorm.
 * User: amalyuchik
 * Date: 7/31/2017
 * Time: 9:27 AM
 */
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/globals.php");

if ($conn == null)
{
    $conn = new connections();
}
$post_record_id = stripslashes($_POST['post_record_id']);
$f_v_array = array('book_lab_line_id'=>$post_record_id);
$table_array = array('book_lab_line');

if ($post_record_id !== null)
{
    $query = $conn->query_construct("delete",$table_array,$f_v_array);
    //echo $query;
    $sql_execute = $conn->runconn_sql_execute($connection_array,$query);
}
else
    echo $error_msg = "Record Id was not valid";

