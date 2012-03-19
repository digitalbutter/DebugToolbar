<?php
$settings = array();
$settings['pdo_class']= $modx->newObject('modSystemSetting');
$settings['pdo_class']->fromArray(array(
    'key' => 'pdo_class',
    'value' => 'core.components.debugtoolbar.om.debugtoolbar',
    'xtype' => 'textfield',
    'namespace' => 'debugtoolbar',
    'area' => 'debug',
),'',true,true);

$settings['dbt.show_debug_toolbar']= $modx->newObject('modSystemSetting');
$settings['dbt.show_debug_toolbar']->fromArray(array(
    'key' => 'dbt.show_debug_toolbar',
    'value' => 1,
    'xtype' => 'modx-combo-boolean',
    'namespace' => 'debugtoolbar',
    'area' => 'debug',
),'',true,true);

return $settings;