<?php
/**
 * @file perftool.php
 * @created_on 2020-02-19
 * @updated_on 2020-02-24
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief PerfTool helper
 */

if(!is_fn("get_cpu_idle")) {
    function get_cpu_idle() {
        $idle = false;

        if(loadHelper("exectool")) {
            $idle = floatval(trim(exec_command("top -n 1 -b | grep -i Cpu\(s\) | awk '{print \$8}'"))) / 100.0;
        }

        return $idle;
    }
}

if(!is_fn("set_min_cpu_idle")) {
    function set_min_cpu_idle($ratio=0.01) {
        $wait = 0;

        // default (cpu_sleep_time): 3 seconds
        $cpu_sleep_time = floatval(get_value_in_array("cpu_sleep_time", $config, 3));
        if($ratio > 0 && $ratio < 1) {
            while(get_cpu_idle() < $ratio) {
                if($wait == 0) {
                    write_common_log("CPU usage exceeded. wait a few seconds...", "helper/preftool");
                }

                sleep($cpu_sleep_time);
                $wait++;
            }
        }

        if($wait > 0) {
            write_common_log(sprintf("CPU usage recovered. waited %s seconds ago", ($wait * $cpu_sleep_time)), "helper/preftool");
        }
    }
}

if(!is_fn("set_cpu_usage_limit")) {
    function set_cpu_usage_limit($ratio=0.9) {
        $config = get_config();
        $num_cores = get_value_in_array("cpu_num_cores", $config, 1);
        $pid = get_shared_var("mypid");

        if(!empty($pid) && loadHelper("exectool")) {
            write_common_log(sprintf("CPU usage limit setted. PID=%s, LIMIT=%s", $pid, ($ratio * $num_cores) * 100), "helper/perftool");
            exec_command(sprintf("cpulimit --pid %s --limit %s", $pid, ($ratio * $num_cores) * 100));
        }
    }
}
