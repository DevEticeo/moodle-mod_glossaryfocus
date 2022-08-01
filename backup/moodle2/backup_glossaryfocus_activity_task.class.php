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
 * Defines backup_glossaryfocus_activity_task
 *
 * @package    mod_glossaryfocus
 * @copyright  2021 Eticeo <https://eticeo.com>
 * @author     2021 Jeremy Carre <jeremy.carre@eticeo.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/glossaryfocus/backup/moodle2/backup_glossaryfocus_stepslib.php');
require_once($CFG->dirroot . '/mod/glossaryfocus/backup/moodle2/backup_glossaryfocus_settingslib.php');

/**
 * Provides the steps to perform one complete backup of the Database instance
 */
class backup_glossaryfocus_activity_task extends backup_activity_task {

    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity.
    }

    /**
     * Defines a backup step to store the instance data in the glossaryfocus.xml file
     */
    protected function define_my_steps() {
        $this->add_step(new backup_glossaryfocus_activity_structure_step('glossaryfocus_structure', 'glossaryfocus.xml'));
    }

    /**
     * Encodes URLs to the index.php and view.php scripts
     *
     * @param string $content some HTML text that eventually contains URLs to the activity instance scripts
     * @return string the content with the URLs encoded
     */
     public static function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot, "/");

        // Link to the list of glossaryfocus.
        $search = "/(".$base."\/mod\/glossaryfocus\/index.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@GLOSSARYFOCUSINDEX*$2@$', $content);

        // Link to GLOSSARYFOCUS view by moduleid.
        $search = "/(".$base."\/mod\/glossaryfocus\/view.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@GLOSSARYFOCUSVIEWBYID*$2@$', $content);

        return $content;
    }
}

