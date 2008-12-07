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
 * @category   Zend
 * @package    Zend_Service_Mollom
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Test helper
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Gdata_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

/**
 * Mollom tests
 */
require_once 'Zend/Service/Mollom/GetServersTest.php';
require_once 'Zend/Service/Mollom/CheckContentTest.php';
require_once 'Zend/Service/Mollom/CheckCaptchaTest.php';
require_once 'Zend/Service/Mollom/SendFeedbackTest.php';
require_once 'Zend/Service/Mollom/VerifyKeyTest.php';

class Zend_Service_Mollom_AllTests
{

    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Service_Mollom');

		$suite->addTestSuite('Zend_Service_Mollom_GetServersTest');
		$suite->addTestSuite('Zend_Service_Mollom_CheckContentTest');
		$suite->addTestSuite('Zend_Service_Mollom_CheckCaptchaTest');
		$suite->addTestSuite('Zend_Service_Mollom_SendFeedbackTest');
		$suite->addTestSuite('Zend_Service_Mollom_VerifyKeyTest');

        return $suite;
    }

}

if (PHPUnit_MAIN_METHOD == 'Zend_Service_Mollom_AllTests::main') {
    Zend_Service_Mollom_AllTests::main();
}
