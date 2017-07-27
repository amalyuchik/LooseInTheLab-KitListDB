<?php
/**
 * Created by PhpStorm.
 * User: sportypants
 * Date: 4/4/17
 * Time: 10:52 PM
 */
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/globals.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/pdo_wrapper_class.php");
class connections
{

	public function runconn_sql_execute($connection_array, $query)
	{
		$conn = null;
		$return_result = null;
		try {
				$conn = new PDO("mysql:host=$connection_array[0];dbname=$connection_array[3]", $connection_array[1], $connection_array[2]);

				// set the PDO error mode to exception
				$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$stmt = $conn->prepare($query);

				$stmt->execute();

				if(substr($query, 0, 6) == 'SELECT')
				{
					// set the resulting array to associative
					$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);

					$return_result = $stmt->fetchAll();
				}
			}
		catch (PDOException $e)
			{
				echo "Connection failed: " . $e->getMessage();
			}
		return $return_result;
	}

//    public function sql_execute($conn, $query)
//    {
//        //"SELECT * FROM products WHERE product_sku != ''";
//        $result = $conn->prepare($query);
//        echo gettype($result);
//        $result->execute();
//
//        if(substr($query, 0, 6) == 'SELECT')
//        {
//            // set the resulting array to associative
//            $result = $result->setFetchMode(PDO::FETCH_ASSOC);
//
//            $return_result = $result->fetchAll();
//        }
//
//        return $return_result;
//    }

	public function query_construct($type, $tables_array, $field_values_array)
	{
		$query = '';
		$fields = '';
		$values = '';

		if($type == 'create')
		{
			//echo var_export($field_values_array, true);
			foreach ($field_values_array as $k => $v)
			{
				$fields .= $k . ",";
				if(gettype($v) == "string" or $v == '')
					$values .= "'" . $v . "',";
				else
					$values .= $v . ",";

			}
			$fields = substr($fields,0, strlen($fields)-1); //Remove the last comma
			$values = substr($values,0, strlen($values)-1); //Remove the last comma

			$query = "INSERT INTO $tables_array[0]" . " (" . $fields . ") VALUES (" . $values . ")";
		}
		elseif($type == 'read')
		{
			if($field_values_array[0] == "TABLE_SCHEMA")
				$query = "SELECT TABLE_NAME,COLUMN_NAME,ORDINAL_POSITION,DATA_TYPE,CHARACTER_MAXIMUM_LENGTH,COLUMN_KEY,COLUMN_COMMENT FROM COLUMNS WHERE " . $field_values_array[0] . " = '" . $field_values_array[1] . "'";
			else
			{










				$query = "SELECT * FROM $tables_array[0] "; //WHERE product_sku != ''"












			}





		}
		elseif($type == 'update')
		{
			//echo var_export($field_values_array, true);
			$f_v_pair = ''; //String of comma separated field/value pairs for and update query
			$where_clause = '';
			$i=0;
			foreach($field_values_array as $record_field_id => $record_field_id_value)//Record ID for the where clause
			{
				if ($i == 0)
				{
					//$record_field_id_value = $field_values_array[$record_field_id]; //Record ID value for the where clause
					$where_clause = $record_field_id . "=" . $record_field_id_value;
				}
				else
					break;
				$i++;
			}
			$field_values_array = array_slice($field_values_array,1,count($field_values_array)-1);
			foreach ($field_values_array as $field=>$value)
			{
				if(gettype($value) == "string" && $value*1 !== false)
				{
					$value = "'".$value."'";
				}
				elseif($value == 0) {
					$value = 0;
				}

				$f_v_pair .= $field . "=" . $value . ",";

			}

			$f_v_pair = substr($f_v_pair, 0, strlen($f_v_pair)-1); //Remove the last comma
			$query = "UPDATE $tables_array[0] SET $f_v_pair WHERE ". $where_clause;
		}
		elseif($type == 'delete')
		{
				$i=0;
				foreach(array_keys($field_values_array) as $record_field_id)//Record ID for the where clause //Really oughta fix this and write it in a better way need to figure out how to get first member of assos array.
				{
					if($i == 0)
					{
						$record_field_id_value = $field_values_array[$record_field_id]; //Record ID value for the where clause
                        //echo $record_field_id_value;
						//Loop through the values array to create the IN statement
						if(count($record_field_id_value) > 1)
						{
							$in = " IN (";

							foreach($record_field_id_value as $id)
							{
								$in .= $id . ",";
							}
							$in = substr($in, 0, strlen($in)-1);
							$in .= ")";

							$query = "DELETE FROM $tables_array[0] WHERE " . $record_field_id . $in;
						}
						else //If there's only one member in the value array, present it
						{
							$query = "DELETE FROM $tables_array[0] WHERE " . $record_field_id . "=" . $record_field_id_value;
						}
					}
					else
						continue;
					$i++;
				}

		}
		return $query;
	}
//        foreach (new TableRows(new RecursiveArrayIterator($result->fetchAll())) as $k => $v)
//        {
//            echo $v;
//        }
}
?>