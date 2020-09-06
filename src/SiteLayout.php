<?php
namespace Jankx\SiteLayout;

use Jankx\SiteLayout\Admin\SiteLayout as SiteLayoutAdmin;
use Jankx\SiteLayout\Admin\Metabox\PostLayout;

class SiteLayout
{
    const LAYOUT_FULL_WIDTH = 'lfw';
    const LAYOUT_CONTENT_SIDEBAR = 'lcs';
    const LAYOUT_SIDEBAR_CONTENT = 'lsc';
    const LAYOUT_CONTENT_SIDEBAR_SIDEBAR = 'lcss';
    const LAYOUT_SIDEBAR_CONTENT_SIDEBAR = 'lscs';
    const LAYOUT_SIDEBAR_SIDEBAR_CONTENT = 'lssc';

    protected $currentLayout;
    protected static $instance;

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        if (!defined('JANKX_SITE_LAYOUT_LOAD_FILE')) {
            define('JANKX_SITE_LAYOUT_LOAD_FILE', __FILE__);
        }

        if (!defined('JANKX_SITE_LAYOUT_DIR')) {
            define('JANKX_SITE_LAYOUT_DIR', realpath(dirname(JANKX_SITE_LAYOUT_LOAD_FILE) . '/..'));
        }

        $this->includes();
        $this->initHooks();
    }

    protected function includes()
    {
        if (is_admin()) {
            new SiteLayoutAdmin();
        }
    }

    protected function initHooks()
    {
        add_action('jankx_setup_environment', array($this, 'setupJankx'));
        add_filter('body_class', array($this, 'bodyClasses'));
        add_action('jankx_page_setup', array($this, 'pageSetup'));
    }

    public function setupJankx($jankx)
    {
        $postLayout = self::instance();
        $jankx->getSupportLayouts = function () use ($postLayout) {
            return $postLayout->getSupportLayouts();
        };
        $jankx->getLayout = function () use ($postLayout) {
            return $postLayout->getLayout();
        };
    }

    public function pageSetup($engine)
    {
        /**
         * Load template for site layout
         */
        $templateLoader = new TemplateLoader($this->getLayout(), $engine);
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
            self::LAYOUT_FULL_WIDTH              => __('Full Width', 'jankx'),
            self::LAYOUT_CONTENT_SIDEBAR         => __('Content Sidebar', 'jankx'),
            self::LAYOUT_SIDEBAR_CONTENT         => __('Sidebar Content', 'jankx'),
            self::LAYOUT_CONTENT_SIDEBAR_SIDEBAR => __('Content Sidebar Sidebar', 'jankx'),
            self::LAYOUT_SIDEBAR_CONTENT_SIDEBAR => __('Sidebar Content Sidebar', 'jankx'),
            self::LAYOUT_SIDEBAR_SIDEBAR_CONTENT => __('Sidebar Sidebar Content', 'jankx'),
        ));

        return $layouts;
    }

    public function getLayout()
    {
        if (!is_null($this->currentLayout)) {
            return $this->currentLayout;
        }

        $this->currentLayout = $this->detectLayout();

        if (empty($this->currentLayout)) {
            $this->currentLayout = $this->defaultLayout();
        }

        return apply_filters('jankx_get_layout', $this->currentLayout);
    }

    public function detectLayout()
    {
        if (is_admin()) {
            $currentScreen = get_current_screen();
            if ($currentScreen->base === 'post') {
                $post_id = isset($_GET['post']) ? (int)$_GET['post'] : 0;
                return get_post_meta($post_id, PostLayout::POST_LAYOUT_META_KEY, true);
            }
        } else {
            if (is_singular()) {
                return get_post_meta(get_the_ID(), PostLayout::POST_LAYOUT_META_KEY, true);
            }
        }
    }

    public function defaultLayout()
    {
        return apply_filters('jankx_default_site_layout', 'lcs');
    }
}
