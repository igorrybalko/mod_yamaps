<?php 
/**
 * @package mod_yamaps
 * @author Rybalko Igor
 * @version 1.0
 * @copyright (C) 2016 http://wolfweb.com.ua
 * @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 *
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class yaMapsModHelper {
	public static function convert($items){
	    $converted = array();
	    foreach ($items as $key => $item) {
	        foreach ($item as $partkey => $value) {
	            $converted[$partkey][$key] = $value;
	        }
	    }
	    return $converted;
	}
}

