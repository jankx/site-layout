<?php
namespace Jankx\SiteLayout;

use Jankx;
use Jankx\SiteLayout\Exceptions\SiteLayoutException;

class TemplateLoader
{
    protected $layout;
    protected $engine;
    protected $numOfSidebars;
    protected $fullContent = false;

    public function __construct($layout, $engine)
    {
        $this->layout = $layout;
        $this->engine = $engine;
        $this->numOfSidebars = 0;
        $this->fullContent = (
            $layout === 'lfw' &&
            is_int(strpos($engine->rootDirectory, 'plugins/elementor')) &&
            $engine->baseTemplate === 'header-footer'
        );
    }

    /**
     * Load site layout via template engine
     *
     * @param TemplateEngine $engine The template engine use in theme
     */
    public function load()
    {
    }
}
