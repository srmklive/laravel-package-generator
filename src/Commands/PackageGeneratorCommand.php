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
     * Copy package source files.
     *
     * @param $source
     * @param $destination
     * @param $content
     * @param string $extension
     */
    private function createPackageSource($source, $destination, $content, $extension = '')
    {
        $source_dir = new \DirectoryIterator($source);

        if (! is_dir($destination))
            @mkdir($destination, 0777, true);

        $replace = [
            'DummyAuthorID' => 'author',
            'DummyPackageName' => 'package',
            'DummyAuthorName' => 'name',
            'DummyAuthorEmail' => 'email',
            'DummyPackageDescription' => 'description',
            'DummyClassName' => 'class',
            'DummyNameSpace' => 'namespace',
        ];

        foreach ($source_dir as $item) {
            // Skip dot items
            if ($item->isDot())
                continue;

            $tmp_destination = str_replace($source, $destination, $item->getPathname());

            if ($item->isFile()) {
                if (!empty($extension))
                    $tmp_destination = str_replace('.stub', $extension, $tmp_destination);

                $tmp_file = basename($tmp_destination);

                if (in_array($tmp_file, ['ServiceProvider.php','Class.php','FacadeAccessor.php'])) {
                    if (in_array($tmp_file, ['ServiceProvider.php','FacadeAccessor.php']))
                        $tmp_file = "$content[class]" . $tmp_file;
                    else
                        $tmp_file = "$content[class].php";

                    $tmp_destination = str_replace(
                        basename($tmp_destination),
                        $tmp_file,
                        $tmp_destination
                    );
                }

                copy($item->getPathname(), $tmp_destination);
                @chmod($tmp_destination, 0777);

                $file_content = file_get_contents($tmp_destination);
                foreach ($replace as $key => $value) {
                    if (!empty($content[$value]))
                        $file_content = str_replace($key, $content[$value], $file_content);
                }

                file_put_contents($tmp_destination, $file_content);
            }

            if ($item->isDir()) {

                $item_dir = str_replace(
                    $source,
                    $destination,
                    $item->getPathname()
                );

                $this->createPackageSource(
                    $item->getPathname(),
                    $item_dir,
                    $content,
                    '.php'
                );
            }
        }
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
