<?php
/**
 * Mollom service for Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Mollom
 * @copyright  Copyright (c) 2008 Ruben Vermeersch (http://www.savanne.be)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Cache_Core
 */
require_once 'Zend/Cache/Core.php';

/**
 * @see Zend_Service_Abstract
 */
require_once 'Zend/Service/Abstract.php';

/**
 * @see Zend_Service_Exception
 */
require_once 'Zend/Service/Exception.php';

/**
 * @see Zend_XmlRpc_Client
 */
require_once 'Zend/XmlRpc/Client.php';

/**
 * @see Zend_XmlRpc_Request
 */
require_once 'Zend/XmlRpc/Request.php';

/**
 * Mollom XML-RPC service implementation
 *
 * @uses Zend_Service_Abstract
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Mollom
 * @throws     Zend_Service_Mollom_UnavailableException
 * @copyright  Copyright (c) 2008 Ruben Vermeersch (http://www.savanne.be)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class Zend_Service_Mollom extends Zend_Service_Abstract
{
    /**
     * Internal cache for mollom servers.
     * 
     * @var Zend_Cache_Core
     * @access private
     */
    private static $_cache = null;

    /** 
     * Hardcoded list of servers, from which the list of available servers
     * will be requested.
     * @var array
     */
    protected $_defaultServers = array(
            'http://xmlrpc1.mollom.com',
            'http://xmlrpc2.mollom.com', 
            'http://xmlrpc3.mollom.com'
        );

    /**
     * List of servers used to do actual API calls.
     * @var array
     */
    private $_servers = array();

    /**
     * Public key
     * @var string
     */
    protected $_publicKey;

    /**
     * Private key
     * @var string
     */
    protected $_privateKey;

    /**
     * The API version supported by this service.
     */
    const API_VERSION = '1.0';

    /**
     * Fault codes
     */
    const FAULT_REFRESH = 1100;
    const FAULT_BUSY = 1200;

    /**
     * Classification labels
     */
    const CLASSIFICATION_HAM = 'ham';
    const CLASSIFICATION_SPAM = 'spam';
    const CLASSIFICATION_UNSURE = 'unsure';

    /**
     * Feedback types
     */
    const FEEDBACK_SPAM = 'spam';
    const FEEDBACK_PROFANITY = 'profanity';
    const FEEDBACK_LOWQUALITY = 'low-quality';
    const FEEDBACK_UNWANTED = 'unwanted';

    /**
     * Statistics types
     */
    const STATISTICS_TOTAL_DAYS         = 'total_days';
    const STATISTICS_TOTAL_ACCEPTED     = 'total_accepted';
    const STATISTICS_TOTAL_REJECTED     = 'total_rejected';
    const STATISTICS_YESTERDAY_ACCEPTED = 'yesterday_accepted';
    const STATISTICS_YESTERDAY_REJECTED = 'yesterday_rejected';
    const STATISTICS_TODAY_ACCEPTED     = 'today_accepted';
    const STATISTICS_TODAY_REJECTED     = 'today_rejected';

    /**
     * Constructor
     *
     * @param string $publicKey The public key
     * @param string $privateKey The private key
     */
    public function __construct($publicKey, $privateKey)
    {
        $this->setPublicKey($publicKey)
             ->setPrivateKey($privateKey);
    }

    /**
     * Get the public key used for making API calls
     *
     * @return string The public key
     */
    public function getPublicKey()
    {
        return $this->_publicKey;
    }

    /**
     * Set the public key used for making API calls
     *
     * @param string $publicKey The public key
     * @return Zend_Service_Mollom
     */
    public function setPublicKey($publicKey)
    {
        $this->_publicKey = (string)$publicKey;
        return $this;
    }

    /**
     * Get the private key used for making API calls
     *
     * @return string The private key
     */
    public function getPrivateKey()
    {
        return $this->_privateKey;
    }

    /**
     * Set the private key used for making API calls
     *
     * @param string $privateKey The private key
     * @return Zend_Service_Mollom
     */
    public function setPrivateKey($privateKey)
    {
        $this->_privateKey = $privateKey;
        return $this;
    }

    /**
     * Returns the set cache
     * 
     * @return Zend_Cache_Core The set cache
     */
    public static function getCache()
    {
        return self::$_cache;
    }

    /**
     * Set a cache for Zend_Service_Mollom
     * 
     * @param Zend_Cache_Core $cache A cache frontend
     */
    public static function setCache(Zend_Cache_Core $cache)
    {
        self::$_cache = $cache;
    }

    /**
     * Returns true when a cache is set
     *
     * @return boolean
     */
    public static function hasCache()
    {
        if (self::$_cache !== null) {
            return true;
        }

        return false;
    }

    /**
     * Removes any set cache
     *
     * @return void
     */
    public static function removeCache()
    {
        self::$_cache = null;
    }

    /**
     * Clears all set cache data
     *
     * @return void
     */
    public static function clearCache()
    {
        self::$_cache->clean();
    }

    /**
     * Validate a captcha.
     *
     * @param string $session_id The ID of the solved captcha
     * @param string $solution The solution of the captcha
     * @return boolean Whether it was solved correctly or not
     */
    public function checkCaptcha($session_id, $solution)
    {
        return $this->_doCall('mollom.checkCaptcha', array('session_id' => $session_id,
                                                           'solution' => $solution));
    }

    /**
     * Check the content to see whether it's spam or not.
     *
     * The $params array can have any of the following keys:
     *      - session_id
     *      - post_title
     *      - post_body
     *      - author_name
     *      - author_url
     *      - author_mail
     *      - author_openid
     *      - author_ip
     *      - author_id
     *
     * The meaning of these parameters is available in the Mollom API documentation:
     * http://mollom.com/api
     *
     * @param array $params A key/value pair containing the content of the post to be checked.
     * @return array An array with a classification label, a rating and a session id.
     */
    public function checkContent(array $params)
    {
        $allowedKeys = array('session_id', 'post_title', 'post_body', 
                             'author_name', 'author_url', 'author_mail', 
                             'author_openid', 'author_ip', 'author_id');

        foreach ($params as $key => $val) {
            if (!in_array($key, $allowedKeys))
                throw new Exception('Illegal key specified');
        }

        $classifications = array(self::CLASSIFICATION_HAM, 
                                 self::CLASSIFICATION_SPAM, 
                                 self::CLASSIFICATION_UNSURE);

        $result = $this->_doCall('mollom.checkContent', $params);
        $result['classification'] = $classifications[$result['spam']-1];
        unset($result['spam']);
        return $result;
    }

    /**
     * Get an audio captcha for a given session id
     *
     * @param string $session_id The session id, will be generated if omitted
     * @param string $author_ip The IP of the content author (the person that will need to solve the captcha)
     * @return array An array containing a session_id and a captcha url.
     */
    public function getAudioCaptcha($session_id = null, $author_ip = null)
    {
        return $this->_doCall('mollom.getAudioCaptcha', array('session_id' => $session_id,
                                                              'author_ip' => $author_ip));
    }

    /**
     * Get an image captcha for a given session id
     *
     * @param string $session_id The session id, will be generated if omitted
     * @param string $author_ip The IP of the content author (the person that will need to solve the captcha)
     * @return array An array containing a session_id and a captcha url.
     */
    public function getImageCaptcha($session_id = null, $author_ip = null)
    {
        return $this->_doCall('mollom.getImageCaptcha', array('session_id' => $session_id,
                                                              'author_ip' => $author_ip));
    }

    /**
     * Get the list of servers used to make API calls
     *
     * @return array An array of strings containing server URLs
     */
    public function getServers()
    {
        if (count($this->_servers) == 0) {
            if (isset(self::$_cache)) {
                $id = 'Zend_Service_Mollom_'.$this->getPublicKey();
                if ($result = self::$_cache->load($id)) {
                    return unserialize($result);
                }
            }
            $this->_updateServerList();
        }
        return $this->_servers;
    }

    /**
     * Retrieve statistics about your mollom usage
     *
     * @param string $type The desired statistic type (check the STATISTICS_* constants for possible values).
     * @return int The value of the requested statistic
     */
    public function getStatistics($type)
    {
        $allowed = array(self::STATISTICS_TOTAL_DAYS, self::STATISTICS_TOTAL_ACCEPTED,
                         self::STATISTICS_TOTAL_REJECTED, self::STATISTICS_YESTERDAY_ACCEPTED,
                         self::STATISTICS_YESTERDAY_REJECTED, self::STATISTICS_TODAY_ACCEPTED,
                         self::STATISTICS_TODAY_REJECTED);
        if (!in_array($type, $allowed)) {
            require_once 'Zend/Service/Exception.php';
            throw new Zend_Service_Exception('Invalid statistics type given.');
        }

        return $this->_doCall('mollom.getStatistics', array('type' => $type));
    }

    /**
     * Send feedback to the Mollom service, used to classify unsure content manually.
     *
     * @param string $session_id The object to which the feedback relates
     * @param string $feedback A feedback rating (see the FEEDBACK_* constants)
     */
    public function sendFeedback($session_id, $feedback)
    {
        $allowed = array(self::FEEDBACK_SPAM, self::FEEDBACK_PROFANITY,
                         self::FEEDBACK_LOWQUALITY, self::FEEDBACK_UNWANTED);
        if (!in_array($feedback, $allowed)) {
            throw new Zend_Service_Exception('Invalid feedback type given');
        }

        $this->_doCall('mollom.sendFeedback', array('session_id' => $session_id, 
                                                    'feedback' => $feedback));
    }

    /**
     * Verify the current key.
     *
     * Will not throw an exception when valid.
     */
    public function verifyKey()
    {
        $this->_doCall('mollom.verifyKey');
    }

    /**
     * Calculate the authentication data used for making API calls.
     *
     * Based on the Drupal module authentication code.
     *
     * @return array Authentication parameters
     */
    private function _getAuthenticationData()
    {
        $public = $this->getPublicKey();
        $private = $this->getPrivateKey();

        // Current timestamp (dateTime format (http://www.w3.org/TR/xmlschema-2/#dateTime))
        $time = gmdate('Y-m-d\TH:i:s.\\0\\0\\0O', time());

        // One time random value
        $nonce = md5(mt_rand());

        // Generate hash (HMAC-SHA1, RFC2104 (http://www.ietf.org/rfc/rfc2104.txt))
        $hash = base64_encode(
            pack('H*', sha1((str_pad($private, 64, chr(0x00)) ^ (str_repeat(chr(0x5c), 64))) .
            pack('H*', sha1((str_pad($private, 64, chr(0x00)) ^ (str_repeat(chr(0x36), 64))) .
            $time . ':' .  $nonce . ':' . $private))))
        );

        $params = array('public_key' => $public, 
                        'time' => $time,
                        'hash' => $hash,
                        'nonce' => $nonce);
        return $params;
    }


    /**
     * Make a call to the Mollom servers.
     *
     * Handles the fallback scenario described in the Mollom documentation.
     * Authentication data is added to the parameters automatically.
     *
     * @param string $method The name of the requested method
     * @param array $params An array of parameters to be passed
     * @param array $servers Override the set of servers to be used for making the API call
     * @return mixed The returned data from Mollom.
     * @throws Zend_Service_Exception Incorrect call made
     * @throws Zend_Service_Mollom_UnavailableException Mollom service not available
     */
    private function _doCall($method, array $params = array(), array $servers = array())
    {
        if (count($servers) == 0) {
            $servers = $this->getServers();
        }

        // Add authentication data and prepare request
        $auth = $this->_getAuthenticationData();
        $reqparams = array_merge($params, $auth);
        $request = new Zend_XmlRpc_Request($method, array($reqparams));

        // Try each server
        foreach ($servers as $server) {
            $response = null;

            $client = new Zend_XmlRpc_Client($server . '/' . self::API_VERSION, $this->getHttpClient());
            $client->doRequest($request, &$response);

            if (!$response->isFault()) {
                // Successfull call, return results
                return $response->getReturnValue();
            }

            $fault = $response->getFault();
            $code = $fault->getCode();

            switch ($code) {
                // Refresh server list and restart the call.
                case self::FAULT_REFRESH:
                    $this->_updateServerList();
                    return $this->_doCall($method, $params);

                // Do nothing, next iteration will use next server
                case self::FAULT_BUSY:
                    break;

                // Bad request
                default:
                    throw new Zend_Service_Exception('Mollom request failed: (' 
                                . $fault->getCode() . ') ' . $fault->getMessage());
            }
        }
        
        // When all servers fail, notify the user.
        require_once 'Zend/Service/Mollom/UnavailableException.php';
        throw new Zend_Service_Mollom_UnavailableException();
    }

    /**
     * Updates the server list by contacting one of the default servers.
     */
    private function _updateServerList()
    {
        $this->_servers = $this->_doCall('mollom.getServerList', array(), $this->_defaultServers);
        if (isset(self::$_cache)) {
            $id = 'Zend_Service_Mollom_'.$this->getPublicKey();
            self::$_cache->save(serialize($this->_servers), $id);
        }
    }
}
