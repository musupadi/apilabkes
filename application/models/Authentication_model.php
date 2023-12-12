<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Authentication_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function checkAuthentication($Username, $Password)
    {
        $pas = md5($Username . $Password);
        $pass = sha1('jksdhf832746aiH{}{()&(*&(*' . $pas . 'HdfevgyDDw{}{}{;;*766&*&*');

        $arrayWhereStd = array(
            'NPM' => $Username,
            'Password' => $pass
        );

        $arrayWhereEmp = array(
            'NIP' => $Username,
            'Password' => $pass
        );

        // cek status config
        $dataConf = $this->db->get_where('db_it.m_config', array(
            'ID' => 1,
            'DevelopMode' => '1',
            'GlobalPassword' => $Password
        ))->result_array();

        if (count($dataConf) > 0) {
            $arrayWhereStd = array(
                'NPM' => $Username
            );

            $arrayWhereEmp = array(
                'NIP' => $Username
            );
        }

        // cek student
        $chekStudent = $this->db->select('NPM, ProgramID, ProdiID, Name, EmailPU, Year')->get_where('db_academic.auth_students', $arrayWhereStd)->result_array();

        $result = [];

        if (count($chekStudent) > 0) {
            // image student
            $db_std = 'ta_' . $chekStudent[0]['Year'];
            $dataFoto = $this->db->select('Photo')->get_where($db_std . '.students', array('NPM' => $chekStudent[0]['NPM']))->result_array();
            $chekStudent[0]['Photo'] = ($dataFoto[0]['Photo'] != '' && $dataFoto[0]['Photo'] != null)
                ? 'https://pcam.podomorouniversity.ac.id/uploads/students/' . $db_std . '/' . $dataFoto[0]['Photo'] : '';
            $result = $chekStudent;
        } else {
            // cek employees
            $checkEmpl = $this->db->select('NIP, Name, TitleAhead, TitleBehind, EmailPU, Photo')
                ->get_where(
                    'db_employees.employees',
                    $arrayWhereEmp
                )->result_array();

            if (count($checkEmpl) > 0) {
                $Photo = ($checkEmpl[0]['Photo'] != '' && $checkEmpl[0]['Photo'] != null)
                    ? 'https://pcam.podomorouniversity.ac.id/uploads/employees/' . $checkEmpl[0]['Photo'] : '';
                $checkEmpl[0]['Photo'] = $Photo;
                $result = $checkEmpl;
            }
        }


        // get API Key
        if (count($result) > 0) {
            $dataKey = $this->db->select('key')->get_where('db_it.user_api_keys', array('user_id' => $Username))->result_array();

            if (count($dataKey) > 0) {
                $key = $dataKey[0]['key'];
            } else {

                $key = $this->checkAPIKEY();

                // $now = DateTime::createFromFormat('U.u', microtime(true));

                $this->db->insert('db_it.user_api_keys', array(
                    'user_id' => $Username,
                    'key' => $key,
                    'level' => 1,
                    'ip_addresses' => $this->input->ip_address(),
                    'date_created' => microtime(true)
                ));
            }

            $result[0]['APIKey'] = $key;
        }


        return $result;
    }

    public function Insert($table,$data){
        return $data = $this->db->insert($table,$data);
    }
    public function checkAdminAuthenticationTraining($Username, $Password)
    {
        $pass = $this->genratePassword($Username, $Password);
        $dbCloud = $this->load->database('cloud', TRUE);

        $result = $dbCloud->query('SELECT em.NIP, em.Name, em.Email, em.EmailPU 
        FROM db_it.admin_training adt
        LEFT JOIN db_employees.employees em ON (adt.NIP = em.NIP)
        WHERE adt.NIP = "'.$Username.'" AND em.Password = "'.$pass.'" ')->result_array();

        if(count($result)>0){
            $key = $this->getKey($Username,2);

            $result[0]['UserType'] = 'admin';
            $result[0]['APIKey'] = $key;

        }

        return $result;

    }

    public function checkAuthenticationTraining($Username, $Password)
    {
        $pass = $this->genratePassword($Username, $Password);
        $UserType = 'participant';

        // cek Participant
        $result = $this->db->get_where('db_training.participant',array(
            'ParticipantCode' => $Username,
            'Password' => $pass,
            'IsDeleted' => '0'
        ))->result_array();
        if(count($result)<=0){
            // cek Trainer
            $result = $this->db->get_where('db_training.trainer',array(
                'TrainerCode' => $Username,
                'Password' => $pass,
                'IsDeleted' => '0'
            ))->result_array();

            $UserType = 'trainer';
        
        }

        if(count($result)>0){
            
            $key = $this->getKey($Username,2);
            
            $result[0]['UserType'] = $UserType;

            $result[0]['APIKey'] = $key;
        }
        return $result;

    }

    private function getKey($Username,$level)
    {
        $dataKey = $this->db->select('key,date_created')->get_where('db_it.user_api_keys', 
            array('user_id' => $Username,'level' => $level))->result_array();

        if (count($dataKey) > 0) {
            
            $checkTime = $this->checkKeyDate($dataKey[0]['date_created']);
            
            if(!$checkTime){
                
                // remove old key and insert new key

                $this->db->where('user_id', $Username);
                $this->db->delete('db_it.user_api_keys');
                $this->db->reset_query();
                
                $key = $this->checkAPIKEY();
                
                $this->db->insert('db_it.user_api_keys', array(
                    'user_id' => $Username,
                    'key' => $key,
                    'level' => 2,
                    'ip_addresses' => $this->input->ip_address(),
                    'date_created' => microtime(true)
                ));

            } else {
                $key = $dataKey[0]['key'];
            }
        
        } else {
            
            $key = $this->checkAPIKEY();
            
            $this->db->insert('db_it.user_api_keys', array(
                'user_id' => $Username,
                'key' => $key,
                'level' => 2,
                'ip_addresses' => $this->input->ip_address(),
                'date_created' => microtime(true)
            ));
        }

        return $key;
    }

    private function checkKeyDate($t)
    {
        $micro = sprintf("%06d",($t - floor($t)) * 1000000);
        $d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
        $date1 = $d->format("Y-m-d H:i:s");
        
        $date2 = date('Y-m-d H:i:s');

        $diff = abs(strtotime($date2) - strtotime($date1));

        $years = floor($diff / (365*60*60*24));
        $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
        $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

        // printf("%d years, %d months, %d days\n", $years, $months, $days);

        $result = false;
        if($days==0){
            $result = true;
        }

        return $result;

    }

    public function checkAPIKEY()
    {
        $rand = $this->genrateCode(true, 40);

        $d = $this->db->get_where('db_it.user_api_keys', array('key' => $rand))->result_array();
        if (count($d) > 0) {
            $this->checkAPIKEY();
        } else {
            return $rand;
        }
    }

    private function genrateCode($casesensitive, $length)
    {

        $k = ($casesensitive) ? 'abcdefghijklmnopqrstuvwxyz' : '';
        $seed = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789' . $k); // and any other characters

        shuffle($seed); // probably optional since array_is randomized; this may be redundant
        $rand = '';
        foreach (array_rand($seed, $length) as $key) $rand .= $seed[$key];

        return $rand;
    }

    public function logOutUsers($Username)
    {
        $this->db->where('user_id', $Username);
        $this->db->delete('db_it.user_api_keys');

        return true;
    }

    private function genratePassword($Username, $Password)
    {

        $plan_password = $Username . '' . $Password;
        $pas = md5($plan_password);
        $pass = sha1('jksdhf832746aiH{}{()&(*&(*' . $pas . 'HdfevgyDDw{}{}{;;*766&*&*');
        return $pass;
    }

    public function matchEmail($Username, $Email, $NewPassword, $ConfirmPassword)
    {
        $this->db->select('db_training.participant.*');
        $this->db->from('db_training.participant');
        $this->db->where(array(
            'Email' => $Email
        ));
        $data = $this->db->get()->result_array();       
        
        if(count($data)<=0){

            $this->db->select('db_training.trainer.*');
            $this->db->from('db_training.trainer');
            $this->db->where(array(
                'Email' => $Email
            )); 
            $data = $this->db->get()->result_array();

        }
        // if(count($result)>0){
            
        //     $key = $this->getKey($Username,2);
            
        //     $result[0]['UserType'] = $UserType;

        //     $result[0]['APIKey'] = $key;
        // }
        if(count($data)>0){
            $data[0]['Username']= $Username;
            $data[0]['Email']= $Email;
            $data[0]['NewPassword']= $NewPassword;
            $data[0]['ConfirmPassword']= $ConfirmPassword;
            $updatePassword = $this->updatePassword($data);

        }
        
        return $data;     
    }

    public function updatePassword($data)
    {
        $ID= $data[0]['ID'];
        $Email= $data[0]['Email'];
        $dataUpdate = [
            'Password' => $this->genratePassword($data[0]['Username'], $data[0]['ConfirmPassword']),
            'UpdatedBy' => $data[0]['Username'],
            'UpdatedAt' => date("Y-m-d H:i:s"),
        ];
        $this->db->where('Email', $Email);
        $this->db->update('db_training.participant',$dataUpdate);
        $update= $this->db->affected_rows();
        // print_r($update);die();
        if($update>0){
            $this->db->where('Email', $Email);
            $this->db->update('db_training.trainer',$dataUpdate);
            $update= $this->db->affected_rows();
        }
        return $update;
    }

    public function cekEmail($Email){
        $CheckUser = false;
        $Type='';
        // Cek Apakah partisipan atau bukan
        $dataPartispan = $this->db->query('SELECT ParticipantCode, Name FROM db_training.participant
                                                  WHERE Email = "' . $Email . '" LIMIT 1')->result_array();

        if (count($dataPartispan) > 0) {

            $Name = $dataPartispan[0]['Name'];
            $Username = $dataPartispan[0]['ParticipantCode'];
            $Type = 'participant';
            $key = $this->getKey($Username,2);
            $CheckUser = true;
        } else {

            $dataTraner = $this->db->select('TrainerCode,Name')->get_where(
                'db_training.trainer',
                array('Email' => $Email)
            )->result_array();

            // cek kedua
            if (count($dataTraner) > 0) {
                $Name = $dataTraner[0]['Name'];
                $Type = 'trainer';
                $Username = $dataTraner[0]['TrainerCode'];
                $key = $this->getKey($Username,2);
                $CheckUser = true;
            }
        }
        $arrNewToken= [];
        if ($CheckUser){
            $to = $Email;
            $DueDate = date('Y-m-d H:i:s');
            $arrNewToken = array(
                'DueDate' => $DueDate,
                'Email'  => $Email,
                'Key' =>$key,
                'Name' => $Name,
                'Type' => $Type
            );
            $timeINT = strtotime($DueDate);
            $maxURL = 300; // 5 minutes
            $timeINT += $maxURL;
            $DateTimeEndURL = date("Y-m-d H:i:s", $timeINT);
            if ($Type == 'trainer') {
                $arrNewToken['TrainerCode'] = $Username;
            } else {
                $arrNewToken['ParticipantCode'] = $Username;
            }
            
        }

        return $arrNewToken;
    }

    public function updatePasswordByEmail($Email, $Password)
    {
        $Email= $Email;
        $cek= $this->cekEmail($Email);
        $Username= ($cek['Type']=='participant') ? $cek['ParticipantCode'] : $cek['TrainerCode'];
        // print_r($Username);die();
        $dataUpdate = [
            'Password' => $this->genratePassword($Username,$Password),
            'UpdatedBy' => $Username,
            'UpdatedAt' => date("Y-m-d H:i:s"),
        ];
        $this->db->where('Email', $Email);
        $this->db->update('db_training.participant',$dataUpdate);
        $update= $this->db->affected_rows();
        if ($update <= 0){
            $this->db->where('Email', $Email);
            $this->db->update('db_training.trainer',$dataUpdate);
            $update= $this->db->affected_rows();
        }

        return 1;
    }

}
