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

class Zend_Service_Mollom_SendFeedbackTest extends PHPUnit_Framework_TestCase
{
    public function setUp() {
        $this->mollom = new Zend_Service_Mollom(TESTS_ZEND_SERVICE_MOLLOM_PUBLICKEY,
                                                TESTS_ZEND_SERVICE_MOLLOM_PRIVATEKEY);
    }

	public function testSendFeedback() {
		try {
			$this->mollom->sendFeedback('test', Zend_Service_Mollom::FEEDBACK_SPAM);
		} catch (Zend_Service_Mollom_UnavailableException $e) { /* Might happen */ }
	}
}
