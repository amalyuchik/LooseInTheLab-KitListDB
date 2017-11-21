<?php
/**
 * Created by IntelliJ IDEA.
 * User: amalyuchik
 * Date: 11/20/2017
 * Time: 12:47 PM
 */
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/globals.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/classes/commercial_book_detail.php");
$book_id = stripslashes($_GET['book_id']);
$book_name = stripslashes($_GET['book_name_supplemental']);
if ($conn == null)
{
    $conn = new connections();
}
if ($select_sql == null)
    $select_sql = new select_sql();

$category_product_list = new commercial_book_detail($connection_array,$book_name,$book_id,$conn,$select_sql,'','');

$conn = null;
$select_sql == null;