<?php
/** http://www.magentix.fr **/

class Magentix_SocialBookmarking_Model_Status extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 2;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => 'Enabled',
            self::STATUS_DISABLED   => 'Disabled'
        );
    }
}