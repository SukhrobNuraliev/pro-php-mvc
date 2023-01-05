<?php

namespace Framework\Database\Command;

use Framework\Database\Connection\Connection;
use Framework\Database\Connection\MysqlConnection;
use Framework\Database\Connection\SqliteConnection;
use Framework\Database\Exception\QueryException;
use Framework\Database\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends Command
{
    protected static $defaultName = 'migrate';

    protected function configure()
    {
        $this->setDescription('Migrates the database')
            ->addOption('fresh', null, InputOption::VALUE_NONE, 'Delete all tables before running the migrations')
            ->setHelp('This command looks for all migration files and runs them');
    }

    /**
     * @throws QueryException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $current = getcwd();
        $pattern = 'database/migrations/*.php';
        $paths = glob("{$current}/{$pattern}");
        if (count($paths) < 1) {
            $this->writeln();
            return Command::SUCCESS;
        }

        $connection = app('database');

        if ($input->getOption('fresh')) {
            $output->writeln('Dropping existing database tables');
            $connection->dropTables();
            $connection = app('database');
        }

        if (!$connection->hasTable('migrations')) {
            $output->writeln('Creating migrations table');
            $this->createMigrationsTable($connection);
        }

        foreach ($paths as $path) {
            [$prefix, $file] = explode('_', $path);
            [$class, $extension] = explode('.', $file);
            require $path;
            $obj = new $class();
            $obj->migrate($connection);

            $connection
                ->query()
                ->from('migrations')
                ->insert(['name'], ['name' => $class]);
        }

        return Command::SUCCESS;
    }

    private function createMigrationsTable(Connection $connection)
    {
        $table = $connection->createTable('migrations');
        $table->id('id');
        $table->string('name');
        $table->execute();
    }

}
