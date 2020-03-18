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



namespace Mirasvit\SearchAutocomplete\Index\Magento\Catalog;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Mirasvit\SearchAutocomplete\Model\Config;
use Magento\Tax\Model\Config as TaxConfig;
use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\CatalogInventory\Helper\Stock as StockHelper;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Theme\Model\View\Design;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Review\Model\ResourceModel\Review\Summary\CollectionFactory as SummaryFactory;
use Magento\Review\Block\Product\ReviewRenderer;

use Mirasvit\SearchAutocomplete\Index\AbstractIndex;
use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Block\Product\ReviewRendererInterface;
use Mirasvit\Core\Service\CompatibilityService;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Product extends AbstractIndex
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\View\LayoutInterface;
     */
    private $layout;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var TaxConfig
     */
    private $taxConfig;

    /**
     * @var CatalogHelper
     */
    protected $catalogHelper;

    /**
     * @var ImageHelper
     */
    protected $imageHelper;

    /**
     * @var StockHelper
     */
    private $stockHelper;

    /**
     * @var PricingHelper
     */
    protected $pricingHelper;

    /**
     * @var design
     */
    protected $design;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \SummaryFactory
     */
    private $summaryFactory;

    /**
     * @var ReviewRenderer
     */
    protected $reviewRenderer;

    /**
     * @var \Magento\Catalog\Block\Product\ListProduct
     */
    private $productBlock;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    private $reviews = [];

    public function __construct(
        TaxConfig $taxConfig,
        Config $config,
        ReviewRenderer $reviewRenderer,
        ImageHelper $imageHelper,
        CatalogHelper $catalogHelper,
        PricingHelper $pricingHelper,
        RequestInterface $request,
        Design $design,
        StockHelper $stockHelper,
        LayoutInterface $layout,
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SummaryFactory $summaryFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->config               = $config;
        $this->reviewRenderer       = $reviewRenderer;
        $this->imageHelper          = $imageHelper;
        $this->catalogHelper        = $catalogHelper;
        $this->pricingHelper        = $pricingHelper;
        $this->request              = $request;
        $this->taxConfig            = $taxConfig;
        $this->design               = $design;
        $this->stockHelper          = $stockHelper;
        $this->layout               = $layout;
        $this->productRepository    = $productRepository;
        $this->searchCriteriaBuilder    = $searchCriteriaBuilder;
        $this->summaryFactory       = $summaryFactory;
        $this->storeManager         = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        $items      = [];
        $categoryId = intval($this->request->getParam('cat'));
        $storeId    = intval($this->request->getParam('store_id'));

        $collection = $this->getCollection();

        $collection->addAttributeToSelect('name')
            ->addAttributeToSelect('short_description')
            ->addAttributeToSelect('description');

        $this->collection->getSelect()->order('score desc');

        if (!$this->config->isOutOfStockAllowed()) {
            $this->stockHelper->addInStockFilterToCollection($this->collection);
        }

        if ($categoryId) {
            $om       = ObjectManager::getInstance();
            $category = $om->create('Magento\Catalog\Model\Category')->load($categoryId);
            $collection->addCategoryFilter($category);
        }

        if ($this->config->isShowRating()) {
            $this->prepareRatingData($collection->getAllIds(), $storeId);
        }
        /** @var \Magento\Catalog\Model\Product $product */
        foreach ($collection as $product) {
            $map = $this->mapProduct($product, $storeId);
            if ($map) {
                $items[] = $map;
            }
        }

        return $items;
    }

    /**
     * @param array                                         $documents
     * @param \Magento\Framework\Search\Request\Dimension[] $dimensions
     *
     * @return array
     */
    public function map($documents, $dimensions)
    {
        if (!$this->config->isFastMode() || count($documents) === 0) {
            return $documents;
        }

        $storeId = current($dimensions)->getValue();
        $productIds = array_keys($documents);

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('entity_id', $productIds, 'in');

        if (CompatibilityService::is20() || CompatibilityService::is21()) {
            // magento disallows addFilter 'store'
        } elseif (CompatibilityService::is22()) {
            $searchCriteria->addFilter('store', $storeId);
        } else {
            $searchCriteria->addFilter('store_id', $storeId);
        }

        $searchCriteria = $searchCriteria->create();
        $productCollection = $this->productRepository->getList($searchCriteria)->getItems();

        if ($this->config->isShowRating()) {
            $this->prepareRatingData($productIds, $storeId);
        }

        foreach ($productCollection as $product) {
            $documents[$product->getId()]['autocomplete'] = $this->mapProduct($product, $storeId);
        }

        return $documents;
    }

    private function prepareRatingData($productIds, $storeId)
    {
        $reviewsCollection = $this->summaryFactory->create()
            ->addEntityFilter($productIds)
            ->addStoreFilter($storeId)
            ->load();

        foreach ($reviewsCollection as $reviewSummary) {
            $this->reviews[$reviewSummary->getData('entity_pk_value')] = $reviewSummary;
        }
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param int                            $storeId
     *
     * @return array
     * @SuppressWarnings(PHPMD)
     */
    public function mapProduct(\Magento\Catalog\Model\Product $product, $storeId = 1)
    {
        $item = [
            'name'        => $product->getName(),
            'url'         => $this->getProductUrl($product, $storeId),
            'sku'         => $this->getSku($product),
            'description' => $this->getDescription($product, $storeId),
            'image'       => $this->getProductImage($product, $storeId),
            'price'       => $this->getPrice($product, $storeId),
            'rating'      => $this->getRating($product, $storeId),
            'cart'        => $this->getCart($product, $storeId),
            'optimize'    => false,
        ];

        return $item;
    }

    /**
     * @return null|string
     */
    private function getProductUrl($product, $storeId)
    {
        $result = $product->getProductUrl();
        $baseUrl = $this->storeManager->getStore($storeId)->getBaseUrl();
        if (strripos($result, $baseUrl) === false) {
            $parsedBaseUrl = parse_url($baseUrl);
            unset($parsedBaseUrl['path']);
            $parsedResult = array_replace(parse_url($result), $parsedBaseUrl);
            $result = $parsedResult["scheme"]. '://' .$parsedResult["host"] . $parsedResult["path"];
        }

        return $result;
    }

    /**
     * @return null|string
     */
    private function getSku($product)
    {
        $result = null;
        if ($this->config->isShowSku()) {
            $result =  html_entity_decode(strip_tags($product->getDataUsingMethod('sku')));
            if (empty($result)) {
                $result = $product->getSku();
            }
        }

        return $result;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return null|string
     */
    private function getDescription($product)
    {
        $result = null;
        if ($this->config->isShowShortDescription()) {
            $result =  html_entity_decode(strip_tags($product->getDataUsingMethod('description')));
            if (empty($result)) {
                $result = strip_tags($product->getShortDescription());
            }
        }

        return $result;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return null|string
     */
    private function getProductImage($product, $storeId)
    {
        $image = null;
        if ($this->config->isShowImage()) {
            $image = false;

            $image = $this->imageHelper->init($product, 'product_page_image_small')
                ->setImageFile($product->getImage())
                ->resize(65 * 2, 80 * 2)
                ->getUrl();

            if (!$image || strpos($image, '/.') !== false) {
                try {
                    $emulation = ObjectManager::getInstance()->get('Magento\Store\Model\App\Emulation');
                    $emulation->startEnvironmentEmulation($storeId, 'frontend', true);
                    $image = $this->imageHelper->getDefaultPlaceholderUrl('thumbnail');
                } catch (\Exception $e) {
                    $this->design->setDesignTheme('Magento/backend', 'adminhtml');
                    $image = $this->imageHelper->getDefaultPlaceholderUrl('thumbnail');
                } finally {
                    $emulation->stopEnvironmentEmulation();
                }
            }
        }

        return $image;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param int                            $storeId
     *
     * @return null|float|string
     */
    private function getPrice($product, $storeId)
    {
        $price = null;

        if ($this->config->isShowPrice()) {
            try {
                $emulation = ObjectManager::getInstance()->get('Magento\Store\Model\App\Emulation');
                $emulation->startEnvironmentEmulation($storeId, 'frontend', true);

                $priceRender = $this->layout->getBlock('product.price.render.default');
                if (!$priceRender) {
                    $priceRender = $this->layout->createBlock(
                        \Magento\Framework\Pricing\Render::class,
                        'product.price.render.default',
                        ['data' => ['price_render_handle' => 'catalog_product_prices']]
                    );
                }
                if ($priceRender) {
                    $price = $priceRender->render(
                        \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                        $product,
                        [
                            'display_minimal_price'  => true,
                            'use_link_for_as_low_as' => true,
                            'zone' => \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST
                        ]
                    );
                }
            } catch (\Exception $e) {
                $price = $product->getMinimalPrice();
                if ($price == 0 && $product->getFinalPrice() > 0) {
                    $price = $product->getFinalPrice();
                } else {
                    $price = $product->getMinPrice();
                }

                $includingTax = $this->taxConfig->getPriceDisplayType() !== TaxConfig::DISPLAY_TYPE_EXCLUDING_TAX;
                $price = $this->catalogHelper->getTaxPrice($product, $price, $includingTax);
                $price = $this->pricingHelper->currency($price, false, false);
            } finally {
                $emulation->stopEnvironmentEmulation();
            }
        }

        return $price;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return array
     */
    private function getCart($product)
    {
        if ($this->productBlock === null) {
            $this->productBlock = ObjectManager::getInstance()
                ->create('Magento\Catalog\Block\Product\ListProduct');
        }

        $cart = [
            'visible' => $this->config->isShowCartButton(),
            'label'   => __('Add to Cart')->render(),
        ];

        $cart['params'] = $this->productBlock->getAddToCartPostParams($product);

        return $cart;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param int                            $storeId
     *
     * @return string | null
     */
    private function getRating($product, $storeId)
    {
        $rating = null;
        if ($this->config->isShowRating() && array_key_exists($product->getId(), $this->reviews)) {
            $product->setData('reviews_count', $this->reviews[$product->getId()]->getReviewsCount());
            $product->setData('rating_summary', $this->reviews[$product->getId()]->getReviewsSummary());

            try {
                $emulation = ObjectManager::getInstance()->get('Magento\Store\Model\App\Emulation');
                $emulation->startEnvironmentEmulation($storeId, 'frontend', true);
                    $rating = $this->reviewRenderer->getReviewsSummaryHtml($product, ReviewRendererInterface::SHORT_VIEW);
            } catch (\Exception $e) {
                $state = ObjectManager::getInstance()->get('Magento\Framework\App\State');
                $state->emulateAreaCode(
                    'frontend',
                    function (&$rating, $product, $storeId) {
                        $rating = $this->reviewRenderer->getReviewsSummaryHtml($product, ReviewRendererInterface::SHORT_VIEW);
                    },
                    [&$rating, $product, $storeId]
                );
            } finally {
                $emulation->stopEnvironmentEmulation();
            }
        }

        return $rating;
    }
}
