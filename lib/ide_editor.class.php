<?php

class CC_Ide_editor extends CC_Ide_notebook {

	public function __construct(){
		parent::__construct();
	}

	public function new_document(){
		static $count = 0;
		$count++;
		$sourceView = new CC_Ide_source();
		$sourceView->set_modified(false);
		$sourceView->set_show_line_numbers(true);
		$sourceView->set_check_brackets(true);
		$sourceView->set_show_line_markers(true);
		$sourceView->set_auto_indent(true);
		$sourceView->set_smart_home_end(true);
		$sourceView->set_data('filename',null);
		$scroll = new GtkScrolledWindow();
		$scroll->add($sourceView);
		$id = $this->append_page_new($scroll, new GtkLabel('Untitled-'.$count));
		$this->show_all();
		$this->set_current_page($id);
	}

	public function open_document($filename){
		if (file_exists($filename)) {
			$sourceView = new CC_Ide_source();
			$sourceView->set_text(file_get_contents($filename));
			$sourceView->set_modified(false);
			$sourceView->set_show_line_numbers(true);
			$sourceView->set_check_brackets(true);
			$sourceView->set_show_line_markers(true);
			$sourceView->set_auto_indent(true);
			$sourceView->set_smart_home_end(true);
			$sourceView->set_data('filename',$filename);
			$scroll = new GtkScrolledWindow();
			$scroll->add($sourceView);
			$id = $this->append_page_new($scroll,new GtkLabel(basename($filename)));
			$this->show_all();
			$this->set_current_page($id);
		}
	}

	public function save_document($filename, $id){
		$scroll = $this->get_nth_page($id);
		if ($scroll !== null) {
			$sourceView = $scroll->get_child();
			$document = $sourceView->get_text();
			file_put_contents($filename,$document);
		}
	}

	public function get_current_page(){
		$scroll = parent::get_current_page();
		return $scroll->get_child();
	}

	public function on_copy(){
		$sourceView = $this->get_current_page();
		if ($sourceView !== null){
			$sourceView->copy();
		}
	}

	public function on_cut(){
		$sourceView = $this->get_current_page();
		if ($sourceView !== null){
			$sourceView->cut();
		}
	}

	public function on_paste(){
		$sourceView = $this->get_current_page();
		if ($sourceView !== null){
			$sourceView->paste();
		}
	}

	public function on_undo(){
		$sourceView = $this->get_current_page();
		if ($sourceView !== null){
			$sourceView->undo();
		}
	}

	public function on_redo(){
		$sourceView = $this->get_current_page();
		if ($sourceView !== null){
			$sourceView->redo();
		}
	}

}

?>