<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/Custom_REST_Controller.php';

class Google extends Custom_REST_Controller
{

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->library('googletraining');
        $this->load->library('form_validation');

        $this->load->model(array(
            'authentication_model' => 'auth',
            'training_model' => 'training'
        ));
    }

    public function authGoogleTraning_get()
    {   
        if (isset($_GET['code'])) {           
            try {
                //authenticate user
                $this->googletraining->getAuthenticate();

                //get user info from google
                $gpInfo = $this->googletraining->getUserInfo();

                //preparing data for database insertion
                $userData['oauth_provider'] = 'google';
                $userData['oauth_uid']      = $gpInfo['id'];
                $userData['first_name']     = $gpInfo['given_name'];
                $userData['last_name']      = $gpInfo['family_name'];
                $userData['email']          = $gpInfo['email'];
                $userData['gender']         = !empty($gpInfo['gender']) ? $gpInfo['gender'] : '';
                $userData['locale']         = !empty($gpInfo['locale']) ? $gpInfo['locale'] : '';
                $userData['profile_url']    = !empty($gpInfo['link']) ? $gpInfo['link'] : '';
                $userData['picture_url']    = !empty($gpInfo['picture']) ? $gpInfo['picture'] : '';
                $Data= $this->auth->cekEmail($userData['email']);
                $CheckUser = false;
                // Cek Apakah partisipan atau bukan 
                if($Data['Type']){               
                    if ($Data['Type']=='participant'  && $Data['Type']!='' && $Data['Type']!=null) {

                        $Name = $Data['Name'];
                        $Username = $Data['ParticipantCode'];
                        $Type = 'participant';
                        $key = $Data['Key'];
                        $CheckUser = true;
                        // print_r(url_training_participant.'auth/'.$key);die();
                        redirect(url_training_participant.'auth/'.$key); 
                    } elseif ($Data['Type']=='trainer' && $Data['Type']!='' && $Data['Type']!=null) {

                        $Name = $Data['Name'];
                        $Type = 'trainer';
                        $Username = $Data['TrainerCode'];
                        $key = $Data['Key'];
                        $CheckUser = true;
                        // print_r($key);die();
                        redirect(url_training_trainer.'auth/'.$key); 
                    } else {
                        $this->response([
                                'status' => false,
                                'error' => 'UNAUTHORIZED'
                            ], REST_Controller::HTTP_UNAUTHORIZED);
                        redirect(PORTAL.'training');
                    }
                } else{
                    redirect(PORTAL.'errorPageLoginGoogle');
                }
            
            } catch (Exception $err) {
                redirect(PORTAL);
            }
        }else {
                redirect(PORTAL);

        }
    }


}