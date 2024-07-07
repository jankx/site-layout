<?php

namespace Jankx\SiteLayout;

class GutenbergLayoutLoader extends LayoutLoader
{
    protected $layout;
    protected $engine;
    protected $fullContent = false;


    /**
     * Load site layout via template engine
     *
     * @param \Jankx\TemplateEngine\Engine $engine The template engine use in theme
     */
    public function load()
    {
        do_action('jankx/template/site/layout', $this);
    }
}
