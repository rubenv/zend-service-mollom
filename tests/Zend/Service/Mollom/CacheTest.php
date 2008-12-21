<?php

/**
 * Zend Framework
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
 * @category     Zend
 * @package      Zend_Service_Mollom
 * @subpackage   UnitTests
 * @copyright  	 Copyright (c) 2008 Ruben Vermeersch (http://www.savanne.be)
 * @license    	 http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Test helper
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once 'Zend/Service/Mollom.php';
require_once 'Zend/Cache.php';

class Zend_Service_Mollom_CacheTest extends PHPUnit_Framework_TestCase
{
    public function setUp() {
        $this->mollom = new Zend_Service_Mollom(TESTS_ZEND_SERVICE_MOLLOM_PUBLICKEY,
                                                TESTS_ZEND_SERVICE_MOLLOM_PRIVATEKEY);

		$backendOptions = array('cache_dir' => sys_get_temp_dir(),
								'file_name_prefix' => 'Zend_Service_Mollom_CacheTest');
		$frontendOptions = array();
		$cache = Zend_Cache::factory('Core',
									 'File',
									 $frontendOptions,
									 $backendOptions);
		Zend_Service_Mollom::setCache($cache);
    }

	public function tearDown() {
		Zend_Service_Mollom::clearCache();
		Zend_Service_Mollom::removeCache();
	}

	public function testHasCache() {
		$this->assertTrue(Zend_Service_Mollom::hasCache());
	}

	public function testHasCachedRecord() {
		try {
			$id = 'Zend_Service_Mollom_'.TESTS_ZEND_SERVICE_MOLLOM_PUBLICKEY;
			$servers = $this->mollom->getServers();

			$cache = Zend_Service_Mollom::getCache();

			$this->assertTrue($cache->test($id) !== false);
			$this->assertEquals(serialize($servers), $cache->load($id));
		} catch (Zend_Service_Mollom_UnavailableException $e) { /* Might happen */ }
	}
}
