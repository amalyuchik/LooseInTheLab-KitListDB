<?php
/**
 * Created by PhpStorm.
 * User: sportypants
 * Date: 4/13/17
 * Time: 10:46 PM
 */

class select_sql
{
    function query($view, $state_id=null, $grade_id=null, $book_id = null)
    {
        $query = '';
        //$notes = mysql_real_escape_string($notes);
        if ($view == 'books' && $state_id!==null)
        {
            //,states.state_abbreviation AS 'State',grades.grade_grade AS 'Grade',books.book_name AS 'Book Name',books.book_notes AS 'Notes', books.book_state_id_fk,books.book_grade_id_fk
            $query = "SELECT book_id, book_grade_id_fk, book_notes, book_state, book_state_id_fk, CONCAT(' Grade ', grades.grade_grade ,' ' ,book_notes) as 'Book Name'  FROM books
                      INNER JOIN grades ON books.book_grade_id_fk = grades.grade_id WHERE book_state_id_fk = $state_id ORDER BY book_name"; // AND book_grade_id_fk = $grade_id
            return $query;
        }
        elseif($view == 'book_contents')//to see the contents of a book
        {
            $query = "SELECT book_lab_line_lab_id_fk AS 'Lab ID',labs.lab_name AS 'Lab Name', book_lab_line_id AS 'Delete'
                        FROM kitliastdb.book_lab_line
                        LEFT JOIN books ON book_lab_line_book_id_fk = book_id
                        LEFT JOIN labs ON book_lab_line_lab_id_fk = lab_id
                        LEFT JOIN states ON  book_state_id_fk = states.state_id
                        LEFT JOIN grades ON book_grade_id_fk = grades.grade_id
                        WHERE book_lab_line_book_id_fk = ".$book_id."
                        ORDER BY book_lab_line_id";
        }
        elseif($view == 'available_state_list') //for book states dropdown
        {
            $query = "SELECT DISTINCT books.book_state_id_fk AS 'ID', states.state_name AS 'State Name', states.state_abbreviation AS 'data' FROM books
                      INNER JOIN states on state_id = book_state_id_fk ORDER BY states.state_abbreviation";
        }
        elseif($view == 'book_name') //for book name in Book Detail
        {
            $query = "SELECT concat(states.state_abbreviation, ' ',grades.grade_grade,' ',books.book_notes) AS 'Book Name' FROM kitliastdb.books
                        LEFT JOIN states ON  book_state_id_fk = states.state_id
                        LEFT JOIN grades ON book_grade_id_fk = grades.grade_id
                        WHERE book_id = ".$book_id;
        }
        elseif($view == 'state_list') //for states dropdown
        {
            $query = "SELECT states.state_id AS 'ID',states.state_name AS 'State Name', states.state_abbreviation AS 'data' FROM states ORDER BY states.state_abbreviation";
        }
        elseif($view == 'grade_list') //for grades dropdown
        {
            $query = "SELECT grades.grade_id AS 'ID', grades.grade_grade AS 'data' FROM grades";
        }
        elseif($view == 'notes')
        {
            $query = "SELECT distinct book_notes AS 'data' FROM kitliastdb.books ORDER BY book_notes ASC";
        }
        elseif($view == 'books')
        {
            //$query = "SELECT distinct book_id AS 'ID', book_name AS 'data' FROM kitliastdb.books ORDER BY book_name ASC";
            $query = "SELECT distinct book_id AS 'ID', CONCAT(book_state,' Grade ', grades.grade_grade, ' ' ,book_notes) AS 'data' FROM kitliastdb.books LEFT JOIN grades ON book_grade_id_fk = grades.grade_id ORDER BY book_name ASC";
        }
        elseif($view == 'lab_types') //for lab types dropdown
        {
            $query = "SELECT lab_type_id AS 'ID', lab_type AS 'data' FROM kitliastdb.lab_types ORDER BY lab_type ASC";
        }
        elseif($view == 'lab_categories') //for lab categories dropdown
        {
            $query = "SELECT lab_category_id AS 'ID', lab_category AS 'data' FROM kitliastdb.lab_categories ORDER BY lab_category ASC";
        }
        elseif($view == 'product_categories') //for product categories dropdown
        {
            $query = "SELECT product_category_id AS 'ID', product_category_name AS 'data' FROM kitliastdb.product_categories ORDER BY product_category_name ASC";
        }
        elseif($view == 'products') //for product dropdown
        {
            $query = "SELECT product_id AS 'ID', product_name AS 'data' FROM kitliastdb.products ORDER BY product_name ASC";
        }
        elseif($view == 'all_labs') //for product categories dropdown
        {
            $query = "SELECT lab_id AS 'ID', lab_name AS 'data' FROM kitliastdb.labs ORDER BY lab_name ASC";
        }
        else
        {
            $query = "SELECT books.book_name AS 'Book Name',books.book_notes AS 'Notes', books.book_state_id_fk,books.book_grade_id_fk,states.state_name,states.state_abbreviation AS 'State',grades.grade_grade AS 'Grade'
                        FROM kitliastdb.book_lab_line
                        LEFT JOIN books ON book_lab_line_book_id_fk = book_id
                        LEFT JOIN states ON  book_state_id_fk = states.state_id
                        LEFT JOIN grades ON book_grade_id_fk = grades.grade_id
                        GROUP BY book_name";
        }

        return $query;
    }

    function edit_query($type,$id,$product_ids='')
    {
        $query = '';
        if($type=='lab')
        {
            $query = "SELECT * FROM labs WHERE lab_id = $id";
        }
        elseif($type=='product')
        {
            $query = "SELECT * FROM products WHERE product_id = $id";
        }
        elseif($type == 'lab_product_ids') //for lab categories dropdown
        {
            $query = "SELECT lab_product_line_product_id_fk FROM kitliastdb.lab_product_line WHERE lab_product_line_lab_id_fk = $id";
        }
        elseif($type == 'lab_products') //for lab categories dropdown
        {
                $query = "SELECT lab_product_line_id,lab_product_line_lab_id_fk,lab_product_line_product_id_fk,products.product_name,lab_product_line_product_category,lab_product_line_product_category_id,lab_product_line_classroom_qty,lab_product_line_participant_qty,lab_product_line_presenter_qty,lab_product_line_refill_qty,lab_product_line_refill_qty_detail,lab_product_line_retail_qty,lab_product_line_retail_qty_detail,lab_product_line_is_reusable,products.product_id,products.product_sku,products.product_description,products.product_manufacturer,products.product_cost,products.product_price
                            FROM kitliastdb.lab_product_line
                            INNER JOIN products ON products.product_id = lab_product_line.lab_product_line_product_id_fk
                            WHERE lab_product_line_product_id_fk IN ($product_ids) and lab_product_line_lab_id_fk = $id ORDER BY lab_product_line_product_category,products.product_name";
        }
        elseif($type == 'product_category') //for product categories in Add product script
        {
            $query = "SELECT 0 AS 'ID', product_category AS 'data' FROM kitliastdb.products WHERE product_id = $id";
        }
        return $query;
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
}
?>