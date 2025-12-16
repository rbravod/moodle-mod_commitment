<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace mod_commitment\persistent;

use core\persistent;

/**
 * Class commitment
 *
 * @package    mod_commitment
 * @copyright  2025 Roberto Bravo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class commitment extends persistent {
    /** @var string */
    const TABLE = 'commitment';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties(): array {
        return [
            'course' => ['type' => PARAM_INT],
            'name' => ['type' => PARAM_TEXT],
            'intro' => ['type' => PARAM_RAW],
            'introformat' => ['type' => PARAM_INT, 'default' => 0],
            'timecreated' => ['type' => PARAM_INT, 'default' => 0],
            'timemodified' => ['type' => PARAM_INT, 'default' => 0],
        ];
    }

    /**
     * Gets a commitment based on its id.
     *
     * @param int $id
     * @return self|null
     */
    public static function get_by_id(int $id) {
        return self::get_record(['id' => $id]);
    }
}
