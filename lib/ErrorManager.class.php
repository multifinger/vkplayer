<?php defined( '_EXEC' ) or die( 'Access denied' );
/**
 * Set default values to error reporting variables
 */

/*$errarr = array(
        'display_errors' => 'Off',
        'display_startup_errors' => 'Off',
        'log_errors' => 'On',
        'html_errors' => 'Off'
);

foreach($errarr as $inikey => $inival) {
    ini_set($inikey, $inival);
}*/

//if E_STRICT is not defined, define it
if(!defined('E_STRICT')) define('E_STRICT', 0);

class ErrorManager {
    /**
     * Current log level
     *
     * @var int
     */
    private static $logLevel = E_ALL;
    /**
     * Current debug mode
     *
     * @var boolean
     */
    private static $debug = false;
    /**
     * Error log file, stacktrace will be written to that file and detailed error information
     *
     * @var string
     */
    private static $logFile = '';
    /**
     * Major error log file (php parse error or some other huge error like calling a function on non object variable etc)
     *
     * @var string
     */
    private static $fatalLogFile = '';
    /**
     * Error level, when errormanager needs to call callback function if set and exit execution
     *
     * @var int
     */
    private static $dieLevel = 0;
    /**
     * Callback function to call when script is supposed to die
     *
     * @var string/array
     */
    private static $dieCallBack = null;
    /**
     * Whether to escape outputted debug code between html comment tags or to display it directly on page
     *
     * @var boolean
     */
    private static $asComments = false;

    /**
     * Error code to readable string mappings
     *
     * @var array
     */
    private static $errorTypes = array (
            E_PARSE => 'Parsing Error',

            E_ALL => 'All errors occured at once',

            E_WARNING => 'Warning',
            E_CORE_WARNING => 'Core Warning',
            E_COMPILE_WARNING => 'Compile Warning',
            E_USER_WARNING => 'User Warning',

            E_ERROR => 'Error',
            E_CORE_ERROR => 'Core Error',
            E_COMPILE_ERROR => 'Compile Error',
            E_USER_ERROR => 'User Error',
            E_RECOVERABLE_ERROR => 'Recoverable error',

            E_NOTICE => 'Notice',
            E_USER_NOTICE => 'User Notice',
            E_STRICT => 'Strict Error',
    );

    /**
     * Sensitive data keys that are to be filtered out and replaced with asterisks (*) so they will not be logged into error log
     *
     * @var array
     */
    private static $sensitiveDataKeys = array('password', 'password1', 'password2', 'email', 'pass', 'pass1', 'pass2');

    /**
     * Sets current log level, log level that is caught by errorhandler when occurs
     *
     * @param int $newLogLevel Log level to set the error handler to catch
     * @param boolean $systemlevelalso Whether to set the php error_reporting to set variable too or not (might reduce php overhead of always calling log function when using only potion of available error codes)
     */
    public static function SetLogLevel($newLogLevel, $systemlevelalso = false) {
        self::$logLevel = (int)$newLogLevel;
        if($systemlevelalso) {
            error_reporting((int)$newLogLevel);
        }
    }

    /**
     * Sets the debug mode on or off (debug mode = errors get output to browser, but between <!-- -->
     *
     * @param boolean $debug
     */
    public static function SetDebug($debug, $ascomments = true) {
        self::$debug = $debug === true || $debug === 'On';
        if(self::$debug) {
            $mode = 'On';
            self::$asComments = $ascomments;
        } else {
            $mode = 'Off';
            self::$asComments = false;
        }
        //system error output turning off
        //ini_set('display_errors', $mode);
        //ini_set('display_startup_errors', $mode);
        //ini_set('html_errors', $mode);
    }

    /**
     * Sets main error logfile to new value
     *
     * @param string $logFile Log file where full stacktraces are logged (please use full path if possible)
     * @param boolean $setFatalLogfile Whether to set fatal log file to $logFile + '.sys.log' or not automatically
     */
    public static function SetLogFile($logFile, $setFatalLogfile = true) {
        self::$logFile = $logFile;
        if($setFatalLogfile) {
            self::SetFatalLogFile($logFile . '.sys.log');
        }
    }

    /**
     * Set log file where php will log all fatal errors that php does not allow code to handle
     *
     * @param string $fatalLogFile Filename where fatal errors are logged (please use full path if possible)
     */
    public static function SetFatalLogFile($fatalLogFile) {
        self::$fatalLogFile = $fatalLogFile;
        ini_set('error_log', $fatalLogFile);
    }

    /**
     * Disables the new ErrorManager() instanciating
     *
     */
    private function __construct() {
    }

    /**
     * Function that does all the logging
     *
     * @param int $errno Error number/level
     * @param string $errmsg Error message
     * @param string $filename File where error occured
     * @param int $linenum Line where error occured
     * @param string $vars Function variables
     */
    public static function Log($errno, $errmsg, $filename, $linenum, $vars) {
        global $user;
        //global $user is assumed to be an object that has ->id and ->username fields in it
        if(!($errno & self::$logLevel)) {
            return;
        }
        $time = date('Y-m-d H:i:s');
        //get stack
        $traceArr = debug_backtrace();
        $traceStr = '';
        //jump over the first two items in stack (being ErrorManager::Log() called from __errormanager() bootstrap, being called by php engine)
        if(isset($traceArr) && count($traceArr) > 1) {
            for($i = 1; $i < count($traceArr); $i++) {
                $trace = $traceArr[$i];
                $traceargs = '';
                if(isset($trace['args'])) {
                    if(is_array($trace['args'])) {
                        $traceargs = self::ArrayToString(self::RemoveSensitiveData($trace['args']));
                    } else {
                        $traceargs = self::RemoveSensitiveData($trace['args']);
                    }
                }
                $traceStr .= "\t\t" . $trace['function'] . "(" . $traceargs . ')' .(isset($trace['file']) ? ' in ' . $trace['file'] . (isset($trace['line']) ? ' at line ' . $trace['line'] : '') : '' ) . "\r\n";
            }
        }

        $err = $time . ' ip=' . $_SERVER['REMOTE_ADDR'] . ' user=' .
                ($user && $user->id ?
                ($user->username ? $user->username : '(empty)') . '['.$user->id.']' : 'guest' ) . "\r\n";

        $err .= "\t" . (isset(self::$errorTypes[$errno]) ? self::$errorTypes[$errno] : 'Unkown error code '.$errno ). ': ' . $errmsg . "\r\n";
        $err .= "\t\tfile=" . $filename.' line=' . $linenum . "\r\n";
        $err .= "\t\t\tREQUEST_URI=" . $_SERVER['REQUEST_URI'] . "\r\n";
        if(isset($_GET) && count($_GET)) {
            $err .= "\t\t\t_GET = " . self::ArrayToString(self::RemoveSensitiveData($_GET), 1, "\t\t\t") . "\r\n";
        }
        if(isset($_POST) && count($_POST)) {
            $err .= "\t\t\t_POST = " . self::ArrayToString(self::RemoveSensitiveData($_POST), 1, "\t\t\t") . "\r\n";
        }
        if(isset($_FILES) && count($_FILES)) {
            $err .= "\t\t\t_FILES = " . self::ArrayToString(self::RemoveSensitiveData($_FILES), 1, "\t\t\t") . "\r\n";
        }
        $err .= "Trace:\r\n".$traceStr;

        $err = iconv( 'Windows-1251','UTF-8', $err);

        if(self::$debug) {
            if(self::$asComments) {
                echo "<!--\r\n" . str_replace(array('<!--', '-->'), array('<*!--', '--*>'), $err ). "\r\n-->\r\n";
            } else {
                echo "<pre>\r\n" . str_replace(array('<!--', '-->'), array('<*!--', '--*>'), $err ). "\r\n</pre>\r\n";
            }
        }
        $err .= "\r\n";

        //if logfile is set, let the system log into that file specifically by appending the composed string to it
        if(self::$logFile) {
            error_log($err, 3, self::$logFile);
        } else {
            //otherwise log the error anywhere where the error_log has been set
            error_log($err);
            // OR! we can use error_log_ext() to create well-behaved log file
            // self::error_log_ext();
        }

        //check if script needs to exit
        if($errno & self::$dieLevel) {
            //check callback
            $call = self::$dieCallBack;
            if(!is_null($call)) {
                if (is_callable($call, false, $funcname)) {
                    if(is_array($call)) {
                        $obj = reset($call);
                        $func = next($call);
                        if(is_string($obj)) {
                            $evalstr = $obj.'::'.$func.'($errno, $errmsg, $filename, $linenum, $vars);';
                        } else {
                            $evalstr = '$obj->'.$func.'($errno, $errmsg, $filename, $linenum, $vars);';
                        }
                    } else {
                        $evalstr = $call.'($errno, $errmsg, $filename, $linenum, $vars);';
                    }
                    eval($evalstr);
                }
            }
            exit;
        }
    }

    /**
     * Function that initiates logging of exeption
     *
     * @param Exeption $e Exeption
     */
    public static function LogException($e) {
        self::Log($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine(), null);
    }

    /**
     * Removes sensitive data values from an array recursively
     *
     * @param array $array Array to unsensitize (passed by reference to reduce memory overhead by creating new arrays each time it is called)
     * @return array Unsensitized array
     */
    private static function &RemoveSensitiveData(&$array) {
        if(!is_array($array)) {
            return $array;
        }
        foreach ($array as $k => &$v) {
            if(in_array($k, self::$sensitiveDataKeys, true)) {
                $v = '*' . $k . '*';
            } elseif(is_array($v)) {
                self::RemoveSensitiveData($v);
            }
        }
        return $array;
    }

    /**
     * Converts array to string representation
     *
     * @param array $arr Array to convert to string
     * @param int $level Level of recursion
     * @return string String representation of an array
     */
    private static function ArrayToString(&$arr, $level = 0) {
        $str = '';
        $pad = '';
        for($i = 0; $i < $level; $i++) $pad .= '  ';
        if(is_array($arr)) {
            $str .= "Array(\r\n";
            foreach ($arr as $k => $v) {
                $str .= $pad . '  [' . $k . '] => ' . self::ArrayToString($v, $level + 1);
            }
            $str .= "$pad)\r\n";
        } else {
            return (is_object($arr) ? get_class($arr) : $arr)."\r\n";
        }
        return $str;
    }

    /**
     * Clears/unlinks logfiles
     *
     * @param boolean $delete Whether to delete logfiles or just clear them
     */
    public static function ClearLogs($delete = false) {
        if($delete) {
            @unlink(self::$logFile);
            @unlink(self::$sysLogFile);
        } else {
            @file_put_contents(self::$logFile, '');
            @file_put_contents(self::$sysLogFile, '');
        }
    }

    /**
     * Sets the die/exit error level when script is supposed to stop executing and set callback function that needs to be called before script dies/exits
     *
     * @param int $level Die/exit error level for script (E_ERROR or E_STRICT or nsuch)
     * @param string/array $callback Can be just a string (global function) or array that has two members, first is classname for static classes or object for object instances and second is function name that is called (error log parameters are passed on to function)
     */
    public static function SetDieLevel($level = 0, $callback = null) {
        self::$dieLevel = $level;
        self::$dieCallBack = $callback;
    }

    public  static function Trace($var) {
        echo "<pre>";
        print_r(var_dump($var));
        echo "</pre>";
    }
}

// Set the error handling function to ErrorManager::Log()
// *** set_error_handler(array('ErrorManager', 'Log'));
// *** Not working, can't set Class::Method as handler