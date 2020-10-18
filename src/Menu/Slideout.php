<?php
namespace Jankx\SiteLayout\Menu;

class Slideout
{
    public function load()
    {
        add_action('jankx_template_before_header', array($this, 'openSlideoutMenu'), 15);
        add_action('jankx_template_after_header', array($this, 'closeSlideoutMenu'), 5);

        add_action('jankx_template_after_header', array($this, 'openMainPanel'), 9);
        add_action('wp_footer', array($this, 'closeMainPanel'), 1);


        $slideDirection = apply_filters('slideout_menu_direction', 'left');

        execute_script("<script>var slideout = new Slideout({
            'panel': document.getElementById('main-panel'),
            'menu': document.getElementById('mobile-menu'),
            'padding': 256,
            'tolerance': 70,
            'side': '{$slideDirection}'
          });

          // Toggle button
          var toogleButton = document.querySelector('.toggle-button');
          if (toogleButton) {
            toogleButton.addEventListener('click', function() {
              slideout.toggle();
            });
          }
          </script>");
    }

    public function openMainPanel()
    {
        ?>
        <div id="main-panel" class="slideout-panel">
        <?php
    }

    public function closeMainPanel()
    {
        ?>
        </div> <!-- end #main-panel block -->
        <?php
    }

    public function openSlideoutMenu()
    {
        ?>
        <nav id="mobile-menu" class="slideout-menu">
        <?php
    }

    public function closeSlideoutMenu()
    {
        ?>
        </nav>
        <?php
    }
}
