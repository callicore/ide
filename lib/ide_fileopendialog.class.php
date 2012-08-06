<?php

class CC_Ide_fileopendialog extends GtkFileChooserDialog{
	
	protected $filetypes = array();
	
	public function __construct($filetypes = array(),GtkWindow $window = null){
		parent::__construct("File Open", $window,
        	Gtk::FILE_CHOOSER_ACTION_OPEN,
        	array(Gtk::STOCK_OK, Gtk::RESPONSE_OK), null);
        $this->filetypes = $filetypes;
        $hbox = new GtkHBox();
    	$this->vbox->pack_start($hbox, 0, 0);
    	$hbox->pack_start(new GtkLabel('File type:'), 0, 0);
    	$combobox = GtkComboBoxEntry::new_text();
    	$combobox->append_text("All Files");
    	$i = 0;
    	foreach ($filetypes as $name => $filter){
    		$combobox->append_text($name);
    		if ($i == 0) {
    			$combobox->get_child()->set_text($name);
    			$filefilter = new GtkFileFilter();
    			$filefilter->add_pattern($this->filetypes[$name]);
    			$this->set_filter($filefilter);
    		}
    	}
    	$combobox->connect('changed', array($this,'on_change_filter'));
      	$hbox->pack_start($combobox, 0, 0);
      	$this->show_all();
	}
	
	function on_change_filter($combobox) {
    	$model = $combobox->get_model();
    	$selected_value = $model->get_value(
   	    $combobox->get_active_iter(), 0);
    	$filter = new GtkFileFilter();
    	$filter->add_pattern($this->filetypes[$selected_value]);
    	$this->set_filter($filter);
	}
	
}

?>