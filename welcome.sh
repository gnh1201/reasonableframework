#!/bin/bash
php cli.php --route welcome --static-ip $(hostname -I)
