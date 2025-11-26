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
 * View Commitment Contract instance.
 *
 * @package    mod_commitment
 * @copyright  2025 Roberto Bravo <roberto.bravo@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

$id = required_param('id', PARAM_INT);
$cm = get_coursemodule_from_id('commitment', $id, 0, false, MUST_EXIST);
$instance = $DB->get_record('commitment', ['id' => $cm->instance], MUST_EXIST);

require_login($cm->course, true, $cm);
$context = context_module::instance($cm->id);

$PAGE->set_url('/mod/commitment/view.php', ['id' => $id]);
$PAGE->set_title(format_string($instance->name));
$PAGE->set_heading(get_string('modulename', 'commitment'));

// Render basic info
echo $OUTPUT->header();
echo $OUTPUT->box(format_text($instance->description));

// Report button (if user is participant)
if (has_capability('mod/commitment:report', $context)) {
    $reporturl = new \core\url('/mod/commitment/report.php', ['id'=>$id]);
    echo $OUTPUT->single_button($reporturl, get_string('reportnow', 'commitment'));
}

echo $OUTPUT->footer();
