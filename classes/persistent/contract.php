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
 * Class contract
 *
 * @package    mod_commitment
 * @copyright  2025 Roberto Bravo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class contract extends persistent{
    /** @var string */
    const TABLE = 'commitment_contract';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties(): array {
        return [
            'commitment' => ['type' => PARAM_INT],
            'userid' => ['type' => PARAM_INT],
            'title' => ['type' => PARAM_TEXT],
            'description' => ['type' => PARAM_RAW],
            'starttime' => ['type' => PARAM_INT, 'default' => 0],
            'endtime' => ['type' => PARAM_INT, 'default' => 0],
            'periodicity' => ['type' => PARAM_TEXT],
            'visibility' => ['type' => PARAM_INT, 'default' => 1],
            'referee' => ['type' => PARAM_INT, 'default' => 0],
            'status' => ['type' => PARAM_TEXT, 'default' => 'active'],
            'timecreated' => ['type' => PARAM_INT, 'default' => 0],
            'timemodified' => ['type' => PARAM_INT, 'default' => 0],
        ];
    }

    /**
     * Create contract from record.
     *
     * @param \stdClass $record
     * @return self
     */
    public static function create_from_record(\stdClass $record): self {
        return new self(0, $record);
    }

    /**
     * Gets a contract based on its id.
     *
     * @param int $id Context ID
     * @return self|null
     */
    public static function get_by_id(int $id): ?self {
        return self::get_record(['id' => $id]);
    }

    /**
     * Get all contracts for a user in a commitment.
     *
     * @param int $commitmentid
     * @param int $userid
     * @return array[\stdClass]
     */
    public static function get_user_contracts_in_commitment(int $commitmentid, int $userid): array {
        return self::get_records(['commitment' => $commitmentid, 'userid' => $userid]);
    }
}
