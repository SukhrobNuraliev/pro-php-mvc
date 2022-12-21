<?php

namespace Framework\View\Engine;

use Exception;
use Framework\View\View;
use HasManager;

class PhpEngine implements Engine
{
    use HasManager;

    protected array $layouts = [];

    /**
     * @throws Exception
     */
    public function render(View $view): string
    {
        extract($view->data);
        ob_start();
        include($view->path);
        $contents = ob_get_contents();
        ob_end_clean();
        if ($layout = $this->layout[$view->path] ?? null) {
            return view($layout, array_merge(
                $view->data,
                ['contents' => $contents],
            ));
        }
        return $contents;
    }

    public function __call(string $name, $values)
    {
        return $this->manager->useMacro($name, ...$values);
    }

    protected function extends(string $template): static
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        $this->layouts[realpath($backtrace[0]['file'])] = $template;
        return $this;
    }

    /**
     * @throws Exception
     */
    protected function includes(string $template, $data = []): void
    {
        print view($template, $data);
    }
}