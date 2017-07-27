<?php
/**
 * Created by PhpStorm.
 * User: amalyuchik
 * Date: 7/27/2017
 * Time: 2:32 PM
 */
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/globals.php");

if ($conn == null)
{
    $conn = new connections();
}
$post_book_id = stripslashes($_POST['post_book_id']);
$post_lab_id = stripslashes($_POST['post_lab_id']);
$f_v_array = array('book_lab_line_book_id_fk'=>$post_book_id,'book_lab_line_lab_id_fk'=>$post_lab_id,'book_lab_line_date_created'=>$current_date);
$table_array = array('book_lab_line');

if ($post_book_id !== null && $post_lab_id !== null)
{
    $query = $conn->query_construct("create",$table_array,$f_v_array);
    echo $query;
    $sql_execute = $conn->runconn_sql_execute($connection_array,$query);
}
else
    echo $error_msg = "Book Id was not provided";

