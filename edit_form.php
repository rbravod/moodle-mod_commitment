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

require_once($CFG->libdir.'/formslib.php');

/**
 * Form for editing Commitment Contract instance.
 *
 * @package    mod_commitment
 * @copyright  2025 Roberto Bravo <roberto.bravo@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_commitment_mod_form extends moodleform {
    public function definition() {
        $mform = $this->_form;
        $mform->addElement('text', 'name', get_string('name', 'commitment'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addElement('editor', 'introeditor', get_string('description', 'commitment'));
        $mform->addElement('date_time_selector', 'starttime', get_string('starttime', 'commitment'));
        $mform->addElement('date_time_selector', 'endtime', get_string('endtime', 'commitment'));
        $choices = ['once' => get_string('once', 'commitment'), 'daily' => get_string('daily', 'commitment'), 'weekly' => get_string('weekly', 'commitment')];
        $mform->addElement('select', 'periodicity', get_string('periodicity', 'commitment'), $choices);
        $mform->addElement('select', 'referee', get_string('referee', 'commitment'),
            get_enrolled_users($this->page->coursecontext));
        $this->add_action_buttons();
    }
}
