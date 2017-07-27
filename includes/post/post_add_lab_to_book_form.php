<?php
/**
 * Created by PhpStorm.
 * User: amalyuchik
 * Date: 7/25/2017
 * Time: 2:32 PM
 */
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/globals.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/classes/add_lab_to_book.php");

if ($conn == null)
{
    $conn = new connections();
}
$post_book_id = stripslashes($_POST['post_book_id']);
if ($post_book_id !== null)
{
    $add_lab_to_book_contents = new add_lab_to_book($global_lab_names_list,$post_book_id);
    echo $add_lab_to_book_row = $add_lab_to_book_contents->return_value_string('');
}
else
    echo $error_msg = "Book Id was not provided";

