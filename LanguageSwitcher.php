<?php
/*
// Set language base
LanguageSwitcher::set([
    ['code' => 'en', 'name' => 'English', 'default' => true],
    ['code' => 'de', 'name' => 'German', 'default' => false]
]);

// Reset language base
LanguageSwitcher::reset();

// Check active language
LanguageSwitcher::active();

// Switch languages
LanguageSwitcher::switch('de');

// List all languages
LanguageSwitcher::list();
*/


class LanguageSwitcher {
    private static $activeLang;
    private static $default;


    public static function set($languageList = null) {

        // Check if language list json is not exists
        if (!is_array($languageList)) die('argument must be type of array');
        if (file_exists(__DIR__."/langList.json")) die('language list is already exist. now you can append it with <pre style="color: green; font-weight: bold;">LanguageSwitcher::append(["code" => "de", "name" => "German", "default" => 0])</pre>');
        if (empty($languageList)) die('You must provide languge code along with language name \'title\'');


        // Create and insert language lsit
        file_put_contents(__DIR__.'/langList.json', self::toJSON($languageList));
    }


    public static function reset() {
        if (file_exists(__DIR__.'/langList.json')) {
            unlink(__DIR__.'/langList.json');
        } else {
            die('nothing to reset');
        }
    }


    private static function toJSON($fileArray) {
        $json = json_encode($fileArray, JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
        return $json;
    }


    public static function append(array $args = []) {
        $langList = file_get_contents(__DIR__.'/langList.json') ?? die('You must set language list first');

        $langList = json_decode($langList, true);

        if (empty($args)) die('You must provide language code, language name and is language a default language like this - <br/> 
<pre>
<b style="color: green;">
LanguageSwitcher::set([
    ["code" => "en", "name" => "English", "default" => 1],
    ["code" => "de", "name" => "German", "default" => 0]
]);
</b>
</pre>
        ');

        $code = isset($args['code']) ? $args['code'] : die('you must provide language code like this - <b style="color: green;"><pre>LanguageSwitcher::append([\'code\' => \'de\']);</pre></b>');
        $name = isset($args['name']) ? $args['name'] : die('You must provide language name like this - <b style="color: green;"><pre>LanguageSwitcher::append([\'name\' => \'German\']);</pre></b>');
        $default = $args['default'] ?? 0;

        if (!isset($args['default'])) $args['default'] = $default;

        foreach ($langList as $key => $value) {
            if (strtolower($name) == strtolower($value['name'])) die($name . ' language is already in list');
            if (strtolower($code) == strtolower($value['code'])) die($code . ' language is already in list');
        }

        if ($default == 1) {
            foreach ($langList as $key => $value) $langList[$key]['default'] = 0;
        }

        $langList[] = $args;

        file_put_contents(__DIR__.'/langList.json', self::toJSON($langList));
    }


    // Reset default language
    public static function setDefault(string $langCode) {
        
        $langList = file_get_contents(__DIR__.'/langList.json') ?? die('You must set language list first');

        $langList = json_decode($langList, true);

        $done = false;

        foreach ($langList as $key => $value) {
            if (strtolower($value['code']) == strtolower($langCode)) {
                $value = 1;
                $langList[$key]['default'] = 1;
                $done = true;
            }
        }

        if (!$done) die("$langCode - such language code not found");

        foreach ($langList as $key => $value) {
            if (strtolower($value['code']) != strtolower($langCode)) {
                $langList[$key]['default'] = 0;
            }
        }

        file_put_contents(__DIR__.'/langList.json', self::toJSON($langList));

        $defaultLang = self::default();
        return "Default language is {$defaultLang['name']}";
    }


    public static function list() {
        if (file_exists(__DIR__.'/langList.json')) {
            $file = file_get_contents(__DIR__.'/langList.json');
            $list = json_decode($file, true);
            return $list;
        } else {
            die('You must set language list first');
        }
    }


    public static function active() {
        if (!empty(self::get_cookie('lang'))) {
            $list = self::list();
            foreach ($list as $key => $value) {
                if ( strtolower($value['code']) == strtolower(self::get_cookie('lang')) ) {
                    return self::get_cookie('lang');
                }
            }

            self::set_cookie(['name' => 'lang', 'value' => self::default()['code']]);
            return self::get_cookie('lang');
        } else {
            self::set_cookie(['name' => 'lang', 'value' => self::default()['code']]);
            return self::default()['code'];
        }
    }


    public static function switch($lang = null) {
        
        if (!$lang) die('Provide language code from language list');

        $list = self::list();
        foreach ($list as $key => $value) {
            if ( strtolower($value['code']) == strtolower($lang) ) {
                self::set_cookie(['name' => 'lang', 'value' => $lang]);
                return self::get_cookie('lang');
            }
        }
        die('There is no such language inside the language list. Check all languages - \'LanguageSwitcher::list()\'');
    }


    public static function default() {
        if (file_exists(__DIR__.'/langList.json')) {
            $list = self::list();
            foreach ($list as $key => $value) if ($value['default'] == 1) return $value;
        } else {
            die('You must set language list first');
        }
    }


    public static function isset() {
        if (file_exists(__DIR__.'/langList.json')) {
            return 1;
        } else {
            return 0;
        }
    }


    // Inline translation
    public static function translate(array $languages) {        
        return $languages[self::active()];
    }


    // Add language flags
    // Render language code HTML
    public static function render(bool $onlyFlags = true) {

        if (isset($_GET['lang'])) {
            self::switch($_GET['lang']);

            $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $url = explode('?', $actual_link)[0];
            return header("Location: $url");
        }

        $inner = "";

        foreach (self::list() as $item) {
            
            $code = strtolower($item['code']);
            $flag = file_exists(__DIR__ . "/flags/{$code}.svg") ? file_get_contents(__DIR__ . "/flags/{$code}.svg") : die("Flag not found for language code - $code");
            $langName = $item['name'];
            $class = '';

            if (!$onlyFlags) $langName = null;

            if (strtolower(self::active()) === strtolower($item['code'])) $class = 'class="active"';

            $inner .= "<li $class>
                <a href=\"?lang={$code}\">
                    <span>{$flag}{$langName}</span>
                </a>
            </li>";
        }
        
        return "<ul>{$inner}</ul>";
    }




    /////////////////////////////////////////////// COOKIE FUNCTIONS ///////////////////////////////////////////////
    // Set cookie
    protected static function set_cookie(array $data) {

        $tobeStored = [
           'name' => $data['name'] ?? null,
           'value' => isset($data['value']) ? self::toJSON($data['value']) : null,
           'expire' => isset($data['expire']) ? time() + $data['expire'] : time() + 86400,
           'path' => $data['path'] ?? '/',
           'domain' => $data['domain'] ?? "",
           'secure' => $data['secure'] ?? false,
           'httponly' => $data['httponly'] ?? false,
        ];

        setcookie($tobeStored['name'], $tobeStored['value'], $tobeStored['expire'], $tobeStored['path'], $tobeStored['domain'], $tobeStored['secure'], $tobeStored['httponly']);
    }


    // Get cookie
    protected static function get_cookie(string $name) {

        if (isset($_COOKIE[$name])) {
            return json_decode($_COOKIE[$name]);
        } else {
            return false;
        }
    }


    // Delete cookie
    protected static function delete_cookie(string $name) {

        if (isset($_COOKIE[$name])) setcookie($name, null, time() - 3600, '/');
        return true;
    }
}