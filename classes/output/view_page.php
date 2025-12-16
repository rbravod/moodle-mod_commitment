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
use mod_commitment\external\commitment_with_contracts_exporter;
use renderable;
use renderer_base;
use templatable;
use mod_commitment\manager;
use mod_commitment\external\commitment_exporter;

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
    /** @var \mod_commitment\persistent\commitment */
    protected $commitment;
    /** @var array list of user contract records (\mod_commitment\persistent\contract[]) */
    protected $usercontracts;

    /**
     * Constructor.
     *
     * @param cm_info $cm
     */
    public function __construct(cm_info $cm) {
        $this->cm = $cm;

        $manager = manager::instance();
        $commitment = $manager->get_commitment($cm->instance);
        $usercontracts = $manager->get_user_contracts($commitment);

        $this->commitment = $commitment;
        $this->usercontracts = $usercontracts;
    }

    #[\Override]
    public function export_for_template(renderer_base $output): \stdClass {
        // Persistent objects to stdClass records.
        $usercontracts = array_map(fn($c) => $c->to_record(), $this->usercontracts);
        $commitment = $this->commitment->to_record();
        $exporter = new commitment_with_contracts_exporter(
            $commitment,
            [
                'usercontracts' => $usercontracts,
                'context' => $this->cm->context,
            ],
        );

        return $exporter->export($output);
    }
}
