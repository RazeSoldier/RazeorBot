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

namespace Razeor;

class JsonParser
{
    private $input;

    private $output;

    private $errorCode;

    private $errorMsg;

    public function __construct(string $json)
    {
        $this->input = $json;
    }

    public function parse() : bool
    {
        $arr = json_decode( $this->input, true );
        if ( $arr === null ) {
            $this->errorMsg = json_last_error_msg();
            $this->errorCode = json_last_error();
            return false;
        } else {
            $this->output = $arr;
            return true;
        }
    }

    public function getInput() : string
    {
        return $this->input;
    }

    /**
     * Return parse result
     * Must call after parse()
     * @return array|null
     */
    public function getOutput() :? array
    {
        return $this->output;
    }

    /**
     * @return int|null
     */
    public function getErrorCode() :? int
    {
        return $this->errorCode;
    }

    /**
     * @return string|null
     */
    public function getErrorMsg() :? string
    {
        return $this->errorMsg;
    }
}