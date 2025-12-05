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

/**
 * Commitment Contract plugin file.
 *
 * @package    mod_commitment
 * @copyright  2025 Roberto Bravo <roberto.bravo@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Checks if certificate activity supports a specific feature.
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function commitment_supports(string $feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_MOD_PURPOSE:
            return MOD_PURPOSE_ASSESSMENT;
        default:
            return null;
    }
}

/**
 * Add commitment instance.
 *
 * @param stdClass $data
 * @param mod_commitment_mod_form|null $mform
 * @return int The instance id of the new commitment
 */
function commitment_add_instance(stdClass $data, ?mod_commitment_mod_form $mform): int {
    global $DB;

    $data->timecreated = time();

    $data->id = $DB->insert_record('commitment', $data);

    return $data->id;
}

/**
 * Update commitment instance.
 *
 * @param stdClass $data
 * @param mod_commitment_mod_form|null $mform
 * @return bool true
 */
function commitment_update_instance(stdClass $data, ?mod_commitment_mod_form $mform) {
    global $DB;

    $data->timemodified = time();
    $data->id = $data->instance;

    return $DB->update_record('commitment', $data);
}

/**
 * Delete commitment instance.
 *
 * @param int $id
 * @return bool success
 */
function commitment_delete_instance(int $id): bool {
    global $DB;

    if (!$DB->record_exists('commitment', ['id' => $id])) {
        return false;
    }

    $DB->delete_records('commitment', ['id' => $id]);
    // TODO: Delete related records (e.g., commitments, logs, etc.)

    return true;
}
