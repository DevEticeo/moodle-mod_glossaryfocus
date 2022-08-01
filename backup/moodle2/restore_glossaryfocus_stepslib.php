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
 * Define all the restore steps that will be used by the restore_glossaryfocus_activity_task
 *
 * @package    mod_glossaryfocus
 * @copyright  2021 Eticeo <https://eticeo.com>
 * @author     2021 Jeremy Carre <jeremy.carre@eticeo.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */

/**
 * Structure step to restore one glossaryfocus activity
 */
class restore_glossaryfocus_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {
        $paths = array();
        $paths[] = new restore_path_element('glossaryfocus', '/activity/glossaryfocus');
        $paths[] = new restore_path_element('glossaryfocus_entry', '/activity/glossaryfocus/entries/entry');

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    protected function process_glossaryfocus($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // Insert the glossaryfocus record.
        $newitemid = $DB->insert_record('glossaryfocus', $data);

        // Immediately after inserting record call this.
        $this->apply_activity_instance($newitemid);
    }

    protected function process_glossaryfocus_entry($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->idglossaryfocus = $this->get_new_parentid('glossaryfocus');

        // Insert the entry record.
        $newitemid = $DB->insert_record('glossaryfocus_entries', $data);
        $this->set_mapping('glossaryfocus_entry', $oldid, $newitemid, true); // Childs and files by itemname.
    }

    /**
     * Called immediately after all the other restore functions.
     */
    protected function after_execute() {
        parent::after_execute();

        // Add the files.
        $this->add_related_files('mod_glossaryfocus', 'intro', null);

        // Add entries related files, matching by itemname (glossary_entry).
         $this->add_related_files('mod_glossaryfocus', 'entry', 'glossaryfocus_entry');
    }
}
