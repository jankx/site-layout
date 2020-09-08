<?php
namespace Jankx\SiteLayout\Integration;

use Jankx\Template\Page;
use Jankx\SiteLayout\SiteLayout;

class Elementor
{
    public function __construct()
    {
        add_action('template_redirect', array($this, 'customTemplates'));
    }

    public function customTemplates()
    {
        add_filter('jankx_template_pre_get_current_site_layout', array($this, 'makeFullwidthLayout'));
    }

    public function makeFullwidthLayout($pre)
    {
        $page = Page::getInstance();
        $templateFile = $page->getTemplateFile();

        if (preg_match('/elementor\/.+\/templates\/header\-footer\.php$/', $templateFile)) {
            return SiteLayout::LAYOUT_FULL_WIDTH;
        }
        return $pre;
    }
}
