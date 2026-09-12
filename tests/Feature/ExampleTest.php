<?php

test('the application redirects unauthenticated user to login', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});
