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

namespace mod_commitment\external;

use core\external\exporter;

/**
 * Class contract_exporter
 *
 * @package    mod_commitment
 * @copyright  2025 Roberto Bravo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class contract_exporter extends exporter {
    #[\Override]
    protected static function define_properties(): array {
        return [
            'id' => ['type' => PARAM_INT],
            'status' => ['type' => PARAM_TEXT],
            'timecreated' => ['type' => PARAM_INT],
            'timemodified' => ['type' => PARAM_INT],
            'periodicity' => ['type' => PARAM_TEXT],
        ];
    }

    #[\Override]
    protected static function define_related() {
        return [
            'context' => 'context',
        ];
    }

    #[\Override]
    protected static function define_other_properties(): array {
        return [
            'cmid' => ['type' => PARAM_INT],
            'contractname' => ['type' => PARAM_TEXT],
            'summary' => ['type' => PARAM_RAW, 'optional' => true],
            'refereedata' => ['type' => PARAM_RAW],
            'start' => ['type' => PARAM_TEXT],
            'end' => ['type' => PARAM_TEXT],
            'contracturl' => ['type' => PARAM_URL],
            'created' => ['type' => PARAM_TEXT],
            // 'reporturl' => ['type' => PARAM_URL],
        ];
    }

    #[\Override]
    protected function get_other_values(\renderer_base $output): array {
        $context = $this->related['context'];
        $course = $output->get_page()->course;
        /** @var \core_renderer $renderer */
        $renderer = $output->get_page()->get_renderer('mod_commitment');

        // Display referee detail.
        $refereeuser = \core_user::get_user($this->data->referee);
        $refereedata = $renderer->user_picture($refereeuser, [
            'includefullname' => true,
            'link' => has_capability('moodle/user:viewdetails', \context_course::instance($course->id)),
            'size' => 35,
        ]);

        return [
            'cmid' => (int)$context->instanceid,
            'contractname' => format_string($this->data->title),
            'summary' => format_text($this->data->description, FORMAT_MOODLE, ['context' => $context]),
            'refereedata' => $refereedata,
            'start' => userdate($this->data->starttime ?? 0),
            'end' => userdate($this->data->endtime ?? 0),
            'contracturl' => (new \moodle_url('/mod/commitment/contract.php', [
                'id' => (int)$context->instanceid,
                'contractid' => (int)$this->data->id,
            ]))->out(false),
            'created' => userdate($this->data->timecreated),
            // 'reporturl' => '',
        ];
    }
}
