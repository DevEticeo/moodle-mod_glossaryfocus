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
 * External API for glossaryfocus
 *
 * @package    mod_glossaryfocus
 * @copyright  2021 Eticeo <https://eticeo.com>
 * @author     2021 Jeremy Carre <jeremy.carre@eticeo.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
namespace mod_glossaryfocus;
defined('MOODLE_INTERNAL') || die();

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use external_multiple_structure;

require_once($CFG->libdir."/externallib.php");

/**
 * Class external
 *
 * @package    mod_glossaryfocus
 * @copyright  2021 Eticeo <https://eticeo.com>
 * @author     2021 Jeremy Carre <jeremy.carre@eticeo.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */
class external extends \external_api {
    /**
     * Returns glossaryfocus_get_words() parameters.
     *
     * @return \external_function_parameters
     */
    public static function get_words_parameters() {
        return new \external_function_parameters(
            array(
                'query' => new external_value(PARAM_TEXT,
                    'Query string (full or partial word name)'),
                'idglossary' => new external_value(PARAM_INT, 'Glossary master id (0 if none)'),
                'courseid' => new external_value(PARAM_INT, 'Course id where the glossaryfocus is (0 if none)'),
            )
        );
    }

    /**
     * Handles return the element's HTML.
     *
     * @param $query        | string|null
     * @param $idglossary   |  int|null
     *
     * @return array of words
     */
    public static function get_words($query, $idglossary, $courseid) {
        global $DB, $CFG;

        require_once($CFG->dirroot.'/mod/glossaryfocus/lib.php');

        $result = [];
        
        $params = array('query' => '%'.$query.'%');
        $condition = "";
        if ($idglossary > 0) {
            $condition .= " AND glossaryid = :glossaryid";
            $params['glossaryid'] = $idglossary;
        }
        $condition .= " AND (g.globalglossary = 1 OR g.course = :courseid)";
        $params['courseid'] = $courseid;

        $listwords = $DB->get_records_sql("SELECT ge.id, ge.concept, g.name, cm.id as cmid
                                            FROM {glossary_entries} ge 
                                            INNER JOIN {glossary} g ON ge.glossaryid = g.id
                                            INNER JOIN {course_modules} cm ON cm.course = g.course
                                            INNER JOIN {modules} m ON m.id = cm.module AND m.name = 'glossary'
                                            WHERE ".$DB->sql_like('concept', ':query')." ".$condition.'
                                            ORDER BY ge.concept, g.name', $params);

        foreach ($listwords as $word) {
            $context = \context_module::instance($word->cmid);
            if (has_capability('mod/glossary:view', $context)) {
                $result[] = ['id' => $word->id, 'name' => $word->concept.' ('.$word->name.')'];
            }
        }

        return $result;
    }

    /**
     * Returns the get_words result value.
     *
     * @return \external_value
     */
    public static function get_words_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'id'    => new external_value(PARAM_INT, 'ID of the word'),
                'name'  => new external_value(PARAM_NOTAGS, 'The word name')
            ])
        );
    }
}
