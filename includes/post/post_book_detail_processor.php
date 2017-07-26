<?php
/**
 * Created by PhpStorm.
 * User: amalyuchik
 * Date: 7/25/2017
 * Time: 2:32 PM
 */
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/globals.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/classes/book_detail.php");
if ($conn == null)
{
    $conn = new connections();
}
$post_book_id = stripslashes($_POST['post_book_id']);
if ($post_book_id !== null)
{
    $book_contents = new book_detail($connection_array, null,$post_book_id,$conn,$select_sql,'','',$global_lab_names_list);
}
else
    echo $error_msg = "Book Id was not provided";

