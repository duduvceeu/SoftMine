<?php

namespace softmine\wizard;


class InstallerLang{
	public static $languages = [
		"en" => "English",
		"es" => "Español",
		"zh" => "中文",
		"ru" => "Pyccĸий",
		"ja" => "日本語",
		"de" => "Deutsch",
		//"vi" => "Tiếng Việt",
		"ko" => "한국어",
		"nl" => "Nederlands",
		"fr" => "Français",
		"it" => "Italiano",
		//"lv" => "Latviešu",
		"ms" => "Melayu",
		"no" => "Norsk",
		"pt" => "Português",
		"sv" => "Svenska",
		"fi" => "Suomi",
		"tr" => "Türkçe",
		//"et" => "Eesti",
	];
	private $texts = [];
	private $lang;
	private $langfile;

	public function __construct($lang = ""){
		if(file_exists(\softmine\PATH . "src/softmine/lang/Installer/" . $lang . ".ini")){
			$this->lang = $lang;
			$this->langfile = \softmine\PATH . "src/softmine/lang/Installer/" . $lang . ".ini";
		}else{
			$files = [];
			foreach(new \DirectoryIterator(\softmine\PATH . "src/softmine/lang/Installer/") as $file){
				if($file->getExtension() === "ini" and substr($file->getFilename(), 0, 2) === $lang){
					$files[$file->getFilename()] = $file->getSize();
				}
			}

			if(count($files) > 0){
				arsort($files);
				reset($files);
				$l = key($files);
				$l = substr($l, 0, -4);
				$this->lang = isset(self::$languages[$l]) ? $l : $lang;
				$this->langfile = \softmine\PATH . "src/softmine/lang/Installer/" . $l . ".ini";
			}else{
				$this->lang = "en";
				$this->langfile = \softmine\PATH . "src/softmine/lang/Installer/en.ini";
			}
		}

		$this->loadLang(\softmine\PATH . "src/softmine/lang/Installer/en.ini", "en");
		if($this->lang !== "en"){
			$this->loadLang($this->langfile, $this->lang);
		}

	}

	public function getLang(){
		return ($this->lang);
	}

	public function loadLang($langfile, $lang = "en"){
		$this->texts[$lang] = [];
		$texts = explode("\n", str_replace(["\r", "\\/\\/"], ["", "//"], file_get_contents($langfile)));
		foreach($texts as $line){
			$line = trim($line);
			if($line === ""){
				continue;
			}
			$line = explode("=", $line);
			$this->texts[$lang][trim(array_shift($line))] = trim(str_replace(["\\n", "\\N",], "\n", implode("=", $line)));
		}
	}

	public function get($name, $search = [], $replace = []){
		if(!isset($this->texts[$this->lang][$name])){
			if($this->lang !== "en" and isset($this->texts["en"][$name])){
				return $this->texts["en"][$name];
			}else{
				return $name;
			}
		}elseif(count($search) > 0){
			return str_replace($search, $replace, $this->texts[$this->lang][$name]);
		}else{
			return $this->texts[$this->lang][$name];
		}
	}

	public function __get($name){
		return $this->get($name);
	}

}
