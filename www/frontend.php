<?php


require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

if (!in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1'))){
    $configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'prod', false);
} else {
    $configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'local', false);
}

sfContext::createInstance($configuration)->dispatch();
