<?php
namespace Jankx\SiteLayout;

use Jankx\SiteLayout\SiteLayout;

class TemplateLoader
{
    protected $layout;
    protected $engine;
    protected $fullContent = false;

    public function __construct($layout, $engine)
    {
        $this->layout = $layout;
        $this->engine = $engine;
        $this->fullContent = (
            $layout === SiteLayout::LAYOUT_FULL_WIDTH ||
            ( is_int(strpos($engine->rootDirectory, 'plugins/elementor'))
                && $engine->baseTemplate === 'header-footer'
            )
        );
    }

    /**
     * Load site layout via template engine
     *
     * @param TemplateEngine $engine The template engine use in theme
     */
    public function load()
    {
        $this->buildBaseLayout();
        $this->buildMainContentWrap();
        $this->buildSidebarLayout();
    }

    protected function buildBaseLayout()
    {
        add_action('jankx_template_after_header', array($this, 'openMainContentSidebarWrap'), 20);
        add_action('jankx_template_after_header', array($this, 'beforeMainContentAndSidebar'), 25);
        add_action('jankx_template_after_header', array($this, 'beforeMainContent'), 30);

        add_action('jankx_template_before_footer', array($this, 'afterMainContent'), 1);
        add_action('jankx_template_before_footer', array($this, 'afterMainContentAndSidebar'), 2);
        add_action('jankx_template_before_footer', array($this, 'closeMainContentSidebarWrap'), 3);
    }

    protected function buildAttributes($attributes)
    {
        if (!is_array($attributes)) {
            return '';
        }
        $attributes_str = '';
        foreach ($attributes as $attribute => $value) {
            $attributes_str .= sprintf('%s="%s" ', $attribute, $value);
        }
        return rtrim($attributes_str);
    }

    // Start base layout for Jankx Framework
    public function openMainContentSidebarWrap()
    {
        $attributes = apply_filters('jankx_tag_main_content_sidebar_attributes', array(
            'class' => 'jankx-wrapper main-content-sidebar'
        ));
        printf('<section %s>', $this->buildAttributes($attributes));
    }

    public function beforeMainContentAndSidebar()
    {
        do_action('jankx_template_before_main_content_sidebar');
    }

    public function beforeMainContent()
    {
        do_action('jankx_template_before_main_content');
    }

    public function afterMainContent()
    {
        do_action('jankx_template_after_main_content');
    }

    public function afterMainContentAndSidebar()
    {
        do_action('jankx_template_after_main_content_sidebar');
    }

    public function closeMainContentSidebarWrap()
    {
        echo '</section>';
    }
    // End base layout for Jankx Framework

    protected function buildMainContentWrap()
    {
        add_action('jankx_template_before_main_content', array($this, 'openMainContent'), 5);
        add_action('jankx_template_after_main_content', array($this, 'closeMainContent'), 25);
    }

    public function openMainContent()
    {
        $attributes = apply_filters('jankx_tag_main_content_sidebar_attributes', array(
            'id' => 'jankx-main-content',
            'class' => 'main-content'
        ));
        printf('<main %s>', $this->buildAttributes($attributes));
    }

    public function closeMainContent()
    {
        echo '</main>';
    }

    protected function buildSidebarLayout()
    {
        if ($this->fullContent) {
            return;
        }

        add_action('jankx_template_after_main_content', 'get_sidebar', 35);

        if (in_array($this->layout, array(
            SiteLayout::LAYOUT_CONTENT_SIDEBAR_SIDEBAR,
            SiteLayout::LAYOUT_SIDEBAR_CONTENT_SIDEBAR,
            SiteLayout::LAYOUT_SIDEBAR_SIDEBAR_CONTENT
        ))) {
            add_action('jankx_template_after_main_content', array($this, 'loadSecondarySidebar'), 45);
        }
    }

    public function loadSecondarySidebar()
    {
        get_sidebar('alt');
    }
}
