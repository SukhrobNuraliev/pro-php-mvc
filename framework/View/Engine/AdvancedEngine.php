<?php

namespace Framework\View\Engine;

use Exception;
use Framework\View\View;

class AdvancedEngine implements Engine
{
    use HasManager;

    protected array $layouts = [];

    /**
     * @throws Exception
     */
    public function render(View $view): string
    {
        $hash = md5($view->path);
        $folder = __DIR__ . '/../../../storage/framework/views';
        $cached = realpath("{$folder}/{$hash}.php");
        if (!file_exists($hash) || filemtime($view->path) >
            filemtime($hash)) {
            $content = $this->compile(file_get_contents($view->path));
            file_put_contents($cached, $content);
        }
        extract($view->data);
        ob_start();
        include($cached);
        $contents = ob_get_contents();
        ob_end_clean();
        if ($layout = $this->layouts[$cached] ?? null) {
            return view($layout, array_merge(
                $view->data,
                ['contents' => $contents],
            ));
        }
        return $contents;
    }

    protected function compile(string $template): string
    {
        // replace `@extends` with `$this->extends`
        $template = preg_replace_callback('#@extends\(([^)]+)\)#',
            function ($matches) {
                return '<?php $this->extends(' . $matches[1] . '); ?>';
            }, $template);

        // replace `@id` with `if(...):`
        $template = preg_replace_callback('#@if\(([^)]+)\)#',
            function ($matches) {
                return '<?php if(' . $matches[1] . '): ?>';
            }, $template);

        // replace `@endif` with `endif`
        $template = preg_replace_callback('#@endif#', function ($matches) {
            return '<?php endif; ?>';
        }, $template);

        // replace `{{ ... }}` with `print $this->escape(...)`
        $template = preg_replace_callback('#\{\{([^}]+)\}\}#', function ($matches) {
            return '<?php print $this->escape(' . $matches[1] . '); ?>';
        }, $template);

        // replace `{!! ... !!}` with `print ...`
        $template = preg_replace_callback('#\{!!([^}]+)!!\}#',
            function ($matches) {
                return '<?php print ' . $matches[1] . '; ?>';
            }, $template);

        // replace `@***(...)` with `$this->***(...)`
        $template = preg_replace_callback('#@([^(]+)\(([^)]+)\)#',
            function ($matches) {
                return '<?php $this->' . $matches[1] . '(' . $matches[2] . '); ?>';
            }, $template);

        return $template;
    }

    /**
     * @throws Exception
     */
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

}
