<?php
require_once (MODX_CORE_PATH . 'components/debugtoolbar/vendors/TextHighlighter/Text/Highlighter.php');
require_once (MODX_CORE_PATH . 'components/debugtoolbar/vendors/TextHighlighter/Text/Highlighter/Renderer/Html.php');

    class DebugToolbar extends PDO
    {  
        public static $log = array();  
        public static $queryCount = array();
        public static $petCount = array();
        public static $base_path = '';

        public function __construct($dsn, $username = null, $password = null) { 
            self::$base_path = MODX_CORE_PATH . 'components/logger/';
            parent::__construct($dsn, $username, $password);
        }
      
        public function query($query) {
            $start = microtime(true);  
            $result = parent::query($query);  
            $time = microtime(true) - $start;
            DebugToolbar::$log[] = array('query' => $query, 'time' => round($time * 1000, 3));  
            return $result;  
        }  
      
        public function prepare($query, $driver_options = array()) {
            return new dbtPDOStatement(parent::prepare($query), $this);  
        }  
      
        public static function printLog() {
            global $modx;

            $base_tpl = 'dbtBase';
            $nav_tpl = 'dbtNavItem';
            $kv_tpl = 'dbtBasicItem';
            $timing_tpl = 'dbtTiming';
            $headers_tpl = 'dbtHeaders';
            $panel_tpl = 'dbtPanel';
            $queries_tpl = 'dbtSql';
            $sql_tpl = 'dbtSqlItem';
            $logitem_tpl = 'dbtLogItem';
            $log_tpl = 'dbtLog';
            $parser_tpl = 'dbtParser';
            $parseritem_tpl = 'dbtParserItem';

            $modx_totalTime = ($modx->getMicroTime() - $modx->startTime);
            $modx_queryTime = $modx->queryTime;
            $modx_phpTime = $modx_totalTime - $modx_queryTime;
	          $modx_totalTime = sprintf("%2.4f s", $modx_totalTime);
	          $modx_queryTime = sprintf("%2.4f s", $modx_queryTime);
            $modx_phpTime = sprintf("%2.4f s", $modx_phpTime);
            $modx_source = $modx->resourceGenerated ? "database" : "cache";

            $timing_array = array();
            $timing = '';
            $timing_array['queries'] = $modx_queryTime;
            $timing_array['php'] = $modx_phpTime;
            $timing_array['total'] = $modx_totalTime;
            $timing_array['source'] = $modx_source;
            $timing_array['memory'] = memory_get_usage();
            $timing_array['memory_peak'] = memory_get_peak_usage();
	          if (function_exists('getrusage')) {
		          $cpu_data = getrusage();
              $timing_array['cpu_user'] = $cpu_data['ru_utime.tv_sec'] + $cpu_data['ru_utime.tv_usec'] / 1000000;
              $timing_array['cpu_system'] = $cpu_data['ru_stime.tv_sec'] + $cpu_data['ru_stime.tv_usec'] / 1000000;
	          }

            $ti = 0;
            foreach ($timing_array as $key => $value) {
                $timing .= $modx->getChunk($kv_tpl, array('idx' => $ti, 'key' => $key, 'value' => $value));
                $ti++;
            }
            $panels['timing'] = $modx->getChunk($timing_tpl, array('timing' => $timing));

            $headers = '';
            $hi = 0;
            foreach (getallheaders() as $key => $value) {
                $headers .= $modx->getChunk($kv_tpl, array('idx' => $ti, 'key' => $key, 'value' => $value));
                $hi++;
            }
            $panels['headers'] = $modx->getChunk($headers_tpl, array('headers' => $headers));

            $queries = '';
            $qi = 0;
            $totalTime = 0;

		        $highlighter = Text_Highlighter::factory('SQL');
		        $highlighter->setRenderer(new Text_Highlighter_Renderer_Html(array(
			        'use_language' => true,
		        )));

            foreach(self::$log as $entry) {
                $totalTime += $entry['time'];
                $tProperties = array(
                    'idx' => $qi,
                    'sql' => preg_replace('/<span\s+[^>]*>(\s*)<\/span>/', '\1', $highlighter->highlight($entry['query'])),
                    'queryTime' => $entry['time'],
                    'queryCount' => self::$queryCount[md5($entry['query'])]
                );
                $queries .= $modx->getChunk($sql_tpl, $tProperties);
                $qi++;
            }
            $qProperties = array(
                'queries' => $queries,
                'totalQueries' => count(self::$log),
                'totalTime' => $totalTime,
                'uQueries' => count(self::$queryCount)
            );
            $panels['queries'] = $modx->getChunk($queries_tpl, $qProperties);

            $parser = '';
            $pi = 0;
            $totalCount = 0;
            foreach(self::$petCount as $id => $item) {
                $totalCount += $item['count'];
                $paProperties = array(
                    'id' => $id,
                    'idx' => $pi,
                    'content' => htmlentities($item['content']),
                    'count' => $item['count']
                );
                $parser .= $modx->getChunk($parseritem_tpl, $paProperties);
                $pi++;
            }
            $petProperties = array(
                'parseritems' => $parser,
                'petCount' => $totalCount
            );
            $panels['parser'] = $modx->getChunk($parser_tpl, $petProperties);

            $logfile = MODX_CORE_PATH . 'cache/logs/debug.log';
            if (is_file($logfile)) {
                $logs = '';
                $li = 0;
                $lines = file($logfile);
                foreach ($lines as $line_num => $line) {
                    $logs .= $modx->getChunk($logitem_tpl, array('idx' => $li, 'log' => $line));
                    $li++;
                }
                file_put_contents(MODX_CORE_PATH . 'cache/logs/error.log', file_get_contents($logfile), FILE_APPEND);
                file_put_contents($logfile, '');
                $panels['logs'] = $modx->getChunk($log_tpl, array('logs' => $logs));
            }

            $nav = array();

            foreach ($panels as $title => $value) {
                $nav[] = array(
                    'title' => ucwords($title),
                    'subtitle' => '',
                    'url' => '',
                    'classname' => $title
                );
                $pProperties = array(
                    'title' => ucwords($title),
                    'content' => $value,
                    'id' => $title
                );
                $panels_output .= $modx->getChunk($panel_tpl, $pProperties);
            }

            foreach ($nav as $idx => $value){
                $nav_output .= $modx->getChunk($nav_tpl, $value);
            }
            
            $output .= $modx->getChunk($base_tpl, array('panels' => $panels_output, 'nav' => $nav_output));

            return $output;
        }  
    }  
      
    class dbtPDOStatement {  
        private $statement; 
        private $bindings;
        private $pdo;
        public $query_count;

        public function __construct(PDOStatement $statement, $pdo) {
            $this->statement = $statement;
            $this->pdo = $pdo;
        }  

        public function toSQL($sql, $bindings){
            if (!empty($sql) && !empty($bindings)) {
                reset($bindings);
                $bound = array();
                while (list ($k, $param)= each($bindings)) {
                    if (!is_array($param)) {
                        $v= $param;
                        $type= $this->pdo->getPDOType($param);
                        $bindings[$k]= array(
                            'value' => $v,
                            'type' => $type
                        );
                    } else {
                        $v= $param['value'];
                        $type= $param['type'];
                    }
                    if (!$v) {
                        switch ($type) {
                            case PDO::PARAM_INT:
                                $v= '0';
                                break;
                            case PDO::PARAM_BOOL:
                                $v= '0';
                                break;
                            default:
                                break;
                        }
                    }
                    if (!is_int($k) || substr($k, 0, 1) === ':') {
                        $pattern= '/' . $k . '\b/';
                        if ($type > 0) {
                            $v= $this->pdo->quote($v, $type);
                        } else {
                            $v= 'NULL';
                        }
                        $bound[$pattern] = $v;
                    } else {
                        $parse= create_function('$d,$v,$t, $pdo', 'return $t > 0 ? $pdo->quote($v, $t) : \'NULL\';');
                        $sql= preg_replace("/(\?)/e", '$parse($this,$bindings[$k][\'value\'],$type, $this->pdo);', $sql, 1);
                    }
                }
                if (!empty($bound)) {
                    $sql= preg_replace(array_keys($bound), array_values($bound), $sql);
                }
            }
            return $sql;
        }
      
        public function execute() {
            global $modx;
            $start = microtime(true);
            $result = $this->statement->execute();
            $time = microtime(true) - $start;
            $sql = $this->toSQL($this->statement->queryString, $this->bindings);
            $queryCount = &DebugToolbar::$queryCount;
            if (isset($queryCount[md5($sql)])){
                $queryCount[md5($sql)] ++;
            }else{
                $queryCount[md5($sql)] = 1;
            }
            DebugToolbar::$log[] = array('query' => $sql,'time' => round($time * 1000, 3));
            return $result;  
        }  

        public function bindValue($parameter, $value, $data_type = PDO::PARAM_STR){
            $this->bindings[$parameter] = array('value' => $value, 'type' => $data_type);
            return call_user_func_array(array($this->statement, 'bindValue'), array($parameter, $value, $data_type));
        }

        public function __call($function_name, $parameters) {
            return call_user_func_array(array($this->statement, $function_name), $parameters);  
        }  
    }  