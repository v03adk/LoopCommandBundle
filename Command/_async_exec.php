<?php

while(true)
{
    passthru('php ../app/console loop_command:start');
    sleep(5);
}