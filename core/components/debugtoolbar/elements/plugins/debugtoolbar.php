<?php
/**
 * @modxDescription Outputs Debug Table
 * @modxCategory    
 * @modxEvent       OnWebPagePrerender
 */
/**
 * @var modX $modx
 * @var array $scriptProperties
 */
if ($modx->context->get('key') == 'mgr' || $modx->getOption('dbt.show_debug_toolbar') != '1') {
	return;
}

if ($modx->event->name == 'OnHandleRequest'){
	$jquery = 'document.write("<script src=\'assets/components/debugtoolbar/js/jquery-1.7.1.min.js\'>\x3C/script>");';
	$modx->regClientScript("<script>window.jQuery || " . $jquery . "</script>");
	$modx->regClientScript(MODX_ASSETS_URL . 'components/debugtoolbar/js/debug_toolbar.js');
	$modx->regClientCSS(MODX_ASSETS_URL . 'components/debugtoolbar/css/debug_toolbar.css');
	$target = array(
	    'target' => 'FILE',
	    'options' => array(
	        'filename' => 'debug.log'),
	);
	$modx->setLogTarget($target);
	$dbt_class = 'core.components.debugtoolbar.om.debugtoolbar';
	$modx->loadClass($dbt_class, MODX_BASE_PATH, true);
}

if ($modx->event->name == 'OnParseDocument'){
	if (isset(DebugToolbar::$petCount[md5($content)])){
		DebugToolbar::$petCount[md5($content)]['count'] ++;
	}else{
		DebugToolbar::$petCount[md5($content)] = array('count' => 1);
	}
	if (DebugToolbar::$petCount[md5($content)]['count'] > 20){
		DebugToolbar::$petCount[md5($content)]['content'] = $content;
	}
}

if ($modx->event->name == 'OnWebPagePrerender'){
	$modx->resource->_output = str_replace('</body>', DebugToolbar::printLog() . '</body>', $modx->resource->_output);
}