<?php

it('requests a token with the trusted-user grant')
    ->skip('OAuth flow is internal in 2.0')
    ->group('integration', 'authentication');
