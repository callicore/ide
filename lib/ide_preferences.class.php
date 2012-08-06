<?php

class CC_Ide_preferences extends CC_Window {
	
	protected $name = 'preferences';
	
	public function __construct(){
		parent::__construct();
		CC_Wm::add_window($this);
	}
	
}

?>