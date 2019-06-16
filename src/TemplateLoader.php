<?php
namespace Jankx\SiteLayouts;

use Jankx\SiteLayouts\Exceptions\SiteLayoutException;

class TemplateLoader
{
    protected $layout;
    public function __construct($layout)
    {
        $this->layout = $layout;
    }

    public function load()
    {
        if (empty($this->layout) && !is_string($layout)) {
            throw new SiteLayoutException(
                sprintf(),
                SiteLayoutException::SITE_LAYOUT_EXCEPTION_INVALID_LAYOUT
            );
        }
    }
}
