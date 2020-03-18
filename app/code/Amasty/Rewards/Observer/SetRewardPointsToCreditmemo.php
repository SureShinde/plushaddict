<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Observer;

use Amasty\Rewards\Block\Adminhtml\Sales\Creditmemo as CreditmemoBlock;
use Magento\Framework\Event\ObserverInterface;

class SetRewardPointsToCreditmemo implements ObserverInterface
{
    /**
     * Set reward points balance to creditmemo before register
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $input = $observer->getEvent()->getInput();
        /** @var \Magento\Sales\Model\Order\Creditmemo $creditmemo */
        $creditmemo = $observer->getEvent()->getCreditmemo();
        if (!empty($input[CreditmemoBlock::REFUND_KEY])) {
            $refundPoints = (float)$input[CreditmemoBlock::REFUND_KEY];
            if ($refundPoints) {
                $creditmemo->setData(CreditmemoBlock::REFUND_KEY, $refundPoints);
                $creditmemo->setAllowZeroGrandTotal(true);
            }
        }

        return $this;
    }
}
