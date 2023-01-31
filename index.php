<?php
    include('class/onetouch.class.php');

    $astropay = new OneTouchRequest();

    var_dump($astropay->create_deposit(10.99, 'USD', 'BR', 'test-deposit', 'test-user', null));
    //var_dump($astropay->deposit_init);
    //var_dump($astropay->create_cashout_v1(10.99, 'USD', 'BR', 'test-cashout', 'test-user', '55 61991049888'));
?>