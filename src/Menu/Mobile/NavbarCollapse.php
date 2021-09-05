<?php
namespace Jankx\SiteLayout\Menu\Mobile;

use Jankx\SiteLayout\Constracts\MobileMenuLayout;

class NavbarCollapse implements MobileMenuLayout {
    const NAME = 'offcanvas';

    public function load()
    {
        add_filter('body_class', array($this, 'appendCollapseStyleToBody'));
    }

    public function appendCollapseStyleToBody($classes) {
        $classes[] = 'menu-style-collapse';

        return $classes;
    }
}
