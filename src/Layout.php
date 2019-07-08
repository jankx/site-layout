<?php
namespace Jankx\SiteLayouts;

use Jankx;
use Jankx\SiteLayouts\Admin\SiteLayout as SiteLayoutAdmin;
use Jankx\SiteLayouts\Admin\Metabox\PostLayout;

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
        if (Jankx::isRequest('admin')) {
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
        $jankx->getSupportLayouts = function() use($postLayout) {
            return $postLayout->getSupportLayouts();
        };
        $jankx->getLayout = function() use($postLayout) {
            return $postLayout->getLayout();
        };
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
        if (!is_null($this->currentLayout)) {
            $this->currentLayout;
        }

        $this->currentLayout = $this->detectLayout();

        if (empty($this->currentLayout)) {
            return $this->defaultLayout();
        }

        return $this->currentLayout;
    }

    public function detectLayout()
    {
        if (Jankx::isRequest('frontend')) {
            if (is_singular()) {
                return get_post_meta(get_the_ID(), PostLayout::POST_LAYOUT_META_KEY, true);
            }
        } elseif (Jankx::isRequest('admin')) {
            $currentScreen = get_current_screen();
            if ($currentScreen->base === 'post') {
                $post_id = isset($_GET['post']) ? (int)$_GET['post'] : 0;
                return get_post_meta($post_id, PostLayout::POST_LAYOUT_META_KEY, true);
            }
        }
    }

    public function defaultLayout()
    {
        return apply_filters('jankx_default_site_layout', 'lcs');
    }
}
