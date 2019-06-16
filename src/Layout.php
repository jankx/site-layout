<?php
namespace Jankx\SiteLayouts;

class Layout
{
    protected $currentLayout;

    protected static $instance;

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->initHooks();
    }

    protected function initHooks()
    {
        add_filter('body_class', array($this, 'bodyClasses'));
        add_action('jankx_page_setup', array($this, 'pageSetup'));
    }

    public function pageSetup()
    {
        /**
         * Load template for site layout
         */
        $templateLoader = new TemplateLoader($this->getLayout());
        $templateLoader->load();
    }

    public function bodyClasses($classes)
    {
        $classes[] = $this->getLayout();
        return $classes;
    }

    public function getSupportLayouts()
    {
        $layouts = apply_filters('jankx_support_layouts', array(
            'lfw'  => __('Full Width', 'jankx'),
            'lcs'  => __('Content Sidebar', 'jankx'),
            'lsc'  => __('Sidebar Content', 'jankx'),
            'lcss' => __('Content Sidebar Sidebar', 'jankx'),
            'lscs' => __('Sidebar Content Sidebar', 'jankx'),
            'lssc' => __('Sidebar Sidebar Content', 'jankx'),
        ));

        return $layouts;
    }

    public function getLayout()
    {
        if (is_null($this->currentLayout)) {
            $this->currentLayout = $this->detectLayout();
        }
        return $this->currentLayout;
    }

    public function detectLayout()
    {
        // Currently support only content sidebar layout
        return 'lcs';
    }
}
