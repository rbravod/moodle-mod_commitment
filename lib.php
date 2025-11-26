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

defined('MOODLE_INTERNAL') || die();

function commitment_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_INTRO: return true;
        default: return null;
    }
}

/**
 * Add commitment instance.
 *
 * @param stdClass $data
 * @param stdClass $mform
 * @return int The instance id of the new commitment
 */
function commitment_add_instance($data, $mform) {
    global $DB;
    $record = new stdClass();
    $record->courseid = $data->course;
    $record->userid = $data->userid;
    $record->name = $data->name;
    $record->description = $data->intro ?? '';
    $record->starttime = isset($data->starttime) ? $data->starttime : 0;
    $record->endtime = isset($data->endtime) ? $data->endtime : 0;
    $record->periodicity = $data->periodicity ?? 'once';
    $record->visibility = $data->visibility ?? 1;
    $record->referee = $data->referee ?? 0;
    $record->status = 'active';
    $record->timecreated = time();
    $record->timemodified = time();

    $id = $DB->insert_record('commitment', $record);
    return $id;
}

/**
 * Update commitment instance.
 *
 * @param stdClass $data
 * @param stdClass $mform
 * @return bool true
 */
function commitment_update_instance($data, $mform) {
    global $DB;
    $record = $DB->get_record('commitment', ['id' => $data->instance]);
    if (!$record) return false;
    $record->name = $data->name;
    $record->description = $data->intro ?? '';
    $record->starttime = $data->starttime ?? 0;
    $record->endtime = $data->endtime ?? 0;
    $record->periodicity = $data->periodicity ?? 'once';
    $record->visibility = $data->visibility ?? 1;
    $record->referee = $data->referee ?? 0;
    $record->timemodified = time();
    return $DB->update_record('commitment', $record);
}

/**
 * Delete commitment instance.
 *
 * @param int $id
 * @return bool success
 */
function commitment_delete_instance($id) {
    global $DB;
    $DB->delete_records('commitment', ['id' => $id]);
    // TODO: Delete related records (e.g., commitments, logs, etc.)
    return true;
}
