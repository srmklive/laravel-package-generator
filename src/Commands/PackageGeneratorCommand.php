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
    protected $description = 'Create your own custom packages.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $author = $this->option('author');
        $name = $this->option('name');
        $email = $this->option('email');
        $package = $this->option('package');

        // Make sure that package name must consist only underscore & dash for symbols
        if (! preg_match('/^[A-Za-z\\-\\_]{1,}$/i', $package)) {
            throw new \Exception('Name must contain only alphabets, underscore & dash!');
        }

        $namespace = $this->option('namespace');
        if (empty($namespace)) {
            $namespace = $this->generateNamespace($author, $package);
        }
        
        
    }

    /**
     * Generate namespace for package.
     *
     * @param $author
     * @param $name
     * @return string
     */
    protected function generateNamespace($author, $name)
    {
        $namespace = ucfirst($author) . '\\';

        $items = explode('-', preg_replace('/([-_])/', '-', $name));
        if (!empty($items)) {
            foreach ($items as $key => $item) {
                $items[$key] = ucfirst($item);
            }
        }

        $name = str_replace('-', '', implode('-', $items));

        return $namespace . $name;
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
            ['name', null, InputOption::VALUE_REQUIRED, 'Package author name. (e.g. John Doe)'],
            ['email', null, InputOption::VALUE_REQUIRED, 'Package author email. (e.g. johndoe@example.com)'],
            ['package', null, InputOption::VALUE_REQUIRED, 'Package name (e.g. my-package).'],
            ['namespace', null, InputOption::VALUE_OPTIONAL, 'Namespace to be used in Package code.'],
            ['description', null, InputOption::VALUE_OPTIONAL, 'Package description.'],
            ['keywords', null, InputOption::VALUE_OPTIONAL, 'Package keywords.'],
        ];
    }
}
