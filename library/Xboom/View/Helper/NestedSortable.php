<?php

/**
 *  CMF for web applications based on Zend Framework 1 and Doctrine 2
 *  Copyright (C) 2010  Eugene Gruzdev aka yugeon
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright  Copyright (c) 2010 yugeon <yugeon.ru@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.html  GNU GPLv3
 */

/**
 * Description of NestedSortable
 *
 * @author yugeon
 */
class Xboom_View_Helper_NestedSortable extends Zend_View_Helper_Abstract
{

    protected $items;
    protected $ulClass = 'sortable';
    protected $liClass;
    protected $_indent = '';
    protected $_path = '';

    public function getLiClass()
    {
        return $this->liClass;
    }

    public function getUlClass()
    {
        return $this->ulClass;
    }

    public function getItems()
    {
        return $this->items;
    }

    protected function _jsNestedSortedPlugin()
    {
        $this->view->headScript()->appendFile(
                $this->_path, 'text/javascript');
        return $this;
    }

    public function getScriptPath()
    {
        return $this->_path;
    }

    public function setScriptPath($path)
    {
        $this->_path = $path;
        return $this;
    }

    protected function _jQueryAddOnLoad($ulClass)
    {
        $code = <<<jQueryCode

    $("ul.$ulClass").nestedSortable({
        disableNesting: "no-nest",
        forcePlaceholderSize: true,
        handle: "div",
        items: "li",
        listType: "ul",
        opacity: .6,
        placeholder: "ui-state-highlight",
        errorClass: "ui-state-error",
        tabSize: 25,
        tolerance: "pointer",
        toleranceElement: "> div"
    });
    $("ul.$ulClass").disableSelection();

jQueryCode;

        $this->view->JQuery()->addOnLoad($code);

        return $this;
    }

    public function getIndent()
    {
        return $this->_indent;
    }

    public function setIndent($indent)
    {
        if (is_int($indent))
        {
            $indent = str_repeat(' ', $indent);
        }
        $this->_indent = (string)$indent;
        return $this;
    }

    protected function _enableJQuery()
    {
        if (false === $this->view->getPluginLoader('helper')->getPaths('ZendX_JQuery_View_Helper'))
        {
            $this->view->addHelperPath('ZendX/JQuery/View/Helper', 'ZendX_JQuery_View_Helper');
        }

        $this->view->JQuery()->enable()
                ->uiEnable();
        return $this;
    }

    /**
     * Returns an HTML string containing an 'a' element for the given page if
     * the page's href is not empty, and a 'span' element if it is empty
     *
     * Overrides {@link Zend_View_Helper_Navigation_Abstract::htmlify()}.
     *
     * @param  Zend_Navigation_Page $page  page to generate HTML for
     * @return string                      HTML string for the given page
     */
    public function htmlify(Zend_Navigation_Page $page)
    {
        $label = $page->getLabel();
        $id = $page->getId();

        if (!empty($this->liClass) && !empty($id))
        {
            $outId = ' id="' . $this->liClass . '_' . $id . '"';
        }
        else if (!empty($id))
        {
            $outId = ' id="' . $id . '"';
        }
        else
        {
            $outId = '';
        }

        return "<li$outId><div class=\"ui-state-default\">{$this->view->escape($label)}</div>";
    }

    public function nestedSortable($items, $ulClass = 'sortable', $liClass = null)
    {
        $this->items = $items;
        $this->ulClass = $ulClass;
        $this->liClass = $liClass;

        return $this;
    }

    public function render($items = null)
    {
        $xhtml = '';

        if (null !== $items)
        {
            $this->items = $items;
        }

        if (empty($this->items))
        {
            return $xhtml;
        }

        $this->_enableJQuery();
        $this->_jQueryAddOnLoad($this->ulClass);
        $this->_jsNestedSortedPlugin();

        // iterate container
        $iterator = new \RecursiveIteratorIterator($this->items,
                        \RecursiveIteratorIterator::SELF_FIRST);
        $prevDepth = -1;
        foreach ($iterator as $page)
        {
            $depth = $iterator->getDepth();
            $myIndent = $this->_indent . str_repeat('        ', $depth);
            if ($depth > $prevDepth)
            {
                // start new ul tag
                if ($this->ulClass && $depth == 0)
                {
                    $this->ulClass = ' class="' . $this->ulClass . '"';
                }
                else
                {
                    $this->ulClass = '';
                }
                $xhtml .= $myIndent . '<ul' . $this->ulClass . '>' . PHP_EOL;
            }
            else if ($prevDepth > $depth)
            {
                // close li/ul tags until we're at current depth
                for ($i = $prevDepth; $i > $depth; $i--)
                {
                    $ind = $this->_indent . str_repeat('        ', $i);
                    //$xhtml .= $ind . '    </li>' . self::EOL;
                    $xhtml .= $ind . '</ul>' . PHP_EOL;
                }
                // close previous li tag
                //$xhtml .= $myIndent . '    </li>' . self::EOL;
            }
            else
            {
                // close previous li tag
                //$xhtml .= $myIndent . '    </li>' . self::EOL;
            }

            $xhtml .= $myIndent . '    ' . $this->htmlify($page) . PHP_EOL;

            // store as previous depth for next iteration
            $prevDepth = $depth;
        }

        if ($xhtml)
        {
            // done iterating container; close open ul/li tags
            for ($i = $prevDepth + 1; $i > 0; $i--)
            {
                $myIndent = $this->_indent . str_repeat('        ', $i - 1);
                $xhtml .= //$myIndent . '    </li>' . self::EOL
                        $myIndent . '</ul>' . PHP_EOL;
            }
            $xhtml = rtrim($xhtml, PHP_EOL);
        }

        return $xhtml;
    }

    public function __toString()
    {
        return $this->render();
    }

}
