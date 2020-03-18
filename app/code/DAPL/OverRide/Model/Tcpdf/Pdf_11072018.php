<?php
/**
 * @author     DAPL TEAM
 * @package    DAPL_OverRide
 */

namespace DAPL\OverRide\Model\Tcpdf;

class Pdf extends \Fooman\PdfCore\Model\Tcpdf\Pdf
{

   public function setPageMark() {
        if($this->page>1){
            $this->backgroundImage ='';
        }
        $this->intmrk[$this->page] = $this->pagelen[$this->page];
        $this->bordermrk[$this->page] = $this->intmrk[$this->page];
        $this->setContentMark();
    }
}
