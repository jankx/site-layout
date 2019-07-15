<?php
namespace Jankx\SiteLayout\Admin;

use Jankx\SiteLayout\Admin\Metabox\PostLayout;

class SiteLayout
{
    protected $postLayout;


    public function __construct()
    {
        $this->postLayout = new PostLayout();
        $this->initHooks();
    }


    public function initHooks()
    {
        add_action('add_meta_boxes', array($this->postLayout, 'addMetabox'));
        add_action('save_post', array($this->postLayout, 'savePost'), 10, 2);
    }
}
