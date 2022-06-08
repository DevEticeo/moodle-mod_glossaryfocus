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
 * Web service for glossaryfocus
 *
 * @package    mod_glossaryfocus
 * @copyright  2021 Eticeo <https://eticeo.com>
 * @author     2021 Jeremy Carre <jeremy.carre@eticeo.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */


defined('MOODLE_INTERNAL') || die();

$functions = array(
    'mod_glossaryfocus_get_words' => array(
        'classname'   => 'mod_glossaryfocus\external',
        'methodname'  => 'get_words',
        'classpath'   => '',
        'description' => 'Retrieve the list of potential words',
        'type'        => 'read',
        'ajax'        => true
    ),
);
