<?php

it('affiche la page de connexion', function () {
    $this->get('/login')->assertOk()->assertSee('Se connecter');
});
