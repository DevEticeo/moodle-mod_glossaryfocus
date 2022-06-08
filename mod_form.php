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
 * The main glossaryfocus configuration form
 *
 * @package    mod_glossaryfocus
 * @copyright  2021 Eticeo <https://eticeo.com>
 * @author     2021 Jeremy Carre <jeremy.carre@eticeo.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/glossaryfocus/locallib.php');

class mod_glossaryfocus_mod_form extends moodleform_mod {
    function definition() {
        global $CFG, $DB;

        $mform = $this->_form;

        $config = get_config('glossaryfocus');

        //-------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->addElement('text', 'name', get_string('name'), array('size'=>'48'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $this->standard_intro_elements();


        //-------------------------------------------------------
        //Pour glossaire maitre
        $options = "";
        $opt_glossarymaster = get_opt_glossarymaster();
        $mform->addElement('select', 'idglossarymaster', get_string('select_idglossarymaster', 'glossaryfocus'), $opt_glossarymaster, $options);

        //-------------------------------------------------------
        //Pour mots
        $options = [
            'ajax' => 'mod_glossaryfocus/form-words-selector',
            'multiple' => true,
            'noselectionstring' => get_string('autocomplete_allwords', 'glossaryfocus')/*,
            'valuehtmlcallback' => function($value) {
                global $DB, $OUTPUT;
                $user = $DB->get_record('user', ['id' => (int)$value], '*', IGNORE_MISSING);
                if (!$user || !user_can_view_profile($user)) {
                    return false;
                }
                $details = user_get_user_details($user);
                return $OUTPUT->render_from_template(
                    'core_search/form-user-selector-suggestion', $details);
            }*/
        ];
        //$opt_glossarymaster = get_opt_glossarymaster();
        if ($this->_instance) {
            $wordsSelect = get_words($this->current->instance);
            //var_dump($wordsSelect);die;
        } else {
            $wordsSelect = [];
        }

        $strWordsSelected = "";
        $autocomplete = $mform->addElement('autocomplete', 'words', get_string('autocomplete_words','glossaryfocus'), [], $options);
        foreach ($wordsSelect as $index => $wordSelected) {
            //echo $wordSelected." ".$index;
            $autocomplete->addOption((string)$wordSelected, (int)$index);
            $strWordsSelected .= $index.',';
            //$autocomplete->setValue("1,2");
        }/*
        $autocomplete->addOption("Vélo", 1);
        $autocomplete->setValue(1);*/
        $strWordsSelected = substr($strWordsSelected, 0, -1);
        $autocomplete->setValue($strWordsSelected);

        //var_dump($autocomplete->getValue());
        //$mform->setDefault("words",$wordsSelect);

        //-------------------------------------------------------
        $this->standard_coursemodule_elements();

        //-------------------------------------------------------
        $this->add_action_buttons();

    }

    /**
     * Enforce defaults here.
     *
     * @param array $defaultvalues Form defaults
     * @return void
     **//*
    public function data_preprocessing(&$defaultvalues) {
        if (!empty($defaultvalues['displayoptions'])) {
            $displayoptions = unserialize($defaultvalues['displayoptions']);
            if (isset($displayoptions['printintro'])) {
                $defaultvalues['printintro'] = $displayoptions['printintro'];
            }
        }
    }*/
}

