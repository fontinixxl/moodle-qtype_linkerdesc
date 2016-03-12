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
 * Description 'question' definition class.
 *
 * @package    qtype
 * @subpackage linkerdesc
 * @copyright  2016 Gerard Cuello <gerard.urv@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Represents a linkerdesc 'question'.
 */
class qtype_linkerdesc_question extends question_information_item {

    /**
     * Start a new attempt at this question, storing any information that will
     * be needed later in the step.
     *
     * This is where the question can do any initialisation required on a
     * per-attempt basis. For example, this is where the multiple choice
     * question type randomly shuffles the choices (if that option is set).
     *
     * Any information about how the question has been set up for this attempt
     * should be stored in the $step, by calling $step->set_qt_var(...).
     *
     * @param question_attempt_step The first step of the {@link question_attempt}
     *      being started. Can be used to store state.
     * @param int $varant which variant of this question to start. Will be between
     *      1 and {@link get_num_variants()} inclusive.
     */
    public function start_attempt(\question_attempt_step $step, $variant) {
        // vars loaded by questiontype->initialize_question_instance()
        foreach ($this->vars as $var) {
            $values = programmedresp_get_random_value($var);
            if (!$values) {
                print_error('errordb', 'qtype_programmedresp');
            }
            $valuetodisplay = implode(', ', $values);
            str_replace('{$' . $var->varname . '}', $valuetodisplay, $this->questiontext, $count);
            // If $var->varname is found in questiontext ($count == true), then store it
            $count && $step->set_qt_var('_var_' . $var->id, $valuetodisplay);
        }
    }

    /**
     * When an in-progress {@link question_attempt} is re-loaded from the
     * database, this method is called so that the question can re-initialise
     * its internal state as needed by this attempt.
     *
     * For example, the multiple choice question type needs to set the order
     * of the choices to the order that was set up when start_attempt was called
     * originally. All the information required to do this should be in the
     * $step object, which is the first step of the question_attempt being loaded.
     *
     * @param question_attempt_step The first step of the {@link question_attempt}
     *      being loaded.
     */
    public function apply_attempt_state(\question_attempt_step $step) {
        global $DB;

        $attemptid = $this->get_attemptid_by_stepid($step->get_id());
        $this->usageid = $this->get_question_usageid($attemptid);

        // Retrive all vars initialized in start_attempt().
        foreach ($step->get_qt_data() as $name => $value) {
            if (substr($name, 0, 5) === '_var_') {
                $varid = substr($name, 5);
                $varname = $this->vars[$varid]->varname;
                $this->questiontext = str_replace('{$' . $varname . '}', $value, $this->questiontext);
                // Store vars (as array form) to be used later to get the correct response
                $this->varvalues[$varid] = explode(',', $value);

                if (!$values = $DB->get_field('qtype_programmedresp_val', 'varvalues',
                        array('attemptid' => $this->usageid, 'varid' => $varid))) {
                    // Add a new random value
                    $val = new stdClass();
                    $val->attemptid = $this->usageid;
                    $val->varid = $varid;
                    $val->varvalues = programmedresp_serialize($this->varvalues[$varid]);
                    if (!$DB->insert_record('qtype_programmedresp_val', $val)) {
                        print_error('errordb', 'qtype_programmedresp');
                    }
                }
            }
        }
    }

    // TODO: move to new helper class.
    /**
     * Get attemptid by step id
     * @param int $stepid unique identification for this step
     * @return int $attemptid
     */
    public function get_attemptid_by_stepid($stepid) {
        global $DB;

        if (!$attempid = $DB->get_field('question_attempt_steps', 'questionattemptid', array('id' => $stepid))) {
            //TODO : show custom message error
        }
        return $attempid;
    }

    public function get_question_usageid($attemptid) {
        global $DB;

        if (!$questionusage = $DB->get_field('question_attempts', 'questionusageid', array('id' => $attemptid))) {
            //TODO : show custom message error
        }
        return $questionusage;
    }

    // END TODO
}
