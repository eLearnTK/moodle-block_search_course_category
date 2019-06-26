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
// @codingStandardsIgnoreLine;
require_once("../../config.php");
require_once('course_form.php');
require_once('lib.php');

global $DB, $OUTPUT, $PAGE, $CFG, $USER;

$search = optional_param('search', null, PARAM_RAW);
$option = required_param('combo_option', PARAM_RAW);

$context = context_system::instance();

$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'block_search_course_category'));
$PAGE->set_heading('Course Category Finder');
$PAGE->set_url('/blocks/search_course/view.php');
$PAGE->navbar->ignore_active();
$PAGE->navbar->add(get_string('pluginname', 'block_search_course_category'));

$form = new course_search_form();
$toform['search'] = $search;
$toform['combo_option'] = $option;
$form->set_data($toform);

echo $OUTPUT->header();
$form->display();

$courserenderer = $PAGE->get_renderer('core', 'course');

if ($fromform = $form->get_data()) {
    if ($fromform->combo_option == 'course') {
        $content = $courserenderer->courses_list(search_courses_by_name($fromform), true);
    }
    if ($fromform->combo_option == 'category') {
        $content = $courserenderer->courses_list(search_courses_by_category($fromform), true);
    }
    if ($fromform->combo_option == 'summary') {
        $content = $courserenderer->courses_list(search_courses_by_summary($fromform), true);
    }
} else {
    $initialsearch = new stdClass();
    $initialsearch->search = $search;
    if ($option == 'course') {
        $content = $courserenderer->courses_list(search_courses_by_name($initialsearch), true);
    }
    if ($option == 'category') {
        $content = $courserenderer->courses_list(search_courses_by_category($initialsearch), true);
    }
    if ($option == 'summary') {
        $content = $courserenderer->courses_list(search_courses_by_summary($initialsearch), true);
    }
}

echo ($content) ? $content : '<h2>' . get_string('no_results', 'block_search_course_category') . '</h2>';

echo $OUTPUT->footer();