<?php

use App\Console\Commands\NameCommand;
use Framework\Database\Command\MigrateCommand;
use Framework\Database\Command\WorkCommand;

return [
    NameCommand::class,
    MigrateCommand::class,
    WorkCommand::class,
];