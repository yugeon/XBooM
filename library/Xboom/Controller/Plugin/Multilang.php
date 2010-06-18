<?php

/**
 * Front Controller Plugin.
 * Hooks routeStartup, dispatchLoopStartup.
 * Multilanguage support, which adds language detection by URL prefix.
 *
 * @category   Multilang
 * @package    Multilang_Controller
 * @subpackage Plugins
 *
 * Description of Multilang
 *
 * @author     yugeon
 * @version    SVN: $Id$
 */

class Xboom_Controller_Plugin_Multilang extends Zend_Controller_Plugin_Abstract
{
    /**
     * Default language.
     *
     * @var string
     */
    protected $_defaultLang = 'en';

    /**
     * Map of supported locales.
     *
     * @var array
     */
    protected $_locales = array('en' => 'en_GB');

    /**
     * URL delimetr symbol.
     * @var string
     */
    protected $_urlDelimiter = '/';

    /**
     * HTTP status code for redirects
     * @var int
     */
    protected $_redirectCode = 302;

    /**
     * Language presented in URL.
     * @var string
     */
    protected $_urlLang = '';

    /**
     * Contructor
     * Verify options
     *
     * @param array $options
     */
    public function __construct($defaultLang = '', Array $localesMap = array())
    {
        $this->_locales = array_merge($this->_locales, $localesMap);
        if (array_key_exists($defaultLang, $this->_locales))
        {
            $this->_defaultLang = $defaultLang;
        }
    }

    /**
     * routeStartup() plugin hook
     * Parse URL and extract language if present in URL. Prepare base url for routing.
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        // Work only with http request
        if (! ($request instanceof Zend_Controller_Request_Http))
            return;

        $front = Zend_Controller_Front::getInstance();
        $baseUrl = $front->getBaseUrl();

        // set baseUrl for view helper BaseUrl
        $view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
        if (null !== $view)
        {
            $uri = Zend_Uri::factory($request->getScheme());
            $uri->setHost($request->getHttpHost());
            $uri->setPath($baseUrl);
            $view->getHelper('BaseUrl')->setBaseUrl($uri);
            unset($uri);
        }
        
        // if language present in URL after baseUrl. (http://host/base_url/en/..., /ru, /rus...)
        $lang = '';
        if (preg_match("#^/([a-zA-Z]{2,3})($|/)#", $request->getPathInfo(), $matches))
        {
            $lang = $matches[1];
        }

        // Check if lang in list of available language
        if (array_key_exists($lang, $this->_locales))
        {
            // save original base URL
            Zend_Registry::set('orig_baseUrl', $baseUrl);
            // change base URL
            $front->setBaseUrl($baseUrl . $this->_urlDelimiter . $lang);
            // init path info with new baseUrl.
            $request->setPathInfo();
            // save present language
            $this->_urlLang = $lang;
        }
    }

    /**
     * dispatchLoopStartup() plugin hook
     * Last chance to define language.
     * If language not present in URL and is a GET request then paste language in
     * URL and redirect immediately.
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        // Work only with http request
        if (! ($request instanceof Zend_Controller_Request_Http))
            return;

        $lang = '';
        if (empty ($this->_urlLang))
        {
            // language not present in URL
            // check if language set in user options.
            $lang = '';
            if (Zend_Registry::isRegistered('user_lang'))
            {
                $lang = Zend_Registry::get('user_lang');
            }
            else
            {
                // language not present in user options
                // take from browser.
                try
                {
                    $locale = new Zend_Locale(Zend_Locale::BROWSER);
                    $lang = $locale->getLanguage();
                    unset($locale);
                }
                catch (Exception $e)
                {
                    $lang = $this->_defaultLang;
                }
            }
            if (!array_key_exists($lang, $this->_locales))
            {
                $lang =  $this->_defaultLang;
            }

            if ($request->isGet())
            {
                $this->_doRedirectAndExit($request, $lang);
            }
        }
        else
        {
            // language present in URL
            $lang = $this->_urlLang;
        }

        // Set up Locale object.
        if (Zend_Registry::isRegistered('user_locale'))
        {
            // from user options
            $localeString = Zend_Registry::get('user_locale');
        }
        else
        {
            $localeString = $this->_locales[$lang];
        }
        $locale = new Zend_Locale($localeString);

        // TODO: location dir for language
        // Set up Translate Object.
        $translationStrings = array();
        $file = APPLICATION_PATH . '/modules/' . $request->getModuleName()
                . '/lang/' . $lang . '.php';
        if (file_exists($file))
        {
            $translationStrings = include $file;
        }
        if(empty($translationStrings) || !is_array($translationStrings))
        {
            $translationStrings = array('test' => '1');
        }
        $translate = new Zend_Translate('array', $translationStrings, $lang);

        // Save language settings.
        Zend_Registry::set('lang', $lang);
        Zend_Registry::set('Zend_Locale', $locale);
        Zend_Registry::set('Zend_Translate', $translate);
    }

    protected function _doRedirectAndExit(Zend_Controller_Request_Abstract $request, $lang)
    {
        // set evaluating language in URL, and redirect request
        $uri = Zend_Uri::factory($request->getScheme());
        $uri->setHost($request->getHttpHost());
        $uri->setPath($request->getBaseUrl() . $this->_urlDelimiter
                . $lang . $request->getPathInfo());
        $query = '';
        $requestUri = $request->getRequestUri();
        if (false !== ($pos = strpos($requestUri, '?')))
        {
            $query = substr($requestUri, $pos + 1);
            $uri->setQuery($query);
        }
        $response = Zend_Controller_Front::getInstance()->getResponse();
        $response->setRedirect($uri, $this->_redirectCode);
        $response->sendHeaders();
        exit();
    }
    /**
     * preDispatch
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        // TODO: add translation for current module
//        $translate = Zend_Registry::get('Zend_Translate');
//        $translate->addTranslation(APPLICATION_PATH . '/modules/'.$request->getModuleName().'/lang/',
//                                   Zend_Registry::get('Zend_Locale'));
    }

}