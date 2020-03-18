<?php
use Magento\Framework\App\Bootstrap;
include('../app/bootstrap.php');

$bootstrap = Bootstrap::create(BP, $_SERVER); 
$objectManager = $bootstrap->getObjectManager();
$state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('crontab');
$observer = $objectManager->create('Magento\ProductAlert\Model\Observer');
$observer->process();
exit;
//echo 'Kill process completed!';
//exit;