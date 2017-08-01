<?php
/**
 * Created by PhpStorm.
 * User: sportypants
 * Date: 4/4/17
 * Time: 10:35 PM
 */
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/data_processing/connections.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/data_processing/select_sql.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/data_processing/sql_functions.php");
//require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/lab_edit_data_object.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/site_nav.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/form_element_classes/select_input.php");

$db_h = 'kitliastdb.db.3766381.hostedresource.com';
$db_u = 'kitliastdb';
$db_p = 'CalStars9462!';
$db_n = 'kitliastdb';
$db_n_schema = 'information_schema';
$state_submit = $_POST['state'];
$grade_submit = $_POST['grade'];
$current_date = date("Y-m-d H:i:s");

$base_url = "http://www.seriouslyfunnyscience.com/";

$connection_array_schema = array($db_h,$db_u,$db_p,$db_n_schema);
$connection_array = array($db_h,$db_u,$db_p,$db_n);
$schema_array = array('TABLE_SCHEMA',$db_n);

//Include script tags
$port = 3306;
$bootstrapLink = '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">';
$angularJSLink = '<script data-require="angular.js@1.6.1" data-semver="1.6.1" src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.6.1/angular.js"></script>';
$angularRouteLink = '<script data-require="angular-route@1.3.0" data-semver="1.3.0" src="https://code.angularjs.org/1.3.0/angular-route.js"></script>';
$jQueryLink = '<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>';
$fontAwesomeLink = '<link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">';
$thispage = $_SERVER['PHP_SELF'];
//End include script tags


$lab_id = $_GET['lab_id'];

/*Password for att_edit and delete pages*/
$set_pass = "Sn1cK3r$";
$do_once = 0;

$test_array_for_query = array('product_id'=>12, 'product_name'=>'name', 'product_description'=>'description', 'product_quantity'=>114);

$table_array_create = array();
$table_array_read = array();
$table_array_update = array();
$table_array_delete = array();

$state_create_array = array('state_name'=>'Walla Xallay', 'state_abbreviation'=>'WX');
//$delete_array = array('state_id'=>array(53, 55, 56));
$delete_array = array('state_id'=>array(59));
$read_array = array('state_id'=>array(59));
$operators_array = array('Equal'=>'=','Not Equal'=>'!=','Less Than'=>'<','Greater Than'=>'>','Less Than or Equal to'=>'<=','Greater Than or Equal to'=>'>=');

if($conn_globals == null)
    $conn_globals = new connections();
if($select_sql == null)
    $select_sql = new select_sql();

//Global lists that can be used throughout the site ike states, grades, lab types and categories...
if($global_available_states_list == null)
{
    $query_available_states = $select_sql->query("available_state_list");
    $global_available_states_list = $conn_globals->runconn_sql_execute($connection_array, $query_available_states);
}if($global_states_list == null)
{
    $query_states = $select_sql->query("state_list");
    $global_states_list = $conn_globals->runconn_sql_execute($connection_array, $query_states);
}
if($global_grades_list == null)
{
    $query_grades = $select_sql->query("grade_list");
    $global_grades_list = $conn_globals->runconn_sql_execute($connection_array, $query_grades);
}
if($global_notes_list == null)
{
    $query_notes = $select_sql->query("notes");
    $global_notes_list = $conn_globals->runconn_sql_execute($connection_array, $query_notes);
}
if($global_books_list == null)
{
    $query_books = $select_sql->query("books");
    $global_books_list = $conn_globals->runconn_sql_execute($connection_array, $query_books);
}
if($global_lab_categories_list == null)
{
    $query_lab_categories = $select_sql->query("lab_categories");
    $global_lab_categories_list = $conn_globals->runconn_sql_execute($connection_array, $query_lab_categories);
}
if($global_lab_types_list == null)
{
    $query_lab_types = $select_sql->query("lab_types");
    $global_lab_types_list = $conn_globals->runconn_sql_execute($connection_array, $query_lab_types);
}
if($global_product_categories_list == null)
{
    $query_product_categories = $select_sql->query("product_categories");
    $global_product_categories_list = $conn_globals->runconn_sql_execute($connection_array, $query_product_categories);
}
if($global_product_names_list == null)
{
    $query_product_names = $select_sql->query("products");
    $global_product_names_list = $conn_globals->runconn_sql_execute($connection_array, $query_product_names);
}
if($global_lab_names_list == null)
{
    $query_lab_names = $select_sql->query("all_labs");
    $global_lab_names_list = $conn_globals->runconn_sql_execute($connection_array, $query_lab_names);
}




?>