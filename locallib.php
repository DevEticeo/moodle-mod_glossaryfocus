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

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->dirroot/mod/glossaryfocus/lib.php");

/**
 * Return the array with the glossary_entries for the glossary focus in param
 * @param $glossaryfocus      | int id of the glossary focus
 *
 * @return array of glossary_entries
 */
function glossaryfocus_get_words_for_view($glossaryfocus) {
    global $DB;

    $listwords = $DB->get_records_sql("SELECT ge.*
                                        FROM {glossaryfocus_entries} gfe 
                                        INNER JOIN  {glossary_entries} ge ON (gfe.idglossaryentry = ge.id)
                                        WHERE gfe.idglossaryfocus = :glossaryfocusid 
                                        ORDER BY ge.concept", array('glossaryfocusid' => $glossaryfocus->id));

    if (empty($listwords) && $glossaryfocus->idglossarymaster > 0) {
        $listwords = $DB->get_records_sql("SELECT ge.*
                                            FROM {glossaryfocus} gf 
                                            INNER JOIN {glossary_entries} ge ON (gf.idglossarymaster = ge.glossaryid)
                                            INNER JOIN {glossary} g ON ge.glossaryid = g.id AND g.globalglossary = 1
                                            WHERE ge.glossaryid = :glossaryfocusid
                                            ORDER BY ge.concept",
                                            array('glossaryfocusid' => $glossaryfocus->idglossarymaster));
    } else if (empty($listwords) && $glossaryfocus->idglossarymaster == 0) {
        $listwords = $DB->get_records_sql("SELECT ge.*
                                            FROM {glossary_entries} ge 
                                            INNER JOIN {glossary} g ON ge.glossaryid = g.id AND g.globalglossary = 1
                                            ORDER BY ge.concept");
    }
    
    return $listwords;
}

/**
 * Return the array with the words of the glossary focus id in param
 * @param $idglossaryfocus      | int id of the glossary focus
 *
 * @return array of string
 */
function glossaryfocus_get_words($idglossaryfocus) {
    global $DB;

    $res = array();
    $listwords = $DB->get_records_sql("SELECT ge.id, ge.concept, g.name, g.id as glossaryid
                                        FROM {glossaryfocus_entries} gfe 
                                        INNER JOIN {glossary_entries} ge ON (gfe.idglossaryentry = ge.id) 
                                        INNER JOIN {glossary} g ON (ge.glossaryid = g.id) AND g.globalglossary = 1
                                        WHERE gfe.idglossaryfocus = :idglossaryfocus", array('idglossaryfocus' => $idglossaryfocus));

    foreach ($listwords as $word) {
        $res[$word->id] = $word->concept.' ('.$word->name.')';
    }

    return $res;
}

/**
 * Return a array with all the glossaries
 *
 * @return array of string
 */
function glossaryfocus_get_opt_glossarymaster() {
    global $DB;

    $res = array();
    $glossariesmaster = $DB->get_records("glossary", array('globalglossary' => 1));

    $res[0] = get_string("otp_all_master", "glossaryfocus");
    foreach ($glossariesmaster as $glossary) {
        $res[$glossary->id] = $glossary->name;
    }

    return $res;
}
