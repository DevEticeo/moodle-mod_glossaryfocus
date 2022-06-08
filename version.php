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
 * Defines the version of glossaryfocus
 * Activity that displays glossary definitions selected from one or more given parent glossaries
 *
 * @package    mod_glossaryfocus
 * @copyright  2021 Eticeo <https://eticeo.com>
 * @author     2021 Jeremy Carre <jeremy.carre@eticeo.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 */

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

$plugin->version   = 2021102604;
$plugin->requires  = 2020110906;
$plugin->component = 'mod_glossaryfocus';
$plugin->cron      = 0; // Period for cron to check this module (secs).

$plugin->maturity  = MATURITY_STABLE;
$plugin->release   = "1.0";

//This plugin need glossary
$plugin->dependencies = array(
    'mod_glossary'  => 2020110900,
);