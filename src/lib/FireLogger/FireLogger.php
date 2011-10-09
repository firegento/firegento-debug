<?php
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // FireLogger for PHP (server-side library)
    // http://firelogger.binaryage.com/#php
    //
    // see test.php for sample usage
    //
    // protocol specs: http://wiki.github.com/darwin/firelogger
    //

    // PHP <5.3.x compatibility
    if (!defined('E_DEPRECATED')) define('E_DEPRECATED', 8192);
    if (!defined('E_USER_DEPRECATED')) define('E_USER_DEPRECATED', 16384);

    // some directives, you may define them before including firelogger.php
    if (!defined('FIRELOGGER_VERSION')) define('FIRELOGGER_VERSION', '0.3');
    if (!defined('FIRELOGGER_API_VERSION')) define('FIRELOGGER_API_VERSION', 1);
    if (!defined('FIRELOGGER_MAX_PICKLE_DEPTH')) define('FIRELOGGER_MAX_PICKLE_DEPTH', 10);
    if (!defined('FIRELOGGER_ENCODING')) define('FIRELOGGER_ENCODING', 'UTF-8');
    // ... there is more scattered throught this source, hint: search for constants beginning with "FIRELOGGER_"

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // this class represents logger object
    // logger has name and you can ask him to perform logging for you like this:
    //
    //   $ajax = new FireLogger("ajax");
    //   $ajax->log("info", "hello from ajax logger");
    //   $ajax->log("have", "fun!");
    //
    //  you may also use shortcut helper functions to log into default logger
    //
    //    flog("Hello from PHP!");
    //    fwarn("Warning, %s alert!", "gertruda");
    //    ...
    //
    class FireLogger {
        // global state kept under FireLogger "namespace"
        public static $enabled = true; // enabled by default, but see the code executed after class
        public static $counter = 0; // an aid for ordering log records on client
        public static $loggers = array(); // the array of all instantiated fire-loggers during request
        public static $default; // points to default logger
        public static $error; // points to error logger (for errors trigerred by PHP)
        public static $oldErrorHandler;
        public static $oldExceptionHandler;
        public static $clientVersion = '?';
        public static $recommendedClientVersion = '0.9';

        // logger instance data
        public $name;  // [optional] logger name
        public $style; // [optional] CSS snippet for logger icon in FireLogger console
        public $logs = array(); // array of captured log records, this will be encoded into headers during
        public $levels = array('debug', 'warning', 'info', 'error', 'critical'); // well-known log levels (originated in Python)

        //------------------------------------------------------------------------------------------------------
        function __construct($name='logger', $style=null) {
            $this->name = $name;
            $this->style = $style;
            FireLogger::$loggers[] = $this;
        }
        //------------------------------------------------------------------------------------------------------
        private function pickle($var, $level = 0) {
            if (is_bool($var) || is_null($var) || is_int($var) || is_float($var)) {
                return $var;

            } elseif (is_string($var)) {
                return @iconv('UTF-16', 'UTF-8//IGNORE', iconv(FIRELOGGER_ENCODING, 'UTF-16//IGNORE', $var)); // intentionally @

            } elseif (is_array($var)) {
                static $marker;
                if ($marker === NULL) $marker = uniqid("\x00", TRUE); // detects recursions
                if (isset($var[$marker])) {
                    return '*RECURSION*';

                } elseif ($level < FIRELOGGER_MAX_PICKLE_DEPTH || !FIRELOGGER_MAX_PICKLE_DEPTH) {
                    $var[$marker] = TRUE;
                    $res = array();
                    foreach ($var as $k => &$v) {
                        if ($k !== $marker) $res[self::pickle($k)] = self::pickle($v, $level + 1);
                    }
                    unset($var[$marker]);
                    return $res;

                } else {
                    return '...';
                }

            } elseif (is_object($var)) {
                $arr = (array) $var;
                $arr['__class##'] = get_class($var);

                static $list = array(); // detects recursions
                if (in_array($var, $list, TRUE)) {
                    return '*RECURSION*';

                } elseif ($level < FIRELOGGER_MAX_PICKLE_DEPTH || !FIRELOGGER_MAX_PICKLE_DEPTH) {
                    $list[] = $var;
                    $res = array();
                    foreach ($arr as $k => &$v) {
                        if ($k[0] === "\x00") {
                            $k = substr($k, strrpos($k, "\x00") + 1);
                        }
                        $res[self::pickle($k)] = self::pickle($v, $level + 1);
                    }
                    array_pop($list);
                    return $res;

                } else {
                    return '...';
                }

            } elseif (is_resource($var)) {
                return '*' . get_resource_type($var) . ' resource*';

            } else {
                return '*unknown type*';
            }
        }
        //------------------------------------------------------------------------------------------------------
        private function extract_file_line($trace) {
            while (count($trace) && !array_key_exists('file', $trace[0])) array_shift($trace);
            $thisFile = $trace[0]['file'];
            while (count($trace) && (array_key_exists('file', $trace[0]) && $trace[0]['file']==$thisFile)) array_shift($trace);
            while (count($trace) && !array_key_exists('file', $trace[0])) array_shift($trace);

            if (count($trace)==0) return array("?", "0");
            $file = $trace[0]['file'];
            $line = $trace[0]['line'];
            return array($file, $line);
        }
        //------------------------------------------------------------------------------------------------------
        private function extract_trace($trace) {
            $t = array();
            $f = array();
            foreach ($trace as $frame) {
                // prevent notices about invalid indices, wasn't able to google smart solution, PHP is dumb ass
                $frame += array('file' => null, 'line' => null, 'class' => null, 'type' => null, 'function' => null, 'object' => null, 'args' => null);
                $t[] = array(
                    $frame['file'],
                    $frame['line'],
                    $frame['class'].$frame['type'].$frame['function'],
                    $frame['object']
                );
                $f[] = $frame['args'];
            };
            return array($t, $f);
        }
        //------------------------------------------------------------------------------------------------------
        function log(/*level, fmt, obj1, obj2, ...*/) {
            if (!FireLogger::$enabled) return; // no-op

            $args = func_get_args();
            $fmt = '';
            $level = 'debug';
            if (is_string($args[0]) && in_array($args[0], $this->levels)) {
                $level = array_shift($args);
            }
            if (is_string($args[0])) {
                $fmt = array_shift($args);
            }

            $time = microtime(true);
            $item = array(
                'name' => $this->name,
                'args' => array(),
                'level' => $level,
                'timestamp' => $time,
                'order' => FireLogger::$counter++, // PHP is really fast, timestamp has insufficient resolution for log records ordering
                'time' => gmdate('H:i:s', (int)$time).'.'.substr(fmod($time, 1.0), 2, 3), // '23:53:13.396'
                'template' => $fmt,
                'message' => $fmt // TODO: render reasonable plain text message
            );
            if ($this->style) $item['style'] = $this->style;
            if (count($args) && $args[0] instanceof Exception) {
                // exception with backtrace
                $e = $args[0];
                $trace = $e->getTrace();
                $ti = $this->extract_trace($trace);
                $item['exc_info'] = array(
                    $e->getMessage(),
                    $e->getFile(),
                    $ti[0]
                );
                $item['exc_frames'] = $ti[1];
                $item['exc_text'] = 'exception';
                $item['template'] = $e->getMessage();
                $item['code'] = $e->getCode();
                $item['pathname'] = $e->getFile();
                $item['lineno'] = $e->getLine();
            } else {
                // rich log record
                $trace = debug_backtrace();
                list($file, $line) = $this->extract_file_line($trace);
                $data = array();
                $item['pathname'] = $file;
                $item['lineno'] = $line;
                foreach ($args as $arg) {
                    // override file/line in case we've got passed FireLoggerFileLine
                    if ($arg instanceof FireLoggerFileLine) {
                        $item['pathname'] = $arg->file;
                        $item['lineno'] = $arg->line;
                        continue; // do not process this arg
                    }
                    // override backtrace in case we've got passed FireLoggerBacktrace
                    if ($arg instanceof FireLoggerBacktrace) {
                        $ti = $this->extract_trace($arg->trace);
                        $item['exc_info'] = array(
                            '',
                            '',
                            $ti[0]
                        );
                        $item['exc_frames'] = $ti[1];
                        continue; // do not process this arg
                    }
                    $data[] = $arg;
                }
                $item['args'] = $data;
            }

            $this->logs[] = self::pickle($item);
        }
        //------------------------------------------------------------------------------------------------------
        static function firelogger_error_handler($errno, $errstr, $errfile, $errline, $errcontext) {
            if (headers_sent()) {
                return false; // calls default error handler
            }

            if (!defined('FIRELOGGER_NO_ERROR_FILTERING')) {
                // FIRELOGGER_NO_ERROR_FILTERING causes error_reporting() settings will have no effect
                if (!($errno & error_reporting())) return;
            }

            $errors = array(
                E_WARNING => 'Warning',
                E_USER_WARNING => 'Warning',
                E_NOTICE => 'Notice',
                E_USER_NOTICE => 'Notice',
                E_STRICT => 'Strict standards',
                E_DEPRECATED => 'Deprecated',
                E_USER_DEPRECATED => 'Deprecated',
            );
            $no = isset($errors[$errno]) ? $errors[$errno] : 'Unknown error';
            FireLogger::$error->log('warning', "$no: $errstr", new FireLoggerFileLine($errfile, $errline), new FireLoggerBacktrace(debug_backtrace()));
        }
        //------------------------------------------------------------------------------------------------------
        //
        // Encoding handler
        //   * collects all log messages from all FireLogger instances
        //   * encodes them into HTTP headers
        //
        // see protocol specs at http://wiki.github.com/darwin/firelogger
        //
        static function handler() {
            if (headers_sent($file, $line)) {
                trigger_error("Cannot send FireLogger headers after output have been sent" . ($file ? " (output started at $file:$line)." : "."), E_USER_WARNING);
                return;
            }

            // detector for fatal errors
            if (function_exists('error_get_last')) {
                $error = error_get_last();
                if (in_array($error['type'], array(E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE))) {
                    FireLogger::$default->log(new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']));
                }
            }

            $logs = array();
            foreach (FireLogger::$loggers as $logger) {
                $logs = array_merge($logs, $logger->logs);
            }

            // final encoding
            $id = dechex(mt_rand(0, 0xFFFF)).dechex(mt_rand(0, 0xFFFF)); // mt_rand is not working with 0xFFFFFFFF
            $json = json_encode(array('logs' => $logs));
            $res = str_split(base64_encode($json), 76); // RFC 2045

            foreach($res as $k=>$v) {
                header("FireLogger-$id-$k:$v");
            }
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // helper class for passing file/line override into log methods
    class FireLoggerFileLine {
        public $file;
        public $line;
        function __construct($file, $line) {
            $this->file = $file;
            $this->line = $line;
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // helper class for passing backtrace override into log methods
    class FireLoggerBacktrace {
        public $trace;
        function __construct($trace) {
            $this->trace = $trace;
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // decide if firelogger should be enabled
    if (!defined('FIRELOGGER_NO_VERSION_CHECK')) {
        if (!isset($_SERVER['HTTP_X_FIRELOGGER'])) {
            FireLogger::$enabled = false;
        } else {
            FireLogger::$clientVersion = $_SERVER['HTTP_X_FIRELOGGER'];
            if (FireLogger::$clientVersion!=FireLogger::$recommendedClientVersion) {
                error_log("FireLogger for PHP (v".FIRELOGGER_VERSION.") works best with FireLogger extension of version ".FireLogger::$recommendedClientVersion.". You are currently using extension v".FireLogger::$clientVersion.". Please visit the homepage and install matching versions => http://firelogger.binaryage.com/php");
            }
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // test if firelogger password matches
    if (!defined('FIRELOGGER_NO_PASSWORD_CHECK') && defined('FIRELOGGER_PASSWORD') && FireLogger::$enabled) {
        if (isset($_SERVER['HTTP_X_FIRELOGGERAUTH'])) {
            $clientHash = $_SERVER['HTTP_X_FIRELOGGERAUTH'];
            $serverHash = md5("#FireLoggerPassword#".FIRELOGGER_PASSWORD."#");
            if ($clientHash!==$serverHash) { // passwords do not match
                FireLogger::$enabled = false;
                trigger_error("FireLogger password do not match. Have you specified correct password FireLogger extension?");
            }
        } else {
            FireLogger::$enabled = false; // silently disable firelogger in case client didn't provide requested password
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // register default logger for convenience
    if (!defined('FIRELOGGER_NO_OUTPUT_HANDLER')) {
        if (FireLogger::$enabled) ob_start(); // start output buffering (in case firelogger should be enabled)
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // register default logger for convenience
    if (!defined('FIRELOGGER_NO_DEFAULT_LOGGER')) {
        FireLogger::$default = new FireLogger('php', 'background-color: #767ab6'); // register default firelogger with official PHP logo color :-)
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // shortcut functions for convenience
    if (!defined('FIRELOGGER_NO_CONFLICT')) {
        function flog(/*fmt, obj1, obj2, ...*/) {
            $args = func_get_args();
            call_user_func_array(array(FireLogger::$default, 'log'), $args);
        }
        function fwarn(/*fmt, obj1, obj2, ...*/) {
            $args = func_get_args();
            array_unshift($args, 'warning');
            call_user_func_array(array(FireLogger::$default, 'log'), $args);
        }
        function ferror(/*fmt, obj1, obj2, ...*/) {
            $args = func_get_args();
            array_unshift($args, 'error');
            call_user_func_array(array(FireLogger::$default, 'log'), $args);
        }
        function finfo(/*fmt, obj1, obj2, ...*/) {
            $args = func_get_args();
            array_unshift($args, 'info');
            call_user_func_array(array(FireLogger::$default, 'log'), $args);
        }
        function fcritical(/*fmt, obj1, obj2, ...*/) {
            $args = func_get_args();
            array_unshift($args, 'critical');
            call_user_func_array(array(FireLogger::$default, 'log'), $args);
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // register global handler for uncaught exceptions
    if (!defined('FIRELOGGER_NO_EXCEPTION_HANDLER')) {
        FireLogger::$oldExceptionHandler = set_exception_handler(array(FireLogger::$default, 'log'));
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // register global handler for errors
    if (!defined('FIRELOGGER_NO_ERROR_HANDLER')) {
        FireLogger::$error = new FireLogger('error', 'background-color: #f00');
        FireLogger::$oldErrorHandler = set_error_handler(array('FireLogger', 'firelogger_error_handler'));
    }

    // enable encoding handler
    if (FireLogger::$enabled) register_shutdown_function(array('FireLogger', 'handler'));
