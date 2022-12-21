<?php

use Framework\View\Manager;

trait HasManager
{
    protected Manager $manager;

    public function setManager(Manager $manager): static
    {
        $this->manager = $manager;
        return $this;
    }
}
