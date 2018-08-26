<?php

/*

JavaScript loader. Load a sequence of JavaScript files using individual
SCRIPT elements, or concatenated, with or without a wrapper function (to keep
variables out of the global scope) and with or without a SCRIPT element wrapper
(with for an internal script, without for external).

Copyright © 2012 Jesse McCarthy <http://jessemccarthy.net/>

This software may be used under the MIT (aka X11) license or
Simplified BSD (aka FreeBSD) license.  See LICENSE.

*/


/**
 * JavaScript loader.
 *
 * @version 0.5.0
 */

class JSLoader {

  /**
   * @property array $cfg Config parameters.
   *
   * bool concat Concatenate scripts.
   *
   * bool function_wrapper Wrap in function to keep out of global scope.
   */

  protected $cfg = array();


  /// array Scripts to be processed.

  protected $scripts = array();


  /// string Generated output.

  protected $output;


  /**
   * Constructor.
   *
   * @param array $cfg Configuration parameters.
   *
   * @return void
   */

  public function __construct( $cfg = array() ) {

    $this->cfg = array_merge( $this->get_default_cfg(), $cfg );

  }
  // __construct


  protected function get_default_cfg() {

    $default_cfg = array(

      'file_system_path' => "./",

      'concat' => false,

      'function_wrapper' => false,

      'indent_string' => "  ",

      // False for an external script or for concatenating output from multiple
      // instances.

      'script_wrapper' => true,

      'with_header' => true

    );


    return $default_cfg;

  }
  // get_default_cfg


  public function parse_scripts( $scripts ) {

    $scripts = explode( "\n", $scripts );


    foreach ( $scripts as $script_i => &$script ) {

      if (

        ! strlen( $script = trim( $script ) ) ||

        $script[0] === "#"

      ) {

        unset( $scripts[ $script_i ] );

      }

    }
    // foreach


    return $scripts;

  }
  // parse_scripts


  /**
   * Set $this->scripts, replacing previous value.
   *
   * @param string|array $scripts New scripts.
   *
   * @param array $cfg Configuration parameters.
   *
   * @return void.
   */

  public function set_scripts( $scripts, $cfg = array() ) {

    $default_cfg = array(

      'add' => false

    );


    $cfg = array_merge( $default_cfg, $cfg );


    if ( ! is_array( $scripts ) ) {

      $scripts = $this->parse_scripts( $scripts );

    }
    // if


    if ( ! $cfg[ 'add' ] ) {

      $this->scripts = array();

    }
    // if


    $this->scripts = array_merge( $this->scripts, $scripts );

  }
  // set_scripts


  /**
   * Add to $this->scripts.
   *
   * @param string|array $scripts New scripts.
   *
   * @return void
   */

  public function add_scripts( $scripts ) {

    $this->set_scripts( $scripts, array( 'add' => true ) );

  }
  // add_scripts


  public function get_output() {

    $scripts = $this->scripts ?: array();


    if ( $this->cfg[ 'concat' ] ) {

      foreach ( $scripts as &$script ) {

        $file = $script;

        $file = rtrim( $this->cfg[ 'file_system_path' ], "/" ) . "/{$file}";

        $script = file_get_contents( $file );

      }
      // foreach


      $scripts = join( "\n\n", $scripts );


      if ( $this->cfg[ 'function_wrapper' ] ) {

        // Indent script content.

        $scripts = explode( "\n", $scripts );

        foreach ( $scripts as &$line ) {

          if ( $line !== "" ) {

            $line = "{$this->cfg[ 'indent_string' ]}{$line}";

          }

        }
        // foreach


        $scripts = join( "\n", $scripts );


        $scripts = <<<DOCHERE
( function () {

{$scripts}

} )();

DOCHERE;

      }
      // if


      if ( $this->cfg[ 'script_wrapper' ] ) {

        $scripts = <<<DOCHERE
<script>

{$scripts}

</script>
DOCHERE;

      }
      // if


      elseif ( $this->cfg[ 'with_header' ] ) {

        header( "Content-Type: text/javascript" );

      }
      // elseif


      $scripts = array( $scripts );

    }
    // if


    else {

      foreach ( $scripts as &$script ) {

        $script = <<<DOCHERE
<script src="{$script}"></script>
DOCHERE;

      }
      // foreach

    }
    // else


    $this->output = join( "\n\n\n", $scripts );

    return $this->output;

  }
  // get_output

}
// JSLoader
