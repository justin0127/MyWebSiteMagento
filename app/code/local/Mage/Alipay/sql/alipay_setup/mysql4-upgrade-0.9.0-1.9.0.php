<?php

$installer = $this;


$obflag  = array(
'type'          => 'int',
'label'         => 'obflag',
'default'       => 0,
'visible'       => false,
'required'      => false,
'user_defined'  => true,
'searchable'    => false,
'filterable'    => false,
'comparable'    => false );


$installer->addAttribute('order', 'obflag', $obflag);

$omcstatus  = array(
'type'          => 'int',
'label'         => 'omcstatus',
'default'       => 0,
'visible'       => false,
'required'      => false,
'user_defined'  => true,
'searchable'    => false,
'filterable'    => false,
'comparable'    => false );


$installer->addAttribute('order', 'omcstatus', $omcstatus);