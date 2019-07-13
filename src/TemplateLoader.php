<?php
namespace Jankx\SiteLayouts;

use Jankx;
use Jankx\SiteLayouts\Exceptions\SiteLayoutException;

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
        if (empty($this->layout) && !is_string($this->layout)) {
            throw new SiteLayoutException(
                sprintf(),
                SiteLayoutException::SITE_LAYOUT_EXCEPTION_INVALID_LAYOUT
            );
        }

        if ($this->layout !== 'lfw') {
            add_action('jankx_after_main_content', array($this, 'getPrimarySidebar'), 15);
            $this->numOfSidebars++;
        }

        if (in_array($this->layout, array('lcss', 'lscs', 'lssc'))) {
            add_action('jankx_after_main_content', array($this, 'getAlternativeSidebar'), 30);
            $this->numOfSidebars++;
        }

        add_action('jankx_before_main_content', array($this, 'openContentSidebarWrap'), 5);
        add_action('jankx_after_main_content', array($this, 'closeContentSidebarWrap'), 45);

        add_action('jankx_before_main_content', array($this, 'openContentWrap'), 10);
        add_action('jankx_after_main_content', array($this, 'closeContentWrap'), 5);
    }

    public function getPrimarySidebar()
    {
        $this->engine->getSidebar();
    }

    public function getAlternativeSidebar()
    {
        $this->engine->getSidebar('alt');
    }

    public function openContentSidebarWrap()
    {
        echo sprintf('<div class="%s">', 'main-content-sidebar');
        if ($this->fullContent) {
            return;
        }
        echo '<div class="container"><div class="row">';
    }

    public function closeContentSidebarWrap()
    {
        echo '</div>';
        if ($this->fullContent) {
            return;
        }
        echo '</div></div>';
    }

    public function openContentWrap()
    {
        $class = '';

        if (!$this->fullContent) {
            $used    = $this->numOfSidebars * 3;
            $columns = Jankx::buildColumnClass(12-$used);
            $class .= ' ' . Jankx::makeColumnClass($columns);
        }
        echo sprintf('<div class="main-content%1$s">', $class);
    }

    public function closeContentWrap()
    {
        echo '</div>';
    }
}
