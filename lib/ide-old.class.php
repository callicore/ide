<?php
/**
 * ide.class.php - Main window for the Callicore development enviroment program
 *
 * main window for the application.
 *
 * This is released under the GPL, see docs/gpl.txt for details
 *
 * @author       Leon Pegg <leon@btarchives.com>
 * @copyright    Leon Pegg (c)2006
 * @link         http://callicore.net/desktop/programs/ide
 * @license      http://www.opensource.org/licenses/gpl-license.php GPL
 * @since        Php 5.2.0
 * @package      callicore
 * @subpackage   ide
 * @category     lib
 * @filesource
 */

/**
 * CC_Dev_ide - checks settings and manages common properties
 *
 * Basically a wrapper class for the application
 */
class CC_Ide extends CC_Main
{
	
	protected $notebook;
	protected $last_search;
	
	protected $menubar = array(
		'_File' => array(
			'file:new',
			'file:open',
			'file:save',
			'file:quit',
		),
		'_Edit' => array(
			'edit:copy',
			'edit:cut',
			'edit:paste',
			'edit:undo',
			'edit:redo',
		),
		'_Tools' => array(
			'tools:preferences',
		),
		'_View' => array(
			//'toolbar:toggle',
			'view:fullscreen',
		),
		'_Help' => array(
			'help:help',
			'help:website',
			'separator',
			'help:about',
		),
	);

	/**
	 * options available for toolbar
	 * @var $tooloptions array
	 */
	protected $tooloptions = array(
		'file:new',
		'file:open',
		'file:save',
		'file:quit',
		'edit:copy',
		'edit:cut',
		'edit:paste',
		'edit:undo',
		'edit:redo',
		'view:fullscreen',
		'help:help',
		'help:website',
		'help:about',
		'project:run',
		'tools:preferences'
	);

	/**
	 * default toolbar layout
	 * @var $tooldefault array
	 */
	protected $tooldefault = array(
		'file:new',
		'file:open',
		'file:save',
		'separator',
		'edit:copy',
		'edit:cut',
		'edit:paste',
		'edit:undo',
		'edit:redo',
		'separator',
		'view:fullscreen',
		'separator',
		'project:run'
	);
	
	/**
	 * public function __construct
	 *
	 * description
	 *
	 * @param type $name about
	 * @return type about
	 */
	public function __construct()
	{
		parent::__construct();
		$this->set_position(Gtk::WIN_POS_CENTER);
		$this->set_name('ide');
		$this->set_title('Callicore Development Enviroment');
		//CC_Wm::add_window($this);
		$this->notebook = new CC_Ide_notebook();
		$this->on_new();
 		$this->vbox->pack_start($this->notebook);
		$this->show_all();
	}

	protected function register_actions(){
		$actions = CC_Actions::instance();
		$actions->add_action('tools',
				array(
					'type' => 'action',
					'name' => 'preferences',
					'label' => 'Preferences',
					'short-label' => 'Preferences',
					'tooltip' => 'Preferences',
					'callback' => array($this, 'on_run'),
					'image' => 'gtk-properties',
				)
		);
		$actions->add_action('project',
				array(
					'type' => 'action',
					'name' => 'run',
					'label' => '_Run',
					'short-label' => '_Run',
					'tooltip' => 'Run File',
					'callback' => array($this, 'on_run'),
					'image' => 'gtk-media-play',
				)
		);
		$actions->add_action('file',
				array(
					'type' => 'action',
					'name' => 'open',
					'label' => '_Open',
					'short-label' => '_Open',
					'tooltip' => 'Open File',
					'callback' => array($this, 'on_open'),
					'image' => 'gtk-open',
				)
		);
		$actions->add_action('file',
				array(
					'type' => 'action',
					'name' => 'save',
					'label' => '_Save',
					'short-label' => '_Save',
					'tooltip' => 'Save File',
					'callback' => array($this, 'on_save'),
					'image' => 'gtk-save',
				)
		);
		$actions->add_action('file',
				array(
					'type' => 'action',
					'name' => 'new',
					'label' => '_New',
					'short-label' => '_New',
					'tooltip' => 'New File',
					'callback' => array($this, 'on_new'),
					'image' => 'gtk-new',
				)
		);
		$actions->add_action('edit',
				array(
					'type' => 'action',
					'name' => 'copy',
					'label' => 'Copy',
					'short-label' => 'Copy',
					'tooltip' => 'Copy',
					'callback' => array($this, 'on_copy'),
					'image' => 'gtk-copy',
				)
		);	
		$actions->add_action('edit',
				array(
					'type' => 'action',
					'name' => 'cut',
					'label' => 'Cut',
					'short-label' => 'Cut',
					'tooltip' => 'Cut',
					'callback' => array($this, 'on_cut'),
					'image' => 'gtk-cut',
				)
		);	
		$actions->add_action('edit',
				array(
					'type' => 'action',
					'name' => 'paste',
					'label' => 'Paste',
					'short-label' => 'Paste',
					'tooltip' => 'Paste',
					'callback' => array($this, 'on_paste'),
					'image' => 'gtk-paste',
				)
		);	
		$actions->add_action('edit',
				array(
					'type' => 'action',
					'name' => 'undo',
					'label' => 'Undo',
					'short-label' => 'Undo',
					'tooltip' => 'Undo',
					'callback' => array($this, 'on_undo'),
					'image' => 'gtk-undo',
				)
		);	
		$actions->add_action('edit',
				array(
					'type' => 'action',
					'name' => 'redo',
					'label' => 'Redo',
					'short-label' => 'Redo',
					'tooltip' => 'Redo',
					'callback' => array($this, 'on_redo'),
					'image' => 'gtk-redo',
				)
		);
		return parent::register_actions();
	}
	
	public function on_run(){
		$this->notebook->run();
		//CC_Os::run('c:\callicore\php.exe c:\php-release\callicore\run.php ide');
	}
	
	public function on_about(){
		$about = new GtkAboutDialog();
		$about->set_authors(array('Leon Pegg'));
		$about->set_license(file_get_contents(CC::$dir . docs . DS . 'gpl.txt'));
		$about->set_version('0.2.0-Dev');
		$about->run();
	}
	
	public function on_new(){
		$buffer = new GtkSourceBuffer();
		$this->notebook->append_page($buffer);
	}
	
	public function on_open(){
		$opendialog = new CC_Ide_fileopendialog(array('PHP' => '*.php'),$this);
		if ($opendialog->run() == Gtk::RESPONSE_OK) {
       		$selected_file = $opendialog->get_filename();
       		$buffer = new GtkSourceBuffer();
       		$buffer->set_text(file_get_contents($selected_file));
       		$this->notebook->append_page($buffer,$selected_file);
    	}
    	$opendialog->destroy();
	}
	
	public function on_save(){
		$this->notebook->save();
	}
	
	public function on_help(){

	}
	
	public function on_copy(){
		$this->notebook->copy();
	}
	
	public function on_cut(){
		$this->notebook->cut();
	}
	
	public function on_paste(){
		$this->notebook->paste();
	}
	
	public function on_undo(){
		$this->notebook->undo();
	}
	
	public function on_redo(){
		$this->notebook->redo();
	}

	public function on_search(){
		
	}
	
}

# ToDo:-
# Write CC_Config_XML to alow for better configuration methods
#  file display settings
#  convert XML to array structure and retrevable XML node objects
# preferences dialog
# php explorer (displaying functions, constants, classes, interfaces)
# code snippits repo
# serach/replace (regex, multiline, standard, match case, hexsearch)
# project manager
# Indent code (customizable code formater)
# php manual serach (web or cached local)
# ini editor text and gui based
?>