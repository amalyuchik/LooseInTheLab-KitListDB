<?php
require_once($_SERVER['DOCUMENT_ROOT']."/amfphp/services/inc_files/sql_functions.php");
//  Testing Komodo upload feature
class processing_service
{

    public function clock_out_event_check($emp_number)
    {
        $conn = sql_pconnect('sqlsrv', 'YescoApps');
        $search_uid = $emp_number['field_list'][0]['values'][0];
        $sql_select = "SELECT TOP 1 field_service_labor_id FROM YescoApps.dbo.field_service_labor WHERE field_service_labor_empnum = '$search_uid' AND field_service_labor_start is not NULL AND field_service_labor_end is NULL ORDER BY field_service_labor_start DESC";

        $result = sql_query_one_row($conn, $sql_select);

        $row = $result;
        if ($result !== false)
            $clock_info_id = $row;
        {
            if ($clock_info_id && $clock_info_id !== null) {
                $clock_out_exists = false;
            } else
                $clock_out_exists = true;
        }
        return $clock_info_id;
    }

    public function validate_file_name_vs_record_number($keyvalue, $resource_table, $object_id, $db_type, $division_id)
    {

        /*
                echo($keyvalue."-".$resource_table."-".$object_id."-".$db_type."-".$division_id);
        */

        $validation = true;
        if ($db_type == 'sqlsrv') {
            $titan_conn = sql_pconnect('sqlsrv', 'YescoApps');

            $sql_key_select = "SELECT object_field_name FROM YescoApps.dbo.object_field WHERE object_field_object_id = $object_id AND object_field_is_key = 1";

            $titan_row = sql_query_one_row($titan_conn, $sql_key_select);

            $sql_select = "SELECT * FROM YescoApps.dbo.$resource_table WHERE " . $titan_row['object_field_name'] . " = $keyvalue";


            $result = sql_query_one_row($titan_conn, $sql_select);

            if (count($result) == 0)
                $validation = false;
        } elseif ($db_type == 'iseries') {
            if ($division_id == 1)
                $this_library = 'JBSSLCDB';
            elseif ($division_id == 2)
                $this_library = 'JBSOGDDB';
            elseif ($division_id == 3)
                $this_library = 'JBSBOIDB';
            elseif ($division_id == 4)
                $this_library = 'JBSLASDB';
            elseif ($division_id == 5)
                $this_library = 'JBSRNODB';
            elseif ($division_id == 6)
                $this_library = 'JBSPHXDB';
            elseif ($division_id == 7)
                $this_library = 'JBSDENDB';
            elseif ($division_id == 8)
                $this_library = 'JBSONTDB';
            elseif ($division_id == 9)
                $this_library = 'JBSELEDB';
            elseif ($division_id == 10)
                $this_library = 'JBSCORPDB';
            elseif ($division_id == 99)
                $this_library = 'JBSODMDB';

            $titan_conn = sql_pconnect('sqlsrv', 'YescoApps');
            $sql_select = "SELECT object_field_name FROM YescoApps.dbo.object_field WHERE object_field_object_id = $object_id AND object_field_is_key = 1";
            $titan_row = sql_query_one_row($titan_conn, $sql_select);

            $conn = sql_pconnect('iseries');

            $sql_str = "SELECT * FROM " . $this_library . "/" . $resource_table . " WHERE " . $titan_row['object_field_name'] . " = '" . $keyvalue . "'";

            $result = iseries_execute($conn, $sql_str);

            if (!$result)
                $validation = false;
        }

        return $validation;
    }

    public function total_weekly_hours($emp_num, $saturday)
    {
        $conn = sql_pconnect('sqlsrv', 'YescoApps');
        $sql_select = "select SUM(field_service_labor_total_hours) total_hours from YescoApps.dbo.field_service_labor where field_service_labor_date_performed >= '$saturday' and field_service_labor_empnum = '$emp_num' and field_service_labor_type <> 'ATTENDANCE'";

        $result = sql_query_one_row($conn, $sql_select);

        if ($result)
            $row = $result;

        $hours_total = $row['total_hours'];

        return $hours_total;
    }

    public function commit_timecards_to_jobscope($timecard_data_array)
    {
        if (isset($_SESSION['wb_user_info']))
            $initials = substr($_SESSION['wb_user_info']['user_first_name'], 0, 1) . substr($_SESSION['wb_user_info']['user_last_name'], 0, 1);
        else
            $initials = "??";

        //Authenticate user by session to see if they have access to process mileage reimbursments very first.
        require_once($_SERVER['DOCUMENT_ROOT'] . "/amfphp/services/inc_files/user_access_functions.php");
        $a_user_data = get_authenticated_user_data();
        $a_user_access = ua_user_has_access($a_user_data['user_id'], 5, 310);    //batch processing privilege = 5, object_id 310 = Timecards/field_service_labor
        if (!isset($a_user_access[0]) || !isset($a_user_access[0]['Success']) || !$a_user_access[0]['Success'])
            return 'Error: You do not have access to commit timecards to Jobscope.';

        //Establish connections.
        $sql_conn = sql_pconnect('sqlsrv', 'YescoApps');
        $j = 0;

        $db1_connection = sql_pconnect('iseries');
        $sql_subselect = "SELECT * FROM jbsslcdb/tptblls WHERE TABLE_CODE_TL = '9003PR'";
        $db2_sub_result = sql_query_rows('iseries', $db1_connection, $sql_subselect);
        $sub_row = $db2_sub_result[0];

        sql_close('iseries', $db1_connection);

        $result_array = array('error_array' => array(),
            'success_array' => array());
        $division_id = "";
        if ($sub_row['TABLE_STATUS_TL'] == "YES") {
            try {
                for ($i = 0; $i < count($timecard_data_array); $i++) {
                    if ($division_id == "" || $division_id != $timecard_data_array[$i]['field_service_labor_division_id']) {
                        if ($db2_connection) {

                            sql_close('iseries', $db2_connection);
                        }

                        //Select Jobscope library and an appropriate username.
                        if ($timecard_data_array[$i]['field_service_labor_division_id'] == 1) {
                            $this_library = 'JBSSLCDB';
                            $jbs_username = 'DBCONNECT1';
                            $jbs_location_code = 11;
                        } elseif ($timecard_data_array[$i]['field_service_labor_division_id'] == 2) {
                            $this_library = 'JBSOGDDB';
                            $jbs_username = 'DBCONNECT2';
                            $jbs_location_code = 21;
                        } elseif ($timecard_data_array[$i]['field_service_labor_division_id'] == 3) {
                            $this_library = 'JBSBOIDB';
                            $jbs_username = 'DBCONNECT3';
                            $jbs_location_code = 31;
                        } elseif ($timecard_data_array[$i]['field_service_labor_division_id'] == 4) {
                            $this_library = 'JBSLASDB';
                            $jbs_username = 'DBCONNECT4';
                            $jbs_location_code = 41;
                        } elseif ($timecard_data_array[$i]['field_service_labor_division_id'] == 5) {
                            $this_library = 'JBSRNODB';
                            $jbs_username = 'DBCONNECT5';
                            $jbs_location_code = 51;
                        } elseif ($timecard_data_array[$i]['field_service_labor_division_id'] == 6) {
                            $this_library = 'JBSPHXDB';
                            $jbs_username = 'DBCONNECT6';
                            $jbs_location_code = 61;
                        } elseif ($timecard_data_array[$i]['field_service_labor_division_id'] == 7) {
                            $this_library = 'JBSDENDB';
                            $jbs_username = 'DBCONNECT7';
                            $jbs_location_code = 71;
                        } elseif ($timecard_data_array[$i]['field_service_labor_division_id'] == 8) {
                            $this_library = 'JBSONTDB';
                            $jbs_username = 'DBCONNECT8';
                            $jbs_location_code = 81;
                        } elseif ($timecard_data_array[$i]['field_service_labor_division_id'] == 9) {
                            $this_library = 'JBSELEDB';
                            $jbs_username = 'DBCONNECT9';
                        } elseif ($timecard_data_array[$i]['field_service_labor_division_id'] == 10) {
                            $this_library = 'JBSCORPDB';
                            $jbs_username = 'DBCONNECTX';
                            $jbs_location_code = 71;
                        } elseif ($timecard_data_array[$i]['field_service_labor_division_id'] == 99) {
                            $this_library = 'JBSODMDB';
                            $jbs_username = 'DBCONNECT';
                        } elseif (isset($_SESSION) && isset($_SESSION['wb_user_info']) && $_SESSION['wb_user_info']['user_id'] == 2735) {
                            $this_library = 'JBSTESTDB';
                            $jbs_username = 'DBCONNECT';
                            $jbs_location_code = 71;
                        } else {
                            $this_library = 'JBSTESTDB';
                            $jbs_username = 'DBCONNECT';
                            $jbs_location_code = 71;
                        }

                        $db2_connection = i5_connect('207.92.85.253', $jbs_username, 'blahblah'); //leave like this because we're connecting with different username

                        $division_id = $timecard_data_array[$i]['field_service_labor_division_id'];

                    }
                    $sql_select_location = '';
                    $db3_result = null;
                    $row_location = null;

                    $sql_select_location = "SELECT LOCATION_CODE_EM, EMPLOYEE_NUMBER_EM FROM $this_library/PPEMPLM WHERE RTRIM(EMPLOYEE_NUMBER_EM) LIKE '%" . ltrim($timecard_data_array[$i]['field_service_labor_empnum'], 0) . "' AND DATE_TERMINATED_EM = 0";

                    $db3_result = sql_query_rows('iseries', $db2_connection, $sql_select_location);

                    if (count($db3_result) > 1) {
                        foreach ($db3_result as $detail_result) {
                            if ($detail_result['EMPLOYEE_NUMBER_EM'] * 1) {
                                $row_location = $detail_result;
                                //$location_code = $row_location['LOCATION_CODE_EM'];
                                //Employee Zeros workaround. Get the employee number the way it is in JobScope while getting their location code
                                $employee_number_zeros_workaround = $row_location['EMPLOYEE_NUMBER_EM'];
                            }
                        }
                    } else {
                        $row_location = $db3_result[0];
                        $employee_number_zeros_workaround = $row_location['EMPLOYEE_NUMBER_EM'];
                    }

                    if (isset($_SESSION) && isset($_SESSION['wb_user_info']) && $_SESSION['wb_user_info']['user_id'] == 2735) {
                        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/amfphp/services/YescoApps/timecard_log/tmp_e.txt", 'w+');
                        //fwrite($fp, var_export($event_data,true)."\n\n");
                        fwrite($fp, $employee_number_zeros_workaround . "<--- Query\n\n");
                        //fwrite($fp, var_export($event_detail,true)."\n\n\n\n");
                        fclose($fp);
                    }

                    //Insert the records one by one.
                    if ($timecard_data_array[$i]['field_service_labor_timecard_type'] == 1) //Job Charge
                    {
                        //This block is to try and alleviate the date jumps on committing data to jobscope.
                        /*$fz = fopen('andrei_tmp.txt', 'a+');
                        $date_ = new DateTime(date('m/d/Y H:i:s', $timecard_data_array[$i]['field_service_labor_date_performed']/1000));
                        $date_nt = date('Ymd H:i:s', $timecard_data_array[$i]['field_service_labor_date_performed']/1000); //Not transformed date for comparison
                        $date_->setTime(00, 00, 00);
                        //$date_->format('Ymd H:i:s');
                        fwrite($fz, $date_nt . " Converts --> \n" . $date_->format('Ymd H:i:s') . "\n\n");
                        fclose($fz);*/

                        $other_payment = ($timecard_data_array[$i]['field_service_labor_other_pay_amount']) ? $timecard_data_array[$i]['field_service_labor_other_pay_amount'] : 0;

                        $sql_select = "INSERT INTO $this_library/XPLABOR VALUES(" . date('Ymd', $timecard_data_array[$i]['field_service_labor_date_performed'] / 1000) . ",
																						 '" . strtoupper($timecard_data_array[$i]['field_service_labor_shift']) . "',
																						 '" . $employee_number_zeros_workaround . "',
																						 '" . strtoupper($timecard_data_array[$i]['field_service_labor_job_number']) . "',
																						 '',
																						 '',
																						 '" . rtrim(ltrim(strtoupper($timecard_data_array[$i]['field_service_labor_work_center']))) . "',
																						 '',
																						 '',
																						 0,
																						 " . $timecard_data_array[$i]['field_service_labor_total_hours'] . ",
																						 '" . strtoupper($timecard_data_array[$i]['field_service_labor_ot_code']) . "',
																						 " . $other_payment . ",
																						 '',
																						 '',
																						 '" . $jbs_location_code/*strtoupper($timecard_data_array[$i]['field_service_labor_user_info_location_code'])*/ . "',
																						 '" . $employee_number_zeros_workaround . "',
																						 '" . $employee_number_zeros_workaround . "',
																						 '')";
                    } elseif ($timecard_data_array[$i]['field_service_labor_timecard_type'] == 2) //Operation Charge
                    {
                        $other_payment = ($timecard_data_array[$i]['field_service_labor_other_pay_amount']) ? $timecard_data_array[$i]['field_service_labor_other_pay_amount'] : 0;

                        $sql_select = "INSERT INTO $this_library/XPLABOR VALUES(" . date('Ymd', $timecard_data_array[$i]['field_service_labor_date_performed'] / 1000) . ",
																						 '" . strtoupper($timecard_data_array[$i]['field_service_labor_shift']) . "',
																						 '" . $employee_number_zeros_workaround . "',
																						 '',
																						 '',
																						 '',
																						 '99',
																						 '',
																						 '" . rtrim(ltrim($timecard_data_array[$i]['field_service_labor_operation'])) . "',
																						 0,
																						 " . $timecard_data_array[$i]['field_service_labor_total_hours'] . ",
																						 '" . strtoupper($timecard_data_array[$i]['field_service_labor_ot_code']) . "',
																						 " . $other_payment . ",
																						 '',
																						 '',
																						 '" . $jbs_location_code/*strtoupper($timecard_data_array[$i]['field_service_labor_user_info_location_code'])*/ . "',
																						 '" . $employee_number_zeros_workaround . "',
																						 '" . $employee_number_zeros_workaround /*ltrim(strtoupper($timecard_data_array[$i]['field_service_labor_empnum']), 0)*/ . "',
																						 '')";

                    }

                    $db2_insert_result = iseries_execute($db2_connection, $sql_select);
                    if ($sql_select != '' || $sql_select) {
                        $sql_select = '';
                        $detail_status_results = null;
                        $detail_row = null;
                    }
                    //Check status fields on detail record(s) to verify the trigger worked.
                    $sql_query = "SELECT STATUS_XL FROM $this_library/XPLABOR WHERE DATE_PERFORMED_XL = " . date('Ymd', $timecard_data_array[$i]['field_service_labor_date_performed'] / 1000) . " AND RELEASE_XL = '" . strtoupper($timecard_data_array[$i]['field_service_labor_job_number']) . "' AND EMPLOYEE_NUMBER_XL = '" . $employee_number_zeros_workaround . "' AND HOURS_WORKED_XL = " . $timecard_data_array[$i]['field_service_labor_total_hours']; //";  //

                    $detail_status_results = sql_query_rows('iseries', $db2_connection, $sql_query);
                    $detail_row = $detail_status_results[0];

                    if ($detail_row && $detail_row['STATUS_XL'] != '0000') {
                        if ($detail_row && $detail_row['STATUS_XL'] != '9999') {
                            $error_msg = '';
                            $sql_select = "SELECT * FROM js_error WHERE js_error_code = " . "'LAB" . $detail_row['STATUS_XL'] . "'";

                            $error_msg_results = sql_query_rows('', $sql_conn, $sql_select);
                            foreach ($error_msg_results as $detail_row) //while($detail_row = mssql_fetch_assoc($error_msg_results))
                                $error_msg = 'Error code: ' . $detail_row['js_error_code'] . ' - Error message:' . $detail_row['js_error_description'];

                            $mssql_select = "UPDATE field_service_labor SET field_service_labor_jobscope_submission = '" . $error_msg . "' WHERE field_service_labor_id = " . $timecard_data_array[$i]['field_service_labor_id'];

                            //error_log($mssql_select);

                            $sql_result = sql_execute($sql_conn, $mssql_select);

                            array_push($result_array['error_array'], $timecard_data_array[$i]);

                            $iseries_sql_delete = "DELETE FROM $this_library/XPLABOR";
                            $delete_status_results = iseries_execute($db2_connection, $iseries_sql_delete);

                        } else
                            $error_msg = 'Error message: LAB9999 - Date is out of range.';
                        array_push($result_array['error_array'], $timecard_data_array[$i]);
                        $iseries_sql_delete = "DELETE FROM $this_library/XPLABOR";
                        $delete_status_results = iseries_execute($db2_connection, $iseries_sql_delete);

                        //return $error_msg;
                    } else if ($detail_row && $detail_row['STATUS_XL'] == '0000') {
                        $iseries_sql_delete = "DELETE FROM $this_library/XPLABOR";
                        $delete_status_results = iseries_execute($db2_connection, $iseries_sql_delete);

                        $mssql_select = "UPDATE field_service_labor SET field_service_labor_date_submitted_to_jobscope = getdate(),  field_service_labor_jobscope_submission = 'Submission Complete' WHERE field_service_labor_id = " . $timecard_data_array[$i]['field_service_labor_id'];
                        //error_log($mssql_select);

                        $sql_result = sql_execute($sql_conn, $mssql_select);
                        array_push($result_array['success_array'], $timecard_data_array[$i]);
                    }

                }
                if ($db2_connection)
                    sql_close('iseries', $db2_connection);

                return $result_array;
            } catch (Exception $e) {

                $error_msg = $e->getMessage();
                $error_msg .= 'Error on line ' . $e->getLine() . ' in ' . $e->getFile();

                throw new Exception('Query failed with message: ' . $error_msg . ' - The SQL Statement: ' . $sql_select . ' failed.');
            }
        } else
            return 'Error: Payroll table has been locked. Please try again later.';

    }
}

?>