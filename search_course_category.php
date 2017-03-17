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
 * Course Category Finder is an extended version of the Course Finder Block. An Option is added to
 * enable the user to search for categories as well as the courses.
 * @author: Tobias Kutzner, Igor Nesterow
 */

require_once("../../config.php");
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->libdir.'/coursecatlib.php');

$option     = $_POST['combo_option'];
$search     = optional_param('search', '', PARAM_RAW);  // Search words,
$page       = optional_param('page', 0, PARAM_INT);     // which page to show,
$perpage    = optional_param('perpage', '', PARAM_RAW); // how many per page, may be integer or 'all'.
$blocklist  = optional_param('blocklist', 0, PARAM_INT);
$modulelist = optional_param('modulelist', '', PARAM_PLUGIN);
$tagid      = optional_param('tagid', '', PARAM_INT);   // Searches for courses tagged with this tag id.

$capabilities = array('moodle/course:create', 'moodle/category:manage');

$usercatlist = coursecat::make_categories_list($capabilities);

$search = trim(strip_tags($search)); // Trim & clean raw searched string.

$site = get_site();

if ($CFG->forcelogin) {
    require_login();
}

if ($option == 'course') {
    $searchcriteria = array();
    foreach (array('search', 'blocklist', 'modulelist', 'tagid', 'combo_option') as $param) {
        if (!empty($$param)) {
            $searchcriteria[$param] = $$param;
        }
    }

    $urlparams = array();
    if ($perpage !== 'all' && !($perpage = (int)$perpage)) {
        // Default number of courses per page.
        $perpage = $CFG->coursesperpage;
    } else {
        $urlparams['perpage'] = $perpage;
    }
    if (!empty($page)) {
        $urlparams['page'] = $page;
    }

    $PAGE->set_url($CFG->wwwroot.'/blocks/search_course_category/search_course_category.php', $searchcriteria + $urlparams);
    $PAGE->set_context(context_system::instance());
    $PAGE->set_pagelayout('standard');
    $courserenderer = $PAGE->get_renderer('core', 'course');

    if ($CFG->forcelogin) {
        require_login();
    }

    $strcourses = new lang_string("courses");
    $strsearch = new lang_string("search");
    $strsearchresults = new lang_string("searchresults");
    $strnovalidcourses = new lang_string('novalidcourses');

    $PAGE->navbar->add($strcourses, new moodle_url('/course/index.php'));
    $PAGE->navbar->add($strsearch, new moodle_url($CFG->wwwroot.'/blocks/search_course_category/search_course_category.php'));
    if (!empty($search)) {
        $PAGE->navbar->add(s($search));
    }

    if (empty($searchcriteria)) {
        // No search criteria specified, print page with just search form.
        $PAGE->set_title("$site->fullname : $strsearch");
    } else {
        // This is search results page.
        $PAGE->set_title("$site->fullname : $strsearchresults");
        // Link to manage search results should be visible if user have system or category level capability.
        if ((can_edit_in_category() || !empty($usercatlist))) {
            $aurl = new moodle_url('/course/management.php', $searchcriteria);
            $searchform = $OUTPUT->single_button($aurl, get_string('managecourses'), 'get');
        } else {
            $searchform = $courserenderer->course_search_form($search, 'navbar');
        }
        $PAGE->set_button($searchform);
    }

    $PAGE->set_heading($site->fullname);

    echo $OUTPUT->header();
    echo $courserenderer->search_courses($searchcriteria);
    echo $OUTPUT->footer();

} else if ($option = 'category') {
    $table   = 'course_categories';
    $select  = $DB->sql_like('name', ':search', $casesensitive = false);
    $param   = array('search' => '%'.$search.'%');
    $records = $DB->get_records_select($table, $select, $param);

    $PAGE->set_url($CFG->wwwroot.'/blocks/search_course_category/search_course_category.php');
    $PAGE->set_context(context_system::instance());
    $PAGE->set_pagelayout('standard');
    $PAGE->set_pagelayout('coursecategory');
    $courserenderer = $PAGE->get_renderer('core', 'course');

    if ($CFG->forcelogin) {
        require_login();
    }

    if (!has_capability('moodle/category:viewhiddencategories', $PAGE->context)) {
        throw new moodle_exception('unknowncategory');
    }

    $PAGE->set_heading($site->fullname);
    echo $OUTPUT->header();
    echo $OUTPUT->skip_link_target();

    foreach ($records as $record) {
        $content = $courserenderer->course_category($record->id);
        echo $content;
    }

    $found = get_string('found', 'block_search_course_category');
    echo "</br><b>".$found."</b></br>";
    foreach ($records as $record) {
        echo $record->name;
        echo "</br>";
    }
    echo $OUTPUT->footer();
}
