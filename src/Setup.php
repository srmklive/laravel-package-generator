<?php

namespace Srmklive\PackageGenerator;

trait Setup
{
    /**
     * Validate package author id.
     *
     * @param string $author
     *
     * @throws \Exception
     *
     * @return string
     */
    private function validateAuthorId($author)
    {
        if (empty($author) && (!preg_match('/^[A-Za-z0-9\\_]{1,}$/i', $author))) {
            throw new \Exception('Author ID must not be empty and should contain only alphabets, underscore & digits!');
        }

        return $author;
    }

    /**
     * Validate package author name.
     *
     * @param string $author
     *
     * @throws \Exception
     *
     * @return string
     */
    private function validateAuthorName($author)
    {
        if (empty($author)) {
            throw new \Exception('Author name must not be empty!');
        }

        return $author;
    }

    /**
     * Validate package author's email address.
     *
     * @param string $email
     *
     * @throws \Exception
     *
     * @return mixed
     */
    private function validateEmail($email)
    {
        $pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/";

        if (empty($email) ||
            (!empty($email) && !preg_match($pattern, $email))
        ) {
            throw new \Exception('Please provide a valid email address!');
        }

        return $email;
    }

    /**
     * Validate package name.
     *
     * @param string $package
     *
     * @throws \Exception
     *
     * @return mixed
     */
    private function validatePackageName($package)
    {
        $pattern = '/^[A-Za-z\\-\\_]{1,}$/i';

        if (empty($package) ||
            (!empty($package) && !preg_match($pattern, $package))
        ) {
            throw new \Exception('Package name must not be empty and should contain only alphabets, underscore & dash!!');
        }

        return $package;
    }

    /**
     * Generate namespace for package.
     *
     * @param $author
     * @param $name
     *
     * @return string
     */
    protected function generateNamespace($author, $name)
    {
        $namespace = ucfirst($author).'\\';

        $items = explode('-', preg_replace('/([-_])/', '-', $name));
        if (!empty($items)) {
            foreach ($items as $key => $item) {
                $items[$key] = ucfirst($item);
            }
        }

        $name = str_replace('-', '', implode('-', $items));

        return $namespace.$name;
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

        if (!is_dir($destination)) {
            @mkdir($destination, 0777, true);
        }

        $replace = [
            'DummyAuthorID'           => 'author',
            'DummyPackageName'        => 'package',
            'DummyAuthorName'         => 'name',
            'DummyAuthorEmail'        => 'email',
            'DummyPackageDescription' => 'description',
            'DummyClassName'          => 'class',
            'DummyNameSpace'          => 'namespace',
        ];

        foreach ($source_dir as $item) {
            // Skip dot items
            if ($item->isDot()) {
                continue;
            }

            $tmp_destination = str_replace($source, $destination, $item->getPathname());

            if ($item->isFile()) {
                if (!empty($extension)) {
                    $tmp_destination = str_replace('.stub', $extension, $tmp_destination);
                }

                $tmp_file = basename($tmp_destination);

                if (in_array($tmp_file, ['ServiceProvider.php', 'Class.php', 'FacadeAccessor.php'])) {
                    if (in_array($tmp_file, ['ServiceProvider.php', 'FacadeAccessor.php'])) {
                        $tmp_file = "$content[class]".$tmp_file;
                    } else {
                        $tmp_file = "$content[class].php";
                    }

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
                    if (!empty($content[$value])) {
                        $file_content = str_replace($key, $content[$value], $file_content);
                    }
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
}
