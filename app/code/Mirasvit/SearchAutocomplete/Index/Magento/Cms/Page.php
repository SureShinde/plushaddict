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
 * @package   mirasvit/module-search-autocomplete
 * @version   1.1.106
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SearchAutocomplete\Index\Magento\Cms;

use Magento\Cms\Helper\Page as PageHelper;
use Mirasvit\SearchAutocomplete\Index\AbstractIndex;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Url;
use Magento\Store\Model\StoreManagerInterface;

class Page extends AbstractIndex
{
    /**
     * @var PageHelper
     */
    private $pageHelper;

    public function __construct(
        PageHelper $pageHelper,
        Url $urlBuilder,
        StoreManagerInterface $storeManager
    ) {
        $this->pageHelper   = $pageHelper;
        $this->urlBuilder   = $urlBuilder;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        $items = [];

        /** @var \Magento\Cms\Model\Page $page */
        foreach ($this->getCollection() as $page) {
            $items[] = $this->mapPage($page, $this->storeManager->getStore()->getId());
        }

        return $items;
    }

    /**
     * @param \Magento\Cms\Model\Page $page
     * @return array
     */
    public function mapPage($page, $storeId)
    {
        $map = [
            'name' => $page->getTitle(),
            'url'  => $this->urlBuilder->getUrl($page->getIdentifier(), ['_scope' => $storeId]),
        ];

        return $map;

    }

    public function map($data, $dimensions)
    {
        $dimension = current($dimensions);
        $storeId   = $dimension->getValue();

        foreach ($data as $entityId => $itm) {
            $om = ObjectManager::getInstance();
            $entity = $om->create('Magento\Cms\Model\Page')->load($entityId);

            $map = $this->mapPage($entity, $storeId);
            $data[$entityId]['autocomplete'] = $map;
        }

        return $data;
    }
}
