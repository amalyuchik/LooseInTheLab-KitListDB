<?php
/**
 * Created by PhpStorm.
 * User: amalyuchik
 * Date: 7/21/2017
 * Time: 10:33 AM
 */

class book_names_left_menu
{
    var $sql_execute = array();
    var $iteration;

    /**
     * book_names_left_menu constructor.
     * @param array $sql_execute
     * @param int $iteration
     */
    public function __construct(array $sql_execute, $iteration = 0)
    {
        $this->sql_execute = $sql_execute;
        $this->iteration = $iteration;

    }

    function generate_book_name_menu()
    {
        $book_menu_string = '';
        $this->iteration = $this->iteration - $this->iteration;
        foreach ($this->sql_execute as $k => $v)
        {
            $book_menu_string .= "<a class=\"list-group-item\" href=\"" . $_SERVER['PHP_SELF'] . "?book_id=";
            $book_menu_string .= $this->sql_execute[$this->iteration]['book_id'] . "&book_name=".$this->sql_execute[$this->iteration]['Book Name']."\">" . $this->sql_execute[$this->iteration]['Book Name'];
            $book_menu_string .= "</a>";
            $book_menu_string .= "\r\n";
            $this->iteration++;
        }
        return $book_menu_string;
    }
}