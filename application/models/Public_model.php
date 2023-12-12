<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Public_model extends CI_Model{
    protected $_table_name;
    protected $_order_by;
    protected $_order_by_type;
    protected $_primary_filter;
    protected $_primary_key;
    protected $_type ; 
    public $rules;
    
    function __construct(){
        parent::__construct();
    }

    public function GetTimestamp(){
        $tz = 'Asia/Jakarta';
        $dt = new DateTime("now", new DateTimeZone($tz));
        $timestamp = $dt->format('Y-m-d G:i:s');

        return $timestamp;
    }
    public function GetInvalidAPIKey(){
        return "Wrong Api Key";
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
    public function CheckAuth($key){
        $this->db->select("*");
        $this->db->from("db_it.public_key");
        $this->db->where(array(
            'key' => $key
        ));
        return $data = $this->db->get()->result_array();
    }
    public function ProdiTexting($prodi,$lang,$type){
        $this->db->select("*");
        $this->db->from("db_prodi.prodi_texting");
        $this->db->where(array(
            'ProdiID' => $prodi,
            'LangID' => $lang,
            'Type' => $type
        ));
        return $data = $this->db->get()->result_array();
    }
    public function Testimonial($prodi){
        //db.prodi.student_testimonials as a
        //db.prodi.prodi_texting as b
        //db.prodi.student_testimonials_detail as c
        if($prodi){
            $this->db->select("a.NPM,a.Photo,b.Description");
            $this->db->from("db_prodi.student_testimonials as a");
            $this->db->join("db_prodi.student_testimonials_details as c","a.ID = c.IDStudentTexting","right");
            $this->db->join("db_prodi.prodi_texting as b","b.ID = c.IDProdiTexting");
            $this->db->where(array(
                'b.ProdiID' => $prodi,
                'b.Type' => 'testimonials'
            ));
        }else{
            $this->db->select("a.NPM,a.Photo,b.Description");
            $this->db->from("db_prodi.student_testimonials as a");
            $this->db->join("db_prodi.student_testimonials_details as c","a.ID = c.IDStudentTexting","right");
            $this->db->join("db_prodi.prodi_texting as b","b.ID = c.IDProdiTexting");
            $this->db->where(array(
                'b.Type' => 'testimonials'
            ));
        }
        return $data = $this->db->get()->result_array();
    }
    public function KepalaProdi($prodi){
        //db.academic.program_study a
        //db_employees.employees b
        //db_prodi.prodi_sambutan c
        if($prodi){
            $this->db->select('a.Name as ProdiName,a.NameEng as ProdiNameEng,a.Degree,a.TitleDegree,a.DegreeEng,a.TitleDegreeEng,c.Photo,b.Name,b.TitleAhead,b.TitleBehind,b.NIP');
            $this->db->from('db_academic.program_study as a');
            $this->db->join('db_employees.employees as b','a.KaprodiID = b.NIP','left');
            $this->db->join('db_prodi.prodi_sambutan as c','a.ID = c.ProdiID','left');
            $this->db->where(array(
                'a.ID' => $prodi));
        }else{
            $this->db->select('a.Name as ProdiName,a.NameEng as ProdiNameEng,a.Degree,a.TitleDegree,a.DegreeEng,a.TitleDegreeEng,c.Photo,b.Name,b.TitleAhead,b.TitleBehind,b.NIP');
            $this->db->from('db_academic.program_study as a');
            $this->db->join('db_employees.employees as b','a.KaprodiID = b.NIP','left');
            $this->db->join('db_prodi.prodi_sambutan as c','a.ID = c.ProdiID','left');
        }
        return $data = $this->db->get()->result_array();
    }
    public function Login($username,$password){
        //db.admission.guest a
        $this->db->select('*');
        $this->db->from('user');
        $this->db->where(array(
            'username' => $username,
            'password' => $password
        ));
        return $data = $this->db->get()->result_array();
    }
    public function Version($ID){
        if($ID){
            $this->db->select('a.Name,a.Link,b.Description,max(b.Version) as Version');
            $this->db->from('db_it.app_version a');
            $this->db->join('db_it.detail_app_version b','a.ID = b.ID_App','left');
            $this->db->where(array(
                'a.ID' => $ID
            ));
            $this->db->group_by("a.Name");
        }else{
            $this->db->select('a.Name,a.Link,b.Description,max(b.Version) as Version');
            $this->db->from('db_it.app_version a');
            $this->db->join('db_it.detail_app_version b','a.ID = b.ID_App','left');
            $this->db->group_by("a.Name");
        }
        return $data = $this->db->get()->result_array();
    }
    public function GetDetailVersion($ID){
            $this->db->select('a.Name,a.Link,b.Description,b.Version');
            $this->db->from('db_it.app_version a');
            $this->db->join('db_it.detail_app_version b','a.ID = b.ID_App','left');
            $this->db->where(array(
                'a.ID' => $ID
            ));
            $this->db->order_by('b.ID','DESC');
            return $data = $this->db->get()->result_array();
    }
    public function Lecturer($prodi)
    {
        //db_prodi.lecturer = a
        //db_employees.employees = b
        //db_academic.program_study = c
        if($prodi){
            $this->db->select('a.NIP,a.photo as gambar_dosen,b.Name,b.TitleAhead,b.TitleBehind,c.Name as ProdiName,c.NameEng as ProdiNameEng');
            $this->db->from('db_prodi.lecturer as a');
            $this->db->join('db_employees.employees as b','a.NIP = b.NIP','left');
            $this->db->join('db_academic.program_study as c','a.ProdiID = c.ID');
            $this->db->where(array('a.ProdiID' => $prodi));
        }else{
            $this->db->select('a.NIP,a.photo as gambar_dosen,b.Name,b.TitleAhead,b.TitleBehind,c.Name as ProdiName,c.NameEng as ProdiNameEng');
            $this->db->from('db_prodi.lecturer as a');
            $this->db->join('db_employees.employees as b','a.NIP = b.NIP','left');
            $this->db->join('db_academic.program_study as c','a.ProdiID = c.ID');
        }
        $data = $this->db->get()->result_array();
        return $data;

    }
    public function ShowFeedback($Limit){
        if($Limit){
            $this->db->select('a.Feedback,b.Name,a.CreatedAt');
            $this->db->from('db_it.feedback as a');
            $this->db->join('db_admission.guest as b','a.ID_Guest = b.ID','left');
            $this->db->limit($Limit);
        }else{
            $this->db->select('a.Feedback,b.Name,a.CreatedAt');
            $this->db->from('db_it.feedback as a');
            $this->db->join('db_admission.guest as b','a.ID_Guest = b.ID','left');
        }
        $data= $this->db->get()->result_array();
        return $data;
    }
    public function GetPrivacy($ID){
        $this->db->select('a.Name,b.Name as AplicationName,a.Link,a.CreatedAt');
        $this->db->from('privacy_apps as a');
        $this->db->join('app_version as b','a.ID_Version = b.ID','left');
        $this->db->where(array('a.ID_Version' => $ID));
        $data = $this->db->get()->result_array();
        return $data;
    }
	public function GetLaporanAll(){
		//a = Pasien b = Laporan c=Hematologi d=Kimia Darah e=Urinalisa f=sedimen g=Immunoserologi
		$this->db->select('
		a.id_pasien,a.nama,a.nrp,a.nip,a.kesatuan,a.tanggal_lahir,a.tempat_lahir,a.jenis_kelamin,
		b.id_laporan,b.hematologi,b.kimiadarah,b.immunoserologi,b.sedimen,b.urinalisa,
		c.id_hematologi,c.hematokrit,c.hemogoblin,c.eritrosit,c.led,c.lekosit,c.trombosit,c.pemeriksa,c.tanggal_pemeriksaan,c.no_lab,
		d.id_kimiadarah,bilurubintotal,d.bilurubinindirect,d.bilurubinindirect,d.sgot,d.spgt,d.cholesteroltotal,d.cholesterolldl,d.cholesterolhdl,d.trigiliserida,d.ureum,d.asamurat,d.guladarahpuasa,d.guladarah2jam,d.guladarahsewaktu,
		e.id_urinalisa,e.warna,e.beratjenis,e.ph,e.lekosit,e.nitrit,e.glukosa,e.protein,e.keton,e.urablinogen,e.eryl,
		f.id_sedimen,f.epitel,f.lekosit,f.entrosit,f.bakteri,f.kristal,
		g.id_immunoserologi,g.hbsag,g.hiv,g.vdrl,c.no_lab as hematogi_lab,d.no_lab as kimiadarah_lab,e.no_lab as urinalisa_lab,f.no_lab as sedimen_lab,g.no_lab as immunoserologi_lab
		');
		$this->db->from('pasien as a');
        $this->db->join('laporan as b','a.id_pasien = b.id_pasien','left');
		$this->db->join('hematologi as c','c.id_pasien = a.id_pasien','left');
		$this->db->join('kimiadarah as d','d.id_pasien = a.id_pasien','left');
		$this->db->join('urinalisa as e','e.id_pasien = a.id_pasien','left');
		$this->db->join('sedimen as f','f.id_pasien = a.id_pasien','left');
		$this->db->join('immunoserologi as g','g.id_pasien = a.id_pasien','left');
		$data = $this->db->get()->result_array();
        return $data;
	}
	public function GetLaporanSearch($nama){
		//a = Pasien b = Laporan c=Hematologi d=Kimia Darah e=Urinalisa f=sedimen g=Immunoserologi
		$this->db->select('
		a.id_pasien,a.nama,a.nrp,a.nip,a.kesatuan,a.tanggal_lahir,a.tempat_lahir,a.jenis_kelamin,
		b.id_laporan,b.hematologi,b.kimiadarah,b.immunoserologi,b.sedimen,b.urinalisa,
		c.id_hematologi,c.hematokrit,c.hemogoblin,c.eritrosit,c.led,c.lekosit,c.trombosit,c.pemeriksa,c.tanggal_pemeriksaan,c.no_lab,
		d.id_kimiadarah,d.bilurubintotal,d.bilurubinindirect,d.bilurubinindirect,d.sgot,d.spgt,d.cholesteroltotal,d.cholesterolldl,d.cholesterolhdl,d.trigiliserida,d.ureum,d.asamurat,d.guladarahpuasa,d.guladarah2jam,d.guladarahsewaktu,
		e.id_urinalisa,e.warna,e.beratjenis,e.ph,e.lekosit,e.nitrit,e.glukosa,e.protein,e.keton,e.urablinogen,e.eryl,
		f.id_sedimen,f.epitel,f.lekosit,f.entrosit,f.bakteri,f.kristal,
		g.id_immunoserologi,g.hbsag,g.hiv,g.vdrl
		');
		$this->db->from('pasien as a');
        $this->db->join('laporan as b','a.id_pasien = b.id_pasien','left');
		$this->db->join('hematologi as c','c.id_pasien = a.id_pasien','left');
		$this->db->join('kimiadarah as d','d.id_pasien = a.id_pasien','left');
		$this->db->join('urinalisa as e','e.id_pasien = a.id_pasien','left');
		$this->db->join('sedimen as f','f.id_pasien = a.id_pasien','left');
		$this->db->join('immunoserologi as g','g.id_pasien = a.id_pasien','left');
		$this->db->like('a.nama',$nama);
		$data = $this->db->get()->result_array();
        return $data;
	}
    public function UserID(){
        
    }

    public function Insert($table,$data){
        return $data = $this->db->insert($table,$data);
    }
    
    public function Update($table,$key,$id,$data){
        return $this->db->update($table,$data,array($key => $id));
    }
    
    public function Delete($table,$key,$id){
        return $this->db->delete($table,array($key => $id));
    }

	public function UpdateDefault($table){
		$this->db->set('status','deactive');
		return $this->db->update($table);
	}
    public function GetWhere($table,$where,$whereTo,$OrderBy){
        $this->db->select("*");
        $this->db->from($table);
        $this->db->where(array(
            $where => $whereTo
        ));
		$this->db->order_by($where,$OrderBy);
        return $data = $this->db->get()->result_array();
    }
	public function GetLike($table,$category,$like){
        $this->db->select("*");
        $this->db->from($table);
		$this->db->like($category,$like);
        return $data = $this->db->get()->result_array();
    }
	public function GetAll($table){
        $this->db->select("*");
        $this->db->from($table);
        return $data = $this->db->get()->result_array();
    }
    public function GetContact($id){
        $this->db->select('a.Contact,b.Name,b.emailPU,b.Photo,b.Address');
        $this->db->from('contact a');
        $this->db->join('db_employees.employees b','a.NIP = b.nip','left');
        $this->db->where(array(
            'a.ID' => $id
        ));
        return $data = $this->db->get()->result_array();
    }
    public function getTrandingNews($Limit)
    {
		// $hasil = [];
        $this->db->select('art.ID_title, cat.Name as Category, art.Title, art.Content,art.Images, 
        art.CreateAT, art.UpdateBY, tp.Name_topic , COUNT(sv.id_article)Tot_Visit');
        $this->db->from('db_blogs.article art');
        $this->db->join('db_blogs.site_visits sv','sv.id_article=art.ID_title','inner');
        $this->db->join('db_blogs.category cat','art.ID_category = cat.ID_category','left');
        $this->db->join('db_blogs.show_topic sh','art.ID_category = cat.ID_category ','left');
        $this->db->join('db_blogs.topic tp','sh.ID_topic = tp.ID_topic ','left');
        $this->db->where(array(
            'art.Status' => "Published",
            'tp.ID_topic' => 2 
        ));
        $this->db->group_by("sv.id_article");
        $this->db->order_by('Tot_Visit','DESC');
        $this->db->limit($Limit);
        $hasil = $this->db->get()->result_array();
		if ($hasil)
		{	
			for ($i=0; $i < count($hasil); $i++) 
			{ 
				$string = $hasil[$i]['Title'];
				$replace = '-';         
				$string = strtolower($string);     
				//replace / and . with white space     
				$string = preg_replace("/[\/\.]/", " ", $string);     
				$string = preg_replace("/[^a-z0-9_\s-]/", "", $string);     
				//remove multiple dashes or whitespaces     
				$string = preg_replace("/[\s-]+/", " ", $string);     
				//convert whitespaces and underscore to $replace     
				$string = preg_replace("/[\s_]/", $replace, $string);

				$slug = $string;
				$hasil[$i]['SEO_title'] = $slug;
				$hasil[$i]['CreateAT'] = date("M d, Y", strtotime($hasil[$i]['CreateAT']));
				$url = url_admblogs.'upload/'.$hasil[$i]['Images'];
				$hasil[$i]['img'] = $hasil[$i]['Images'];
				$hasil[$i]['url'] = $url;
				$cek = $this->is_url_exist($url);
				if (!$cek)
				{
					$hasil[$i]['Images'] = 'default.png';
				}
			}
		}
		
		return $hasil;
    }
    public function getRecentNews()
    {//
        $this->db->select('art.ID_title, cat.Name as Category, art.Title, art.Content,art.Images,art.CreateAT,art.UpdateBY');
        $this->db->from('db_blogs.article art');
        $this->db->join('db_blogs.category cat','art.ID_category = cat.ID_category ','left');
        $this->db->where(array(
            'art.Status' => "Published"
        ));
        $this->db->order_by('art.ID_title','DESC');
        $hasil = $this->db->get()->result();
        $hasil = array();

		// $hasil= $this->db->query('SELECT art.ID_title, cat.Name as Category, art.Title, art.Content,art.Images, art.CreateAT, 
        // art.UpdateBY FROM db_blogs.article art 
        // LEFT JOIN db_blogs.category cat ON art.ID_category = cat.ID_category 
        // WHERE art.Status="Published" ORDER BY art.ID_title desc')->result();
		// $hasil = array();
		for ($i=0; $i < count($hasil); $i++) 
        { 
			$string=$hasil[$i]->Title;
	        $replace = '-';         
	        $string = strtolower($string);     
	        //replace / and . with white space     
	        $string = preg_replace("/[\/\.]/", " ", $string);     
	        $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);     
	        //remove multiple dashes or whitespaces     
	        $string = preg_replace("/[\s-]+/", " ", $string);     
	        //convert whitespaces and underscore to $replace     
	        $string = preg_replace("/[\s_]/", $replace, $string);

	        $slug = $string;
	        $hasil[$i]->SEO_title = $slug;
	        $hasil[$i]->CreateAT = date("M d, Y", strtotime($hasil[$i]->CreateAT));
			$url = url_admblogs.'upload/'.$hasil[$i]->Images;
			$hasil[$i]->url = $url;
			$cek = $this->is_url_exist($url);
			if(!$cek)
            {
				$hasil[$i]->Images = 'default.png';
			}
		}
		return $hasil;
		
	}
    public function getRecomentNews($Limit)
    {
        $this->db->select('art.ID_title, cat.Name as Category, art.Title, art.Content,art.Images, art.CreateAT, art.UpdateBY, tp.Name_topic');
        $this->db->from('db_blogs.article art');
        $this->db->join('db_blogs.category cat','art.ID_category = cat.ID_category','left');
        $this->db->join('db_blogs.show_topic sh','art.ID_title = sh.ID_article','left');
        $this->db->join('db_blogs.topic tp','sh.ID_topic = tp.ID_topic','left');
        $this->db->where(array(
            'art.Status' => "Published",
            'tp.ID_topic' => 1 
        ));
        $this->db->order_by('art.ID_title','DESC');
        $this->db->limit($Limit);
        $hasil = $this->db->get()->result();


        // $hasil= $this->db->query('SELECT art.ID_title, cat.Name as Category, art.Title, art.Content,art.Images, art.CreateAT, art.UpdateBY, tp.Name_topic FROM db_blogs.article art 
		// 	LEFT JOIN db_blogs.category cat ON art.ID_category = cat.ID_category 
		// 	LEFT JOIN db_blogs.show_topic sh ON art.ID_title = sh.ID_article
		// 	LEFT JOIN	db_blogs.topic tp ON sh.ID_topic = tp.ID_topic
		// 	WHERE art.Status="Published" AND tp.ID_topic = 1 ORDER by art.ID_title desc LIMIT $Limit')->result();
		for ($i=0; $i < count($hasil); $i++) 
        { 
			$string=$hasil[$i]->Title;
	        $replace = '-';         
	        $string = strtolower($string);     
	        //replace / and . with white space     
	        $string = preg_replace("/[\/\.]/", " ", $string);     
	        $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);     
	        //remove multiple dashes or whitespaces     
	        $string = preg_replace("/[\s-]+/", " ", $string);     
	        //convert whitespaces and underscore to $replace     
	        $string = preg_replace("/[\s_]/", $replace, $string);

	        $slug = $string;
	        $hasil[$i]->SEO_title = $slug;
	        $hasil[$i]->CreateAT = date("M d, Y", strtotime($hasil[$i]->CreateAT));
			$url = url_admblogs.'upload/'.$hasil[$i]->Images;
			$hasil[$i]->url = $url;
			$cek = $this->is_url_exist($url);
			if(!$cek)
            {
				$hasil[$i]->Images = 'default.png';
			}
		}
		return $hasil;
    }
    public function getBrochure($idProdi){
        $this->db->select('a.Name,a.Link,c.name as ProdiName,');
        $this->db->from('db_admission.brochure as a');
        $this->db->join('db_employees.employees as b','a.UpdatedBy = b.NIP','left');
        $this->db->join('db_academic.program_study as c','a.ID_Prodi = c.ID','left');
        $this->db->where(array(
            'a.ID_Prodi' => $idProdi
        ));
        return $data = $this->db->get()->result_array();
    }
    public function Reservation($tanggal1,$tanggal2){
        $this->db->select('a.id,a.Start,a.End,a.Time,a.Colspan,a.Agenda,a.Room,a.CreatedBy,b.NIP,b.Name,b.Photo');
        $this->db->from('db_reservation.t_booking as a');
        $this->db->join('db_employees.employees as b','a.CreatedBy = b.NIP','left');
        $this->db->where('a.Status = 1');
        $this->db->where('a.Status1 = 1');
        // $this->db->where('a.start = ',"2018-08-31");
        $this->db->where('a.start >= ',$tanggal1);
        $this->db->where('a.start <= ',$tanggal2);
        $this->db->order_by('a.start', 'ASC');
        return $data = $this->db->get()->result_array();
    }
    public function Schedule($Day,$tanggal1,$tanggal2){
        $this->db->select('b.MKCode,b.Name,b.NameEng,
        c.Credit,c.DayID,c.TimePerCredit,c.StartSEssions,c.EndSessions,
        d.Room,
        e.NIP,e.Name as Dosen,
        h.Name as Days,h.NameEng as DaysEng');
        $this->db->from('db_academic.schedule a');
        // $this->db->join('db_academic.schedule_team_teaching f','a.ID = f.ScheduleID');
        $this->db->join('db_academic.schedule_details_course g','a.ID = g.ScheduleID');
        $this->db->join('db_academic.mata_kuliah b','g.MKID = b.ID');
        $this->db->join('db_academic.schedule_details c','a.ID = c.ScheduleID');
        $this->db->join('db_academic.classroom d','c.ClassroomID = d.ID');
        $this->db->join('db_employees.employees e','a.Coordinator = e.NIP');
        $this->db->join('db_academic.days h','c.DayID = h.ID');
        $this->db->where('h.ID = ',$Day);
        $this->db->where('c.StartSessions >= ',substr($tanggal1,11,13),'00:00');
        $this->db->where('c.StartSessions <= ',substr($tanggal2,11,13),'00:00');
        $this->db->order_by('c.StartSessions', 'ASC');
        $this->db->group_by('b.MKCode');
        return $data = $this->db->get()->result_array();
    }
    public function getannouncement(){
        $data = $this->db->query('SELECT annc.*, em.Name FROM db_notifikasi.announcement annc
                LEFT JOIN db_employees.employees em ON (em.NIP = annc.CreatedBy)
                ORDER BY IF(MONTH(Start) > MONTH(NOW()), MONTH(End) + 3, MONTH(Start)),
                DAY(Start) and annc.ID DESC limit 10')->result_array();
        // SELECT annc.*, em.Name FROM db_notifikasi.announcement annc
        // LEFT JOIN db_employees.employees em ON (em.NIP = annc.CreatedBy)
        // WHERE annc.Start >= NOW() AND annc.End <= NOW() - INTERVAL 7 DAY;
        return $data;
    }
    public function getcustomwalltv(){
		$this->db->select('e.id_design, a.name, a.description, b.data,d.name as CategoryName, d.description as CategoryDescription');
		$this->db->from('db_walltv.design a');
		$this->db->join('db_walltv.preset e', 'e.id_design = a.id_design');
		$this->db->join('db_walltv.design_detail b', 'e.id_preset = b.id_preset');
		$this->db->join('db_walltv.category d', 'b.id_category = d.id_category');
		$this->db->where('e.status', 'active');
		$this->db->order_by('d.id_category','ASC');

		$query = $this->db->get();
		
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return array();
		}
    }
    public function insertdatafoto($tbl, $idname, $file_upload, $loc, $input, $userlevel = false, $id = null, $edit = null, $onlyfile = false, $width = 800, $height = 800)
	{
		$this->db->trans_begin();
			global $SConfig;
			$this->load->library('upload');

			if ($edit == true) 
			{
				$getdatafoto = $this->getdetail($tbl, array($idname => $id));
				$ext = get_ext($_FILES[$file_upload]["name"]);
				if ($onlyfile == true) 
				{
					if ($ext == 'jpg' || $ext == 'png' || $ext == 'jpeg' || $ext == 'JPG' || $ext == 'PNG' || $ext == 'JPEG' || $ext == 'pdf' || $ext == 'PDF')
					{
						unlink('./'.$getdatafoto[$file_upload]);
						$this->updatedata($tbl, $input, array($idname => $id));
					}
					else
					{
						$error = 1;
					}
				}
				else
				{
					if ($ext == 'jpg' || $ext == 'png' || $ext == 'jpeg' || $ext == 'JPG' || $ext == 'PNG' || $ext == 'JPEG')
					{
						unlink('./'.$getdatafoto[$file_upload]);
						$this->updatedata($tbl, $input, array($idname => $id));
					}
					else
					{
						$error = 1;
					}
				}
			}
			else
			{
				$id = $this->insertdata($tbl, $input);

				if ($userlevel == TRUE) 
				{
					foreach ($userlevel as $key => $value) 
					{
						$insert = array
								(
									'id_user' => $id,
									'id_level' => $value
								);
						$this->insertdata('tbl_user_level', $insert);
					}
				}
			}

			$error = 0;
			$config['file_name'] = uniqid();
			$config['upload_path'] = './uploads/'.$loc;
			if ($onlyfile == true) 
			{
				$config['allowed_types'] = 'jpg|jpeg|png|pdf';
			}
			else
			{
				$config['allowed_types'] = 'jpg|jpeg|png';
			}
			

			$this->upload->initialize($config);
			if ($this->upload->do_upload($file_upload)) 
			{
				if ($onlyfile == true) 
				{
					$link_file = '/uploads/'.$loc.'/'.$config['file_name'].'.'.get_ext($_FILES[$file_upload]["name"]);
					$this->updatedata($tbl, array($file_upload => $link_file), array($idname => $id));
				}
				else
				{
					$gbr = $this->upload->data();
	                //Compress Image
	                $config['image_library']='gd2';
	            	$config['source_image']='./uploads/'.$loc.'/'.$gbr['file_name'];
	            	$config['new_image']= './uploads/'.$loc.'/'.$gbr['file_name'];
	            	$link_file = '/uploads/'.$loc.'/'.$config['file_name'].'.'.get_ext($_FILES[$file_upload]["name"]);
					$this->updatedata($tbl, array($file_upload => $link_file), array($idname => $id));
	                
	                $config['create_thumb']= FALSE;
	                $config['maintain_ratio']= TRUE;
	                $config['width']= $width;
	                $config['height']= $height;
	                
	                $this->load->library('image_lib', $config);
	                $this->image_lib->resize();
				}
			}
			else
			{
				$error = 1;
				$upl = $this->upload->display_errors();
			}

		if ($this->db->trans_status() === FALSE || $error == 1)
		{
			$this->db->trans_rollback();
			return $upl;
		}
		else
		{
			$this->db->trans_commit();
			return $id;
		}
	}






    ///// core
    public function inserts($table, $data,$affected=FALSE,$batch=FALSE){
		if($batch == TRUE){
			$this->db->insert_batch($table, $data);
			// $this->db->insert_batch('tbl_users',$data);
		}
		else{
			$this->db->set($data);
			$this->db->insert($table);

			if ($affected==TRUE) 
			{
				$query = $this->db->query('SELECT LAST_INSERT_ID()');
				$row = $query->row_array();
				return $row['LAST_INSERT_ID()'];
			}
			else
			{
				$id = $this->db->insert_id();
				return $id;
			}
		}
	}

	public function updates($table, $data,$where=array(), $batch=false){
		if ($batch == false) 
		{
			$data['updated_at'] = date('Y-m-d H:i:s');
			$this->db->set($data);
			$this->db->where($where);
			$this->db->update($table);
		}
		if ($batch == true) 
		{
			$this->db->update_batch($table, $data, $where);
			return TRUE;
		}
	}	

	public function get($table,$where=null,$id=NULL,$single=FALSE){
		if($id != NULL){
			$this->db->where($where);
			$method = 'row_array';
		}

		else if($single == TRUE){
			$method = 'row_array';
		}

		else{
			$method = 'result_array';
		}

		if($this->_order_by_type){
			$this->db->order_by($this->_order_by,$this->_order_by_type);
			// $this->db->order_by('ID','DESC');
		}
		else{
			$this->db->order_by($this->_order_by);
		}

		return $this->db->get($table)->$method();
	}

	public function get_by($table, $where = NULL, $order = NULL, $sort = null ,$limit = NULL, $offset = NULL, $single, $select = NULL){
		if($select != NULL){
			$this->db->select($select);
		}

		$this->db->from($table);

		if($where){
			$this->db->where($where);
		}

		if(($limit) && ($offset)){
			$this->db->limit($limit,$offset);
		}
		else if($limit){
			$this->db->limit($limit);
		}

		if ($order) 
		{
			$this->db->order_by($order, $sort);
		}

		return $this->get(NULL,$single);
	}

	public function deletes($id){

		if(!$id){
			return FALSE;
		}

		$this->db->where($this->_primary_key,$id);
		$this->db->limit(1);
		$this->db->delete($table);
	}

	public function delete_by($table, $where = NULL)
	{
		if($where){
			$this->db->where($where);
		}

		$this->db->delete($table);
	}

	public function count($table, $where = NULL){
		if($where){
			$this->db->where($where);
		}

		$this->db->from($table);
		return $this->db->count_all_results();
	}

    public function getdata($table, $where = NULL, $order = null, $sort = null, $limit = NULL, $offset = NULL, $single = FALSE, $select = NULL)
	{
		if ($where != NULL) 
		{
			return $this->get_by($table, $where, $order, $sort, $limit, $offset, $single, $select);
		}
		else
		{
			return $this->get($table);
		}
	}

	public function getdetail($table, $where)
	{
		return $this->get($table, $where, TRUE);
	}

	public function insertdata($table, $data, $affected=FALSE,$batch=FALSE)
	{
		return $this->inserts($table, $data, $affected, $batch);
	}

	public function updatedata($table, $data, $where, $batch=false)
	{
		$this->updates($table, $data, $where, $batch);
		return TRUE;
	}

	public function deletedata($table, $where)
	{
		return $this->delete_by($table, $where);
	}

	public function countdata($table, $where)
	{
		return $this->count($table, $where);
	}
    function get_ext($data)
    {
        $array = explode(".",$data);

        $lastKey = key(array_slice($array, -1, 1, true));
        return $array[$lastKey];
    }
}