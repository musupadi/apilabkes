<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Library_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function getLoanList($member_id)
    {
        $dbLib = $this->load->database('server22', TRUE);

        $now = date("Y-m-d");

        $data = $dbLib->query('SELECT l.loan_id, l.member_id, l.loan_date, l.due_date, l.is_lent, l.is_return, b.biblio_id , b.title, b.image, l.renewed
                                        FROM library.loan l
                                        LEFT JOIN library.item i ON (i.item_code = l.item_code)
                                        LEFT JOIN library.biblio b ON (b.biblio_id = i.biblio_id)
                                        WHERE l.member_id = "' . $member_id . '" ORDER BY  l.is_return ASC ,l.loan_date DESC,
                                        l.due_date DESC, b.title ASC')->result_array();

        $dataHoliday = $dbLib->query('SELECT holiday_date FROM library.holiday ORDER BY holiday_id DESC')->result_array();

        $dataHolidayArr = [];
        if (count($dataHoliday) > 0) {
            for ($h = 0; $h < count($dataHoliday); $h++) {
                array_push($dataHolidayArr, $dataHoliday[$h]['holiday_date']);
            }
        }

        // cek denda
        if (count($data) > 0) {
            for ($i = 0; $i < count($data); $i++) {
                $sumDueDate = 0;
                $d = $data[$i];
                if ($d['is_return'] == '0' || $d['is_return'] == 0) {

                    // Cek apakah sudah lewat hari ini atau tidak
                    if ($d['due_date'] < $now) {
                        $ckDate = date('Y-m-d', strtotime($d['due_date'] . ' +1 day'));
                        $rageDate = $this->dateRange($ckDate, $now);
                        $sumDueDate = count($rageDate);
                        if (count($rageDate) > 0) {
                            for ($h = 0; $h < count($rageDate); $h++) {

                                $dt =  date('N', strtotime($rageDate[$h]));
                                if (in_array($rageDate[$h], $dataHolidayArr) || $dt == '6' || $dt == '7') {
                                    $sumDueDate = $sumDueDate - 1;
                                }
                            }
                        }
                    }
                }


                $data[$i]['SumOfLate'] = $sumDueDate;
            }
        }

        return $data;
    }

    public function getListBiblio($page, $perpage)
    {
        $dbLib = $this->load->database('server22', TRUE);
        $data = $dbLib->limit($perpage, $page * $perpage)->get('biblio')->result_array();

        return $data;
    }

    public function searchBiblio($key, $limit)
    {
        $dbLib = $this->load->database('server22', TRUE);
        $data = $dbLib->query('SELECT b.*, mp.publisher_name FROM biblio b 
                                    LEFT JOIN mst_publisher mp ON (mp.publisher_id = b.publisher_id)
                                    WHERE b.title LIKE "%' . $key . '%" OR 
                                    b.isbn_issn LIKE "%' . $key . '%" OR 
                                    b.publish_year LIKE "%' . $key . '%" OR 
                                    mp.publisher_name LIKE "%' . $key . '%"
                                    LIMIT ' . $limit)->result_array();

        return $data;
    }

    public function detailBiblio($biblio_id)
    {
        $dbLib = $this->load->database('server22', TRUE);
        $data = $dbLib->query('SELECT b.*, mp.publisher_name, ml.language_name, mg.gmd_name, mpl.place_name AS publish_place_name FROM biblio b 
                                    LEFT JOIN mst_publisher mp ON (mp.publisher_id = b.publisher_id)
                                    LEFT JOIN mst_language ml ON (ml.language_id = b.language_id)
                                    LEFT JOIN mst_gmd mg ON (mg.gmd_id = b.gmd_id)
                                    LEFT JOIN mst_place mpl ON (mpl.place_id = b.publish_place_id)
                                    WHERE b.biblio_id = "' . $biblio_id . '" ')->result_array();



        if (count($data) > 0) {
            // get authors
            $data[0]['Authors'] = $dbLib->query('SELECT * FROM biblio_author b 
                                    LEFT JOIN mst_author m 
                                    ON (m.author_id = b.author_id)
                                    WHERE b.biblio_id = "' . $biblio_id . '"')->result_array();

            $data[0]['Topics'] = $dbLib->query('SELECT * FROM biblio_topic b 
                                    LEFT JOIN mst_topic m 
                                    ON (m.topic_id = b.topic_id)
                                    WHERE b.biblio_id = "' . $biblio_id . '"')->result_array();
        }

        return $data;
    }

    public function detailBooking($id)
    {
        $data = $this->db->query('SELECT b.* FROM db_library.booking b
                                    WHERE b.ID = "' . $id . '" ')->result_array();

        return $data;
    }

    public function dataListBooking($username)
    {
        $data = $this->db->query('SELECT * FROM db_library.booking b WHERE b.Username = "' . $username . '" ')->result_array();

        if (count($data) > 0) {

            for ($i = 0; $i < count($data); $i++) {
                $dataBiblio = $this->detailBiblio($data[$i]['biblio_id']);
                $data[$i]['data_biblio'] = $dataBiblio;
            }
        }

        return $data;
    }

    public function insertBooking($data)
    {
        $data = $this->db->insert('db_library.booking', $data);
        return $data;
    }

    public function updateBooking($where, $data)
    {
        $this->db->update('db_library.booking', $data, $where);
        return 1;
    }

    public function deleteBooking($id)
    {
        $this->db->where('ID', $id);
        $this->db->delete('db_library.booking');
        return 1;
    }
}
