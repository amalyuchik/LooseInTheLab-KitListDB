<?php
if(!isset($_COOKIE['ID_LooseInTheLab'])){
    header("Location: http://www.google.com");
    //header("Location: http://www.seriouslyfunnyscience.com/workshops/login.php");
}
/**
 * Created by IntelliJ IDEA.
 * User: amalyuchik
 * Date: 11/7/2017
 * Time: 4:42 PM
 */
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/globals.php");
//require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/data_processing/sql_functions.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/table_rows.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/classes/book_detail.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/classes/book_names_left_menu.php");

if(!$conn)
    $conn = new connections();
if (!$select_sql)
    $select_sql = new select_sql();

$book_name = '';