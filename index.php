<?php
if(!isset($_COOKIE['ID_LooseInTheLab'])){
    header("Location: http://www.seriouslyfunnyscience.com/workshops/login.php");
}
/**
 * Created by PhpStorm.
 * User: sportypants
 * Date: 4/4/17
 * Time: 10:32 PM
 */
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/globals.php");
//require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/sql_functions.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/data_processing/select_sql.php");

echo "<table style='border: solid 1px black;'>";
echo "<tr><th>Id</th><th>SKU</th><th>Name</th></tr>";

class TableRows extends RecursiveIteratorIterator {
    function __construct($it) {
        parent::__construct($it, self::LEAVES_ONLY);
    }

    function current() {
        return "<td style='width:150px;border:1px solid black;'>" . parent::current(). "</td>";
    }

    function beginChildren() {
        echo "<tr>";
    }

    function endChildren() {
        echo "</tr>" . "\n";
    }
}


require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/data_processing/connections.php");

$connection = new connections();

//$connection = new connections();
//$connection_ = $connection->runconn($db_h, $db_u, $db_p, $db_n);

array_push($table_array_create, 'states');
array_push($table_array_read, 'lab_product_line');
array_push($table_array_update, 'table1','table2', 'table3');
array_push($table_array_delete, 'states');
$testing_sql_construct5 = $connection->query_construct('read', $table_array_read, $schema_array);
echo $testing_sql_construct5;
echo "<--- Testing Select SQL Construct 5<br /><br />";

$sql_schema_execute = $connection->runconn_sql_execute($connection_array_schema, $testing_sql_construct5);

foreach (new TableRows(new RecursiveArrayIterator($sql_schema_execute)) as $k => $v)
{
    echo $v;
}

$testing_sql_construct1 = $connection->query_construct('create', $table_array_create, $state_create_array);
echo $testing_sql_construct1;
echo "<--- Testing Create SQL Construct 1<br /><br />";

//$sql_execute = $connection->runconn_sql_execute($connection_array, $testing_sql_construct1);
//$sql_execute = $connection->runconn_sql_execute($connection_array, $query_select);

//$sql_execute = $connection->runconn_sql_execute($connection_array, $testing_sql_construct4);


$testing_sql_construct2 = $connection->query_construct('update', $table_array_update, $test_array_for_query);
echo $testing_sql_construct2;
echo "<--- Testing Update SQL Construct 2<br /><br />";


$testing_sql_construct3 = $connection->query_construct('delete', $table_array_delete, $delete_array);
echo $testing_sql_construct3;
echo "<--- Testing Delete SQL Construct 3<br /><br />";
echo "</table>";

?>