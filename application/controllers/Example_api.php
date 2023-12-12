<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/Custom_REST_Controller.php';

class Example_api extends Custom_REST_Controller
{

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
    }

    public function test_post()
    {
        $user_id = $this->post('user_id');
        $act = $this->post('act');
        $lat = $this->post('lat');
        $lng = $this->post('lng');
        $location = $this->post('location');
        $curr_date = $this->post('curr_date');

        $db_59 = $this->load->database('server59', TRUE);

        if ($act == 'insert') {

            $db_59->insert('db_it.test_geo', array(
                'user_id' => $user_id,
                'lat' => $lat,
                'lng' => $lng,
                'location' => $location,
                'curr_date' => $curr_date,
            ));
        } else {
            $db_59->delete('db_it.test_geo', array('user_id' => $user_id));
        }



        $this->response([
            'status' => true,
            'data' => []
        ], REST_Controller::HTTP_OK);
    }

    public function test_get()
    {
        $user_id = $this->get('user_id');
        $db_59 = $this->load->database('server59', TRUE);
        $data = $db_59->order_by('id', 'desc')->get_where('db_it.test_geo', array(
            'user_id' => $user_id
        ))->result_array();

        $this->response([
            'status' => true,
            'data' => $data
        ], REST_Controller::HTTP_OK);
    }

    public function test_delete()
    {
        $user_id = $this->delete('user_id');
        print_r($user_id);
        exit();
        $db_59 = $this->load->database('server59', TRUE);
        $db_59->delete('db_it.test_geo', array('user_id' => $user_id));

        $this->response([
            'status' => true,
            'data' => []
        ], REST_Controller::HTTP_OK);
    }

    public function users_get()
    {
        $result = array(
            'test' => 'data'
        );
        $this->response([
            'status' => true,
            'data' => $result
        ], REST_Controller::HTTP_OK);
    }

    public function users_post()
    {
        $name = $this->post('name');

        $db_59 = $this->load->database('server59', TRUE);

        $db_59->insert('db_it.test_usr', array('name' => $name));

        $id = $db_59->insert_id();

        $this->response([
            'status' => true,
            'data' => array(
                'user_id' => $id,
                'name' => $name
            )
        ], REST_Controller::HTTP_OK);
    }

    public function users_put()
    {
        # code...
    }

    public function users_delete()
    {
        # code...
    }
}
