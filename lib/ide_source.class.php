<?php

class CC_Ide_source extends GtkSourceView {
	
	protected $buffer;
	
	public function __construct(){
		parent::__construct();
		$this->buffer = new GtkSourceBuffer();
		$this->set_buffer($this->buffer);
	}
		
	public function get_modified(){
		return $this->buffer->get_modified();
	}
	
	public function get_highlight(){
		return $this->buffer->get_highlight();
	}
	
	public function get_check_brackets(){
		return $this->buffer->get_check_brackets();
	}
	
	public function get_text(){
		return $this->buffer->get_text($this->buffer->get_start_iter(),$this->buffer->get_end_iter());
	}
	
	public function set_text($text){
		return $this->buffer->set_text($text);
	}
	
	public function set_modified($setting){
		return $this->buffer->set_modified($setting);
	}
	
	public function set_highlight($setting){
		return $this->buffer->set_highlight($setting);
	}
	
	public function set_check_brackets($setting){
		return $this->buffer->set_check_brackets($setting);
	}
	
	public function copy(){
		$clipboard = GtkClipboard::get(Gdk::atom_intern('CLIPBOARD', false));
		$this->buffer->copy_clipboard($clipboard);
	}
	
	public function cut(){
		$clipboard = GtkClipboard::get(Gdk::atom_intern('CLIPBOARD', false));
		$this->buffer->cut_clipboard($clipboard,true);
	}
	
	public function paste(){
		$clipboard = GtkClipboard::get(Gdk::atom_intern('CLIPBOARD', false));
		$iter = $this->buffer->get_iter_at_mark($this->buffer->get_insert());
		$this->buffer->paste_clipboard($clipboard,$iter,true);
	}
	
	public function undo(){
		if ($this->buffer->can_undo()) {
			$this->buffer->undo();
		}
	}
	
	public function redo(){
		if ($this->buffer->can_redo()) {
			$this->buffer->redo();
		}
	}
	
	public function buffer_connect_simple(){
		return call_user_method_array('connect_simple',$this->buffer,func_get_args());
	}
	
	public function buffer_connect_simple_after(){
		return call_user_method_array('connect_simple_after',$this->buffer,func_get_args());
	}
	
	public function buffer_connect(){
		return call_user_method_array('connect',$this->buffer,func_get_args());
	}
	
	public function buffer_connect_after(){
		return call_user_method_array('connect_after',$this->buffer,func_get_args());
	}
	
}

?>