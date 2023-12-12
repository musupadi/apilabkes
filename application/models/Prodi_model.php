<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Prodi_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function getProdiList()
    {
    	$this->db->select('ID, NameEng');
		$this->db->from('db_academic.program_study');
		$this->db->where_not_in('db_academic.program_study.StatusAdmisi', array(0));

		$this->db->order_by('db_academic.program_study.NameEng', 'ASC');

		$query = $this->db->get();
		return $query->result_array();
    }

    public function getProdiDetail($id_prodi)
    {
    	$this->db->select('Host, FileLogo, FileLogoP');
		$this->db->from('db_academic.program_study_detail');
		$this->db->where('db_academic.program_study_detail.ProdiID', $id_prodi);

		$query = $this->db->get();
		return $query->row_array();
    }

    public function getProdiSlider($id_prodi)
    {
    	$this->db->select('Images');
		$this->db->from('db_prodi.slider');
		$this->db->where('db_prodi.slider.ProdiID', $id_prodi);

		$query = $this->db->get();
		return $query->result_array();
    }
    
    public function getProdiAbout($id_prodi, $lang = 'EN')
    {
    	$this->db->select('*');
		$this->db->from('db_prodi.prodi_texting');
		$this->db->where('db_prodi.prodi_texting.ProdiID', $id_prodi);
		$this->db->where('db_prodi.prodi_texting.Type', 'about');

		if ($lang == 'EN') 
		{
			$this->db->where('db_prodi.prodi_texting.LangID', 1);
		}
		else
		{
			$this->db->where('db_prodi.prodi_texting.LangID', 2);
		}

		$query = $this->db->get();
		return $query->row_array();
    }

    public function getProdiHost($id_prodi)
    {
    	$this->db->select('Host');
		$this->db->from('db_academic.program_study_detail');
		$this->db->where('db_academic.program_study_detail.ProdiID', $id_prodi);

		$query = $this->db->get();
		return $query->row_array();
    }

    public function prodiLecturer($id_prodi)
    {
    	$this->db->select('db_prodi.lecturer.*, db_employees.employees.Name');
		$this->db->from('db_prodi.lecturer');
		$this->db->join('db_employees.employees', 'db_prodi.lecturer.NIP = db_employees.employees.NIP');
		$this->db->where('db_prodi.lecturer.ProdiID', $id_prodi);

		$query = $this->db->get();
		return $query->result_array();
    }

    public function prodiTestimoni($id_prodi)
    {
    	$this->db->select('std_test.*, test_detail.IDProdiTexting, test_text.Description');
		$this->db->from('db_prodi.student_testimonials AS std_test');
		$this->db->join('db_prodi.student_testimonials_details AS test_detail', 'std_test.ID = test_detail.IDStudentTexting');

		$this->db->join('db_prodi.prodi_texting AS test_text', 'test_detail.IDProdiTexting = test_text.ID');
		$this->db->where('std_test.ProdiID', $id_prodi);

		$query = $this->db->get();
		return $query->result_array();
    }
}