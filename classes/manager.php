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

namespace mod_commitment;

use context_module;
use mod_commitment\persistent\actionlog;
use mod_commitment\persistent\commitment;
use mod_commitment\persistent\contract;
use mod_commitment\persistent\period;
use mod_commitment\persistent\report;
use mod_commitment\persistent\validation;

/**
 * Commitment Contract management class.
 *
 * @package    mod_commitment
 * @copyright  2025 Roberto Bravo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {
    /** @var manager */
    private static $instance;

    /**
     * Protected constructor: use manager::instance() to obtain the singleton.
     * The constructor intentionally does not initialise mutable state.
     */
    protected function __construct() {
        // No mutable state should be initialised here.
    }

    /**
     * Singleton instance
     */
    public static function instance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get a commitment contract persistent by id.
     *
     * @param int $id commitment id
     * @return commitment|null
     */
    public function get_commitment(int $id): ?commitment {
        static $cache = [];

        if (\array_key_exists($id, $cache)) {
            return $cache[$id];
        }

        $commitment = commitment::get_record(['id' => $id]);

        if (!$commitment) {
            return null;
        }

        $cache[$id] = $commitment;
        return $commitment;
    }

    /**
     * Get a contract persistent by id.
     *
     * @param int $commitmentid commitment id
     * @param int $contractid
     * @return contract|null
     */
    public function get_contract(int $commitmentid, int $contractid): ?contract {
        static $cache = [];

        if (\array_key_exists($contractid, $cache)) {
            return $cache[$contractid];
        }

        $contract = contract::get_record(['id' => $contractid, 'commitment' => $commitmentid]);

        if (!$contract) {
            return null;
        }

        $cache[$contractid] = $contract;
        return $contract;
    }

    /**
     * Get all user contracts for a given commitment.
     *
     * @param commitment $commitment
     * @return array[contract]
     */
    public function get_user_contracts(commitment $commitment): array {
        global $USER;
        static $cache = [];

        $id = $commitment->get('id');

        $idx = "{$id}:{$USER->id}";
        if (\array_key_exists($idx, $cache)) {
            return $cache[$id];
        }

        $contracts = contract::get_user_contracts_in_commitment($id, $USER->id) ?: [];

        $cache[$idx] = $contracts;
        return $contracts;
    }

    /**
     * Create a new contract (returns contract persistent)
     */
    public function create_contract(array $data): contract {
        global $DB;
        $transaction = $DB->start_delegated_transaction();
        $now = time();
        $record = [
            'commitmentid' => $data['commitmentid'],
            'userid' => $data['userid'],
            'title' => $data['title'],
            'description' => $data['description'] ?? '',
            'starttime' => $data['starttime'] ?? $now,
            'endtime' => $data['endtime'] ?? 0,
            'periodicity' => $data['periodicity'] ?? 'weekly',
            'visibility' => $data['visibility'] ?? 1,
            'referee' => $data['referee'] ?? 0,
            'status' => 'active',
            'timecreated' => $now,
            'timemodified' => $now,
        ];
        $c = new contract(0, $record);
        // create initial periods
        $this->generate_periods_for_contract($c);
        $transaction->allow_commit();
        return $c;
    }

    /**
     * Generate reporting periods for a contract
     * (simple example, adapt periodicity parsing)
     */
    public function generate_periods_for_contract(contract $contract): array {
        $periods = [];
        // very simple weekly generator until endtime or 4 periods
        $start = (int)$contract->get('starttime') ?: time();
        $end = (int)$contract->get('endtime');
        $periodcount = 4;
        $length = 7 * 24 * 3600; // weekly default
        for ($i = 0; $i < $periodcount; $i++) {
            $ps = $start + ($i * $length);
            $pe = $ps + $length - 1;
            if ($end && $ps > $end) {
                break;
            }
            $record = [
                'contractid' => $contract->get('id'),
                'periodstart' => $ps,
                'periodend' => $pe,
                'status' => 'open',
                'timecreated' => time(),
                'timemodified' => time(),
            ];
            $periods[] = new period(0, $record);
        }
        return $periods;
    }

    /**
     * Submit a report (controller will handle file storage; we store filename/reference)
     */
    public function submit_report(int $periodid, int $userid, string $text, string $evidenceref = ''): report {
        global $DB;
        $transaction = $DB->start_delegated_transaction();
        $now = time();
        $r = new report(0, [
            'periodid' => $periodid,
            'userid' => $userid,
            'text' => $text,
            'evidencefile' => $evidenceref,
            'reporttime' => $now,
            'status' => 'submitted',
            'timecreated' => $now,
            'timemodified' => $now,
        ]);
        // log action
        actionlog::log($r->get('periodid'), 'report_submitted', ['reportid' => $r->get('id'), 'userid' => $userid]);
        $transaction->allow_commit();
        return $r;
    }

    /**
     * Validate a report: only callable by validator (caller must check capability)
     */
    public function validate_report(int $reportid, int $validatorid, string $status, string $comment = ''): validation {
        global $DB;
        $transaction = $DB->start_delegated_transaction();
        $now = time();
        $v = new validation(0, [
            'reportid' => $reportid,
            'validatorid' => $validatorid,
            'status' => $status,
            'comment' => $comment,
            'timecreated' => $now,
        ]);
        // update report status
        $rep = report::get_record(['id' => $reportid]);
        if ($rep) {
            $rep->set('status', $status === 'approved' ? 'approved' : 'rejected');
            $rep->save();
        }
        // log
        actionlog::log($rep ? $rep->get('periodid') : 0, 'report_validated', ['reportid' => $reportid, 'validator' => $validatorid, 'status' => $status]);
        $transaction->allow_commit();
        return $v;
    }

    /**
     * Finalize due periods: called from cron / task
     */
    public function finalize_due_periods(): void {
        global $DB;
        $now = time();
        $openperiods = period::get_open_periods_due($now);
        foreach ($openperiods as $p) {
            // check if a report exists
            $rep = report::get_record(['periodid' => $p->get('id')]);
            if (!$rep) {
                $p->set('status', 'no_report');
                $p->save();
                actionlog::log($p->get('contractid'), 'period_no_report', ['periodid' => $p->get('id')]);
                // optionally notify user/referee
            } else {
                if ($rep->get('status') === 'approved') {
                    $p->set('status', 'finalized');
                } else {
                    $p->set('status', 'awaiting_validation');
                }
                $p->save();
            }
        }
    }

    /**
     * Generic log helper
     */
    public function log_action(int $contractid, string $action, $payload = null): actionlog {
        return actionlog::log($contractid, $action, $payload);
    }

    /**
     * Send a message using Moodle messaging API
     */
    public function notify_user(int $userid, string $subject, string $body): void {
        $eventdata = new \core\message\message();
        $eventdata->component = 'mod_commitment';
        $eventdata->name = 'notification';
        $eventdata->userfrom = \core_user::get_support_user();
        $eventdata->userto = \core_user::get_user($userid);
        $eventdata->subject = $subject;
        $eventdata->fullmessage = $body;
        $eventdata->fullmessageformat = FORMAT_MARKDOWN;
        $eventdata->smallmessage = $subject;
        message_send($eventdata);
    }
}
