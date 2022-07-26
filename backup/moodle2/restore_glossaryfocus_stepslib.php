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
        $userinfo = $this->get_setting_value('userinfo');

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

        // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
        // See MDL-9367.
        $data->assesstimestart = $this->apply_date_offset($data->assesstimestart);
        $data->assesstimefinish = $this->apply_date_offset($data->assesstimefinish);
        if ($data->scale < 0) { // Scale found, get mapping.
            $data->scale = -($this->get_mappingid('scale', abs($data->scale)));
        }
        $formats = get_list_of_plugins('mod/glossaryfocus/formats'); // Check format.
        if (!in_array($data->displayformat, $formats)) {
            $data->displayformat = 'dictionary';
        }
        if (!empty($data->mainglossaryfocus) and $data->mainglossaryfocus == 1 and
            $DB->record_exists('glossaryfocus', array('mainglossaryfocus' => 1, 'course' => $this->get_courseid()))) {
            // Only allow one main glossaryfocus in the course.
            $data->mainglossaryfocus = 0;
        }

        // insert the glossaryfocus record
        $newitemid = $DB->insert_record('glossaryfocus', $data);
        $this->apply_activity_instance($newitemid);
    }

    protected function process_glossaryfocus_entry($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->glossaryfocusid = $this->get_new_parentid('glossaryfocus');
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->sourceglossaryfocusid = $this->get_mappingid('glossaryfocus', $data->sourceglossaryfocusid);

        // Insert the entry record.
        $newitemid = $DB->insert_record('glossaryfocus_entries', $data);
        $this->set_mapping('glossaryfocus_entry', $oldid, $newitemid, true); // Childs and files by itemname.
    }

    protected function process_glossaryfocus_alias($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->entryid = $this->get_new_parentid('glossaryfocus_entry');
        $data->alias = $data->alias_text;
        $newitemid = $DB->insert_record('glossaryfocus_alias', $data);
    }

    protected function process_glossaryfocus_rating($data) {
        global $DB;

        $data = (object)$data;

        // Cannot use ratings API, cause, it's missing the ability to specify times (modified/created).
        $data->contextid = $this->task->get_contextid();
        $data->itemid = $this->get_new_parentid('glossaryfocus_entry');
        if ($data->scaleid < 0) { // scale found, get mapping
            $data->scaleid = -($this->get_mappingid('scale', abs($data->scaleid)));
        }
        $data->rating = $data->value;
        $data->userid = $this->get_mappingid('user', $data->userid);

        // Make sure that we have both component and ratingarea set. These were added in 2.1.
        // Prior to that all ratings were for entries so we know what to set them too.
        if (empty($data->component)) {
            $data->component = 'mod_glossaryfocus';
        }
        if (empty($data->ratingarea)) {
            $data->ratingarea = 'entry';
        }

        $newitemid = $DB->insert_record('rating', $data);
    }

    protected function process_glossaryfocus_entry_tag($data) {
        $data = (object)$data;

        if (!core_tag_tag::is_enabled('mod_glossaryfocus', 'glossaryfocus_entries')) {
            // Tags disabled in server, nothing to process.
            return;
        }

        $tag = $data->rawname;
        if (!$itemid = $this->get_mappingid('glossaryfocus_entry', $data->itemid)) {
            // Some orphaned tag, we could not find the glossaryfocus entry for it - ignore.
            return;
        }

        $context = context_module::instance($this->task->get_moduleid());
        core_tag_tag::add_item_tag('mod_glossaryfocus', 'glossaryfocus_entries', $itemid, $context, $tag);
    }
}