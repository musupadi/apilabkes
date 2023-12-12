<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Blogs_model extends CI_Model
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
    public function getTrandingNews()
    {
		// $hasil = [];
        $hasil= $this->db->query('SELECT art.ID_title, cat.Name as Category, art.Title, art.Content,art.Images, 
        art.CreateAT, art.UpdateBY, tp.Name_topic , COUNT(sv.id_article)Tot_Visit 
            FROM db_blogs.article art
			INNER JOIN db_blogs.site_visits sv ON sv.id_article=art.ID_title 
			LEFT JOIN db_blogs.category cat ON art.ID_category = cat.ID_category 
			LEFT JOIN db_blogs.show_topic sh ON art.ID_title = sh.ID_article
			LEFT JOIN	db_blogs.topic tp ON sh.ID_topic = tp.ID_topic
			WHERE art.Status="Published" AND tp.ID_topic = 2 
			GROUP BY sv.id_article
			ORDER BY Tot_Visit DESC LIMIT 5')->result_array();
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

    public function getTrendingNewsNew()
    {
		// $hasil = [];
        $hasil= $this->db->query('SELECT art.ID_title, cat.Name as Category, art.Title, art.Content,art.Images, 
        art.CreateAT, art.UpdateBY, tp.Name_topic , COUNT(sv.id_article)Tot_Visit 
            FROM db_blogs.article art
			INNER JOIN db_blogs.site_visits sv ON sv.id_article=art.ID_title 
			LEFT JOIN db_blogs.category cat ON art.ID_category = cat.ID_category 
			LEFT JOIN db_blogs.show_topic sh ON art.ID_title = sh.ID_article
			LEFT JOIN	db_blogs.topic tp ON sh.ID_topic = tp.ID_topic
			WHERE art.Status="Published" AND tp.ID_topic = 2 
			GROUP BY sv.id_article
			ORDER BY Tot_Visit DESC LIMIT 10')->result_array();
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

    public function getRecentNews2()
    {//
		$hasil= $this->db->query('SELECT art.ID_title, cat.Name as Category, art.Title, art.Content,art.Images, art.CreateAT, 
        art.UpdateBY FROM db_blogs.article art 
        LEFT JOIN db_blogs.category cat ON art.ID_category = cat.ID_category 
        WHERE art.Status="Published" ORDER BY art.ID_title desc')->result();
		$hasil = array();
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
    public function getRecomentNews()
    {
        $hasil= $this->db->query('SELECT art.ID_title, cat.Name as Category, art.Title, art.Content,art.Images, art.CreateAT, art.UpdateBY, tp.Name_topic FROM db_blogs.article art 
			LEFT JOIN db_blogs.category cat ON art.ID_category = cat.ID_category 
			LEFT JOIN db_blogs.show_topic sh ON art.ID_title = sh.ID_article
			LEFT JOIN	db_blogs.topic tp ON sh.ID_topic = tp.ID_topic
			WHERE art.Status="Published" AND tp.ID_topic = 1 ORDER by art.ID_title desc LIMIT 20')->result();
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

    public function getNewsList($offset = 0, $limit = 5, $category = false)
    {
    	$this->db->select('db_blogs.article.*, db_blogs.category.Name');
		$this->db->from('db_blogs.article');
		$this->db->join('db_blogs.category', 'db_blogs.article.ID_category = db_blogs.category.ID_category');

		$this->db->limit($limit, $offset);

		if ($category != false) 
		{
			$this->db->where('db_blogs.article.ID_category', $category);
		}

		$this->db->where('db_blogs.article.Status', 'Published');

		$this->db->order_by('db_blogs.article.CreateAT', 'DESC');

		$query = $this->db->get();
		return $query->result_array();
    }

    public function getNewsDetail($id_title)
    {
    	$this->db->select('db_blogs.article.*, db_blogs.category.Name');
		$this->db->from('db_blogs.article');
		$this->db->join('db_blogs.category', 'db_blogs.article.ID_category = db_blogs.category.ID_category');

		$this->db->where('db_blogs.article.ID_title', $id_title);

		$query = $this->db->get();
		return $query->row_array();
    }

    public function getRecentNews($limit = 5)
    {
    	$this->db->select('db_blogs.article.*, db_blogs.category.Name');
		$this->db->from('db_blogs.article');
		$this->db->join('db_blogs.category', 'db_blogs.article.ID_category = db_blogs.category.ID_category');

		$this->db->limit($limit, 0);

		$this->db->where('db_blogs.article.Status', 'Published');

		$this->db->order_by('db_blogs.article.CreateAT', 'DESC');

		$query = $this->db->get();
		return $query->result_array();
    }

    public function getCategoryNews($limit = false)
    {
    	$this->db->select('*');
		$this->db->from('db_blogs.category');

		if ($limit != false) 
		{
			$this->db->limit(10, 0);
		}

		$this->db->order_by('db_blogs.category.Name', 'ASC');

		$query = $this->db->get();
		return $query->result_array();
    }
}