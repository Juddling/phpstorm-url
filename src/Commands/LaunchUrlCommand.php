<?php


namespace Juddling\PHPStorm;


use Illuminate\Console\Command;

class LaunchUrlCommand extends Command
{
    protected $signature = 'phpstorm:url {url}';
    protected $description = 'Takes a URL and opens its controller in PHPStorm';

    public function fire()
    {
        \Route::dispatch(Illuminate\Http\Request::create($url, 'GET'));
        list($controller, $action) = explode('@', \Route::currentRouteAction());

        $filename = str_replace('\\', '/', $controller) . '.php';
        $lineNumber = $this->lineNumber($action, $filename);

        // PHPStorm command line launcher
        $launchCommand = sprintf("/usr/local/bin/phpstorm . --line %s %s", $lineNumber, $filename);
        $this->info($launchCommand);
        shell_exec($launchCommand);
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