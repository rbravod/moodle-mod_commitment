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

use renderer_base;

/**
 * Class commitment_with_contracts_exporter
 *
 * @package    mod_commitment
 * @copyright  2025 Roberto Bravo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class commitment_with_contracts_exporter extends commitment_exporter {
    #[\Override]
    protected static function define_related() {
        return [
            ...parent::define_related(),
            'usercontracts' => 'stdClass[]',
        ];
    }

    #[\Override]
    protected static function define_other_properties(): array {
        return [
            ...parent::define_other_properties(),
            'hascontracts' => ['type' => PARAM_BOOL],
            'contracts' => ['type' => PARAM_RAW], // Will be an array of exported contracts.
        ];
    }

    #[\Override]
    protected function get_other_values(renderer_base $output): array {
        $othervalues = parent::get_other_values($output);

        $context = $this->related['context'];
        $contracts = $this->related['usercontracts'];

        $exportedcontracts = [];

        if (is_iterable($contracts)) {
            foreach ($contracts as $contract) {
                $ce = new contract_exporter($contract, ['context' => $context]);
                $exportedcontracts[] = $ce->export($output);
            }
        }

        $othervalues['hascontracts'] = !empty($exportedcontracts);
        $othervalues['contracts'] = $exportedcontracts;

        return $othervalues;
    }
}
