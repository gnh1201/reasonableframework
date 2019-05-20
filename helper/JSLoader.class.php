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
    protected $cfg = array();

    protected $scripts = array();

    protected $output;

    public function __construct($cfg = array()) {
        $this->cfg = array_merge($this->get_default_cfg() , $cfg);
    }

    protected function get_default_cfg(){
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

    public function parse_scripts($scripts) {
        $scripts = explode("\n", $scripts);
        foreach($scripts as $script_i => & $script) {
            if (!strlen($script = trim($script)) || $script[0] === "#") {
                unset($scripts[$script_i]);
            }
        }

        return $scripts;
    }

    public function set_scripts($scripts, $cfg = array()) {
        $default_cfg = array(
            'add' => false
        );
        $cfg = array_merge($default_cfg, $cfg);
        if (!is_array($scripts)) {
            $scripts = $this->parse_scripts($scripts);
        }

        if (!$cfg['add']) {
            $this->scripts = array();
        }

        $this->scripts = array_merge($this->scripts, $scripts);
    }

    public function add_scripts($scripts)
    {
        $this->set_scripts($scripts, array(
            'add' => true
        ));
    }

    public function get_output() {
        $scripts = $this->scripts ? $this->scripts : array();
        if ($this->cfg['concat']) {
            foreach($scripts as & $script) {
                $file = $script;
                $file = rtrim($this->cfg['file_system_path'], "/") . "/{$file}";
                $script = file_get_contents($file);
            }

            $scripts = join("\n\n", $scripts);
            if ($this->cfg['function_wrapper']) {
                $scripts = explode("\n", $scripts);
                foreach($scripts as & $line) {
                    if ($line !== "") {
                        $line = "{$this->cfg['indent_string']}{$line}";
                    }
                }

                $scripts = join("\n", $scripts);
                $scripts = <<<DOCHERE
( function () {

{$scripts}

} )();

DOCHERE;
            }

            // if

            if ($this->cfg['script_wrapper']) {
                $scripts = <<<DOCHERE
<script>

{$scripts}

</script>
DOCHERE;
            } elseif ($this->cfg['with_header']) {
                header("Content-Type: text/javascript");
            }

            $scripts = array(
                $scripts
            );
        }
        else {
            foreach($scripts as & $script) {
                $script = <<<DOCHERE
<script src="{$script}"></script>
DOCHERE;
            }
        }

        $this->output = join("\n\n\n", $scripts);
        return $this->output;
    }
}
