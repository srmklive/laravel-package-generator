<?php

namespace Srmklive\PackageGenerator\Commands;

use Illuminate\Console\Command as ConsoleCommand;
use Srmklive\PackageGenerator\Setup as PackageSetup;
use Symfony\Component\Console\Input\InputOption;

class PackageGeneratorCommand extends ConsoleCommand
{
    use PackageSetup;

    /**
     * The console command name.
     * @var string
     */
    protected $name = 'make:package';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Create new custom package.';

    /**
     * Execute the console command.
     *
     * @throws \Exception
     */
    public function handle()
    {
        $author = $this->option('author');
        $name = $this->option('name');
        $email = $this->option('email');
        $package = $this->option('package');
        $description = $this->option('description');
        $keywords = $this->option('keywords');

        // Make sure that package name must consist only underscore & dash for symbols
        if (! preg_match('/^[A-Za-z\\-\\_]{1,}$/i', $package)) {
            throw new \Exception('Name must contain only alphabets, underscore & dash!');
        }

        $namespace = $this->option('namespace');
        if (empty($namespace)) {
            $namespace = $this->generateNamespace($author, $package);
        }

        $vendor_dir = base_path('vendor');
        $package_dir = $vendor_dir . "/$author/$package";
        $stubs_dir = realpath(dirname(__FILE__) . '/../../stubs');

        // Create package directory
        if (! is_dir($package_dir)) {
            @mkdir($package_dir, 0777, true);
            @chmod($package_dir, 0777);
        }

        $content = [
            'author' => $author,
            'package' => $package,
            'name' => $name,
            'email' => $email,
            'description' => $description,
            'class' => ucfirst($package),
            'namespace' => $namespace,
            'packagenamespace' => addslashes($namespace)
        ];

        // Copy source code files
        $this->createPackageSource($stubs_dir, $package_dir, $content);
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
