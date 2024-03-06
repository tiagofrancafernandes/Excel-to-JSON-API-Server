<?php

Tracy\Debugger::enable(Tracy\Debugger::DEVELOPMENT);

throw new RuntimeException('Hello Tracy!');
