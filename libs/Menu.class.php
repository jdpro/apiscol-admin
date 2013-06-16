<?php
class Menu {

	private $render;
	private $items;
	private $prefix;


	function Menu($prefix) {
		$this->items=array();
		$this->prefix=$prefix;
	}
	function addItem($label, $key, $current, $linkOnTopItem) {
		$this->items[$key]=array();
		$this->items[$key]['label']=$label;
		$this->items[$key]['linkOnTopItem']=$linkOnTopItem;
		$this->items[$key]['current']=($current==$key);
	}
	function addSubItem($itemKey, $label, $key, $current) {
		assert(array_key_exists($itemKey, $this->items));
		if(!array_key_exists('children', $this->items[$itemKey]))
			$this->items[$itemKey]['children']=array();
		$this->items[$itemKey]['children'][$key]['current']=$current==$key;
		$this->items[$itemKey]['children'][$key]['label']=$label;
	}

	function toHTML() {
		$this->render = '<ul class="menu ui-state-active ui-corner-all">';
		foreach ($this->items as $key=>$value) {
			$this->render .= '<li class="menu-item';
			if ($value['current']==true)
				$this->render .= ' current ui-state-focus';
			$url="";
			if ($value['linkOnTopItem']==true)
				$url=$this->prefix.'/'.$key;
			if(array_key_exists('children', $value))
				$this->render .= '" id="main_menu_item_'.$key.'"><span>{'.($value['label']).'}</span>';
			else
				$this->render .= '" id="main_menu_item_'.$key.'"><a href="'.$url.'">{'.($value['label']).'}</a>';
			if(array_key_exists('children', $value)){
				$this->render .= '<ul class="ui-state-active">';
				foreach ($value['children'] as $key2=>$value2) {
					$this->render .= '<li class="sub-menu-item';
					if ($value2['current'])
						$this->render .= ' current ';
					$this->render .= '" id="menu_item_secondaire_'.$key2.'"><a href="'.$this->prefix.'/'.$key.'/'.$key2.'">{'.($value2['label']).'}</a></li>';
				}
				$this->render .= '</ul>';
			}
			$this->render .= '</li>';
		}
		$this->render.='</ul>';
		return $this->render;
	}
}

?>
