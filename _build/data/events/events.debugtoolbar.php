<?php
/**
* Adds events to DebugToolbar plugin
*
* @package debugtoolbar
* @subpackage build
*/
$events = array();

$events['OnHandleRequest']= $modx->newObject('modPluginEvent');
$events['OnHandleRequest']->fromArray(array(
    'event' => 'OnHandleRequest',
    'priority' => 0,
    'propertyset' => 0,
),'',true,true);

$events['OnParseDocument']= $modx->newObject('modPluginEvent');
$events['OnParseDocument']->fromArray(array(
    'event' => 'OnParseDocument',
    'priority' => 0,
    'propertyset' => 0,
),'',true,true);

$events['OnWebPagePrerender']= $modx->newObject('modPluginEvent');
$events['OnWebPagePrerender']->fromArray(array(
    'event' => 'OnWebPagePrerender',
    'priority' => 0,
    'propertyset' => 0,
),'',true,true);

return $events;