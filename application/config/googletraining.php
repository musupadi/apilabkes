<?php defined('BASEPATH') OR exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
|  Google API Configuration
| -------------------------------------------------------------------
|  client_id         string   Your Google API Client ID.
|  client_secret     string   Your Google API Client secret.
|  redirect_uri      string   URL to redirect back to after login.
|  application_name  string   Your Google application name.
|  api_key           string   Developer key.
|  scopes            string   Specify scopes
*/

// test local triner
$config['google']['client_id']        = '652219708720-0ucaogn22na4cmm3koso645ocirv7qer.apps.googleusercontent.com';
$config['google']['client_secret']    = 'a4gx_jX5r7jsZjbcLJyl1S8A';
$config['google']['redirect_uri']     = base_url().'google/authGoogleTraning';
$config['google']['application_name'] = 'Login to Podomoro Training Portal';
$config['google']['api_key']          = '';
$config['google']['scopes']           = array();
