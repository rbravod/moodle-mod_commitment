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
$contractid = required_param('contractid', PARAM_INT);

[$course, $cm] = get_course_and_cm_from_cmid($id, 'commitment');

require_course_login($cm->course, true, $cm);
$context = context_module::instance($cm->id);

$PAGE->set_url('/mod/commitment/contract.php', ['id' => $id, 'contractid' => $contractid]);
$PAGE->set_title($PAGE->set_title($course->shortname . ': ' . $PAGE->activityrecord->name));
$PAGE->set_heading(get_string('modulename', 'commitment'));

/** @var core_renderer $output */
$output = $PAGE->get_renderer('mod_commitment');
$outputpage = new \mod_commitment\output\contract_page($cm, $contractid);
$data = $outputpage->export_for_template($output);

$context = \context_module::instance($id);
$PAGE->set_context($context);

echo $output->header();
echo $output->render_from_template('mod_commitment/contract_page', $data);
echo $output->footer();
