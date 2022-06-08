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
 * Library of interface functions and constants for module glossaryfocus
 *
 * @package    mod_glossaryfocus
 * @copyright  2021 Eticeo <https://eticeo.com>
 * @author     2021 Jeremy Carre <jeremy.carre@eticeo.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

/**
 * Add glossaryfocus instance.
 *
 * @param stdClass $data
 * @param mod_glossaryfocus_mod_form $mform
 * @return int new glossaryfocus instance id
 */
function glossaryfocus_add_instance($data, $mform) {
    global $DB;

    $data->timemodified = time();
    $data->timecreated = time();
    // need to work with list of words
    $save = $data->words;
    $data->words ="";
    
    try {
        //insert glossaryfocus into table
        $data->id = $DB->insert_record("glossaryfocus", $data);
        $data->words = $save;
        if (!empty($data->words)) {
            //now for each words selected insert it in glossaryfocus_entries
            foreach ($data->words as $word) {
                $DB->insert_record("glossaryfocus_entries", array("idglossaryfocus"=>$data->id, "idglossaryentrie"=>$word,"timecreated"=>$data->timecreated));
            }
        }

    } catch (Exception $e) {
        var_dump($e);
        die;
    }
    
    $completiontimeexpected = !empty($data->completionexpected) ? $data->completionexpected : null;
    \core_completion\api::update_completion_date_event($data->coursemodule, 'glossaryfocus', $data->id, $completiontimeexpected);

    return $data->id;
}

/**
 * Update glossaryfocus instance.
 *
 * @param $data     | stdClass
 * @param $mform    | mod_glossaryfocus_mod_form
 * @return bool true
 */
function glossaryfocus_update_instance($data, $mform) {
    global $DB;
    
    $data->id = $data->instance;
    
    //Update the glossaryfocus primary table
    $DB->update_record('glossaryfocus', $data);

    //Need to update entries table
    if (!empty($data->words)) {
        // purge all entries
        $DB->delete_records("glossaryfocus_entries", array("idglossaryfocus"=>$data->id));

        //now for each words selected insert it in glossaryfocus_entries
        foreach ($data->words as $word) {

            $DB->insert_record("glossaryfocus_entries", array("idglossaryfocus"=>$data->id, "idglossaryentrie"=>$word));
        }
    } else {
        // We delete all
        $DB->delete_records("glossaryfocus_entries", array("idglossaryfocus"=>$data->id));
    }

    return true;
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param $id  | int of the glossary focus
 * @return bool true if successful
 */
function glossaryfocus_delete_instance($id) {
    global $CFG, $DB;

    // Ensure the glossaryfocus exists.
    if (!$glossaryfocus = $DB->get_record('glossaryfocus', array('id'=>$id))) {
        return false;
    }

    $cm = get_coursemodule_from_instance('glossaryfocus', $id);
    \core_completion\api::update_completion_date_event($cm->id, 'glossaryfocus', $id, null);


    $DB->delete_records('glossaryfocus', array('id'=>$glossaryfocus->id));

    // Ensure the glossaryfocus_entries exists
    if (!$glossaryfocus = $DB->get_records('glossaryfocus_entries', array('idglossaryfocus' => $id))) {
        // We don't, so it's over
        return true;
    }

    // Delete the glossaryfocus instance.
    if (!$DB->delete_records('glossaryfocus_entries', array('idglossaryfocus' => $id))) {
        return false;
    }

    return true;
}

/**
 * The features this activity supports.
 *
 * @uses FEATURE_GROUPS
 * @uses FEATURE_GROUPINGS
 * @uses FEATURE_GROUPMEMBERSONLY
 * @uses FEATURE_MOD_INTRO
 * @uses FEATURE_COMPLETION_TRACKS_VIEWS
 * @uses FEATURE_GRADE_HAS_GRADE
 * @uses FEATURE_GRADE_OUTCOMES
 * @param $feature  | string FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, null if doesn't know
 */
function glossaryfocus_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_ARCHETYPE:
            return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_GROUPS:
            return true;
        case FEATURE_GROUPINGS:
            return true;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return false;
        default:
            return null;
    }
}

/**
 * Function to be run periodically according to the moodle cron.
 */
function glossaryfocus_cron() {
    return true;
}
