<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('default', fn() => true);