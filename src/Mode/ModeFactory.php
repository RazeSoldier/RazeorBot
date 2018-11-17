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

namespace Razeor\Mode;

use Razeor\{
    Config,
    IFactory,
    Logger,
    ShellOutput
};

class ModeFactory implements IFactory
{
    public const CONFIG_KEY = 'RunningMode';

    public function make() : IMode
    {
        if ( !Config::getInstance()->has( self::CONFIG_KEY ) ) {
            // Default using single-process mode
            return new SingleProcessMode();
        }
        $mode = Config::getInstance()->get( self::CONFIG_KEY );
        switch ( $mode ) {
            case 'single':
                $modeObj =  new SingleProcessMode();
                break;
            case 'multi':
                if ( extension_loaded( 'pcntl' ) ) {
                    $modeObj = new MultiProcessMode();
                    break;
                }
                ShellOutput::println( 'PCNTL PHP extension unavailable, so we using single-process mode instead',
                    ShellOutput::YELLOW );
                $modeObj = new SingleProcessMode();
                break;
            case 'one':
                $modeObj = new OneTaskMode();
                break;
            default:
                throw new \RuntimeException( "Unknown value: $mode" );
        }
        Logger::getInstance()->notice( "Using $mode mode" );
        return $modeObj;
    }
}
