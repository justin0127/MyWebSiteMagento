<?php

/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/LICENSE-M1.txt
 *
 * @category   AW
 * @package    AW_Productquestions
 * @copyright  Copyright (c) 2008-2010 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/LICENSE-M1.txt
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();


try
{
    $tablePrefix = (string) Mage::getConfig()->getTablePrefix();
    // $oldTableName = $tablePrefix.substr($newTableName, strlen($tablePrefix));
    $oldTableName = $tablePrefix.'productquestions';

    $newTableName = $this->getTable('productquestions');

    $installer->run("RENAME TABLE $oldTableName TO $newTableName;");
}
catch (Exception $e) { Mage::log($e); }

$installer->endSetup();

?>
