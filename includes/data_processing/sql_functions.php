<?php
/**
 * Created by PhpStorm.
 * User: sportypants
 * Date: 4/4/17
 * Time: 10:44 PM
 */
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/globals.php");
require_once($_SERVER['DOCUMENT_ROOT']."/kit_db/includes/connections.php");

//function	sql_pconnect($db_type, $db_name='', $user_logged_in, $options=null)
function	sql_pconnect($user_logged_in, $options=null)
{
    if ($user_logged_in)
    {
        return pdo_connect($db_n);
    }
//    else if ($db_type == 5)
//        return mysql_pconnect('208.109.87.175', 'superme', 'ch1ck3nF1ng3r$', false);
//    else if ($db_type == 'mysql' || $db_type == 2)
//        return new mysqli("home.yesco.com", "root", "ch1ck3nF1ng3r$", $db_name);

    return NULL;
}

?>