<?php
/**
 * Nav Helper untuk menambahkan highlighter pada menu yang aktif
 *
 * @author Andy Aliansah <andyaliansah97@gmail.com>
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
function menu_open_master()
{
    $CI = &get_instance();
     
    $class  = $CI->router->fetch_class();
    $master = array("trainees", "companies", "job_titles", "requirements");
	
    return (in_array($class, $master)) ? 'menu-open' : '';
}

function active_link_master()
{
    $CI = &get_instance();
     
    $class  = $CI->router->fetch_class();
    $master = array("trainees", "companies", "job_titles", "requirements");
	
    return (in_array($class, $master)) ? 'active' : '';
}

function active_link($controller)
{
    $CI = &get_instance();
     
    $class = $CI->router->fetch_class();
	
    return ($class == $controller) ? 'active' : '';
}

?>
