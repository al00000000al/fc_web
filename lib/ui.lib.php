<?php

/**
 * @param array $items
 * @param string $type
 * @param string $menu_class
 * @return string
 */
function uiMenu($items = array(), $type = '', $menu_class = 'menu'){
	$html_items = hUiMenuItem($items, $menu_class);
    $type = ($menu_class == 'menu' ? ' '.$type : $type);
	return <<<HTML
<ul class="{$menu_class}{$type}">
{$html_items}</ul>
HTML;
}

/**
 * @param array $items
 * @param string $menu_class
 * @return string
 */
function hUiMenuItem($items = array(), $menu_class = ''){
	global $lang;
	$html_items = '';
	foreach($items as $item){
	    $text = isset($lang[$item[1]]) ? $lang[$item[1]] : $item[1];
		$html_items .= sUiMenuItem($item[0], $text, $item[2], $menu_class);
	}
	return $html_items;
}

/**
 * @param string $item
 * @param string $text
 * @param array $options
 * @param string $menu_class
 * @return string
 */
function sUiMenuItem($item = '', $text = '', $options = array(), $menu_class = 'menu'){
    $li_opts = '';
    $a_opts = '';
    foreach ($options as $el){
        $li_opts .= sUiMenuItemClass($menu_class, 'item', $el);
        $a_opts .= sUiMenuItemClass($menu_class, 'link', $el);
    }
	return <<<HTML
	<li class="{$menu_class}__item{$li_opts}"><a class="{$menu_class}__link{$a_opts}" href="{$item}">{$text}</a></li>

HTML;
}

/**
 * @param $menu_class
 * @param $type
 * @param $name
 * @return string
 */
function sUiMenuItemClass($menu_class, $type, $name){
    return <<<HTML
 {$menu_class}__{$type}_{$name}
HTML;

}

/**
 * @param $content
 * @return string
 */
function sUiHeader($content){
	return
<<<HTML
<header class="header page__header">
	{$content}
</header>
HTML;
}


/**
 * @param $title
 * @param string $href
 * @param string $class
 * @param string $onclick
 * @return string
 */
function sUiBtnHref($title, $href='#', $class='', $onclick=''){
	$onclick = 'onclick="'. $onclick . '"';
	return <<<HTML
 <a href="{$href}" class="button {$class}" {$onclick}>{$title}</a>
HTML;
}


/**
 * @param $items
 * @param $menu_class
 * @param string $opt_html
 * @return string
 */
function uiAside($items, $menu_class, $opt_html = ''){
	$html  = uiMenu($items, '__menu', $menu_class);
	return <<<HTML
 <aside class="{$menu_class}">
	{$opt_html}{$html}
 </aside>
HTML;
 }
