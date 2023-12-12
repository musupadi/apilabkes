<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Marketing_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    public function is_url_exist($url)
    {
	    $ch = curl_init($url);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_NOBODY, true);
	    curl_exec($ch);
	    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	    if($code == 200){
	        $status = true;
	    }else{
	        $status = false;
	    }
	    curl_close($ch);
	    return $status;
	}

    public function getMarketingActList($offset = 0, $limit = 5)
    {
    	$this->db->select('db_admission.marketing_activity.*, db_employees.employees.Name');
		$this->db->from('db_admission.marketing_activity');
		$this->db->join('db_employees.employees', 'db_admission.marketing_activity.CreatedBy = db_employees.employees.NIP');

		$this->db->limit($limit, $offset);

		$this->db->order_by('db_admission.marketing_activity.Start', 'DESC');

		$query = $this->db->get();
		return $query->result_array();
    }

    public function getMarketingActDetail($id_marketing_activity)
    {
    	$this->db->select('db_admission.marketing_activity.*, db_employees.employees.Name');
		$this->db->from('db_admission.marketing_activity');
		$this->db->join('db_employees.employees', 'db_admission.marketing_activity.CreatedBy = db_employees.employees.NIP');

		$this->db->where('db_admission.marketing_activity.ID', $id_marketing_activity);

		$query = $this->db->get();
		return $query->row_array();
    }

}