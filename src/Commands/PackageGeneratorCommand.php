<?php namespace Srmklive\PackageGenerator\Commands;

use Illuminate\Console\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputOption;

class PackageGeneratorCommand extends ConsoleCommand
{
    /**
     * The console command name.
     * @var string
     */
    protected $name = 'make:package';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Generate your own custom packages for use in Laravel applications.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $author = $this->input->getOption('author');

        $name = $this->input->getOption('name');

        $namespace = $this->input->getOption('namespace');


    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['author', null, InputOption::VALUE_REQUIRED, 'Package author id (e.g. Acme).'],
            ['name', null, InputOption::VALUE_REQUIRED, 'Package name (e.g. my-package).'],
            ['namespace', null, InputOption::VALUE_OPTIONAL, 'Namespace to be used in Package code.']
        ];
    }
}
