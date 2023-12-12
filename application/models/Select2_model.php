<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Select2_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }
    
    public function getInstitutions($term)
    {
        
        
        $data = $this->db->query('SELECT * FROM db_research.university a WHERE 
        a.Code_University LIKE "%'.$term.'%" 
        OR a.Name_University LIKE "%'.$term.'%" LIMIT 15 ')->result_array();

        $result = [];
        if(count($data)>0){
            for($i=0;$i<count($data);$i++){
                $d = $data[$i];
                $arr = array(
                    'id' => $d['ID'],
                    'text' => $d['Name_University']
                );
                array_push($result,$arr);
            }
        }

        return $result;

    }

    public function getTrainingCourse($term)
    {
        $data = $this->db->query('SELECT * FROM db_training.m_course a 
        WHERE a.IsDeleted = "0" AND ( a.CourseCode LIKE "%'.$term.'%" OR 
        a.NameEng LIKE "%'.$term.'%" ) LIMIT 15 ')->result_array();

        // a.Name LIKE "%'.$term.'%" OR

        $result = [];
        if(count($data)>0){
            for($i=0;$i<count($data);$i++){
                $d = $data[$i];
                $arr = array(
                    'id' => $d['ID'],
                    'text' => $d['CourseCode'].' - '.$d['NameEng']
                );
                array_push($result,$arr);
            }
        }

        return $result;
    }

    public function getTrainer($term)
    {
        $data = $this->db->query('SELECT t.TrainerCode,
        TRIM(CONCAT(
        COALESCE(t.TitleAhead, NULL, "", "-" || t.TitleAhead)
        , " "
        , t.Name, 
        " "
        ,
        COALESCE(t.TitleBehind, NULL, "", "-" || t.TitleBehind)
        )) AS Name
        FROM db_training.trainer t 
        WHERE t.IsDeleted = "0" AND ( 
        t.TrainerCode LIKE "%'.$term.'%" OR
        t.Name LIKE "%'.$term.'%" OR
        t.TitleAhead LIKE "%'.$term.'%" OR
        t.TitleBehind LIKE "%'.$term.'%" )
        LIMIT 15')->result_array();

        $result = [];
        if(count($data)>0){
            for($i=0;$i<count($data);$i++){
                $d = $data[$i];
                $arr = array(
                    'id' => $d['TrainerCode'],
                    'text' => $d['TrainerCode'].' - '.$d['Name']
                );
                array_push($result,$arr);
            }
        }

        return $result;
    }

    public function getTrainingClassroom($term)
    {
        $data = $this->db->query('SELECT * FROM db_training.m_classroom c 
        WHERE c.IsDeleted = "0" AND (c.RoomCode LIKE "%'.$term.'%" OR c.Name LIKE "%'.$term.'%")
        LIMIT 15')->result_array();

        $result = [];
        if(count($data)>0){
            for($i=0;$i<count($data);$i++){
                $d = $data[$i];
                $arr = array(
                    'id' => $d['ID'],
                    'text' => $d['Name']
                );
                array_push($result,$arr);
            }
        }

        return $result;
    }
}
