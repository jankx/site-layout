<?php

namespace Jankx\SiteLayout;

if (!defined('ABSPATH')) {
    exit('Cheatin huh?');
}

class GutenbergLayoutLoader extends LayoutLoader
{
    protected $layout;
    protected $engine;
    protected $fullContent = false;
}
