<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/Custom_REST_Controller.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Labkes extends Custom_REST_Controller{

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model(array(
            'Public_model' => 'model'
        ));
    }
    public function laporanexcel_get(){
        // Load the database library if not already loaded
        $this->load->database();

        // Fetch data from the database (replace 'your_table' with your actual table name)
        $result = $this->model->GetLaporanAll();
        $data = $result;

        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();

        // Set the spreadsheet properties (optional)
        $spreadsheet->getProperties()
            ->setCreator('Your Name')
            ->setTitle('Database Export')
            ->setDescription('Export data from database to Excel');

        // Add data to the spreadsheet
        $spreadsheet->getActiveSheet()->setCellValue('A1', 'Hello, Sheet: ');
           $spreadsheet->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
        $spreadsheet->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
        $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
        // Set column width (adjust as needed)
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(30);
        
        
        $spreadsheet->getActiveSheet()
            ->fromArray($data, null, 'A2');

        // Create a writer object
        $writer = new Xlsx($spreadsheet);

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="export.xlsx"');
        header('Cache-Control: max-age=0');

        // Save the spreadsheet to a file (or output to browser)
        $writer->save('php://output');
    }
    public function login_post(){
        $Username = $this->post('Username');
        $Password = $this->post('Password');
        $data =  [
            'Username' => $Username,
            'Password' => $Password
        ];
        $result = $this->model->Login($Username,$Password);
        if($result){
            $this->response([
                'status' => true,
                'code' => 0,
                'Message' => 'Anda Berhasil Login',
                'data' => $result
            ], REST_Controller::HTTP_OK);
        }else{
            $this->response([
                'status' => false,
                'code' => 1,
                'Message' => 'Username atau Password Salah',
                'data' => array()
            ], REST_Controller::HTTP_OK);
        }

    }
    public function tambahpasien_post(){
        $nama = $this->post('nama');
        $nip = $this->post('nip');
        $nrp = $this->post('nrp');
        $kesatuan = $this->post('kesatuan');
        $tempat_lahir = $this->post('tempat_lahir');
        $tanggal_lahir = $this->post('tanggal_lahir');
        $jenis_kelamin = $this->post('jenis_kelamin');
        $data =  [
            'nama' => $nama,
            'nrp' => $nrp,
            'nip' => $nip,
            'kesatuan' => $kesatuan,
            'tempat_lahir' => $tempat_lahir,
            'tanggal_lahir' => $tanggal_lahir,
            'jenis_kelamin' => $jenis_kelamin,
        ];
        $result = $this->model->Insert("pasien",$data);
        $InsertAkhir = $this->model->GetWhere('pasien','nama',$nama,'DESC');
        if($result){
            $this->response([
                'status' => true,
                'code' => 0,
                'Message' => 'Data Pasien Berhasil Ditambahkan',
                'data' => $InsertAkhir[0],
            ], REST_Controller::HTTP_OK);
        }else{
            $this->response([
                'status' => false,
                'code' => 1,
                'Message' => 'Data Pasien Gagal ditambahkan',
                'data' => array()
            ], REST_Controller::HTTP_OK);
        }

    }
    public function hematologi_post(){
        $id_pasien = $this->post('id_pasien');
        $hematokrit = $this->post('hematokrit');
        $hemogoblin = $this->post('hemogoblin');
        $eritrosit = $this->post('eritrosit');
        $led = $this->post('led');
        $lekosit = $this->post('lekosit');
        $trombosit = $this->post('trombosit');
        $pemeriksa = $this->post('pemeriksa');
        $tanggal_pemeriksaan = $this->post('tanggal_pemeriksaan');
        $no_lab = $this->post('no_lab');

        $Check = $this->model->GetWhere('laporan','id_pasien',$id_pasien,'ASC');
        if($Check){
            // $ID_Laporan = $this->model->GetWhere('laporan','id_laporan',$Check[0]['id_laporan']);
            $data =  [
                'hematologi' => 'true'
            ];
            $Update = $this->model->Update("laporan","id_laporan",$Check[0]['id_laporan'],$data);
            if($Check[0]['hematologi'] == 'true'){
                $update =  [
                    'id_pasien' => $id_pasien,
                    'id_laporan' => $Check[0]['id_laporan'],
                    'hemogoblin' => $hemogoblin,
                    'hematokrit' => $hematokrit,
                    'eritrosit' => $eritrosit,
                    'led' => $led,
                    'lekosit' => $lekosit,
                    'trombosit' => $trombosit,
                    'pemeriksa' => $pemeriksa,
                    'tanggal_pemeriksaan' => $tanggal_pemeriksaan,
                    'no_lab' => $no_lab,
                ];
                $Update = $this->model->Update("hematologi","id_laporan",$Check[0]['id_laporan'],$update);
                if($Update){
                    $this->response([
                        'status' => true,
                        'code' => 0,
                        'Message' => 'Data Berhasil Hematologi Diubah',
                        'data' => $Update
                    ], REST_Controller::HTTP_OK);
                }else{
                    $this->response([
                        'status' => false,
                        'code' => 1,
                        'Message' => 'Data Gagal Disimpan Di bagian Data Hematologi',
                        'data' => array()
                    ], REST_Controller::HTTP_OK);
                }
            }else{
                $insert =  [
                    'id_pasien' => $id_pasien,
                    'id_laporan' => $Check[0]['id_laporan'],
                    'hemogoblin' => $hemogoblin,
                    'hematokrit' => $hematokrit,
                    'eritrosit' => $eritrosit,
                    'led' => $led,
                    'lekosit' => $lekosit,
                    'trombosit' => $trombosit,
                    'pemeriksa' => $pemeriksa,
                    'tanggal_pemeriksaan' => $tanggal_pemeriksaan,
                    'no_lab' => $no_lab
                ];
                $Insert = $this->model->Insert("hematologi",$insert);
                if($Insert){
                    $this->response([
                        'status' => true,
                        'code' => 0,
                        'Message' => 'Data Berhasil Hematologi Ditambahkan',
                        'data' => $Insert
                    ], REST_Controller::HTTP_OK);
                }else{
                    $this->response([
                        'status' => false,
                        'code' => 1,
                        'Message' => 'Data Gagal Disimpan Di bagian Data Hematologi',
                        'data' => array()
                    ], REST_Controller::HTTP_OK);
                }
            }
        }else{
            $input =  [
                'id_pasien' => $id_pasien,
                'hematologi' => "true",
                'kimiadarah' => "false",
                'immunoserologi' => "false",
                'sedimen' => "false",
                'urinalisa' => "false"
            ];
            $result = $this->model->Insert("laporan",$input);
            if($result){
                $DataLaporan = $this->model->GetWhere('laporan','id_pasien',$id_pasien,'DESC');
                $data =  [
                    'id_pasien' => $id_pasien,
                    'id_laporan' => $DataLaporan[0]['id_laporan'],
                    'hemogoblin' => $hemogoblin,
                    'hematokrit' => $hematokrit,
                    'eritrosit' => $eritrosit,
                    'led' => $led,
                    'lekosit' => $lekosit,
                    'trombosit' => $trombosit,
                    'pemeriksa' => $pemeriksa,
                    'tanggal_pemeriksaan' => $tanggal_pemeriksaan,
                    'no_lab' => $no_lab
                ];
                $result2 = $this->model->Insert("hematologi",$data);
                if($result2){
                    $this->response([
                        'status' => true,
                        'code' => 0,
                        'Message' => 'Data Berhasil Hematologi Ditambahkan',
                        'data' => $result
                    ], REST_Controller::HTTP_OK);
                }else{
                    $this->response([
                        'status' => false,
                        'code' => 1,
                        'Message' => 'Data Gagal Disimpan Di bagian Data Hematologi',
                        'data' => array()
                    ], REST_Controller::HTTP_OK);
                }
            }else{
                $this->response([
                    'status' => false,
                    'code' => 1,
                    'Message' => 'Data Gagal Disimpan Di bagian laporan',
                    'data' => array()
                ], REST_Controller::HTTP_OK);
            }
        }
    }
    public function kimiadarah_post(){
        $id_pasien = $this->post('id_pasien');
        $bilurubintotal = $this->post('bilurubintotal');
        $bilurubindirect = $this->post('bilurubindirect');
        $bilurubinindirect = $this->post('bilurubinindirect');
        $sgot = $this->post('sgot');
        $spgt = $this->post('spgt');
        $cholesteroltotal = $this->post('cholesteroltotal');
        $cholesterolldl = $this->post('cholesterolldl');
        $cholesterolhdl = $this->post('cholesterolhdl');
        $trigiliserida = $this->post('trigiliserida');
        $ureum = $this->post('ureum');
        $asamurat = $this->post('asamurat');
        $guladarahpuasa = $this->post('guladarahpuasa');
        $guladarah2jam = $this->post('guladarah2jam');
        $guladarahsewaktu = $this->post('guladarahsewaktu');
        $pemeriksa = $this->post('pemeriksa');
        $tanggal_pemeriksaan = $this->post('tanggal_pemeriksaan');
        $no_lab = $this->post('no_lab');

        $Check = $this->model->GetWhere('laporan','id_pasien',$id_pasien,'ASC');
        if($Check){
            // $ID_Laporan = $this->model->GetWhere('laporan','id_laporan',$Check[0]['id_laporan']);
            if($Check[0]['kimiadarah'] == 'true'){
                $update =  [
                    'id_pasien' => $id_pasien,
                    'id_laporan' => $Check[0]['id_laporan'],
                    'bilurubintotal' => $bilurubintotal,
                    'bilurubindirect' => $bilurubindirect,
                    'bilurubinindirect' => $bilurubinindirect,
                    'sgot' => $sgot,
                    'spgt' => $spgt,
                    'cholesteroltotal' => $cholesteroltotal,
                    'cholesterolldl' => $cholesterolldl,
                    'cholesterolhdl' => $cholesterolhdl,
                    'trigiliserida' => $trigiliserida,
                    'ureum' => $ureum,
                    'asamurat' => $asamurat,
                    'guladarahpuasa' => $guladarahpuasa,
                    'guladarah2jam' => $guladarah2jam,
                    'guladarahsewaktu' => $guladarahsewaktu,
                    'pemeriksa' => $pemeriksa,
                    'tanggal_pemeriksaan' => $tanggal_pemeriksaan,
                    'no_lab' => $no_lab
                ];
                $Update = $this->model->Update("kimiadarah","id_laporan",$Check[0]['id_laporan'],$update);
                if($Update){
                    $this->response([
                        'status' => true,
                        'code' => 0,
                        'Message' => 'Data Berhasil Kimia Darah Diubah',
                        'data' => $Update
                    ], REST_Controller::HTTP_OK);
                }else{
                    $this->response([
                        'status' => false,
                        'code' => 1,
                        'Message' => 'Data Gagal Disimpan Di bagian Data Hematologi',
                        'data' => array()
                    ], REST_Controller::HTTP_OK);
                }
            }else{
                $data =  [
                    'kimiadarah' => 'true'
                ];
                $Update = $this->model->Update("laporan","id_laporan",$Check[0]['id_laporan'],$data);
                $insert =  [
                    'id_pasien' => $id_pasien,
                    'id_laporan' => $Check[0]['id_laporan'],
                    'bilurubintotal' => $bilurubintotal,
                    'bilurubindirect' => $bilurubindirect,
                    'bilurubinindirect' => $bilurubinindirect,
                    'sgot' => $sgot,
                    'spgt' => $spgt,
                    'cholesteroltotal' => $cholesteroltotal,
                    'cholesterolldl' => $cholesterolldl,
                    'cholesterolhdl' => $cholesterolhdl,
                    'trigiliserida' => $trigiliserida,
                    'ureum' => $ureum,
                    'asamurat' => $asamurat,
                    'guladarahpuasa' => $guladarahpuasa,
                    'guladarah2jam' => $guladarah2jam,
                    'guladarahsewaktu' => $guladarahsewaktu,
                    'pemeriksa' => $pemeriksa,
                    'tanggal_pemeriksaan' => $tanggal_pemeriksaan,
                    'no_lab' => $no_lab
                ];
                $Insert = $this->model->Insert("kimiadarah",$insert);
                if($Insert){
                    $this->response([
                        'status' => true,
                        'code' => 0,
                        'Message' => 'Data Berhasil Kimia Darah Ditambahkan',
                        'data' => $Insert
                    ], REST_Controller::HTTP_OK);
                }else{
                    $this->response([
                        'status' => false,
                        'code' => 1,
                        'Message' => 'Data Gagal Disimpan Di bagian Data Kimia Darah',
                        'data' => array()
                    ], REST_Controller::HTTP_OK);
                }
            }
        }else{
            $input =  [
                'id_pasien' => $id_pasien,
                'hematologi' => "false",
                'kimiadarah' => "true",
                'immunoserologi' => "false",
                'sedimen' => "false",
                'urinalisa' => "false"
            ];
            $result = $this->model->Insert("laporan",$input);
            if($result){
                $DataLaporan = $this->model->GetWhere('laporan','id_pasien',$id_pasien,'DESC');
                $data =  [
                    'id_pasien' => $id_pasien,
                    'id_laporan' => $DataLaporan[0]['id_laporan'],
                    'bilurubintotal' => $bilurubintotal,
                    'bilurubindirect' => $bilurubindirect,
                    'bilurubinindirect' => $bilurubinindirect,
                    'sgot' => $sgot,
                    'spgt' => $spgt,
                    'cholesteroltotal' => $cholesteroltotal,
                    'cholesterolldl' => $cholesterolldl,
                    'cholesterolhdl' => $cholesterolhdl,
                    'trigiliserida' => $trigiliserida,
                    'ureum' => $ureum,
                    'asamurat' => $asamurat,
                    'guladarahpuasa' => $guladarahpuasa,
                    'guladarah2jam' => $guladarah2jam,
                    'guladarahsewaktu' => $guladarahsewaktu,
                    'pemeriksa' => $pemeriksa,
                    'tanggal_pemeriksaan' => $tanggal_pemeriksaan,
                    'no_lab' => $no_lab
                ];
                $result2 = $this->model->Insert("kimiadarah",$data);
                if($result2){
                    $this->response([
                        'status' => true,
                        'code' => 0,
                        'Message' => 'Data Berhasil Kimia Darah Ditambahkan',
                        'data' => $result
                    ], REST_Controller::HTTP_OK);
                }else{
                    $this->response([
                        'status' => false,
                        'code' => 1,
                        'Message' => 'Data Gagal Disimpan Di bagian Data Kimia Darah',
                        'data' => array()
                    ], REST_Controller::HTTP_OK);
                }
            }else{
                $this->response([
                    'status' => false,
                    'code' => 1,
                    'Message' => 'Data Gagal Disimpan Di bagian laporan',
                    'data' => array()
                ], REST_Controller::HTTP_OK);
            }
        }
    }
    public function urinalisa_post(){
        $id_pasien = $this->post('id_pasien');
        $warna = $this->post('warna');
        $beratjenis = $this->post('beratjenis');
        $ph = $this->post('ph');
        $lekosit = $this->post('lekosit');
        $nitrit = $this->post('nitrit');
        $glukosa = $this->post('glukosa');
        $protein = $this->post('protein');
        $keton = $this->post('keton');
        $urablinogen = $this->post('urablinogen');
        $bilurubin = $this->post('bilurubin');
        $eryl = $this->post('eryl');
        $pemeriksa = $this->post('pemeriksa');
        $tanggal_pemeriksaan = $this->post('tanggal_pemeriksaan');
        $no_lab = $this->post('no_lab');

        $Check = $this->model->GetWhere('laporan','id_pasien',$id_pasien,'ASC');
        if($Check){
            // $ID_Laporan = $this->model->GetWhere('laporan','id_laporan',$Check[0]['id_laporan']);
            if($Check[0]['urinalisa'] == 'true'){
                $update =  [
                    'id_pasien' => $id_pasien,
                    'id_laporan' => $Check[0]['id_laporan'],
                    'warna' => $warna,
                    'beratjenis' => $beratjenis,
                    'ph' => $ph,
                    'lekosit' => $lekosit,
                    'nitrit' => $nitrit,
                    'glukosa' => $glukosa,
                    'protein' => $protein,
                    'keton' => $keton,
                    'urablinogen' => $urablinogen,
                    'bilurubin' => $bilurubin,
                    'eryl' => $eryl,
                    'pemeriksa' => $pemeriksa,
                    'tanggal_pemeriksaan' => $tanggal_pemeriksaan,
                    'no_lab' => $no_lab
                ];
                $Update = $this->model->Update("urinalisa","id_laporan",$Check[0]['id_laporan'],$update);
                if($Update){
                    $this->response([
                        'status' => true,
                        'code' => 0,
                        'Message' => 'Data Berhasil Urinalisa Diubah',
                        'data' => $Update
                    ], REST_Controller::HTTP_OK);
                }else{
                    $this->response([
                        'status' => false,
                        'code' => 1,
                        'Message' => 'Data Gagal Disimpan Di bagian Data Urinalisa',
                        'data' => array()
                    ], REST_Controller::HTTP_OK);
                }
            }else{
                $data =  [
                    'urinalisa' => 'true'
                ];
                $Update = $this->model->Update("laporan","id_laporan",$Check[0]['id_laporan'],$data);
                $insert =  [
                    'id_pasien' => $id_pasien,
                    'id_laporan' => $Check[0]['id_laporan'],
                    'warna' => $warna,
                    'beratjenis' => $beratjenis,
                    'ph' => $ph,
                    'lekosit' => $lekosit,
                    'nitrit' => $nitrit,
                    'glukosa' => $glukosa,
                    'protein' => $protein,
                    'keton' => $keton,
                    'urablinogen' => $urablinogen,
                    'bilurubin' => $bilurubin,
                    'eryl' => $eryl,
                    'pemeriksa' => $pemeriksa,
                    'tanggal_pemeriksaan' => $tanggal_pemeriksaan,
                    'no_lab' => $no_lab
                ];
                $Insert = $this->model->Insert("urinalisa",$insert);
                if($Insert){
                    $this->response([
                        'status' => true,
                        'code' => 0,
                        'Message' => 'Data Berhasil Urinalisa Ditambahkan',
                        'data' => $Insert
                    ], REST_Controller::HTTP_OK);
                }else{
                    $this->response([
                        'status' => false,
                        'code' => 1,
                        'Message' => 'Data Gagal Disimpan Di bagian Data Urinalisa',
                        'data' => array()
                    ], REST_Controller::HTTP_OK);
                }
            }
        }else{
            $input =  [
                'id_pasien' => $id_pasien,
                'hematologi' => "false",
                'kimiadarah' => "false",
                'immunoserologi' => "false",
                'sedimen' => "false",
                'urinalisa' => "true"
            ];
            $result = $this->model->Insert("laporan",$input);
            if($result){
                $DataLaporan = $this->model->GetWhere('laporan','id_pasien',$id_pasien,'DESC');
                $data =  [
                    'id_pasien' => $id_pasien,
                    'id_laporan' => $DataLaporan[0]['id_laporan'],
                    'warna' => $warna,
                    'beratjenis' => $beratjenis,
                    'ph' => $ph,
                    'lekosit' => $lekosit,
                    'nitrit' => $nitrit,
                    'glukosa' => $glukosa,
                    'protein' => $protein,
                    'keton' => $keton,
                    'urablinogen' => $urablinogen,
                    'bilurubin' => $bilurubin,
                    'eryl' => $eryl,
                    'pemeriksa' => $pemeriksa,
                    'tanggal_pemeriksaan' => $tanggal_pemeriksaan,
                    'no_lab' => $no_lab
                ];
                $result2 = $this->model->Insert("urinalisa",$data);
                if($result2){
                    $this->response([
                        'status' => true,
                        'code' => 0,
                        'Message' => 'Data BerhasilUrinalisa Ditambahkan',
                        'data' => $result
                    ], REST_Controller::HTTP_OK);
                }else{
                    $this->response([
                        'status' => false,
                        'code' => 1,
                        'Message' => 'Data Gagal Disimpan Di bagian Urinalisa',
                        'data' => array()
                    ], REST_Controller::HTTP_OK);
                }
            }else{
                $this->response([
                    'status' => false,
                    'code' => 1,
                    'Message' => 'Data Gagal Disimpan Di bagian laporan',
                    'data' => array()
                ], REST_Controller::HTTP_OK);
            }
        }
    }
    public function sedimen_post(){
        $id_pasien = $this->post('id_pasien');
        $epitel = $this->post('epitel');
        $lekosit = $this->post('lekosit');
        $entrosit = $this->post('entrosit');
        $bakteri = $this->post('bakteri');
        $kristal = $this->post('kristal');
        $pemeriksa = $this->post('pemeriksa');
        $tanggal_pemeriksaan = $this->post('tanggal_pemeriksaan');
        $no_lab = $this->post('no_lab');

        $Check = $this->model->GetWhere('laporan','id_pasien',$id_pasien,'ASC');
        if($Check){
            // $ID_Laporan = $this->model->GetWhere('laporan','id_laporan',$Check[0]['id_laporan']);
            if($Check[0]['sedimen'] == 'true'){
                $update =  [
                    'id_pasien' => $id_pasien,
                    'id_laporan' => $Check[0]['id_laporan'],
                    'epitel' => $epitel,
                    'lekosit' => $lekosit,
                    'entrosit' => $entrosit,
                    'bakteri' => $bakteri,
                    'kristal' => $kristal,
                    'pemeriksa' => $pemeriksa,
                    'tanggal_pemeriksaan' => $tanggal_pemeriksaan,
                    'no_lab' => $no_lab
                ];
                $Update = $this->model->Update("sedimen","id_laporan",$Check[0]['id_laporan'],$update);
                if($Update){
                    $this->response([
                        'status' => true,
                        'code' => 0,
                        'Message' => 'Data Berhasil Sedimen Diubah',
                        'data' => $Update
                    ], REST_Controller::HTTP_OK);
                }else{
                    $this->response([
                        'status' => false,
                        'code' => 1,
                        'Message' => 'Data Gagal Disimpan Di bagian Data Sedimen',
                        'data' => array()
                    ], REST_Controller::HTTP_OK);
                }
            }else{
                $data =  [
                    'sedimen' => 'true'
                ];
                $Update = $this->model->Update("laporan","id_laporan",$Check[0]['id_laporan'],$data);
                $insert =  [
                    'id_pasien' => $id_pasien,
                    'id_laporan' => $Check[0]['id_laporan'],
                    'epitel' => $epitel,
                    'lekosit' => $lekosit,
                    'entrosit' => $entrosit,
                    'bakteri' => $bakteri,
                    'kristal' => $kristal,
                    'pemeriksa' => $pemeriksa,
                    'tanggal_pemeriksaan' => $tanggal_pemeriksaan,
                    'no_lab' => $no_lab
                ];
                $Insert = $this->model->Insert("sedimen",$insert);
                if($Insert){
                    $this->response([
                        'status' => true,
                        'code' => 0,
                        'Message' => 'Data Berhasil Sedimen Ditambahkan',
                        'data' => $Insert
                    ], REST_Controller::HTTP_OK);
                }else{
                    $this->response([
                        'status' => false,
                        'code' => 1,
                        'Message' => 'Data Gagal Disimpan Di bagian Data Sedimen',
                        'data' => array()
                    ], REST_Controller::HTTP_OK);
                }
            }
        }else{
            $input =  [
                'id_pasien' => $id_pasien,
                'hematologi' => "false",
                'kimiadarah' => "false",
                'immunoserologi' => "false",
                'sedimen' => "true",
                'urinalisa' => "false"
            ];
            $result = $this->model->Insert("laporan",$input);
            if($result){
                $DataLaporan = $this->model->GetWhere('laporan','id_pasien',$id_pasien,'DESC');
                $data =  [
                    'id_pasien' => $id_pasien,
                    'id_laporan' => $DataLaporan[0]['id_laporan'],
                    'epitel' => $epitel,
                    'lekosit' => $lekosit,
                    'entrosit' => $entrosit,
                    'bakteri' => $bakteri,
                    'kristal' => $kristal,
                    'pemeriksa' => $pemeriksa,
                    'tanggal_pemeriksaan' => $tanggal_pemeriksaan,
                    'no_lab' => $no_lab
                ];
                $result2 = $this->model->Insert("sedimen",$data);
                if($result2){
                    $this->response([
                        'status' => true,
                        'code' => 0,
                        'Message' => 'Data Berhasil Sedimen Ditambahkan',
                        'data' => $result
                    ], REST_Controller::HTTP_OK);
                }else{
                    $this->response([
                        'status' => false,
                        'code' => 1,
                        'Message' => 'Data Gagal Disimpan Di bagian Sedimen',
                        'data' => array()
                    ], REST_Controller::HTTP_OK);
                }
            }else{
                $this->response([
                    'status' => false,
                    'code' => 1,
                    'Message' => 'Data Gagal Disimpan Di bagian laporan',
                    'data' => array()
                ], REST_Controller::HTTP_OK);
            }
        }
    }
    public function immunoserologi_post(){
        $id_pasien = $this->post('id_pasien');
        $hbsag = $this->post('hbsag');
        $hiv = $this->post('hiv');
        $vdri = $this->post('vdrl');
        $pemeriksa = $this->post('pemeriksa');
        $tanggal_pemeriksaan = $this->post('tanggal_pemeriksaan');
        $no_lab = $this->post('no_lab');

        $Check = $this->model->GetWhere('laporan','id_pasien',$id_pasien,'ASC');
        if($Check){
            // $ID_Laporan = $this->model->GetWhere('laporan','id_laporan',$Check[0]['id_laporan']);
            if($Check[0]['immunoserologi'] == 'true'){
                $update =  [
                    'id_pasien' => $id_pasien,
                    'id_laporan' => $Check[0]['id_laporan'],
                    'hbsag' => $hbsag,
                    'hiv ' => $hiv ,
                    'vdrl' => $vdri,
                    'pemeriksa' => $pemeriksa,
                    'tanggal_pemeriksaan' => $tanggal_pemeriksaan,
                    'no_lab' => $no_lab
                ];
                $Update = $this->model->Update("immunoserologi","id_laporan",$Check[0]['id_laporan'],$update);
                if($Update){
                    $this->response([
                        'status' => true,
                        'code' => 0,
                        'Message' => 'Data Berhasil Immunoserologi Diubah',
                        'data' => $Update
                    ], REST_Controller::HTTP_OK);
                }else{
                    $this->response([
                        'status' => false,
                        'code' => 1,
                        'Message' => 'Data Gagal Disimpan Di bagian Data Immunoserologi',
                        'data' => array()
                    ], REST_Controller::HTTP_OK);
                }
            }else{
                $data =  [
                    'immunoserologi' => 'true'
                ];
                $Update = $this->model->Update("laporan","id_laporan",$Check[0]['id_laporan'],$data);
                $insert =  [
                    'id_pasien' => $id_pasien,
                    'id_laporan' => $Check[0]['id_laporan'],
                    'hbsag' => $hbsag,
                    'hiv ' => $hiv ,
                    'vdrl' => $vdri,
                    'pemeriksa' => $pemeriksa,
                    'tanggal_pemeriksaan' => $tanggal_pemeriksaan,
                    'no_lab' => $no_lab
                ];
                $Insert = $this->model->Insert("immunoserologi",$insert);
                if($Insert){
                    $this->response([
                        'status' => true,
                        'code' => 0,
                        'Message' => 'Data Berhasil Immunoserologi Ditambahkan',
                        'data' => $Insert
                    ], REST_Controller::HTTP_OK);
                }else{
                    $this->response([
                        'status' => false,
                        'code' => 1,
                        'Message' => 'Data Gagal Disimpan Di bagian Data Immunoserologi',
                        'data' => array()
                    ], REST_Controller::HTTP_OK);
                }
            }
        }else{
            $input =  [
                'id_pasien' => $id_pasien,
                'hematologi' => "false",
                'kimiadarah' => "false",
                'immunoserologi' => "true",
                'sedimen' => "false",
                'urinalisa' => "false"
            ];
            $result = $this->model->Insert("laporan",$input);
            if($result){
                $DataLaporan = $this->model->GetWhere('laporan','id_pasien',$id_pasien,'DESC');
                $data =  [
                    'id_pasien' => $id_pasien,
                    'id_laporan' => $DataLaporan[0]['id_laporan'],
                    'hbsag' => $hbsag,
                    'hiv ' => $hiv ,
                    'vdrl' => $vdri,
                    'pemeriksa' => $pemeriksa,
                    'tanggal_pemeriksaan' => $tanggal_pemeriksaan,
                    'no_lab' => $no_lab
                ];
                $result2 = $this->model->Insert("immunoserologi",$data);
                if($result2){
                    $this->response([
                        'status' => true,
                        'code' => 0,
                        'Message' => 'Data Berhasil Sedimen Ditambahkan',
                        'data' => $result
                    ], REST_Controller::HTTP_OK);
                }else{
                    $this->response([
                        'status' => false,
                        'code' => 1,
                        'Message' => 'Data Gagal Disimpan Di bagian Sedimen',
                        'data' => array()
                    ], REST_Controller::HTTP_OK);
                }
            }else{
                $this->response([
                    'status' => false,
                    'code' => 1,
                    'Message' => 'Data Gagal Disimpan Di bagian laporan',
                    'data' => array()
                ], REST_Controller::HTTP_OK);
            }
        }
    }
    public function datapasien_get(){
        $nama = $this->get('nama');
        $result;
        if($nama=="" || $nama==null){
            $result = $this->model->GetAll("pasien");
        }else{
            $result = $this->model->GetLike('pasien','nama',$nama);
        }
        if($result){
            $this->response([
                'status' => true,
                'code' => 0,
                'Message' => 'Data Pasien Didapatkan',
                'data' => $result
            ], REST_Controller::HTTP_OK);
        }else{
            $this->response([
                'status' => false,
                'code' => 1,
                'Message' => 'Data Pasien Gagal Didapatkan',
                'data' => array()
            ], REST_Controller::HTTP_OK);
        }

    }
    public function laporan_get(){
        $nama = $this->get('nama');
        $result;
        if($nama=="" || $nama==null){
            $result = $this->model->GetLaporanAll();
        }else{
            $result = $this->model->GetLaporanSearch($nama);
        }
        if($result){
            $this->response([
                'status' => true,
                'code' => 0,
                'Message' => 'Data Pasien Didapatkan',
                'data' => $result
            ], REST_Controller::HTTP_OK);
        }else{
            $this->response([
                'status' => false,
                'code' => 1,
                'Message' => 'Data Pasien Gagal Didapatkan',
                'data' => array()
            ], REST_Controller::HTTP_OK);
        }
    }
    public function countlaporan_get(){
        $hematologi = $this->model->count("hematologi");
        $immunoserologi	 = $this->model->count("immunoserologi	");
        $kimiadarah = $this->model->count("kimiadarah");
        $sedimen = $this->model->count("sedimen");
        $urinalisa = $this->model->count("urinalisa");
        $pasien = $this->model->count("pasien");
        
        $this->response([
                    'status' => true,
                    'code' => 0,
                    'Message' => 'Data Pasien Didapatkan',
                    'Hematologi' => $hematologi,
                    'Immunoserologi' => $immunoserologi,
                    'Kimiadarah' => $kimiadarah,
                    'Sedimen' => $sedimen,
                    'Urinalisa' => $urinalisa,
                    'Pasien' => $pasien
        ], REST_Controller::HTTP_OK);
    }
}
?>