<?php
/**
 * Created by PhpStorm.
 * User: sportypants
 * Date: 4/14/17
 * Time: 1:14 AM
 */
class TableRows extends RecursiveIteratorIterator {
    function __construct($it) {
        parent::__construct($it, self::LEAVES_ONLY);
    }

    function current() {
        return "<td style=\"padding-left:5px;padding-right:5px;border:1px solid black;\">" . parent::current(). "</td>";
    }

    function beginChildren() {
        echo "<tr>";
    }

    function endChildren() {
        echo "</tr>" . "\n";
    }
}
class TableHeads extends RecursiveIteratorIterator {
    function __construct($it) {
        parent::__construct($it, self::LEAVES_ONLY);
    }

    function current() {
        return "<th style=\"padding:10;border:1px solid black;font-weight: bold;\">" . parent::current(). "</th>";
    }

    function beginChildren() {
        echo "<tr style=\"background-color: aquamarine;\">";
    }

    function endChildren() {
        echo "</tr>" . "\n";
    }
}
?>