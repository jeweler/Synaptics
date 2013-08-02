<?php
class Routes
{
    var $query, $type, $controller, $action, $format, $root;

    function __construct($query)
    {
        list($this->query, $this->format) = self::devide($query);
        $this->type = $_SERVER['REQUEST_METHOD'];
        $this->types = YAML::YAMLLoad(DIR.'core/configs/types.yaml');
        $routes = YAML::YAMLLoad(DIR.'core/configs/routes.yaml');
        if (isset($routes['root'])) {
            $this->root = $routes['root'];
            if(substr($query,-1)=='/') $this->query .= $this->root;
            unset($routes['root']);
        }
        $this->routes = $routes;
        $setted = false;
        foreach ($routes as $route) {

            $string = $route['string'];
            $vars = self::getVars($string);
            $types = self::getTypes($string);
            $format = isset($route['format']) ? $route['format'] : null;
            if (isset($route['via'])) {
                if (in_array(strtolower($route['via']), array('post', 'get', 'put'))) {
                    if (strtolower($route['via']) !== strtolower($this->type))
                        continue;
                }
            }
            $statics = isset($route['data']) ? $route['data'] : array();
            $regexprs = isset($route['types']) ? $route['types'] : array();
            if (self::is_valid($statics, $vars)) {
                $regexpr = self::getRegexpr($string);
                if (preg_match_all($regexpr, $this->query, $results)) {
                    $full = self::getFull($string, $this->query, $statics);
                    $end = false;
                    foreach ($types as $key => $type) {

                        if (key_exists($type, $this->types)) {
                            if (!preg_match('/' . $this->types[$type] . '/', $full[$key])) $end = true;

                        }

                        if (key_exists($type, $regexprs)) {
                            if (!preg_match('/' . $regexprs[$type] . '/', $full[$key])) $end = true;

                        }

                    }
                    if (($format !== null) and ($format !== $this->format)) continue;

                    if ($end) continue;

                    if (!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $full['action']))
                        continue;
                    $this->controller = $full['controller'];
                    $this->action = $full['action'];
                    unset($full['controller']);
                    unset($full['action']);
                    $this->get = $full;
                    $this->current = $route;
                    unset($this->routes);
                    unset($this->types);

                    $setted = true;
                    break;
                }
            }
        }
        if (!$setted) {
            new Except(new Exception('Не найден ни один маршрут по запросу "http://' . $_SERVER['HTTP_HOST'] . '/' . $this->query . ':' . $this->type . '"'), 'Ошибка путей');
        }
    }

    static function devide($query)
    {
        if (preg_match_all("/^(.*?)\.([0-9\w]+?)$/", $query, $values)) {

            return array($values[1][0], $values[2][0]);
        } else {
            return array($query, "");
        }
    }

    /**
     * Выдаёт маршрут по параметрам
     * @access public
     * @return string
     * @param string $controller имя контроллера
     * @param string $action имя экшна
     * @param array $params дополнительные параметры
     * @param boolean $trigger дополнять ли маршрут {/}+Route+{.html} по умолчанию true
     */
    static function get($controller = "", $action = "", $params = array(), $format = null)
    {
        $full = array_merge($params, array('controller' => $controller, 'action' => $action));
        $routes = YAML::YAMLLoad('core/configs/routes.yaml');
        foreach ($routes as $route) {
            $vars = self::getVars($route['string']);
            $statics = isset($route['data']) ? $route['data'] : array();
            $formatss = isset($route['format']) ? $route['format'] : null;
            $fullr = array_merge($vars, array_keys($statics));
            if (array_diff($fullr, array_keys($full)) == array()) {
                $stop = false;
                foreach ($full as $key => $value) {

                    if (key_exists($key, $statics)) {

                        if ($full[$key] !== $statics[$key]) {
                            $stop = true;

                        }

                    }

                }
                if ($formatss !== null) {
                    if ($formatss !== $format) continue;
                }
                if ($stop) continue;
                $ret = $route['string'];
                foreach ($full as $key => $value) {
                    $ret = preg_replace('/{ *' . $key . ' *(?:\:.*?)?}/', $value, $ret);
                }
                if ($format !== null and $formatss !== "") $ret .= '.' . $format;
                return $ret;
            }
        }
        return "/system/404.html";
    }

    private static function getFull($string, $request, $data)
    {
        $f = self::getVars($string);
        $s = self::getVals($string, $request);
        return array_merge(array_combine($f, $s), $data);
    }

    private static function getRegexpr($string)
    {
        $string = preg_quote($string);
        $string = str_replace("\\{", '{', $string);
        $string = str_replace("\\}", '}', $string);
        $string = str_replace("\\:", ':', $string);
        $string = str_replace("/", '\/', $string);
        $string = preg_replace('#\\{([1-9\w]+)(?::([1-9\w]+))?\\}#', '([^\/]+)', $string);
        return '/^' . $string . '$/';
    }

    private static function is_valid($array1, $array2)
    {
        $arr = array_merge(array_keys($array1), $array2);
        return (in_array('controller', $arr) and in_array('action', $arr));
    }

    private static function getVals($string, $request)
    {
        $count = preg_match_all('/{([1-9\w]+)(?::([1-9\w]+))?}/', $string, $results);
        if ($count) {
            $keys = $results[1];
            $regexprs = self::getRegexpr($string);
            if (count(preg_match_all($regexprs, $request, $values))) {
                unset($values[0]);
                $ret = array();
                foreach ($values as $val) {
                    $ret [] = $val[0];
                }
                return $ret;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    private static function getTypes($string)
    {
        $count = preg_match_all('/{([1-9\w]+)(?::([1-9\w]+))?}/', $string, $results);
        $ret = array_combine($results[1], $results[2]);
        return $count ? $ret : array();
    }

    private static function getVars($string)
    {
        $count = preg_match_all('/{([1-9\w]+)(?::([1-9\w]+))?}/', $string, $results);
        return $count ? $results[1] : array();
    }
}

?>