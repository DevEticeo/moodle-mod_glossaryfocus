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
 * Search user selector module.
 *
 * @module mod_glossaryfocus/form-words-selector
 * @copyright  2021 Eticeo <https://eticeo.com>
 * @author     2021 Jeremy Carre <jeremy.carre@eticeo.fr>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax'], function($, Ajax, Templates) {

    return /** @alias module:mod_glossaryfocus/form-words-selector */ {

        processResults: function(selector, results) {
            var words = [];
            $.each(results, function(index, word) {
                words.push({
                    value: word.id,
                    label: word.name
                });
            });
            return words;
        },

        transport: function(selector, query, success, failure) {
            var promise;

            var idglossary = $('#id_idglossarymaster').val();
            if (typeof idglossary !== "undefined" && $('#id_idglossarymaster').val() !== '') {
                idglossary = idglossary;
            } else {
                idglossary = 0;
            }
            promise = Ajax.call([{
                methodname: 'mod_glossaryfocus_get_words',
                args: {
                    query: query,
                    idglossary: idglossary
                }
            }]);

            promise[0].then(success).fail(failure);
        }

    };

});
