<?php

require_once($_SERVER['DOCUMENT_ROOT']."/amfphp/services/inc_files/sql_functions.php");

//This function returns a connection to the sql server. This is used primarily for keeping
// the username and password to the server in one place.
function	get_mssql_connection()
{
    return sql_pconnect('sqlsrv', 'YescoApps');
}

//This function returns the base path to all vo class files, so it can be managed in one place.
function	get_vo_base_path()
{
    return $_SERVER['DOCUMENT_ROOT']."/amfphp/services/vo/";
}

//Returns adjusted values sent from a Flex EntParameter object, which can
// be used to retrieve datasets, etc for the indicated enterprise object.
function	get_parameter_vars($parameters)
{
    //user_access_functions.php is needed for get_authenticated_user_data() and ua_user_has_access().
    require_once($_SERVER['DOCUMENT_ROOT']."/amfphp/services/inc_files/user_access_functions.php");

    $array_result = array();
    $array_result['ent_object'] = NULL;
    $array_result['ent_object_id'] = -1;
    $array_result['ent_object_fields'] = array();
    /*
    $fp = fopen('tmp.txt', 'w+');
    fwrite($fp, var_export($parameters,true)."\n\n");
    fclose($fp);
    */
    try
    {
        $ent_object_name = array_key_exists('native_object',$parameters) ? $parameters['native_object'] : 'none';
        $array_result['is_exclusive'] = array_key_exists('is_exclusive',$parameters) ? $parameters['is_exclusive'] : true;
        $array_result['field_filters'] = array_key_exists('field_list',$parameters) ? $parameters['field_list'] : array();
        $array_result['validated_division_filter'] = array();
        $array_result['sort_fields'] = array_key_exists('sort_fields',$parameters) ? $parameters['sort_fields'] : array();
        $array_result['include_attachments'] = array_key_exists('include_attachments',$parameters) ? $parameters['include_attachments'] : false;
        $array_result['user'] = get_authenticated_user_data();	//Attempts to retrieve information about a user previously authenticated within a session.
        $array_result['access'] = array(0=>array('Success'=>false,'Access Type'=>0));	//Defines read access that the logged-in user has for the indicated object.
    }
    catch(Exception $e)
    {
        $ent_object_name = '';
        $array_result['is_exclusive'] = '';
        $array_result['field_filters'] = array();
        $array_result['validated_division_filter'] = array();
        $array_result['sort_fields'] = array();
        $array_result['include_attachments'] = false;
        $array_result['user'] = array();
        $array_result['access'] = array(0=>array('Success'=>false,'Access Type'=>0));
    }

    //Only allow the ent_object and ent_object_id to be set if both the specified ent_object is valid and
    // if the user has some form of read access to the object.
    //Otherwise, the script calling this function should not process the request it has received.
    if ($ent_object_name != '')
    {
        //Retrieve the indicated ent_object's information from the database.
        $array_result['ent_object'] = get_ent_object_data_by_name($ent_object_name);
        $array_result['ent_object_id'] = $array_result['ent_object']['ent_object_id'];

        //Allow any user (including anonymous users) to view object data if they have any form of read access to the object.
//        $user_id = ( isset($array_result['user']['user_id']) ) ? $array_result['user']['user_id'] : 1;	//user_id 1 is the anonymous user.
//        $a_user_access = array(0=>array('Success'=>false,'Access Type'=>0));
//        if ($array_result['ent_object_id'] > -1)
//            $a_user_access = ua_user_has_access($user_id, 2, $array_result['ent_object_id'], '', '', false);	//read privilege = 2
//        $array_result['access'] = $a_user_access;
        /*
        $fp = fopen('tmp.txt', 'w+');
        fwrite($fp, var_export($array_result,true)."\n\n");
        fclose($fp);
        */

        if ($a_user_access[0]['Success'] == true)
        {
            //The user has some form of read access to this object.

            //Get the field definitions for this object.
            $array_result['ent_object_fields'] = get_field_data($array_result['ent_object_id']);

            //Adjust field_filters so the user doesn't get access to data he/she doesn't have access to read.
            //Add any conditional filters required by this object.
            include_once($_SERVER['DOCUMENT_ROOT']."/amfphp/services/inc_files/filter_functions.php");
            $a_filters = adjust_filters_for_security($array_result['user'], $array_result['ent_object'], $array_result['ent_object_fields'], $array_result['field_filters']);
            $array_result['field_filters'] = $a_filters['adjusted_field_filters'];
            $array_result['validated_division_filter'] = $a_filters['validated_division_filter'];
        }
        else
        {
            /*
            $fp = fopen('tmp.txt', 'a+');
            fwrite($fp, "The user does not have access!\n");
            fwrite($fp, "Given parameter:\n".var_export($parameters,true)."\n");
            fwrite($fp, "Created parameter:\n".var_export($array_result,true)."\n\n");
            fclose($fp);
            */

            //Trigger an event identifying the user access failure.
            require_once($_SERVER['DOCUMENT_ROOT']."/amfphp/services/inc_files/event_functions.php");
            wb_trigger_event('crud', array('Action'=>'read','EntParameter'=>$array_parameters,'Query'=>'N/A','Result'=>array(false, 'The logged-in user (user_id='.$array_result['user']['user_id'].') does not have access to the requested resource (ent_object_id='.$array_result['ent_object_id'].').')));

            //The user doesn't have access to read records for this object.
            //Return -1 for the object id so the function calling this one will know not to process the request.
            $array_result['ent_object'] = NULL;
            $array_result['ent_object_id'] = -1;
        }
    }
    /*
    $fp = fopen('tmp.txt', 'w+');
    fwrite($fp, var_export($array_result['field_filters'],true)."\n\n");
    fclose($fp);
    */

    return $array_result;
}

//Returns an array containing adjusted values representing an individual field filter
// from the $array_result['field_filters'] array returned by get_parameter_vars().
function	get_field_vars($filter_field)
{
    $array_result = array();

    $array_result['name'] = array_key_exists('name',$filter_field) ? $filter_field['name'] : '';
    $array_result['generic_name'] = array_key_exists('generic_name',$filter_field) ? $filter_field['generic_name'] : '';
    $array_result['type'] = array_key_exists('type',$filter_field) ? $filter_field['type']['sz_type'] : 'Like';
    $array_result['values'] = array_key_exists('values',$filter_field) ? $filter_field['values'] : array();
    $array_result['is_exclusive'] = array_key_exists('is_exclusive',$filter_field) ? $filter_field['is_exclusive'] : true;
    $array_result['is_complement'] = array_key_exists('is_complement',$filter_field) ? $filter_field['is_complement'] : false;

    return $array_result;
}

/*
//I don't think this is used anywhere anymore. The above function should be used instead, anyway.

//Returns adjusted values for a single field filter in the field_filters index of the
// array returned by get_parameter_vars().
function	get_field_filter_vars($field_filter)
{
	$array_result = array();
	
	$array_result['name'] = array_key_exists('name',$field_filter) ? $field_filter['name'] : '';
	$array_result['generic_name'] = array_key_exists('generic_name',$field_filter) ? $field_filter['generic_name'] : '';
	$array_result['type'] = array_key_exists('type',$field_filter) ? $field_filter['type']['sz_type'] : 'Like';
	$array_result['values'] = array_key_exists('values',$field_filter) ? $field_filter['values'] : array();
	
	if (($array_result['name']=='') && ($array_result['generic_name']==''))
	{
		return false;
	}
	
	return $array_result;
}
*/

function	new_field_filter($name, $generic_name, $type, $is_exclusive=false, $values=array())
{
    $result = array();

    $result['name'] = $name;
    $result['generic_name'] = $generic_name;
    $result['type'] = array();
    $result['type']['sz_type'] = $type;
    $result['is_exclusive'] = $is_exclusive;
    $result['values'] = $values;

    return $result;
}

function	get_ent_object_id($ent_object_name)
{
    $conn = get_mssql_connection();
    $sql_select = "SELECT ent_object_id FROM [ent_object] WHERE ent_object_name='".$ent_object_name."'";
    $result = sql_query_rows('', $conn, $sql_select); //mssql_query($sql_select, $conn);
    if (count($result) > 0)
        return $result[0]['ent_object_id'];
    else
        return -1;
}

function	get_ent_object_data_by_name($ent_object_name)
{
    $conn = get_mssql_connection();
    $sql_select = "SELECT * FROM [ent_objects_with_virtual_fields] WHERE ent_object_name='$ent_object_name'";
    $result = sql_query_rows('', $conn, $sql_select);

    if (count($result) > 0)
    {
        //Trim the results for the ent_object query.
        $new_data = array();
        foreach($result[0] as $field_name=>$field_value)
            $new_data[$field_name] = trim($field_value);

        //Return the trimmed results.
        return $new_data;
    }
    else
    {
        //No ent_object matched the given name.
        //Return an empty array.
        return array();
    }
}

function	get_ent_object_search_info($ent_object_id)
{
    $conn = get_mssql_connection();
    $sql_select = "SELECT * FROM [ent_objects_with_virtual_fields] WHERE ent_object_id='$ent_object_id'";
    $result = sql_query_rows('', $conn, $sql_select);

    return get_ent_object_search_info_from_result($result);
}

function	get_ent_object_search_info_by_name($ent_object_name)
{
    $conn = get_mssql_connection();
    $sql_select = "SELECT * FROM [ent_objects_with_virtual_fields] WHERE ent_object_name='$ent_object_name'";
    $result = sql_query_rows('', $conn, $sql_select);

    return get_ent_object_search_info_from_result($result);
}

function	get_ent_object_search_info_from_result($ent_object_result)
{
    if ($ent_object_result && count($ent_object_result)>0)
    {
        $obj_result['object_id'] =			trim($ent_object_result[0]['ent_object_id']);
        $obj_result['object_name'] =		trim($ent_object_result[0]['ent_object_name']);
        $obj_result['object_server_id'] =	trim($ent_object_result[0]['ent_object_dbserver_id']);
        $obj_result['object_server_ip'] =	trim($ent_object_result[0]['ent_object_data_server_ip']);
        $obj_result['object_db_name'] =		trim($ent_object_result[0]['ent_object_data_db_name']);
        $obj_result['object_table_name'] =	trim($ent_object_result[0]['ent_object_data_table_name']);
        $obj_result['object_filter'] =		trim($ent_object_result[0]['ent_object_data_filter']);
    }
    else
    {
        $obj_result = array();
        $obj_result['object_id'] =			'';
        $obj_result['object_name'] =		'';
        $obj_result['object_server_id'] =	'';
        $obj_result['object_server_ip'] =	'';
        $obj_result['object_db_name'] =		'';
        $obj_result['object_table_name'] =	'';
        $obj_result['object_filter'] =		'';
    }

    return $obj_result;
}

//Returns a db_type, based on an IP address.
function	get_db_type_by_ip($db_ip)
{
    if (($db_ip == '207.92.85.60') || ($db_ip == 'titan.yesco.com'))	//Titan (MS SQL)
        return	'mssql';
    else if ($db_ip == '207.92.85.253')	//AS400 (I-Series)
        return	'iseries';
    else if ($db_ip == '207.92.85.69')	//Everest (MySQL)
        return	'mysql';
    else
        return	'';	//Not a Workbench database server.
}

//Returns a list of Jobscope libraries, based on the expected $obj_division_field
// in $field_filters.
function	get_db2_libs($field_filters, $obj_division_field)
{
    $libs = array();

    if ($obj_division_field == '')
        return $libs;

    //Ignore the is_exclusive flag for JS division fields for now.
    foreach($field_filters as $filter)
    {
        //Get the parsed values for this field.
        $field_vars = get_field_vars($filter);
        if (($field_vars['name']==$obj_division_field) || ($field_vars['generic_name']=='division_id'))
        {
            //Assume a division_id was given.
            foreach($field_vars['values'] as $value)
            {
                $tmp_lib = get_div_data($value);
                if ($tmp_lib)
                    $libs[get_div_lib($value)] = $tmp_lib;
            }
        }
    }

    //No field matched.
    return $libs;
}

function	get_div_lib($div_id)
{
    $conn = get_mssql_connection();
    $result = sql_query_rows('', $conn, "SELECT division_library_js FROM [division] WHERE division_id='$div_id'");
    if (count($result) > 0)
        return $result[0]['division_library_js'];
    else
        return	'';
}

function	get_div_data($div_id)
{
    $conn = get_mssql_connection();
    $result = sql_query_rows("SELECT division_id, division_name, division_library_js FROM [division] WHERE division_id='$div_id'", $conn);
    if (count($result) > 0)
        return $result[0];
    else
        return	'';
}

function	get_div_data_list()
{
    $divs = array();

    $conn = get_mssql_connection();
    $result = sql_query_rows('', $conn, "SELECT division_id, division_name FROM [division]");
    if (count($result) > 0)
    {
        foreach ($result as $row)
            $divs[ $row['division_id'] ] = $row['division_name'];

        //Add the 0 index to the list.
        $divs[0] = '';
    }

    return $divs;
}

//Returns information about all fields in the dataset for the indicated enterprise object.
//If $field_name is not an empty string, only information about that field is returned.
//If $key_field_only is true, only information about the key field is returned.
//If both $field_name is not empty and $key_field_only is true, then at most two fields
// will be returned.
function	get_field_data($ent_object_id, $field_name='', $key_field_only=false, $div_field_only=false, $sz_type='')
{
    $fields_result = array();

    $field_filter = 		($field_name=='') ? '' : " AND object_field_name='$field_name'";
    $key_field_filter = 	($key_field_only==false) ? '' : " AND object_field_is_key=1";
    $div_field_filter = 	($div_field_only==false) ? '' : " AND object_field_type_name='division_id'";
    $field_type_filter =	($sz_type=='') ? '' : " AND object_field_type_name='$sz_type'";

    //require_once($_SERVER['DOCUMENT_ROOT']."/amfphp/services/inc_files/user_access_functions.php");
    //$user_data = get_authenticated_user_data();

    $conn = get_mssql_connection();
    $sql_select = "SELECT * FROM [object_field] WHERE object_field_object_id='$ent_object_id'$field_filter$key_field_filter$div_field_filter$field_type_filter ORDER BY object_field_id";

    /*
    if ($user_data['user_id'] == 22)
    {
        $fp = fopen('tmp.txt', 'w+');
        fwrite($fp, $sql_select.":\n\n");
        fclose($fp);
    }
    */

    $result = sql_query_rows('', $conn, $sql_select);
    if (!$result)
        return $fields_result;

    foreach ($result as $row)
    {
        $fields_result[] = $row;

        if ($user_data['user_id'] == 22)
        {
            $fp = fopen('tmp.txt', 'a+');
            fwrite($fp, $row['object_field_name']."\n");
            fclose($fp);
        }
    }
    if ($user_data['user_id'] == 22)
    {
        foreach ($result as $row)
        {
            $fp = fopen('tmp.txt', 'a+');
            fwrite($fp, $row['object_field_name']." -- STILL HERE (A)!!!\n");
            fclose($fp);
        }
        foreach ($result as $row)
        {
            $fp = fopen('tmp.txt', 'a+');
            fwrite($fp, $row['object_field_name']." -- STILL HERE (B)!!!\n");
            fclose($fp);
        }
        foreach ($result as $row)
        {
            $fp = fopen('tmp.txt', 'a+');
            fwrite($fp, $row['object_field_name']." -- STILL HERE (C)!!!\n");
            fclose($fp);
        }
    }

    return $fields_result;
}

//Returns the record data describing the division field, if there is one.
//Otherwise, returns NULL.
//$obj_fields should be an array returned by get_field_data().
//It is assumed that ent_object's only have one division field, and that it describes a division_id.
function	get_obj_division_field_data_from_fields($obj_fields)
{
    if ($obj_fields)
    {
        foreach($obj_fields as $field)
        {
            if ($field['object_field_type_name']=='division_id')
                return $field;
        }
    }

    //No division field found.
    return NULL;
}

//Returns the name of the division_id field, if there is one.
//Otherwise, returns an empty string.
//$obj_fields should be an array returned by get_field_data().
function	get_obj_division_field_name_from_fields($obj_fields)
{
    if ($obj_fields)
    {
        foreach($obj_fields as $field)
        {
            if ($field['object_field_type_name']=='division_id')
                return $field['object_field_name'];
        }
    }

    //No division_code field found.
    return '';
}

function	get_js_library_from_division_id($div_id)
{
    $js_lib = '';

    $conn = get_mssql_connection();
    $sql_select = "SELECT division_libary_js FROM [division] WHERE division_id='$div_id' ORDER BY division_id";
    $result = sql_query_rows('', $conn, $sql_select);
    if ($result && count($result) > 0)
        $js_lib = $result[0]['division_libary_js'];

    return $js_lib;
}

//Returns the name of the division field for the indicated enterprise object, if one is specified.
//If a division field is not specified, returns an empty string.
function	get_obj_division_field_name($ent_object_id)
{
    $key_field_name = '';

    $conn = get_mssql_connection();
    $sql_select = "SELECT object_field_name FROM [object_field] WHERE object_field_object_id='$ent_object_id' AND object_field_type_name='division_id' ORDER BY object_field_id";
    $result = sql_query_rows('', $conn, $sql_select);
    if ($result && count($result) > 0)
        $key_field_name = $row[0]['object_field_name'];

    return $key_field_name;
}

//Returns a comma-delimited string of local field names for the given object fields.
//If $show_virtual_fields is true (default), virtual fields will be included.
function	sz_get_object_field_names($obj_table_name, $obj_fields, $show_virtual_fields=true, $sql_type='mssql')
{
    //Escape sql tables/fields with delimiters, based on sql server type.
    $escape_chars = get_escape_chars($sql_type);

    //Specify to which table each field belongs, so errors about ambiguous column names don't occur.
    if ($sql_type == 'iseries')
        $obj_table_name = '';
    else
        $obj_table_name = $escape_chars[0].$obj_table_name.$escape_chars[1].'.';

    //Create an array of fields.
    $a_fields = array();
    foreach($obj_fields as $field)
    {
        if ($field['object_field_is_virtual']==true)
        {
            //This is a virtual field.
            $is_concatenated_list_data_field =	field_is_concatenated($field['object_field_list_data_field'], $sql_type);
            if ($is_concatenated_list_data_field || $field['object_field_custom_join'] != '')
            {
                $a_fields[] = '('.$field['object_field_list_data_field'].') AS '.$escape_chars[0].$field['object_field_name'].$escape_chars[1];
            }
            else if (($field['object_field_list_table']!='') && ($field['object_field_list_table_alias']!='') && ($field['object_field_list_data_field']!='') && ($field['object_field_name']!=''))
            {
                $a_fields[] = $escape_chars[0].$field['object_field_list_table_alias'].$escape_chars[1].'.'.$escape_chars[0].$field['object_field_list_data_field'].$escape_chars[1].' AS '.$escape_chars[0].$field['object_field_name'].$escape_chars[1];
            }
        }
        else
        {
            //This is not a virtual field.
            $a_fields[] = $obj_table_name.$escape_chars[0].$field['object_field_name'].$escape_chars[1];
        }
    }

    //Create a comma-separated string of fields from the array of fields.
    $sz_fields = implode(', ', $a_fields);

    return $sz_fields;
}

//Returns the name of the key field, if one is specified.
//Otherwise, returns an empty string.
//$obj_fields should be an array returned by get_field_data().
function	get_obj_key_field_name_from_fields($obj_fields)
{
    foreach($obj_fields as $field)
    {
        if ($field['object_field_is_key'] == true)
            return $field['object_field_name'];
    }

    //No key field found.
    return '';
}

//Returns the name of the key field for the indicated enterprise object, if one is specified.
//If a key field is not specified, returns an empty string.
function	get_obj_key_field_name($ent_object_id)
{
    $key_field_name = '';

    $conn = get_mssql_connection();
    $sql_select = "SELECT object_field_name FROM [object_field] WHERE object_field_object_id='$ent_object_id' AND object_field_is_key=1 ORDER BY object_field_id";
    $result = sql_query_rows('', $conn, $sql_select);
    if ($result && count($result) > 0)
        $key_field_name = $row[0]['object_field_name'];

    return $key_field_name;
}

//Returns a SQL where clause based on specified filters for fields.
//Virtual fields are ignored. They need to be handled manually, outside of this function.
//$array_parameters should contain an array returned from get_parameter_vars().
//$field_data should contain an array returned by get_field_data().
//$search_info should contain an array returned from get_ent_object_data().
function	get_sql_where($ent_object_data, $array_parameters, $field_data, $general_object_filter, $use_virtual_fields=true, $sql_type='mssql')
{
    /*
    $fp = fopen('tmp.txt', 'w+');
    fwrite($fp, var_export($array_parameters['field_filters'],true)."\n\n\n\n\n\n");
    fclose($fp);
    */

    //Create 3 strings: one containing exclusive filters, one containing inclusive filters and one containing
    // the division filter (if there is one).
    $sql_where_inc = '';
    $sql_where_exc = '';
    $sql_where_div = '';
    foreach($array_parameters['field_filters'] as $field_filter)
    {
        //Get the parsed values for this field.
        $field_vars = get_field_vars($field_filter);
        $field_name =			$field_filter['name'];
        $field_name_generic =	$field_filter['generic_name'];
        $field_type =			$field_filter['type']['sz_type'];
        $field_values =			$field_filter['values'];
        $field_is_exclusive =	$field_filter['is_exclusive'];
        $field_is_complement =	$field_filter['is_complement'];

        //Identify whether this is a division field.
        $field_is_division =	($field_name=='SQL' && $field_name_generic=='division_id') ? true : is_division_field($field_name, $field_data);

        //Get the field name for this field.
        if ($field_name == '')
            $field_name = get_local_name($field_name_generic, $field_data);

        //If a field name wasn't specified, skip the field.
        if ($field_name == '')
            continue;

        //If the field is a virtual field (it doesn't actually exist in the specified
        // database table) and $use_virtual_fields is false, skip the field.
        if (($use_virtual_fields==false) && is_virtual_field($field_name,$field_data))
            continue;

        //If get_field_filter() returns an empty string, something is wrong with the
        // filter parameter and this field should be skipped.
        if ($field_type == 'SQL')
        {
            $this_where = trim(get_field_conditional_filter($ent_object_data, $field_values, $field_data, $sql_type));
        }
        else
        {
            $this_where = trim(get_field_filter($ent_object_data['ent_object_data_table_name'], $field_name, $field_type, $field_values, $field_data, $sql_type));
            if ($this_where != '' && $field_is_complement)
                $this_where = "(NOT($this_where))";
        }

        if ($this_where != '')
        {
            if ($field_is_division)
            {
                //There should be only one division field filter.
                $sql_where_div = $this_where;
            }
            else
            {
                //Append the current field filter to its respective where clause, based on whether
                // it's an exclusive or inclusive filter.
                if ($field_is_exclusive)
                {
                    //Only place the $sql_glue after the first filter.
                    $this_glue = ($sql_where_exc == '') ? '' : ' AND ';
                    $sql_where_exc .= $this_glue.$this_where;
                }
                else
                {
                    //Only place the $sql_glue after the first filter.
                    $this_glue = ($sql_where_inc == '') ? '' : ' OR ';
                    $sql_where_inc .= $this_glue.$this_where;
                }
            }
        }
    }

    //Trim SQL filters. Not sure why this is needed.
    $sql_where_exc = trim($sql_where_exc);
    $sql_where_inc = trim($sql_where_inc);
    $sql_where_div = trim($sql_where_div);
    /*
    $fp = fopen('tmp1.txt', 'a+');
    fwrite($fp, 'sql_where_exc:'.$sql_where_exc."\n\n");
    fwrite($fp, 'sql_where_inc:'.$sql_where_inc."\n\n");
    fwrite($fp, 'sql_where_div:'.$sql_where_div."\n\n");
    fclose($fp);
    */

    //Calculate how the two groups of filters (inclusive & exclusive) be claused together,
    // based on $sql_glue and the exclusive & inclusive filter clauses.
    if (($sql_where_exc!='') && ($sql_where_inc!=''))
    {
        $this_glue = ($array_parameters['is_exclusive']==true) ? ' AND ' : ' OR ';
        $sql_where = '(('.$sql_where_exc.')'.$this_glue.'('.$sql_where_inc.'))';
    }
    else if ($sql_where_exc != '')
    {
        //Only exclusive filters were found and created.
        $sql_where = '('.$sql_where_exc.')';
    }
    else if ($sql_where_inc != '')
    {
        //Only inclusive filters were found and created.
        $sql_where = '('.$sql_where_inc.')';
    }
    else
    {
        //Both WHERE clauses were blank. No filters were found, or they weren't valid.
        //Return an empty string.
        $sql_where = '';
    }

    if ($sql_where_div != '')
    {
        if ($sql_where == '')
            $sql_where = $sql_where_div;
        else
            $sql_where = "($sql_where_div) AND ($sql_where)";
    }

    //Done.
    return $sql_where;
}

//This function attempts to find a generic field name in the $field_data array that
// matches the specified generic field name.
//If it finds a match, it returns the local field name for the given field data.
//If no match is found, an empty string is returned.
//$field_data should be an array returned from the get_field_data() function.
function	get_local_name($field_name_generic, $field_data)
{
    foreach($field_data as $field)
    {
        if ($field['object_field_generic_name'] != '' && $field['object_field_generic_name'] == $field_name_generic)
            return $field['object_field_name'];
        else if ($field_name_generic == 'key_field' && $field['object_field_is_key'] == 1)
            return $field['object_field_name'];
    }

    //No field matched.
    return '';
}

//This function attempts to find a field where the given field_sort_column's value matches the given field_sort_value
// within the given field_data array.
//If it finds a match, it returns the field data for the matching field.
//If no match is found, returns NULL.
//$field_data should be an array returned from the get_field_data() function.
function	get_field_data_by_column_value($field_sort_column, $field_sort_value, $field_data)
{
    foreach($field_data as $field)
    {
        if ($field[$field_sort_column] == $field_sort_value)
            return $field;
    }

    //No field column value matched.
    return NULL;
}

//This function attempts to find a generic field name in the $field_data array that
// matches the specified generic field name.
//If it finds a match, it returns the local field name for the given field data.
//If no match is found, an empty string is returned.
//$field_data should be an array returned from the get_field_data() function.
function	get_field_value($local_field_name, $which_value, $field_data)
{
    foreach($field_data as $field)
    {
        if ($field['object_field_name'] == $local_field_name)
            return $field[$which_value];
    }

    //No field matched.
    return '';
}

/*
function	is_virtual_field($local_field_name, $field_data)
{
	foreach($field_data as $field)
	{
		if ($field['object_field_name'] == $local_field_name)
		{
			//Make sure that the value returned here is a boolean type.
			if ($field['object_field_is_virtual'] == true)
				return true;
			else
				return false;
		}
	}
	
	//No field matched.
	//Return true by default. This will ensure that the field must be handled manually, if
	// it is to be handled.
	return true;
}
*/

function	is_virtual_field($local_field_name, $field_data)
{
    return field_has_value($local_field_name, 'object_field_is_virtual', true, true, $field_data);
}

function	is_list_field($local_field_name, $field_data)
{
    return field_has_value($local_field_name, 'object_field_is_list', true, false, $field_data);
}

function	field_has_value($local_field_name, $object_field, $expected_field_value, $default_return_value, $field_data)
{
    foreach($field_data as $field)
    {
        if ($field['object_field_name'] == $local_field_name)
        {
            if ($field[$object_field] == $expected_field_value)
                return true;
            else
                return false;
        }
    }

    //No field matched.
    //Return the default value.
    return $default_return_value;
}

function	get_field_by_name($local_field_name, $field_data)
{
    foreach($field_data as $field)
    {
        if ($field['object_field_name'] == $local_field_name)
            return $field;
    }

    //No field matched.
    //Return an empty array.
    return array();
}

function	is_division_field($local_field_name, $field_data)
{
    foreach($field_data as $field)
    {
        if ($field['object_field_name'] == $local_field_name)
        {
            if ($field['object_field_type_name']=='division_id' || $field['object_field_type_name']=='division_code')
                return true;
            else
                return false;
        }
    }

    //No field matched.
    //Return false by default.
    return false;
}

function	get_field_filter($obj_table_name, $field_name, $field_type, $field_values, $field_data, $sql_type='mssql')
{
    //If $field_values doesn't contain any values, return an empty string.
    if (count($field_values)<1 && $field_type!='In')
        return '';

    //Define escape characters, based on the sql type.
    $escape_chars = get_escape_chars($sql_type);

    //Find out if this field contains a concatenated value in the object_field_list_data_field field.
    $object_field_list_data_field = get_field_value($field_name, 'object_field_list_data_field', $field_data);
    $is_data_concatenated = field_is_concatenated($object_field_list_data_field, $sql_type);

    if ($is_data_concatenated)
    {
        //The developer has manually described this field. Don't alter it.
        $full_field_name = $object_field_list_data_field;
    }
    else
    {
        //Escape the field name, to avoid sql errors with key words.
        $full_field_name = $escape_chars[0].$field_name.$escape_chars[1];

        //Identify to which table each field belongs, so SQL statements don't break with errors about ambiguous column names.
        if ($sql_type == 'iseries')
            $obj_table_name = '';
        else
            $obj_table_name = $escape_chars[0].$obj_table_name.$escape_chars[1].'.';

        //Escape the field name, to avoid sql errors with key words.
        $full_field_name = $obj_table_name.$escape_chars[0].$field_name.$escape_chars[1];

        //If this is a virtual field, filter by the foreign table and field.
        if ( is_virtual_field($field_name,$field_data) )
        {
            $table_name = get_field_value($field_name, 'object_field_list_table_alias', $field_data);
            $foreign_field_name = get_field_value($field_name, 'object_field_list_data_field', $field_data);

            if (($table_name!='') && ($foreign_field_name!=''))
                $full_field_name = $escape_chars[0].$table_name.$escape_chars[1].'.'.$escape_chars[0].$foreign_field_name.$escape_chars[1];
            else
                //No foreign data found. No filter can be created for this virtual field.
                return '';
        }
    }

    //Loop over all values and make sure that quotes are escaped.
    //Also- DB2 queries are case-sensitive, and Jobscope saves everything as uppercase. Make sure all db2 queries are searching with uppercase search values.

    $new_values = array();
    foreach($field_values as $this_field_value)
    {
        if (gettype($this_field_value) === 'string')
            $this_field_value = str_replace("'", "''", $this_field_value);

        if ($sql_type == 'iseries')
            $this_field_value = strtoupper($this_field_value);

        $new_values[] = $this_field_value;
    }
    $field_values = $new_values;

    //NOTE:	The "Contains", "StartsWith" and "EndsWith" types are just specialized versions of a "Like".
    //		They have been added here to make client code simpler and more user-friendly.
    switch($field_type)
    {
        case 'Equal':
            if (gettype($field_values[0]) === "NULL")
                return "($full_field_name IS NULL)";
            else
                return "($full_field_name = '".$field_values[0]."')";
        case 'Like':
            return "($full_field_name LIKE '".$field_values[0]."')";
        case 'Contains':
            return "($full_field_name LIKE '%".$field_values[0]."%')";
        case 'StartsWith':
            if ($sql_type == 'iseries')
                return "(TRIM($full_field_name) LIKE '".$field_values[0]."%')";	//i-Series fields are always CHAR fields that could contain padding at the end.
            else
                return "($full_field_name LIKE '".$field_values[0]."%')";
        case 'EndsWith':
            if ($sql_type == 'iseries')
                return "(TRIM($full_field_name) LIKE '%".$field_values[0]."')";	////i-Series fields are always CHAR fields that could contain padding at the end.
            else
                return "($full_field_name LIKE '%".$field_values[0]."')";
        case 'Between':
        case 'FloatingDate':	//The client will always parse out 2 dates and basically create a "Between" filter out of any "FloatingDate" filters, so they should be handled just like a "Between" on the server-side.
            if (count($field_values) < 2)
                return '';
            else
                return "($full_field_name BETWEEN '".$field_values[0]."' AND '".$field_values[1]."')";
        case 'In':
            //If NULL is one of the values, it must be handled differently than other values.
            $null_index = array_search(NULL,$field_values);
            if ($null_index === false)
            {
                $sql_in = "($full_field_name IN ('";
                $sql_in .= implode("','", $field_values);
                $sql_in .= "'))";
            }
            else
            {
                unset($field_values[$null_index]);
                $sql_in = "($full_field_name IN ('";
                $sql_in .= implode("','", $field_values);
                $sql_in .= "'))";
                $sql_in = "($sql_in OR ($full_field_name IS NULL))";
            }
            return $sql_in;
        case 'GreaterThan':
            return "($full_field_name > '".$field_values[0]."')";
        case 'GreaterThanOrEqualTo':
            return "($full_field_name >= '".$field_values[0]."')";
        case 'LessThan':
            return "($full_field_name < '".$field_values[0]."')";
        case 'LessThanOrEqualTo':
            return "($full_field_name <= '".$field_values[0]."')";
        case 'EqualField':
            if (gettype($field_values[0]) === "NULL")
                return "($full_field_name IS NULL)";
            else
                return "($full_field_name = [".$field_values[0]."])";
        default:
            if (count($field_values) == 1)
                //Assume a 'Like'.
                return "($full_field_name LIKE '".$field_values[0]."')";
            else	//count > 1
            {
                //Assume an 'In'.
                $sql_in = "($full_field_name IN ('";
                $sql_in .= implode("','", $field_values);
                $sql_in .= "'))";
                return $sql_in;
            }
    }
}

function	get_field_conditional_filter($ent_object_data, $field_values, $field_data, $sql_type='mssql')
{
    if (count($field_values) < 1)
        return '';

    $sql_filter = '(';
    foreach($field_values as $this_value)
    {
        $this_value_parsed = parse_sql_condition($this_value, $ent_object_data, $field_data, $sql_type);
        $sql_filter .= '('.$this_value_parsed.') AND ';
    }
    $sql_filter = substr($sql_filter, 0, strlen($sql_filter)-5);	//Get rid of final " AND ".
    $sql_filter .= ')';

    return $sql_filter;
}

function	parse_sql_condition($pseudo_sql, $ent_object_data, $field_data, $sql_type)
{
    //So far, SQL conditions are only allowed to be added to a filter on the server-side. As long as
    // this is true, then we can skip validating the SQL conditions here.

    //Get escape characters for the given SQL syntax type.
    $escape_chars = get_escape_chars($sql_type);

    //Replace any pseudo-syntax with the data it represents.
    //Regular expressions documentation: //http://us.php.net/manual/en/regexp.reference.php

    $parsed_sql = $pseudo_sql;

    //Get a list of all generic field names to be replaced in the pseudo-SQL.
    //(GF = "Generic Field")
    $num_value_matches = preg_match_all('|<<GF:([\D]+?)>>|', $pseudo_sql, $value_matches);
    //Replace any generic field names with local, escaped field names with table aliases.
    if ($num_value_matches && $num_value_matches>0)
    {
        foreach($value_matches[1] as $this_match)
        {
            $this_field = get_field_data_by_column_value('object_field_generic_name', $this_match, $field_data);
            if ($this_field)
            {
                if ($this_field['object_field_is_virtual'])
                {
                    $of_field_name =	$this_field['object_field_list_data_field'];
                    $of_table_name =	$this_field['object_field_list_table_alias'];
                }
                else
                {
                    $of_field_name =	$this_field['object_field_name'];
                    $of_table_name =	$ent_object_data['ent_object_data_table_name'];
                }

                $local_field_name = $escape_chars[0].$of_table_name.$escape_chars[1].'.'.$escape_chars[0].$of_field_name.$escape_chars[1];
                $parsed_sql = str_replace('<<GF:'.$this_match.'>>', $local_field_name, $parsed_sql);
            }
        }
    }
    /*
    $fp = fopen('tmp.txt', 'w+');
    fwrite($fp, "ent_object_name: ".$ent_object_data['ent_object_name']."\n\n");
    fwrite($fp, "pseudo_sql: ".$pseudo_sql."\n\n");
    fwrite($fp, "num_field_matches: ".$num_value_matches."\n\n");
    fwrite($fp, "field_matches: ".var_export($value_matches,true)."\n\n");
    fclose($fp);
    */

    //Get a list of all local field names to be replaced in the pseudo-SQL.
    //(LF = "Local Field")
    $num_value_matches = preg_match_all('|<<LF:([\D]+?)>>|', $pseudo_sql, $value_matches);
    //Replace any local field names with escaped local field names that include table aliases.
    if ($num_value_matches && $num_value_matches>0)
    {
        foreach($value_matches[1] as $this_match)
        {
            $this_field = get_field_data_by_column_value('object_field_name', $this_match, $field_data);
            if ($this_field)
            {
                if ($this_field['object_field_is_virtual'])
                {
                    $of_field_name =	$this_field['object_field_list_data_field'];
                    $of_table_name =	$this_field['object_field_list_table_alias'];
                }
                else
                {
                    $of_field_name =	$this_field['object_field_name'];
                    $of_table_name =	$ent_object_data['ent_object_data_table_name'];
                }


                $local_field_name = $escape_chars[0].$of_table_name.$escape_chars[1].'.'.$escape_chars[0].$of_field_name.$escape_chars[1];
                $parsed_sql = str_replace('<<LF:'.$this_match.'>>', $local_field_name, $parsed_sql);
            }
        }
    }
    /*
    $fp = fopen('tmp.txt', 'a+');
    fwrite($fp, "ent_object_name: ".$ent_object_data['ent_object_name']."\n\n");
    fwrite($fp, "pseudo_sql: ".$pseudo_sql."\n\n");
    fwrite($fp, "num_field_matches: ".$num_value_matches."\n\n");
    fwrite($fp, "field_matches: ".var_export($value_matches,true)."\n\n");
    fclose($fp);
    */

    /*
    //Get a list of all local field id's to be replaced in the pseudo-SQL.
    //(ID = "Local Field ID"- matches object_field_id)
    $num_value_matches = preg_match_all('|<<ID:([\d]+?)>>|', $pseudo_sql, $value_matches);
    //Replace any local field id's with escaped local field names that include table aliases.
    //here!!!
    */

    //Get a list of all user-field values to be replaced in the pseudo-SQL.
    //(UV = "User Value")
    $num_value_matches = preg_match_all('|<<UV:([\D]+?)>>|', $pseudo_sql, $value_matches);
    //Replace any user-field values with values from the logged-in user.
    if ($num_value_matches && $num_value_matches>0)
    {
        foreach($value_matches[1] as $this_match)
        {
            $this_value = get_user_field_value_by_field_name($this_match);
            if ($this_value)
                $parsed_sql = str_replace('<<UV:'.$this_match.'>>', $this_value, $parsed_sql);
        }
    }
    /*
    $fp = fopen('tmp.txt', 'a+');
    fwrite($fp, "ent_object_name: ".$ent_object_data['ent_object_name']."\n\n");
    fwrite($fp, "pseudo_sql: ".$pseudo_sql."\n\n");
    fwrite($fp, "num_field_matches: ".$num_value_matches."\n\n");
    fwrite($fp, "field_matches: ".var_export($value_matches,true)."\n\n");
    fclose($fp);
    */

    //Return the new SQL statement.
    return $parsed_sql;
}

function	get_user_field_value_by_field_name($user_field)
{
    require_once($_SERVER['DOCUMENT_ROOT']."/amfphp/services/inc_files/user_access_functions.php");
    $user_data = get_authenticated_user_data();
    if ( isset($user_data[$user_field]) )
        return $user_data[$user_field];
    else
        return '';
}

function	get_sql_tables($key_table_name, $field_data, $sql_type='mssql')
{
    $escape_chars = get_escape_chars($sql_type);
    $key_table_name = $escape_chars[0].$key_table_name.$escape_chars[1];
    $table_prefix = '';

    if ($sql_type == 'iseries')
    {
        $table_prefix = "<<JS_LIBRARY>>/";
        $key_table_name = strtoupper($key_table_name);
    }

    $sql_tables = $table_prefix.$key_table_name."\n";

    //Keep track of each table alias used. If a table alias is shared among virtual object_field's, then
    // this function expects that the fields were meant to be joined with the same join statement.
    $aliases_used = array();

    //All tables are left-outer-joined, because list data might not exist in the foreign table.
    //This function expects a 1-to-1 relationship for all joins!
    foreach($field_data as $field)
    {
        if ($field['object_field_is_virtual'] == true)
        {
            if ($field['object_field_inner_join'])
                $join_syntax = 'INNER JOIN';
            else
                $join_syntax = 'LEFT OUTER JOIN';

            $list_table_alias_bare = $field['object_field_list_table_alias'];
            if (($list_table_alias_bare!='') && (!in_array($list_table_alias_bare,$aliases_used)))
            {
                if ($field['object_field_custom_join'] != '')
                {
                    $sql_tables .= ' '.$field['object_field_custom_join']."\n";
                }
                else if ($field['object_field_list_table'] != '' && $field['object_field_list_key_field'] != '' && $field['object_field_key_field'] != '')
                {
                    //Only escape the list table if it hasn't been defined from another database (in which case it is assumed it has already been escaped if necessary).
                    $found_pos1 = strpos($field['object_field_list_table'], '.');
                    $found_pos2 = strpos($field['object_field_list_table'], '/');
                    $found_pos3 = strpos($field['object_field_list_table'], '[');
                    $escape_list_table = ($found_pos1 === false && $found_pos2 === false && $found_pos3 === false);

                    //Find out if this field contains a period in the object_field_key_field value, assume that whoever created that value has already done the necessary
                    // appending of the table name and wrapping the field with escape characters.
                    $found_pos = strpos($field['object_field_key_field'], '.');
                    $is_key_escaped = ($found_pos === false) ? false : true;

                    $list_table_alias = $escape_chars[0].$list_table_alias_bare.$escape_chars[1];
                    $list_table_name = ($escape_list_table) ? $table_prefix.$escape_chars[0].$field['object_field_list_table'].$escape_chars[1] : $field['object_field_list_table'];
                    $list_key_field_name = $escape_chars[0].$field['object_field_list_key_field'].$escape_chars[1];
                    $key_field_name = ($is_key_escaped) ? $field['object_field_key_field'] : $key_table_name.'.'.$escape_chars[0].$field['object_field_key_field'].$escape_chars[1];
                    //$additional_tests = (strtolower($field['object_field_data_type'])=='string') ? " AND $list_table_alias.$list_key_field_name<>''" : '';
                    $additional_tests = '';

                    $sql_tables .= ' '.$join_syntax.' '.$list_table_name.' '.$list_table_alias.' ON '.$list_table_alias.'.'.$list_key_field_name.'='.$key_field_name.$additional_tests."\n";
                }
                $aliases_used[] = $list_table_alias_bare;
            }
        }
    }

    return $sql_tables;
}

//This function returns a SQL "SORT BY" clause.
//If sort_fields have been specified, they are used to create the SORT BY clause. Otherwise, if object_fields have been specified, they are used to create a default ORDER BY
// clause. If no sort_fields have been specified and the default sort hasn't been specified, this function returns an empty string.
//sort_fields should be a sort_fields array from an EntParameter object. Each sort_field object should contain a (local) field name and the sort order type (ASC or DESC).
//Fields are sorted in the order they are placed on the array, with index 0 getting sorted first.
function	sz_get_sql_order_by($sort_fields, $object_fields=array())
{
    //Attempt to create an ORDER BY clause, based on the sort_fields array.
    $a_order_by = array();
    foreach($sort_fields as $this_sort)
        $a_order_by[] = $this_sort['fieldName'].' '.$this_sort['orderType'];

    if (count($a_order_by) > 0)
    {
        //Return the ORDER BY created from sort_fields.
        return ' ORDER BY '.implode(',',$a_order_by);
    }
    else
    {
        //No sort_fields were specified.
        //Attempt to create a SORT BY clause by a default sort specified in the object's object_fields.
        $default_sort_fields = array();
        foreach($object_fields as $this_field)
        {
            if ($this_field['object_field_sort_order'] > 0)
            {
                if ( field_is_concatenated($this_field['object_field_list_data_field'],$sql_type) )
                    $this_field_name = $this_field['object_field_list_data_field'];
                else
                    $this_field_name = $this_field['object_field_name'];

                $default_sort_fields[$this_field['object_field_sort_order']] = array	(
                    'fieldName'=>$this_field_name,
                    'orderType'=>$this_field['object_field_sort_type']
                );
            }
        }

        if (count($default_sort_fields) > 0)
        {
            //Sort default_sort_fields by key value, so the fields are in the correct order.
            ksort($default_sort_fields);

            //Create the ORDER BY clause.
            $a_order_by = array();
            foreach($default_sort_fields as $this_sort)
                $a_order_by[] = $this_sort['fieldName'].' '.$this_sort['orderType'];

            return ' ORDER BY '.implode(',',$a_order_by);
        }

        //No default sort (or parameter sort) was specified. Return an empty string with no ORDER BY clause.
        return '';
    }
}

//This used to just tell whether more than one field was specified in field_value, which would mean they needed to be concatenated.
//Now it really is used to tell whether any SQL statement is being used instead of just specifying a single field to get a value.
function	field_is_concatenated($field_value, $sql_type='mssql')
{
    if ($sql_type == 'iseries')
        $a_search_strings = array('||', 'CONCAT', '(', ' ', '-');
    else
        $a_search_strings = array('.', '+', '[', '(', ' ', '-');

    foreach($a_search_strings as $this_search_string)
    {
        $found_pos = strpos($field_value, $this_search_string);
        $is_concatenated_field = !($found_pos === false);
        if ($is_concatenated_field === true)
            return true;
    }

    return false;
}

function	get_escape_chars($sql_type)
{
    if ($sql_type == 'sqlsrv')
        return array('[',']');
    else if ($sql_type == 'mssql')
        return array('[',']');
    else if ($sql_type == 'mysql')
        return array('`','`');
    else
        return array('','');
}
?>