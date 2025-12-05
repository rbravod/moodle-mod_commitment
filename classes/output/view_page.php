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
 * Class view_page
 *
 * @package    mod_commitment
 * @copyright  2025 Roberto Bravo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class view_page implements renderable, templatable {
    /** @var cm_info */
    protected $cm;
    /** @var \stdClass commitment, module instance record */
    protected $commitment;
    /** @var array list of contract records (stdClass) */
    protected $contracts;

    /**
     * Constructor.
     *
     * @param cm_info $cm
     * @param \stdClass $commitment
     * @param array $contracts  Array of stdClass contract DB records
     */
    public function __construct(cm_info $cm, \stdClass $commitment, array $contracts) {
        $this->cm = $cm;
        $this->commitment = $commitment;
        $this->contracts = $contracts;
    }

    /**
     * Export data for mustache template.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        $contracts = [];
        foreach ($this->contracts as $contract) {
            // Build per-contract data for the template.
            $contracts[] = [
                'id' => (int)$contract->id,
                'title' => format_string($contract->title),
                'shortdescription' => format_text($contract->description ?? '', FORMAT_HTML, [
                    'trusted' => false, 'overflowdiv' => true]),
                'status' => (string)($contract->status ?? 'active'),
                'created' => userdate($contract->timecreated),
                'contracturl' => (new moodle_url('/mod/commitment/contract.php', [
                    'id' => $this->cm->id,
                    'contractid' => $contract->id,
                ]))->out(false),
            ];
        }

        return [
            'cmid' => $this->cm->id,
            'commitmentid' => $this->commitment->id,
            'createcontracturl' => (new moodle_url('/mod/commitment/new_contract.php', ['id' => $this->cm->id]))->out(false),
            'contracts' => $contracts,
            'hascontracts' => !empty($contracts),
        ];
    }
}
