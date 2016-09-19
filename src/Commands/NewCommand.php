<?php

namespace Srmklive\PackageGenerator\Commands;

use Srmklive\PackageGenerator\Setup as PackageSetup;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class NewCommand extends SymfonyCommand
{
    use PackageSetup;

    /**
     * The input interface.
     *
     * @var InputInterface
     */
    public $input;

    /**
     * The output interface.
     *
     * @var OutputInterface
     */
    public $output;

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('new')
            ->setDescription('Create a new composer package')
            ->addArgument('folder', InputArgument::REQUIRED, 'The folder name containing package contents.')
            ->addOption('laravel', null, InputOption::VALUE_NONE, 'Create Laravel specific package');
    }

    /**
     * Execute the command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws \Exception
     *
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = new SymfonyStyle($input, $output);

        $stubs_dir = realpath(dirname(__FILE__).'/../../stubs');

        $path = getcwd().'/'.$input->getArgument('folder');
        if (!is_dir($path)) {
            @mkdir($path, 0777, true);
            @chmod($path, 0777);
        }

        $author = $this->validateAuthorId(
            $this->askQuestion($input, $output, 'Please enter Package\'s Author ID (e.g. Acme): ')
        );

        $package = $this->validatePackageName(
            $this->askQuestion($input, $output, 'Please enter name of the Package (e.g. myapp): ')
        );

        $name = $this->validateAuthorName(
            $this->askQuestion($input, $output, 'Please enter Package\'s Author Name (e.g. John Doe): ')
        );

        $email = $this->validateEmail(
            $this->askQuestion($input, $output, 'Please enter Author\'s email address: (e.g. johndoe@example.com)')
        );

        $description = $this->askQuestion($input, $output, 'Please enter Package\'s Description: ', 'Descripton for package '.$package);

        $namespace = $this->askQuestion($input, $output, 'Please enter Package Namespace: ', $this->generateNamespace($author, $package));

        $content = [
            'author'           => $author,
            'package'          => $package,
            'name'             => $name,
            'email'            => $email,
            'description'      => $description,
            'class'            => ucfirst($package),
            'namespace'        => $namespace,
            'packagenamespace' => addslashes($namespace),
        ];
    }

    /**
     * Get parameters for creating packages.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param string          $title
     * @param string          $hint
     *
     * @return mixed
     */
    private function askQuestion($input, $output, $title, $hint = '')
    {
        $helper = $this->getHelper('question');

        $question = new Question($title, $hint);

        return $helper->ask($input, $output, $question);
    }
}
