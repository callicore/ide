<?php

class CC_Ide extends CC_Main {
	
	protected $editor;
	protected $pane_top;
	protected $pane_bottom;
	protected $pane_left;
	protected $pane_right;
	
	protected $website = 'http://callicore.net/desktop/ide';
	protected $name = 'ide';
	
	protected $menubar = array(
		'_File' => array(
			'file:new',
			'file:open',
			'file:save',
			'file:saveas',
			'file:quit',
		),
		'_Edit' => array(
			'edit:copy',
			'edit:cut',
			'edit:paste',
			'edit:undo',
			'edit:redo',
		),
		'_View' => array(
			'toolbar:toggle',
			'view:fullscreen',
			'view:top_pane',
			'view:bottom_pane',
			'view:left_pane',
			'view:right_pane',
		),
		'_Tools' => array(
			'tools:preferences',
		),
		'_Plugins' => array(
		),
		'_Help' => array(
			'help:help',
			'help:website',
			'separator',
			'help:about',
		),
	);

	protected $tooloptions = array(
		'file:new',
		'file:open',
		'file:save',
		'file:saveas',
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
		'tools:preferences',
		'view:top_pane',
		'view:bottom_pane',
		'view:left_pane',
		'view:right_pane',
	);

	protected $tooldefault = array(
		'file:new',
		'file:open',
		'file:save',
		'file:saveas',
		'separator',
		'edit:copy',
		'edit:cut',
		'edit:paste',
		'edit:undo',
		'edit:redo',
		'separator',
		'view:fullscreen',
	);

	public function __construct()
	{
		parent::__construct();
		$this->set_position(Gtk::WIN_POS_CENTER);
		CC_Wm::add_window($this);
		$this->set_title('Callicore Development Enviroment');
		$this->editor = new CC_Ide_editor();
		$vpaned1 = new GtkVPaned();
		$vpaned2 = new GtkVPaned();
		$hpaned1 = new GtkHPaned();
		$hpaned2 = new GtkHPaned();
		$vpaned1->pack1($vpaned2);
		$vpaned2->pack2($hpaned1);
		$hpaned1->pack2($hpaned2);
		$this->pane_top = new CC_Ide_notebook();
		$this->pane_bottom = new CC_Ide_notebook();
		$this->pane_left = new CC_Ide_notebook();
		$this->pane_right = new CC_Ide_notebook();
		$hpaned1->pack1($this->pane_left);
		$vpaned1->pack2($this->pane_bottom);
		$vpaned2->pack1($this->pane_top);
		$hpaned2->pack1($this->editor);
		$hpaned2->pack2($this->pane_right);
 		$this->vbox->pack_start($vpaned1);
		$this->show_all();
		$this->pane_top->hide_all();
		$this->pane_bottom->hide_all();
		$this->pane_left->hide_all();
		$this->pane_right->hide_all();
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
					'callback' => array($this, 'on_preferences'),
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
					//'callback' => array($this, 'on_run'),
					'image' => 'gtk-media-play',
				)
		);
		$actions->add_action('view',
				array(
					'type' => 'action',
					'name' => 'top_pane',
					'label' => 'Top pane',
					'short-label' => 'Top pane',
					'tooltip' => 'Top pane',
					'callback' => array($this, 'on_top_pane'),
					'image' => 'cc-view-top',
				)
		);
		$actions->add_action('view',
				array(
					'type' => 'action',
					'name' => 'bottom_pane',
					'label' => 'Bottom pane',
					'short-label' => 'Bottom pane',
					'tooltip' => 'Bottom pane',
					'callback' => array($this, 'on_bottom_pane'),
					'image' => 'cc-view-bottom',
				)
		);
		$actions->add_action('view',
				array(
					'type' => 'action',
					'name' => 'left_pane',
					'label' => 'Left pane',
					'short-label' => 'Left pane',
					'tooltip' => 'Left pane',
					'callback' => array($this, 'on_left_pane'),
					'image' => 'cc-view-left',
				)
		);
		$actions->add_action('view',
				array(
					'type' => 'action',
					'name' => 'right_pane',
					'label' => 'Right pane',
					'short-label' => 'Right pane',
					'tooltip' => 'Right pane',
					'callback' => array($this, 'on_right_pane'),
					'image' => 'cc-view-right',
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
					'name' => 'saveas',
					'label' => '_Save As',
					'short-label' => '_Save As',
					'tooltip' => 'Save File As',
					'callback' => array($this, 'on_save_as'),
					'image' => 'gtk-save-as',
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
	
	public function on_help(){
		
	}
	
	public function on_about(){
		
	}
	
	public function on_open(){
		$opendialog = new CC_Ide_fileopendialog(array('PHP' => '*.php'),$this);
		if ($opendialog->run() == Gtk::RESPONSE_OK) {
       		$selected_file = $opendialog->get_filename();
       		$this->editor->open_document($selected_file);
    	}
    	$opendialog->destroy();
	}
	
	public function on_copy(){
		$this->editor->on_copy();
	}
	
	public function on_cut(){
		$this->editor->on_cut();
	}
	
	public function on_paste(){
		$this->editor->on_paste();
	}
	
	public function on_undo(){
		$this->editor->on_undo();
	}
	
	public function on_redo(){
		$this->editor->on_redo();
	}
	
	public function on_new(){
		$this->editor->new_document();
	}
	
	public function on_top_pane(){
		if ($this->pane_top->is_visible()) {
			$this->pane_top->hide_all();
		}else{
			$this->pane_top->show_all();
		}
	}
	
	public function on_bottom_pane(){
		if ($this->pane_bottom->is_visible()) {
			$this->pane_bottom->hide_all();
		}else{
			$this->pane_bottom->show_all();
		}
	}
	
	public function on_left_pane(){
		if ($this->pane_left->is_visible()) {
			$this->pane_left->hide_all();
		}else{
			$this->pane_left->show_all();
		}
	}
	
	public function on_right_pane(){
		if ($this->pane_right->is_visible()) {
			$this->pane_right->hide_all();
		}else{
			$this->pane_right->show_all();
		}
	}
	
	public function on_preferences(){
		$preferences = new CC_Ide_preferences();
		$preferences->show_all(true);
	}
	
	public function on_save(){
		$sourceView = $this->editor->get_current_page();
		if ($sourceView !== null) {
			$filename = $sourceView->get_data('filename');
			if ($filename !== null) {
				echo $filename."\n";
				$this->editor->save_document($filename,$this->editor->get_current_page_id());
			}else{
				$this->on_save_as();
			}
		}
	}
	
	public function on_save_as(){
		$sourceView = $this->editor->get_current_page();
		if ($sourceView !== null) {
			$dialog = new GtkFileChooserDialog("File Save", null, Gtk::FILE_CHOOSER_ACTION_SAVE, // note 2
        										array(Gtk::STOCK_OK, Gtk::RESPONSE_OK), null);
        	$dialog->show_all();
        	if ($dialog->run() == Gtk::RESPONSE_OK) {
        	    $filename = $dialog->get_filename(); // get the input filename
        	    $sourceView->set_data('filename',$filename);
        	    $this->editor->save_document($filename,$this->editor->get_current_page_id());
        	}
        	$dialog->destroy();
		}
	}
	
}

?>