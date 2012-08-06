<?php

class CC_Ide_notebook extends GtkNotebook {
	
	public function __construct(){
		parent::__construct();
		$this->set_scrollable(true);
	}
	
	public function append_page_new($child, $label){
		$tab = new GtkHBox();
		$event_box = new GtkEventBox();
		$event_box->add(
		$button = GtkImage::new_from_stock(
			Gtk::STOCK_CLOSE,
			Gtk::ICON_SIZE_MENU)
		);
		$event_box->show();
		$button->show();
		$event_box->connect('button_press_event', array($this, 'close_page'), $child);
		$tab->pack_start($label);
		$tab->pack_start($event_box);
		$tab->show_all();
		return parent::append_page($child,$tab);
	}
	
	public function close_page($child){
		$id = $this->page_num($child);
		$this->remove_page($id);
	}
	
	public function get_current_page(){
		return $this->get_nth_page(parent::get_current_page());
	}
	
	public function get_current_page_id(){
		return parent::get_current_page();
	}
	
	public function get_tab_label_new($child){
		$tab = parent::get_tab_label($child);
		$tab_label = $tab->get_children();
		return $tab_label[0];
	}
	
	public function get_current_tab_label(){
		return $this->get_tab_label_new($this->get_current_page());
	}
	
}

?>