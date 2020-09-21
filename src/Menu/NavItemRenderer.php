<?php
namespace Jankx\SiteLayout\Menu;

use Jankx\Option\Option;
use Jankx\SiteLayout\Menu\JankxItems;

class NavItemRenderer
{
    protected $hook_walker_nav_menu_start_el_is_called;

    public function resetWalkerSupportHookStartEl($sorted_menu_items)
    {
        $this->hook_walker_nav_menu_start_el_is_called = false;

        // Does not do anythin and return $sorted_menu_items
        return $sorted_menu_items;
    }

    public function getJankxLogo($item, $depth, $args)
    {
        $logoType = Option::get('logo_type', 'image');

        return jankx_component('logo', array(
            'type' => $logoType,
            'text' => $item->post_title,
            'image_url' => Option::get('logo_image_url'),
        ));
    }

    public function getJankxSearchForm($item, $depth, $args)
    {
        return get_search_form(array(
            'echo' => false,
        ));
    }

    protected function getContent($item_output, $item, $depth, $args)
    {
        $method = sprintf("get%s", preg_replace_callback(array('/^([a-z])/', '/[-_]([a-z])/'), function ($matches) {
            if (isset($matches[1])) {
                return strtoupper($matches[1]);
            }
        }, $item->type));
        $callable = apply_filters('jankx_site_layout_nav_item_callback', array($this, $method), $item, $depth, $args);

        if (!is_callable($callable)) {
            return $item_output;
        }

        return call_user_func($callable, $item, $depth, $args);
    }

    public function renderMenuItem($item_output, $item, $depth, $args)
    {
        // Create the flag to
        if (!$this->hook_walker_nav_menu_start_el_is_called) {
            $this->hook_walker_nav_menu_start_el_is_called = true;
        }
        $jankxItems = JankxItems::get_nav_items();

        if (isset($jankxItems[$item->type])) {
            $pre = apply_filters("jankx_site_layout_nav_item_{$item->type}", null, $item_output, $item, $depth, $args);
            if ($pre) {
                return $pre;
            }
            $content = $this->getContent($item_output, $item, $depth, $args);
            if ($content) {
                $item_output = $content;
            }
        }

        return $item_output;
    }

    public function renderMenuItemSubtitle($title, $item, $args, $depth)
    {
        $subtitle = get_post_meta($item->ID, '_jankx_menu_item_subtitle', true);
        if (!$subtitle) {
            return $title;
        }
        return sprintf('%s<span class="jankx-subtitle menu-item-subtitle">%s</span>', $title, $subtitle);
    }
}
