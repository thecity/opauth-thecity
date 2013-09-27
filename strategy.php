<?php
session_start();
require('TheCityStrategy.php');

$conf = array(
    'client_id' => '873763bfdae3661a67378813d5f5a6a93bd50667b7487ba1957aeca3deb1d72a',
    'client_secret' => '0ff8ec98cedd72de1dc39184355bf619ac9583d94a686c69ed72543c1549c74a',
    'redirect_uri' => 'http://localhost/thecity/thecity-php',
    'strategy_url_name' => '',
    'scope' => 'user_basic',
);

if(isset($_GET['access_token'])) {
  $conf['access_token'] = $_GET['access_token'];
}

$env = array(
  'host' => ((array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'],
  'path' => '/',
  'callback_url' => 'http://localhost/thecity/thecity-php',
  'callback_transport' => 'session',
  'debug' => true
);

$tc = new TheCityStrategy($conf, $env);

if(!isset($_GET['code'])) {
  $tc->request();
} else {
  $tc->oauth2callback();
}