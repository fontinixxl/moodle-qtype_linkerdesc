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
 * Unit tests for the linkerdescription question type class.
 *
 * @package    qtype_linkerdescription
 * @copyright  2013 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/linkerdescription/questiontype.php');
require_once($CFG->dirroot . '/question/engine/tests/helpers.php');
require_once($CFG->dirroot . '/question/type/edit_question_form.php');
require_once($CFG->dirroot . '/question/type/linkerdescription/edit_linkerdescription_form.php');


/**
 * Unit tests for the linkerdescription question type class.
 *
 * @copyright  2013 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_linkerdescription_test extends advanced_testcase {
    protected $qtype;

    protected function setUp() {
        $this->qtype = new qtype_linkerdescription();
    }

    protected function tearDown() {
        $this->qtype = null;
    }

    public function test_name() {
        $this->assertEquals($this->qtype->name(), 'linkerdescription');
    }

    public function test_actual_number_of_questions() {
        $this->assertEquals(0, $this->qtype->actual_number_of_questions(null));
    }

    public function test_can_analyse_responses() {
        $this->assertFalse($this->qtype->can_analyse_responses());
    }

    public function test_get_random_guess_score() {
        $this->assertNull($this->qtype->get_random_guess_score(null));
    }

    public function test_get_possible_responses() {
        $this->assertEquals(array(), $this->qtype->get_possible_responses(null));
    }


    public function test_question_saving() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $questiondata = test_question_maker::get_question_data('linkerdescription');
        $formdata = test_question_maker::get_question_form_data('linkerdescription');

        $generator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $generator->create_question_category(array());

        $formdata->category = "{$cat->id},{$cat->contextid}";
        qtype_linkerdescription_edit_form::mock_submit((array)$formdata);

        $form = qtype_linkerdescription_test_helper::get_question_editing_form($cat, $questiondata);

        $this->assertTrue($form->is_validated());

        $fromform = $form->get_data();

        $returnedfromsave = $this->qtype->save_question($questiondata, $fromform);
        $actualquestionsdata = question_load_questions(array($returnedfromsave->id));
        $actualquestiondata = end($actualquestionsdata);

        foreach ($questiondata as $property => $value) {
            if (!in_array($property, array('id', 'version', 'timemodified', 'timecreated'))) {
                $this->assertAttributeEquals($value, $property, $actualquestiondata);
            }
        }
    }
}
