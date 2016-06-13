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
 * @package    moodlecore
 * @subpackage backup-moodle2
 * @copyright  2016 onwards Gerard Cuello {gerard.urv@gmail.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

/**
 * Restore plugin class that provides the necessary information
 * needed to restore one linker qtype plugin
 *
 * @copyright  2016 onwards Gerard Cuello {gerard.urv@gmail.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_qtype_linkerdesc_plugin extends restore_qtype_plugin {

    /**
     * Returns the paths to be handled by the plugin at question level
     */
    protected function define_question_plugin_structure() {

        $paths = array();

        // Add own qtype stuff.
        $elename = 'var';
        $elepath = $this->get_pathfor('/vars/var');
        $paths[] = new restore_path_element($elename, $elepath);

        $elename = 'concatvar';
        $elepath = $this->get_pathfor('/concatvars/concatvar');
        $paths[] = new restore_path_element($elename, $elepath);

        return $paths; // And we return the interesting paths.
    }

    /**
     * Process the qtype/programmedresp_var element
     */
    public function process_var($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // Detect if the question is created or mapped.
        $oldquestionid   = $this->get_old_parentid('question');
        $newquestionid   = $this->get_new_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ?
            true : false;

        // Find out whether this var has yet inserted.
        // Si la variable ha sigut restaurada previament per una pregunta
        // programada (xq l'utilitzava com a argument) Hem d'actualitzar el questionid amb
        // el nou d'aquesta pregunta.
        if ($newvarid = $this->get_mappingid('var', $oldid)){
            $var = new stdClass();
            $var->id = $newvarid;
            $var->question = $newquestionid;
            $DB->update_record('qtype_programmedresp_var', $var);

            return true;
        }
        // If the question has been created by restore, we need to create its
        // vars too.
        if ($questioncreated) {
            // Adjust some columns.
            $data->question = $newquestionid;
            // Insert record.
            // TODO: Ensure there aren't vars with the same name
            $newitemid = $DB->insert_record('qtype_programmedresp_var', $data);

            // Mapping the old var id with the new inserted one.
            // It might will be used later by qtype_programmedresp to get the correct var id for its arg.
            $this->set_mapping('var', $oldid, $newitemid);
        }

    }

    public function process_concatvar($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // Detect if the question is created or mapped.
        $oldquestionid   = $this->get_old_parentid('question');
        $newquestionid   = $this->get_new_parentid('question');
        $questioncreated = $this->get_mappingid('question_created', $oldquestionid) ?
            true : false;

        // The same as  process_var
        if ($newconcatid = $this->get_mappingid('concatvar', $oldid)){
            $concatvar = new stdClass();
            $concatvar->id = $newconcatid;
            $concatvar->question = $newquestionid;
            $DB->update_record('qtype_programmedresp_conc', $concatvar);

            return true;
        }

        // If the question has been created by restore, we need to create its
        // concatenated vars too.
        if ($questioncreated) {
            // Adjust some columns.
            $data->question = $newquestionid;
            // Insert record.
            $newitemid = $DB->insert_record('qtype_programmedresp_conc', $data);

            // Mapping the old var id with the new inserted one.
            // It might will be used later by qtype_programmedresp to get the correct var id for its arg.
            $this->set_mapping('concatvar', $oldid, $newitemid);
        }
    }
}
