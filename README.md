# robots_txt_checker

PHP-класс для проверки разрешения индексации той или иной ссылки согласно robots.txt

Пример:

$rtc = new robots_txt_checker(file_get_contents('robots.txt'));
$x = $rtc->check('http://example.com/some/url');
var_dump($x);
