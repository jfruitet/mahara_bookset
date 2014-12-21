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

define('INTERNAL', true);

define('MENUITEM', 'content/bookset');
define('SECTION_PLUGINTYPE', 'artefact');
define('SECTION_PLUGINNAME', 'bookset');
define('SECTION_PAGE', 'index');
define('BOOKSET_SUBPAGE', 'index');

defined('INTERNAL') || die();

require_once(dirname(dirname(dirname(__FILE__))) . '/init.php');
safe_require('artefact', 'bookset'); // BookSet : set of Booklet
safe_require('artefact', 'booklet');  // Booklet : Christophe Declercq's Mahara artefact

// safe_require('artefact', 'file');

define('TITLE', get_string('booksets','artefact.bookset'));

if (!PluginArtefactBookset::is_active()) {
    throw new AccessDeniedException(get_string('plugindisableduser', 'mahara', get_string('bookset','artefact.bookset')));
}

if (!PluginArtefactbooklet::is_active()) {
    throw new AccessDeniedException(get_string('plugindisableduser', 'mahara', get_string('booklet','artefact.booklet')));
}

$designer = get_record('artefact_booklet_designer', 'id', $USER->get('id'));
// renvoit les designers d'id = user pour savoir si user est designer
$tomes = get_records_array('artefact_booklet_tome', 'public', 1);
// renvoit la liste des tomes publics
$admin = get_record('usr', 'id', $USER->get('id'));

/**********************
if ($designer) {
    $modform = array(
        'name'        => 'modform',
        'plugintype'  => 'artefact',
        'successcallback' => 'modform_submit',
        'pluginname'  => 'booklet',
        'method'      => 'post',
        'renderer'    => 'oneline',
        'elements'    => array(
            'save' => array(
                'type' => 'submit',
                'value' => get_string('modif', 'artefact.booklet'),
            )
        ),
        'autofocus'  => false,
    );
    $indexform['modform'] = pieform($modform);
}
*********/
if ($admin->admin) {
    // si admin : formulaires de gestion des concepteurs
    $sql = "SELECT * FROM {usr}
           WHERE id IN (SELECT id from {artefact_booklet_designer})";
    $items = get_records_sql_array($sql,array());
    // liste des usr qui sont designers
    if ($items) {
        $designers = array();
        foreach ($items as $item) {
            // construit un tableau des designers : id -> name
            $designers[$item->id] = $item->firstname.' '.$item->lastname.' ('.$item->username.')';
        }
        // formulaire de suppression de designers
        $admindeleteform = pieform(array(
            'name' => 'admindeleteform',
            'plugintype' => 'artefact',
            'successcallback' => 'admindeleteform_submit',
            'pluginname' => 'booklet',
            'method' => 'post',
            'renderer' => 'oneline',
            'elements' => array(
                'id' => array(
                    'type' => 'select',
                    'options' => $designers,
                    'title' => get_string('deletedesigner', 'artefact.booklet'),
                ),
                'save' => array(
                    'type' => 'submit',
                    'value' => get_string('delete', 'artefact.booklet'),
                )
            ),
        ));
    }
    else {
        $admindeleteform = "";
    }

	// formulaire d'ajout de designers
    $adminform = pieform(array(
        'name' => 'adminform',
        'plugintype' => 'artefact',
        'successcallback' => 'adminform_submit',
        'pluginname' => 'booklet',
        'method' => 'post',
        'renderer' => 'oneline',
        'elements' => array(
            'name' => array(
                'type' => 'text',
                'title' => get_string('adddesigner', 'artefact.booklet'),
                'size' => 20,
            ),
            'save' => array(
                'type' => 'submit',
                'value' => get_string('add', 'artefact.booklet'),
            )
        ),
    ));
    $aide = '';
    $pf = '<fieldset class="pieform-fieldset"><legend>'. get_string('adminfield', 'artefact.booklet') . ' ' . $aide . '</legend>' . $adminform . $admindeleteform . '</fieldset>';
    $indexform['adminform'] = $pf;
}
// offset and limit for pagination
$offset = param_integer('offset', 0);
$limit  = param_integer('limit', 10);
$order = param_alpha('order', 'ASC');

$booksets = ArtefactTypeBookset::get_booksets(0, $offset, $limit, $order);
ArtefactTypeBookset::build_booksets_list_html($booksets);

$js = <<< EOF
addLoadEvent(function () {
    {$booksets['pagination_js']}
});
EOF;


$smarty = smarty(array('tablerenderer', 'paginator', 'jquery'));
// $smarty->assign('PAGEHELPNAME', true);
// $smarty->assign('PAGEHELPICON', $aide);
$smarty->assign('PAGEHEADING', hsc(get_string("booksets", "artefact.bookset")));
// $smarty->assign('help', $aide);
$smarty->assign('indexform', $indexform);
//$smarty->assign('choiceform', $choiceform);

$smarty->assign('d', $designer);
// $smarty->display('artefact:booklet:index.tpl');


$smarty->assign_by_ref('booksets', $booksets);
if ($limit<$booksets['count']){
	$smarty->assign('urlalllists', '<a href="' . get_config('wwwroot') . 'artefact/bookset/index.php?public=0&amp;offset=0&amp;limit='.$booksets['count'].'&amp;order='.$order.'">'.get_string('alllists','artefact.bookset',$booksets['count']).'</a>');
}
else{
	$smarty->assign('urlalllists', '<a href="' . get_config('wwwroot') . 'artefact/bookset/index.php?public=0&amp;offset=0&amp;limit=10&amp;order='.$order.'">'.get_string('paginationlists','artefact.bookset',10).'</a>');
}
if ($order=='ASC'){
	$smarty->assign('orderlist', '<a href="' . get_config('wwwroot') . 'artefact/bookset/index.php?public=0&amp;offset='.$offset.'&amp;limit='.$limit.'&amp;order=DESC">'.get_string('inverselist','artefact.bookset').'</a>');
}
else{
	$smarty->assign('orderlist', '<a href="' . get_config('wwwroot') . 'artefact/bookset/index.php?public=0&amp;offset='.$offset.'&amp;limit='.$limit.'&amp;order=ASC">'.get_string('inverselist','artefact.bookset').'</a>');
}

$smarty->assign('strnobooksetaddone',
    get_string('nobooksetaddone', 'artefact.bookset',
    '<a href="' . get_config('wwwroot') . 'artefact/bookset/new.php">', '</a>'));


$smarty->assign('INLINEJAVASCRIPT', $js);
$smarty->assign('SUBPAGENAV', PluginArtefactBookset::submenu_items());
$smarty->display('artefact:bookset:index.tpl');



function modform_submit(Pieform $form, $values) {
    $goto = get_config('wwwroot').'/artefact/booklet/tomes.php';
    redirect($goto);
}

function choiceform_submit(Pieform $form, $values) {
    global $USER, $SESSION;
    $goto = get_config('wwwroot') . '/artefact/booklet/index.php';
    $count = count_records('artefact_booklet_selectedtome', 'iduser', $USER->get('id'));
    $data = new StdClass;
    $data->idtome = $values['typefield'];
    $data->iduser = $USER->get('id');
    if ($count == 0) {
        insert_record('artefact_booklet_selectedtome', $data);
    }
    else {
        update_record('artefact_booklet_selectedtome', $data, 'iduser', $USER->get('id'));
    }
    $SESSION->add_ok_msg(get_string('bookletsaved', 'artefact.booklet'));
    redirect($goto);
}

function adminform_submit(Pieform $form, $values) {
    global $USER, $SESSION;
    $goto = get_config('wwwroot') . '/artefact/booklet/index.php';
    $designer = get_record('usr', 'username', $values['name']);
    if (!$designer) {
        $SESSION->add_error_msg(get_string('errusername', 'artefact.booklet'));
        redirect($goto);
    }
    $count = count_records('artefact_booklet_designer', 'id', $designer->id);
    if ($count != 0) {
        $SESSION->add_error_msg(get_string('useralreadyadd', 'artefact.booklet'));
        redirect($goto);
    }
    $dataobject = new stdClass();
    $dataobject->id = $designer->id;
    insert_record('artefact_booklet_designer', $dataobject);
    $SESSION->add_ok_msg(get_string('usersaved', 'artefact.booklet'));
    redirect($goto);
}

function admindeleteform_submit(Pieform $form, $values) {
    global $USER, $SESSION;
    $goto = get_config('wwwroot') . '/artefact/booklet/index.php';
    delete_records('artefact_booklet_designer', 'id', $values['id']);
    $SESSION->add_ok_msg(get_string('userdeleted', 'artefact.booklet'));
    redirect($goto);
}
