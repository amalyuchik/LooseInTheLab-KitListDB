<?php
/**
 * Created by PhpStorm.
 * User: sportypants
 * Date: 5/4/17
 * Time: 9:43 PM
 */
class site_nav
{
	function __construct()
	{
        echo '<div class="navbar navbar-default navbar-fixed-top">
                <div class="container">
                <div class="navbar-header">
                    <a href="../kit-db/" class="navbar-brand">Books &amp; Kits</a></div>';
		echo '<nav>';
		echo '<ul class="nav navbar-nav" style="display:inline-block;">';
		   echo '<li class="nav-item"><a class="nav-link" href="/kit_db/">Home</a></li>';
		   echo '<li class="nav-item"><a class="nav-link" href="/kit_db/books.php">Books</a></li>';
		   echo '<li class="nav-item"><a class="nav-link" href="http://www.seriouslyfunnyscience.com/workshops/">Workshops</a></li>';
		   echo '<li class="nav-item"><a class="nav-link disabled" href="/contact">Contact us</a></li>';
		echo '</ul>';
		echo '</nav></div></div>';
	}
}
?>


