<?php


namespace Juddling\PHPStorm;


use Composer\Autoload\ClassLoader;
use Illuminate\Console\Command;

class LaunchUrlCommand extends Command
{
    protected $signature = 'phpstorm:url {url}';
    protected $description = 'Takes a URL and opens its controller in PHPStorm';

    public function fire()
    {
        // Run a new request through the Laravel router
        \Route::dispatch(\Illuminate\Http\Request::create($this->argument('url'), 'GET'));
        list($controller, $action) = explode('@', \Route::currentRouteAction());

        $filename = $this->fileName($controller);
        $lineNumber = $this->lineNumber($action, $filename);

        // Execute the command to open the file
        $launchCommand = sprintf("%s . --line %s %s", $this->findLauncher(), $lineNumber, $filename);
        $this->info($launchCommand);
        shell_exec($launchCommand);
    }

    /**
     * Given a class name, it will return the file name and path, using the initialised Composer class loader
     */
    private function fileName($className)
    {
        /** @var ClassLoader $classLoader */
        $classLoader = require __DIR__ . '/../../../../autoload.php';
        return $classLoader->findFile($className);
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
    private function lineNumber($functionName, $fileName)
    {
        $grepOutput = shell_exec("grep -n \"function $functionName\" $fileName");
        $matches = [];

        if (!preg_match("/([0-9]+)/", $grepOutput, $matches)) {
            throw new RuntimeException("Couldn't find function in file");
        }

        return $matches[0];
    }
}