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
 * Define all the backup steps that will be used by the backup_choice_activity_task
 *
 * @package    mod_glossaryfocus
 * @copyright  2021 Eticeo <https://eticeo.com>
 * @author     2021 Jeremy Carre <jeremy.carre@eticeo.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */

/**
 * Define the complete glossaryfocus structure for backup, with file and id annotations
 */
class backup_glossaryfocus_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {
        global $DB;

        $DB->insert_record('glossaryfocus_entries', array('idglossaryfocus' => 1, 'idglossaryentry' => 0));
        // Define each element separated.
        $glossaryfocus = new backup_nested_element('glossaryfocus', array('id'),
            array('name', 'intro', 'introformat', 'idglossarymaster', 'timecreated', 'timemodified'));

        $entries = new backup_nested_element('entries');

        $entry = new backup_nested_element('entry', array('id'), array('idglossaryentry'));

        // Build the tree.
        $glossaryfocus->add_child($entries);
        $entries->add_child($entry);

        // Define sources.
        $glossaryfocus->set_source_table('glossaryfocus', array('id' => backup::VAR_ACTIVITYID));

        $entry->set_source_table('glossaryfocus_entries', array('idglossaryfocus' => backup::VAR_PARENTID), 'id ASC');

        // Define file annotations?
        $glossaryfocus->annotate_files('mod_glossaryfocus', 'intro', null); // This file area hasn't itemid.

        // Return the root element (glossaryfocus), wrapped into standard activity structure.
        return $this->prepare_activity_structure($glossaryfocus);
    }
}
