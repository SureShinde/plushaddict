<?php
/**
 * A Magento 2 module named DAPL/Schedulejob
 * Copyright (C) 2017 DAPL2018
 * 
 * This file included in DAPL/Schedulejob is licensed under OSL 3.0
 * 
 * http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * Please see LICENSE.txt for the full text of the OSL 3.0 license
 */

namespace DAPL\Schedulejob\Cron;

class OrderDispatched
{

    protected $logger;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(\Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        $this->logger->addInfo("Cronjob OrderDispatched is executed.");
    }
}
