<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/Custom_REST_Controller.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class InputPublicAPI extends Custom_REST_Controller
{

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->library('email');
        $this->load->model(array('Public_model' => 'model','authentication_model' => 'auth'));
        $this->load->helper(array("General_helper"));
    }

    public function resetPasswordRequest_post(){
        /*
		@Description Request Reset Password
	    */
        $Email = $this->post('Email');
        $username = $this->model->GetWhere('db_admission.guest','Email',$Email);
        if($username){
            $pas = md5($Email);
            $pass = sha1('Zyarga' . $pas . 'Aurelius' . $this->model->GetTimestamp());
            $data =  [
                'Token' => $pass,
                'Email' => $Email,
                'CreatedAt' => $this->model->GetTimestamp()
            ];
            $result = $this->model->Insert("token_guest",$data);
    
            $mail = new PHPMailer(true);
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();
            $mail->SMTPOptions = array(
                                 'ssl' => array(
                                 'verify_peer' => false,
                                 'verify_peer_name' => false,
                                 'allow_self_signed' => true
                                    )
                                );                                          //Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'ithelpdesk.notif@podomorouniversity.ac.id';                     //SMTP username
            $mail->Password   = 'Podomoro2018';                               //SMTP password
            $mail->SMTPSecure = 'ssl';            //Enable implicit TLS encryption
            $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    
            //Recipients
            $mail->setFrom('no-reply@podomorouniversity.ac.id', 'Podomoro University');
            $mail->addAddress($Email, $Email);     //Add a recipient
            // $mail->addAddress('ellen@example.com');               //Name is optional
            // $mail->addReplyTo('info@example.com', 'Information');
            // $mail->addCC('cc@example.com');
            // $mail->addBCC('bcc@example.com');
    
            // //Attachments
            // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
    
            //Content
    
            $data['token'] = $pass;
    
    
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Reset Password Campus Profile';
            $mail->Body = '<h1>Forget Password</h1>
        
            <table style="width: 100%; border: 1px solid #002060;">
                <tr style="background-color: #002060; ">
                    <td style="padding: 10px;" colspan="2">
                        <font style="float: left; width: 50%;">
                            <img src="https://portal.podomorouniversity.ac.id/assets/icon/logo_pu.png" alt="Avatar" style="width:70%; max-width: 200px;">
                        </font>
                        <font style="float: right; width: 50%; text-align: right; vertical-align: middle; padding-top: 20px;">
                            <h4 style="color: white; "><b>Campus Profile</b></h4>
                        </font>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: justify;">
                        <p>Untuk sdr/i <b>'.$Email.'</b>, dimohon untuk konfirmasi diri:
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: justify;">
                    <center>
                        Tekan tombol dibawah ini untuk membuat password baru:<br><br>
                        
                        <a href="https://api.podomorouniversity.ac.id/publicapi/resetPasswordGuest?Token='.$pass.'" style="background-color: #002060; border: none;color: white;padding: 15px; text-align: center;text-decoration: none;display: inline-block;font-size: 16px; width: 70%;">Reset Password</a>
                        </center><br>
                    </td>
                </tr>
            </table>';
            $mail->send();
                
            $this->response([
                'status' => true,
                'Message' => "Email Telah Terkirim",
            ], REST_Controller::HTTP_OK);
        }else{
            $this->response([
                'status' => true,
                'Message' => "Email Tidak Ditemukan",
            ], REST_Controller::HTTP_OK);
        }

        
    }
    public function guestChangePassword_post(){
        /*
		@Description Untuk Mengubah Password
	    */
        $email = $this->post('email');
        $token = $this->post('token');
        $pass = $this->post('password_baru');
        $conf_pass = $this->post('password_konfirmasi');
      

        $Check = $this->model->GetWhere('token_guest','Token',$token);
        if($Check){
            if($pass==$conf_pass){
                $username = $this->model->GetWhere('db_admission.guest','Email',$email);
                
                $pas = md5($username[0]['Username'] . $pass);
                $passwords = sha1('Zyarga' . $pas . 'Aurelius');
                $data =  [
                    'Password' => $passwords
                ];
                $Update = $this->model->Update("db_admission.guest","Username",$username[0]['Username'],$data);
                if($Update){
                    $this->model->Delete("db_it.token_guest",'Email',$email);
                }
            }
        }
    }
    public function guestRegister_post(){
        $this->email->initialize($config);
        /*
		@Description Untuk mendaftarkan Guest dalam Aplikasi Campus Profile, API Key Wajib disiisi dan didapatkan hanya oleh pihak Developer
	    */

        // $post = $this->input->post();
        $Key = $this->post('Apikey');
        $username = $this->post('Username');
        $password = $this->post('Password');
        $name = $this->post('Name');
        $email = $this->post('Email');
        $phone = $this->post('Phone');
        $address = $this->post('Address');
        $longitude = $this->post('Longitude');
        $latitude = $this->post('Latitude');

        $pas = md5($username . $password);
        $pass = sha1('Zyarga' . $pas . 'Aurelius');
        $data =  [
            'Username' => $username,
            'Password' => $pass,
            'Name' => $name,
            'Email' => $email,
            'Phone' => $phone,
            'Address' => $address,
            'Longitude' => $longitude,
            'Latitude' => $latitude,
        ];
        $Check = $this->model->GetWhere('public_key','Key',$Key);
        if($Check){
            $RegisterChecker = $this->model->GetWhere('db_admission.guest','Username',$username);
            if($RegisterChecker){
                $this->response([
                    'status' => true,
                    'Message' => "Username already used by somone else",
                ], REST_Controller::HTTP_OK);
            }else{
                $result = $this->model->Insert("db_admission.guest",$data);
                if($result){
                    $this->response([
                        'status' => true,
                        'Message' => "User Succesfuly Registered",
                    ], REST_Controller::HTTP_OK);
                }else{
                    $this->response([
                        'status' => false,
                        'Message' => "User Failed to Registered",
                    ], REST_Controller::HTTP_OK);
                }    
            }
        }else{
            $this->response([
                'status' => false,
                'Message' => "Wrong Api Key",
            ], REST_Controller::HTTP_OK);
        }
        
    }
    public function inputFeedback_post(){
        /*
		@Description Untuk Input Feedback
	    */
        $id = $this->post('ID');
        $feedback = $this->post('Feedback');
        $data =  [
            'ID_Guest' => $id,
            'Feedback' => $feedback,
            'CreatedBy' => $feedback,
            'CreatedAt' => $this->model->GetTimestamp(),
            'UpdatedBy' => "",
            'UpdatedAt' => $this->model->GetTimestamp(),
            'Status' => "1",
        ];
        if($id AND $feedback){
            $result = $this->model->Insert("db_it.feedback",$data);
            if($result){
                $this->response([
                    'status' => true,
                    'Message' => "Feedback has been sent",
                ], REST_Controller::HTTP_OK);
            }else{
                $this->response([
                    'status' => true,
                    'Message' => "Feedback Failed to sent",
                ], REST_Controller::HTTP_OK);
            }
        }else{
            $this->response([
                'status' => false,
                'Message' => "ID Guest and Feedback Must Be Filled",
            ], REST_Controller::HTTP_OK);
        }
    }
    public function inputPrivacyAndPolicy_post(){
        /*
		@Description Untuk Menginput Privacy And Policy
	    */
        $Username = $this->rest->user_id;

        $id = $this->post('ID');
        $name = $this->post('Name');
        $link = $this->post('Link');
        $data =  [
            'ID_Version' => $id,
            'Name' => $name,
            'Link' => $link,
            'CreatedBy' => $Username,
            'CreatedAt' => $this->model->GetTimestamp(),
            'UpdatedBy' => $Username,
            'UpdatedAt' => $this->model->GetTimestamp(),
        ];
        $result = $this->model->Insert("db_it.privacy_apps",$data);
        if($result){
            $this->response([
                'status' => true,
                'Message' => "Privacy And Policy Inserted",
            ], REST_Controller::HTTP_OK);
        }else{
            $this->response([
                'status' => true,
                'Message' => "Privacy And Policy Failed to Inserted",
            ], REST_Controller::HTTP_OK);
        }
    }
    public function addVersion_post(){
        /*
		@Description Untuk Mendambahkan Version pada Mobile Apps dan wajib menggunakan akses sebagai Employee
	    */
        $name = $this->post('Name');
        $link = $this->post('Link');
        $Username = $this->post('NIP');
        $Checker = $this->model->GetWhere('db_employees.employees','NIP',$Username);
        if($Checker){
            $data =  [
                'name' => $name,
                'link' => $link,
                'CreatedBy' => $Username,
                'CreatedAt' => $this->model->GetTimestamp(),
                'UpdatedBy' => $Username,
                'UpdatedAt' => $this->model->GetTimestamp(),
            ];
            $result = $this->model->Insert("db_it.app_version",$data);
            if($result){
                $this->response([
                    'status' => true,
                    'Message' => "Version Added",
                ], REST_Controller::HTTP_OK);
            }else{
                $this->response([
                    'status' => false,
                    'Message' => "Version failed added",
                ], REST_Controller::HTTP_OK);
            } 
        }else{
            $this->response([
                'status' => false,
                'Message' => "NIP Not Found",
            ], REST_Controller::HTTP_OK);
        }
        
    }
    public function UpdateVersion_post(){

        /*
		@Description Untuk Mengupdate Version dalam Aplikasi Mobile
	    */
        
        $id = $this->post('ID');
        $name = $this->post('Name');
        $link = $this->post('Link');
        $Username = $this->post('NIP');
        $Checker = $this->model->GetWhere('db_employees.employees','NIP',$Username);

        if($Checker){
            $data =  [
                'Name' => $name,
                'Link' => $link,
                'UpdatedBy' => $Username,
                'UpdatedAt' => $this->model->GetTimestamp(),
            ];
    
            $result = $this->model->Update("db_it.app_version","id",$id,$data);
            if($result){
                $this->response([
                    'status' => true,
                    'Message' => "Version Update Succesfully",
                ], REST_Controller::HTTP_OK);
            }else{
                $this->response([
                    'status' => false,
                    'Message' => "Version Update Failed",
                ], REST_Controller::HTTP_OK);
            }    
        }else{
            $this->response([
                'status' => false,
                'Message' => "NIP Not Found",
            ], REST_Controller::HTTP_OK);
        }
    }
    public function DeleteVersion_post(){
        /*
		@Description Untuk menghapus Version dalam aplikasi Mobile dan wajib menggunakan akses sebagai Employee
	    */
        $id = $this->post('ID');
        $Username = $this->post('NIP');
        $Checker = $this->model->GetWhere('db_employees.employees','NIP',$Username);
        
        if($Checker){
            $data =  [
                'ID' => $id
            ];
            $result = $this->model->Delete("db_it.app_version",'ID',$data);
            if($result){
                $this->response([
                    'status' => true,
                    'Message' => "Delete Version Succesfully",
                ], REST_Controller::HTTP_OK);
            }else{
                $this->response([
                    'status' => false,
                    'Message' => "Delete Version Failed",
                ], REST_Controller::HTTP_OK);
            }    
        }else{
            $this->response([
                'status' => false,
                'Message' => "NIP Not Found",
            ], REST_Controller::HTTP_OK);
        }
    }
    public function detailAddVersion_post(){
        /*
		@Description Untuk Mendambahkan Version pada Mobile Apps dan wajib menggunakan akses sebagai Employee
	    */
        $Username = $this->post('NIP');
        $Checker = $this->model->GetWhere('db_employees.employees','NIP',$Username);

        if($Checker){
            $ID_Version = $this->post('ID_App');
            $Description = $this->post('Description');
            $Version = $this->post('Version');

            
            
            $data =  [
                'ID_App' => $ID_Version,
                'Description' => $Description,
                'Version' => $Version,
                'CreatedBy' => $Username,
                'CreatedAt' => $this->model->GetTimestamp(),
                'UpdatedBy' => $Username,
                'UpdatedAt' => $this->model->GetTimestamp(),
            ];
            $result = $this->model->Insert("db_it.detail_app_version",$data);
            if($result){
                $this->response([
                    'status' => true,
                    'Message' => "Version Added",
                ], REST_Controller::HTTP_OK);
            }else{
                $this->response([
                    'status' => false,
                    'Message' => "Version failed added",
                ], REST_Controller::HTTP_OK);
            }     
        }else{
            $this->response([
                'status' => false,
                'Message' => "NIP Not Found",
            ], REST_Controller::HTTP_OK);
        }



        
    }
    public function detailUpdateVersion_post(){

        /*
		@Description Untuk Mengupdate Version dalam Aplikasi Mobile
	    */
        $Username = $this->post('NIP');
        $Checker = $this->model->GetWhere('db_employees.employees','NIP',$Username);


        if($Checker){
            $id = $this->post('ID');
            $ID_Version = $this->post('ID_App');
            $Description = $this->post('Description');
            $Version = $this->post('Version');

            
            $data =  [
                'ID_App' => $ID_Version,
                'Description' => $Description,
                'Version' => $Version,
                'UpdatedBy' => $Username,
                'UpdatedAt' => $this->model->GetTimestamp(),
            ];

            $result = $this->model->Update("db_it.detail_app_version","ID",$id,$data);
            if($result){
                $this->response([
                    'status' => true,
                    'Message' => "Version Update Succesfully",
                ], REST_Controller::HTTP_OK);
            }else{
                $this->response([
                    'status' => false,
                    'Message' => "Version Update Failed",
                ], REST_Controller::HTTP_OK);
            }    
        }else{
            $this->response([
                'status' => false,
                'Message' => "NIP Not Found",
            ], REST_Controller::HTTP_OK);
        }

        
    }
    public function detailDeleteVersion_post(){
        /*
		@Description Untuk menghapus Version dalam aplikasi Mobile dan wajib menggunakan akses sebagai Employee
	    */
        $Username = $this->post('NIP');
        $Checker = $this->model->GetWhere('db_employees.employees','NIP',$Username);
        
        if($Checker){
            $id = $this->post('ID');

            $data =  [
                'ID' => $Username
            ];
            $result = $this->model->Delete("db_it.app_version",'ID',$data);
            if($result){
                $this->response([
                    'status' => true,
                    'Message' => "Delete Version Succesfully",
                ], REST_Controller::HTTP_OK);
            }else{
                $this->response([
                    'status' => false,
                    'Message' => "Delete Version Failed",
                ], REST_Controller::HTTP_OK);
            }      
        }else{
            $this->response([
                'status' => false,
                'Message' => "NIP Not Found",
            ], REST_Controller::HTTP_OK);
        }
    }
    public function updateApikey_post(){
        /*
		@Description Untuk Mengupdate API Key dan wajib menggunakan akses sebagai Employee
	    */

        $Username = $this->post('NIP');
        $Checker = $this->model->GetWhere('db_employees.employees','NIP',$Username);
        if($Checker){
            $Key= $this->post('Apikey');

            $data =  [
                'Key' => $Key,
                'UpdatedBy' => $Username,
                'UpdatedAt' => $this->model->GetTimestamp(),
            ];
            $result = $this->model->Update("db_it.public_key",'id','1',$data);
            if($result){
                $this->response([
                    'status' => true,
                    'Message' => "API Key Update Succesfully",
                    'New Key' => $Key,
                ], REST_Controller::HTTP_OK);
            }else{
                $this->response([
                    'status' => false,
                    'Message' => "Api Key Update Failed",
                ], REST_Controller::HTTP_OK);
            }    
        }else{
            $this->response([
                'status' => false,
                'Message' => "NIP Not Found",
            ], REST_Controller::HTTP_OK);
        }
    }
    public function inputBrochure_post(){
        /*
		@Description Untuk Mendambahkan Link Brosur, wajib menggunakan akses sebagai Employee
	    */
        $Username = $this->post('NIP');
        $Checker = $this->model->GetWhere('db_employees.employees','NIP',$Username);
        if($Checker){
            $nama = $this->post('Name');
            $link = $this->post('Link');
            $id_prodi = $this->post('IDProdi');
        
            $data =  [
                'Name' => $nama,
                'Link' => $link,
                'ID_Prodi' => $id_prodi,
                'CreatedBy' => $Username,
                'CreatedAt' => $this->model->GetTimestamp(),
                'UpdatedBy' => $Username,
                'UpdatedAt' => $this->model->GetTimestamp()
            ];
            $result = $this->model->Insert("db_admission.brochure",$data);
            if($result){
                $this->response([
                    'status' => true,
                    'Message' => "Brochure Added",
                ], REST_Controller::HTTP_OK);
            }else{
                $this->response([
                    'status' => false,
                    'Message' => "Brochure failed added",
                ], REST_Controller::HTTP_OK);
            }    
        }else{
            $this->response([
                'status' => false,
                'Message' => "NIP Not Found",
            ], REST_Controller::HTTP_OK);
        }
    }
    public function updateBrochure_post(){
        /*
		@Description Untuk Mendambahkan Link Brosur, wajib menggunakan akses sebagai Employee
	    */
        $Username = $this->post('NIP');
        $Checker = $this->model->GetWhere('db_employees.employees','NIP',$Username);
        if($Checker){
            $id = $this->post('ID');
            $nama = $this->post('Name');
            $link = $this->post('Link');
            $id_prodi = $this->post('IDProdi');
            $data =  [
                'Name' => $nama,
                'Link' => $link,
                'ID_Prodi' => $id_prodi,
                'UpdatedBy' => $Username,
                'UpdatedAt' => $this->model->GetTimestamp()
            ];
            $result = $this->model->Update("db_admission.brochure",'id',$id,$data);
            if($result){
                $this->response([
                    'status' => true,
                    'Message' => "Brochure Updated",
                ], REST_Controller::HTTP_OK);
            }else{
                $this->response([
                    'status' => false,
                    'Message' => "Brochure failed Updated",
                ], REST_Controller::HTTP_OK);
            }    
        }else{
            $this->response([
                'status' => false,
                'Message' => "NIP Not Found",
            ], REST_Controller::HTTP_OK);
        }
    }
    public function inputContact_post(){
        /*
		@Description Untuk Mendambahkan Contact Developer atau Admission yang bersangkutan NIP wajib diisi dan harus sudah terdaftar dari db_employee, wajib menggunakan akses sebagai Employee
	    */

        $Username = $this->post('NIP');
        $Checker = $this->model->GetWhere('db_employees.employees','NIP',$Username);
        
        if($Checker){
            $contact = $this->post('Contact');
            $nip = $this->post('NIP');

            $data =  [
                'Contact' => $contact,
                'NIP' => $nip,
                'CreatedBy' => $Username,
                'CreatedAt' => $this->model->GetTimestamp(),
                'UpdatedBy' => $Username,
                'UpdatedAt' => $this->model->GetTimestamp(),
            ];
            $result = $this->model->Insert("db_it.contact",$data);
            if($result){
                $this->response([
                    'status' => true,
                    'Message' => "Contact Added",
                ], REST_Controller::HTTP_OK);
            }else{
                $this->response([
                    'status' => false,
                    'Message' => "Contact failed added",
                ], REST_Controller::HTTP_OK);
            }    
        }else{
            $this->response([
                'status' => false,
                'Message' => "NIP Not Found",
            ], REST_Controller::HTTP_OK);
        }
    }
    public function updateContact_post(){
        /*
		@Description Untuk mengubah Data Contact
	    */
        $Username = $this->post('NIP');
        $Checker = $this->model->GetWhere('db_employees.employees','NIP',$Username);
        $id = $this->input->post('ID');
        $contact = $this->post('Contact');
        $nip = $this->post('NIP');
        if($Checker){
            $data =  [
                'Contact' => $contact,
                'NIP' => $nip,
                'UpdatedBy' => $Username,
                'UpdatedAt' => $this->model->GetTimestamp(),
            ];
            $result = $this->model->Update("db_it.contact",'ID',$id,$data);
            if($result){
                $this->response([
                    'status' => true,
                    'Message' => "Contact Updated",
                ], REST_Controller::HTTP_OK);
            }else{
                $this->response([
                    'status' => false,
                    'Message' => "Contact failed Updated",
                ], REST_Controller::HTTP_OK);
            }     
        }else{
            $this->response([
                'status' => false,
                'Message' => "NIP Not Found",
            ], REST_Controller::HTTP_OK);
        } 
    }
    public function inputPrivacy_post(){
           /*
		@Description Untuk Menginput Privacy and Policy
	    */
        $Username = $this->post('NIP');
        $Checker = $this->model->GetWhere('db_employees.employees','NIP',$Username);
        $ID = $this->input->post('ID_App');
        $Name = $this->input->post('Name');
        $Link = $this->input->post('Link');  
        if($Checker){

    
            $data = [
                'ID_Version' => $ID,
                'Name' => $Name,
                'Link' => $Link,
                'CreatedBy' => $Username,
                'CreatedAt' => $this->model->GetTimestamp(),
                'UpdatedBy' => $Username,
                'UpdatedAt' => $this->model->GetTimestamp(),
            ];
            $result = $this->model->Insert("db_it.privacy_apps",$data);
            if($result){
                $this->response([
                    'status' => true,
                    'Message' => "Privacy & Policy Added",
                ], REST_Controller::HTTP_OK);
            }else{
                $this->response([
                    'status' => false,
                    'Message' => "Privacy & Policy failed to Added",
                ], REST_Controller::HTTP_OK);
            }     
        }else{
            $this->response([
                'status' => false,
                'Message' => "NIP Not Found",
            ], REST_Controller::HTTP_OK);
        }
    }
    public function updatePrivacy_post(){
           /*
		@Description Untuk mengupdate Privacy And Policy
	    */
        $Username = $this->post('NIP');
        $Checker = $this->model->GetWhere('db_employees.employees','NIP',$Username);
        $ID = $this->input->post('ID');
        $ID_App = $this->input->post('ID_App');
        $Name = $this->input->post('Name');
        $Link = $this->input->post('Link');
        if($Checker){
     
            $data = [
                'ID' => $ID,
                'ID_Version' => $ID_App,
                'Name' => $Name,
                'Link' => $Link,
                'CreatedBy' => $Username,
                'CreatedAt' => $this->model->GetTimestamp(),
                'UpdatedBy' => $Username,
                'UpdatedAt' => $this->model->GetTimestamp(),
            ];
            $result = $this->model->Update("db_it.privacy_apps",'ID',$ID,$data);
            if($result){
                $this->response([
                    'status' => true,
                    'Message' => "Privacy Updated",
                ], REST_Controller::HTTP_OK);
            }else{
                $this->response([
                    'status' => false,
                    'Message' => "Privacy failed Updated",
                ], REST_Controller::HTTP_OK);
            }    
        }else{
            $this->response([
                'status' => false,
                'Message' => "NIP Not Found",
            ], REST_Controller::HTTP_OK);
        }
    }
    public function updateCustomWallTV_post(){
        /*
     @Description Untuk mengupdate Privacy And Policy
     */
     $id= $this->post('id');
     $data= $this->post('data');
     $picture= $this->post('picture');

     if($picture=="true"){
        //  Picture
        // Update
        $insert = array
				(
					'data' => $data
				);
        $insertdb = $this->model->insertdatafoto('db_walltv.design_data', 'id', 'data', 'design_data', $insert,false,$id,true);
        // Insert
        // $insertdb = $this->model->insertdatafoto('db_walltv.design_data', 'id', 'image', 'design_data', $insert);
        if($insertdb){
            $this->response([
                'status' => true,
                'Message' => $insertdb,
            ], REST_Controller::HTTP_OK);
        }else{
            $this->response([
                'status' => false,
                'Message' => $insertdb,
            ], REST_Controller::HTTP_OK);
        }       
     }else{
        // String
        $data =  [
            'data' => $data,
        ];
        $result = $this->model->Update("db_walltv.design_data","id",$id,$data);
        if($result){
            $this->response([
                'status' => true,
                'Message' => "Font Size Small Updated",
            ], REST_Controller::HTTP_OK);
        }else{
            $this->response([
                'status' => false,
                'Message' => "Font Size Small Failed Updated",
            ], REST_Controller::HTTP_OK);
        }     
     }
 }
 public function selectdesign_post(){
    /*
    @Description Untuk mengupdate Privacy And Policy
    */
    $id_preset= $this->post('id_preset');
    $data = array
    (
        'status' => 'active'
    );
    $defaultDB = $this->model->UpdateDefault('db_walltv.preset');
    $Update = $this->model->Update("db_walltv.preset","id_preset",$id_preset,$data);
    if($Update){
                $this->response([
                    'status' => true,
                    'Message' => "Data Berhasil Terupdate",
                ], REST_Controller::HTTP_OK);
            }else{
               $this->response([
                'status' => false,
                'Message' => "Data Tidak Berhasil Terupdate",
                ], REST_Controller::HTTP_OK);
            }
           
    }
}
?>