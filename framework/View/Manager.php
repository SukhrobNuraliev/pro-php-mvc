<?php

namespace Framework\View;

use Exception;
use Framework\View\Engine\Engine;

class Manager
{
    protected array $paths = [];
    protected array $engines = [];
    protected array $macros = [];

    public function addMacro(string $name, \Closure $closure): static
    {
        $this->macros[$name] = $closure;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function useMacro(string $name, ...$values)
    {
        if (isset($this->macros[$name])) {
            // we bind the closure so that $this
            // inside a macro refers to the view object
            // which means $data and $path can be used
            // and you can get back to the $engine...
            $bound = $this->macros[$name]->bindTo($this);
            return $bound(...$values);
        }
        throw new Exception("Macro isn't defined: '{$name}'");
    }

    public function addPath(string $path): static
    {
        $this->paths[] = $path;
        return $this;
    }

    public function addEngine(string $extension, Engine $engine): static
    {
        $this->engines[$extension] = $engine;
        $this->engines[$extension]->setManager($this);
        return $this;
    }

    /**
     * @throws Exception
     */
    public function resolve(string $template, array $data = []): View
    {
        foreach ($this->engines as $extension => $engine) {
            foreach ($this->paths as $path) {
                $file = "{$path}/{$template}.{$extension}";
                if (is_file($file)) {
                    return new View($engine, realpath($file), $data);
                }
            }
        }
        throw new Exception("Could not render '{$template}'");
    }
}