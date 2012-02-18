<?php
namespace Quipp;
use Quipp\HTTP\Security;
use Quipp\Module\AccountManagement\Module as AMM;
use Quipp\Validation\Validator;
use Quipp\Validation\StdRules;
use Symfony\Component\HttpFoundation\Request;

/**
 * The core object for a Quipp website
 * @author Resolution Interactive Media
 * @property DB $db Quipp databaes object (make sure is injected in init.php)
 * @method \Monolog\Logger log() log(string $name) Log a message
 */
class Core {
    /**
     * @internal
     */
    protected static $self = null;

    /**
     * @internal
     */
    protected static $cfg = array();

    /**
     * Container for methods
     * @see addMethod
     */
    protected $callbacks = array();

    /**
     * Instances of classes created by the factory method
     */
    protected $instances = array();

    /**
     * @var Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @internal
     */
    final private function __clone() {}

    /**
     * @internal
     */
    protected function __construct(array $config, Request $request) {
        if (null !== static::$self) {
            throw new \RuntimeException('Quipp Core has already been instantiated');
        }

        $this->config($config);
        $this->request = $request;
    }

    /**
     * @param array Configuration for site
     * @return Core
     */
    public static function getInstance(array $config = array(), Request $request = null) {
        if (null === static::$self) {
            if (null === $request) {
                throw new \RuntimeException("A request object must be passed to the constructor");
            }

            $className = get_called_class();
            static::$self = new $className($config, $request);
        } else {
            static::$self->config($config);
            // do I overwrite Request or throw an exetpion? (or just do nothing)...
        }

        return static::$self;
    }

    /**
     * @return Symfony\Component\HttpFoundation\Request
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * Check to see if the HTTP request was made via ajax
     * @return bool
     * @deprecated
     */
    public function isAjax() {
        return $this->request->isXmlHttpRequest();

        static $answer = null;
        if (null === $answer) {
            $xr = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? $_SERVER['HTTP_X_REQUESTED_WITH'] : '';
            $answer = ($xr == 'XMLHttpRequest' ? true : false);
        }

        return $answer;
    }

    /**
     * Run the Security class, ensuring the site is protected
     * @return Quipp\HTTP\Security
     * @throws Quipp\HTTP\Exception
     */
    public function secure() {
        static $instance = null;
        if (null === $instance) {
            $instance = new Security($this);
            $instance->run();
        }

        return $instance;
    }

    public function newValidator() {
        return new Validator($this);
    }

     /**
     * @param string Input to check
     * @param string 4 character string of validation type
     * @throws Exception If $against is not a matching option
     * @return bool
     */
    public function validate($val, $against) {
        static $rules = null;
        if (null === $rules) {
            $rules = new StdRules;
        }

        if (!method_exists($rules, $against)) {
            throw new Exception("{$against} is not a validator");
        }

        return call_user_func(array($rules, $against), $val);
    }

    /**
     * Return a StaticPage route if one exists
     * First we check the site specific location, then the default Quipp locations
     * @param string Name of the static page to look for
     * @return StaticPage\BasePage
     * @throws Exception If page is not found
     */
    public function getPage($name) {
        $name = rtrim($name, '/');
        $ns   = null;
        $ans  = '\\' . $this->config('site_psr') . '\\StaticPage\\' . $name;
        $cns  = __NAMESPACE__ . '\\StaticPage\\' . $name;

        if (class_exists($ans)) {
            $ns = $ans;
        } elseif (class_exists($cns)) {
            $ns = $cns;
        }

        if (null === $ns) {
            throw new Exception("Page {$name} not found");
        }

        $instance = new $ns($this);
        return $instance;
    }

    /**
     * Get a view (HTML) file from the sites theme directory.
     * @param string Name of the file (withouth extension) to fetch
     * @param bool If false return file name, if true require the file for display purposes
     * @return mixed string if found, any return value from the theme file instead
     * @throws Exception If the page was not found
     */
    public function view($name, $return_path = false) {
        $check = $_SERVER['DOCUMENT_ROOT'] . '/themes/' . $this->config('theme') . '/' . $name . '.php';
        if (!is_file($check)) {
            throw new Exception("Theme file not found");
        }

        return ((boolean)$return_path ? $check : require $check);
    }

    /**
     * @return Iterator
     */
    public function listModules() {
        return array_keys($this->config('modules'));
    }

    /**
     * @return Module\ModuleInterface
     * @throws InvalidArgumentException
     */
    public function getModule($name) {
        if (false === ($psr = $this->config("modules.{$name}"))) {
            throw new InvalidArgumentException("Module '{$name}' not found");
        }

        return $this->factory($psr, array($this));
    }

    /**
     * @param string|array
     * @param string
     * @param string
     * @return bool
     */
    public function sendEmail($to, $subject, $body, $alt_body = false) {
    
        $config = $this->config('mailer');
        $class  = $config['class'];
        $mail   = new $class();
        
        $mail->Mailer  = $config['send_using'];
        $to     = (array)$to;
        
        
        $mail->SetFrom($config['from_email'], $config['from_name']);
        $mail->Subject = $subject;
        
        foreach($to as $address) {
            $mail->AddAddress($address);
        }
        
        if ($alt_body !== false) {
            $mail->AltBody = $alt_body;
        }
        
        $mail->MsgHTML($body);
        
        if (!$mail->Send()) {
            $mail->ClearAddresses();
            return false;
        } else {
            $mail->ClearAddresses();
            return true;
        }
    }

    /**
     * Merges any number of arrays of any dimensions, the later overwriting
     * previous keys, unless the key is numeric, in whitch case, duplicated
     * values will not be added.
     *
     * The arrays to be merged are passed as arguments to the function.
     *
     * @param array
     * @return array Resulting array, once all have been merged
     */
    public function array_merge_recursive_replace() {
        // Holds all the arrays passed
        $params =  func_get_args();
   
        // First array is used as the base, everything else overwrites on it
        $return = array_shift($params);
   
        // Merge all arrays on the first array
        foreach ($params as $array) {
            foreach($array as $key => $value) {
                // Numeric keyed values are added (unless already there)
                if(is_numeric($key) && (!in_array($value, $return))) {
                    if(is_array($value)) {
                        $return[] = $this->array_merge_recursive_replace($return[$key], $value);
                    } else {
                        $return[] = $value;
                    }
                      
                // String keyed values are replaced
                } else {
                    if (isset($return[$key]) && is_array($value) && is_array($return[$key])) {
                        $return[$key] = $this->array_merge_recursive_replace($return[$key], $value);
                    } else {
                        $return[$key] = $value;
                    }
                }
            }
        }
   
        return $return;
    }

    /**
     * Return configuration value
     * 
     * @param mixed $value If string: Value key to search for, If array: Merge given array over current config settings
     * @param string $default Default value to return if $value not found
     * @return string|array
     */
    public function config($value = null, $default = false) {
        // Setter
        if(is_array($value)) {
            if(count($value) > 0) {
                // Merge given config settings over any previous ones (if $value is array)
                static::$cfg = $this->array_merge_recursive_replace(static::$cfg, $value);
            }
        // Getter
        } else {
            // Config array is static to persist across multiple instances
            $cfg = static::$cfg;
            
            // No value passed - return entire config array
            if($value === null) { return $cfg; }
            
            // Find value to return
            if(strpos($value, '.') !== false) {
                $cfgValue = $cfg;
                $valueParts = explode('.', $value);
                foreach($valueParts as $valuePart) {
                    if(isset($cfgValue[$valuePart])) {
                        $cfgValue = $cfgValue[$valuePart];
                    } else {
                        $cfgValue = $default;
                    }
                }
            } else {
                $cfgValue = $cfg;
                if(isset($cfgValue[$value])) {
                    $cfgValue = $cfgValue[$value];
                } else {
                    $cfgValue = $default;
                }
            }
            
            return $cfgValue;
        }
    }

    /**
     * Determine if a method exists within the class (including overloaded)
     * @param string
     * @return bool
     */
    public function isMethod($method) {
        if (method_exists($this, $method)) {
            return true;
        }

        return (boolean)isset($this->callbacks[$method]);
    }

   /**
     * Add a custom user method via PHP5.3 closure or PHP callback
     * @param string Method name to be used as callback
     * @param callback
     * @return null
     */
    public function addMethod($method, $callback) {
        $this->callbacks[$method] = $callback;
    }

    /**
     * Run user-added callback
     * @throws BadMethodCallException
     */
    public function __call($method, $args) {
        if(isset($this->callbacks[$method]) && is_callable($this->callbacks[$method])) {
            $callback = $this->callbacks[$method];
            return call_user_func_array($callback, $args);
        } else {
            throw new \BadMethodCallException("Method '" . __CLASS__ . "::" . $method . "' not found or the command is not a valid callback type.");	
        }
    }

    public static function __set_state(array $properties) {

    }

    /**
     * Factory method for loading and instantiating new objects
     *
     * @param string $className Name of the class to attempt to load
     * @param array $params Array of parameters to pass to instantiate new object with
     * @return object Instance of the class requested
     * @throws InvalidArgumentException
     */
    public function factory($className, array $params = array()) {
        if (in_array($this, $params)) {
            $param_cp = $params;
            array_splice($param_cp, array_search($this, $params), 1, 'Quipp\\Core');

            $instanceHash = md5($className) . var_export($param_cp, true);
        } else {
            $instanceHash = md5($className . var_export($params, true));
        }
        
        // Return already instantiated object instance if set
        if(isset($this->instances[$instanceHash])) {
            return $this->instances[$instanceHash];
        }
        
        // Return new class instance
        // Reflection is known for incurring overhead - hack to avoid it if we can
        $paramCount = count($params);
        if(0 === $paramCount) {
            $instance = new $className();
        } elseif(1 === $paramCount) {
            $instance = new $className(current($params));
        } elseif(2 === $paramCount) {
            $instance = new $className($params[0], $params[1]);
        } elseif(3 === $paramCount) {
            $instance = new $className($params[0], $params[1], $params[2]);
        } else {
            $class = new \ReflectionClass($className);
            $instance = $class->newInstanceArgs($args);
        }
        
        return $this->setInstance($instanceHash, $instance);
    }

    /**
     * Class-level object instance cache
     * Note: This function does not check if $class is currently in use or already instantiated.
     *  This will override any previous instances of $class within the $instances array.
     *
     * @param $hash string Hash or name of the object instance to cache
     * @param $instance object Instance of object you wish to cache
     * @return object
     */
    public function setInstance($hash, $instance) {
        $this->instances[$hash] = $instance;
        return $instance;
    }
}