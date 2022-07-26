<?php
// This file is part of the glossaryfocus module for Moodle - http://moodle.org/
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
 * Prints a particular instance of glossaryfocus
 *
 * @package    mod_glossaryfocus
 * @copyright  2021 Eticeo <https://eticeo.com>
 * @author     2021 Jeremy Carre <jeremy.carre@eticeo.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */

require('../../config.php');
require_once($CFG->dirroot.'/mod/glossaryfocus/lib.php');
require_once($CFG->dirroot.'/mod/glossaryfocus/locallib.php');
require_once($CFG->libdir.'/completionlib.php');

$id = optional_param('id', 0, PARAM_INT); // Course Module ID.

if (!$cm = get_coursemodule_from_id('glossaryfocus', $id)) {
    print_error('invalidcoursemodule');
}

$glossaryfocus = $DB->get_record('glossaryfocus', array('id' => $cm->instance), '*', MUST_EXIST);
$listwords = glossaryfocus_get_words_for_view($glossaryfocus);

$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/page:view', $context);

// Completion and trigger events.
$event = \mod_glossaryfocus\event\course_module_viewed::create(array(
    'objectid' => $glossaryfocus->id,
    'context' => $context,
));
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('glossaryfocus', $glossaryfocus);
$event->trigger();

$PAGE->set_url('/mod/glossaryfocus/view.php', array('id' => $cm->id));

$PAGE->set_title($course->shortname.': '.$glossaryfocus->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_activity_record($glossaryfocus);

$output = $PAGE->get_renderer('mod_glossaryfocus');

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($glossaryfocus->name), 2);

$contentintro = format_module_intro('glossaryfocus', $glossaryfocus, $cm->id);
echo $OUTPUT->box($contentintro, "generalbox center clearfix");

echo $output->render_glossaryfocus_words($listwords, $cm->id);


echo $OUTPUT->footer();
