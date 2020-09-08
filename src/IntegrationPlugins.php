<?php
namespace Jankx\SiteLayout;

use Jankx\SiteLayout\Integration\Elementor;

class IntegrationPlugins
{
    protected static $activePlugins;

    public function __construct()
    {
        static::$activePlugins = get_option('active_plugins', array());
    }

    public function integrate()
    {
        if (in_array('elementor/elementor.php', static::$activePlugins)) {
            new Elementor();
        }
    }
}
