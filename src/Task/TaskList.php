<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @copyright
 */

namespace Razeor\Task;

use Razeor\Checker\TaskInfoChecker;
use Razeor\Config;
use Razeor\JsonParser;
use Razeor\ShellOutput;

/**
 * Used to storage all valid tasks
 * The instance is managed by TaskManager
 *
 * The instance can pass to foreach(), the key is a task name,
 * the value is a task information, includes 'mainClass' and 'time'
 * @package Razeor\Task
 */
final class TaskList implements \Iterator, \Countable
{
    public const DEFAULT_DIR = ROOT_PATH . '/tasks';

    public const TASK_INFO_FILENAME = 'task.json';

    /**
     * @var \ArrayIterator|null
     */
    private $listIterator;

    /**
     * @var TaskList
     */
    private static $instance;

    /**
     * @var string[]
     */
    private $list = [];

    /**
     * @var string A path that storage task files
     */
    private $storageDir;

    private function __construct()
    {
        # Set self::$storageDir according Config @{
        if ( Config::getInstance()->has( 'TaskDir' ) ) {
            $path = Config::getInstance()->get( 'TaskDir' );
            if ( !is_dir( $path ) ) {
                throw new \Exception( "$path is not standard directory" );
            }
            if ( !is_readable( $path ) ) {
                throw new \RuntimeException( "Failed to read the task dir: $path" );
            }
            $this->storageDir = $path;
        } else {
            $this->storageDir = self::DEFAULT_DIR;
        }
        # @}
        $this->readTasks();
    }

    public static function getInstance() : TaskList
    {
        if ( self::$instance === null ) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function rewind() : void
    {
        $this->listIterator->rewind();
    }

    public function current()
    {
        return $this->listIterator->current();
    }

    public function key()
    {
        return $this->listIterator->key();
    }

    public function next() : void
    {
        $this->listIterator->next();
    }

    public function valid() : bool
    {
        return $this->listIterator->valid();
    }

    public function count() : int
    {
        return count( $this->list );
    }

    public function listIsEmpty() : bool
    {
        return $this->count() === 0 ? true : false;
    }

    private function readTasks() : void
    {
        $this->list = [];
        $dir = new \DirectoryIterator( $this->storageDir );
        foreach ( $dir as $fileInfo ) {
            if ( !$fileInfo->isDot() && $fileInfo->isDir() ) {
                try {
                    $taskName = $fileInfo->getFilename();
                    $filePath = $dir->getRealPath() . '/' . self::TASK_INFO_FILENAME;
                    if ( !is_readable( $filePath ) ) {
                        throw new \RuntimeException( "Failed to read $filePath" );
                    }
                    $jsonParser = new JsonParser( file_get_contents( $filePath ) );
                    if ( $jsonParser->parse() ) {
                        $json = $jsonParser->getOutput();
                        $checker = new TaskInfoChecker( $json );
                        if ( $checker->check() ) {
                            if ( !is_readable( $mainFilePath = "{$dir->getRealPath()}/{$json['MainClass']}.php" ) ) {
                                throw new \RuntimeException( "Failed to read {$mainFilePath}" );
                            }
                            $this->list[$taskName] = [
                                'mainClass' => $json['MainClass'],
                                'time' => ( new \DateTime( $json['Time'] ) )->format( 'U' )
                            ];
                        } else {
                            throw new \RuntimeException( 'info file missing some required option' );
                        }
                    } else {
                        throw new \RuntimeException( "Failed to decode the json: {$jsonParser->getErrorMsg()}" );
                    }
                } catch ( \RuntimeException $e ) {
                    ShellOutput::println( "Ignore $taskName, reason: {$e->getMessage()}",
                        ShellOutput::YELLOW );
                }
            }
        }
        if ( $this->list === [] ) {
            $this->listIterator = null;
        } else {
            $this->listIterator = new \ArrayIterator( $this->list );
        }
    }

    /**
     * Refresh the list according to the files
     */
    public function sync() : void
    {
        $this->readTasks();
    }

    /**
     * Get the main file of a task
     * @param string $taskName
     * @return string
     */
    public function getMainClassFile(string $taskName) : string
    {
        if ( isset( $this->list[$taskName] ) ) {
            return "{$this->storageDir}/$taskName/{$this->list[$taskName]['mainClass']}.php";
        } else {
            throw new \RuntimeException( "Undefined task: $taskName" );
        }
    }

    public function getMainClass(string $taskName) : string
    {
        if ( isset( $this->list[$taskName] ) ) {
            return $this->list[$taskName]['mainClass'];
        } else {
            throw new \RuntimeException( "Undefined task: $taskName" );
        }
    }
}
