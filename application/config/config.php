<?php  if (! defined('BASEPATH')) {
     exit('No direct script access allowed');
 }

$config['stripe']['mode']='test'; // test or live
$config['stripe']['sk_test'] = 'sk_test_YOUR_KEY';
$config['stripe']['pk_test'] = 'pk_test_YOUR_KEY';
$config['stripe']['sk_live'] = 'sk_live_YOUR_KEY';
$config['stripe']['pk_live'] = 'pk_live_YOUR_KEY';
$config['stripe']['currency'] = 'usd';