<?php

namespace Juddling\PHPStorm;

use Composer\Autoload\ClassLoader;
use Illuminate\Console\Command;
use Illuminate\Routing\Route;

class LaunchUrlCommand extends Command
{
    protected $signature = 'phpstorm:url {url}';
    protected $description = 'Takes a URL and opens its controller in PHPStorm';

    public function handle()
    {
        // Create a new request
        $request = \Illuminate\Http\Request::create($this->argument('url'), 'GET');

        /** @var Route $route */
        $route = \Route::getRoutes()->match($request);

        list($controller, $action) = explode('@', $route->getActionName());

        $filename = $this->fileName($controller);
        $lineNumber = $this->lineNumber($action, $filename);

        // Execute the command to open the file
        $launchCommand = sprintf("%s . --line %s %s", $this->findLauncher(), $lineNumber, $filename);
        $this->info($launchCommand);
        shell_exec($launchCommand);
    }

    /*
     * Keep this for compatibility with older Laravel versions
     */
    public function fire()
    {
        $this->handle();
    }

    /**
     * Given a class name, it will return the file name and path, using the initialised Composer class loader
     */
    private function fileName($className)
    {
        $projectComposerAutoloadFile = __DIR__ . '/../../../autoload.php';

        if (!file_exists($projectComposerAutoloadFile)) {
            throw new \RuntimeException("Couldn't open project's autoload file");
        }

        /** @var ClassLoader $classLoader */
        $classLoader = require $projectComposerAutoloadFile;
        // leading backslash will cause Composer to not find the class
        $className = trim($className, '\\');

        return $classLoader->getClassMap()[$className];
    }

    /**
     * Returns the path to the installed PHPstorm command line launcher
     */
    private function findLauncher()
    {
        $paths = [
            "/usr/local/bin/pstorm",
            "/usr/local/bin/phpstorm"
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        throw new \RuntimeException("Couldn't find PHPStorm launcher, have you created one?");
    }

    /**
     * Returns the line number in which a function is defined in a given file
     */
    public function lineNumber($functionName, $fileName)
    {
        $file = fopen($fileName, 'r');

        $lineNumber = 1;
        while ($line = fgets($file)) {
            if (preg_match("/function $functionName/", $line)) {
                return $lineNumber;
            }

            $lineNumber++;
        }

        return 1;
    }
}
