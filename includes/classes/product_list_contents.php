<?php
/**
 * Created by PhpStorm.
 * User: amalyuchik
 * Date: 8/1/2017
 * Time: 8:18 AM
 */

class product_list_contents
{
    var $connection_array = array();
    var $product_category_id;
    var $product_category = '';
    var $conn;
    var $select_sql;
    var $do_once = 0;
    var $product_list_contents_html_string = '';

    /**
     * product_list_contents constructor.
     * @param array $connection_array
     * @param $product_category_id
     * @param string $product_category
     * @param $conn
     * @param $select_sql
     * @param int $do_once
     * @param string $product_list_contents_html_string
     */
    public function __construct(array $connection_array, $product_category_id, $product_category, $conn, $select_sql, $do_once, $product_list_contents_html_string)
    {
        $this->connection_array = $connection_array;
        $this->product_category_id = $product_category_id;
        $this->product_category = $product_category;
        $this->conn = $conn;
        $this->select_sql = $select_sql;
        $this->do_once = $do_once;
        $this->product_list_contents_html_string = $product_list_contents_html_string;
        echo $this->generate_product_list();
    }


    function generate_product_list()
    {
        $sql_execute = $this->conn->runconn_sql_execute($this->connection_array, $this->select_sql);
        if($sql_execute !== null)
        {
            $this->product_list_contents_html_string .= "<header><h1>Products in " . $this->product_category . " category</h1></header>";
            $this->product_list_contents_html_string .= "<p>" . count($sql_execute) . " Products in this category.</p>";
            $this->product_list_contents_html_string .= "<table id='book_detail_table' style='border: solid 1px grey;max-width: 700px;'>";
            $this->product_list_contents_html_string .= "<tr> ";
                    $this->product_list_contents_html_string .= "<th style='padding-top: 5px;padding-bottom: 5px;' class=\"table_header\">Product Id</th>";
                    $this->product_list_contents_html_string .= "<th style='padding-top: 5px;padding-bottom: 5px;' class=\"table_header\">Product Name/SKU</th>";
                    $this->product_list_contents_html_string .= "<th style='padding-top: 5px;padding-bottom: 5px;' class=\"table_header\">Cost</th>";
                    $this->product_list_contents_html_string .= "<th style='padding-top: 5px;padding-bottom: 5px;' class=\"table_header\">Price</th>";
//            foreach ($sql_execute[0] as $k => $v)
//            {
//                if ($this->do_once < count($sql_execute[0]))
//                {
//                }
//                else
//                    continue;
//
//                $this->do_once++;
//            }
            $this->product_list_contents_html_string .= "</tr> ";
            $this->do_once = $this->do_once - $this->do_once;
            $this->product_list_contents_html_string .= "<tr id=\"top_row\" class=\"blank_white_row\" style='border-bottom:1px solid lightgrey;'>";
            foreach ($sql_execute as $k => $v) {
                $compound_product_name = $sql_execute[$this->do_once]['product_sku'] == '' ? $sql_execute[$this->do_once]['product_name'] : $sql_execute[$this->do_once]['product_name'] . " - " . $sql_execute[$this->do_once]['product_sku'];
                $this->product_list_contents_html_string .= "<tr><td style=\"padding:5px;border-bottom:1px solid lightgrey;text-align: center;\">";
                $this->product_list_contents_html_string .= "<a href=\"" . $this->base_url . "product_edit.php?product_id=";
                $this->product_list_contents_html_string .= $sql_execute[$this->do_once]['product_id'] . "\" target=\"_blank\" >" . $sql_execute[$this->do_once]['product_id'] . "</a>";
                $this->product_list_contents_html_string .= "</td><td style=\"padding:5px;border-bottom:1px solid lightgrey;min-width: 300px;\">";
                $this->product_list_contents_html_string .= "<a href=\"" . $this->base_url . "product_edit.php?product_id=" . $sql_execute[$this->do_once]['product_id'] . "\" target=\"_blank\"  >" . $compound_product_name . "</a>";
                $this->product_list_contents_html_string .= "</td>";
                $this->product_list_contents_html_string .= "<td style=\"padding:5px;border-bottom:1px solid lightgrey;text-align: center;\">" . $sql_execute[$this->do_once]['product_cost'] . "</td>";
                $this->product_list_contents_html_string .= "<td style=\"padding:5px;border-bottom:1px solid lightgrey;text-align: center;\">" . $sql_execute[$this->do_once]['product_price'] . "</td>";

                $this->do_once++;
            }

            $this->product_list_contents_html_string .= "</table>";
        }
        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/kit_db/includes/classes/tmp-e.txt", 'w+');
        fwrite($fp, $this->product_list_contents_html_string . "<--- String\n\n");
        fclose($fp);

            return $this->product_list_contents_html_string;
    }
}