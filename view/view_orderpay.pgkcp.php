<?php
/**
 * @file view_orderpay.pgkcp.php
 * @date 2018-08-25
 * @author Go Namhyeon <gnh1201@gmail.com>
 * @brief KCP PG(Payment Gateway) View
 */

if(!defined("_DEF_RSF_")) set_error_exit("do not allow access");
?>

<!DOCTYPE html>
<html>
<head>
    <title>*** NHN KCP [AX-HUB Version] ***</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" /> 
    <meta http-equiv="Pragma" content="no-cache"> 
    <meta http-equiv="Expires" content="-1">
</head>

<body>
    <h1>NHN KCP [AX-HUB Version]</h1>

    <form id="order_info" name="order_info" method="post" action="<?php echo $pgkcp_action_url; ?>">
        <fieldset>
            <legend>결제 정보</legend>

            <div>
                <input type="hidden" name="_token" value="<?php echo $_token; ?>">
                <input type="hidden" name="route" value="<?php echo $_next_route; ?>">
            </div>

            <ul>
<?php
                foreach($payinfo as $k=>$v) {
?>
                <li>
                    <label for="<?php echo $k; ?>"><?php echo $k; ?></label>
                    <input id="<?php echo $k; ?>" name="<?php echo $k; ?>" value="<?php echo $v; ?>" readonly="readonly"/>
                </li>
<?php
                }
?>
            </ul>

            <button id="btn_submit" type="submit">Submit</button>
        </fieldset>
    </form>

    <?php echo $jsoutput; ?>
</body>
</html>
