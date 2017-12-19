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
 * 360-degree feedback items management page.
 *
 * @author Jun Pataleta
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mod_threesixo
 */

require_once("../../config.php");

$instanceid = required_param('id', PARAM_INT);
$itemid = optional_param('itemid', 0, PARAM_INT);

$viewurl = new moodle_url('view.php');
$viewurl->param('id', $instanceid);

if ($instanceid == 0) {
    print_error('error360notfound', 'mod_threesixo', $viewurl);
}

$PAGE->set_url('/mod/threesixo/edit_items.php', ['id' => $instanceid]);

if (!$cm = get_coursemodule_from_id('threesixo', $instanceid)) {
    print_error('invalidcoursemodule');
}

if (!$course = $DB->get_record("course", ["id" => $cm->course])) {
    print_error('coursemisconf');
}

require_login($course, true, $cm);

if (!$threesixty = $DB->get_record("threesixo", ["id" => $cm->instance])) {
    print_error('error360notfound', 'mod_threesixo', $viewurl);
}

// Check capability to edit items.
$context = context_module::instance($cm->id);
if (!\mod_threesixo\api::can_edit_items($threesixty->id, $context)) {
    print_error('nocaptoedititems', 'mod_threesixo', $viewurl);
}

$question = '';
$questiontype = 0;

$PAGE->navbar->add(get_string('titlemanageitems', 'threesixo'));
$PAGE->set_heading($course->fullname);
$PAGE->set_title($threesixty->name);

echo $OUTPUT->header();
/// Print the main part of the page.
echo $OUTPUT->heading(format_string($threesixty->name));
echo $OUTPUT->heading(get_string('edititems', 'mod_threesixo'), 3);

$viewurl = new moodle_url('/mod/threesixo/view.php', ['id' => $cm->id]);
echo html_writer::link($viewurl,  get_string('backto360dashboard', 'mod_threesixo'), ['class' => 'pull-right']);

// 360-degree feedback item list.
$itemslist = new mod_threesixo\output\list_360_items($instanceid, $course->id, $threesixty->id);
$itemslistoutput = $PAGE->get_renderer('mod_threesixo');
echo $itemslistoutput->render($itemslist);

echo $OUTPUT->footer();
