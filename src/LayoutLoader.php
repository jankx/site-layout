<?php
namespace Jankx\SiteLayout;

use Jankx\SiteLayout\SiteLayout;

class LayoutLoader
{
    protected $layout;
    protected $engine;
    protected $fullContent = false;

    public function __construct($layout, $engine)
    {
        $this->layout = $layout;
        $this->engine = $engine;
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

        do_action('jankx_template_build_site_layout', $this);
    }

    protected function buildBaseLayout()
    {
        add_action('jankx_template_after_header', array($this, 'openMainContentSidebarWrap'), 15);
        add_action('jankx_template_before_footer', array($this, 'closeMainContentSidebarWrap'), 5);

        if (!apply_filters('jankx/template/content_sidebar/container/disabled', false)) {
            add_action('jankx_template_after_header', array($this, 'openJankxSidebarContentContainer'), 20);
            add_action('jankx_template_before_footer', array($this, 'closeJankxSidebarContentContainer'), 4);
        }

        add_action('jankx_template_after_header', array($this, 'openMainContentSidebarWrapInner'), 30);
        add_action('jankx_template_before_footer', array($this, 'closeMainContentSidebarWrapInner'), 3);

        add_action('jankx_template_after_header', array($this, 'beforeMainContentAndSidebar'), 25);
        add_action('jankx_template_after_header', array($this, 'beforeMainContent'), 30);

        add_action('jankx_template_before_footer', array($this, 'afterMainContent'), 1);
        add_action('jankx_template_before_footer', array($this, 'afterMainContentAndSidebar'), 2);
    }

    // Start base layout for Jankx Framework
    public function openMainContentSidebarWrap()
    {
        $attributes = apply_filters('jankx_tag_main_content_sidebar_attributes', array(
            'class' => 'jankx-wrapper main-content-sidebar'
        ));
        printf('<div %s>', jankx_generate_html_attributes($attributes));

        do_action('jankx_template_main_content_sidebar_start');
    }

    public function openJankxSidebarContentContainer()
    {
        jankx_open_container(array('main-content-sidebar-wrapper'));
    }

    public function closeJankxSidebarContentContainer()
    {
        jankx_close_container();
        echo '<!-- Close .main-content-sidebar-wrapper -->';
    }

    public function openMainContentSidebarWrapInner()
    {
        jankx_template('layout/content-sidebar-open');
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

    public function closeMainContentSidebarWrapInner()
    {
        jankx_template('layout/content-sidebar-close');

        do_action('jankx_template_main_content_sidebar_end');
    }

    public function closeMainContentSidebarWrap()
    {
        echo '</div><!-- Close .main-content-sidebar-wrap -->';
    }
    // End base layout for Jankx Framework

    protected function buildMainContentWrap()
    {
        add_action('jankx_template_before_main_content', array($this, 'openMainContent'), 9);
        add_action('jankx_template_after_main_content', array($this, 'closeMainContent'), 25);
    }

    public function openMainContent()
    {
        $attributes = apply_filters('jankx_tag_main_content_sidebar_attributes', array(
            'id' => 'jankx-main-content',
            'class' => 'main-content'
        ));
        printf('<main %s>', jankx_generate_html_attributes($attributes));
    }

    public function closeMainContent()
    {
        echo '</main>';
    }

    protected function buildSidebarLayout()
    {
        if ($this->layout === SiteLayout::LAYOUT_FULL_WIDTH) {
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
