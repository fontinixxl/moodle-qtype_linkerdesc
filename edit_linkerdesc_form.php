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
 * Defines the editing form for the linkerdesc question type.
 *
 * @package    qtype
 * @subpackage linkerdesc
 * @copyright  2016 Gerard Cuello <gerard.urv@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/question/type/programmedresp/lib.php');
require_once($CFG->dirroot . '/question/type/programmedresp/programmedresp_output.class.php');

/**
 * linkerdesc editing form definition.
 *
 */
class qtype_linkerdesc_edit_form extends question_edit_form {

    private $concatvars;
    /**
     * Add question-type specific form fields.
     *
     * @param MoodleQuickForm $mform the form being built.
     */
    protected function definition_inner($mform) {
        global $PAGE, $CFG;
        // We don't need those default element.
        $mform->removeElement('defaultmark');
        $mform->addElement('hidden', 'defaultmark', 0);
        $mform->setType('defaultmark', PARAM_RAW);

//        $mform->removeElement('generalfeedback');
//        $mform->addElement('hidden', 'generalfeedback');
//        $mform->setType('generalfeedback', PARAM_RAW);

        $PAGE->requires->js('/question/type/programmedresp/script.js');

        // Adding wwwroot required by script.js
        echo "<script type=\"text/javascript\">//<![CDATA[\n" .
        "this.wwwroot = '" . $CFG->wwwroot . "';\n" .
        "//]]></script>\n";

        // TODO: Refacor
        // context id will be required on contents.php once it will called by AJAX (script.js)
        $mform->addElement('hidden', 'contextid', $PAGE->context->id);
        $mform->setType('contextid', PARAM_INT);

        $outputmanager = new prgrammedresp_output($mform);
        $editingjsparam = 'false';
        // In a new question the vars div should be loaded
        if (!empty($this->question->id)) {
            $editingjsparam = 'true';
        }

        // Button label
        if (!empty($this->question->id)) {
            $buttonlabel = get_string('refreshvarsvalues', 'qtype_programmedresp');
        } else {
            $buttonlabel = get_string('assignvarsvalues', 'qtype_programmedresp');
        }

        $varsattrs = array('onclick' => 'display_vars(this, ' . $editingjsparam . ');');
        $mform->addElement('button', 'vars', $buttonlabel, $varsattrs);

        // Link to fill vars data
        $mform->addElement('header', 'varsheader', get_string("varsvalues", "qtype_programmedresp"));

        $mform->addElement('html', '<div id="id_vars_content">');
        if (!empty($this->question->id)) {
            $outputmanager->display_vars($this->question->questiontext,
                    false, $this->question->options->concatvars);
        }
        $mform->addElement('html', '</div>');

        // TODO: review this requires. Maybe is not longer nedeed.
        if (empty($this->question->id)) {
            // Add the onload javascript to hide next steps
            $PAGE->requires->js('/question/type/programmedresp/onload.js');
        }
    }

    protected function data_preprocessing($question) {
        
        if (!empty($question->id)) {
            $vars = programmedresp_preprocess_vars($question->options->vars);
            $question = (object) array_merge((array) $question, (array) $vars);
        }
        
        return $question;
    }

    public function qtype() {
        return 'linkerdesc';
    }

}
