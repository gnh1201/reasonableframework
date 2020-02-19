<?php
/**
 * @file perftool.php
 * @created_on 2020-02-19
 * @updated_on 2020-02-19
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief PerfTool helper
 */

if(!is_fn("get_cpu_idle")) {
    function get_cpu_idle() {
        $idle = false;

        if(loadHelper("exectool")) {
            $idle = floatval(trim(exec_command("top -n 1 | grep -i Cpu\(s\) | awk '{print \$8}'")));
        }

        return $idle;
    }
}
