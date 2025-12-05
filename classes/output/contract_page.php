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

namespace mod_commitment\output;

use cm_info;
use renderable;
use renderer_base;
use moodle_url;
use templatable;

/**
 * Class contract_page
 *
 * @package    mod_commitment
 * @copyright  2025 Roberto Bravo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class contract_page implements renderable, templatable {
    /** @var cm_info */
    protected $cm;
    /** @var \stdClass commitment, module instance record */
    protected $commitment;
    /** @var \stdClass */
    protected $contract;

    /**
     * Constructor.
     *
     * @param cm_info $cm
     * @param \stdClass $commitment
     * @param \stdClass $contract
     */
    public function __construct(cm_info $cm, \stdClass $commitment, \stdClass $contract) {
        $this->cm = $cm;
        $this->commitment = $commitment;
        $this->contract = $contract;
    }

    /**
     * Export data for mustache template.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        global $OUTPUT, $CFG;
        require_once($CFG->dirroot . '/user/lib.php');

        // Referee display.
        $referee = '';
        if (!empty($this->contract->referee)) {
            $refereeuser = \core_user::get_user($this->contract->referee);
            $course = get_course($this->cm->course);
            $referee = $OUTPUT->user_picture($refereeuser, [
                'includefullname' => true,
                'link' => user_can_view_profile($refereeuser, $course),
                'size' => 35,
            ]);
        }

        return [
            'cmid' => $this->cm->id,
            'title' => format_string($this->contract->title),
            'description' => format_text($this->contract->description
                ?? '', $this->contract->descriptionformat ?? FORMAT_HTML, ['trusted' => false, 'overflowdiv' => true]),
            'periodicity' => $this->contract->periodicity ?? 'once',
            'status' => $this->contract->status ?? 'active',
            'starttime' => !empty($this->contract->starttime) ? userdate($this->contract->starttime) : '',
            'endtime' => !empty($this->contract->endtime) ? userdate($this->contract->endtime) : '',
            'referee' => $referee,
            'created' => userdate($this->contract->timecreated),
            'reporturl' => (new moodle_url('/mod/commitment/report.php', [
                'cmid' => $this->cm->id,
                'contractid' => $this->contract->id,
            ]))->out(false),
        ];
    }
}
