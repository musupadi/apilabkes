<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Students_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function getStudentDetailFront($npm, $year)
    {
    	$this->db->select('Name, Photo');
		$this->db->from('ta_'.$year.'.students AS std');
		$this->db->where('std.NPM', $npm);

		$query = $this->db->get();
		return $query->row_array();
    }
}