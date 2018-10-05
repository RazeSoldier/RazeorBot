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

use PhpParser\Node\Expr\ConstFetch;
use Razeor\Config;
use Razeor\ShellOutput;

/**
 * Used to storage all valid tasks
 * The instance is managed by TaskManager
 * @package Razeor\Task
 */
final class TaskList
{
    public const DEFAULT_DIR = ROOT_PATH . '/tasks';

    public const TASK_INFO_FILENAME = 'task.json';

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

    private function readTasks() : void
    {
        $dir = new \DirectoryIterator( $this->storageDir );
        foreach ( $dir as $fileInfo ) {
            if ( !$fileInfo->isDot() && $fileInfo->isDir() ) {
                try {
                    $checker = new TaskInfoFileChecker( $dir->getRealPath() . '/' . self::TASK_INFO_FILENAME );
                    if ( $checker->check() ) {

                    }
                } catch ( \RuntimeException $e ) {
                    ShellOutput::println( "Ignore {$fileInfo->getFilename()}, reason: {$e->getMessage()}",
                        ShellOutput::YELLOW );
                }
            }
        }
    }

    /**
     * Refresh the list according to the files
     */
    public function sync() : void
    {
        $this->readTasks();
    }
}