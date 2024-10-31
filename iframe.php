<?php
error_reporting(E_ALL & ~E_NOTICE);	
echo '<html>
<base target="_parent" />';
require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
//require_once ($_SERVER['DOCUMENT_ROOT'].'/wp-blog-header.php');
//require_once ($_SERVER['DOCUMENT_ROOT'].'/wp-includes/registration.php');
get_stylesheet();
//get_header( 'home' );

echo '<head>';
if (get_option('super_mega_include_stylesheets'))
{
	wp_head();
}
echo '</head>';
echo '<body style="background: #FFF;">';

$popup_title = get_the_title(get_option('popup_selection'));
$popup_content = get_post_field('post_content', get_option('popup_selection'));

echo '<div id="post">';
echo '<h2>'.$popup_title.'</h2>';
echo $popup_content;

echo '</div><!--post-->';
echo '</body>';
echo '</html>';
?>
