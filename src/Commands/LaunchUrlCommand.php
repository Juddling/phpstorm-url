<?php


namespace Juddling\PHPStorm;


use Illuminate\Console\Command;

class LaunchUrlCommand extends Command
{
    protected $signature = 'phpstorm:url {url}';
    protected $description = 'Takes a URL and opens its controller in PHPStorm';

    public function fire()
    {
        \Route::dispatch(\Illuminate\Http\Request::create($this->argument('url'), 'GET'));
        list($controller, $action) = explode('@', \Route::currentRouteAction());

        $filename = str_replace('\\', '/', $controller) . '.php';
        $lineNumber = $this->lineNumber($action, $filename);

        // PHPStorm command line launcher
        $launchCommand = sprintf("%s . --line %s %s", $this->findLauncher(), $lineNumber, $filename);
        $this->info($launchCommand);
        shell_exec($launchCommand);
    }

    private function findLauncher() {
        $paths = [
            "/usr/local/bin/phpstorm",
            "/usr/local/bin/pstorm"
        ];

        foreach($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        throw new \RuntimeException("Couldn't find PHPStorm launcher, have you created one?");
    }

    private function lineNumber($functionName, $fileName) {
        $grepOutput = shell_exec("grep -n \"function $functionName\" $fileName");
        $matches = [];

        if (!preg_match("/([0-9]+)/", $grepOutput, $matches)) {
            throw new RuntimeException("Couldn't find function in file");
        }

        return $matches[0];
    }
}