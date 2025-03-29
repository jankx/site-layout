<?php

namespace Jankx\SiteLayout\Menu\Mobile;

use Jankx\SiteLayout\Constracts\MobileMenuLayout;

class SecondaryMenuOffcanvas implements MobileMenuLayout
{
    const NAME = 'secondary-nav-slideout';

    public function load()
    {
        add_filter('jankx_asset_js_dependences', function ($deps) {

            $deps[] = 'mmenu-light';
            return $deps;
        });
        add_filter('jankx_asset_css_dependences', function ($deps) {

            $deps[] = 'mmenu-light';
            return $deps;
        });
        add_filter('jankx_component_navigation_secondary_args', function ($args) {

            // $args['container_class'] = 'mm-menu mm-horizontal mm-offcanvas';
            $args['container_id'] = 'offcanvas-menu';
            return $args;
        });
        add_action('body_class', array($this, 'addMmenuToBodyClasses'));
        execute_script(jsContent: '<script>
                const mmenu = new MmenuLight(
                    document.querySelector( "#offcanvas-menu" ),
                    "(max-width: 767px)"
                );
                const navigator = mmenu.navigation({
                    // options
                });
                const drawer = mmenu.offcanvas({
                    // options
                });
                document.querySelector( "button.toggle-sp-menu-button" )
                                .addEventListener( "click", ( event ) => {
                                    event.preventDefault();
                                    drawer.open();
                                });
            </script>'
        );
    }


    public function addMmenuToBodyClasses($classes)
    {
        $classes[] = 'mmenu-offcanvas';
        return $classes;
    }
}
