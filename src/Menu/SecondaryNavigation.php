<?php

namespace Jankx\SiteLayout\Menu;

use Jankx\GlobalConfigs;

class SecondaryNavigation
{
    public function __construct()
    {
    }

    public function init()
    {
        add_action('jankx/component/header/content/after', [$this, 'loadSecondaryMenu']);
    }

    public function loadSecondaryMenu()
    {
        jankx_component('nav', [
            'theme_location' => 'secondary',
            'open_container' => true,
            'sticky' => GlobalConfigs::get('customs.layout.menu.secondary.sticky', false)
        ], true);
    }
}
