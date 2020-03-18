<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Plugin\Sales\Api;

use Amasty\Rewards\Api;
use Amasty\Rewards\Block\Adminhtml\Sales\Creditmemo as CreditmemoBlock;
use Amasty\Rewards\Helper\Data as RewardsHelper;

class CreditmemoRepositoryInterfacePlugin
{
    /**
     * @var Api\RewardsProviderInterface
     */
    private $rewardsProvider;

    /**
     * @var Api\Data\ExpirationArgumentsInterfaceFactory
     */
    private $expirationFactory;

    public function __construct(
        Api\RewardsProviderInterface $rewardsProvider,
        Api\Data\ExpirationArgumentsInterfaceFactory $expirationFactory
    ) {
        $this->rewardsProvider = $rewardsProvider;
        $this->expirationFactory = $expirationFactory;
    }

    /**
     * @param \Magento\Sales\Api\CreditmemoRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\CreditmemoInterface|\Magento\Sales\Model\Order\Creditmemo $creditmemo
     *
     * @return \Magento\Sales\Api\Data\CreditmemoInterface|\Magento\Sales\Model\Order\Creditmemo
     */
    public function afterSave(\Magento\Sales\Api\CreditmemoRepositoryInterface $subject, $creditmemo)
    {
        if ($amount = $creditmemo->getData(CreditmemoBlock::REFUND_KEY)) {
            $order = $creditmemo->getOrder();
            $comment = __('Refund #%1 for Order #%2', $creditmemo->getIncrementId(), $order->getIncrementId());
            /** @var Api\Data\ExpirationArgumentsInterface $expire */
            $expire = $this->expirationFactory->create();
            $this->rewardsProvider->addPoints(
                $amount,
                $order->getCustomerId(),
                RewardsHelper::REFUND_ACTION,
                $comment,
                $expire
            );
        }

        return $creditmemo;
    }
}
