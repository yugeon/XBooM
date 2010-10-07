<?php

/**
 * Description of Multilang
 *
 * @author yugeon
 * @group functional
 */
class Xboom_Controller_Plugin_MultilangTest extends ControllerTestCase
{

    protected $_options;

    public function setUp()
    {
        parent::setUp();
        $this->_options = $this->application->getOption('resources');
    }

    public function testRuLanguageInUrl()
    {
        $_SERVER['HTTP_HOST'] = 'xboom.local';
        $this->dispatch('/ru');
        $this->assertTrue(Zend_Registry::get('lang') === 'ru');
        $this->assertResponseCode(200);
    }

    public function _testNoLanguageInUrlGet()
    {
        $this->markTestSkipped(
            "Testing would break the test suite"
        );
        $_SERVER['HTTP_HOST'] = 'xboom.local';
        // this test break dispatch loop if GET-request
        $this->getRequest()->setMethod('GET');
        $this->dispatch('');
        $this->assertRedirectTo('http://xboom.local/'
                . $this->_options['multilang']['default']);
    }

    public function _testNoLanguageInUrlGetQuery()
    {
                $this->markTestSkipped(
            "Testing would break the test suite"
        );
        $_SERVER['HTTP_HOST'] = 'xboom.local';
        // this test break dispatch loop if GET-request
        $this->getRequest()->setMethod('GET');
        $this->dispatch('/?a=1');
        $this->assertRedirectTo('http://xboom.local/'
                . $this->_options['multilang']['default'] . '/?a=1');
    }

    public function testNoLanguageInUrl()
    {
        $_SERVER['HTTP_HOST'] = 'xboom.local';
        // this test break dispatch loop if GET-request
        $this->getRequest()->setMethod('POST');
        $this->dispatch('');
        $this->assertTrue(Zend_Registry::get('lang')
                === $this->_options['multilang']['default']);
        $this->assertResponseCode(200);
    }

    public function testNoLanguageInUrlPresentInBrowser()
    {
        $_SERVER['HTTP_HOST'] = 'xboom.local';
        putenv('HTTP_ACCEPT_LANGUAGE=ru,en-us;q=0.7,en;q=0.3');
        // this test break dispatch loop if GET-request
        $this->getRequest()->setHeader('X_REQUESTED_WITH', 'XMLHttpRequest');
        $this->dispatch('');
        $this->assertTrue(Zend_Registry::get('lang')
                === 'ru');
        $this->assertResponseCode(200);
    }

    public function testNoLanguageInUrlPresentInUserOptions()
    {
        $_SERVER['HTTP_HOST'] = 'xboom.local';
        Zend_Registry::set('user_lang', 'ua');
        // this test break dispatch loop if GET-request
        $this->getRequest()->setMethod('POST');
        $this->dispatch('');
        $this->assertTrue(Zend_Registry::get('lang') === 'ua');
        $this->assertResponseCode(200);
        Zend_Registry::set('user_lang', '');
    }

    public function testNoLanguageInUrlBadPresentInUserOptions()
    {
        $_SERVER['HTTP_HOST'] = 'xboom.local';
        Zend_Registry::set('user_lang', 'gg');
        // this test break dispatch loop if GET-request
        $this->getRequest()->setMethod('POST');
        $this->dispatch('');
        $this->assertTrue(Zend_Registry::get('lang')
                === $this->_options['multilang']['default']);
        $this->assertResponseCode(200);
        Zend_Registry::set('user_lang', '');
    }

    public function testNoLanguageInUrlAndAjaxRequest()
    {
        $_SERVER['HTTP_HOST'] = 'xboom.local';
        // this test break dispatch loop if GET-request
        $this->getRequest()->setMethod('GET');
        $this->getRequest()->setHeader('X_REQUESTED_WITH', 'XMLHttpRequest');
        $this->dispatch('');
        $this->assertTrue(Zend_Registry::get('lang')
                === $this->_options['multilang']['default']);
        $this->assertResponseCode(200);
    }

}
?>
