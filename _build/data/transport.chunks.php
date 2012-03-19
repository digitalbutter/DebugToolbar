<?php
/**
* @package debugtoolbar
* @subpackage build
*/

function getChunkContent($filename) {
    $o = file_get_contents($filename);
    $o = trim($o);
    return $o;
}

$chunks = array();

$chunks['dbtBase']= $modx->newObject('modChunk');
$chunks['dbtBase']->fromArray(array(
    'name' => 'dbtBase',
    'description' => '',
    'snippet' => getChunkContent($sources['elements'].'chunks/dbtBase.html'),
),'',true,true);

$chunks['dbtBasicItem']= $modx->newObject('modChunk');
$chunks['dbtBasicItem']->fromArray(array(
    'name' => 'dbtBasicItem',
    'description' => '',
    'snippet' => getChunkContent($sources['elements'].'chunks/dbtBasicItem.html'),
),'',true,true);

$chunks['dbtHeaders']= $modx->newObject('modChunk');
$chunks['dbtHeaders']->fromArray(array(
    'name' => 'dbtHeaders',
    'description' => '',
    'snippet' => getChunkContent($sources['elements'].'chunks/dbtHeaders.html'),
),'',true,true);

$chunks['dbtLog']= $modx->newObject('modChunk');
$chunks['dbtLog']->fromArray(array(
    'name' => 'dbtLog',
    'description' => '',
    'snippet' => getChunkContent($sources['elements'].'chunks/dbtLog.html'),
),'',true,true);

$chunks['dbtLogItem']= $modx->newObject('modChunk');
$chunks['dbtLogItem']->fromArray(array(
    'name' => 'dbtLogItem',
    'description' => '',
    'snippet' => getChunkContent($sources['elements'].'chunks/dbtLogItem.html'),
),'',true,true);

$chunks['dbtNavItem']= $modx->newObject('modChunk');
$chunks['dbtNavItem']->fromArray(array(
    'name' => 'dbtNavItem',
    'description' => '',
    'snippet' => getChunkContent($sources['elements'].'chunks/dbtNavItem.html'),
),'',true,true);

$chunks['dbtPanel']= $modx->newObject('modChunk');
$chunks['dbtPanel']->fromArray(array(
    'name' => 'dbtPanel',
    'description' => '',
    'snippet' => getChunkContent($sources['elements'].'chunks/dbtPanel.html'),
),'',true,true);

$chunks['dbtSql']= $modx->newObject('modChunk');
$chunks['dbtSql']->fromArray(array(
    'name' => 'dbtSql',
    'description' => '',
    'snippet' => getChunkContent($sources['elements'].'chunks/dbtSql.html'),
),'',true,true);

$chunks['dbtSqlItem']= $modx->newObject('modChunk');
$chunks['dbtSqlItem']->fromArray(array(
    'name' => 'dbtSqlItem',
    'description' => '',
    'snippet' => getChunkContent($sources['elements'].'chunks/dbtSqlItem.html'),
),'',true,true);

$chunks['dbtTiming']= $modx->newObject('modChunk');
$chunks['dbtTiming']->fromArray(array(
    'name' => 'dbtTiming',
    'description' => '',
    'snippet' => getChunkContent($sources['elements'].'chunks/dbtTiming.html'),
),'',true,true);

$chunks['dbtParserItem']= $modx->newObject('modChunk');
$chunks['dbtParserItem']->fromArray(array(
    'name' => 'dbtParserItem',
    'description' => '',
    'snippet' => getChunkContent($sources['elements'].'chunks/dbtParserItem.html'),
),'',true,true);

$chunks['dbtParser']= $modx->newObject('modChunk');
$chunks['dbtParser']->fromArray(array(
    'name' => 'dbtParser',
    'description' => '',
    'snippet' => getChunkContent($sources['elements'].'chunks/dbtParser.html'),
),'',true,true);

return $chunks;