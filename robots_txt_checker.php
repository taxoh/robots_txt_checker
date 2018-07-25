<?php

// класс для проверки разрешения индексации той или иной ссылки согласно robots.txt 
class robots_txt_checker
{
	protected $regex_list;
	
	// при создании просто передаётся текст robots.txt
	function __construct($robots_txt_content)
	{
		$this->regex_list = [];
		if (preg_match_all('#(?<=^|\n)(allow|disallow):([^\r\n]*)#is', $robots_txt_content, $m, PREG_SET_ORDER))
		{
			foreach ($m as $mm)
			{
				$type = strtolower($mm[1]);
				$url = $mm[2];
				$url = trim($url);
				if (!$url)
				{
					$url = '/';
					$type = (($type=='allow')?'disallow':'allow');
				}
				if (substr($url,0,1)!='/') $url = '/'.$url;
				$url = '#^'.preg_quote($url, '#').'#';
				$url = preg_replace(['#\\\\\*#', '#\\\\\$#'], ['.*?', '$'], $url);
				$this->regex_list[] = ['type' => $type, 'regex' => $url];
			}
		}
	}

	// вернет true, если robots разрешает к индексации заданный $url, false - если индексация запрещена
	function check($url)
	{
		// получить относительную ссылку от корня
		$url = preg_replace('#^https?://[^/]+#i', '', $url);
		$res = true;
		foreach ($this->regex_list as $e)
		{if ($e['type']=='disallow' && preg_match($e['regex'], $url)) $res = false;}
		// Allow имеет больший приоритет чем Disallow
		foreach ($this->regex_list as $e)
		{if ($e['type']=='allow' && preg_match($e['regex'], $url)) $res = true;}
		return $res;
	}
}
