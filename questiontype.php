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
 * Question type class for the linkerdesc 'question' type.
 *
 * @package    qtype
 * @subpackage linkerdesc
 * @copyright  2016 Gerard Cuello <gerard.urv@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/type/programmedresp/questiontype.php');
require_once($CFG->dirroot . '/question/type/programmedresp/lib.php');

/**
 * This class use programmedresp/questiontype.php as a
 * base class to either get question options and save question vars.
 * TODO: translate it!
 * Té la única funció de vincualar la propia pregunta amb el questionari on
 * s'ha afegit.D'aquesta manera des del programmedresp podrem obtindre les variables
 * linker vinculades a un quiz en particular.
 */
class qtype_linkerdesc extends qtype_programmedresp {

    public function get_question_options($question) {
        global $DB;
        $question->options = new stdClass();
        $question->options->vars = $DB->get_records('qtype_programmedresp_var',
            array('question' => $question->id));
        $question->options->concatvars = $DB->get_records_select('qtype_programmedresp_conc',
            'question = ?', array($question->id));
    }
    protected function initialise_question_instance(\question_definition $question, $questiondata) {
        $question->id = $questiondata->id;
        $question->category = $questiondata->category;
        $question->contextid = $questiondata->contextid;
        $question->parent = $questiondata->parent;
        $question->qtype = $this;
        $question->name = $questiondata->name;
        $question->questiontext = $questiondata->questiontext;
        $question->questiontextformat = $questiondata->questiontextformat;
        $question->generalfeedback = $questiondata->generalfeedback;
        $question->generalfeedbackformat = $questiondata->generalfeedbackformat;
        $question->defaultmark = 0;
        $question->length = $questiondata->length;
        $question->stamp = $questiondata->stamp;
        $question->version = $questiondata->version;
        $question->hidden = $questiondata->hidden;
        $question->timecreated = $questiondata->timecreated;
        $question->timemodified = $questiondata->timemodified;
        $question->createdby = $questiondata->createdby;
        $question->modifiedby = $questiondata->modifiedby;

        $question->vars = ($questiondata->options->vars) ? $questiondata->options->vars : array();
    }

    public function save_question($question, $form) {
        // Make very sure that descriptions can'e be created with a grade of
        // anything other than 0.
        $form->defaultmark = 0;
        return parent::save_question($question, $form);
    }

    public function delete_question($questionid, $contextid) {
        return true;
    }

    public function actual_number_of_questions($question) {
        // Used for the feature number-of-questions-per-page
        // to determine the actual number of questions wrapped by this question.
        // The question type linkerdesc is not even a question
        // in itself so it will return ZERO!
        return 0;
    }

    public function get_random_guess_score($questiondata) {
        return null;
    }

    /**
     * @return null to tell the base class to do nothing
     */
    public function extra_question_fields() {
        return null;
    }

    public function is_real_question_type() {
        return false;
    }

    public function is_usable_by_random() {
        return false;
    }

    public function can_analyse_responses() {
        return false;
    }

}
