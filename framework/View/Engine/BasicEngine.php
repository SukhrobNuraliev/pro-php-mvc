<?php

namespace Framework\View\Engine;

use Framework\View\Engine\Engine;
use Framework\View\View;
use HasManager;

class BasicEngine implements Engine
{
    use HasManager;

    public function render(View $view): string
    {
        $contents = file_get_contents($view->path);
        foreach ($view->data as $key => $value) {
            $contents = str_replace(
                '{' . $key . '}', $value, $contents
            );
        }
        return $contents;
    }
}
