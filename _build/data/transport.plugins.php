<?php

/**
* Package in plugins
*
* @package debugtoolbar
* @subpackage build
*/

function getPluginContent($filename) {
    $o = file_get_contents($filename);
    $o = str_replace('<?php','',$o);
    $o = str_replace('?>','',$o);
    $o = trim($o);
    return $o;
}

$plugins = array();

/* create the plugin object */
$plugins[0] = $modx->newObject('modPlugin');
$plugins[0]->set('name','DebugToolbar');
$plugins[0]->set('description','');
$plugins[0]->set('plugincode', getPluginContent($sources['elements'] . 'plugins/debugtoolbar.php'));
$plugins[0]->set('category', 0);

$events = include $sources['data'] . 'events/events.debugtoolbar.php';
if (is_array($events) && !empty($events)) {
    $plugins[0]->addMany($events);
    $modx->log(xPDO::LOG_LEVEL_INFO,'Packaged in '.count($events).' Plugin Events for DebugToolbar.'); flush();
} else {
    $modx->log(xPDO::LOG_LEVEL_ERROR,'Could not find plugin events for DebugToolbar!');
}
unset($events);

return $plugins;