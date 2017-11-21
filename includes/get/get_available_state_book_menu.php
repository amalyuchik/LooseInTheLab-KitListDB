<?php
/**
 * Created by IntelliJ IDEA.
 * User: amalyuchik
 * Date: 11/20/2017
 * Time: 3:04 PM
 */
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/globals.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/classes/commercial_book_detail.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/classes/state_available_book_menu.php");
$state_id = stripslashes($_GET['state_id']);
$state_name = stripslashes($_GET['state_name']);
if ($conn == null)
{
    $conn = new connections();
}
if ($select_sql == null)
    $select_sql = new select_sql();

$state_available_book_menu_list = new state_available_book_menu($connection_array,$state_name,$state_id,$conn,$select_sql,'');

$conn = null;
$select_sql == null;