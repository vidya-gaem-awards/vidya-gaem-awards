<?php
$container->loadFromExtension('twig', array(
    'globals' => array(
        'user' => ['loggedIn' => false],
        'steamLoginLink' => '',
    ),
));
