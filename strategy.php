<?php

require('TheCityStrategy.php');

$conf = array(
    'client_id' => '873763bfdae3661a67378813d5f5a6a93bd50667b7487ba1957aeca3deb1d72a',
    'client_secret' => '0ff8ec98cedd72de1dc39184355bf619ac9583d94a686c69ed72543c1549c74a',
    'redirect_uri' => 'http://localhost/thecity/thecity-php',
    'scope' => 'user_basic',
);
$tc = new TheCityStrategy($conf);
if(!isset($_GET['code'])) {
  $tc->request();
} else {
  $tc->oauth2callback();
}

