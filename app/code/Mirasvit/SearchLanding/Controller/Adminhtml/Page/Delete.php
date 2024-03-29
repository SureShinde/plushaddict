<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-search-landing
 * @version   1.0.8
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SearchLanding\Controller\Adminhtml\Page;

use Mirasvit\SearchLanding\Api\Data\PageInterface;
use Mirasvit\SearchLanding\Controller\Adminhtml\Page;

class Delete extends Page
{

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam(PageInterface::ID);
        $resultRedirect = $this->resultRedirectFactory->create();

        $model = $this->initModel();

        if (!$model->getId() && $id) {
            $this->messageManager->addErrorMessage(__('This page no longer exists.'));

            return $resultRedirect->setPath('*/*/');
        }

        try {
            $this->pageRepository->delete($model);

            $this->messageManager->addSuccessMessage(__('You deleted the page.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->context->getResultRedirectFactory()->create()->setPath('*/*/');
    }
}
