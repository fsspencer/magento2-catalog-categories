<?php
namespace Codealist\CatalogCategories\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Eav\Model\Entity\TypeFactory as EavTypeFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Model\Product;
use Magento\Store\Model\StoreManagerInterface;
/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var CategoryFactory
     */
    protected $_categoryFactory;
    /**
     * @var CategoryRepositoryInterface
     */
    protected $_categoryRepository;
    /**
     * @var EavTypeFactory
     */
    protected $_eavTypeFactory;
    /**
     * @var SetFactory
     */
    protected $_attributeSetFactory;
    /**
     * @var AttributeSetRepositoryInterface
     */
    protected $_attributeSetRepository;
    /**
     * @var EavSetupFactory
     */
    protected $_eavSetupFactory;

    /**
     * InstallData constructor.
     * @param CategoryFactory $categoryFactory
     * @param CategoryRepositoryInterface $categoryRepository
     * @param EavTypeFactory $eavTypeFactory
     * @param SetFactory $attributeSetFactory
     * @param AttributeSetRepositoryInterface $attributeSetRepository
     * @param EavSetupFactory $eavSetupFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CategoryFactory $categoryFactory,
        CategoryRepositoryInterface $categoryRepository,
        EavTypeFactory $eavTypeFactory,
        SetFactory $attributeSetFactory,
        AttributeSetRepositoryInterface $attributeSetRepository,
        EavSetupFactory $eavSetupFactory,
        StoreManagerInterface $storeManager
    )
    {
        $this->_categoryFactory = $categoryFactory;
        $this->_categoryRepository = $categoryRepository;
        $this->_eavTypeFactory = $eavTypeFactory;
        $this->_attributeSetFactory = $attributeSetFactory;
        $this->_attributeSetRepository = $attributeSetRepository;
        $this->_eavSetupFactory = $eavSetupFactory;
        $this->_storeManager = $storeManager;
    }
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /**
         * Specify your categories and sub-categories
         */
        $categories = [
            'Men Clothes' => [
                'Shirts',
                'Pants',
                'Accessories'
            ],
            'Women Clothes' => [
                'Skirts',
                'Tops',
                'Tunics',
                'Pants',
                'Accessories'
            ],
            'Featured' => [],
            'New Arrivals' => []
        ];

        foreach ($categories as $parentNameCategory => $childCategories){
            $parentCategory = $this->_createCategory($parentNameCategory);
            if (!is_array($childCategories)) continue;
            foreach ($childCategories as $childCategory){
                $this->_createCategory($childCategory, $parentCategory->getId());
            }
        }
    }
    /**
     * @param $name
     * @param int|null $parentId
     * @return \Magento\Catalog\Api\Data\CategoryInterface
     */
    private function _createCategory($name, $parentId = null){
        $categoryTmp = $this->_categoryFactory->create()
            ->loadByAttribute('name', $name);
        if ($categoryTmp) return $categoryTmp;
        $categoryTmp = $this->_categoryFactory->create();
        $categoryTmp->setName($name);
        $categoryTmp->setIsActive(true);
        $categoryTmp->setUrlKey($categoryTmp->formatUrlKey($name));
        $categoryTmp->setStoreId(0);
        if ($parentId) $categoryTmp->setParentId($parentId);
        $category = $this->_categoryRepository->save($categoryTmp);
        return $category;
    }
}