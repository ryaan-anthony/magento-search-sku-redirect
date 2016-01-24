<?php

class Ryaan_SkuRedirect_Model_Observer
{
    /**
     * @var Mage_CatalogSearch_Helper_Data
     */
    protected $helper;
    /**
     * @var Mage_Core_Model_Store
     */
    protected $store;

    /**
     * Initialize the class
     * @param array
     */
    public function __construct(array $args = [])
    {
        list($this->helper, $this->store) = $this->checkTypes(
            $this->nullCoalesce($args, 'helper', Mage::helper('catalogsearch')),
            $this->nullCoalesce($args, 'store', Mage::app()->getStore())
        );
    }

    /**
     * Return the value at field in array if it exists. Otherwise, use the
     * default value.
     * @param  array
     * @param  string|int
     * @param  mixed
     * @return mixed
     */
    protected function nullCoalesce(array $arr, $field, $default)
    {
        return isset($arr[$field]) ? $arr[$field] : $default;
    }

    /**
     * Validate constructor parameters.
     * @param Mage_CatalogSearch_Helper_Data
     * @param Mage_Core_Model_Store
     * @return array
     */
    protected function checkTypes(
        Mage_CatalogSearch_Helper_Data $config,
        Mage_Core_Model_Store $store
    ) {
        return func_get_args();
    }

    /**
     * @param Varien_Event_Observer
     */
    public function handleSkuSearchRedirect(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();

        $controllerAction = $event->getControllerAction();

        $query = $this->getQuery();

        $productModel = $this->getProductModel();

        $productResult = $productModel->loadByAttribute('sku', $query->getQueryText());

        if ($productResult instanceof Mage_Catalog_Model_Product) {

            $this->redirectToProductUrl($controllerAction, $productResult);

        }
    }

    /**
     * @return Mage_CatalogSearch_Model_Query
     */
    protected function getQuery()
    {
        $query = $this->helper->getQuery();

        $query->setStoreId($this->store->getId());

        return $query;
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    protected function getProductModel()
    {
        return Mage::getModel('catalog/product');
    }

    /**
     * @param Mage_Core_Controller_Varien_Action
     * @param Mage_Catalog_Model_Product
     */
    protected function redirectToProductUrl(
        Mage_Core_Controller_Varien_Action $controllerAction,
        Mage_Catalog_Model_Product $product
    ) {
        $controllerAction->getResponse()
            ->setRedirect($product->getProductUrl());
    }
}
