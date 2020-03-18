<?php
/**
 * @author     DAPL TEAM
 * @package    DAPL_OverRide
 */

namespace DAPL\OverRide\Block;

class Invoice extends \Fooman\PdfCustomiser\Block\Invoice
{
    
    /**
     * @return string
     */
    public function getPaymentBlock()
    {
        $payment = $this->getOrder()->getPayment();
        $method = $payment->getMethodInstance();
        $methodTitle = $method->getTitle();
        return $methodTitle;
       
    }
    
    /**
     * @return string
     */
    public function getOrderCommantBlock()
    {

       return trim($this->getOrder()->getData(\Bold\OrderComment\Model\Data\OrderComment::COMMENT_FIELD_NAME));
       
    }

}

