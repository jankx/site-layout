<?php
namespace Jankx\SiteLayout;

use Jankx\Template\Template;
use Jankx\Template\Engine\EngineManager;
use Jankx\SiteLayout\Admin\Metabox\PostLayout;

use function get_current_screen;

class SiteLayout
{
    const LAYOUT_FULL_WIDTH              = 'jankx-fw';
    const LAYOUT_CONTENT_SIDEBAR         = 'jankx-cs';
    const LAYOUT_SIDEBAR_CONTENT         = 'jankx-sc';
    const LAYOUT_CONTENT_SIDEBAR_SIDEBAR = 'jankx-css';
    const LAYOUT_SIDEBAR_CONTENT_SIDEBAR = 'jankx-lscs';
    const LAYOUT_SIDEBAR_SIDEBAR_CONTENT = 'jankx-ssc';

    protected $currentLayout;
    protected static $instance;

    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    private function __construct()
    {
        if (!defined('JANKX_SITE_LAYOUT_LOAD_FILE')) {
            define('JANKX_SITE_LAYOUT_LOAD_FILE', __FILE__);
        }

        if (!defined('JANKX_SITE_LAYOUT_DIR')) {
            define('JANKX_SITE_LAYOUT_DIR', realpath(dirname(JANKX_SITE_LAYOUT_LOAD_FILE) . '/..'));
        }

        $this->loadFeatures();
        $this->initHooks();
    }

    protected function loadFeatures()
    {
        if (is_admin()) {
            new Admin();
        }
    }

    protected function initHooks()
    {
        add_filter('body_class', array($this, 'bodyClasses'));
        add_action('jankx_call_page_template', array($this, 'buildLayout'));
    }

    public function buildLayout($page)
    {
        /**
         * Load template for site layout
         */
        $templateLoader = new TemplateLoader(
            $this->getLayout(),
            EngineManager::getEngine(
                Template::getDefaultLoader()
            )
        );
        $templateLoader->load();
    }

    public function bodyClasses($classes)
    {
        $classes[] = $this->getLayout();
        return $classes;
    }

    public function getSupportLayouts()
    {
        $layouts = apply_filters('jankx_support_site_layouts', array(
            static::LAYOUT_FULL_WIDTH              => __('Full Width', 'jankx'),
            static::LAYOUT_CONTENT_SIDEBAR         => __('Content Sidebar', 'jankx'),
            static::LAYOUT_SIDEBAR_CONTENT         => __('Sidebar Content', 'jankx'),
            static::LAYOUT_CONTENT_SIDEBAR_SIDEBAR => __('Content Sidebar Sidebar', 'jankx'),
            static::LAYOUT_SIDEBAR_CONTENT_SIDEBAR => __('Sidebar Content Sidebar', 'jankx'),
            static::LAYOUT_SIDEBAR_SIDEBAR_CONTENT => __('Sidebar Sidebar Content', 'jankx'),
        ));

        return $layouts;
    }

    public function getLayout()
    {
        if (!is_null($this->currentLayout)) {
            return $this->currentLayout;
        }

        $this->currentLayout = $this->getCurrentLayout();

        if (empty($this->currentLayout)) {
            $this->currentLayout = $this->getDefaultLayout();
        }

        return apply_filters('jankx_get_layout', $this->currentLayout);
    }

    public function getCurrentLayout()
    {
        if (is_admin()) {
            $currentScreen = get_current_screen();
            if ($currentScreen->base === 'post') {
                $post_id = isset($_GET['post']) ? (int)$_GET['post'] : 0;
                return get_post_meta($post_id, PostLayout::POST_LAYOUT_META_KEY, true);
            }
        }

        if (is_singular()) {
            return get_post_meta(get_the_ID(), PostLayout::POST_LAYOUT_META_KEY, true);
        }
    }

    public function getDefaultLayout()
    {
        return apply_filters('jankx_default_site_layout', static::LAYOUT_CONTENT_SIDEBAR);
    }


    public function registerSidebars()
    {
        $primaryArgs = apply_filters('jankx_site_layout_primary_sidebar_args', array(
            'id' => 'primary',
            'name' => __('Primary Sidebar', 'jankx'),
            'before_widget' => '<section id="%1$s" class="widget jankx-widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h3 class="jankx-title widget-title">',
            'after_title' => '</h3>'
        ));
        register_sidebar($primaryArgs);

        if (apply_filters('jankx_site_layout_enable_alt_sidebar', true)) {
            $secondaryArgs = apply_filters('jankx_site_layout_secondary_sidebar_args', array(
                'id' => 'secondary',
                'name' => __('Secondary Sidebar', 'jankx'),
                'before_widget' => '<section id="%1$s" class="widget jankx-widget %2$s">',
                'after_widget' => '</section>',
                'before_title' => '<h3 class="jankx-title widget-title">',
                'after_title' => '</h3>'
            ));
            register_sidebar($secondaryArgs);
        }
    }
}
