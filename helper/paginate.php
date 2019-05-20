<?php
/**
 * @file pagenate.php
 * @date 2018-01-01
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief Page navigation helper
 */

if(!check_function_exists("paginate_get_current_page")) {
    function paginate_get_current_page($page=1) {
        $current_page = 1;

        if($page > 0) {
            $current_page = $page; 
        }

        return $current_page;
    }
}

if(!check_function_exists("paginate_get_total_pages")) {
    function paginate_get_total_pages($item_per_page=1.0, $total_records=1.0) {
        $total_pages = 1;

        if($item_per_page > 0) {
            $total_pages = ceil($total_records / $item_per_page);
        }
        
        return $total_pages;
    }
}

if(!check_function_exists("paginate_get_query_string")) {
    function paginate_get_query_string() {
        loadHelper("networktool");
        $net_event = get_network_event();
        return get_value_in_array("query", $net_event, "");
    }
}

// https://www.sanwebe.com/2011/05/php-pagination-function
if(!check_function_exists("paginate_make_html")) {
    function paginate_make_html($item_per_page, $current_page, $total_records, $total_pages, $page_url, $qry='') {
        $pagination = '';
        if($total_pages > 0 && $total_pages != 1 && $current_page <= $total_pages) { //verify total pages and current page number
            $pagination .= '<ul class="pagination justify-content-end">';
           
            $right_links    = $current_page + 3;
            $previous       = $current_page - 3; //previous link
            $next           = $current_page + 1; //next link
            $first_link     = true; //boolean var to decide our first link
            
            // prevent minus page number
            if($previous < 0) {
                $previous = 1;
            }
            
            $qry_url = '';
            if(!empty($qry)) {
                $qry_url = '?' . $qry;
            }

            if($current_page > 1) {
                $previous_link = ($previous == 0) ? 1 : $previous;
                $pagination .= '<li class="page-item first"><a class="page-link" href="' . $page_url . '1' . $qry_url.'" title="First">&laquo;</a></li>'; //first link
                $pagination .= '<li class="page-item"><a class="page-link" href="' . $page_url . $previous_link . $qry_url.'" title="Previous">&lt;</a></li>'; //previous link
                    for($i = ($current_page-2); $i < $current_page; $i++) { //Create left-hand side links
                        if($i > 0) {
                            $pagination .= '<li class="page-item"><a class="page-link" href="' . $page_url . $i . $qry_url . '">' . $i . '</a></li>';
                        }
                    }  
                $first_link = false; //set first link to false
            }

            if($first_link) { //if current active page is first link
                $pagination .= '<li class="page-item first active"><a class="page-link" href="#">' . $current_page . '</a></li>';
            } elseif($current_page == $total_pages) { //if it's the last active link
                $pagination .= '<li class="page-item last active"><a class="page-link" href="#">' . $current_page . '</a></li>';
            } else { //regular current link
                $pagination .= '<li class="page-item active"><a class="page-link" href="#">' . $current_page . '</a></li>';
            }

            for($i = $current_page+1; $i < $right_links ; $i++) { //create right-hand side links
                if($i <= $total_pages) {
                    $pagination .= '<li class="page-item"><a class="page-link" href="' . $page_url . $i . $qry_url.'">' . $i . '</a></li>';
                }
            }
            if($current_page < $total_pages) {
                    $next_link = ($i > $total_pages) ? $total_pages : $i;
                    $pagination .= '<li class="page-item"><a class="page-link" href="'. $page_url . $next_link . $qry_url.'" >&gt;</a></li>'; //next link
                    $pagination .= '<li class="page-item last"><a class="page-link" href="' . $page_url . $total_pages . $qry_url.'" title="Last">&raquo;</a></li>'; //last link
            }

            $pagination .= '</ul>';
        }

        return $pagination; //return pagination links
    }
}
