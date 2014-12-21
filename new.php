<?php
/**
 *
 * @package    mahara
 * @subpackage artefact-bookset
 * @author     Jean FRUITET - jean.fruitet@univ-nantes.fr
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL version 3 or later
 * @copyright  For copyright information on Mahara, please see the README file distributed with this software.
 *
 */

define('INTERNAL', 1);
define('MENUITEM', 'content/bookset');
define('SECTION_PLUGINTYPE', 'artefact');
define('SECTION_PLUGINNAME', 'bookset');

require(dirname(dirname(dirname(__FILE__))) . '/init.php');
safe_require('artefact', 'bookset');
if (!PluginArtefactBookset::is_active()) {
    throw new AccessDeniedException(get_string('plugindisableduser', 'mahara', get_string('bookset','artefact.bookset')));
}

$id = param_integer('id',0);
$positionafter = param_integer('positionafter', -1); // item position before new one

if ($id) {
    $bookset = new ArtefactTypeBookset($id);
	//print_object($bookset);
	//exit;
    if (!$USER->can_edit_artefact($bookset)) {
        throw new AccessDeniedException(get_string('accessdenied', 'error'));
    }
    define('TITLE', get_string('newbookset','artefact.bookset'));
    $form = ArtefactTypeBookset::get_form($bookset); // new item to insert
}
else {
    define('TITLE', get_string('newbookset','artefact.bookset'));
    $form = ArtefactTypeBookset::get_form();
}

$smarty =& smarty();
$smarty->assign_by_ref('form', $form);
$smarty->assign_by_ref('PAGEHEADING', hsc(TITLE));
$smarty->display('artefact:bookset:new.tpl');
