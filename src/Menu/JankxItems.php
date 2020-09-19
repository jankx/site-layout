<?php
namespace Jankx\SiteLayout\Menu;

class JankxItems
{
    protected static $jankxNavItems;

    public function register()
    {
        add_action('admin_head-nav-menus.php', array( $this, 'add_menu_meta_boxes' ));
        add_filter('wp_setup_nav_menu_item', array($this, 'setup_jankx_items'));
    }

    public function add_menu_meta_boxes()
    {
        add_meta_box(
            'jankx_nav_links',
            __('Jankx Items', 'jankx'),
            array( $this, 'nav_menu_links' ),
            'nav-menus',
            'side'
        );
    }

    protected function get_nav_items()
    {
        if (!is_null(static::$jankxNavItems)) {
            return static::$jankxNavItems;
        }

        static::$jankxNavItems = array(
            'jankx_logo' => __('Site Logo', 'jankx'),
            'jankx_search_form' => __('Search Form', 'jankx')
        );

        return static::$jankxNavItems;
    }

    protected function create_menu_nav_item($key)
    {
        $title = '';
        if ($key === 'jankx_logo') {
            $title = get_bloginfo('name');
        }

        $item = wp_parse_args(array(), array(
            'type' => $key,
            'title' => $title,
            'url' => "#jankx-{$key}",
            'classes' => null
        ));

        return apply_filters("jankx_site_layout_{$key}_nav_item", $item, $key);
    }

    protected function render_menu_item_hidden_input($index, $item)
    {
        foreach ($item as $type => $value) : ?>
            <?php if (is_null($value)) : ?>
                <input type="hidden"
                    class="menu-item-<?php echo $type; ?>"
                    name="menu-item[<?php echo esc_attr($index); ?>][menu-item-<?php echo $type; ?>]"
                />
            <?php else : ?>
                <input
                    type="hidden"
                    class="menu-item-type"
                    name="menu-item[<?php echo esc_attr($index); ?>][menu-item-<?php echo $type; ?>]"
                    value="<?php echo $value; ?>"
                />
            <?php endif; ?>
            <?php
        endforeach;
    }

    public function nav_menu_links()
    {
        $items = $this->get_nav_items();
        ?>
        <div id="posttype-jankx-nav-items" class="posttypediv">
            <div id="tabs-panel-jankx-nav-items" class="tabs-panel tabs-panel-active">
                <ul id="jankx-nav-items-checklist" class="categorychecklist form-no-clear">
                    <?php
                    $i = -1;
                    foreach ($items as $key => $value) :
                        $item = $this->create_menu_nav_item($key);
                        ?>
                        <li>
                            <label class="menu-item-title">
                                <input
                                    type="checkbox"
                                    class="menu-item-checkbox"
                                    name="menu-item[<?php echo esc_attr($i); ?>][menu-item-object-id]"
                                    value="<?php echo esc_attr($i); ?>"
                                />
                                <?php echo esc_html($value); ?>
                            </label>
                            <?php $this->render_menu_item_hidden_input($i, $item); ?>
                        </li>
                        <?php
                        $i--;
                    endforeach;
                    ?>
                </ul>
            </div>
            <p class="button-controls">
                <span class="list-controls">
                    <a href="<?php echo esc_url(admin_url('nav-menus.php?page-tab=all&selectall=1#posttype-jankx-nav-items')); ?>" class="select-all"><?php esc_html_e('Select all', 'jankx'); ?></a>
                </span>
                <span class="add-to-menu">
                    <button
                        type="submit"
                        class="button-secondary submit-add-to-menu right"
                        value="<?php esc_attr_e('Add to menu', 'jankx'); ?>"
                        name="add-post-type-menu-item"
                        id="submit-posttype-jankx-nav-items"
                    >
                        <?php esc_html_e('Add to menu', 'jankx'); ?>
                    </button>
                    <span class="spinner"></span>
                </span>
            </p>
        </div>
        <?php
    }

    public function setup_jankx_items($menu_item)
    {
        $items = $this->get_nav_items();

        if (isset($items[$menu_item->type])) {
            $menu_item->type_label = sprintf('Jankx %s', $items[$menu_item->type]);
        }

        return $menu_item;
    }
}
