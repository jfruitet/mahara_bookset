<?php
/**
 *
 * @package    mahara
 * @subpackage artefact-checklist
 * @author     Jean FRUITET - jean.fruitet@univ-nantes.fr
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL version 3 or later
 * @copyright  For copyright information on Mahara, please see the README file distributed with this software.
 *
 */

define('INTERNAL', true);
define('MENUITEM', 'content/checklist');
define('SECTION_PLUGINTYPE', 'artefact');
define('SECTION_PLUGINNAME', 'checklist');
define('SECTION_PAGE', 'checklist');
define('BOOKSET_SUBPAGE', 'index');

require(dirname(dirname(dirname(__FILE__))) . '/init.php');
safe_require('artefact', 'bookset');
if (!PluginArtefactBookset::is_active()) {
    throw new AccessDeniedException(get_string('plugindisableduser', 'mahara', get_string('bookset','artefact.bookset')));
}



$id = param_integer('id');
$componentid = param_integer('componentid', 0); // component id to move
$direction = param_integer('direction', 0); // 0 : move up or 1 : move down

// offset and limit for pagination
$offset = param_integer('offset', 0);
$limit  = param_integer('limit', 10);
$order = param_alpha('order', 'ASC');

$artefact = new ArtefactTypeBookset($id);

if (!$USER->can_edit_artefact($artefact)) {
    throw new AccessDeniedException(get_string('accessdenied', 'error'));
}

define('TITLE', get_string('components', 'artefact.bookset', $artefact->get('title') ));

// move component up or down
if (!empty($componentid)){
	if ($order=='DESC'){
        $direction = $direction ? 0 : 1;
	}
    ArtefactTypeBookset::invert_component($artefact->get('id'), $componentid, $direction);
}

$components = ArtefactTypeBookset::get_components($artefact->get('id'), $offset, $limit, $order);
	/*
	// DEBUG
	echo "<br />bookset.php :: 37<br />\n";
	print_object($components);
	exit;
	*/
$smarty = smarty(array('paginator'));
$smarty->assign_by_ref('components', $components);
$smarty->assign_by_ref('bookset', $id);
$smarty->assign_by_ref('tags', $artefact->get('tags'));
$smarty->assign_by_ref('owner', $artefact->get('owner'));

$smarty->assign('artefacttitle', $artefact->get('title'));
$smarty->assign('artefactdescription', $artefact->get('description'));
$smarty->assign('artefactstatus', $artefact->get('status'));
$smarty->assign('artefactpublic', $artefact->get('public'));



if ($limit<$components['count']){
	$smarty->assign('urlallcomponents', '<a href="' . get_config('wwwroot') . 'artefact/bookset/bookset.php?id='.$artefact->get('id').'&amp;offset=0&amp;limit='.$components['count'].'">'.get_string('allcomponents','artefact.bookset',$components['count']).'</a>');
}
else{
	$smarty->assign('urlallcomponents', '<a href="' . get_config('wwwroot') . 'artefact/bookset/bookset.php?id='.$artefact->get('id').'&amp;offset=0&amp;limit=10">'.get_string('paginationlists','artefact.bookset',10).'</a>');
}
if ($order=='ASC'){
	$smarty->assign('orderlist', '<a href="' . get_config('wwwroot') . 'artefact/bookset/bookset.php?id='.$artefact->get('id').'&amp;offset='.$offset.'&amp;limit='.$limit.'&amp;order=DESC">'.get_string('inverselist','artefact.bookset',10).'</a>');
}
else{
	$smarty->assign('orderlist', '<a href="' . get_config('wwwroot') . 'artefact/bookset/bookset.php?id='.$artefact->get('id').'&amp;offset='.$offset.'&amp;limit='.$limit.'&amp;order=ASC">'.get_string('inverselist','artefact.bookset',10).'</a>');
}

$smarty->assign('iconcheckpath', ArtefactTypeBookset::get_icon_checkpath());
$smarty->assign('strorder', $order);

$smarty->assign('strnocomponentsaddone',
    get_string('nocomponentaddone', 'artefact.bookset',
    '<a href="' . get_config('wwwroot') . 'artefact/bookset/select/index.php?id='.$artefact->get('id').'">', '</a>'));
$smarty->assign('booksetcomponentsdescription', get_string('booksetcomponentsdescription', 'artefact.bookset', get_string('selectcomponents', 'artefact.bookset')));
$smarty->assign('PAGEHEADING', get_string("booksetcomponents", "artefact.bookset", $artefact->get('title')));
// $smarty->assign('INLINEJAVASCRIPT', $js);
$smarty->assign('SUBPAGENAV', PluginArtefactBookset::submenu_items());
$smarty->display('artefact:bookset:componentsselect.tpl');