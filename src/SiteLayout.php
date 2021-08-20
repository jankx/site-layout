<?php
namespace Jankx\SiteLayout;

use Jankx\SiteLayout\Admin\Metabox\PostLayout;
use Jankx\Template\Page;
use Jankx\Template\Template;
use Jankx\SiteLayout\Constracts\MobileMenuLayout;
use Jankx\SiteLayout\Menu\JankxItems;
use Jankx\SiteLayout\Menu\Slideout;
use Jankx\SiteLayout\Customizer\Header as HeaderCustomizer;
use Jankx\TemplateLoader;

use function get_current_screen;

class SiteLayout
{
    const LAYOUT_FULL_WIDTH              = 'jankx-fw';
    const LAYOUT_CONTENT_SIDEBAR         = 'jankx-cs';
    const LAYOUT_SIDEBAR_CONTENT         = 'jankx-sc';
    const LAYOUT_CONTENT_SIDEBAR_SIDEBAR = 'jankx-css';
    const LAYOUT_SIDEBAR_CONTENT_SIDEBAR = 'jankx-scs';
    const LAYOUT_SIDEBAR_SIDEBAR_CONTENT = 'jankx-ssc';

    protected static $instance;
    protected static $sidebarName;
    protected static $mobileMenus;

    protected $currentLayout;
    protected $menu;

    public $layoutLoader;

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

    public static function getSidebarName($name = null)
    {
        if (is_null($name)) {
            return static::$sidebarName;
        }
        static::$sidebarName = $name;
    }

    protected function loadFeatures()
    {
        $footerBuilder = new FooterBuilder();
        $footerBuilder->build();

        $this->menu = new JankxItems();

        $headerCustomizer = new HeaderCustomizer();
        $headerCustomizer->customize();

        if (is_admin()) {
            new Admin();
        }
    }

    protected function initHooks()
    {
        add_action('init', array($this, 'registerMenus'));
        add_action('widgets_init', array($this, 'registerSidebars'), 5);
        add_action('jankx_prepare_render_template', array($this, 'buildLayout'));

        add_action('get_sidebar', array(SiteLayout::class, 'getSidebarName'));
        add_action('init', array($this->menu, 'register'));

        add_action('wp_head', array($this, 'metaViewport'), 5);
        add_filter('body_class', array($this, 'bodyClasses'));

        add_action('template_redirect', array($this, 'createMobileMenu'), 5);
    }

    public function registerMenus()
    {
        register_nav_menus(
            apply_filters(
                'jankx_site_layout_register_menus',
                array(
                    'primary' => __('Primary Menu', 'jankx'),
                    'secondary' => __('Second Menu', 'jankx'),
                )
            )
        );
    }

    public function buildLayout()
    {
        /**
         * Load template for site layout
         */
        $this->layoutLoader = new LayoutLoader(
            $this->getLayout(),
            TemplateLoader::getTemplateEngine()
        );
        $this->layoutLoader->load();
    }

    public function bodyClasses($classes)
    {
        if (!apply_filters('jankx_template_disable_base_css', false)) {
            $classes[] = 'jankx-base';
        }
        if (jankx_is_mobile_template()) {
            $classes[] = 'jankx-mobile';
        }

        $classes[] = apply_filters('jankx_site_layout_menu_style', 'default-navigation');
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

    public function getLayout($skipDefault = false)
    {
        if (is_null($this->currentLayout)) {
            $this->currentLayout = $this->getCurrentLayout();

            if (empty($this->currentLayout)) {
                if ($skipDefault) {
                    return $this->currentLayout;
                }
                $this->currentLayout = $this->getDefaultLayout();
            }
        }
        return apply_filters('jankx_template_get_site_layout', $this->currentLayout);
    }

    public function getCurrentLayout()
    {
        $pre = apply_filters('jankx_template_pre_get_current_site_layout', null);
        if (!empty($pre)) {
            return $pre;
        }

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
        return apply_filters(
            'jankx_template_default_site_layout',
            is_singular('post') ? static::LAYOUT_CONTENT_SIDEBAR : static::LAYOUT_FULL_WIDTH
        );
    }

    public function registerSidebars()
    {
        $primaryArgs = apply_filters('jankx_site_layout_primary_sidebar_args', array(
            'id' => 'primary',
            'name' => __('Primary Sidebar', 'jankx'),
            'before_widget' => '<section id="%1$s" class="widget jankx-widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h3 class="jankx-title widget-title"><span>',
            'after_title' => '</span></h3>'
        ));
        register_sidebar($primaryArgs);

        if (apply_filters('jankx_site_layout_enable_alt_sidebar', true)) {
            $secondaryArgs = apply_filters('jankx_site_layout_secondary_sidebar_args', array(
                'id' => 'secondary',
                'name' => __('Secondary Sidebar', 'jankx'),
                'before_widget' => '<section id="%1$s" class="widget jankx-widget %2$s">',
                'after_widget' => '</section>',
                'before_title' => '<h3 class="jankx-title widget-title"><span>',
                'after_title' => '</span></h3>'
            ));
            register_sidebar($secondaryArgs);
        }
    }

    public function metaViewport()
    {
        if (!apply_filters('jankx_site_support_responsive_layout', true)) {
            return;
        }
        ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <?php
    }

    public static function getMobileMenus()
    {
        if (is_null(static::$mobileMenus)) {
            static::$mobileMenus = apply_filters(
                'jankx_site_layout_mobile_menus',
                array(
                    Slideout::NAME => Slideout::class
                )
            );
        }
        return static::$mobileMenus;
    }

    public function createMobileMenu()
    {
        // Check theme enable mobile menu
        if (!apply_filters('jankx_site_layout_enable_mobile_menu', true)) {
            return;
        }
        $menus = static::getMobileMenus();
        $useMenu = apply_filters('jankx_site_layout_mobile_menu', Slideout::NAME);

        if (isset($menus[$useMenu])) {
            $mobileMenu = new $menus[$useMenu]();
            if (is_a($mobileMenu, MobileMenuLayout::class)) {
                $mobileMenu->load();
            }
        }
    }
}
