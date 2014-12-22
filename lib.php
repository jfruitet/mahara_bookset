<?php
/**
 * Mahara: Electronic portfolio, weblog, bookset builder and social networking
 * Copyright (C) 2006-2008 Catalyst IT Ltd (http://www.catalyst.net.nz)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    mahara
 * @subpackage artefact-bookset
 * @author     Jean FRUITET - jean.fruitet@univ-nantes.fr
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 *
 */

defined('INTERNAL') || die();

class PluginArtefactBookset extends PluginArtefact {

	/** 
     * This function returns a list of classnames 
     * of artefact types this plugin provides.
     * @abstract
     * @return array
     */
	public static function get_artefact_types() {
		return array(
			'bookset',
		);
	}
	
    /**
    * This function returns a list of classnames
    * of block types this plugin provides
    * they must match directories inside artefact/$name/blocktype
    * @abstract
    * @return array
    */
    public static function get_block_types() {
        return array();
    }

	public static function get_plugin_name() {
		return 'bookset';
	}

    public static function is_active() {
        return get_field('artefact_installed', 'active', 'name', 'bookset');
    }

	
    /**
     * This function returns an array of menu components
     * to be displayed
     * Each component should be a stdClass object containing -
     * - name language pack key
     * - url relative to wwwroot
     * @return array
     */
	public static function menu_items() {
		return array(
   			'content/bookset' => array(
				'path' => 'content/bookset',
				'url' => 'artefact/bookset/index.php',
				'title' => get_string('bookset_short', 'artefact.bookset'),
				'weight' => 70,
			)
		);
	}
	
	/**
	 *   This function return a list of submenues
	 *      This function use bookset components as new tabs

	*/
    public static function submenu_items() {
		global $USER;
        $tabs = array();
        $tabs['index'] = array(
                'page'  => 'index',
                'url'   => 'artefact/bookset',
                'title' => get_string('mybooksets', 'artefact.bookset'),
            );
		/*
        $tabs['import'] = array(
                'page'  => 'import',
                'url'   => 'artefact/bookset/import.php',
                'title' => get_string('import', 'artefact.bookset'),
            );
		*/
        if ($booksets = ArtefactTypeBookset::get_booksets()){
				// DEBUG
				//echo "<br />DEBUG :: lib.php :: 103 :: BOOKSET<br />\n";
				//print_object($booksets);
				//exit;
			if (!empty($booksets['data'])){
				//DEBUG
				//echo "<br />DEBUG :: lib.php :: 103 :: BOOKSET<br />\n";
				//print_object($booksets['data']);
				//exit;

        		foreach ($booksets['data'] as $bookset){
					// DEBUG
					//echo "<br />DEBUG :: lib.php :: 103 :: BOOKSET<br />\n";
					//print_object($bookset);
					//exit;
					if ($bookset->select && ($components = ArtefactTypeBookset::get_bookset_components_info($bookset->id))){
						// DEBUG
						//echo "<br />DEBUG :: lib.php :: 103 :: BOOKSET<br />\n";
						//print_object($components);

						foreach ($components as $component){
							//DEBUG
							//echo "<br />DEBUG :: lib.php :: 103 :: BOOKSET<br />\n";
							//print_object($component);
							if ($component->public || ($component->owner == $USER->get('id'))){
								$index = $component->tomeid;
                        		$tabs[$index] = array(
			                		'page'  => 'import',
            			    		'url'   => 'artefact/bookset/dispatch.php?id='.$component->tomeid,
			                		'title' => strip_tags($bookset->title.'::'.$component->title),
            					);
							}
						}
					}
				}
			}
		}

        if (defined('BOOKSET_SUBPAGE') && isset($tabs[BOOKSET_SUBPAGE])) {
            $tabs[BOOKSET_SUBPAGE]['selected'] = true;
        }
		//DEBUG
		//echo "<br />DEBUG :: lib.php :: 139 :: TABS<br />\n";
		//print_object($tabs);
		//exit;
        return $tabs;
    }


	 /**
     * When filtering searches, some artefact types are classified the same way
     * even when they come from different artefact plugins.  This function allows
     * artefact plugins to declare which search filter content type each of their
     * artefact types belong to.
     * @return array of artefacttype => array of filter content types
     */
    public static function get_artefact_type_content_types() {
        return array(
            'bookset' => array('text'),
        );
    }

	 
    /**
     * Returns the relative URL path to the place in mahara that relates
     * to the artefact.
     * E.g. For plan artefact the link will be 'artefact/plans/index.php'
     * @param int The name of the artefact type (in case different ones need different links)
     * @return string Url path to artefact.
     */	 
    public static function progressbar_link($artefacttype) {
        return 'artefact/bookset/index.php';
    }

  	public static function get_activity_types() {
        return array();
    }

	public static function get_cron() {
		return array();
	}

	public static function postinst($prevversion) {
        if ($prevversion == 0) {
            $sort = (get_record_sql('SELECT MAX(sort) AS maxsort FROM {blocktype_category}')->maxsort) + 1;
            insert_record('blocktype_category', (object)array('name' => 'bookset', 'sort' => $sort));
            /* log_warn('installation de la categorie bookset'); */
        }
        else {
            /* log_warn('pas d installation necessaire de la categorie bookset'); */
        }
    }


}


class ArtefactTypeBookset extends ArtefactType {

 	// nouvelles proprietes par rapport a la classe artefact standart
	protected $status = 0;
    protected $public = 0;
    protected $select = 0;
	
    public function __construct($id = 0, $data = null) {

        if (empty($this->id)) {
            $this->container = 1;
        }

        parent::__construct($id, $data);

        if ($this->id) {
            if ($pdata = get_record('artefact_bookset', 'artefact', $this->id)) {
                foreach($pdata as $name => $value) {
                    if (property_exists($this, $name)) {
                        $this->{$name} = $value;
                    }
                }
            }
            else {
                // This should never happen unless the user is playing around with task IDs in the location bar or similar
                throw new ArtefactNotFoundException(get_string('booksetdoesnotexist', 'artefact.bookset'));
            }
        }

    }
	
	public static function get_links($id) {
        return array(
            '_default' => get_config('wwwroot') . 'artefact/bookset/bookset.php?id=' . $id,
        );

	}

	public static function is_public() {
        return $this->get('public');
	}


    /**
     * Returns a URL for an icon for the appropriate artefact
     *
     * @param array $options Options for the artefact. The array MUST have the
     *                       'id' key, representing the ID of the artefact for
     *                       which the icon is being generated. Other keys
     *                       include 'size' for a [width]x[height] version of
     *                       the icon, as opposed to the default 20x20, and
     *                       'view' for the id of the view in which the icon is
     *                       being displayed.
     * @abstract
     * @return string URL for the icon
     */
	public static function get_icon($options=null) {
        global $THEME;
        return $THEME->get_url('images/bookset.png', false, 'artefact/bookset');
	}


    /**
     * Returns a URL for an icon for the appropriate artefact
     *
     * @return string URL for the icon
     */
	public static function get_icon_checkpath($options=null) {
        global $THEME;
        return $THEME->get_url('images/btn_check.png', false, 'artefact/bookset');
	}


    /**
     * Returns a URL for an icon for the appropriate artefact
     *
     * @return string URL for the icon
     */
	public static function get_icon_showpath($options=null) {
        global $THEME;
        return $THEME->get_url('images/btn_show.png', false, 'artefact/bookset');
	}




    /**
     * This method extends ArtefactType::commit() by adding additional data
     * into the artefact_bookset table.
     * If artefact has extra information in other tables, you need modify
     * this method, and call parent::commit() in your own function.
     */
    public function commit() {
        if (empty($this->dirty)) {
            return;
        }

        // Return whether or not the commit worked
        $success = false;

        db_begin();
        $new = empty($this->id);

        parent::commit();

        $this->dirty = true;

        $data = (object)array(
   			'artefact'  	=> $this->get('id'),
            'status' 	=> $this->get('status'),
            'public' 		=> $this->get('public'),
            'select' 		=> $this->get('select'),
        );
		// DEBUG
		// print_object($data);
		// exit;
        if ($new) {
            $success = insert_record('artefact_bookset', $data);
        }
        else {
            $success = update_record('artefact_bookset', $data, 'artefact');
        }

        db_commit();

        $this->dirty = $success ? false : true;

        return $success;
    }


    /** 
     * This function provides basic delete functionality.  It gets rid of the
     * artefact's row in the artefact table, and the tables that reference the
     * artefact table.  It also recursively deletes child artefacts.
     *
     * If your artefact has additional data in another table, you should
     * modify this function, but you MUST call parent::delete() after you
     * have done your own thing.
     */
    public function delete() {
        if (empty($this->id)) {
            return;
        }

        db_begin();
        delete_records('artefact_bookset_component', 'booksetid', $this->id);
        delete_records('artefact_bookset', 'artefact', $this->id);

        parent::delete();
        db_commit();
    }

    public static function bulk_delete($artefactids) {
        if (empty($artefactids)) {
            return;
        }

        $idstr = join(',', array_map('intval', $artefactids));

        db_begin();
        delete_records_select('artefact_bookset', 'artefact IN (' . $idstr . ')');
        parent::bulk_delete($artefactids);
        db_commit();
    }


	public static function is_singular() {
		return false;
	}

   /**
     * This function returns a bookset of the given user's.
     *
     * @return array (count: integer, data: array)
     */
    public static function get_bookset($id) {
        global $USER;

        ($bookset = get_record_sql("
							SELECT a.*, ac.status, ac.public
							FROM {artefact} a
            				JOIN {artefact_bookset} ac ON ac.artefact = a.id
                            WHERE a.id = ? AND a.owner = ? AND a.artefacttype = 'bookset'
                            ORDER BY a.title ASC", array($id, $USER->get('id'))));

 		if ($bookset){
            $bookset->description = '<p>' . preg_replace('/\n\n/','</p><p>', $bookset->description) . '</p>';
            if (!empty($bookset->status)){
				$bookset->status = '<p><i>' . preg_replace('/\n\n/','</p><p>', $bookset->status) . '</i></p>';
			}
            if (!empty($bookset->public)){
				$bookset->public = '<p>' . get_string('publiclist', 'artefact.bookset').'</p>';
			}
		}

        return $bookset;
    }


   /**
     * This function returns a list of the given user's booksets.
     *
     * @param limit how many bookset to display per page
     * @param offset current page to display
     * @return array (count: integer, data: array)
     */

    public static function get_booksets($public=0, $offset=0, $limit=10, $order='ASC') {
        global $USER;
		if (!empty($public)){
			($booksets = get_records_sql_array("
							SELECT a.*, ac.status, ac.public, ac.select
							FROM {artefact} a
            				JOIN {artefact_bookset} ac ON ac.artefact = a.id
                            WHERE ac.public = ? AND a.artefacttype = 'bookset'
                            ORDER BY a.title ".$order, array(1), $offset, $limit))
                            || ($booksets = array());
			$count = count(get_records_sql_array("
							SELECT a.id
							FROM {artefact} a
            				JOIN {artefact_bookset} ac ON ac.artefact = a.id
                            WHERE ac.public = ? AND a.artefacttype = 'bookset'
                            ", array(1)));
		}
        else{
			($booksets = get_records_sql_array("
							SELECT a.*, ac.status, ac.public, ac.select
							FROM {artefact} a
            				JOIN {artefact_bookset} ac ON ac.artefact = a.id
                            WHERE a.owner = ? AND a.artefacttype = 'bookset'
                            ORDER BY a.title ".$order, array($USER->get('id')), $offset, $limit))
                            || ($booksets = array());
			$count = count_records('artefact', 'owner', $USER->get('id'), 'artefacttype', 'bookset');
		}

		$result = array(
            'count'  =>  $count,
	        'data'   => $booksets,
            'offset' => $offset,
            'limit'  => $limit,
        );

        return $result;
    }

    /**
    * Gets the new/edit fields for the bookset pieform
    *
    */
    public static function get_booksetform_elements($bookset) {
        $elements = array(
            'title' => array(
                'type' => 'text',
                'defaultvalue' => null,
                'title' => get_string('title', 'artefact.bookset'),
                'size' => 30,
                'rules' => array(
                    'required' => true,
                ),
            ),
            'description' => array(
                'type'  => 'wysiwyg',
                'rows' => 10,
                'cols' => 50,
                'resizable' => true,
                'defaultvalue' => null,
                'title' => get_string('description', 'artefact.bookset'),
            ),
            'status' => array(
                'type'  => 'radio',
            	'options' => array(
                	0 => get_string('no'),
                	1 => get_string('yes'),
            	),
            	'defaultvalue' => 0,
            	'rules' => array(
                	'required' => true
            	),
            	'separator' => ' &nbsp; ',
                'title' => get_string('status', 'artefact.bookset'),
				'description' => get_string('selectstatusdesc', 'artefact.bookset'),
            ),
            'public' => array(
                'type'  => 'radio',
            	'options' => array(
                	0 => get_string('no'),
                	1 => get_string('yes'),
            	),
            	'defaultvalue' => 0,
            	'rules' => array(
                	'required' => true
            	),
            	'separator' => ' &nbsp; ',
                'title' => get_string('publiclist', 'artefact.bookset'),
                'description' => get_string('publiclistdesc','artefact.bookset'),
            ),
            'select' => array(
                'type'  => 'radio',
            	'options' => array(
                	0 => get_string('no'),
                	1 => get_string('yes'),
            	),
            	'defaultvalue' => 0,
            	'rules' => array(
                	'required' => true
            	),
            	'separator' => ' &nbsp; ',
                'title' => get_string('selectthisbookset', 'artefact.bookset'),
                'description' => get_string('selectthisbooksetdesc','artefact.bookset'),
            ),

        );

        if (!empty($bookset)) {
			//echo "<br />DEBUG :: lib.php :: 443<br />\n";
			//print_object($bookset);
			//exit;
            foreach ($elements as $k => $element) {
                $elements[$k]['defaultvalue'] = $bookset->get($k);
            }
            $elements['bookset'] = array(
                'type' => 'hidden',
                'value' => $bookset->id,
            );
        }

        if (get_config('licensemetadata')) {
            $elements['license'] = license_form_el_basic($bookset);
            $elements['license_advanced'] = license_form_el_advanced($bookset);
        }

        return $elements;
    }


    /**
     * Builds the booksets list table
     *
     * @param booksets (reference)
     */
    public static function build_booksets_list_html(&$booksets) {
 		//print_object($booksets);
		//exit;
        $recs=&$booksets['data'];
        //print_object($recs);
		//exit;
		foreach($recs as $bookset) {
            $bookset->description =  strip_tags($bookset->description,'<a>');
            $bookset->status = ($bookset->status ? get_string('allowed','artefact.bookset') : get_string('forbidden','artefact.bookset'));
            $bookset->public = ($bookset->public ? get_string('yes') : get_string('no'));
            $bookset->select = ($bookset->select ? get_string('yes') : get_string('no'));
		}

		$smarty = smarty_core();
        $smarty->assign_by_ref('booksets', $booksets);
		$smarty->assign('iconcheckpath', ArtefactTypeBookset::get_icon_checkpath());
        $smarty->assign('iconshowpath', ArtefactTypeBookset::get_icon_showpath());

        $booksets['tablerows'] = $smarty->fetch('artefact:bookset:booksetlist.tpl');
        $pagination = build_pagination(array(
            'id' => 'booksetlist_pagination',
            'class' => 'center',
            'url' => get_config('wwwroot') . 'artefact/bookset/index.php',
            // 'jsonscript' => 'artefact/bookset/bookset.json.php',     // source d'erreur inconnue ??????????
            'datatable' => 'booksetlist',
            'count' => $booksets['count'],
            'limit' => $booksets['limit'],
            'offset' => $booksets['offset'],
            'firsttext' => '',
            'previoustext' => '',
            'nexttext' => '',
            'lasttext' => '',
            'numbersincludefirstlast' => false,
            'resultcounttextsingular' => get_string('bookset', 'artefact.bookset'),
            'resultcounttextplural' => get_string('booksets', 'artefact.bookset'),
        ));
        $booksets['pagination'] = $pagination['html'];
        $booksets['pagination_js'] = $pagination['javascript'];
    }


    public static function validate(Pieform $form, $values) {
        global $USER;
        if (!empty($values['bookset'])) {
            $id = (int) $values['bookset'];
            $artefact = new ArtefactTypeBookset($id);
            if (!$USER->can_edit_artefact($artefact)) {
                $form->set_error('submit', get_string('canteditdontownbookset', 'artefact.bookset'));
            }
        }
    }


    public static function submit(Pieform $form, $values) {
        global $USER, $SESSION;

        $new = false;

        if (!empty($values['bookset'])) {
            $id = (int) $values['bookset'];
            $artefact = new ArtefactTypeBookset($id);
        }
        else {
            $artefact = new ArtefactTypeBookset();
            $artefact->set('owner', $USER->get('id'));
            $new = true;
        }

        $artefact->set('title', $values['title']);
        $artefact->set('description', $values['description']);
        $artefact->set('status', $values['status']);
        $artefact->set('public', $values['public']);
        $artefact->set('select', $values['select']);

        if (get_config('licensemetadata')) {
            $artefact->set('license', $values['license']);
            $artefact->set('licensor', $values['licensor']);
            $artefact->set('licensorurl', $values['licensorurl']);
        }


        $artefact->commit();

        $SESSION->add_ok_msg(get_string('booksetsavedsuccessfully', 'artefact.bookset'));

        if ($new) {
            redirect('/artefact/bookset/bookset.php?id='.$artefact->get('id'));
        }
        else {
            redirect('/artefact/bookset/index.php');
        }
    }

 
    /**
    * Gets the new/edit bookset pieform
    *
    */
    public static function get_form($bookset=null) {
        require_once(get_config('libroot') . 'pieforms/pieform.php');
        require_once('license.php');
        $elements = call_static_method(generate_artefact_class_name('bookset'), 'get_booksetform_elements', $bookset);
        $elements['submit'] = array(
            'type' => 'submitcancel',
            'value' => array(get_string('savebookset','artefact.bookset'), get_string('cancel')),
            'goto' => get_config('wwwroot') . 'artefact/bookset/index.php',
        );
        $booksetform = array(
            'name' => empty($bookset) ? 'addbookset' : 'editbookset',
            'plugintype' => 'artefact',
            'pluginname' => 'bookset',
            'validatecallback' => array(generate_artefact_class_name('bookset'),'validate'),
            'successcallback' => array(generate_artefact_class_name('bookset'),'submit'),
            'elements' => $elements,
        );

        return pieform($booksetform);
    }

    /**
    * Gets the new/edit bookset pieform
    *
    */
    public static function get_form_select($bookset=null) {
    	global $USER;
		require_once(get_config('libroot') . 'pieforms/pieform.php');
        require_once('license.php');
        if (!empty($bookset)){
			//echo "<br />lib.php :: 639 :: BOOKSET<br />\n";
			//print_object($bookset);
			//exit;
  			// DEBUG
			//echo "<br />lib.php :: 644 :: SELECT : '$select' <br />\n";
            $elements = array(
				'html' => array(
					'type' => 'html',
					'value' => $bookset->title,
				),
				'booksetid' => array(
					'type' => 'hidden',
					'value' => $bookset->id,
				),
			);

			$elements['select'] = array(
                'type'  => 'radio',
            	'options' => array(
                	0 => get_string('no'),
                	1 => get_string('yes'),
            	),
               	'defaultvalue' => $bookset->select,
                'separator' => ' &nbsp; ',
	           	'title' => get_string('selectthisbookset', 'artefact.bookset'),
   		       	'description' => get_string('selectthisbooksetdesc', 'artefact.bookset'),  // hint / help information
			);

			$elements['submit'] = array(
            	'type' => 'submitcancel',
	            'value' => array(get_string('selectbookset','artefact.bookset'), get_string('cancel')),
    	        'goto' => get_config('wwwroot') . 'artefact/bookset/index.php',
        	);
	        $booksetform = array(
    	        'name' => empty($bookset) ? 'addbookset' : 'selectbookset',
        	    'plugintype' => 'artefact',
            	'pluginname' => 'bookset',
	            'validatecallback' => array(generate_artefact_class_name('bookset'),'validate'),
    	        'successcallback' => array(generate_artefact_class_name('bookset'),'submit_selectbookset'),
        	    'elements' => $elements,
        	);
            //echo "<br />lib.php :: 665 :: BOOKSETFORM<br />\n";
			//print_object($booksetform);
			//exit;

    	    return pieform($booksetform);
		}
		return null;
    }

	/**
	 *   set this bookset as default bookset
	 *
	 */

    public static function submit_selectbookset(Pieform $form, $values) {
        global $USER, $SESSION;
        if (!empty($values['booksetid'])) {
            $id = (int) $values['booksetid'];

            $select = 0;
			if (!empty($values['select'])){
				$select = (int) $values['select'];
			}
			if (!empty($values['booksetid'])){
    	        set_field('artefact_bookset', 'select', $select, 'artefact', $id);
			}

			$SESSION->add_ok_msg(get_string('booksetselectedsuccessfully', 'artefact.bookset'));
            redirect('/artefact/bookset/index.php?id='.$id);
		}
        redirect('/artefact/bookset/index.php');
    }

	/******************  Select booklet assoiated to Bookset ******************************************************************/
    /**
    * Gets the new/edit bookset pieform
	* select some artefact_booklet_component
    *
    */
    public static function get_form_componentselect($bookset=null, $components=null) {

		require_once(get_config('libroot') . 'pieforms/pieform.php');
        require_once('license.php');
        $elements = call_static_method(generate_artefact_class_name('bookset'), 'get_bookset_elements', $bookset);

		//echo "<br />DEBUG :: lib.php :: 581<br />artefact_bookset\n";
        //print_object($elements);
		//exit;
		if (!empty($elements)){
			if (!empty($components['data'])){
				$i = 0;
				foreach ($components['data'] as $component){
    	            //echo "<br />component<br />\n";
					//print_object($component);
            	    //$name="'select_".$component->id."'";

					if (!empty($component->componentid)){
							//$strcomment = get_string('componentyetselected', 'bookset');
		                	$elements['select'.$i] = array(
        		        		'type' => 'checkbox',
	            		    	'defaultvalue' => 0,
    	            			'title' => $component->title,
        	        			'description' => 'Yet selected',  // hint / help information
           					);
					}
					else {
                		$elements['select'.$i] = array(
                			'type' => 'checkbox',
		                	'defaultvalue' => $component->id,   // tome id
    		            	'title' => $component->title,
        		        	'description' => '',  // hint / help information
           				);
					}

                	$elements['html'.$i] = array(
                		'type' => 'html',
                		'value' => $component->title.'<br /><i>'.strip_tags ($component->help, '<a>').'</i>',
           			);
					$elements['title'.$i] = array(
                		'type' => 'hidden',
                		'value' => $component->title,
           			);
					$elements['help'.$i] = array(
                		'type' => 'hidden',
                		'value' => $component->help,
           			);
					$elements['status'.$i] = array(
                		'type' => 'hidden',
                		'value' => $component->status,
           			);
					$elements['public'.$i] = array(
                		'type' => 'hidden',
                		'value' => $component->public,
           			);
					$elements['artefacttomeid'.$i] = array(
                		'type' => 'hidden',
                		'value' => $component->artefactid,
           			);
					$elements['tomeid'.$i] = array(
                		'type' => 'hidden',
                		'value' => $component->id,
           			);
					$elements['displayorder'.$i] = array(
                		'type' => 'hidden',
                		'value' => $i,
           			);
                	$i++;
				}
			}
            $elements['nbcomponents'] = array(
                'type' => 'hidden',
                'value' => $i,
           	);
		}

        //print_object($elements);
		//exit;
        $elements['submit'] = array(
            'type' => 'submitcancel',
            'value' => array(get_string('savebookset','artefact.bookset'), get_string('cancel')),
            'goto' => get_config('wwwroot') . 'artefact/bookset/bookset.php?id='.$bookset->id,
        );
        $form = array(
            'name' => empty($bookset) ? 'addbookset' : 'editbookset',
            'plugintype' => 'artefact',
            'pluginname' => 'bookset',
            'validatecallback' => array(generate_artefact_class_name('bookset'),'validate_componentselect'),
            'successcallback' => array(generate_artefact_class_name('bookset'),'submit_componentselect'),
            'elements' => $elements,
        );
        //echo "<br />DEBUG :: lib.php :: 653<br />SELECT COMPONENT FORM<br />\n";
        //print_object($form);
		//exit;
        return pieform($form);
    }

    /**
    * Gets the fields for the bookset pieform
    *
    */
    public static function get_bookset_elements($bookset) {
		if (!empty($bookset)) {
			$elements = array(
				'title' => array(
					'type' => 'hidden',
					'value' => $bookset->title,
				),
				'description' => array(
					'type' => 'hidden',
					'value' => $bookset->description,
				),	
				'status' => array(
					'type' => 'hidden',
					'value' => $bookset->status,
				),
				'public' => array(
					'type' => 'hidden',
					'value' => $bookset->public,
				),
				'select' => array(
					'type' => 'hidden',
					'value' => $bookset->select,
				),
				'booksetid' => array(
					'type' => 'hidden',
					'value' => $bookset->id,
				),
			);

			if (get_config('licensemetadata')) {
				$elements['license'] = license_form_el_basic($bookset);
				$elements['license_advanced'] = license_form_el_advanced($bookset);
			}

			return $elements;
		}
		return null;
	}


    public static function validate_componentselect(Pieform $form, $values) {

		global $USER;
        if (!empty($values['bookset'])) {
            $id = (int) $values['booksetid'];
            $artefact = new ArtefactTypeBookset($id);
			/*
			if (!$artefact->get('public')) {
                $form->set_error('submit', get_string('canteditdontownbookset', 'artefact.bookset'));
            }
			*/
        }
    }

	/**
	 * New artefact bookset component list
	 *
	 *
	 */
    public static function submit_componentselect(Pieform $form, $values) {
        global $USER, $SESSION;

		$artefact = new ArtefactTypeBookset();
        $artefact->set('owner', $USER->get('id'));
        $artefact->set('title', $values['title']);
        $artefact->set('description', $values['description']);
        $artefact->set('status', $values['status']);
        $artefact->set('public', $values['public']);
        $artefact->set('select', $values['select']);

        if (get_config('licensemetadata')) {
            $artefact->set('license', $values['license']);
            $artefact->set('licensor', $values['licensor']);
            $artefact->set('licensorurl', $values['licensorurl']);
        }
		if (!empty($values['booksetid'])){
            $artefact->set('id', $values['booksetid']);
		}
        $artefact->commit();

		// recopier les artefact_components dans la table artefact_bookset_component
       	$k=0;
		if (!empty($values['nbcomponents'])){
			for ($i=0; $i<$values['nbcomponents']; $i++){
				if (!empty($values['select'.$i])){
					// new component
            		$booklet_component = new stdClass();
            		$booklet_component->booksetid = $artefact->get('id');
					$booklet_component->tomeid = $values['tomeid'.$i];
					$booklet_component->displayorder = $k++;
        			// save
					if ($rec=get_record('artefact_bookset_component', 'booksetid', $artefact->get('id'), 'tomeid', $values['tomeid'.$i])){
						$booklet_component->id = $rec->id;
						update_record('artefact_bookset_component', $booklet_component, 'id');
					}
					else {
						$newid = insert_record('artefact_bookset_component', $booklet_component);
					}
				}
			}
		}
        $SESSION->add_ok_msg(get_string('booksetsavedsuccessfully', 'artefact.bookset'));
        redirect('/artefact/bookset/index.php?id='.$artefact->get('id'));
    }

    /**
     * move components up or down
     * @param booksetid
     * @param componentid
     * @param direction
     */
	 public static function invert_component($booksetid, $componentid, $direction=0){
		if (!empty($booksetid)){
			$nbcomponents = count_records('artefact_bookset_component', 'booksetid', $booksetid);

        	$component1 = get_record_sql("SELECT *
							FROM {artefact_bookset_component} 
							WHERE id = ? ", array($componentid));
			if (!empty($component1)){
            	$pos1=$component1->displayorder;

				if (!empty($direction)){ // Down
                	if ($pos1 == $nbcomponents-1){
                        roll_components($booksetid, $nbcomponents, 1);
						return true;
					}
					else{
						$pos2=($component1->displayorder<$nbcomponents-1) ? $component1->displayorder+1 : $nbcomponents-1;
					}
				}
				else{  // Up
					if ($pos1 == 0){
						roll_components($booksetid, $nbcomponents, 0);
                        return true;
					}
					else{
                		$pos2=($component1->displayorder-1>0)? $component1->displayorder-1 : 0;
					}
				}

        		$component2 = get_record_sql("SELECT *
							FROM {artefact_bookset_component} 
                            WHERE booksetid = ? AND displayorder = ? ", array($booksetid, $pos2));
				if (!empty($component2)){
					set_field('artefact_bookset_component', 'displayorder', $pos1, 'id', $component2->id);
				}
                set_field('artefact_bookset_component', 'displayorder', $pos2, 'id', $component1->id);
			}
		}
	}

   /**
     * This function returns a list of all possibly bookset components,
     * ie all artefact_booklet_tome records
     *
     * @param public true if only public tome accepted
     * @param status true if only status !=0 accepted  (status : 0 no modify condition)
     * @return array (count: integer, data: array)
     */
    public static function get_all_components($booksetid=null, $public=null, $status=null) {
        $datenow = time(); // time now to use for formatting components by completion
		$params = array();
        $select = '';
		if (!empty($public)){
            $select.=' AND at.public = ? ';
			$params[] = $public;
		}
		if (!empty($status)){
            $select.=' AND at.status = ? ';
			$params[] = $status;
		}
        $sql = " SELECT at.id as id, at.title as title, at.status as status, at.public as public, at.help as help, a.id as artefactid, a.owner as owner
				FROM {artefact_booklet_tome} at, {artefact} a
				WHERE a.artefacttype = 'tome'
				AND a.id = at.artefact ". $select. "
				ORDER BY at.title ";

		$n=0;
		$results = get_records_sql_array($sql, $params);

		if ($results){
            $n = count($results);
			// verifier si deja present
            foreach ($results as $r){
				if (!empty($r) && !empty($booksetid)){
					if ($rec=get_record('artefact_bookset_component', 'booksetid', $booksetid, 'tomeid', $r->id)){
						$r->componentid = $rec->id;
					}
					else{
                        $r->componentid = 0;
					}
				}
				else{
                    $r->componentid = 0;
				}
			}
		}

		$result = array(
				'count'  => $n,
				'data'   => $results,
				'id'     => $booksetid,
		);

        return $result;
    }

   /**
     * This function returns a list of the current bookset components.
     *
     * @param limit how many components to display per page
     * @param offset current page to display
     * @param order
     * @return array (count: integer, data: array)
     */
    public static function get_components($booksetid, $offset=0, $limit=10, $order='ASC') {
        $datenow = time(); // time now to use for formatting components by completion

		($results = get_records_sql_array(" SELECT ac.id as id, ac.booksetid as booksetid, ac.displayorder as displayorder, at.id as tomeid, at.title as title, at.status as status, at.public as public, at.help as help, a.id as artefactid, a.owner as owner
				FROM {artefact_bookset_component} ac, {artefact_booklet_tome} at, {artefact} a
				WHERE ac.booksetid = ? AND at.id = ac.tomeid AND a.id = at.artefact AND a.artefacttype = 'tome'
				ORDER BY ac.displayorder ".$order, array($booksetid), $offset, $limit))
				|| ($results = array());

		if ($results){
			foreach($results as $rec){
                $rec->help = strip_tags ($rec->help, '<a>');
			}
		}
		$result = array(
				'count'  => count_records('artefact_bookset_component', 'booksetid', $booksetid),
				'data'   => $results,
				'offset' => $offset,
				'limit'  => $limit,
				'id'     => $booksetid,
		);

        return $result;
    }

   /**
     * This function returns a list of the current bookset components.
     *
     * @return array (count: integer, data: array)
     */
    public static function get_bookset_components_info($booksetid) {
		($results = get_records_sql_array(" SELECT ac.id as id, at.id as tomeid, at.title as title, at.status as status, at.public as public, a.id as artefactid, a.owner as owner
				FROM {artefact_bookset_component} ac, {artefact_booklet_tome} at, {artefact} a
				WHERE ac.booksetid = ? AND at.id = ac.tomeid AND a.id = at.artefact AND a.artefacttype = 'tome'
				ORDER BY ac.displayorder ASC", array($booksetid)))
				|| ($results = array());
        return $results;
    }



   /**
     * This function returns a current bookset component.
     *
     * @param booksetid : id booksetid
     * @param componentid : id component
     * @return object record
     */
    public static function get_component($booksetid, $componentid) {
		//return get_record("artefact_bookset_component", "booksetid", $booksetid, "id", $componentid);
        $result = array();
		if ($booksetid && $componentid){
        	if ($result=get_record_sql(" SELECT ac.id as id, ac.booksetid as booksetid, ac.displayorder as displayorder, at.id as tomeid, at.title as title, at.status as status, at.public as public, at.help as help, a.id as artefactid, a.owner as owner
				FROM {artefact_bookset_component} ac, {artefact_booklet_tome} at, {artefact} a
				WHERE ac.booksetid = ? AND ac.id = ? AND at.id = ac.tomeid AND a.id = at.artefact AND a.artefacttype = 'tome'",
				array($booksetid, $componentid)))
			{
				$result->help = strip_tags ($result->help, '<a>');
			}
		}
		return ($result);
 	}


    /**
     * Component deletion
     */
    public function deletecomponent($componentid) {
        if (empty($componentid)) {
            return;
        }
        db_begin();
        delete_records('artefact_bookset_component', 'id', $componentid);
        db_commit();
    }

 	/******************  Export lists ******************************************************************
    public static function get_form_export($bookset=null, $components=null) {
        require_once(get_config('libroot') . 'pieforms/pieform.php');
        require_once('license.php');
        $elements = call_static_method(generate_artefact_class_name('bookset'), 'get_exportform_elements', $bookset);

		$elementcomponents = array();
        $i = 0;
		if (!empty($components['data'])){
			foreach ($components['data'] as $component){
				// formatting scale display
				// valueindex formatting
                $scalestr=scale_display($component->scale, $component->valueindex);

                $elementcomponents['help'.$i] = array(
                	'type' => 'html',
                	'value' => '<b>'.strip_tags($component->title).'</b> :: '.$scalestr,
           		);

                $elementcomponents['select'.$i] = array(
                	'type' => 'checkbox',
                	'title' => $component->code,
                    'defaultvalue' => $component->id,
                	'description' => strip_tags($component->description),
           		);
				$elements['componentid'.$i] = array(
                	'type' => 'hidden',
                	'value' => $component->id,
           		);

                $i++;
			}
		}

		$elements['nbcomponents'] = array(
           	'type' => 'hidden',
           	'value' => $i,
        );

		$elements['resetcomponents'] = array(
                'type'  => 'radio',
	            'options' => array(
    	           	0 => get_string('no'),
        	       	1 => get_string('yes'),
            	),
            	'defaultvalue' => 1,
	            'separator' => ' &nbsp; ',
    	        'title' => get_string('resetlist', 'artefact.bookset'),
        	    'description' => get_string('resetlistdesc','artefact.bookset'),
		);

        $elements['optionnal'] = array(
	            'type' => 'fieldset',
    	        'name' => 'components',
				'title' => 'exportlist',
        	    'collapsible' => true,
            	'collapsed' => true,
	            'legend' => get_string('selectinglist','artefact.bookset'),
                'elements' => $elementcomponents,
  	    );
        $elements['submit'] = array(
            'type' => 'submitcancel',
            'value' => array(get_string('saveexportlist','artefact.bookset'), get_string('exportdonecancel','artefact.bookset')),
            'goto' => get_config('wwwroot') . 'artefact/bookset/index.php',
        );

		// DEBUG
        // print_object($elements);
		// exit;

        $form = array(
            'name' => 'export',
		    'method' => 'post',
            'plugintype' => 'artefact',
            'pluginname' => 'bookset',
            'action' => '',
            'validatecallback' => array(generate_artefact_class_name('bookset'),'validate'),
            'successcallback' => array(generate_artefact_class_name('bookset'),'submit_export'),
            'elements' => $elements,
        );

        //print_object($form);
		//exit;
        return pieform($form);
    }
*********************************/
    /**
    * Gets the new/edit fields for the bookset pieform
    *
    */
	/***********************************
    public static function get_exportform_elements($bookset) {
        $elements = array(
            'id' => array(
                'type' => 'hidden',
                'value' => $bookset->id,
            ),

            'public' => array(
                'type'  => 'hidden',
            	'value' => 0,             // by default exported lists are not public at loading
            ),
            'title' => array(
                'type' => 'text',
                'defaultvalue' => $bookset->title,
                'title' => get_string('title', 'artefact.bookset'),
                'size' => 60,
                'rules' => array(
                    'required' => true,
                ),
            ),
            'description' => array(
                'type'  => 'wysiwyg',
                'rows' => 2,
                'cols' => 50,
                'resizable' => true,
                'defaultvalue' => $bookset->description,
                'title' => get_string('description', 'artefact.bookset'),
            ),
            'status' => array(
                'type'  => 'wysiwyg',
                'rows' => 2,
                'cols' => 50,
                'resizable' => true,
                'defaultvalue' => $bookset->status,
                'title' => get_string('status', 'artefact.bookset'),
            ),
        );

        if (!empty($bookset)) {
            foreach ($elements as $k => $element) {
                $elements[$k]['defaultvalue'] = $bookset->get($k);
            }
            $elements['bookset'] = array(
                'type' => 'hidden',
                'value' => $bookset->id,
            );
        }

        if (get_config('licensemetadata')) {
            $elements['license'] = license_form_el_basic($bookset);
            $elements['license_advanced'] = license_form_el_advanced($bookset);
        }

        return $elements;
    }
***************************************************/

	/**
	 * export artefact & components list
	 *
	 *
	 */
/***************************************************	 
    public static function submit_export(Pieform $form, $values) {
    	global $USER, $SESSION;

	    if (empty($values['id'])){
    		redirect(get_config('wwwroot') . 'artefact/bookset/index.php');
		}

        $new = true;

        $exportid = $values['id'];
        $exporttitle = $values['title'];
        $exportdescription = $values['description'];
        $exportstatus = $values['status'];
        $exportpublic = $values['public'];
        if (get_config('licensemetadata')) {
    		$license = $values['license'];
    		$licensor = $values['licensor'];
    		$licensorurl = $values['licensorurl'];
		}

        $exportlicense = '';
        $exportlicensor = '';
        $exportlicensorurl = '';
        if (get_config('licensemetadata')) {
            $exportlicense = $values['license'];
            $exportlicensor = $values['licensor'];
            $exportlicensorurl = $values['licensorurl'];
        }

		if (!empty($values['resetcomponents'])){
            $resetcomponents=1;
		}
		else{
            $resetcomponents=0;
		}

 		// checked components
		$exportcomponentsids='';
		if (!empty($values['nbcomponents'])){
			for ($i=0; $i<$values['nbcomponents']; $i++){
				if (!empty($values['select'.$i])){
                    $exportcomponentsids.=$values['componentid'.$i];
					if ($i<$values['nbcomponents']-1){
                        $exportcomponentsids.=',';
					}
				}
			}
		}
		//exit;
        $SESSION->add_ok_msg(get_string('booksetsavedsuccessfully', 'artefact.bookset'));
        redirect(get_config('wwwroot') . 'artefact/bookset/exportxml.php?id='.$exportid.'&title='.urlencode($exporttitle).'&description='.urlencode($exportdescription).'&status='.urlencode($exportstatus).'&public='.$exportpublic.'&resetcomponents='.$resetcomponents.'&componentsids='.$exportcomponentsids.'&license='.urlencode($exportlicense).'&licensor='.urlencode($exportlicensor).'&licensorurl='.urlencode($exportlicensorurl));
    }
***************************************************/
 

    /**
     * Renders a bookset.
     *
     * @param  array  Options for rendering
     * @return array  A two key array, 'html' and 'javascript'.
     */

	public function render_self($options) {
        $this->add_to_render_path($options);
		
		$order = !isset($options['order']) ? 'ASC'  : $options['order'];
        $limit = !isset($options['limit']) ? 10 : (int) $options['limit'];
        $offset = isset($options['offset']) ? intval($options['offset']) : 0;

        $components = ArtefactTypeBookset::get_components($this->id, $offset, $limit, $order);

        $template = 'artefact:bookset:bookset_rows.tpl';

        $baseurl = get_config('wwwroot') . 'view/artefact.php?artefact=' . $this->id;
        if (!empty($options['viewid'])) {
            $baseurl .= '&view=' . $options['viewid'];
        }

        $pagination = array(
            'baseurl' => $baseurl,
            'id' => 'booklet_pagination',
            'datatable' => 'booklet_table',
            'jsonscript' => 'artefact/bookset/viewbooklet.json.php',
        );

        ArtefactTypeBookset::render_components($components, $template, $options, $pagination);

        $smarty = smarty_core();
        $smarty->assign_by_ref('components', $components);
        if (isset($options['viewid'])) {
            $smarty->assign('artefacttitle', '<a href="' . $baseurl . '">' . hsc($this->get('title')) . '</a>');
        }
        else {
            $smarty->assign('artefacttitle', hsc($this->get('title')));
        }

        $smarty->assign('bookset', $this);

        if (!empty($options['details']) and get_config('licensemetadata')) {
            $smarty->assign('license', render_license($this));
        }
        else {
            $smarty->assign('license', false);
        }
        $smarty->assign('owner', $this->get('owner'));

		$smarty->assign('tags', $this->get('tags'));

        return array('html' => $smarty->fetch('artefact:bookset:viewbookset.tpl'), 'javascript' => '');
    }

    
    public function render_components(&$components, $template, $options, $pagination) {
        $smarty = smarty_core();
 		$smarty->assign_by_ref('components', $components);
        $smarty->assign_by_ref('options', $options);
		if (!empty($template)){
        	$items['tablerows'] = $smarty->fetch($template);
		}
        if ($items['limit'] && $pagination) {
            $pagination = build_pagination(array(
                'id' => $pagination['id'],
                'class' => 'center',
                'datatable' => $pagination['datatable'],
                'url' => $pagination['baseurl'],
                'jsonscript' => $pagination['jsonscript'],
                'count' => $items['count'],
                'limit' => $items['limit'],
                'offset' => $items['offset'],
                'numbersincludefirstlast' => false,
                'resultcounttextsingular' => get_string('component', 'artefact.bookset'),
                'resultcounttextplural' => get_string('components', 'artefact.bookset'),
            ));
            $items['pagination'] = $pagination['html'];
            $items['pagination_js'] = $pagination['javascript'];
        }
    }

    public static function is_countable_progressbar() {
        return true;
    }	

}  // class end
	
	
	

	/**
     * This function returns the number of bookset_components from a bookset.
     *
     * @param bookset : artefact id
     * @return array  (count: integer, data: array)
     */
    function count_nb_components($bookset) {

        ($results = get_records_sql_array("
            SELECT COUNT(ac.id) AS nbcomponents
			FROM {artefact_bookset_component} 
			WHERE ac.booksetid = ?", array($bookset)))
            || ($results = array());

        $result = array(
            'count'   => $results[0]->nbcomponents,
            'id'     => $bookset,
        );

		//print_object($result);
		//exit;
        return $result;
    }

    /**
    * Export bookset + components
    *
    */
	function set_xml_bookset($data){
		$doc = new DOMDocument();
		$doc->version = '1.0';
		$doc->encoding = 'UTF-8';

        $docbkset = $doc->createElement('bookset');
        $docbkset->setAttribute('title', $data->title);
//		$docbkset->setAttribute('owner', $data->owner;
        $docbkset->setAttribute('public', $data->public);
		$docbkset->setAttribute('status', $data->public);

        $description = $doc->createCDATASection($data->description);
        $docdescription = $doc->createElement('description');
        $docdescription->appendChild($description);

		$docbkset->appendChild($docdescription);

        foreach ($data->components as $component) {
		    $doccomp = $doc->createElement('components');
			$doccomp->setAttribute('booksetid', $component->booksetid);
    		$doccomp->setAttribute('tomeid', $component->tomeid);
        	$doccomp->setAttribute('displayorder', $component->displayorder);
			$docbset->appendChild($docbkset);
		}

		$doc->appendChild($docbset);
        //print_object($doc);
		//exit;

		$xml = $doc->saveXML();
		return($xml);
	}


    /**
     * This function sets the displayorder of artefact_bookset_component for a bookset.
     *
     * @param booksetid : artefact id
     * @param offset : position to move
	 * @param direction 0:up, 1:down
     * @return nothing
     */
     function roll_components($booksetid, $nbcomponents, $direction=0){
        if (!empty($booksetid)){
			$components = get_records_sql_array("SELECT id FROM {artefact_bookset_component} 
                	WHERE booksetid=? ORDER BY displayorder ASC", array($booksetid) );

			if (!empty($components)){
				if (!empty($direction)){ // down
			    	$counter=0;
    	        	foreach ($components as $component) {
            	    	if ($counter < $nbcomponents-1){
							$counter++;
		        		}
						else{
                        	$counter = 0;
						}
                    	set_field('artefact_bookset_component', 'displayorder', $counter, 'id', $component->id);
					}
				}
				else{
			    	$counter=0;
    	        	foreach ($components as $component) {
            	    	if ($counter==0){
							$pos=$nbcomponents-1;
		        		}
						else{
                        	$pos=$counter-1;
						}
                        $counter++;
                    	set_field('artefact_bookset_component', 'displayorder', $pos, 'id', $component->id);
					}
				}
			}
		}
	}

     /**
     * This function increment displayorder of all artefact_bookset_component for a bookset.
     *
     * @param booksetid : artefact id
     * @param componentid : first component to move
     * @return nothing
     */
     function move_component_down($booksetid, $componentid=0){
        if (!empty($booksetid)){
			$components = get_records_sql_array("SELECT id FROM {artefact_bookset_component} 
					WHERE booksetid = ? ORDER BY displayorder ASC", array($booksetid) );
            $move = false;
			if (!empty($components)){
			    $counter=-1;
    	        foreach ($components as $component) {
					$counter++;
					if ($component->id == $componentid){
						$move = true;
					}
					if ($move){
                    	set_field('artefact_bookset_component', 'displayorder', $counter, 'artefact', $component->id);
					}
				}
			}
		}
	}


   	/**
     * This function sets the displayorder of components for a bookset.
     *
     * @param booksetid : parent artefact id
     * @return nothing
     */
	function reset_displayorder($booksetid){
		$bookset = get_record_sql_array("SELECT id FROM {artefact}
			WHERE id=? AND artefacttype = 'bookset' ", array($booksetid));
		// components
        if (!empty($bookset)){
			if ($components = get_records_sql_array("SELECT id FROM {artefact_bookset_component} WHERE booksetid = ? ORDER BY displayorder ASC", array($booksetid) )) {
				$counter=0;
				foreach ($components as $component) {
					set_field('artefact_bookset_component', 'displayorder', $counter, 'artefact', $component->id);
					$counter++;
				}
			}
		}
	}

	/**
     * This function sets the displayorder of components for all booksets.
     *
     * @return nothing
     */
	function reset_all_displayorder(){
		$booksets = get_records_sql_array("SELECT id FROM {artefact} WHERE artefacttype = 'bookset' ", array());
		//print_object($booksets);
        if (!empty($booksets)) {
            foreach ($booksets as $bookset) {
				reset_displayorder($bookset->id);
			}
		}
	}


