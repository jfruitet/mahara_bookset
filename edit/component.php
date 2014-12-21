<?php
/**
 *
 * @package    mahara
 * @subpackage artefact-bookset
 * @author     Jean FRUITET - jean.fruitet@univ-nantes
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
require_once('pieforms/pieform/elements/calendar.php');
require_once(get_config('docroot') . 'artefact/lib.php');
safe_require('artefact','bookset');

if (!PluginArtefactBookset::is_active()) {
    throw new AccessDeniedException(get_string('plugindisableduser', 'mahara', get_string('bookset','artefact.bookset')));
}

define('TITLE', get_string('editcomponent','artefact.bookset'));

$id = param_integer('id');
$component = new ArtefactTypeItem($id);
if (!$USER->can_edit_artefact($component)) {
    throw new AccessDeniedException(get_string('accessdenied', 'error'));
}

$form = ArtefactTypeItem::get_form($component->get('parent'), $component);

$smarty = smarty();
$smarty->assign('editform', $form);
$smarty->assign('PAGEHEADING', hsc(get_string("editingcomponent", "artefact.bookset")));
$smarty->assign('SUBPAGENAV', PluginArtefactBookset::submenu_components());
$smarty->display('artefact:bookset:edit_index.tpl');
