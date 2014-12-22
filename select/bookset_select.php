<?php
/**
 *
 * @package    mahara
 * @subpackage artefact-bookset
 * @author     Jean FRUITET - jean.fruitet@univ-nan
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL version 3 or later
 * @copyright  For copyright information on Mahara, please see the README file distributed with this software.
 *
 */

define('INTERNAL', true);
define('MENUITEM', 'content/bookset');
define('SECTION_PLUGINTYPE', 'artefact');
define('SECTION_PLUGINNAME', 'bookset');
define('SECTION_PAGE', 'index');
define('BOOKSET_SUBPAGE', 'index');

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/init.php');
require_once('pieforms/pieform.php');
require_once(get_config('docroot') . 'artefact/lib.php');

safe_require('artefact','bookset');

if (!PluginArtefactBookset::is_active()) {
    throw new AccessDeniedException(get_string('plugindisableduser', 'mahara', get_string('bookset','artefact.bookset')));
}

$id = param_integer('id');
define('TITLE', get_string('selectbookset','artefact.bookset'));
$artefact = new ArtefactTypeBookset($id);
if (!$USER->can_edit_artefact($artefact)) {
    throw new AccessDeniedException(get_string('accessdenied', 'error'));
}

$editform = ArtefactTypeBookset::get_form_select($artefact);
//print_object($editform);
//exit;
$smarty = smarty();
$smarty->assign('editform', $editform);
$smarty->assign('PAGEHEADING', hsc(get_string("editingbookset", "artefact.bookset")));
$smarty->assign('SUBPAGENAV', PluginArtefactBookset::submenu_items());
$smarty->display('artefact:bookset:edit_index.tpl');
