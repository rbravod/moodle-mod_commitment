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
use mod_commitment\manager;
use mod_commitment\external\contract_exporter;

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
    /** @var \mod_commitment\persistent\commitment, module instance record */
    protected $commitment;
    /** @var \mod_commitment\persistent\contract */
    protected $contract;

    /**
     * Constructor.
     *
     * @param cm_info $cm
     * @param int $contractid
     */
    public function __construct(cm_info $cm, int $contractid) {
        $this->cm = $cm;

        $manager = manager::instance();
        $this->commitment = $manager->get_commitment($cm->instance);
        $this->contract = $manager->get_contract($cm->instance, $contractid);
    }

    #[\Override]
    public function export_for_template(renderer_base $output): \stdClass {
        $exporter = new contract_exporter(
            $this->contract->to_record(),
            ['context' => $this->cm->context],
        );

        return $exporter->export($output);
    }
}
