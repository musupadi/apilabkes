<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/Custom_REST_Controller.php';

class PublicApi2 extends Custom_REST_Controller
{

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model(array(
            'Public_model' => 'model',
            'authentication_model' => 'auth',
            'blogs_model',
            'Custom_model',
            'announcement_model' => 'announcement'
        ));
    }
    public function resetPassword_get(){
        /*
		@Description Untuk mereset Password Akun Guest
	    */
        $Token = $this->get('Token');
        $data['token'] = $this->model->GetWhere('token_guest','Token',$Token);
        if($data['token']){
            $this->load->view('resetpass',$data);
        }else{
            echo "Error Mohon coba request lagi";
        }
    }
    public function academicSchedule_get()
    {
        /*
		@Description Untuk mendapatkan Academic Schedule
	    */
        $Key = $this->get('Apikey');
        $findactivesemester = $this->Custom_model->getdetail('db_academic.semester', array('Status' => 1));
        $academicyear = $this->Custom_model->getdetail('db_academic.academic_years', array('SemesterID' => $findactivesemester['ID']));
        $Check = $this->model->GetWhere('public_key','Key',$Key);
        if($academicyear){
            $this->response([
                'status' => true,
                'Message' => "Data Succesfully Received",
                'Semester' => $findactivesemester['Name'],
                'data' => $academicyear
            ], REST_Controller::HTTP_OK);
    
        }else{
            $this->response([
                'status' => true,
                'Message' => "Data Failed Received",
                'Semester' => array(),
                'data' => array()
            ], REST_Controller::HTTP_OK);
    
        }
        // if($Check){
        //     $this->response([
        //         'status' => true,
        //         'Message' => "Data Succesfully Received",
        //         'Semester' => $findactivesemester['Name'],
        //         'data' => $academicyear
        //     ], REST_Controller::HTTP_OK);
        // }else{
        //     $this->response([
        //         'status' => true,
        //         'Message' => $this->model->GetInvalidAPIKey()
        //     ], REST_Controller::HTTP_OK);
        // }
    }
    public function showFeedback_get()
    {
         /*
		@Description Untuk mendapatkan Data Feedback
	    */
        $Key = $this->get('Apikey');
        $Limit = $this->get('Limit');
        $Check = $this->model->GetWhere('public_key','Key',$Key);
        $result = $this->model->ShowFeedback($Limit);
        if($result){
            $this->response([
                'status' => true,
                'Message' => 'Data Succesfully Received',
                'data' => $result
            ], REST_Controller::HTTP_OK);
        }else{
            $this->response([
                'status' => true,
                'Message' => 'Data Succesfully Received',
                'data' => array()
            ], REST_Controller::HTTP_OK);
        }
        // if($Check){
        //     $result = $this->model->ShowFeedback($Limit);
        //     $this->response([
        //         'status' => true,
        //         'Message' => 'Data Succesfully Received',
        //         'data' => $result
        //     ], REST_Controller::HTTP_OK);
        // }else{
        //     $this->response([
        //         'status' => true,
        //         'Message' => $this->model->GetInvalidAPIKey()
        //     ], REST_Controller::HTTP_OK);
        // }
    }
    public function privacyAndPolicy_get(){
        /*
		@Description Untuk mendapatkan Privacy And Policy
	    */
        $Key = $this->get('Apikey');
        $ID = $this->get('ID_App');
        $Check = $this->model->GetWhere('public_key','Key',$Key);
        $result = $this->model->GetPrivacy($ID);
        if($result){
            $this->response([
                'status' => true,
                'Message' => 'Data Succesfully Received',
                'data' => $result
            ], REST_Controller::HTTP_OK);
        }else{
            $this->response([
                'status' => true,
                'Message' => 'Data Failed Received',
                'data' => array()
            ], REST_Controller::HTTP_OK);
        }
        // if($Check){
        //     $result = $this->model->GetPrivacy($ID);
        //     $this->response([
        //         'status' => true,
        //         'Message' => 'Data Succesfully Received',
        //         'data' => $result
        //     ], REST_Controller::HTTP_OK);
        // }else{
        //     $this->response([
        //         'status' => true,
        //         'Message' => $this->model->GetInvalidAPIKey()
        //     ], REST_Controller::HTTP_OK);
        // }
    }
    public function reservation_get(){
        /*
		@Description Untuk mendapatkan Data Reservation
	    */
        $Key = $this->get('Apikey');
        $Tanggal = $this->get('Mulai');
        $Tanggal2 = $this->get('Akhir');
        $Check = $this->model->GetWhere('db_it.public_key','Key',$Key);
        $result = $this->model->Reservation($Tanggal,$Tanggal2);
        if($result){
            $this->response([
                'status' => true,
                'Message' => 'Data Succesfully Received',
                'data' => $result
            ], REST_Controller::HTTP_OK);  
        }else{
            $this->response([
                'status' => true,
                'Message' => 'Data Failed to Received',
                'data' => array()
            ], REST_Controller::HTTP_OK);  
        }
       
        // if($Check){
        //     $result = $this->model->Reservation($Tanggal,$Tanggal2);
        //     $this->response([
        //         'status' => true,
        //         'Message' => 'Data Succesfully Received',
        //         'data' => $result
        //     ], REST_Controller::HTTP_OK);
        // }else{
        //     $this->response([
        //         'status' => true,
        //         'Message' => $this->model->GetInvalidAPIKey()
        //     ], REST_Controller::HTTP_OK);
        // }
    }
    public function schedule_get(){
           /*
		@Description Untuk mendapatkan Data Schedule
	    */
        $Key = $this->get('Apikey');
        $Tanggal = $this->get('Mulai');
        $Tanggal2 = $this->get('Akhir');
        $Day = $this->get('Day');
        $Check = $this->model->GetWhere('db_it.public_key','Key',$Key);
        $result = $this->model->Schedule($Day,$Tanggal,$Tanggal2);
        if($result){
            $this->response([
                'status' => true,
                'Message' => 'Data Succesfully Received',
                'data' => $result
            ], REST_Controller::HTTP_OK);
        }else{
            $this->response([
                'status' => true,
                'Message' => 'Data Failed to Received',
                'data' => array()
            ], REST_Controller::HTTP_OK);  
        }
        
       
        // if($Check){
        //     $result = $this->model->Schedule($Day,$Tanggal,$Tanggal2);
        //     $this->response([
        //         'status' => true,
        //         'Message' => 'Data Succesfully Received',
        //         'data' => $result
        //     ], REST_Controller::HTTP_OK);
        // }else{
        //     $this->response([
        //         'status' => true,
        //         'Message' => $this->model->GetInvalidAPIKey()
        //     ], REST_Controller::HTTP_OK);
        // }
        
    }
    public function announcementPublic_get(){
           /*
		@Description Untuk mendapatkan Data Public Announcement
	    */
        $Key = $this->get('Apikey');
        $Check = $this->model->GetWhere('db_it.public_key','Key',$Key);
        $result = $this->announcement->getannouncement();
        if($result){
            $this->response([
                'status' => true,
                'Message' => 'Data Succesfully Received',
                'data' => $result
            ], REST_Controller::HTTP_OK);
        }else{
            $this->response([
                'status' => true,
                'Message' => 'Data Failed to Received',
                'data' => array()
            ], REST_Controller::HTTP_OK);  
        }
        // if($Check){
        //     $result = $this->announcement->getannouncement();
        //     $this->response([
        //         'status' => true,
        //         'Message' => 'Data Succesfully Received',
        //         'data' => $result
        //     ], REST_Controller::HTTP_OK);
        // }else{
        //     $this->response([
        //         'status' => true,
        //         'Message' => $this->model->GetInvalidAPIKey()
        //     ], REST_Controller::HTTP_OK);
        // }
    }
    public function customwalltv_get(){
    /*
     @Description Untuk mendapatkan Data Public Announcement
    */
     $Key = $this->get('Apikey');
     $Check = $this->model->GetWhere('db_it.public_key','Key',$Key);
     $result = $this->model->getcustomwalltv();
     foreach ($result as $key => $value) {
        $result[$key]['index'] = $key;
     }
     if($result){
         $this->response([
             'status' => true,
             'Message' => 'Data Succesfully Received',
             'data' => $result
         ], REST_Controller::HTTP_OK);
     }else{
         $this->response([
             'status' => true,
             'Message' => 'Data Failed to Received',
             'data' => array()
         ], REST_Controller::HTTP_OK);  
     }
 }
 public function walltvupdate_get(){
        /*
        @Description Untuk mendapatkan Data Public Announcement
        */
        $Key = $this->get('Apikey');
        $id = $this->get('id');


        $result = $this->model->getcustomwalltv($id);
        if($result){
            $this->response([
                'status' => true,
                'Message' => 'Data Succesfully Received',
                'data' => $result
            ], REST_Controller::HTTP_OK);
        }else{
            $this->response([
                'status' => true,
                'Message' => 'Data Failed to Received',
                'data' => array()
            ], REST_Controller::HTTP_OK);  
        }
 }
 public function categorywalltv_get(){
    /*
    @Description Untuk mendapatkan Data Public Announcement
    */
    $Key = $this->get('Apikey');


    $result = $this->model->get('db_walltv.category');
    if($result){
        $this->response([
            'status' => true,
            'Message' => 'Data Succesfully Received',
            'data' => $result
        ], REST_Controller::HTTP_OK);
    }else{
        $this->response([
            'status' => true,
            'Message' => 'Data Failed to Received',
            'data' => array()
        ], REST_Controller::HTTP_OK);  
    }
 }
 public function listdesign_get(){
    /*
    @Description Untuk mengambil Data List Design
    */
    $result = $this->model->get('db_walltv.design');
    if($result){
        $this->response([
            'status' => true,
            'Message' => 'Data Succesfully Received',
            'data' => $result
        ], REST_Controller::HTTP_OK);
    }else{
        $this->response([
            'status' => true,
            'Message' => 'Data Failed to Received',
            'data' => array()
        ], REST_Controller::HTTP_OK);  
    }
 }
 public function listpreset_get(){
    /*
    @Description Untuk mengambil Data List Design
    */
    $id_design = $this->input->get('id_design');
    $where = array
				(
					'id_design' => $id_design
				);
    $result = $this->model->getdata('db_walltv.preset',$where);
    if($result){
        $this->response([
            'status' => true,
            'Message' => 'Data Succesfully Received',
            'data' => $result
        ], REST_Controller::HTTP_OK);
    }else{
        $this->response([
            'status' => true,
            'Message' => 'Data Failed to Received',
            'data' => array()
        ], REST_Controller::HTTP_OK);  
    }
 }
}
?>