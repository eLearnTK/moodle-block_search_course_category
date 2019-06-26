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
 * Course Category Finder Block
 *
 * Course Category Finder is an extended version of the Course Finder Block. An Option is added to
 * enable the user to search for categories as well as the courses.
 *
 * @package    block_search_course_category
 * @copyright  Copyrights © 2016 - 2017 | BTU Cottbus-Senftenberg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("{$CFG->libdir}/formslib.php");
require_once("lib.php");

/**
 * Display list of enrolled courses based on login user.
 *
 * @copyright 3i Logic<lms@3ilogic.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_search_form extends moodleform {

    public function definition() {

        global $CFG, $PAGE;

        $mform    = $this->_form;

        $mform->addElement('header', 'basic_search', get_string('search', 'block_search_course_category'));
        $mform->setExpanded('basic_search', true);

        $mform->addElement('text', 'search', get_string('searchcourses', 'block_search_course_category'));
        $mform->setType('search', PARAM_TEXT);

        $mform->addElement( 'select', 'combo_option', get_string('search_context', 'block_search_course_category'),
                            array(
                                'course' => get_string('combo_option_course', 'block_search_course_category'),
                                'category' => get_string('combo_option_category', 'block_search_course_category'),
                                'summary' => get_string('combo_option_summary', 'block_search_course_category')
                            ));

        $mform->addElement('advcheckbox', 'filtercheckbox', '', get_string('filtercheckbox', 'block_search_course_category'));
        $mform->setDefault('filtercheckbox', true);

        $mform->disabledIf('coursestartdate', 'filtercheckbox', 'checked');
        $mform->disabledIf('courseenddate', 'filtercheckbox', 'checked');
        $mform->disabledIf('sortmenu', 'filtercheckbox', 'checked');
        $mform->disabledIf('description', 'filtercheckbox', 'checked');
        $mform->disabledIf('courseformat', 'filtercheckbox', 'checked');
        $mform->disabledIf('completioncriteria', 'filtercheckbox', 'checked');

        $mform->addElement('header', 'filterresults', get_string('filterresults', 'block_search_course_category'));
        $mform->setExpanded('filterresults', false);

        $datestr = get_string('searchfromtime', 'block_search_course_category');
        $mform->addElement('date_time_selector', 'coursestartdate', $datestr, array('optional' => true));
        $mform->setDefault('coursestartdate', 0);

        $datestr2 = get_string('searchtilltime', 'block_search_course_category');
        $mform->addElement('date_time_selector', 'courseenddate', $datestr2, array('optional' => true));
        $mform->setDefault('courseenddate', 0);

        $cfnonestr = get_string('courseformat_none', 'block_search_course_category');
        $mform->addElement('select', 'courseformat', get_string('courseformat', 'block_search_course_category'), array(
            '' => $cfnonestr,
            'topics' => get_string('courseformat_topics', 'block_search_course_category'),
            'weeks' => get_string('courseformat_weeks', 'block_search_course_category'),
            'singleactivity' => get_string('courseformat_activity', 'block_search_course_category'),
        ));

        $mform->addElement('select', 'completioncriteria', get_string('completioncriteria', 'block_search_course_category'), array(
            '' => get_string('completioncriteria_none', 'block_search_course_category'),
            '1' => get_string('completioncriteria_enable', 'block_search_course_category'),
            '0' => get_string('completioncriteria_disable', 'block_search_course_category')
        ));

        $mform->addElement('header', 'sortresults', get_string('sortheading', 'block_search_course_category'));
        $mform->setExpanded('sortresults', false);

        $mform->addElement('select', 'sortmenu', get_string('sortby', 'block_search_course_category'), array(
            '' => get_string('sortby_none', 'block_search_course_category'),
            'fullname' => get_string('sortby_fullname', 'block_search_course_category'),
            'shortname' => get_string('sortby_shortname', 'block_search_course_category'),
            'startdate' => get_string('sortby_startdate', 'block_search_course_category')
        ));

        $this->add_action_buttons(false, get_string('search', 'block_search_course_category'));
    }
}
