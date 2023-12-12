<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Announcement_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function getannouncement($offset = 0, $limit = 10)
    {
        $data = $this->db->query('SELECT annc.*, em.Name FROM db_notifikasi.announcement annc
                LEFT JOIN db_employees.employees em ON (em.NIP = annc.CreatedBy)
                ORDER BY IF(MONTH(Start) > MONTH(NOW()), MONTH(End) + 3, MONTH(Start)),
                DAY(Start) and annc.ID DESC limit '.$limit.' OFFSET '.$offset.'')->result_array();
        // SELECT annc.*, em.Name FROM db_notifikasi.announcement annc
        // LEFT JOIN db_employees.employees em ON (em.NIP = annc.CreatedBy)
        // WHERE annc.Start >= NOW() AND annc.End <= NOW() - INTERVAL 7 DAY;
        return $data;
    }

    public function getrecentannouncement($offset = 0, $limit = 5)
    {
        $data = $this->db->query('SELECT annc.*, em.Name FROM db_notifikasi.announcement annc
                LEFT JOIN db_employees.employees em ON (em.NIP = annc.CreatedBy)
                ORDER BY IF(MONTH(Start) > MONTH(NOW()), MONTH(End) + 3, MONTH(Start)),
                DAY(Start) and annc.ID DESC limit '.$limit.' OFFSET '.$offset.'')->result_array();
        // SELECT annc.*, em.Name FROM db_notifikasi.announcement annc
        // LEFT JOIN db_employees.employees em ON (em.NIP = annc.CreatedBy)
        // WHERE annc.Start >= NOW() AND annc.End <= NOW() - INTERVAL 7 DAY;
        return $data;
    }

    public function getannouncementdetail($id_announcement)
    {
        $this->db->select('db_notifikasi.announcement.*, db_employees.employees.Name');
        $this->db->from('db_notifikasi.announcement');
        $this->db->join('db_employees.employees', 'db_notifikasi.announcement.CreatedBy = db_employees.employees.NIP');

        $this->db->where('db_notifikasi.announcement.ID', $id_announcement);

        $query = $this->db->get();
        return $query->row_array();
    }
}