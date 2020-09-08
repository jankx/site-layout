<?php
namespace Jankx\SiteLayout;

class FooterBuilder
{
    public function build()
    {
        add_action('widgets_init', array($this, 'registerSidebars'), 30);
    }

    public function registerSidebars()
    {
        $numOfFooterWidgets = apply_filters('jankx_template_num_of_footer_widgets', 4);

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
            'after_widget' => '</div>',
            'before_title' => '<h3 class="jankx-title widget-title">',
            'after_title' => '</h3>'
        ));

        $currentSidebarIndex = 1;
        while ($currentSidebarIndex <= $numOfFooterWidgets) {
            // Create the name and description of footer widgets are diferrent
            $footerWidgetSidebarArgs = array_merge(
                $footerWidgetSidebarArgs,
                array(
                    'id' => sprintf('footer_%d', $currentSidebarIndex),
                    'name' => sprintf(__('Footer %d', 'jankx'), $currentSidebarIndex),
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
}
