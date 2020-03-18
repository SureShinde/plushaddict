<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace STech\Reindex\Controller\Adminhtml\Indexer;

use Magento\Backend\App\Action\Context;

class Reindex extends \Magento\Indexer\Controller\Adminhtml\Indexer
{
    /** @var \Magento\Framework\Indexer\IndexerInterface  */
    protected $indexerFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param \Magento\Indexer\Model\IndexerFactory $indexerFactory
     */
    public function __construct(
        Context $context,
        \Magento\Indexer\Model\IndexerFactory $indexerFactory
    ) {
        $this->indexerFactory = $indexerFactory;
        parent::__construct($context);
    }
    
    /**
     * Turn mview on for the given indexers
     *
     * @return void
     */
    protected function _isAllowed()
    {
        return true;
    }

    public function execute()
    {
        $indexerIds = $this->getRequest()->getParam('indexer_ids');
        if (!is_array($indexerIds)) {
            $this->messageManager->addError(__('Please select indexers.'));
        } else {
            try {
                foreach ($indexerIds as $indexerId) { 
                    $indexer = $this->indexerFactory->create();
                    $indexer->load($indexerId)->reindexAll();
                }

                $this->messageManager->addSuccess(
                    __('Reindex %1 indexer(s).', count($indexerIds))
                );
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __("We couldn't reindex because of an error.")
                );
            }
        }
        $this->_redirect('indexer/indexer/list');
    }
}
