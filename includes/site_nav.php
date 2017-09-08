<?php
/**
 * Created by PhpStorm.
 * User: sportypants
 * Date: 5/4/17
 * Time: 9:43 PM
 */
class site_nav
{
	public $nav_string;
	public function __construct($nav_string = '')
	{
		$this->nav_string = '';
        $this->nav_string .= '<div class="navbar navbar-default navbar-fixed-top">
                <div class="container">
                <div class="navbar-header">
                    <a href="../kit-db/" class="navbar-brand">Books &amp; Kits</a></div>';
        $this->nav_string .= '<nav>';
        $this->nav_string .= '<ul class="nav navbar-nav" style="display:inline-block;">';
        $this->nav_string .= '<li class="nav-item"><a class="nav-link" href="/kit_db/">Home</a></li>';
        $this->nav_string .= '<li class="nav-item"><a class="nav-link" href="/kit_db/books.php">Books</a></li>';
        $this->nav_string .= '<li class="nav-item"><a class="nav-link" href="http://www.seriouslyfunnyscience.com/kit_db/product_list.php">Products</a></li>';
		$this->nav_string .= '<li class="nav-item"><a class="nav-link disabled" href="http://www.seriouslyfunnyscience.com/workshops">Workshops</a></li>';
		$this->nav_string .= '<li class="nav-item"><a class="nav-link disabled" href="/contact">Contact us</a></li>';
		$this->nav_string .= '</ul>';
		$this->nav_string .= '</nav></div></div>';
	}
	public function __toString()
    {
        return $this->nav_string;
    }
}
?>