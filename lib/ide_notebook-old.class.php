<?php
/**
 * ide_notebook.class.php - Tabed GtkSourceView Wrapper widget
 *
 * handels ide fetures copy, cut, paste, undo, redo, open/newand save
 * manages GtkSourceView and GtkSourceBuffer properys 
 *  auto intdent
 *  space instead of tab
 *  show line numbers
 *  smart home end
 *  tab width
 *  bracket highlight
 *  syntax highlight
 * supports ondrop file open
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
 * @todo         fix save label issue
 */

class CC_Ide_notebook extends GtkNotebook {

	protected $buffers = array();
	static $count = 0;

	public function __construct(){
		parent::__construct();
		$this->set_scrollable(true);
		$config = CC_Config::instance();
		if ($config->tab_pos !== null) {
			switch ($config->tab_pos){
				case 'POS_TOP': $this->set_tab_pos(Gtk::POS_TOP); break;
				case 'POS_BOTTOM': $this->set_tab_pos(Gtk::POS_BOTTOM); break;
				case 'POS_LEFT': $this->set_tab_pos(Gtk::POS_LEFT); break;
				case 'POS_RIGHT': $this->set_tab_pos(Gtk::POS_RIGHT); break;
			}
		}else{
			$this->set_tab_pos(Gtk::POS_BOTTOM);
			$config->tab_pos = 'POS_BOTTOM';
		}
		$this->connect('drag-data-received', array($this, 'on_drop'));
        $this->drag_dest_set(Gtk::DEST_DEFAULT_DROP, array(array('text/uri-list', 0, 0)), Gdk::ACTION_COPY | Gdk::ACTION_MOVE);
	}
	
	public function append_page($buffer, $filename = null){
		self::$count++;
		$view = new GtkScrolledWindow();
		$source = GtkSourceView::new_with_buffer($buffer);
		$source->set_show_line_numbers(1);
		$source->set_auto_indent(1);
		$source->set_highlight_current_line(1);
		$buffer->set_highlight(1);
		$buffer->set_check_brackets(1);
		$view->add($source);
		$hbox = new GtkHBox();
		$button = new GtkButton('X');
		if ($filename == null) {
			$label = new GtkLabel('untitled-'.self::$count);
			$label->set_data('filename','untitled-'.self::$count);
		}else{
			$label = new GtkLabel(basename($filename));
			$label->set_data('filename',basename($filename));
		}
		$buffer->set_modified(false);
		$buffer->connect('modified-changed',array($this,'on_modified'),$label);
		$hbox->pack_start($label);
		$hbox->pack_start($button);
		$label->show();
		$button->show();
		$id = parent::append_page($view,$hbox);
		$view->set_data('tab_id',self::$count);
		$view->set_data('filename',$filename);
		$button->connect('clicked', array($this,'tab_close'),$view);
		$this->buffers[self::$count] = $buffer;
		$this->show_all();
		$this->set_current_page($id);
	}
	
	public function get_current_buffer(){
		$page_id = parent::get_current_page();
		if ($page_id !== -1){
			$child = $this->get_nth_page($page_id);
			$id = $child->get_data('tab_id');
			return $this->buffers[$id];
		}
		return false;
	}
	
	public function get_current_page(){
		return $this->get_nth_page(parent::get_current_page());
	}
	
	public function &get_current_label(){
		$child = $this->get_current_page();
		$tab_label = $this->get_tab_label($child);
		$tab = $tab_label->get_children();
		return $tab[0];
	}
	
	public function on_modified($widget, $label){
		$text = $label->get_data('filename');
		$text .= ' *';
		$label->set_text($text);
	}
	
	public function copy(){
		$cb = GtkClipboard::get(Gdk::atom_intern('CLIPBOARD', false));
		$child = $this->get_nth_page(parent::get_current_page());
		$id = $child->get_data('tab_id');
		$this->buffers[$id]->copy_clipboard($cb);
	}
	
	public function cut(){
		$cb = GtkClipboard::get(Gdk::atom_intern('CLIPBOARD', false));
		$child = $this->get_nth_page(parent::get_current_page());
		$id = $child->get_data('tab_id');
		$this->buffers[$id]->cut_clipboard($cb,true);
	}
	
	public function paste(){
		$cb = GtkClipboard::get(Gdk::atom_intern('CLIPBOARD', false));
		$child = $this->get_nth_page(parent::get_current_page());
		$id = $child->get_data('tab_id');
		$iter = $this->buffers[$id]->get_iter_at_mark($this->buffers[$id]->get_insert());
		$this->buffers[$id]->paste_clipboard($cb,$iter,true);
	}
	
	public function undo(){
		$child = $this->get_nth_page(parent::get_current_page());
		$id = $child->get_data('tab_id');
		if ($this->buffers[$id]->can_undo()) {
			$this->buffers[$id]->undo();
		}
	}
	
	public function redo(){
		$child = $this->get_nth_page(parent::get_current_page());
		$id = $child->get_data('tab_id');
		if ($this->buffers[$id]->can_redo()) {
			$this->buffers[$id]->redo();
		}
	}
	
	public function save(){
		$id = parent::get_current_page();
		if ($id !== -1){
			$child = $this->get_nth_page(parent::get_current_page());
			$tab_label = $this->get_tab_label($child);
			$tab = $tab_label->get_children();
			$label = $this->get_current_label();
			//echo $label;
			$label->set_label($label->get_data('filename'));
			$filename = $child->get_data('filename');
			$tab_id = $child->get_data('tab_id');
			if ($filename !== null) {
				$buffer = $this->buffers[$tab_id];
				file_put_contents($filename,$buffer->get_text($buffer->get_start_iter(),$buffer->get_end_iter()));
			}
		}
	}
	
	public function run(){
		// run not complete
	}

	public function search($sting,$options = array()){
		
	}
	
	public function on_drop($widget, $context, $x, $y, $data, $info, $time){
		echo $data->data;
    	$file = explode("\r\n",$data->data);
    	$buffer->set_text(file_get_contents($file[0]));
       	$this->append_page($buffer,$file[0]);
    }
    
   	public function tab_close($button, $view){
		$id = $view->get_data('tab_id');
		$this->buffers[$id] = null;
		unset($this->buffers[$id]);
		$id = $this->page_num($view);
		$this->remove_page($id);
	}

}

?>