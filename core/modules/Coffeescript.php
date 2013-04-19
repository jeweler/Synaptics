<?php

namespace CoffeeScript;

define('COFFEESCRIPT_VERSION', '1.3.1');

class Init {

  /**
   * Dummy function that doesn't actually do anything, it's just used to make
   * sure that this file gets loaded.
   */
  static function init() {
  }

  /**
   * This function may be used in lieu of an autoloader.
   */
  static function load($root = NULL)
  {
    if ($root === NULL)
    {
      $root = realpath(dirname(__FILE__));
    }

    $files = array(
      'needs/Compiler',
      'needs/Error',
      'needs/Helpers',
      'needs/Lexer',
      'needs/Nodes',
      'needs/Parser',
      'needs/Rewriter',
      'needs/Scope',
      'needs/SyntaxError',
      'needs/Value',

      'needs/Base',  // load the base class first
      'needs/While', // For extends While

      'needs/Access',
      'needs/Arr',
      'needs/Assign',
      'needs/Block',
      'needs/Call',
      'needs/Class',
      'needs/Closure',
      'needs/Code',
      'needs/Comment',
      'needs/Existence',
      'needs/Extends',
      'needs/For',
      'needs/If',
      'needs/In',
      'needs/Index',
      'needs/Literal',
      'needs/Obj',
      'needs/Op',
      'needs/Param',
      'needs/Parens',
      'needs/Range',
      'needs/Return',
      'needs/Slice',
      'needs/Splat',
      'needs/Switch',
      'needs/Throw',
      'needs/Try',
      'needs/Values',
    );

    foreach ($files as $file)
    {
      require_once "$root/$file.php";
    }
  }

}

//
// Function shortcuts. These are all used internally.
//

function args(array $args, $required, array $optional = NULL) { return Helpers::args($args, $required, $optional); }
function compact(array $array) { return Helpers::compact($array); }
function del( & $obj, $key) { return Helpers::del($obj, $key); }
function extend($obj, $properties) { return Helpers::extend($obj, $properties); }
function flatten(array $array) { return Helpers::flatten($array); }
function & last( & $array, $back = 0) { return Helpers::last($array, $back); }
function wrap($v) { return Helpers::wrap($v); }
function t() { return call_user_func_array('CoffeeScript\Lexer::t', func_get_args()); }
function t_canonical() { return call_user_func_array('CoffeeScript\Lexer::t_canonical', func_get_args()); }
function multident($code, $tab) { return Nodes::multident($code, $tab); }
function unfold_soak($options, $parent, $name) { return Nodes::unfold_soak($options, $parent, $name); }
function utility($name) { return Nodes::utility($name); }
function yy() { return call_user_func_array('CoffeeScript\Nodes::yy', func_get_args()); }

?>