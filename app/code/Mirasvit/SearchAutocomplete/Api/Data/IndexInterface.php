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


namespace Mirasvit\SearchAutocomplete\Api\Data;

interface IndexInterface
{
    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return number
     */
    public function getIsActive();

    /**
     * @return number
     */
    public function getOrder();

    /**
     * @return number
     */
    public function getLimit();

    /**
     * @param array $data
     * @return $this
     */
    public function addData($data);
}
