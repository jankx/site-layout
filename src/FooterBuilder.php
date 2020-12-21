<?php
namespace Jankx\SiteLayout;

class FooterBuilder
{
    protected static $numOfFooterWidgets;

    public function build()
    {
        add_action('widgets_init', array($this, 'registerSidebars'), 30);
        add_action('template_redirect', array($this, 'renderFooterWidgetsContent'), 30);
        add_action('wp_enqueue_scripts', array($this, 'generateFooterWidgetStyles'));
    }

    public function renderFooterWidgetsContent()
    {
        $numOfFooterWidgets = apply_filters('jankx_template_num_of_frontend_footer_widgets', static::$numOfFooterWidgets);
        if ($numOfFooterWidgets <= 0) {
            return;
        }
        add_action('jankx_template_before_footer_widgets', array($this, 'openFooterWidgetAreas'));
        add_action('jankx_template_footer_widgets', array($this, 'render'));
        add_action('jankx_template_after_footer_widgets', array($this, 'closeFooterWidgetAreas'));
    }

    public static function getNumOfFooterWidgets()
    {
        if (!is_null(static::$numOfFooterWidgets)) {
            return static::$numOfFooterWidgets;
        }
        static::$numOfFooterWidgets = apply_filters('jankx_template_num_of_footer_widgets', 4);

        // Return the number of the footer widgets
        return static::$numOfFooterWidgets;
    }

    public function registerSidebars()
    {
        $numOfFooterWidgets = static::getNumOfFooterWidgets();

        /**
         * Disable footer widget when the number of it is larger
         * jankx_template_maximum_footer_widgets: 10
         */
        if ($numOfFooterWidgets > apply_filters('jankx_template_maximum_footer_widgets', 10)) {
            $numOfFooterWidgets = 0;
        }

        // Disable footer widgets when the num of it less than and equal 0
        if ($numOfFooterWidgets <= 0) {
            return;
        }

        // Make footer widget sidebar has same structure
        $footerWidgetSidebarArgs = apply_filters('jankx_template_footer_widget_args', array(
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="jankx-title widget-title">',
            'after_title'   => '</h3>'
        ));

        $currentSidebarIndex = 1;
        while ($currentSidebarIndex <= $numOfFooterWidgets) {
            // Create the name and description of footer widgets are diferrent
            $footerWidgetSidebarArgs = array_merge(
                $footerWidgetSidebarArgs,
                array(
                    'id'          => sprintf('footer_%d', $currentSidebarIndex),
                    'name'        => sprintf(__('Footer %d', 'jankx'), $currentSidebarIndex),
                    'description' => sprintf('The widgets are show in the footer area #%d', $currentSidebarIndex)
                )
            );

            // Register sidebar for footer widget
            register_sidebar(apply_filters(
                "jankx_template_footer_widget_sidebar{$currentSidebarIndex}_args",
                $footerWidgetSidebarArgs,
                $currentSidebarIndex,
                $footerWidgetSidebarArgs
            ));
            $currentSidebarIndex += 1;
        }
    }

    public function openFooterWidgetAreas()
    {
        jankx_template('layout/footer/open-widget-areas', array(
            'footer_widget_classes' => implode(' ', (array)apply_filters(
                'jankx_template_footer_widget_wrapper_class',
                array('jankx-footer-widgets-area')
            ))));
    }

    public function closeFooterWidgetAreas()
    {
        jankx_template('layout/footer/close-widget-areas');
    }

    public function render()
    {
        $numOfFooterWidgets = static::getNumOfFooterWidgets();
        // Do not render anything when footer widgets area is disable
        if ($numOfFooterWidgets <= 0) {
            return;
        }
        $currentSidebarIndex = 1;

        do_action('jankx_template_before_footer_widgets');
        while ($currentSidebarIndex <= $numOfFooterWidgets) {
            jankx_template(array(
                "layout/footer/widget-areas/area-{$currentSidebarIndex}",
                'layout/footer/widget-areas/general'
            ), array(
                'index' => $currentSidebarIndex,
            ));
            $currentSidebarIndex += 1;
        }
        do_action('jankx_template_after_footer_widgets');
    }

    public function generateFooterWidgetStyles()
    {
        $numOfFooterWidgets = static::getNumOfFooterWidgets();
        // Disable footer widgets when the num of it less than and equal 0
        if ($numOfFooterWidgets <= 0) {
            return;
        }

        // Load footer styles and don't echo by set 4 argument value is `false`
        jankx_template('layout/footer/styles', compact('numOfFooterWidgets'), null, false);
    }
}
