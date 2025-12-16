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
 * Class commitment_exporter
 *
 * @package    mod_commitment
 * @copyright  2025 Roberto Bravo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class commitment_exporter extends exporter {
    #[\Override]
    protected static function define_properties(): array {
        return [
            'id' => ['type' => PARAM_INT],
            'course' => ['type' => PARAM_INT],
            'introformat' => ['type' => PARAM_INT],
            'timecreated' => ['type' => PARAM_INT],
            'timemodified' => ['type' => PARAM_INT],
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
            'commitmentname' => ['type' => PARAM_TEXT],
            'summary' => ['type' => PARAM_RAW, 'optional' => true],
            'cmid' => ['type' => PARAM_INT],
        ];
    }

    #[\Override]
    protected function get_other_values(\renderer_base $output): array {
        $context = $this->related['context'];

        return [
            'commitmentname' => format_string($this->data->name),
            'summary' => format_text($this->data->intro, $this->data->introformat, ['context' => $context]),
            'cmid' => $context->instanceid,
        ];
    }
}
