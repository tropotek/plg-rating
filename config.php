<?php
$config = \App\Config::getInstance();

/** @var \Composer\Autoload\ClassLoader $composer */
$composer = $config->getComposer();
if ($composer)
    $composer->add('Rate\\', dirname(__FILE__));

$routes = $config->getRouteCollection();
if (!$routes) return;

$params = array('role' => 'staff');

$routes->add('Rating Question Manager', new \Tk\Routing\Route('/staff/ratingQuestionManager.html', 'Rate\Controller\Question\Manager::doDefault', $params));
$routes->add('Rating Question Edit', new \Tk\Routing\Route('/staff/ratingQuestionEdit.html', 'Rate\Controller\Question\Edit::doDefault', $params));






