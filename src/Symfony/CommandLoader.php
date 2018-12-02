<?php
/**
 * Created by PhpStorm.
 * User: sasa
 * Date: 01.12.18
 * Time: 15:39
 */

namespace Sasa\Symfony;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;
use Symfony\Component\Console\Exception\CommandNotFoundException;

class CommandLoader implements CommandLoaderInterface
{
    /**
     * @var array
     */
    private $commands;

    /**
     * CommandLoader constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {

        $this->commands = [];

        $allFiles = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        $phpFiles = new \RegexIterator($allFiles, '/\.php$/');

        foreach ($phpFiles as $phpFile) {
            $content = file_get_contents($phpFile->getRealPath());
            $tokens = token_get_all($content);
            $namespace = '';
            for ($index = 0; isset($tokens[$index]); $index++) {
                if (!isset($tokens[$index][0])) {
                    continue;
                }
                if (T_NAMESPACE === $tokens[$index][0]) {
                    $index += 2; // Skip namespace keyword and whitespace
                    while (isset($tokens[$index]) && is_array($tokens[$index])) {
                        $namespace .= $tokens[$index++][1];
                    }
                }
                if (T_CLASS === $tokens[$index][0] && T_WHITESPACE === $tokens[$index + 1][0] && T_STRING === $tokens[$index + 2][0]) {
                    $index += 2; // Skip class keyword and whitespace
                    $className = $namespace.'\\'.$tokens[$index][1];
                    if(class_exists($className)) {
                        $class = new $className();
                        if($class instanceof Command) {
                            $this->commands[$class->getName()] = $class;
                        }
                    }

                    break;
                }
            }
        }
    }


    public function get($name)
    {
        if($this->has($name)){
            return $this->commands[$name];
        }

        throw new CommandNotFoundException($name.' was not found');
    }

    public function has($name)
    {
        return isset($this->commands[$name]);
    }

    public function getNames()
    {
        return array_keys($this->commands);
    }
}