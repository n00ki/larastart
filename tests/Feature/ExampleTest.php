<?php

declare(strict_types=1);

it('shows the welcome page', function () {
    $response = $this->get('/');

    $response->assertOk();
});
