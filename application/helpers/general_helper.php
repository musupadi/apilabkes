<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

function get_ext($data)
{
	$array = explode(".",$data);

	$lastKey = key(array_slice($array, -1, 1, true));
	return $array[$lastKey];
}