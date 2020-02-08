<?php

/*
 * This file is part of the Pagerfanta package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pagerfanta\View\Template;

use Pagerfanta\View\Template\TwitterBootstrapTemplate;

/**
 * @author Pablo Díez <pablodip@gmail.com>
 */
class DolopediaTemplate extends TwitterBootstrapTemplate
{
    static protected $defaultOptions = array(
        'prev_message'        => 'Anterior',
        'prev_disabled_href'  => '',
        'next_message'        => 'Siguiente',
        'next_disabled_href'  => '',
        'dots_message'        => '&hellip;',
        'dots_href'           => '#',
        'css_container_class' => 'centered',
        'css_prev_class'      => 'paginator-textual',
        'css_next_class'      => 'paginator-textual',
        'css_disabled_class'  => 'disabled',
        'css_dots_class'      => 'paginator-item',
        'css_active_class'    => 'current'
    );

    private $pageClass = 'paginator-item';

    public function container()
    {
        return sprintf('<div class="%s"><ul class="paginator">%%previous%% %%pages%% %%next%%</ul></div>',
            $this->option('css_container_class')
        );
    }

    public function page($page)
    {
        $text = $page;

        return $this->pageWithText($page, $text);
    }

    public function pageWithText($page, $text)
    {
        $class = $this->pageClass;

        return $this->pageLinkWithTextAndClass($page, $text, $class);
    }

    private function pageWithTextAndClass($page, $text, $class)
    {
        return $this->li($page, null, $class);
    }
    
    private function pageLinkWithTextAndClass($page, $text, $class)
    {
        $href = $this->generateRoute($page);

        return $this->li($text, $href, $class);
    }

    public function previousDisabled()
    {
        $class = $this->previousDisabledClass();
        $href = $this->option('prev_disabled_href');
        $text = $this->option('prev_message');

        return $this->pageWithTextAndClass($text, $href, $class);
    }

    private function previousDisabledClass()
    {
        return $this->option('css_prev_class').' '.$this->option('css_disabled_class');
    }

    public function previousEnabled($page)
    {
        $text = $this->option('prev_message');
        $class = $this->option('css_prev_class');

        return $this->pageLinkWithTextAndClass($page, $text, $class);
    }

    public function nextDisabled()
    {
        $class = $this->nextDisabledClass();
        $href = $this->option('next_disabled_href');
        $text = $this->option('next_message');

        return $this->pageWithTextAndClass($text, $href, $class);
    }

    private function nextDisabledClass()
    {
        return $this->option('css_next_class').' '.$this->option('css_disabled_class');
    }

    public function nextEnabled($page)
    {
        $text = $this->option('next_message');
        $class = $this->option('css_next_class');

        return $this->pageLinkWithTextAndClass($page, $text, $class);
    }

    public function first()
    {
        return $this->page(1);
    }

    public function last($page)
    {
        return $this->page($page);
    }

    public function current($page)
    {
        $text = $page;
        $class = $this->pageClass . ' ' . $this->option('css_active_class');

        return $this->pageWithTextAndClass($page, $text, $class);
    }

    public function separator()
    {
        $class = $this->option('css_dots_class');
        $href = $this->option('dots_href');
        $text = $this->option('dots_message');

        return $this->li($text, $href, $class);
    }

    private function li($text, $href, $class)
    {
        $liClass = $class ? sprintf(' class="%s"', $class) : '';

        $link = $this->link($class, $href, $text);

        return sprintf('<li %s>%s</li>', $liClass, $link);
    }
    
    private function link($class, $href, $text)
    {
        $linkClass = ' class="paginator-link"';

        if ($href) {
            return sprintf('<a%s href="%s">%s</a>', $linkClass, $href, $text);
        } else {
            return sprintf('<span%s>%s</span>',$linkClass, $text);
        }
    }
}