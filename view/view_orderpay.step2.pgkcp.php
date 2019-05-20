<!doctype html>
<html>
    <head>
        <title>*** NHN KCP [AX-HUB Version] ***</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" /> 
        <meta http-equiv="Pragma" content="no-cache"> 
        <meta http-equiv="Expires" content="-1">
    </head>

    <body>
        <form id="pay_info" name="pay_info" method="post" action="<?php echo $pgkcp_action_url; ?>">
            <fieldset>
                <legend>결제진행중</legend>

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
